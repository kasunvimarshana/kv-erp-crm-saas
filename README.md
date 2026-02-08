# KV-ERP-CRM-SaaS

[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)
[![PHP Version](https://img.shields.io/badge/php-%3E%3D8.2-blue.svg)](https://www.php.net/)
[![Laravel](https://img.shields.io/badge/laravel-10.x-red.svg)](https://laravel.com)
[![PostgreSQL](https://img.shields.io/badge/postgresql-15%2B-blue.svg)](https://www.postgresql.org/)
[![Vue.js](https://img.shields.io/badge/vue.js-3.x-green.svg)](https://vuejs.org)

**Dynamic, enterprise-grade SaaS ERP-CRM system** with a modular, maintainable architecture. Fully supports multi-tenant, multi-organization, multi-vendor, multi-branch, multi-location, multi-currency, multi-language, multi-time-zone, and multi-unit operations with nested structures. Designed for global scalability, complex workflows, and long-term maintainability.

## 🌟 Overview

KV-ERP-CRM-SaaS is a comprehensive, modern ERP-CRM system built on proven architectural patterns and technologies. It draws inspiration from leading systems:

- **[Laravel](https://github.com/laravel/laravel)** - Modern PHP framework with MVC architecture and Eloquent ORM
- **[Odoo](https://github.com/odoo/odoo)** - Modular ERP architecture and multi-tier design
- **[kv-erp](https://github.com/kasunvimarshana/kv-erp)** - Clean architecture and domain-driven design principles
- **[PHP_POS](https://github.com/kasunvimarshana/PHP_POS)** - Point of Sale module structure and functionality

### Key Differentiators

✅ **Clean Architecture** - Clear separation of concerns with DDD principles  
✅ **Modular Design** - Independent, cohesive modules with well-defined boundaries  
✅ **Multi-Tenancy** - Database-per-tenant isolation for maximum security  
✅ **Event-Driven** - Asynchronous processing, loose coupling between modules  
✅ **API-First** - RESTful APIs with comprehensive OpenAPI 3.0 documentation  
✅ **Production-Ready** - Monitoring, logging, error tracking, and security built-in

## ✨ Core Features

### Enterprise Capabilities

- 🏢 **Multi-Tenant Architecture**: Database-per-tenant isolation for security and customization
- 🌍 **Global Operations**: Multi-currency, multi-language, multi-timezone support
- 🔐 **Advanced Security**: RBAC/ABAC, MFA, SSO, audit logging, data encryption
- 📊 **Comprehensive Modules**: CRM, Sales, Inventory, Purchasing, Accounting, HR, Manufacturing, POS, Projects
- 🔄 **Event-Driven**: Asynchronous processing for scalability and loose coupling
- 🚀 **High Performance**: Redis caching, queue processing, optimized database queries
- 📱 **Modern UI**: Vue.js 3 with Tailwind CSS, responsive design
- 🔌 **RESTful APIs**: Comprehensive API with OpenAPI 3.0 documentation
- 📈 **Scalable**: Horizontal and vertical scaling support with Kubernetes

### Technical Excellence

- **Controller → Service → Repository**: Proven layered architecture pattern
- **Clean Code**: SOLID principles, PSR standards, comprehensive testing
- **DevOps Ready**: Docker, Kubernetes, CI/CD pipelines
- **Observable**: Prometheus metrics, ELK logging, Sentry error tracking
- **Maintainable**: Extensive documentation, clear module boundaries

## 📋 Table of Contents

- [Architecture](#-architecture)
- [Technology Stack](#-technology-stack)
- [Quick Start](#-quick-start)
- [Documentation](#-documentation)
- [Modules](#-modules)
- [Development](#-development)
- [Testing](#-testing)
- [Deployment](#-deployment)
- [Contributing](#-contributing)
- [License](#-license)

## 🏗️ Architecture

KV-ERP-CRM-SaaS follows **Clean Architecture** and **Domain-Driven Design** principles:

```
┌─────────────────────────────────────────┐
│         Presentation Layer              │
│  (Controllers, Views, API Endpoints)    │
├─────────────────────────────────────────┤
│         Application Layer               │
│    (Services, Use Cases, DTOs)          │
├─────────────────────────────────────────┤
│           Domain Layer                  │
│  (Entities, Value Objects, Events)      │
├─────────────────────────────────────────┤
│        Infrastructure Layer             │
│   (Repositories, Database, External)    │
└─────────────────────────────────────────┘
```

### Key Design Patterns

- **Repository Pattern**: Data access abstraction
- **Service Layer**: Business logic encapsulation
- **Event-Driven**: Domain events for module communication
- **CQRS**: Command Query Responsibility Segregation
- **Strategy Pattern**: Pluggable algorithms (tax, pricing)
- **Factory Pattern**: Object creation abstraction
- **Observer Pattern**: Event handling and notifications

📖 For detailed architecture, see [ARCHITECTURE.md](ARCHITECTURE.md)

## 🛠️ Technology Stack

### Backend
- **Framework**: Laravel 10+ (PHP 8.2+)
- **Database**: PostgreSQL 15+ / MySQL 8+
- **Cache**: Redis 7+
- **Queue**: Redis Queue / Amazon SQS
- **Search**: Meilisearch / Elasticsearch

### Frontend
- **Framework**: Vue.js 3 (Composition API)
- **Build Tool**: Vite 4+
- **State Management**: Pinia
- **UI Framework**: Tailwind CSS
- **HTTP Client**: Axios

### DevOps
- **Containerization**: Docker
- **Orchestration**: Kubernetes
- **CI/CD**: GitHub Actions
- **Monitoring**: Prometheus + Grafana
- **Logging**: ELK Stack
- **Error Tracking**: Sentry

📖 For complete stack details, see [TECHNOLOGY_STACK.md](TECHNOLOGY_STACK.md)

## 🚀 Quick Start

### Prerequisites

- PHP 8.2+
- Composer 2.x
- Node.js 18 LTS+
- PostgreSQL 15+ / MySQL 8+
- Redis 7+

### Installation

```bash
# 1. Clone the repository
git clone https://github.com/kasunvimarshana/kv-erp-crm-saas.git
cd kv-erp-crm-saas

# 2. Install PHP dependencies
composer install

# 3. Install Node dependencies
npm install

# 4. Copy environment file
cp .env.example .env

# 5. Generate application key
php artisan key:generate

# 6. Configure database in .env
# DB_CONNECTION=pgsql
# DB_HOST=127.0.0.1
# DB_PORT=5432
# DB_DATABASE=kv_erp_crm_saas
# DB_USERNAME=your_username
# DB_PASSWORD=your_password

# 7. Run migrations
php artisan migrate

# 8. Seed database
php artisan db:seed

# 9. Build frontend assets
npm run dev

# 10. Start development server
php artisan serve
```

Access the application at: `http://localhost:8000`

### Docker Quick Start

```bash
# Start all services
docker-compose up -d

# Run migrations
docker-compose exec app php artisan migrate

# Seed database
docker-compose exec app php artisan db:seed

# Access application
open http://localhost
```

## 📚 Documentation

### Core Documentation

- [**ARCHITECTURE.md**](ARCHITECTURE.md) - System architecture and design principles
- [**MODULE_STRUCTURE.md**](MODULE_STRUCTURE.md) - Module development guide
- [**ENTITY_RELATIONSHIPS.md**](ENTITY_RELATIONSHIPS.md) - Data model and relationships
- [**PROJECT_STRUCTURE.md**](PROJECT_STRUCTURE.md) - Directory structure and organization
- [**TECHNOLOGY_STACK.md**](TECHNOLOGY_STACK.md) - Technologies and tools
- [**DEPLOYMENT.md**](DEPLOYMENT.md) - Deployment instructions for various environments

### API Documentation

- **OpenAPI 3.0 Specification**: Complete API documentation
- **Postman Collection**: Ready-to-use API testing collection
- **Authentication Guide**: API authentication and authorization

### Module Documentation

Each module contains its own documentation in `/modules/{ModuleName}/Docs/`:
- `README.md` - Module overview and features
- `API.md` - Module API endpoints
- `CHANGELOG.md` - Version history

## 📦 Modules

### Core Modules

1. **Core/Foundation** - Multi-tenancy, authentication, authorization, audit logging
2. **CRM** - Lead management, opportunities, contacts, pipeline
3. **Sales** - Quotations, sales orders, invoicing, payments
4. **Customer** - Customer management, contacts, addresses
5. **Inventory** - Products, stock management, warehouses, movements
6. **Purchasing** - Vendors, purchase orders, goods receipt
7. **Accounting** - Chart of accounts, journal entries, financial reports
8. **HR** - Employees, attendance, leave, payroll
9. **Manufacturing** - BOM, work orders, production planning
10. **POS** - Point of Sale, cash register, receipts
11. **Project** - Project management, tasks, time tracking
12. **Reporting** - Dashboards, custom reports, analytics
13. **Integration** - API gateway, webhooks, third-party integrations

📖 For detailed module information, see [MODULE_STRUCTURE.md](MODULE_STRUCTURE.md)

## 💻 Development

### Setting Up Development Environment

```bash
# Using Laravel Sail (recommended)
./vendor/bin/sail up

# Install development dependencies
composer install --dev
npm install

# Enable debug mode
# Set APP_DEBUG=true in .env

# Install Laravel Telescope (optional)
composer require laravel/telescope --dev
php artisan telescope:install
php artisan migrate
```

### Code Style

```bash
# Fix code style
./vendor/bin/php-cs-fixer fix

# Run static analysis
./vendor/bin/phpstan analyse

# Check code quality
composer check-style
composer analyse
```

### Creating a New Module

```bash
# Use the module generator
php artisan module:make ModuleName

# This creates the complete module structure
# See MODULE_STRUCTURE.md for details
```

## 🧪 Testing

### Running Tests

```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Unit
php artisan test --testsuite=Feature

# Run tests with coverage
php artisan test --coverage

# Run specific test
php artisan test --filter=SalesOrderTest
```

### Test Structure

```
tests/
├── Unit/              # Component-level tests
├── Feature/           # Endpoint/integration tests
├── Integration/       # Cross-module tests
└── Browser/           # E2E tests (Dusk)
```

### Writing Tests

```php
// Feature test example
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
```

## 🚀 Deployment

### Production Deployment

```bash
# 1. Set production environment
APP_ENV=production
APP_DEBUG=false

# 2. Optimize application
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# 3. Run migrations
php artisan migrate --force

# 4. Build frontend
npm run build

# 5. Set proper permissions
chmod -R 755 storage bootstrap/cache
```

### Docker Deployment

```bash
# Build production image
docker build -t kv-erp-crm-saas:latest .

# Deploy with Docker Compose
docker-compose -f docker-compose.prod.yml up -d
```

### Kubernetes Deployment

```bash
# Apply configurations
kubectl apply -f k8s/

# Check deployment status
kubectl get pods
kubectl get services
```

📖 For detailed deployment guide, see [DEPLOYMENT.md](DEPLOYMENT.md)

## 🤝 Contributing

We welcome contributions! Please see our contributing guidelines:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

### Development Guidelines

- Follow PSR-12 coding standards
- Write tests for new features
- Update documentation
- Follow commit message conventions
- Ensure CI/CD pipeline passes

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🙏 Acknowledgments

This project draws inspiration from:

- [Laravel Framework](https://github.com/laravel/laravel) - PHP framework
- [Odoo](https://github.com/odoo/odoo) - Modular ERP architecture
- [kv-erp](https://github.com/kasunvimarshana/kv-erp) - Clean architecture implementation
- [PHP_POS](https://github.com/kasunvimarshana/PHP_POS) - POS module structure

## 📞 Support

- **Documentation**: [docs/](docs/)
- **Issues**: [GitHub Issues](https://github.com/kasunvimarshana/kv-erp-crm-saas/issues)
- **Discussions**: [GitHub Discussions](https://github.com/kasunvimarshana/kv-erp-crm-saas/discussions)

## 🗺️ Roadmap

- [x] Architecture documentation
- [x] Module structure definition
- [x] Entity relationships design
- [ ] Core module implementation
- [ ] CRM module implementation
- [ ] Sales module implementation
- [ ] Inventory module implementation
- [ ] API documentation with Swagger
- [ ] Frontend development
- [ ] Testing suite
- [ ] Production deployment guide
- [ ] Performance optimization
- [ ] Security hardening

---

**Built with ❤️ using Laravel, Vue.js, and modern web technologies**
