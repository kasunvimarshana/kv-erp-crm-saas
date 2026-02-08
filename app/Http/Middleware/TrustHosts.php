<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrustHosts
{
    /**
     * The trusted host patterns.
     *
     * @return array<int, string|null>
     */
    public function hosts(): array
    {
        return [
            $this->allSubdomainsOfApplicationUrl(),
        ];
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($this->shouldSpecifyTrustedHosts()) {
            $this->specifyTrustedHosts($request);
        }

        return $next($request);
    }

    /**
     * Determine if trusted host verification should be enabled.
     */
    protected function shouldSpecifyTrustedHosts(): bool
    {
        return config('app.env') !== 'local';
    }

    /**
     * Specify the trusted hosts.
     */
    protected function specifyTrustedHosts(Request $request): void
    {
        foreach ($this->hosts() as $host) {
            if ($host !== null) {
                $request->setTrustedHosts([$host]);
            }
        }
    }

    /**
     * Get a pattern matching all subdomains of the application URL.
     */
    protected function allSubdomainsOfApplicationUrl(): string
    {
        $host = parse_url(config('app.url'), PHP_URL_HOST);

        return '^(.+\.)?'.preg_quote($host).'$';
    }
}
