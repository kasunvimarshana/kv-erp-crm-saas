<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Tenant Identification Method
    |--------------------------------------------------------------------------
    |
    | This option controls how tenants are identified in the application.
    | Supported methods: "domain", "subdomain", "header"
    |
    | - domain: Full domain-based identification (e.g., tenant1.com)
    | - subdomain: Subdomain-based identification (e.g., tenant1.example.com)
    | - header: HTTP header-based identification (X-Tenant-Domain)
    |
    */

    'identification_method' => env('TENANCY_IDENTIFICATION_METHOD', 'domain'),

    /*
    |--------------------------------------------------------------------------
    | Central Database Connection
    |--------------------------------------------------------------------------
    |
    | This is the database connection used for central data like tenant
    | information, subscriptions, and billing. This database stores all
    | tenant metadata but not tenant-specific business data.
    |
    */

    'central_connection' => env('TENANCY_CENTRAL_CONNECTION', 'pgsql'),

    /*
    |--------------------------------------------------------------------------
    | Tenant Database Driver
    |--------------------------------------------------------------------------
    |
    | The database driver to use for tenant databases.
    | This should match your configured database connections.
    |
    */

    'tenant_database_driver' => env('TENANCY_TENANT_DATABASE_DRIVER', 'pgsql'),

    /*
    |--------------------------------------------------------------------------
    | Central Routes
    |--------------------------------------------------------------------------
    |
    | Routes that should not be scoped to a tenant. These routes will use
    | the central database connection and will not attempt to identify
    | a tenant or switch database connections.
    |
    */

    'central_routes' => [
        'api/v1/central/*',
        '_debugbar/*',
        'horizon/*',
        'telescope/*',
    ],

    /*
    |--------------------------------------------------------------------------
    | Tenant Cache Configuration
    |--------------------------------------------------------------------------
    |
    | Configure how tenant information is cached for performance.
    |
    */

    'cache' => [
        'enabled' => env('TENANCY_CACHE_ENABLED', true),
        'ttl' => env('TENANCY_CACHE_TTL', 3600), // 1 hour
        'prefix' => 'tenant:',
    ],

    /*
    |--------------------------------------------------------------------------
    | Tenant Storage Configuration
    |--------------------------------------------------------------------------
    |
    | Configure storage paths and structure for tenant-specific files.
    |
    */

    'storage' => [
        'disk' => 'tenants',
        'path_prefix' => 'tenants',
        'per_tenant_directory' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Database Migration Configuration
    |--------------------------------------------------------------------------
    |
    | Configure how migrations are handled for tenant databases.
    |
    */

    'migrations' => [
        'run_on_tenant_creation' => true,
        'run_on_tenant_activation' => false,
        'tenant_migrations_path' => database_path('migrations/tenant'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Tenant Limits
    |--------------------------------------------------------------------------
    |
    | Default limits for new tenants. These can be overridden per tenant.
    |
    */

    'limits' => [
        'max_users' => env('TENANCY_DEFAULT_MAX_USERS', 10),
        'max_organizations' => env('TENANCY_DEFAULT_MAX_ORGANIZATIONS', 1),
        'max_storage_mb' => env('TENANCY_DEFAULT_MAX_STORAGE_MB', 1024),
    ],

    /*
    |--------------------------------------------------------------------------
    | Subscription Plans
    |--------------------------------------------------------------------------
    |
    | Define available subscription plans and their limits.
    |
    */

    'plans' => [
        'basic' => [
            'name' => 'Basic',
            'max_users' => 10,
            'max_organizations' => 1,
            'max_storage_mb' => 1024,
            'features' => ['core_modules'],
        ],
        'professional' => [
            'name' => 'Professional',
            'max_users' => 50,
            'max_organizations' => 5,
            'max_storage_mb' => 10240,
            'features' => ['core_modules', 'advanced_reporting', 'api_access'],
        ],
        'enterprise' => [
            'name' => 'Enterprise',
            'max_users' => -1, // Unlimited
            'max_organizations' => -1, // Unlimited
            'max_storage_mb' => -1, // Unlimited
            'features' => ['core_modules', 'advanced_reporting', 'api_access', 'custom_integrations', 'priority_support'],
        ],
    ],

];
