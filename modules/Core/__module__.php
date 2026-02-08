<?php

declare(strict_types=1);

return [
    // Basic Information
    'name' => 'Core',
    'display_name' => 'Core Foundation',
    'description' => 'Foundation module providing multi-tenancy, authentication, authorization, and audit logging',
    'version' => '1.0.0',
    'author' => 'KV-ERP-CRM-SaaS',
    'author_email' => 'dev@kv-erp-crm-saas.com',
    
    // Module Classification
    'category' => 'core',
    'type' => 'module',
    
    // Dependencies
    'dependencies' => [],
    
    // Optional dependencies
    'optional_dependencies' => [],
    
    // Module Status
    'enabled' => true,
    'installed' => true,
    'auto_load' => true,
    
    // Database
    'database' => [
        'migrations_path' => 'Migrations',
        'seeders_path' => 'Seeders',
    ],
    
    // Service Providers
    'providers' => [
        'Modules\\Core\\Providers\\CoreServiceProvider',
        'Modules\\Core\\Providers\\RouteServiceProvider',
    ],
    
    // Permissions required by this module
    'permissions' => [
        'core.admin',
        'tenants.view',
        'tenants.create',
        'tenants.edit',
        'tenants.delete',
        'organizations.view',
        'organizations.create',
        'organizations.edit',
        'organizations.delete',
        'users.view',
        'users.create',
        'users.edit',
        'users.delete',
        'roles.view',
        'roles.create',
        'roles.edit',
        'roles.delete',
        'permissions.view',
        'permissions.assign',
    ],
    
    // Menu items
    'menu' => [
        [
            'title' => 'Administration',
            'icon' => 'shield',
            'route' => null,
            'permission' => 'core.admin',
            'order' => 100,
            'children' => [
                [
                    'title' => 'Tenants',
                    'route' => 'core.tenants.index',
                    'permission' => 'tenants.view',
                ],
                [
                    'title' => 'Organizations',
                    'route' => 'core.organizations.index',
                    'permission' => 'organizations.view',
                ],
                [
                    'title' => 'Users',
                    'route' => 'core.users.index',
                    'permission' => 'users.view',
                ],
                [
                    'title' => 'Roles & Permissions',
                    'route' => 'core.roles.index',
                    'permission' => 'roles.view',
                ],
            ],
        ],
    ],
    
    // API endpoints provided
    'api' => [
        'version' => 'v1',
        'prefix' => 'core',
        'endpoints' => [
            'GET /tenants',
            'POST /tenants',
            'GET /tenants/{id}',
            'PUT /tenants/{id}',
            'DELETE /tenants/{id}',
            'GET /organizations',
            'POST /organizations',
            'GET /users',
            'POST /users',
        ],
    ],
    
    // Module settings
    'settings' => [
        'multi_tenancy_enabled' => true,
        'database_per_tenant' => true,
        'tenant_identification_method' => 'domain', // domain, subdomain, header
        'enable_audit_logging' => true,
        'enable_mfa' => true,
    ],
];
