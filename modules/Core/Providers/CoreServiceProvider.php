<?php

declare(strict_types=1);

namespace Modules\Core\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Core\Models\Organization;
use Modules\Core\Models\Tenant;
use Modules\Core\Repositories\Contracts\OrganizationRepositoryInterface;
use Modules\Core\Repositories\Contracts\TenantRepositoryInterface;
use Modules\Core\Repositories\Eloquent\OrganizationRepository;
use Modules\Core\Repositories\Eloquent\TenantRepository;
use Modules\Core\Services\Contracts\OrganizationServiceInterface;
use Modules\Core\Services\Contracts\TenantServiceInterface;
use Modules\Core\Services\OrganizationService;
use Modules\Core\Services\TenantService;

/**
 * Core Service Provider
 * 
 * Registers all Core module services, repositories, and bindings.
 * This provider handles dependency injection for the Core module.
 */
final class CoreServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register Repositories
        $this->app->bind(
            TenantRepositoryInterface::class,
            function ($app) {
                return new TenantRepository(new Tenant());
            }
        );

        $this->app->bind(
            OrganizationRepositoryInterface::class,
            function ($app) {
                return new OrganizationRepository(new Organization());
            }
        );

        // Register Services
        $this->app->bind(
            TenantServiceInterface::class,
            TenantService::class
        );

        $this->app->bind(
            OrganizationServiceInterface::class,
            OrganizationService::class
        );

        // Register middleware
        $this->app->make('router')->aliasMiddleware(
            'tenant',
            \Modules\Core\Middleware\TenantIdentification::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Load migrations
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');

        // Register module routes if needed
        // $this->loadRoutesFrom(__DIR__ . '/../Routes/api.php');
    }
}
