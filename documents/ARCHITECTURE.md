# HSL Labs - Healthcare Management System Architecture

## System Overview

HSL Labs is a comprehensive healthcare management system designed for post-surgery patient care, featuring inventory management, recurring treatment plans, and role-based access control. The system facilitates seamless collaboration between surgeons and their staff while maintaining strict data isolation and security.

## Architecture Stack

-   **Framework**: Laravel 12.36.1
-   **PHP Version**: 8.2.4
-   **Database**: MySQL/MariaDB
-   **Frontend**: Blade Templates with Bootstrap 5.1.3
-   **JavaScript**: jQuery 3.6.0
-   **Icons**: FontAwesome 6.0.0
-   **Authentication**: Laravel Breeze
-   **Permissions**: Spatie Laravel Permission

## Database Schema

### Core Tables

#### Users Table

-   Primary user authentication table
-   Stores basic user information
-   Links to role-based permissions

#### Patients Table

-   Patient medical records
-   Linked to specific doctors
-   Supports active/inactive status management

#### Staff Details Table

-   Links staff users to their assigned doctors
-   Enables hierarchical access control
-   Supports doctor-staff relationship management

#### Products Table

-   Medical product inventory
-   Supports stock management
-   Custom selling price per doctor
-   Purchase price tracking

#### Purchase Orders System

-   `purchase_orders` - Doctor inventory requests
-   `purchase_order_items` - Individual product items
-   Status tracking (pending, confirmed, delivered, cancelled)

#### Sales Orders System

-   `sales_orders` - Staff-created patient orders
-   `sales_order_items` - Individual product items
-   Instant confirmation for staff orders
-   Real-time stock deduction

#### Recurring Orders System

-   `recurring_orders` - Monthly treatment plans (2-12 months)
-   `recurring_order_items` - Template product items
-   Automatic monthly processing
-   Status management (active, paused, completed, cancelled)

## User Roles & Permissions

### 1. Surgeon Role

**Primary Responsibilities:**

-   Complete system oversight
-   Inventory management
-   Staff supervision
-   Patient management

**Key Capabilities:**

-   Add/manage patients
-   Create/manage staff accounts
-   Purchase inventory from suppliers
-   Set custom selling prices for products
-   View all orders and recurring plans
-   Access comprehensive reporting

### 2. Staff Role

**Primary Responsibilities:**

-   Patient order management
-   Recurring treatment setup
-   Daily operations support

**Key Capabilities:**

-   Create sales orders for assigned doctor's patients
-   Setup recurring treatment plans (2-12 months)
-   Process due recurring orders
-   View assigned patients only
-   Access filtered reporting

## Core Functionality

### 1. Authentication & Access Control

**Implementation:**

-   Laravel Breeze for authentication
-   Spatie Laravel Permission for role management
-   Middleware-based route protection
-   Session-based user management

**Security Features:**

-   Role-based access control (RBAC)
-   Data isolation by doctor assignment
-   Staff limited to assigned doctor's data
-   Secure password requirements

### 2. Patient Management System

**Features:**

-   Patient registration and profiles
-   Medical history tracking
-   Doctor assignment system
-   Status management (active/inactive)

**Access Control:**

-   Surgeons: Full access to their patients
-   Staff: Read-only access to assigned doctor's patients

### 3. Inventory Management System

#### Purchase Orders (Doctor Workflow)

**Process Flow:**

1. Doctor browses product catalog
2. Creates purchase orders from suppliers
3. Admin confirms/delivers orders
4. Stock automatically updated
5. Products become available for sales

**Features:**

-   Product browsing with search/filter
-   Multi-item order creation
-   Status tracking and history
-   Inventory level monitoring
-   Custom selling price management

#### Sales Orders (Staff Workflow)

**Process Flow:**

1. Staff selects patient from assigned doctor
2. Chooses products from available inventory
3. Order instantly confirmed and processed
4. Stock automatically deducted
5. Order recorded in system

**Features:**

-   Real-time stock checking
-   Automatic price calculation using custom selling prices
-   Instant order confirmation
-   Complete order history

### 4. Recurring Orders System

**Purpose:**
Automated monthly treatment plans for post-surgery patients requiring 2-12 months of recurring medication/supplies.

**Key Features:**

-   **Flexible Duration**: 2-12 months treatment plans
-   **Monthly Processing**: Automatic order generation on specified day
-   **Stock Management**: Real-time inventory checking and deduction
-   **Dynamic Pricing**: Uses current selling prices at processing time
-   **Status Control**: Active, paused, completed, cancelled states

**Workflow:**

1. Staff creates recurring order template
2. System schedules monthly processing
3. Automatic sales order generation on due dates
4. Stock verification and deduction
5. Completion tracking and notifications

**Processing Logic:**

-   Due date calculation based on day-of-month setting
-   Automatic stock availability checking
-   Current selling price application
-   Remaining months decrementing
-   Auto-completion when duration reached

### 5. Product & Pricing Management

**Selling Price System:**

-   Default: Selling price = Purchase price
-   Override: Doctors can set custom selling prices
-   Real-time updates via AJAX interface
-   Staff orders use doctor's custom pricing

**Stock Management:**

-   Real-time inventory tracking
-   Automatic deduction on sales
-   Stock availability validation
-   Low stock monitoring

## Technical Implementation

### 1. Database Design

**Key Relationships:**

```
Users (Surgeons) → Patients (1:Many)
Users (Surgeons) → Staff Details (1:Many)
Users (Staff) → Staff Details (1:1)
Patients → Sales Orders (1:Many)
Patients → Recurring Orders (1:Many)
Products → Purchase Order Items (1:Many)
Products → Sales Order Items (1:Many)
Products → Recurring Order Items (1:Many)
```

**Indexing Strategy:**

-   Role-based access optimization
-   Date-based query optimization
-   Status filtering optimization
-   Foreign key constraint optimization

### 2. Controller Architecture

**Medical Controllers:**

-   `PatientController` - Patient CRUD operations
-   `StaffController` - Staff management
-   `PurchaseOrderController` - Inventory purchasing
-   `SalesOrderController` - Patient order management
-   `RecurringOrderController` - Recurring treatment plans

### 3. Business Logic Implementation

**Order Processing:**

-   Validation layers for stock availability
-   Transactional operations for data consistency
-   Error handling with rollback mechanisms
-   Audit trail maintenance

**Recurring Order Processing:**
``
// 1. Validate due date and remaining months
// 2. Check stock availability for all items
// 3. Create sales order with current prices
// 4. Deduct stock quantities
// 5. Update recurring order status
// 6. Calculate next due date

```

### 4. Frontend Architecture

**Layout Structure:**
- `layouts/medical.blade.php` - Main medical portal layout
- Role-based navigation menus
- Responsive Bootstrap design
- Dynamic status indicators

**JavaScript Functionality:**
- Real-time price calculations
- Dynamic form field management
- AJAX operations for price updates
- Form validation and user feedback

## Test User Credentials

### Surgeon Account
```

Email: doctor@example.com
Password: password123
Role: Surgeon
Capabilities:

-   Full system access
-   Patient management
-   Staff supervision
-   Inventory purchasing
-   Price management

```

### Staff Account
```

Email: staff@example.com
Password: password123
Role: Staff
Doctor Assignment: Doctor ID 2
Capabilities:

-   Limited to assigned doctor's patients
-   Sales order creation
-   Recurring order management
-   Order processing

```

### Admin Account
```

Email: admin@example.com
Password: password123
Role: Admin
Capabilities:

-   System administration
-   User management
-   Purchase order confirmation
-   System settings

```

## Testing Scenarios

### 1. Purchase Order Workflow
```

1. Login as surgeon (doctor@example.com)
2. Navigate to Purchase Orders → Browse Products
3. Add products to cart and create order
4. Login as admin to confirm/deliver order
5. Verify stock updates in Inventory in Hand

```

### 2. Sales Order Workflow
```

1. Login as staff (staff@example.com)
2. Navigate to Patient Orders → Create Order
3. Select patient from assigned doctor
4. Add products and submit (instant confirmation)
5. Verify stock deduction and order history

```

### 3. Recurring Order Workflow
```

1. Login as staff (staff@example.com)
2. Navigate to Recurring Orders → Setup Recurring Order
3. Create 6-month plan for patient
4. Set monthly processing day
5. Monitor Due for Processing section
6. Process due orders manually or wait for automation

```

### 4. Price Management Workflow
```

1. Login as surgeon (doctor@example.com)
2. Navigate to Purchase Orders → Inventory in Hand
3. Update selling prices for products
4. Login as staff to verify updated prices in orders

````

## System Features Summary

### Implemented Features ✅
- ✅ User authentication and role management
- ✅ Patient management with doctor assignment
- ✅ Staff management and doctor linking
- ✅ Product inventory system
- ✅ Purchase order system for doctors
- ✅ Sales order system for staff
- ✅ Custom selling price management
- ✅ Recurring order system (2-12 months)
- ✅ Automatic monthly order processing
- ✅ Real-time stock management
- ✅ Role-based data access control
- ✅ Responsive web interface
- ✅ Order status management
- ✅ Due order notifications

### Advanced Features
- **Dynamic Pricing**: Real-time selling price updates
- **Flexible Recurring Plans**: 2-12 month treatment options
- **Smart Stock Management**: Automatic availability checking
- **Role-Based Security**: Strict data isolation
- **Audit Trail**: Complete order history tracking
- **Responsive Design**: Mobile-friendly interface

## Database Seeders

### Default Data Creation
```bash
php artisan db:seed --class=RolesAndAdminSeeder
````

**Creates:**

-   Admin user with full permissions
-   Surgeon role with medical permissions
-   Staff role with limited permissions
-   Test surgeon and staff accounts
-   Sample patients and products

## API Endpoints

### Medical Portal Routes

```
/medical/dashboard - Medical portal dashboard
/medical/patients/* - Patient management
/medical/staff/* - Staff management (surgeons only)
/medical/purchase-orders/* - Inventory management
/medical/sales-orders/* - Patient order management
/medical/recurring-orders/* - Recurring treatment plans
```

### Authentication Routes

```
/login - User authentication
/register - User registration (if enabled)
/logout - Session termination
/profile - User profile management
```

## Security Considerations

### Data Protection

-   Role-based access control (RBAC)
-   Doctor-staff data isolation
-   Patient data privacy
-   Secure password hashing

### Business Logic Security

-   Stock validation before order processing
-   Transactional operations for data integrity
-   Input validation and sanitization
-   CSRF protection on all forms

## Performance Optimizations

### Database Optimizations

-   Strategic indexing on frequently queried fields
-   Eager loading for relationship queries
-   Optimized queries for role-based filtering

### Frontend Optimizations

-   Lazy loading for large datasets
-   AJAX for real-time updates
-   Responsive design for mobile devices

## Future Enhancement Possibilities

### Planned Features

-   Payment gatway integration
-   SMS/Email notifications for due orders
-   Advanced reporting and analytics
-   API endpoints for mobile applications
-   Automated recurring order processing via scheduled tasks
-   Inventory alerts and reorder points
-   Patient communication portal
-   Advanced search and filtering
-   Export functionality for reports

### Scalability Considerations

-   Database partitioning for large datasets
-   Caching strategies for improved performance
-   Queue system for order processing
-   Load balancing for high availability

---
