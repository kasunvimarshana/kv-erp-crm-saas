# Project Structure

## Overview

This document outlines the complete directory structure of the KV-ERP-CRM-SaaS system, combining Laravel best practices with a modular architecture inspired by Odoo.

## Root Directory Structure

```
kv-erp-crm-saas/
├── .github/                        # GitHub workflows, templates
│   ├── workflows/                  # CI/CD pipelines
│   │   ├── tests.yml
│   │   ├── deploy.yml
│   │   └── code-quality.yml
│   └── ISSUE_TEMPLATE/
├── app/                            # Core application code (Laravel standard)
│   ├── Console/                    # Artisan commands
│   │   └── Kernel.php
│   ├── Exceptions/                 # Exception handling
│   │   └── Handler.php
│   ├── Http/                       # HTTP layer
│   │   ├── Controllers/            # Base controllers
│   │   ├── Middleware/             # Global middleware
│   │   └── Kernel.php
│   ├── Providers/                  # Service providers
│   │   ├── AppServiceProvider.php
│   │   ├── AuthServiceProvider.php
│   │   ├── EventServiceProvider.php
│   │   └── RouteServiceProvider.php
│   └── Support/                    # Helper classes
│       ├── Helpers.php
│       └── Traits/
├── bootstrap/                      # Laravel bootstrap
│   ├── app.php
│   └── cache/
├── config/                         # Configuration files
│   ├── app.php
│   ├── database.php
│   ├── cache.php
│   ├── queue.php
│   ├── mail.php
│   ├── services.php
│   ├── modules.php                 # Module configuration
│   ├── tenant.php                  # Multi-tenancy config
│   └── permissions.php             # Permissions config
├── database/                       # Database files
│   ├── factories/                  # Model factories
│   ├── migrations/                 # Core migrations
│   │   ├── 2024_01_01_000001_create_tenants_table.php
│   │   ├── 2024_01_01_000002_create_organizations_table.php
│   │   ├── 2024_01_01_000003_create_users_table.php
│   │   ├── 2024_01_01_000004_create_roles_table.php
│   │   ├── 2024_01_01_000005_create_permissions_table.php
│   │   └── ...
│   └── seeders/                    # Database seeders
│       ├── DatabaseSeeder.php
│       ├── TenantSeeder.php
│       ├── RoleSeeder.php
│       └── PermissionSeeder.php
├── modules/                        # Business modules (core feature)
│   ├── Core/                       # Foundation module
│   │   ├── __module__.php
│   │   ├── Config/
│   │   ├── Controllers/
│   │   │   ├── Web/
│   │   │   └── Api/
│   │   ├── Models/
│   │   │   ├── Tenant.php
│   │   │   ├── Organization.php
│   │   │   ├── User.php
│   │   │   ├── Role.php
│   │   │   └── Permission.php
│   │   ├── Services/
│   │   │   ├── TenantService.php
│   │   │   ├── OrganizationService.php
│   │   │   └── AuthService.php
│   │   ├── Repositories/
│   │   ├── Events/
│   │   ├── Listeners/
│   │   ├── Middleware/
│   │   │   ├── TenantIdentification.php
│   │   │   └── CheckPermission.php
│   │   ├── Policies/
│   │   ├── Views/
│   │   ├── Resources/
│   │   ├── Requests/
│   │   ├── Jobs/
│   │   ├── Migrations/
│   │   ├── Seeders/
│   │   ├── Tests/
│   │   ├── Routes/
│   │   │   ├── web.php
│   │   │   └── api.php
│   │   ├── Assets/
│   │   └── Docs/
│   ├── CRM/                        # Customer Relationship Management
│   │   ├── __module__.php
│   │   ├── Controllers/
│   │   ├── Models/
│   │   │   ├── Lead.php
│   │   │   ├── Opportunity.php
│   │   │   ├── Activity.php
│   │   │   └── Campaign.php
│   │   ├── Services/
│   │   │   ├── LeadService.php
│   │   │   └── OpportunityService.php
│   │   └── [same structure as Core]
│   ├── Sales/                      # Sales Management
│   │   ├── __module__.php
│   │   ├── Controllers/
│   │   ├── Models/
│   │   │   ├── SalesOrder.php
│   │   │   ├── SalesOrderLine.php
│   │   │   ├── Quotation.php
│   │   │   └── PriceList.php
│   │   ├── Services/
│   │   │   ├── SalesOrderService.php
│   │   │   └── QuotationService.php
│   │   └── [same structure]
│   ├── Customer/                   # Customer Management
│   │   ├── __module__.php
│   │   ├── Models/
│   │   │   ├── Customer.php
│   │   │   ├── Contact.php
│   │   │   └── CustomerAddress.php
│   │   └── [same structure]
│   ├── Inventory/                  # Inventory Management
│   │   ├── __module__.php
│   │   ├── Models/
│   │   │   ├── Product.php
│   │   │   ├── ProductCategory.php
│   │   │   ├── Warehouse.php
│   │   │   ├── StockItem.php
│   │   │   ├── StockMovement.php
│   │   │   └── UnitOfMeasure.php
│   │   ├── Services/
│   │   │   ├── InventoryService.php
│   │   │   └── StockMovementService.php
│   │   └── [same structure]
│   ├── Purchasing/                 # Procurement
│   │   ├── __module__.php
│   │   ├── Models/
│   │   │   ├── Vendor.php
│   │   │   ├── PurchaseOrder.php
│   │   │   ├── PurchaseOrderLine.php
│   │   │   └── GoodsReceipt.php
│   │   └── [same structure]
│   ├── Accounting/                 # Financial Accounting
│   │   ├── __module__.php
│   │   ├── Models/
│   │   │   ├── Account.php
│   │   │   ├── Journal.php
│   │   │   ├── JournalEntry.php
│   │   │   ├── JournalEntryLine.php
│   │   │   ├── Invoice.php
│   │   │   ├── InvoiceLine.php
│   │   │   ├── Payment.php
│   │   │   └── Tax.php
│   │   └── [same structure]
│   ├── HR/                         # Human Resources
│   │   ├── __module__.php
│   │   ├── Models/
│   │   │   ├── Employee.php
│   │   │   ├── Department.php
│   │   │   ├── Attendance.php
│   │   │   ├── Leave.php
│   │   │   └── Payroll.php
│   │   └── [same structure]
│   ├── Manufacturing/              # Manufacturing Resource Planning
│   │   ├── __module__.php
│   │   ├── Models/
│   │   │   ├── BillOfMaterials.php
│   │   │   ├── WorkOrder.php
│   │   │   ├── ProductionOrder.php
│   │   │   └── QualityCheck.php
│   │   └── [same structure]
│   ├── POS/                        # Point of Sale
│   │   ├── __module__.php
│   │   ├── Models/
│   │   │   ├── POSSession.php
│   │   │   ├── POSOrder.php
│   │   │   ├── CashRegister.php
│   │   │   └── POSPayment.php
│   │   └── [same structure]
│   ├── Project/                    # Project Management
│   │   ├── __module__.php
│   │   ├── Models/
│   │   │   ├── Project.php
│   │   │   ├── Task.php
│   │   │   ├── Milestone.php
│   │   │   └── TimeEntry.php
│   │   └── [same structure]
│   ├── Reporting/                  # Reports & Analytics
│   │   ├── __module__.php
│   │   ├── Models/
│   │   │   ├── Report.php
│   │   │   ├── Dashboard.php
│   │   │   └── Widget.php
│   │   └── [same structure]
│   └── Integration/                # Third-party integrations
│       ├── __module__.php
│       ├── Models/
│       │   ├── Integration.php
│       │   ├── Webhook.php
│       │   └── APIKey.php
│       └── [same structure]
├── public/                         # Public web root
│   ├── index.php                   # Entry point
│   ├── .htaccess
│   ├── css/                        # Compiled CSS
│   ├── js/                         # Compiled JavaScript
│   ├── images/
│   ├── fonts/
│   └── storage -> ../storage/app/public
├── resources/                      # Frontend resources
│   ├── css/                        # Source CSS
│   ├── js/                         # Source JavaScript
│   │   ├── app.js                  # Main JS entry
│   │   ├── bootstrap.js
│   │   └── components/             # Vue/React components
│   │       ├── common/
│   │       └── modules/
│   ├── views/                      # Blade templates
│   │   ├── layouts/
│   │   │   ├── app.blade.php
│   │   │   ├── guest.blade.php
│   │   │   └── admin.blade.php
│   │   ├── auth/
│   │   │   ├── login.blade.php
│   │   │   ├── register.blade.php
│   │   │   └── passwords/
│   │   ├── dashboard.blade.php
│   │   ├── errors/
│   │   │   ├── 404.blade.php
│   │   │   ├── 500.blade.php
│   │   │   └── 503.blade.php
│   │   └── components/             # Blade components
│   └── lang/                       # Translations
│       ├── en/
│       ├── es/
│       └── fr/
├── routes/                         # Global routes
│   ├── web.php                     # Web routes
│   ├── api.php                     # API routes
│   ├── console.php                 # Console routes
│   └── channels.php                # Broadcasting channels
├── storage/                        # Storage directory
│   ├── app/
│   │   ├── public/                 # Publicly accessible files
│   │   └── private/                # Private files
│   ├── framework/
│   │   ├── cache/
│   │   ├── sessions/
│   │   ├── testing/
│   │   └── views/
│   ├── logs/
│   │   └── laravel.log
│   └── tenants/                    # Tenant-specific storage
│       ├── {tenant_id}/
│       │   ├── uploads/
│       │   └── exports/
├── tests/                          # Application tests
│   ├── Unit/                       # Unit tests
│   │   ├── Models/
│   │   ├── Services/
│   │   └── Repositories/
│   ├── Feature/                    # Feature tests
│   │   ├── Auth/
│   │   ├── Api/
│   │   └── Modules/
│   ├── Integration/                # Integration tests
│   └── Browser/                    # Browser tests (Dusk)
├── docker/                         # Docker configuration
│   ├── php/
│   │   └── Dockerfile
│   ├── nginx/
│   │   ├── Dockerfile
│   │   └── default.conf
│   ├── postgres/
│   │   └── init.sql
│   └── redis/
├── docs/                           # Documentation
│   ├── ARCHITECTURE.md             # System architecture
│   ├── MODULE_STRUCTURE.md         # Module guide
│   ├── ENTITY_RELATIONSHIPS.md     # Data model
│   ├── API_REFERENCE.md            # API documentation
│   ├── DEPLOYMENT.md               # Deployment guide
│   ├── DEVELOPMENT.md              # Development guide
│   └── images/                     # Documentation images
├── scripts/                        # Utility scripts
│   ├── setup.sh                    # Initial setup
│   ├── deploy.sh                   # Deployment script
│   ├── backup.sh                   # Backup script
│   └── install-module.sh           # Module installation
├── .env.example                    # Environment template
├── .gitignore
├── .editorconfig
├── .php-cs-fixer.php               # PHP CS Fixer config
├── phpstan.neon                    # PHPStan config
├── phpunit.xml                     # PHPUnit config
├── composer.json                   # PHP dependencies
├── composer.lock
├── package.json                    # Node dependencies
├── package-lock.json
├── vite.config.js                  # Vite configuration
├── tailwind.config.js              # Tailwind CSS config
├── postcss.config.js               # PostCSS config
├── tsconfig.json                   # TypeScript config (if using TS)
├── docker-compose.yml              # Docker Compose
├── docker-compose.prod.yml         # Production Docker Compose
├── Makefile                        # Make commands
├── artisan                         # Artisan CLI
├── README.md                       # Project readme
└── LICENSE                         # License file
```

## Key Directories Explained

### `/app` - Core Application

Contains Laravel's core application code:
- **Console**: Artisan commands
- **Exceptions**: Global exception handling
- **Http**: Controllers, middleware, requests (for non-module code)
- **Providers**: Service providers for bootstrapping
- **Support**: Helper classes and traits

### `/modules` - Business Modules

The heart of the application. Each module is self-contained with:
- Complete MVC structure
- Business logic (Services)
- Data access (Repositories)
- Tests
- Routes
- Assets
- Documentation

**Philosophy**: Modules can be developed, tested, and deployed independently.

### `/config` - Configuration

System-wide configuration files:
- Laravel standard configs (app, database, cache, etc.)
- Custom configs (modules, tenant, permissions)

### `/database` - Database Assets

- **migrations**: Core/shared database migrations
- **seeders**: Initial data seeding
- **factories**: Model factories for testing

Note: Module-specific migrations are in module directories.

### `/public` - Web Root

Public-facing files:
- Entry point (index.php)
- Compiled assets (CSS, JS)
- Static files (images, fonts)
- Symlink to storage

### `/resources` - Frontend Resources

- **css**: Source stylesheets
- **js**: JavaScript/TypeScript source
- **views**: Blade templates
- **lang**: Translations

### `/routes` - Global Routes

- **web.php**: Web interface routes
- **api.php**: API routes (global)
- **console.php**: Console commands
- **channels.php**: Broadcast channels

Module-specific routes are in module directories.

### `/storage` - File Storage

- **app**: Application files (public/private)
- **framework**: Framework caches, sessions
- **logs**: Application logs
- **tenants**: Tenant-specific files (isolated)

### `/tests` - Test Suite

- **Unit**: Component-level tests
- **Feature**: Endpoint/integration tests
- **Integration**: Cross-module tests
- **Browser**: End-to-end tests (Dusk)

### `/docker` - Container Configuration

Docker configurations for:
- PHP-FPM
- Nginx
- PostgreSQL
- Redis

### `/docs` - Documentation

Comprehensive documentation:
- Architecture guides
- API reference
- Development guides
- Deployment instructions

### `/scripts` - Utility Scripts

Shell scripts for:
- Setup and installation
- Deployment
- Backups
- Module management

## Module Directory Structure (Detailed)

Each module follows this structure:

```
ModuleName/
├── __module__.php              # Module manifest
├── Config/                     # Module config
│   ├── config.php
│   ├── permissions.php
│   └── menu.php
├── Controllers/
│   ├── Web/                    # Web controllers
│   │   ├── {Resource}Controller.php
│   │   └── Dashboard{Module}Controller.php
│   └── Api/                    # API controllers (versioned)
│       ├── V1/
│       │   └── {Resource}ApiController.php
│       └── V2/
├── Models/                     # Domain models
│   ├── {MainEntity}.php
│   ├── {RelatedEntity}.php
│   └── Traits/
│       └── {Trait}.php
├── Services/                   # Business logic
│   ├── {Module}Service.php
│   └── Contracts/
│       └── {Module}ServiceInterface.php
├── Repositories/               # Data access
│   ├── {Module}Repository.php
│   └── Contracts/
│       └── {Module}RepositoryInterface.php
├── Events/                     # Domain events
│   ├── {Entity}Created.php
│   ├── {Entity}Updated.php
│   └── {Entity}Deleted.php
├── Listeners/                  # Event listeners
│   └── {Event}Listener.php
├── Observers/                  # Model observers
│   └── {Model}Observer.php
├── Policies/                   # Authorization
│   └── {Model}Policy.php
├── Middleware/                 # Module middleware
│   └── {Custom}Middleware.php
├── Views/                      # Blade views
│   ├── index.blade.php
│   ├── create.blade.php
│   ├── edit.blade.php
│   ├── show.blade.php
│   └── partials/
├── Resources/                  # API resources
│   ├── {Entity}Resource.php
│   └── {Entity}Collection.php
├── Requests/                   # Form requests
│   ├── Store{Entity}Request.php
│   └── Update{Entity}Request.php
├── Jobs/                       # Queue jobs
│   └── {Process}{Entity}Job.php
├── Notifications/              # Notifications
│   └── {Entity}{Action}Notification.php
├── Mail/                       # Email templates
│   └── {Entity}{Action}Mail.php
├── Rules/                      # Validation rules
│   └── {Custom}Rule.php
├── Exceptions/                 # Custom exceptions
│   └── {Module}Exception.php
├── Migrations/                 # Database migrations
│   └── {timestamp}_create_{table}.php
├── Seeders/                    # Database seeders
│   ├── {Module}DatabaseSeeder.php
│   └── {Entity}TableSeeder.php
├── Factories/                  # Model factories
│   └── {Model}Factory.php
├── Tests/                      # Module tests
│   ├── Unit/
│   │   ├── Models/
│   │   ├── Services/
│   │   └── Repositories/
│   ├── Feature/
│   │   ├── Api/
│   │   └── Web/
│   └── Integration/
├── Routes/                     # Module routes
│   ├── web.php
│   ├── api.php
│   └── console.php
├── Console/                    # Console commands
│   └── Commands/
│       └── {Module}Command.php
├── Assets/                     # Frontend assets
│   ├── js/
│   │   ├── components/
│   │   └── {module}.js
│   ├── css/
│   │   └── {module}.css
│   └── images/
├── Docs/                       # Module docs
│   ├── README.md
│   ├── API.md
│   └── CHANGELOG.md
└── composer.json               # Module dependencies (optional)
```

## File Naming Conventions

### PHP Files
- **Models**: PascalCase, singular (e.g., `SalesOrder.php`)
- **Controllers**: PascalCase + `Controller` (e.g., `SalesOrderController.php`)
- **Services**: PascalCase + `Service` (e.g., `SalesOrderService.php`)
- **Repositories**: PascalCase + `Repository` (e.g., `SalesOrderRepository.php`)
- **Migrations**: snake_case with timestamp (e.g., `2024_01_01_000001_create_sales_orders_table.php`)

### JavaScript/TypeScript Files
- **Components**: PascalCase (e.g., `SalesOrderForm.vue`)
- **Utilities**: camelCase (e.g., `formatCurrency.js`)

### CSS Files
- **Stylesheets**: kebab-case (e.g., `sales-order.css`)

### Blade Views
- **Views**: kebab-case (e.g., `sales-order-list.blade.php`)

## Environment-Specific Directories

### Development
- `/storage/logs/` - Detailed logging
- `/storage/debugbar/` - Debug bar cache

### Production
- `/storage/logs/` - Error logging only
- `/storage/cache/` - Heavy caching

### Testing
- `/storage/framework/testing/` - Test artifacts
- `/database/factories/` - Test data generation

## Conclusion

This project structure provides:
- **Clear separation of concerns**
- **Modular architecture** for scalability
- **Laravel standards** for familiarity
- **Odoo-inspired modularity** for flexibility
- **Easy navigation** for developers
- **Testability** at all levels
- **Maintainability** for long-term success

Each directory has a specific purpose, making the codebase easy to understand and navigate for both new and experienced developers.
