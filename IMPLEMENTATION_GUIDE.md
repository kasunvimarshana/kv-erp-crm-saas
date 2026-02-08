# Implementation Guide

## Overview

This guide provides step-by-step instructions for implementing the KV-ERP-CRM-SaaS system based on the architecture and design documented in this repository.

## Table of Contents

1. [Project Initialization](#project-initialization)
2. [Core Module Implementation](#core-module-implementation)
3. [Multi-Tenancy Setup](#multi-tenancy-setup)
4. [First Business Module](#first-business-module)
5. [Testing Setup](#testing-setup)
6. [Frontend Setup](#frontend-setup)
7. [API Documentation](#api-documentation)
8. [Best Practices](#best-practices)

## Project Initialization

### Step 1: Create Laravel Project

```bash
# Create new Laravel project
composer create-project laravel/laravel kv-erp-crm-saas
cd kv-erp-crm-saas

# Install additional dependencies
composer require --dev barryvdh/laravel-ide-helper
composer require spatie/laravel-permission
composer require spatie/laravel-query-builder
composer require laravel/sanctum
composer require darkaonline/l5-swagger

# Frontend dependencies
npm install vue@next @vitejs/plugin-vue
npm install pinia vue-router@4 axios
npm install -D tailwindcss postcss autoprefixer
```

### Step 2: Configure Environment

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Configure database in .env
# DB_CONNECTION=pgsql
# DB_HOST=127.0.0.1
# DB_PORT=5432
# DB_DATABASE=kv_erp_crm_saas
# DB_USERNAME=postgres
# DB_PASSWORD=your_password
```

### Step 3: Create Directory Structure

```bash
# Create modules directory
mkdir -p modules

# Create documentation directory
mkdir -p docs

# Create scripts directory
mkdir -p scripts

# Create Docker configuration
mkdir -p docker/{php,nginx,postgres}
```

## Core Module Implementation

### Step 1: Create Core Module Structure

```bash
# Create Core module directory
mkdir -p modules/Core/{Config,Controllers,Models,Services,Repositories,Events,Listeners,Middleware,Policies,Migrations,Seeders,Tests,Routes}

# Create subdirectories
mkdir -p modules/Core/Controllers/{Web,Api}
mkdir -p modules/Core/Services/Contracts
mkdir -p modules/Core/Repositories/Contracts
mkdir -p modules/Core/Tests/{Unit,Feature}
mkdir -p modules/Core/Routes
```

### Step 2: Create Module Manifest

Create `modules/Core/__module__.php`:

```php
<?php

return [
    'name' => 'Core',
    'display_name' => 'Core Foundation',
    'description' => 'Foundation module providing multi-tenancy, authentication, and authorization',
    'version' => '1.0.0',
    'author' => 'Your Company',
    'category' => 'core',
    'dependencies' => [],
    'enabled' => true,
    'auto_load' => true,
    
    'providers' => [
        'Modules\\Core\\Providers\\CoreServiceProvider',
    ],
    
    'permissions' => [
        'core.admin',
        'users.view',
        'users.create',
        'users.edit',
        'users.delete',
        'roles.view',
        'roles.create',
        'roles.edit',
        'roles.delete',
    ],
];
```

### Step 3: Create Core Migrations

```bash
# Create tenant migration
php artisan make:migration create_tenants_table

# Create organizations migration
php artisan make:migration create_organizations_table

# Create users migration (modify default)
php artisan make:migration add_tenant_fields_to_users_table
```

Example `create_tenants_table.php`:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('domain')->unique();
            $table->string('subdomain')->unique()->nullable();
            $table->string('company_name');
            $table->string('database_name')->unique();
            $table->string('database_host')->default('localhost');
            $table->integer('database_port')->default(5432);
            $table->enum('status', ['active', 'suspended', 'trial', 'expired'])->default('trial');
            $table->enum('plan', ['basic', 'professional', 'enterprise'])->default('basic');
            $table->integer('max_users')->default(10);
            $table->integer('max_organizations')->default(1);
            $table->timestamp('subscription_start')->nullable();
            $table->timestamp('subscription_end')->nullable();
            $table->string('billing_email');
            $table->json('custom_settings')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
```

### Step 4: Create Core Models

Create `modules/Core/Models/Tenant.php`:

```php
<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tenant extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'domain',
        'subdomain',
        'company_name',
        'database_name',
        'database_host',
        'database_port',
        'status',
        'plan',
        'max_users',
        'max_organizations',
        'subscription_start',
        'subscription_end',
        'billing_email',
        'custom_settings',
    ];

    protected $casts = [
        'subscription_start' => 'datetime',
        'subscription_end' => 'datetime',
        'custom_settings' => 'array',
        'max_users' => 'integer',
        'max_organizations' => 'integer',
    ];

    public function organizations(): HasMany
    {
        return $this->hasMany(Organization::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isTrial(): bool
    {
        return $this->status === 'trial';
    }

    public function isExpired(): bool
    {
        return $this->status === 'expired' || 
               ($this->subscription_end && $this->subscription_end->isPast());
    }
}
```

### Step 5: Create Service Layer

Create `modules/Core/Services/Contracts/TenantServiceInterface.php`:

```php
<?php

namespace Modules\Core\Services\Contracts;

use Modules\Core\Models\Tenant;
use Illuminate\Support\Collection;

interface TenantServiceInterface
{
    public function create(array $data): Tenant;
    public function update(Tenant $tenant, array $data): Tenant;
    public function delete(Tenant $tenant): bool;
    public function findByDomain(string $domain): ?Tenant;
    public function getAllActive(): Collection;
}
```

Create `modules/Core/Services/TenantService.php`:

```php
<?php

namespace Modules\Core\Services;

use Modules\Core\Models\Tenant;
use Modules\Core\Repositories\Contracts\TenantRepositoryInterface;
use Modules\Core\Services\Contracts\TenantServiceInterface;
use Modules\Core\Events\TenantCreated;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TenantService implements TenantServiceInterface
{
    public function __construct(
        private TenantRepositoryInterface $tenantRepository
    ) {}

    public function create(array $data): Tenant
    {
        return DB::transaction(function () use ($data) {
            // Generate database name if not provided
            if (!isset($data['database_name'])) {
                $data['database_name'] = 'tenant_' . uniqid();
            }

            // Create tenant
            $tenant = $this->tenantRepository->create($data);

            // Create tenant database
            $this->createTenantDatabase($tenant);

            // Dispatch event
            event(new TenantCreated($tenant));

            return $tenant;
        });
    }

    public function update(Tenant $tenant, array $data): Tenant
    {
        return $this->tenantRepository->update($tenant, $data);
    }

    public function delete(Tenant $tenant): bool
    {
        return DB::transaction(function () use ($tenant) {
            // Backup tenant data (implement as needed)
            
            // Delete tenant database (optional, or just mark as deleted)
            // $this->dropTenantDatabase($tenant);

            // Soft delete tenant
            return $this->tenantRepository->delete($tenant);
        });
    }

    public function findByDomain(string $domain): ?Tenant
    {
        return $this->tenantRepository->findByDomain($domain);
    }

    public function getAllActive(): Collection
    {
        return $this->tenantRepository->getAllActive();
    }

    private function createTenantDatabase(Tenant $tenant): void
    {
        // Create new database for tenant
        DB::statement("CREATE DATABASE {$tenant->database_name}");

        // Run migrations on tenant database
        config(['database.connections.tenant' => [
            'driver' => 'pgsql',
            'host' => $tenant->database_host,
            'port' => $tenant->database_port,
            'database' => $tenant->database_name,
            'username' => config('database.connections.pgsql.username'),
            'password' => config('database.connections.pgsql.password'),
        ]]);

        DB::purge('tenant');
        
        // Run tenant migrations
        \Artisan::call('migrate', [
            '--database' => 'tenant',
            '--path' => 'modules/*/Migrations',
        ]);
    }
}
```

### Step 6: Create Middleware

Create `modules/Core/Middleware/TenantIdentification.php`:

```php
<?php

namespace Modules\Core\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Core\Services\Contracts\TenantServiceInterface;
use Symfony\Component\HttpFoundation\Response;

class TenantIdentification
{
    public function __construct(
        private TenantServiceInterface $tenantService
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        // Identify tenant from domain/subdomain
        $domain = $request->getHost();
        
        $tenant = $this->tenantService->findByDomain($domain);

        if (!$tenant) {
            abort(404, 'Tenant not found');
        }

        if (!$tenant->isActive()) {
            abort(403, 'Tenant is not active');
        }

        // Set tenant context
        app()->instance('tenant', $tenant);

        // Switch database connection
        config(['database.default' => 'tenant']);
        config(['database.connections.tenant' => [
            'driver' => 'pgsql',
            'host' => $tenant->database_host,
            'port' => $tenant->database_port,
            'database' => $tenant->database_name,
            'username' => config('database.connections.pgsql.username'),
            'password' => config('database.connections.pgsql.password'),
        ]]);

        DB::purge('tenant');

        return $next($request);
    }
}
```

## Multi-Tenancy Setup

### Configure Multi-Tenancy

1. **Register Middleware** in `app/Http/Kernel.php`:

```php
protected $middlewareGroups = [
    'web' => [
        // ... other middleware
        \Modules\Core\Middleware\TenantIdentification::class,
    ],
];
```

2. **Create Central Database** - Keep central database for tenant management
3. **Create Tenant Databases** - Each tenant gets separate database
4. **Implement Database Switching** - Switch connections based on identified tenant

## First Business Module

### Create Sales Module

```bash
# Create Sales module structure
mkdir -p modules/Sales/{Controllers,Models,Services,Repositories,Tests,Routes}

# Create controllers
php artisan make:controller Modules/Sales/Controllers/Api/SalesOrderApiController --api

# Create models
php artisan make:model Modules/Sales/Models/SalesOrder
php artisan make:model Modules/Sales/Models/SalesOrderLine

# Create migrations
php artisan make:migration create_sales_orders_table
php artisan make:migration create_sales_order_lines_table
```

Follow the patterns established in Core module for:
- Service layer
- Repository layer
- Events and listeners
- API resources
- Form requests
- Tests

## Testing Setup

### Configure PHPUnit

Update `phpunit.xml`:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="./vendor/phpunit/phpunit/phpunit.xsd"
         bootstrap="vendor/autoload.php"
         colors="true">
    <testsuites>
        <testsuite name="Unit">
            <directory suffix="Test.php">./tests/Unit</directory>
            <directory suffix="Test.php">./modules/*/Tests/Unit</directory>
        </testsuite>
        <testsuite name="Feature">
            <directory suffix="Test.php">./tests/Feature</directory>
            <directory suffix="Test.php">./modules/*/Tests/Feature</directory>
        </testsuite>
    </testsuites>
</phpunit>
```

### Write Tests

Example test for TenantService:

```php
<?php

namespace Modules\Core\Tests\Unit;

use Tests\TestCase;
use Modules\Core\Models\Tenant;
use Modules\Core\Services\TenantService;

class TenantServiceTest extends TestCase
{
    public function test_can_create_tenant(): void
    {
        $service = app(TenantService::class);

        $tenant = $service->create([
            'domain' => 'example.com',
            'company_name' => 'Example Corp',
            'billing_email' => 'billing@example.com',
        ]);

        $this->assertInstanceOf(Tenant::class, $tenant);
        $this->assertEquals('example.com', $tenant->domain);
        $this->assertDatabaseHas('tenants', [
            'domain' => 'example.com',
        ]);
    }
}
```

## Frontend Setup

### Configure Vite

Update `vite.config.js`:

```javascript
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
});
```

### Setup Vue.js

Update `resources/js/app.js`:

```javascript
import { createApp } from 'vue';
import { createPinia } from 'pinia';
import router from './router';
import App from './App.vue';

const app = createApp(App);
const pinia = createPinia();

app.use(pinia);
app.use(router);

app.mount('#app');
```

### Create Router

Create `resources/js/router/index.js`:

```javascript
import { createRouter, createWebHistory } from 'vue-router';

const routes = [
    {
        path: '/',
        name: 'dashboard',
        component: () => import('../views/Dashboard.vue'),
    },
    // Add more routes
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
```

## API Documentation

### Setup Swagger

```bash
# Publish Swagger config
php artisan vendor:publish --provider "L5Swagger\L5SwaggerServiceProvider"

# Annotate controllers
```

Example controller with Swagger annotations:

```php
<?php

namespace Modules\Sales\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Modules\Sales\Services\Contracts\SalesOrderServiceInterface;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="Sales Orders", description="Sales order management")
 */
class SalesOrderApiController extends Controller
{
    public function __construct(
        private SalesOrderServiceInterface $salesOrderService
    ) {}

    /**
     * @OA\Get(
     *     path="/api/v1/sales/orders",
     *     tags={"Sales Orders"},
     *     summary="List sales orders",
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function index(): JsonResponse
    {
        $orders = $this->salesOrderService->getAllOrders();
        return response()->json($orders);
    }
}
```

Generate documentation:

```bash
php artisan l5-swagger:generate
```

Access at: `http://localhost:8000/api/documentation`

## Best Practices

### Code Organization

1. **Follow SOLID Principles**
2. **Use Dependency Injection**
3. **Keep Controllers Thin**
4. **Business Logic in Services**
5. **Data Access in Repositories**

### Naming Conventions

- **Models**: Singular, PascalCase (SalesOrder)
- **Controllers**: PascalCase + Controller (SalesOrderController)
- **Services**: PascalCase + Service (SalesOrderService)
- **Methods**: camelCase (createOrder, getAllOrders)
- **Variables**: camelCase ($salesOrder, $customerData)

### Git Workflow

```bash
# Feature branch
git checkout -b feature/sales-module

# Regular commits
git add .
git commit -m "feat: implement sales order creation"

# Push to remote
git push origin feature/sales-module

# Create PR for review
```

### Documentation

- Keep README.md updated
- Document all API endpoints
- Add inline comments for complex logic
- Update CHANGELOG.md

## Next Steps

1. Implement remaining core entities (Organization, User, Role, Permission)
2. Build authentication system with Laravel Sanctum
3. Implement RBAC with Spatie Laravel Permission
4. Create remaining business modules (CRM, Inventory, Purchasing, etc.)
5. Build frontend components
6. Write comprehensive tests
7. Setup CI/CD pipeline
8. Deploy to staging environment

## Resources

- [Laravel Documentation](https://laravel.com/docs)
- [Vue.js Documentation](https://vuejs.org/guide)
- [PostgreSQL Documentation](https://www.postgresql.org/docs/)
- [Architecture Documentation](ARCHITECTURE.md)
- [Module Structure Guide](MODULE_STRUCTURE.md)
- [Entity Relationships](ENTITY_RELATIONSHIPS.md)

---

This implementation guide provides the foundation for building the KV-ERP-CRM-SaaS system. Follow the patterns established here for all modules to maintain consistency and quality across the codebase.
