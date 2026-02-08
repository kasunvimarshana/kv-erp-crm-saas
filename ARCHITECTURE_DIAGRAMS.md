# System Architecture Diagram

## Multi-Tenancy Flow

```
┌─────────────────────────────────────────────────────────────────┐
│                        HTTP Request                              │
│                  (tenant1.example.com)                           │
└───────────────────────────┬─────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────────┐
│                  Laravel HTTP Kernel                             │
│                  (app/Http/Kernel.php)                           │
└───────────────────────────┬─────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────────┐
│            TenantIdentification Middleware                       │
│    (modules/Core/Middleware/TenantIdentification.php)           │
│                                                                  │
│  1. Extract domain from request                                 │
│  2. Resolve tenant from cache/database                          │
│  3. Validate tenant status                                      │
│  4. Switch database connection                                  │
│  5. Set tenant context                                          │
└───────────────────────────┬─────────────────────────────────────┘
                            │
                   ┌────────┴────────┐
                   │                 │
         ┌─────────▼──────┐  ┌──────▼─────────┐
         │  Central DB     │  │  Tenant DB     │
         │  (tenants,      │  │  (organizations│
         │   metadata)     │  │   users, data) │
         └─────────────────┘  └────────────────┘
                   │                 │
                   └────────┬────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────────┐
│                     Application                                  │
│                  (Controllers, Services)                         │
└─────────────────────────────────────────────────────────────────┘
```

## Database Structure

### Central Database (kv_erp_central)

```
┌─────────────────────────────────────────────────────────┐
│ tenants                                                  │
├─────────────────────────────────────────────────────────┤
│ id (UUID)                                                │
│ domain (unique)                                          │
│ subdomain (unique, nullable)                             │
│ company_name                                             │
│ database_name (unique) ─────────────┐                   │
│ database_host                       │                   │
│ database_port                       │                   │
│ status (enum)                       │                   │
│ plan (enum)                         │                   │
│ max_users                           │                   │
│ max_organizations                   │                   │
│ subscription_start                  │                   │
│ subscription_end                    │                   │
│ billing_email                       │                   │
│ custom_settings (json)              │                   │
│ created_at, updated_at, deleted_at  │                   │
└─────────────────────────────────────┘                   │
                                                           │
                                                           │
### Tenant Database (tenant_xxx)                          │
                                                           │
┌────────────────────────────────────────┐                │
│ organizations                          │◄───────────────┘
├────────────────────────────────────────┤   Points to this DB
│ id (UUID)                              │
│ tenant_id (FK) ──┐                     │
│ parent_id (FK)   │                     │
│ name             │                     │
│ code (unique)    │                     │
│ type (enum)      │                     │
│ status (enum)    │                     │
│ email, phone     │                     │
│ address, city... │                     │
│ currency (ISO)   │                     │
│ timezone (IANA)  │                     │
│ locale (ISO)     │                     │
│ settings (json)  │                     │
│ timestamps       │                     │
└──────────────────┘                     │
           │                             │
           │ Self-referencing            │
           └─────────────────────────────┘
              (Parent-Child Hierarchy)


┌────────────────────────────────────────┐
│ users (to be implemented)              │
├────────────────────────────────────────┤
│ id (UUID)                              │
│ tenant_id (FK)                         │
│ organization_id (FK)                   │
│ name, email, password                  │
│ ...                                    │
└────────────────────────────────────────┘
```

## Module Architecture

```
modules/Core/
│
├── __module__.php          ← Module manifest with metadata
│
├── Models/                 ← Domain entities
│   ├── Tenant.php          ← Main tenant model (273 lines)
│   ├── Organization.php    ← Organization model (209 lines)
│   └── User.php            ← To be implemented
│
├── Services/               ← Business logic layer
│   ├── Contracts/          ← Service interfaces
│   │   ├── TenantServiceInterface.php (to do)
│   │   └── OrganizationServiceInterface.php (to do)
│   ├── TenantService.php   ← To be implemented
│   └── OrganizationService.php ← To be implemented
│
├── Repositories/           ← Data access layer
│   ├── Contracts/          ← Repository interfaces
│   │   ├── TenantRepositoryInterface.php (to do)
│   │   └── OrganizationRepositoryInterface.php (to do)
│   └── Eloquent/           ← Eloquent implementations
│       ├── TenantRepository.php (to do)
│       └── OrganizationRepository.php (to do)
│
├── Controllers/            ← HTTP request handlers
│   ├── Api/                ← API controllers (to do)
│   │   ├── TenantApiController.php
│   │   └── OrganizationApiController.php
│   └── Web/                ← Web controllers (to do)
│
├── Middleware/             ← Request processing
│   ├── TenantIdentification.php ✓ (171 lines)
│   └── CheckPermission.php (to do)
│
├── Migrations/             ← Database schema (to do)
│   ├── create_users_table.php
│   ├── create_roles_table.php
│   └── create_permissions_table.php
│
├── Routes/                 ← API and web routes (to do)
│   ├── api.php
│   └── web.php
│
└── Tests/                  ← Automated tests (to do)
    ├── Unit/
    └── Feature/
```

## Technology Stack Layers

```
┌─────────────────────────────────────────────────────────┐
│                    Frontend Layer                        │
│  Vue 3 + Vite + Pinia + Tailwind CSS + Vue Router      │
│  (Modern, reactive, type-safe)                          │
└────────────────────┬────────────────────────────────────┘
                     │ HTTP/HTTPS, JSON API
                     ▼
┌─────────────────────────────────────────────────────────┐
│                   API Layer (REST)                       │
│  Laravel Sanctum + Spatie Permission + Rate Limiting    │
│  OpenAPI 3.0 Documentation                              │
└────────────────────┬────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────┐
│                 Application Layer                        │
│  Services (Business Logic) + Events + Jobs              │
│  Clean Architecture + DDD + SOLID                       │
└────────────────────┬────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────┐
│                  Domain Layer                            │
│  Entities (Models) + Value Objects + Aggregates        │
│  Repository Interfaces + Service Interfaces             │
└────────────────────┬────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────┐
│              Infrastructure Layer                        │
│  Eloquent ORM + PostgreSQL + Redis + Queue              │
│  File Storage + External APIs                           │
└─────────────────────────────────────────────────────────┘
```

## Request Flow Example

### Creating a Tenant

```
User Request
    │
    ├─→ POST /api/v1/central/tenants
    │
    └─→ TenantApiController@store
            │
            ├─→ StoreTenantRequest (validation)
            │
            └─→ TenantService@create
                    │
                    ├─→ TenantRepository@create
                    │       │
                    │       └─→ Tenant::create() [Eloquent]
                    │               │
                    │               └─→ Central DB (INSERT)
                    │
                    ├─→ createTenantDatabase()
                    │       │
                    │       ├─→ CREATE DATABASE tenant_xxx
                    │       └─→ Run migrations on tenant DB
                    │
                    └─→ event(new TenantCreated($tenant))
                            │
                            └─→ TenantCreatedListener
                                    └─→ Send welcome email, etc.
```

### Accessing Tenant Data

```
User Request (tenant1.example.com)
    │
    ├─→ GET /api/v1/organizations
    │
    └─→ TenantIdentification Middleware
            │
            ├─→ Resolve tenant from domain
            │       └─→ Cache::remember('tenant:tenant1.example.com')
            │               └─→ Tenant::where('domain', 'tenant1.example.com')
            │
            ├─→ Validate tenant status
            │
            ├─→ Switch to tenant database
            │       └─→ Config::set('database.default', 'tenant')
            │
            └─→ OrganizationApiController@index
                    │
                    └─→ OrganizationService@getAll
                            │
                            └─→ OrganizationRepository@getAll
                                    │
                                    └─→ Organization::all()
                                            │
                                            └─→ Tenant DB (SELECT)
```

## Security Layers

```
┌─────────────────────────────────────────────────────────┐
│ 1. Transport Security                                    │
│    - HTTPS/TLS encryption                               │
│    - Certificate validation                             │
└────────────────────┬────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────┐
│ 2. Authentication                                        │
│    - Laravel Sanctum (API tokens)                       │
│    - Multi-Factor Authentication (MFA)                  │
│    - Password policies                                  │
└────────────────────┬────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────┐
│ 3. Tenant Isolation                                      │
│    - Database-per-tenant                                │
│    - No cross-tenant data access                        │
│    - Tenant identification middleware                   │
└────────────────────┬────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────┐
│ 4. Authorization                                         │
│    - RBAC (Role-Based Access Control)                   │
│    - ABAC (Attribute-Based Access Control)              │
│    - Spatie Permission package                          │
└────────────────────┬────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────┐
│ 5. Input Validation                                      │
│    - Form Request validation                            │
│    - SQL injection prevention (Eloquent)                │
│    - XSS prevention (Blade escaping)                    │
│    - CSRF protection                                    │
└────────────────────┬────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────┐
│ 6. Rate Limiting                                         │
│    - API throttling per tenant                          │
│    - DDoS protection                                    │
└─────────────────────────────────────────────────────────┘
```

## Performance Optimization Layers

```
┌─────────────────────────────────────────────────────────┐
│ 1. CDN (Content Delivery Network)                       │
│    - Static asset distribution                          │
│    - Global edge caching                                │
└────────────────────┬────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────┐
│ 2. HTTP Cache                                            │
│    - ETag validation                                    │
│    - Cache-Control headers                              │
└────────────────────┬────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────┐
│ 3. Application Cache (Redis)                            │
│    - Tenant data caching (1 hour TTL)                   │
│    - Query result caching                               │
│    - Session storage                                    │
└────────────────────┬────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────┐
│ 4. Database Optimization                                 │
│    - Indexes on foreign keys                            │
│    - Indexes on frequently queried columns              │
│    - Query optimization                                 │
│    - Eager loading (N+1 prevention)                     │
└────────────────────┬────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────┐
│ 5. Async Processing (Queues)                            │
│    - Background jobs (Redis queue)                      │
│    - Email sending                                      │
│    - Report generation                                  │
│    - Data import/export                                 │
└─────────────────────────────────────────────────────────┘
```

## Summary

This architecture provides:
- ✅ **Complete tenant isolation** - Database-per-tenant
- ✅ **Scalability** - Horizontal scaling ready
- ✅ **Security** - Multiple security layers
- ✅ **Performance** - Multi-level caching
- ✅ **Maintainability** - Clean Architecture + DDD
- ✅ **Extensibility** - Modular structure
- ✅ **Flexibility** - Multi-org, multi-currency, multi-language

**Status**: Foundation implemented, ready for service layer and API development.
