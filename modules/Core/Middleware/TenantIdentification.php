<?php

declare(strict_types=1);

namespace Modules\Core\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Modules\Core\Models\Tenant;
use Symfony\Component\HttpFoundation\Response;

/**
 * Tenant Identification Middleware
 * 
 * This middleware identifies the tenant from the request (domain/subdomain)
 * and switches the database connection to the tenant's dedicated database.
 * 
 * This is the core of the multi-tenancy system implementing database-per-tenant isolation.
 */
final class TenantIdentification
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip tenant identification in testing environment
        if (app()->environment('testing')) {
            return $next($request);
        }

        // Skip tenant identification for central routes (tenant management, etc.)
        if ($this->isCentralRoute($request)) {
            return $next($request);
        }

        // Identify tenant from domain
        $domain = $this->extractDomain($request);
        
        // Get tenant from cache or database
        $tenant = $this->resolveTenant($domain);

        if (!$tenant) {
            return response()->json([
                'error' => 'Tenant not found',
                'message' => 'The requested tenant domain could not be found.',
            ], 404);
        }

        // Validate tenant status
        if (!$tenant->isActive()) {
            return response()->json([
                'error' => 'Tenant not active',
                'message' => $this->getTenantStatusMessage($tenant),
            ], 403);
        }

        // Set tenant in application context
        app()->instance('tenant', $tenant);
        $request->attributes->set('tenant', $tenant);

        // Switch database connection to tenant database
        $this->switchToTenantDatabase($tenant);

        return $next($request);
    }

    /**
     * Check if the route is a central route (no tenant context needed).
     */
    private function isCentralRoute(Request $request): bool
    {
        $path = $request->path();
        
        $centralPaths = [
            'api/v1/central/tenants',
            'api/v1/central/health',
            '_debugbar',
        ];

        foreach ($centralPaths as $centralPath) {
            if (str_starts_with($path, $centralPath)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Extract domain from request based on configuration.
     */
    private function extractDomain(Request $request): string
    {
        $method = config('tenancy.identification_method', 'domain');

        return match ($method) {
            'subdomain' => $this->extractSubdomain($request),
            'header' => $request->header('X-Tenant-Domain', $request->getHost()),
            default => $request->getHost(),
        };
    }

    /**
     * Extract subdomain from host.
     */
    private function extractSubdomain(Request $request): string
    {
        $host = $request->getHost();
        $parts = explode('.', $host);
        
        // Return first part as subdomain if we have multiple parts
        return count($parts) > 2 ? $parts[0] : $host;
    }

    /**
     * Resolve tenant from cache or database.
     */
    private function resolveTenant(string $domain): ?Tenant
    {
        $cacheKey = "tenant:{$domain}";
        
        return Cache::remember($cacheKey, 3600, function () use ($domain) {
            // Try to find by domain first
            $tenant = Tenant::where('domain', $domain)->first();
            
            // If not found by domain, try subdomain
            if (!$tenant) {
                $tenant = Tenant::where('subdomain', $domain)->first();
            }
            
            return $tenant;
        });
    }

    /**
     * Switch database connection to tenant's dedicated database.
     */
    private function switchToTenantDatabase(Tenant $tenant): void
    {
        // Configure tenant database connection
        Config::set('database.connections.tenant', $tenant->getDatabaseConfig());

        // Set tenant connection as default
        Config::set('database.default', 'tenant');

        // Purge any existing connection to ensure fresh connection
        DB::purge('tenant');

        // Reconnect with tenant database
        DB::reconnect('tenant');
    }

    /**
     * Get appropriate status message for tenant.
     */
    private function getTenantStatusMessage(Tenant $tenant): string
    {
        return match (true) {
            $tenant->isSuspended() => 'This tenant has been suspended. Please contact support.',
            $tenant->isExpired() => 'This tenant subscription has expired. Please renew your subscription.',
            $tenant->isTrial() => 'This tenant is in trial mode.',
            default => 'This tenant is not currently active.',
        };
    }

    /**
     * Terminate the middleware.
     * Clean up tenant context after request.
     */
    public function terminate(Request $request, Response $response): void
    {
        // Switch back to central database
        Config::set('database.default', 'pgsql');
        DB::purge('tenant');
        
        // Clear tenant from application context
        app()->forgetInstance('tenant');
    }
}
