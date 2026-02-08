# GitHub Copilot Instructions - KV-ERP-CRM-SaaS

## Project Overview

KV-ERP-CRM-SaaS is a comprehensive, enterprise-grade SaaS ERP-CRM system built with Laravel 10+ (PHP 8.2+) and Vue.js 3. The system follows Clean Architecture and Domain-Driven Design (DDD) principles with a modular, maintainable structure. It supports multi-tenant, multi-organization operations with database-per-tenant isolation.

## Technology Stack

### Backend
- **Framework**: Laravel 10+ with PHP 8.2+
- **Database**: PostgreSQL 15+ (primary) / MySQL 8+ (alternative)
- **Cache/Queue**: Redis 7+
- **Search**: Meilisearch / Elasticsearch
- **Authentication**: Laravel Sanctum for API, Laravel Passport for OAuth2

### Frontend
- **Framework**: Vue.js 3 with Composition API
- **Build Tool**: Vite 4+
- **State Management**: Pinia
- **UI Framework**: Tailwind CSS
- **HTTP Client**: Axios

### DevOps
- **Containerization**: Docker with Docker Compose
- **Orchestration**: Kubernetes for production
- **CI/CD**: GitHub Actions
- **Web Server**: Nginx
- **Monitoring**: Prometheus + Grafana, ELK Stack
- **Error Tracking**: Sentry

## Coding Standards

### PHP Standards (PSR-12)

- **Always** use strict types: `declare(strict_types=1);` at the top of PHP files
- **Always** use type hints for parameters and return types
- Follow PSR-12 coding standard
- Use final classes by default unless inheritance is specifically needed
- Use readonly properties where applicable (PHP 8.1+)
- Use dependency injection, avoid static calls except Laravel facades
- Follow SOLID principles
- Keep methods small and focused (single responsibility)
- Use meaningful variable and method names

**Example:**
```php
<?php

declare(strict_types=1);

namespace Modules\Sales\Services;

use Modules\Sales\Models\SalesOrder;
use Modules\Sales\Repositories\Contracts\SalesOrderRepositoryInterface;

final class SalesOrderService implements SalesOrderServiceInterface
{
    public function __construct(
        private readonly SalesOrderRepositoryInterface $repository
    ) {}

    public function create(array $data): SalesOrder
    {
        // Implementation
    }
}
```

### JavaScript/TypeScript Standards

- Use ES6+ features consistently
- Use `const` by default, `let` when reassignment is needed, **never** use `var`
- Use arrow functions where appropriate
- Follow Vue.js 3 Composition API best practices
- Use single quotes for strings
- Always use semicolons
- Add JSDoc comments for complex functions
- Use async/await instead of raw promises

### CSS Standards

- **Prefer** Tailwind CSS utility classes over custom CSS
- Use mobile-first responsive design approach
- Use `@apply` directive sparingly for repeated patterns
- Keep custom CSS minimal and well-organized
- Follow BEM naming convention if custom classes are necessary

## Architecture Patterns

### Layered Architecture

Follow this request flow:
```
HTTP Request → Controller → Service → Repository → Database
                    ↓
            Event Dispatcher (if needed)
```

**Controllers**: Handle HTTP requests, validate input, call services, return responses
**Services**: Contain business logic, orchestrate operations, dispatch events
**Repositories**: Abstract data access, implement repository interfaces
**Models**: Eloquent models representing entities, define relationships

### Module Structure

Each module follows this structure:
```
modules/ModuleName/
├── Controllers/         # HTTP request handlers
├── Models/             # Eloquent models
├── Services/           # Business logic
├── Repositories/       # Data access layer
│   ├── Contracts/      # Repository interfaces
│   └── Eloquent/       # Eloquent implementations
├── Events/             # Domain events
├── Listeners/          # Event listeners
├── Policies/           # Authorization policies
├── Requests/           # Form request validation
├── Resources/          # API resource transformers
├── Jobs/               # Queue jobs
├── Migrations/         # Database migrations
├── Seeders/            # Database seeders
├── Tests/              # Module tests
│   ├── Unit/
│   └── Feature/
├── Routes/             # Module routes (web.php, api.php)
├── Config/             # Module configuration
└── Assets/             # Frontend assets
    ├── js/
    └── css/
```

### Design Patterns to Use

- **Repository Pattern**: For data access abstraction
- **Service Layer Pattern**: For business logic encapsulation
- **Event-Driven Architecture**: For module decoupling
- **Strategy Pattern**: For pluggable algorithms (tax, pricing, shipping)
- **Factory Pattern**: For complex object creation
- **Observer Pattern**: For event handling and notifications
- **CQRS**: Separate read and write operations where beneficial

## Testing Guidelines

### Running Tests
```bash
# Run all tests
php artisan test

# Run specific suite
php artisan test --testsuite=Unit
php artisan test --testsuite=Feature

# Run with coverage
php artisan test --coverage --min=80
```

### Writing Tests

- **Always** write tests for new features
- Aim for **80%+ code coverage**
- Use descriptive test method names: `test_can_create_sales_order()`
- Use database transactions in feature tests
- Mock external dependencies in unit tests
- Use factories for test data generation

**Unit Test Example:**
```php
public function test_can_calculate_order_total(): void
{
    $calculator = new OrderTotalCalculator();
    
    $total = $calculator->calculate([
        'subtotal' => 100.00,
        'tax_rate' => 0.10,
        'discount' => 5.00,
    ]);
    
    $this->assertEquals(105.00, $total);
}
```

**Feature Test Example:**
```php
public function test_can_create_sales_order(): void
{
    $user = User::factory()->create();
    
    $response = $this->actingAs($user)
        ->postJson('/api/v1/sales/orders', [
            'customer_id' => 1,
            'order_date' => now(),
            'lines' => [
                ['product_id' => 1, 'quantity' => 10, 'price' => 100],
            ],
        ]);
    
    $response->assertStatus(201)
        ->assertJsonStructure(['data' => ['id', 'order_number']]);
        
    $this->assertDatabaseHas('sales_orders', [
        'customer_id' => 1,
    ]);
}
```

## Security Best Practices

### Input Validation
- **Always** validate user input using Form Requests
- Use Laravel's validation rules
- Sanitize data before database operations
- Never trust user input

### Authentication & Authorization
- Use Laravel Sanctum for API authentication
- Implement policies for authorization
- Use middleware for route protection
- Implement role-based access control (RBAC)
- Consider attribute-based access control (ABAC) for complex permissions

### Data Protection
- **Never** commit secrets, API keys, or passwords to version control
- Use `.env` file for environment-specific configuration
- Encrypt sensitive data at rest using Laravel's encryption
- Use HTTPS/TLS in production
- Implement rate limiting and throttling
- Use prepared statements (Eloquent ORM provides this)

### SQL Injection Prevention
- **Always** use Eloquent ORM or Query Builder
- **Never** concatenate user input into raw SQL queries
- Use parameter binding for raw queries

### XSS Prevention
- Blade templates automatically escape output
- Use `{!! !!}` only when absolutely necessary and data is sanitized
- Validate and sanitize HTML input

### CSRF Protection
- Laravel provides CSRF protection by default
- Include CSRF token in forms
- Use `@csrf` directive in Blade templates

## API Development

### RESTful API Guidelines

- Use resource-based URLs: `/api/v1/sales/orders`
- Use proper HTTP methods: GET (read), POST (create), PUT/PATCH (update), DELETE (delete)
- Return appropriate HTTP status codes
- Use API versioning: `/api/v1/`, `/api/v2/`
- Implement pagination for list endpoints
- Use Laravel API Resources for data transformation
- Document APIs using OpenAPI 3.0 annotations

### API Response Format

**Success Response:**
```json
{
    "data": {
        "id": 1,
        "order_number": "SO-2024-001",
        "customer": {...},
        "lines": [...]
    },
    "meta": {
        "total": 100,
        "per_page": 15,
        "current_page": 1
    }
}
```

**Error Response:**
```json
{
    "message": "Validation failed",
    "errors": {
        "customer_id": ["The customer field is required."]
    }
}
```

## Database Conventions

### Naming Conventions
- Use snake_case for table and column names
- Use plural names for tables: `sales_orders`, `customers`, `products`
- Use singular names for pivot tables with alphabetical order: `customer_product`
- Foreign keys: `{table_singular}_id` (e.g., `customer_id`, `product_id`)
- Boolean columns: prefix with `is_`, `has_`, `can_` (e.g., `is_active`, `has_discount`)

### Migration Best Practices
- Create reversible migrations (implement `down()` method)
- Use descriptive migration names
- Add indexes for foreign keys and frequently queried columns
- Use soft deletes where appropriate: `$table->softDeletes()`
- Add timestamps: `$table->timestamps()`
- Define foreign key constraints with proper cascade actions

### Model Conventions
- Define fillable or guarded properties
- Cast attributes to appropriate types using `$casts`
- Define relationships clearly
- Use query scopes for common queries
- Implement model events when needed

## Code Quality Tools

### PHP
```bash
# Fix code style (PSR-12)
./vendor/bin/php-cs-fixer fix

# Run static analysis
./vendor/bin/phpstan analyse

# Check code style without fixing
./vendor/bin/php-cs-fixer fix --dry-run --diff
```

### JavaScript
```bash
# Run ESLint
npm run lint

# Fix linting issues
npm run lint:fix
```

## Documentation Requirements

### Code Documentation
- Use PHPDoc blocks for all classes, methods, and complex logic
- Include `@param`, `@return`, `@throws` tags
- Add description for non-obvious code
- Document complex algorithms and business rules

**Example:**
```php
/**
 * Calculate the total amount for a sales order.
 *
 * This method calculates the total by adding subtotal, taxes,
 * shipping, and subtracting any discounts applied.
 *
 * @param SalesOrder $order The sales order to calculate
 * @return float The calculated total amount
 * @throws InvalidOrderException If order data is invalid or incomplete
 */
public function calculateTotal(SalesOrder $order): float
{
    // Implementation
}
```

### API Documentation
- Use OpenAPI 3.0 annotations for all API endpoints
- Document request parameters, request body, and responses
- Include examples in API documentation

## Git Workflow

### Commit Message Format
Follow [Conventional Commits](https://www.conventionalcommits.org/):

```
<type>(<scope>): <subject>

<body>

<footer>
```

**Types:**
- `feat`: New feature
- `fix`: Bug fix
- `docs`: Documentation changes
- `style`: Code style changes (formatting)
- `refactor`: Code refactoring
- `test`: Adding or updating tests
- `chore`: Build process or auxiliary tool changes
- `perf`: Performance improvements

**Examples:**
```
feat(sales): add sales order validation
fix(inventory): correct stock calculation for multiple warehouses
docs(api): update authentication documentation
refactor(crm): extract lead scoring logic to service
```

### Branch Naming
- `feature/feature-name` - New features
- `bugfix/bug-description` - Bug fixes
- `hotfix/critical-fix` - Critical production fixes
- `refactor/refactor-description` - Code refactoring
- `docs/documentation-update` - Documentation changes

## Multi-Tenancy Guidelines

- **Always** be aware of tenant context in multi-tenant operations
- Use tenant-scoped queries to prevent data leakage
- Each tenant has a dedicated database (database-per-tenant strategy)
- Tenant identification via subdomain or domain
- Use middleware for tenant resolution
- Never hard-code tenant IDs
- Test multi-tenant features thoroughly

## Performance Optimization

### Caching
- Cache configuration: `php artisan config:cache`
- Cache routes: `php artisan route:cache`
- Cache views: `php artisan view:cache`
- Use Redis for application caching
- Implement query result caching for expensive operations
- Use cache tags for organized cache management

### Database Optimization
- Use eager loading to prevent N+1 queries: `$orders->with('customer', 'lines')`
- Add indexes for frequently queried columns
- Use database query optimization techniques
- Monitor slow queries and optimize them
- Use chunking for large datasets: `Model::chunk(1000, function($items) {})`

### Queue System
- Move time-consuming operations to queues
- Use queue priorities for critical jobs
- Implement job retry logic
- Monitor queue workers
- Use supervisor for queue worker management

## Error Handling

- Use try-catch blocks for expected exceptions
- Log errors appropriately using Laravel's logging
- Return meaningful error messages to users
- Don't expose sensitive information in error messages
- Use custom exception classes for domain-specific errors
- Implement global exception handling in `app/Exceptions/Handler.php`

## Common Pitfalls to Avoid

- ❌ Don't use mass assignment without defining `$fillable` or `$guarded`
- ❌ Don't put business logic in controllers
- ❌ Don't use raw SQL queries without parameter binding
- ❌ Don't commit `.env` file or sensitive data
- ❌ Don't ignore N+1 query problems
- ❌ Don't use `select *` when you only need specific columns
- ❌ Don't forget to validate user input
- ❌ Don't use global variables or singletons (except Laravel facades)
- ❌ Don't ignore error cases in code
- ❌ Don't write tests after the code (TDD preferred)

## Helpful Commands

```bash
# Development
php artisan serve                    # Start development server
php artisan tinker                   # Laravel REPL
npm run dev                          # Start Vite dev server

# Database
php artisan migrate                  # Run migrations
php artisan migrate:rollback         # Rollback last migration
php artisan db:seed                  # Run seeders
php artisan migrate:fresh --seed     # Fresh migration with seeding

# Cache
php artisan cache:clear              # Clear application cache
php artisan config:clear             # Clear config cache
php artisan route:clear              # Clear route cache
php artisan view:clear               # Clear compiled views

# Testing
php artisan test                     # Run tests
php artisan test --coverage          # Run tests with coverage

# Code Quality
./vendor/bin/php-cs-fixer fix        # Fix code style
./vendor/bin/phpstan analyse         # Static analysis
npm run lint                         # Lint JavaScript

# Queue
php artisan queue:work               # Start queue worker
php artisan queue:restart            # Restart queue workers

# Module Generation
php artisan module:make ModuleName   # Create new module
```

## Additional Resources

- [ARCHITECTURE.md](../ARCHITECTURE.md) - Detailed architecture documentation
- [MODULE_STRUCTURE.md](../MODULE_STRUCTURE.md) - Module development guide
- [TECHNOLOGY_STACK.md](../TECHNOLOGY_STACK.md) - Complete technology stack details
- [CONTRIBUTING.md](../CONTRIBUTING.md) - Contribution guidelines
- [DEPLOYMENT.md](../DEPLOYMENT.md) - Deployment instructions
- [ENTITY_RELATIONSHIPS.md](../ENTITY_RELATIONSHIPS.md) - Data model documentation

## Final Notes

- **Code Quality**: Write clean, maintainable, and well-documented code
- **Testing**: Always write tests for new features and bug fixes
- **Security**: Security is paramount - never compromise on security practices
- **Performance**: Consider performance implications of your code
- **Documentation**: Keep documentation up to date with code changes
- **Collaboration**: Follow team conventions and communicate effectively
- **Learning**: Stay updated with Laravel and Vue.js best practices

When generating code, prioritize:
1. **Security** - Protect against common vulnerabilities
2. **Maintainability** - Write code that's easy to understand and modify
3. **Performance** - Consider scalability and efficiency
4. **Testing** - Include comprehensive tests
5. **Documentation** - Document complex logic and APIs
