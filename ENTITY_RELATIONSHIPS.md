# Entity Relationships and Core Entities

## Overview

This document defines the core entities, their attributes, and relationships in the KV-ERP-CRM-SaaS system. The entity model is designed to support complex, multi-tenant, multi-organizational business operations.

## Core Entity Hierarchy

```
┌─────────────────────────────────────────────────────────┐
│                        TENANT                           │
│  (Database-per-tenant isolation)                        │
├─────────────────────────────────────────────────────────┤
│  • tenant_id (PK)                                       │
│  • domain                                               │
│  • subdomain                                            │
│  • database_name                                        │
│  • status                                               │
│  • plan                                                 │
│  • subscription_start                                   │
│  • subscription_end                                     │
└───────────────────┬─────────────────────────────────────┘
                    │
                    │ 1:N
                    │
┌───────────────────▼─────────────────────────────────────┐
│                   ORGANIZATION                          │
│  (Tenant can have multiple organizations)              │
├─────────────────────────────────────────────────────────┤
│  • organization_id (PK)                                 │
│  • tenant_id (FK)                                       │
│  • parent_organization_id (FK, nullable)                │
│  • name                                                 │
│  • legal_name                                           │
│  • tax_id                                               │
│  • currency_code                                        │
│  • timezone                                             │
│  • language                                             │
└───────────────────┬─────────────────────────────────────┘
                    │
                    │ 1:N
                    │
        ┌───────────┼───────────┬───────────┬──────────┐
        │           │           │           │          │
        ▼           ▼           ▼           ▼          ▼
    Locations   Departments  Users    Customers   Vendors
```

## 1. Foundation Entities

### 1.1 Tenant

**Purpose**: Top-level entity for multi-tenancy, represents a separate customer/company.

```
Tenant
├── tenant_id (UUID, PK)
├── domain (String, Unique)
├── subdomain (String, Unique)
├── company_name (String)
├── database_name (String, Unique)
├── database_host (String)
├── database_port (Integer)
├── status (Enum: active, suspended, trial, expired)
├── plan (Enum: basic, professional, enterprise)
├── max_users (Integer)
├── max_organizations (Integer)
├── subscription_start (DateTime)
├── subscription_end (DateTime)
├── billing_email (String)
├── technical_contact (String)
├── custom_settings (JSON)
├── created_at (DateTime)
├── updated_at (DateTime)
└── deleted_at (DateTime, nullable)
```

**Relationships**:
- Has Many: Organizations
- Has Many: Users (global tenant users)
- Has One: Subscription

### 1.2 Organization

**Purpose**: Represents a company/branch within a tenant, supports hierarchical structures.

```
Organization
├── organization_id (UUID, PK)
├── tenant_id (UUID, FK → Tenant)
├── parent_organization_id (UUID, FK → Organization, nullable)
├── code (String, Unique within tenant)
├── name (String)
├── legal_name (String)
├── tax_id (String)
├── registration_number (String)
├── email (String)
├── phone (String)
├── website (String)
├── logo_url (String)
├── currency_code (String, ISO 4217)
├── timezone (String, IANA)
├── language_code (String, ISO 639-1)
├── fiscal_year_start (Date)
├── address (JSON: street, city, state, country, postal_code)
├── is_active (Boolean)
├── settings (JSON)
├── created_at (DateTime)
├── updated_at (DateTime)
└── deleted_at (DateTime, nullable)
```

**Relationships**:
- Belongs To: Tenant
- Belongs To: Parent Organization (self-referential)
- Has Many: Child Organizations (self-referential)
- Has Many: Locations
- Has Many: Departments
- Has Many: Users (through assignments)
- Has Many: Customers
- Has Many: Vendors
- Has Many: Products
- Has Many: Warehouses

### 1.3 User

**Purpose**: System users with authentication and authorization.

```
User
├── user_id (UUID, PK)
├── tenant_id (UUID, FK → Tenant)
├── employee_id (UUID, FK → Employee, nullable)
├── username (String, Unique within tenant)
├── email (String, Unique within tenant)
├── email_verified_at (DateTime)
├── password (String, hashed)
├── first_name (String)
├── last_name (String)
├── full_name (String, computed)
├── phone (String)
├── avatar_url (String)
├── language_preference (String)
├── timezone_preference (String)
├── is_active (Boolean)
├── is_system_admin (Boolean)
├── last_login_at (DateTime)
├── last_login_ip (String)
├── two_factor_secret (String, encrypted)
├── two_factor_enabled (Boolean)
├── remember_token (String)
├── created_at (DateTime)
├── updated_at (DateTime)
└── deleted_at (DateTime, nullable)
```

**Relationships**:
- Belongs To: Tenant
- Belongs To: Employee (optional)
- Belongs To Many: Roles
- Has Many: Permissions (direct)
- Has Many: Sessions
- Has Many: Audit Logs
- Belongs To Many: Organizations (assignments)

### 1.4 Role

**Purpose**: Define sets of permissions for authorization (RBAC).

```
Role
├── role_id (UUID, PK)
├── tenant_id (UUID, FK → Tenant)
├── name (String)
├── display_name (String)
├── description (Text)
├── is_system_role (Boolean)
├── level (Integer: organization, tenant, system)
├── created_at (DateTime)
├── updated_at (DateTime)
└── deleted_at (DateTime, nullable)
```

**Relationships**:
- Belongs To: Tenant
- Belongs To Many: Users
- Belongs To Many: Permissions

### 1.5 Permission

**Purpose**: Granular access control for resources and actions.

```
Permission
├── permission_id (UUID, PK)
├── name (String, Unique)
├── display_name (String)
├── module (String)
├── resource (String)
├── action (String: view, create, edit, delete, approve)
├── description (Text)
├── created_at (DateTime)
└── updated_at (DateTime)
```

**Relationships**:
- Belongs To Many: Roles
- Belongs To Many: Users (direct permissions)

## 2. Geographic & Organizational Entities

### 2.1 Location/Branch

**Purpose**: Physical locations, branches, stores, or facilities.

```
Location
├── location_id (UUID, PK)
├── organization_id (UUID, FK → Organization)
├── parent_location_id (UUID, FK → Location, nullable)
├── code (String, Unique within organization)
├── name (String)
├── type (Enum: headquarters, branch, warehouse, store, factory)
├── address (JSON)
├── phone (String)
├── email (String)
├── manager_id (UUID, FK → Employee)
├── timezone (String)
├── operating_hours (JSON)
├── is_active (Boolean)
├── created_at (DateTime)
├── updated_at (DateTime)
└── deleted_at (DateTime, nullable)
```

**Relationships**:
- Belongs To: Organization
- Belongs To: Parent Location (self-referential)
- Has Many: Child Locations
- Has Many: Employees
- Has Many: Warehouses
- Belongs To: Manager (Employee)

### 2.2 Department

**Purpose**: Organizational units within a company.

```
Department
├── department_id (UUID, PK)
├── organization_id (UUID, FK → Organization)
├── parent_department_id (UUID, FK → Department, nullable)
├── code (String, Unique within organization)
├── name (String)
├── description (Text)
├── manager_id (UUID, FK → Employee)
├── cost_center (String)
├── is_active (Boolean)
├── created_at (DateTime)
├── updated_at (DateTime)
└── deleted_at (DateTime, nullable)
```

**Relationships**:
- Belongs To: Organization
- Belongs To: Parent Department
- Has Many: Child Departments
- Has Many: Employees
- Belongs To: Manager (Employee)

## 3. Party Entities (People & Organizations)

### 3.1 Customer

**Purpose**: Customers who purchase goods/services.

```
Customer
├── customer_id (UUID, PK)
├── organization_id (UUID, FK → Organization)
├── customer_number (String, Unique)
├── customer_type (Enum: individual, company)
├── name (String)
├── legal_name (String)
├── tax_id (String)
├── email (String)
├── phone (String)
├── website (String)
├── billing_address (JSON)
├── shipping_address (JSON)
├── payment_terms (String: Net 30, Net 60, etc.)
├── credit_limit (Decimal)
├── currency_code (String)
├── price_list_id (UUID, FK → PriceList, nullable)
├── sales_rep_id (UUID, FK → Employee, nullable)
├── customer_since (Date)
├── status (Enum: active, inactive, blocked)
├── notes (Text)
├── custom_fields (JSON)
├── created_at (DateTime)
├── updated_at (DateTime)
└── deleted_at (DateTime, nullable)
```

**Relationships**:
- Belongs To: Organization
- Belongs To: Price List
- Belongs To: Sales Representative (Employee)
- Has Many: Contacts
- Has Many: Sales Orders
- Has Many: Invoices
- Has Many: Payments
- Has Many: Opportunities (CRM)
- Has Many: Shipping Addresses

### 3.2 Vendor/Supplier

**Purpose**: Suppliers from whom goods/services are purchased.

```
Vendor
├── vendor_id (UUID, PK)
├── organization_id (UUID, FK → Organization)
├── vendor_number (String, Unique)
├── vendor_type (Enum: manufacturer, distributor, service_provider)
├── name (String)
├── legal_name (String)
├── tax_id (String)
├── email (String)
├── phone (String)
├── website (String)
├── billing_address (JSON)
├── payment_terms (String)
├── currency_code (String)
├── bank_account (JSON: bank_name, account_number, routing_number)
├── lead_time_days (Integer)
├── minimum_order_value (Decimal)
├── buyer_id (UUID, FK → Employee, nullable)
├── status (Enum: active, inactive, blocked)
├── rating (Integer: 1-5)
├── notes (Text)
├── custom_fields (JSON)
├── created_at (DateTime)
├── updated_at (DateTime)
└── deleted_at (DateTime, nullable)
```

**Relationships**:
- Belongs To: Organization
- Belongs To: Buyer (Employee)
- Has Many: Contacts
- Has Many: Purchase Orders
- Has Many: Vendor Invoices
- Has Many: Payments
- Belongs To Many: Products (vendor products)

### 3.3 Contact

**Purpose**: Individual contacts for customers/vendors.

```
Contact
├── contact_id (UUID, PK)
├── contactable_id (UUID, polymorphic)
├── contactable_type (String: Customer, Vendor)
├── first_name (String)
├── last_name (String)
├── full_name (String, computed)
├── title (String)
├── email (String)
├── phone (String)
├── mobile (String)
├── department (String)
├── is_primary (Boolean)
├── notes (Text)
├── created_at (DateTime)
├── updated_at (DateTime)
└── deleted_at (DateTime, nullable)
```

**Relationships**:
- Morphs To: Customer or Vendor

### 3.4 Employee

**Purpose**: Company employees (HR module).

```
Employee
├── employee_id (UUID, PK)
├── organization_id (UUID, FK → Organization)
├── employee_number (String, Unique)
├── user_id (UUID, FK → User, nullable)
├── first_name (String)
├── last_name (String)
├── full_name (String, computed)
├── email (String)
├── phone (String)
├── date_of_birth (Date)
├── gender (Enum)
├── nationality (String)
├── identification_number (String, encrypted)
├── department_id (UUID, FK → Department)
├── job_title (String)
├── employment_type (Enum: full_time, part_time, contract)
├── employment_status (Enum: active, on_leave, terminated)
├── hire_date (Date)
├── termination_date (Date, nullable)
├── manager_id (UUID, FK → Employee, nullable)
├── location_id (UUID, FK → Location)
├── salary (Decimal, encrypted)
├── salary_currency (String)
├── bank_account (JSON, encrypted)
├── emergency_contact (JSON)
├── address (JSON)
├── photo_url (String)
├── created_at (DateTime)
├── updated_at (DateTime)
└── deleted_at (DateTime, nullable)
```

**Relationships**:
- Belongs To: Organization
- Belongs To: Department
- Belongs To: Location
- Belongs To: User (optional)
- Belongs To: Manager (Employee, self-referential)
- Has Many: Subordinates (Employees)

## 4. Product & Inventory Entities

### 4.1 Product

**Purpose**: Goods or services sold/purchased.

```
Product
├── product_id (UUID, PK)
├── organization_id (UUID, FK → Organization)
├── product_number (String, Unique)
├── name (String)
├── description (Text)
├── product_type (Enum: stockable, consumable, service)
├── category_id (UUID, FK → ProductCategory)
├── uom_id (UUID, FK → UnitOfMeasure)
├── barcode (String)
├── sku (String)
├── default_code (String)
├── weight (Decimal)
├── weight_uom_id (UUID, FK → UnitOfMeasure)
├── volume (Decimal)
├── volume_uom_id (UUID, FK → UnitOfMeasure)
├── cost_price (Decimal)
├── list_price (Decimal)
├── currency_code (String)
├── tax_category_id (UUID, FK → TaxCategory)
├── is_active (Boolean)
├── can_be_sold (Boolean)
├── can_be_purchased (Boolean)
├── track_inventory (Boolean)
├── reorder_level (Decimal)
├── reorder_quantity (Decimal)
├── lead_time_days (Integer)
├── image_url (String)
├── images (JSON: array of URLs)
├── specifications (JSON)
├── custom_fields (JSON)
├── created_at (DateTime)
├── updated_at (DateTime)
└── deleted_at (DateTime, nullable)
```

**Relationships**:
- Belongs To: Organization
- Belongs To: Product Category
- Belongs To: Unit of Measure
- Has Many: Product Variants
- Has Many: Stock Items (inventory)
- Has Many: Prices (price list items)
- Belongs To Many: Vendors
- Has Many: BOM Lines (if manufactured)

### 4.2 ProductCategory

**Purpose**: Hierarchical product classification.

```
ProductCategory
├── category_id (UUID, PK)
├── organization_id (UUID, FK → Organization)
├── parent_category_id (UUID, FK → ProductCategory, nullable)
├── code (String, Unique within organization)
├── name (String)
├── description (Text)
├── image_url (String)
├── is_active (Boolean)
├── created_at (DateTime)
├── updated_at (DateTime)
└── deleted_at (DateTime, nullable)
```

**Relationships**:
- Belongs To: Organization
- Belongs To: Parent Category
- Has Many: Child Categories
- Has Many: Products

### 4.3 UnitOfMeasure (UOM)

**Purpose**: Measurement units for products.

```
UnitOfMeasure
├── uom_id (UUID, PK)
├── organization_id (UUID, FK → Organization, nullable)
├── category (Enum: length, weight, volume, time, unit)
├── name (String)
├── code (String)
├── symbol (String)
├── base_uom_id (UUID, FK → UnitOfMeasure, nullable)
├── conversion_factor (Decimal)
├── is_base_unit (Boolean)
├── is_standard (Boolean: system-defined)
├── created_at (DateTime)
└── updated_at (DateTime)
```

**Relationships**:
- Belongs To: Organization (nullable for system UOMs)
- Belongs To: Base UOM
- Has Many: Products

### 4.4 Warehouse

**Purpose**: Storage facility for inventory.

```
Warehouse
├── warehouse_id (UUID, PK)
├── organization_id (UUID, FK → Organization)
├── location_id (UUID, FK → Location)
├── code (String, Unique)
├── name (String)
├── address (JSON)
├── warehouse_type (Enum: own, third_party, virtual)
├── manager_id (UUID, FK → Employee)
├── is_active (Boolean)
├── created_at (DateTime)
├── updated_at (DateTime)
└── deleted_at (DateTime, nullable)
```

**Relationships**:
- Belongs To: Organization
- Belongs To: Location
- Belongs To: Manager (Employee)
- Has Many: Storage Locations
- Has Many: Stock Items

### 4.5 StockItem

**Purpose**: Inventory records for products in warehouses.

```
StockItem
├── stock_item_id (UUID, PK)
├── product_id (UUID, FK → Product)
├── warehouse_id (UUID, FK → Warehouse)
├── storage_location_id (UUID, FK → StorageLocation, nullable)
├── quantity_on_hand (Decimal)
├── quantity_reserved (Decimal)
├── quantity_available (Decimal, computed)
├── valuation_method (Enum: FIFO, LIFO, Average)
├── unit_cost (Decimal)
├── total_value (Decimal, computed)
├── lot_number (String, nullable)
├── serial_number (String, nullable)
├── expiry_date (Date, nullable)
├── last_counted_at (DateTime)
├── created_at (DateTime)
└── updated_at (DateTime)
```

**Relationships**:
- Belongs To: Product
- Belongs To: Warehouse
- Belongs To: Storage Location
- Has Many: Stock Movements

## 5. Sales Entities

### 5.1 SalesOrder

**Purpose**: Customer orders for products/services.

```
SalesOrder
├── sales_order_id (UUID, PK)
├── organization_id (UUID, FK → Organization)
├── order_number (String, Unique)
├── customer_id (UUID, FK → Customer)
├── contact_id (UUID, FK → Contact, nullable)
├── opportunity_id (UUID, FK → Opportunity, nullable)
├── order_date (Date)
├── delivery_date (Date, nullable)
├── sales_rep_id (UUID, FK → Employee)
├── warehouse_id (UUID, FK → Warehouse)
├── price_list_id (UUID, FK → PriceList, nullable)
├── currency_code (String)
├── exchange_rate (Decimal)
├── status (Enum: draft, confirmed, in_progress, delivered, cancelled)
├── payment_terms (String)
├── subtotal (Decimal)
├── tax_amount (Decimal)
├── discount_amount (Decimal)
├── shipping_amount (Decimal)
├── total_amount (Decimal)
├── billing_address (JSON)
├── shipping_address (JSON)
├── notes (Text)
├── internal_notes (Text)
├── approved_by_id (UUID, FK → User, nullable)
├── approved_at (DateTime, nullable)
├── created_by_id (UUID, FK → User)
├── created_at (DateTime)
├── updated_at (DateTime)
└── deleted_at (DateTime, nullable)
```

**Relationships**:
- Belongs To: Organization
- Belongs To: Customer
- Belongs To: Contact
- Belongs To: Opportunity
- Belongs To: Sales Representative (Employee)
- Belongs To: Warehouse
- Belongs To: Price List
- Has Many: Order Lines
- Has Many: Invoices
- Has Many: Shipments
- Has Many: Payments

### 5.2 SalesOrderLine

**Purpose**: Line items in a sales order.

```
SalesOrderLine
├── order_line_id (UUID, PK)
├── sales_order_id (UUID, FK → SalesOrder)
├── line_number (Integer)
├── product_id (UUID, FK → Product, nullable)
├── description (Text)
├── quantity (Decimal)
├── uom_id (UUID, FK → UnitOfMeasure)
├── unit_price (Decimal)
├── discount_percent (Decimal)
├── discount_amount (Decimal)
├── tax_id (UUID, FK → Tax, nullable)
├── tax_amount (Decimal)
├── subtotal (Decimal)
├── total (Decimal)
├── quantity_delivered (Decimal)
├── quantity_invoiced (Decimal)
├── notes (Text)
├── created_at (DateTime)
└── updated_at (DateTime)
```

**Relationships**:
- Belongs To: Sales Order
- Belongs To: Product
- Belongs To: Unit of Measure
- Belongs To: Tax

### 5.3 Invoice

**Purpose**: Billing document for sales.

```
Invoice
├── invoice_id (UUID, PK)
├── organization_id (UUID, FK → Organization)
├── invoice_number (String, Unique)
├── invoice_type (Enum: customer_invoice, vendor_bill, credit_note, debit_note)
├── customer_id (UUID, FK → Customer, nullable)
├── vendor_id (UUID, FK → Vendor, nullable)
├── sales_order_id (UUID, FK → SalesOrder, nullable)
├── invoice_date (Date)
├── due_date (Date)
├── currency_code (String)
├── exchange_rate (Decimal)
├── status (Enum: draft, posted, sent, partial, paid, cancelled)
├── payment_status (Enum: unpaid, partial, paid, overdue)
├── subtotal (Decimal)
├── tax_amount (Decimal)
├── discount_amount (Decimal)
├── total_amount (Decimal)
├── amount_paid (Decimal)
├── amount_due (Decimal, computed)
├── billing_address (JSON)
├── notes (Text)
├── terms_and_conditions (Text)
├── posted_by_id (UUID, FK → User, nullable)
├── posted_at (DateTime, nullable)
├── created_by_id (UUID, FK → User)
├── created_at (DateTime)
├── updated_at (DateTime)
└── deleted_at (DateTime, nullable)
```

**Relationships**:
- Belongs To: Organization
- Belongs To: Customer (for customer invoices)
- Belongs To: Vendor (for vendor bills)
- Belongs To: Sales Order
- Has Many: Invoice Lines
- Has Many: Payments
- Has Many: Journal Entries (accounting)

## 6. Purchasing Entities

### 6.1 PurchaseOrder

**Purpose**: Orders placed with vendors.

```
PurchaseOrder
├── purchase_order_id (UUID, PK)
├── organization_id (UUID, FK → Organization)
├── po_number (String, Unique)
├── vendor_id (UUID, FK → Vendor)
├── contact_id (UUID, FK → Contact, nullable)
├── order_date (Date)
├── expected_delivery_date (Date)
├── buyer_id (UUID, FK → Employee)
├── destination_warehouse_id (UUID, FK → Warehouse)
├── currency_code (String)
├── exchange_rate (Decimal)
├── status (Enum: draft, confirmed, received, cancelled)
├── payment_terms (String)
├── subtotal (Decimal)
├── tax_amount (Decimal)
├── shipping_amount (Decimal)
├── total_amount (Decimal)
├── delivery_address (JSON)
├── notes (Text)
├── internal_notes (Text)
├── approved_by_id (UUID, FK → User, nullable)
├── approved_at (DateTime, nullable)
├── created_by_id (UUID, FK → User)
├── created_at (DateTime)
├── updated_at (DateTime)
└── deleted_at (DateTime, nullable)
```

**Relationships**:
- Belongs To: Organization
- Belongs To: Vendor
- Belongs To: Contact
- Belongs To: Buyer (Employee)
- Belongs To: Warehouse
- Has Many: PO Lines
- Has Many: Goods Receipts
- Has Many: Vendor Invoices

## 7. Accounting Entities

### 7.1 Account

**Purpose**: Chart of accounts.

```
Account
├── account_id (UUID, PK)
├── organization_id (UUID, FK → Organization)
├── parent_account_id (UUID, FK → Account, nullable)
├── account_code (String, Unique within organization)
├── name (String)
├── account_type (Enum: asset, liability, equity, revenue, expense)
├── account_sub_type (String)
├── currency_code (String)
├── is_reconcilable (Boolean)
├── is_active (Boolean)
├── balance (Decimal, computed)
├── created_at (DateTime)
├── updated_at (DateTime)
└── deleted_at (DateTime, nullable)
```

**Relationships**:
- Belongs To: Organization
- Belongs To: Parent Account
- Has Many: Child Accounts
- Has Many: Journal Entry Lines

### 7.2 JournalEntry

**Purpose**: Accounting transactions.

```
JournalEntry
├── journal_entry_id (UUID, PK)
├── organization_id (UUID, FK → Organization)
├── journal_id (UUID, FK → Journal)
├── entry_number (String, Unique)
├── entry_date (Date)
├── reference (String)
├── description (Text)
├── status (Enum: draft, posted)
├── posted_by_id (UUID, FK → User, nullable)
├── posted_at (DateTime, nullable)
├── created_by_id (UUID, FK → User)
├── created_at (DateTime)
├── updated_at (DateTime)
└── deleted_at (DateTime, nullable)
```

**Relationships**:
- Belongs To: Organization
- Belongs To: Journal
- Has Many: Journal Entry Lines

## Summary of Key Relationships

### One-to-Many (1:N)
- Tenant → Organizations
- Organization → Customers, Vendors, Products, Warehouses
- Customer → Sales Orders, Invoices
- Sales Order → Order Lines
- Product → Stock Items

### Many-to-Many (M:N)
- Users ↔ Roles
- Roles ↔ Permissions
- Users ↔ Organizations (assignments)
- Products ↔ Vendors
- Products ↔ Categories (can be hierarchical)

### Self-Referential
- Organization → Parent/Child Organizations
- Department → Parent/Child Departments
- Employee → Manager/Subordinates
- Product Category → Parent/Child Categories

### Polymorphic
- Contacts → Customer or Vendor
- Addresses → Customer, Vendor, Location, etc.
- Comments → Any entity
- Attachments → Any entity

## Database Design Principles

1. **UUID Primary Keys**: For distributed systems and tenant data merging
2. **Soft Deletes**: Preserve data for audit trails
3. **Timestamps**: Track creation and modification
4. **JSON Columns**: Flexible metadata and custom fields
5. **Computed Columns**: Derived values (full_name, total_amount)
6. **Indexes**: Strategic indexing for performance
7. **Foreign Keys**: Enforce referential integrity
8. **Normalization**: 3NF for transactional data
9. **Denormalization**: Strategic for reporting/analytics

## Conclusion

This entity model provides a comprehensive foundation for an enterprise ERP-CRM-SaaS system, supporting complex multi-tenant, multi-organizational operations with proper data isolation, relationships, and extensibility.
