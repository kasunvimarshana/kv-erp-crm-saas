# Module Structure Guide

## Overview

This document defines the standard structure for all modules in KV-ERP-CRM-SaaS. Following this structure ensures consistency, maintainability, and scalability across the entire system.

## Module Organization Philosophy

Inspired by Odoo's modular architecture and Laravel's best practices, each module in KV-ERP-CRM-SaaS is:

- **Self-Contained**: Contains all necessary components (models, controllers, views, etc.)
- **Loosely Coupled**: Communicates with other modules through well-defined interfaces and events
- **Highly Cohesive**: Focuses on a single business domain or functional area
- **Extensible**: Can be extended or overridden without modifying core code
- **Testable**: Includes comprehensive test coverage

## Standard Module Directory Structure

```
modules/
└── {ModuleName}/
    ├── __module__.php              # Module manifest and metadata
    ├── Config/                     # Module-specific configuration
    │   ├── config.php              # General module config
    │   ├── permissions.php         # Module permissions definition
    │   └── menu.php                # Menu items configuration
    ├── Controllers/                # HTTP request handlers
    │   ├── Web/                    # Web controllers
    │   │   └── {Module}Controller.php
    │   └── Api/                    # API controllers
    │       └── {Module}ApiController.php
    ├── Models/                     # Eloquent models (Domain entities)
    │   ├── {MainEntity}.php
    │   ├── {RelatedEntity}.php
    │   └── Traits/                 # Reusable model traits
    ├── Services/                   # Business logic services
    │   ├── {Module}Service.php
    │   └── Contracts/              # Service interfaces
    │       └── {Module}ServiceInterface.php
    ├── Repositories/               # Data access layer
    │   ├── {Module}Repository.php
    │   └── Contracts/              # Repository interfaces
    │       └── {Module}RepositoryInterface.php
    ├── Events/                     # Domain events
    │   ├── {Entity}Created.php
    │   ├── {Entity}Updated.php
    │   └── {Entity}Deleted.php
    ├── Listeners/                  # Event listeners
    │   └── {EventName}Listener.php
    ├── Observers/                  # Model observers
    │   └── {Model}Observer.php
    ├── Policies/                   # Authorization policies
    │   └── {Model}Policy.php
    ├── Middleware/                 # Module-specific middleware
    │   └── {Module}Middleware.php
    ├── Views/                      # Blade views
    │   ├── index.blade.php
    │   ├── create.blade.php
    │   ├── edit.blade.php
    │   ├── show.blade.php
    │   └── partials/               # Partial views
    ├── Resources/                  # API resources (transformers)
    │   ├── {Entity}Resource.php
    │   └── {Entity}Collection.php
    ├── Requests/                   # Form requests (validation)
    │   ├── Store{Entity}Request.php
    │   └── Update{Entity}Request.php
    ├── Jobs/                       # Queue jobs
    │   └── {Process}{Entity}Job.php
    ├── Notifications/              # Module notifications
    │   └── {Entity}{Action}Notification.php
    ├── Mail/                       # Email templates
    │   └── {Entity}{Action}Mail.php
    ├── Rules/                      # Custom validation rules
    │   └── {Custom}Rule.php
    ├── Exceptions/                 # Custom exceptions
    │   └── {Module}Exception.php
    ├── Migrations/                 # Database migrations
    │   └── YYYY_MM_DD_HHMMSS_create_{table}_table.php
    ├── Seeders/                    # Database seeders
    │   ├── {Module}DatabaseSeeder.php
    │   └── {Entity}TableSeeder.php
    ├── Factories/                  # Model factories for testing
    │   └── {Model}Factory.php
    ├── Tests/                      # Module tests
    │   ├── Unit/                   # Unit tests
    │   ├── Feature/                # Feature tests
    │   └── Integration/            # Integration tests
    ├── Routes/                     # Module routes
    │   ├── web.php                 # Web routes
    │   ├── api.php                 # API routes
    │   └── console.php             # Console routes (optional)
    ├── Console/                    # Console commands
    │   └── Commands/
    │       └── {Module}Command.php
    ├── Assets/                     # Frontend assets
    │   ├── js/                     # JavaScript files
    │   │   ├── components/         # Vue/React components
    │   │   └── {module}.js
    │   ├── css/                    # Stylesheets
    │   │   └── {module}.css
    │   └── images/                 # Module-specific images
    ├── Docs/                       # Module documentation
    │   ├── README.md               # Module overview
    │   ├── API.md                  # API documentation
    │   └── CHANGELOG.md            # Version history
    └── composer.json               # Module dependencies (optional)
```

## Module Manifest (__module__.php)

The `__module__.php` file is the module's manifest, containing metadata and configuration:

```php
<?php

return [
    // Basic Information
    'name' => 'Sales',
    'display_name' => 'Sales Management',
    'description' => 'Comprehensive sales order management, quotations, and invoicing',
    'version' => '1.0.0',
    'author' => 'Your Company',
    'author_email' => 'dev@yourcompany.com',
    
    // Module Classification
    'category' => 'core', // core, extension, third-party
    'type' => 'module', // module, plugin, theme
    
    // Dependencies
    'dependencies' => [
        'Core',           // Core/Foundation module
        'Customer',       // Customer module
        'Product',        // Product module
        'Inventory',      // Inventory module
    ],
    
    // Optional dependencies (soft dependencies)
    'optional_dependencies' => [
        'Accounting',     // If installed, creates journal entries
        'CRM',            // If installed, links to opportunities
    ],
    
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
        'Modules\\Sales\\Providers\\SalesServiceProvider',
        'Modules\\Sales\\Providers\\EventServiceProvider',
        'Modules\\Sales\\Providers\\RouteServiceProvider',
    ],
    
    // Aliases
    'aliases' => [
        'Sales' => 'Modules\\Sales\\Facades\\Sales',
    ],
    
    // Configuration files to publish
    'config' => [
        'sales' => 'Config/config.php',
    ],
    
    // Assets to publish
    'assets' => [
        'sales-assets' => 'Assets',
    ],
    
    // Permissions required by this module
    'permissions' => [
        'sales.view',
        'sales.create',
        'sales.edit',
        'sales.delete',
        'sales.approve',
        'sales.reports.view',
    ],
    
    // Menu items provided by this module
    'menu' => [
        [
            'title' => 'Sales',
            'icon' => 'shopping-cart',
            'route' => 'sales.index',
            'permission' => 'sales.view',
            'order' => 20,
            'children' => [
                [
                    'title' => 'Quotations',
                    'route' => 'sales.quotations.index',
                    'permission' => 'sales.view',
                ],
                [
                    'title' => 'Sales Orders',
                    'route' => 'sales.orders.index',
                    'permission' => 'sales.view',
                ],
                [
                    'title' => 'Invoices',
                    'route' => 'sales.invoices.index',
                    'permission' => 'sales.view',
                ],
            ],
        ],
    ],
    
    // Widgets/Dashboard components
    'widgets' => [
        'sales_overview' => 'Modules\\Sales\\Widgets\\SalesOverviewWidget',
        'recent_orders' => 'Modules\\Sales\\Widgets\\RecentOrdersWidget',
    ],
    
    // API endpoints provided (for documentation)
    'api' => [
        'version' => 'v1',
        'prefix' => 'sales',
        'endpoints' => [
            'GET /orders',
            'POST /orders',
            'GET /orders/{id}',
            'PUT /orders/{id}',
            'DELETE /orders/{id}',
        ],
    ],
    
    // Module settings/preferences
    'settings' => [
        'default_payment_terms' => 'Net 30',
        'auto_invoice' => true,
        'require_approval' => false,
    ],
];
```

## Key Components Explained

### 1. Controllers

**Purpose**: Handle HTTP requests and return responses.

**Best Practices**:
- Keep controllers thin (delegate to services)
- One controller per resource (RESTful)
- Use form requests for validation
- Return appropriate HTTP status codes

```php
// Example: Sales Order Controller
namespace Modules\Sales\Controllers\Api;

use Modules\Sales\Requests\StoreSalesOrderRequest;
use Modules\Sales\Services\Contracts\SalesOrderServiceInterface;
use Modules\Sales\Resources\SalesOrderResource;

class SalesOrderApiController extends Controller
{
    public function __construct(
        private SalesOrderServiceInterface $salesOrderService
    ) {}

    public function index()
    {
        $orders = $this->salesOrderService->getAllOrders();
        return SalesOrderResource::collection($orders);
    }

    public function store(StoreSalesOrderRequest $request)
    {
        $order = $this->salesOrderService->createOrder($request->validated());
        return new SalesOrderResource($order);
    }
}
```

### 2. Models

**Purpose**: Represent domain entities and database tables.

**Best Practices**:
- Define relationships clearly
- Use accessors/mutators for data transformation
- Implement model events
- Use traits for shared behavior
- Add query scopes

```php
namespace Modules\Sales\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesOrder extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'order_number', 'customer_id', 'order_date', 
        'total_amount', 'status', 'notes'
    ];

    protected $casts = [
        'order_date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    // Relationships
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function orderLines(): HasMany
    {
        return $this->hasMany(SalesOrderLine::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    // Accessors
    public function getFormattedTotalAttribute(): string
    {
        return number_format($this->total_amount, 2);
    }
}
```

### 3. Services

**Purpose**: Encapsulate business logic and orchestrate operations.

**Best Practices**:
- One service per aggregate root
- Handle transactions
- Dispatch events
- Implement interface contracts
- Coordinate between repositories

```php
namespace Modules\Sales\Services;

use Modules\Sales\Repositories\Contracts\SalesOrderRepositoryInterface;
use Modules\Sales\Events\SalesOrderCreated;
use Illuminate\Support\Facades\DB;

class SalesOrderService implements SalesOrderServiceInterface
{
    public function __construct(
        private SalesOrderRepositoryInterface $orderRepository
    ) {}

    public function createOrder(array $data): SalesOrder
    {
        return DB::transaction(function () use ($data) {
            // Create order
            $order = $this->orderRepository->create($data);
            
            // Create order lines
            foreach ($data['lines'] as $line) {
                $order->orderLines()->create($line);
            }
            
            // Update inventory
            $this->updateInventory($order);
            
            // Dispatch event
            event(new SalesOrderCreated($order));
            
            return $order;
        });
    }
}
```

### 4. Repositories

**Purpose**: Abstract data access logic.

**Best Practices**:
- Interface-based contracts
- Query optimization
- Caching strategies
- Avoid business logic

```php
namespace Modules\Sales\Repositories;

use Modules\Sales\Models\SalesOrder;

class SalesOrderRepository implements SalesOrderRepositoryInterface
{
    public function __construct(private SalesOrder $model) {}

    public function findById(int $id): ?SalesOrder
    {
        return $this->model
            ->with(['customer', 'orderLines'])
            ->find($id);
    }

    public function getAllPaginated(int $perPage = 15)
    {
        return $this->model
            ->with(['customer'])
            ->latest()
            ->paginate($perPage);
    }

    public function create(array $data): SalesOrder
    {
        return $this->model->create($data);
    }
}
```

### 5. Events & Listeners

**Purpose**: Decouple modules through event-driven architecture.

**Event Example**:
```php
namespace Modules\Sales\Events;

use Modules\Sales\Models\SalesOrder;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SalesOrderCreated
{
    use Dispatchable, SerializesModels;

    public function __construct(public SalesOrder $order) {}
}
```

**Listener Example**:
```php
namespace Modules\Inventory\Listeners;

use Modules\Sales\Events\SalesOrderCreated;

class ReserveInventoryListener
{
    public function handle(SalesOrderCreated $event): void
    {
        // Reserve inventory for the order
        foreach ($event->order->orderLines as $line) {
            $this->inventoryService->reserve(
                $line->product_id,
                $line->quantity
            );
        }
    }
}
```

## Module Lifecycle

### 1. Installation
- Run migrations
- Seed initial data
- Register permissions
- Publish assets and config

### 2. Activation
- Load service providers
- Register routes
- Register event listeners
- Initialize module

### 3. Upgrade
- Run new migrations
- Update configurations
- Clear caches
- Re-publish assets

### 4. Deactivation
- Unregister routes
- Remove from autoload
- Maintain data integrity

### 5. Uninstallation
- Rollback migrations (optional)
- Remove module files
- Clean up configurations

## Module Dependency Management

### Dependency Resolution

The system resolves module dependencies automatically:

1. Check required dependencies are installed
2. Load dependencies in correct order
3. Validate version compatibility
4. Handle circular dependencies

```php
// Example dependency graph
Core (no dependencies)
  ├── Customer (depends: Core)
  ├── Product (depends: Core)
  ├── Inventory (depends: Core, Product)
  └── Sales (depends: Core, Customer, Product, Inventory)
```

### Inter-Module Communication

Modules communicate through:

1. **Events**: For asynchronous, decoupled communication
2. **Services**: For direct, synchronous operations
3. **APIs**: For external or cross-boundary communication
4. **Database**: Shared data through foreign keys (carefully)

## Testing Strategy

### Unit Tests
Test individual components in isolation:
- Services
- Repositories
- Models (methods, scopes, accessors)
- Validators

### Feature Tests
Test HTTP endpoints and workflows:
- API endpoints
- Web controllers
- Form submissions
- Authentication/authorization

### Integration Tests
Test module interactions:
- Cross-module event handling
- Service collaborations
- Database transactions

```php
// Example feature test
namespace Modules\Sales\Tests\Feature;

use Tests\TestCase;
use Modules\Sales\Models\SalesOrder;

class SalesOrderTest extends TestCase
{
    public function test_can_create_sales_order()
    {
        $response = $this->postJson('/api/v1/sales/orders', [
            'customer_id' => 1,
            'order_date' => now(),
            'lines' => [
                ['product_id' => 1, 'quantity' => 10, 'price' => 100],
            ],
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('sales_orders', [
            'customer_id' => 1,
        ]);
    }
}
```

## Documentation Requirements

Each module should include:

### README.md
- Module overview
- Features list
- Installation instructions
- Configuration guide
- Usage examples
- Troubleshooting

### API.md
- API endpoints
- Request/response examples
- Authentication requirements
- Rate limiting
- Error codes

### CHANGELOG.md
- Version history
- New features
- Bug fixes
- Breaking changes

## Best Practices Summary

1. **Single Responsibility**: Each component has one job
2. **Dependency Injection**: Use constructor injection
3. **Interface Contracts**: Program to interfaces, not implementations
4. **Event-Driven**: Use events for cross-module communication
5. **Test Coverage**: Aim for 80%+ code coverage
6. **Documentation**: Keep docs up-to-date
7. **Versioning**: Use semantic versioning
8. **Security**: Validate all inputs, authorize all actions
9. **Performance**: Optimize queries, use caching
10. **Maintainability**: Follow SOLID principles

## Conclusion

This module structure provides a consistent, scalable foundation for building enterprise-grade ERP-CRM-SaaS applications. By following these guidelines, developers can create modules that are maintainable, testable, and easily integrated with the rest of the system.
