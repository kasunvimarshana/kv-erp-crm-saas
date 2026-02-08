# Setup Guide - KV-ERP-CRM-SaaS

## Quick Start (Development)

This guide will help you set up the KV-ERP-CRM-SaaS development environment.

## Prerequisites

- **PHP**: 8.2 or higher
- **Composer**: 2.x
- **Node.js**: 18 LTS or higher
- **npm**: 9.x or higher
- **PostgreSQL**: 15+ (recommended) or MySQL 8+
- **Redis**: 7+ (for caching and queues)
- **Git**: Latest version

## Installation Steps

### 1. Clone the Repository

```bash
git clone https://github.com/kasunvimarshana/kv-erp-crm-saas.git
cd kv-erp-crm-saas
```

### 2. Install PHP Dependencies

```bash
composer install
```

### 3. Install Node.js Dependencies

```bash
npm install
```

### 4. Environment Configuration

```bash
# Copy the environment template
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 5. Configure Database

Edit `.env` file and configure your database settings:

```env
# Central Database (for tenant management)
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=kv_erp_central
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Tenant Database Configuration (template)
TENANT_DB_HOST=127.0.0.1
TENANT_DB_PORT=5432
TENANT_DB_USERNAME=your_username
TENANT_DB_PASSWORD=your_password
```

### 6. Configure Redis

```env
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
```

### 7. Create Database

```bash
# PostgreSQL
createdb kv_erp_central

# Or using psql
psql -U postgres -c "CREATE DATABASE kv_erp_central;"
```

### 8. Run Migrations

```bash
# Run central database migrations
php artisan migrate

# Expected output: Migrations for tenants and organizations tables
```

### 9. Build Frontend Assets

```bash
# Development mode with hot reload
npm run dev

# Or build for production
npm run build
```

### 10. Start Development Server

```bash
# Start Laravel development server
php artisan serve

# The application will be available at: http://localhost:8000
```

### 11. Start Queue Workers (Separate Terminal)

```bash
# Start queue worker for background jobs
php artisan queue:work
```

---

## Docker Setup (Alternative)

For a containerized development environment:

```bash
# Start all services
docker-compose up -d

# Run migrations
docker-compose exec app php artisan migrate

# Build frontend assets
docker-compose exec app npm run dev

# Access application
# http://localhost
```

---

## Database Setup Details

### Central Database

The central database stores:
- Tenant information
- Tenant-to-database mappings
- System-wide configurations
- Tenant subscription details

### Tenant Databases

Each tenant gets its own isolated database containing:
- Organizations
- Users
- Roles and Permissions
- Business data (customers, products, orders, etc.)

**Note**: Tenant databases are created automatically when a new tenant is provisioned through the TenantService.

---

## Multi-Tenancy Configuration

### Tenant Identification Methods

Configure in `.env`:

```env
# Options: domain, subdomain, header
TENANCY_IDENTIFICATION_METHOD=domain

# Database strategy
TENANCY_DATABASE_STRATEGY=per_tenant
```

### Domain-Based Identification

Example: `tenant1.example.com` → Tenant identified by domain

### Subdomain-Based Identification

Example: `tenant1.myapp.com` → Tenant identified by subdomain "tenant1"

### Header-Based Identification

Example: `X-Tenant-Domain: tenant1` → Tenant identified by HTTP header

---

## Testing Setup

### Configure Test Environment

Create `.env.testing`:

```env
APP_ENV=testing
DB_CONNECTION=testing
DB_DATABASE=kv_erp_test
CACHE_DRIVER=array
QUEUE_CONNECTION=sync
SESSION_DRIVER=array
```

### Create Test Database

```bash
createdb kv_erp_test
```

### Run Tests

```bash
# Run all tests
php artisan test

# Run with coverage
php artisan test --coverage --min=80

# Run specific test suite
php artisan test --testsuite=Unit
php artisan test --testsuite=Feature

# Run specific test
php artisan test --filter=TenantTest
```

---

## Development Workflow

### Code Style

```bash
# Check code style
composer check-style
# or
./vendor/bin/pint --test

# Fix code style automatically
composer fix-style
# or
./vendor/bin/pint
```

### Static Analysis

```bash
# Run PHPStan
composer analyse
# or
./vendor/bin/phpstan analyse
```

### Frontend Linting

```bash
# Check JavaScript/Vue code
npm run lint

# Fix JavaScript/Vue code
npm run lint:fix
```

---

## Creating Your First Tenant

After setup, you can create your first tenant:

```php
use Modules\Core\Models\Tenant;

$tenant = Tenant::create([
    'domain' => 'tenant1.localhost',
    'company_name' => 'Tenant 1 Company',
    'billing_email' => 'billing@tenant1.com',
    'status' => 'active',
    'plan' => 'professional',
    'max_users' => 50,
    'max_organizations' => 5,
]);

// This will automatically:
// 1. Create a dedicated database for the tenant
// 2. Run migrations on the tenant database
// 3. Make tenant ready for use
```

---

## Troubleshooting

### Common Issues

#### 1. "Class not found" Errors

```bash
# Clear and rebuild autoloader
composer dump-autoload
```

#### 2. Permission Errors

```bash
# Set proper permissions
chmod -R 755 storage bootstrap/cache
```

#### 3. Migration Errors

```bash
# Refresh migrations (WARNING: This will drop all tables)
php artisan migrate:fresh

# Rollback one step
php artisan migrate:rollback
```

#### 4. Cache Issues

```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

#### 5. Vite/Asset Issues

```bash
# Clear node modules and reinstall
rm -rf node_modules package-lock.json
npm install
npm run dev
```

---

## Development Tools

### Laravel Telescope (Optional)

For debugging and monitoring:

```bash
composer require laravel/telescope --dev
php artisan telescope:install
php artisan migrate

# Access at: http://localhost:8000/telescope
```

### Laravel Debugbar (Optional)

For development debugging:

```bash
composer require barryvdh/laravel-debugbar --dev
```

### IDE Helpers (Optional)

For better IDE autocomplete:

```bash
composer require --dev barryvdh/laravel-ide-helper
php artisan ide-helper:generate
php artisan ide-helper:models
```

---

## Next Steps

1. ✅ Complete the service layer implementation
2. ✅ Implement authentication system (Laravel Sanctum)
3. ✅ Create API controllers
4. ✅ Build Vue.js frontend components
5. ✅ Write comprehensive tests
6. ✅ Set up CI/CD pipeline

---

## Additional Resources

- [Laravel Documentation](https://laravel.com/docs/10.x)
- [Vue.js 3 Documentation](https://vuejs.org/guide/introduction.html)
- [PostgreSQL Documentation](https://www.postgresql.org/docs/)
- [Project Architecture](ARCHITECTURE.md)
- [Module Structure](MODULE_STRUCTURE.md)
- [Contributing Guidelines](CONTRIBUTING.md)
- [Implementation Status](IMPLEMENTATION_STATUS.md)

---

## Support

For issues and questions:
- GitHub Issues: https://github.com/kasunvimarshana/kv-erp-crm-saas/issues
- Documentation: See `docs/` directory
- Email: dev@kv-erp-crm-saas.com

---

**Built with ❤️ using Laravel 10, Vue.js 3, PostgreSQL, and modern web technologies**
