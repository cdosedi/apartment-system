# Casa Oro - Apartment Management System

A modern apartment and room rental management system built with Laravel 12, featuring tenant management, lease tracking, payment processing, electric bill management, and comprehensive income reporting.

## Features

### Core Modules

- **Dashboard** - Real-time analytics with occupancy rates, revenue metrics, and profit trends
- **Tenant Management** - Multi-step tenant onboarding with lease agreement generation
- **Lease Management** - Full lease lifecycle management with automatic payment scheduling
- **Room Management** - Bed-based capacity tracking with dynamic availability
- **Electric Bill Management** - Per-room billing with pro-rated sharing among tenants
- **Payment Processing** - Individual and bulk payment handling with receipt generation
- **Income Reports** - Monthly/yearly financial reports with Excel export
- **Admin Management** - Role-based admin accounts with soft delete support

### Key Features

- PDF generation for lease agreements, invoices, and receipts
- Excel export for income reports
- Soft delete support for recoverable records
- Automatic payment status tracking (pending → overdue → paid)
- Real-time utility expense tracking and recovery analysis
- Multi-tenant architecture with bed-level capacity

## Tech Stack

- **Backend**: Laravel 12 (PHP 8.2+)
- **Frontend**: Blade Templates + Tailwind CSS + Alpine.js
- **Build Tool**: Vite
- **Database**: MySQL
- **PDF Generation**: Barryvdh/laravel-dompdf
- **Excel Export**: Maatwebsite/Excel

## Requirements

- PHP 8.2 or higher
- Composer
- Node.js 18+ and npm
- MySQL 8.0+ or compatible database

## Installation

### 1. Clone and Install Dependencies

```bash
git clone <repository-url>
cd apartment-system
composer install
npm install
```

### 2. Environment Setup

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` and configure your database connection:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=apartment-system
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 3. Database Setup

```bash
php artisan migrate
php artisan db:seed --class=TenantLeaseSeeder  # Seeds sample data
```

The seeder includes:
- 40 rooms with 3-bed capacity each
- Multiple tenants with overlapping leases
- Balanced payment statuses (paid, pending, overdue)
- Electric bills with proper pro-rated sharing calculation

### 4. Build Frontend Assets

```bash
npm run build
```

### 5. Start Development Server

```bash
php artisan serve
```

Visit `http://localhost:8000` in your browser.

## Project Structure

```
apartment-system/
├── app/
│   ├── Http/
│   │   ├── Controllers/       # Application controllers
│   │   │   ├── AdminController.php
│   │   │   ├── DashboardController.php
│   │   │   ├── ElectricBillController.php
│   │   │   ├── LeaseController.php
│   │   │   ├── PaymentController.php
│   │   │   ├── ReportController.php
│   │   │   ├── RoomController.php
│   │   │   └── TenantController.php
│   │   └── Requests/         # Form request validation classes
│   ├── Models/               # Eloquent models
│   │   ├── User.php
│   │   ├── Tenant.php
│   │   ├── Lease.php
│   │   ├── Room.php
│   │   ├── LeasePayment.php
│   │   ├── ElectricBill.php
│   │   ├── Invoice.php
│   │   └── Receipt.php
│   ├── Exports/              # Excel export classes
│   └── Providers/            # Service providers
├── bootstrap/                # Application bootstrap
├── config/                   # Laravel configuration files
├── database/
│   ├── migrations/           # Database schema migrations
│   ├── factories/             # Model factories for testing
│   └── seeders/              # Database seeders
├── resources/
│   └── views/                # Blade templates
│       ├── admins/
│       ├── auth/
│       ├── dashboard.blade.php
│       ├── electric-bills/
│       ├── leases/
│       ├── layouts/
│       ├── partials/
│       ├── payments/
│       ├── pdf/               # PDF templates
│       ├── reports/
│       └── tenants/
├── routes/
│   ├── web.php              # Main web routes
│   └── auth.php             # Authentication routes
├── storage/
│   ├── app/                  # Application storage
│   ├── framework/            # Framework cache/views
│   └── logs/                 # Application logs
└── tests/                    # PHPUnit tests
```

## Database Schema

### Core Tables

| Table | Description |
|-------|-------------|
| `users` | Admin/user accounts with is_admin flag |
| `tenants` | Tenant profiles with contact and status info |
| `leases` | Lease agreements linking tenants to rooms |
| `rooms` | Room definitions with bed capacity |
| `lease_payments` | Monthly payment schedule and tracking |
| `electric_bills` | Monthly electric bill records per room |
| `invoices` | Generated invoice records |
| `receipts` | Payment receipt records |

### Key Relationships

```
User → (hasMany) → Tenant (via created_by)
Tenant → (hasMany) → Lease
Lease → (belongsTo) → Room
Lease → (hasMany) → LeasePayment
Room → (hasMany) → ElectricBill
LeasePayment → (belongsTo) → ElectricBill
LeasePayment → (hasOne) → Invoice
LeasePayment → (hasOne) → Receipt
```

## Usage Guide

### Creating a New Tenant

1. Navigate to **Tenants** → **Add New Tenant**
2. Fill in tenant information (Step 1)
3. Select available room and lease terms (Step 2)
4. Preview and accept lease agreement (Step 3)
5. Confirm and create tenant (Step 4)

The system will automatically:
- Create the tenant record
- Create the lease agreement
- Generate monthly payment dues
- Mark first month as paid

### Managing Electric Bills

1. Navigate to **Electric** in the sidebar
2. Click **Add New Bill** for a specific room
3. Enter the billing month and total amount
4. The system automatically:
   - Calculates pro-rated shares per tenant
   - Distributes to their pending payments
   - Tracks unpaid debt for carry-over

### Electric Bill Sharing Logic

The system uses **bed-days calculation** for fair pro-rating:

1. **Calculate Total Room Bed-Days**: Sum of all tenants' occupied days in the billing month
2. **Calculate Tenant's Share**: `(Tenant's Days / Total Room Bed-Days) × Room Total Bill`
3. **Handle Move-in/Move-out**: First month and last month are pro-rated based on actual stay
4. **Debt Carry-over**: If payment is not yet made, electric debt carries to next month

**Payment Status Logic**:
- **Paid on time**: Electric is deferred to next month (collected with next bill)
- **Paid late**: Current electric + previous debt is collected immediately
- **Overdue**: Current electric + previous debt shown, debt cleared after processing

### Processing Payments

1. Navigate to tenant's **Show** page
2. Find the pending payment
3. Click **Pay** and enter payment method
4. System generates receipt automatically

### Generating Reports

1. Navigate to **Reports** → **Income**
2. Select time period (Month or Year)
3. Use date picker to navigate periods
4. Click **Download Excel** for detailed export

## Available Routes

### Dashboard & Home
| Route | Description |
|-------|-------------|
| `/` | Login page |
| `/dashboard` | Main analytics dashboard |

### Tenant Management
| Route | Description |
|-------|-------------|
| `/tenants` | List all tenants |
| `/tenants/create` | Create new tenant (multi-step) |
| `/tenants/{tenant}` | View tenant details |
| `/tenants/{tenant}/edit` | Edit tenant |

### Lease Management
| Route | Description |
|-------|-------------|
| `/leases` | List active leases |
| `/leases/{lease}` | View lease details |
| `/leases/{lease}/download-agreement` | Download PDF lease |
| `/leases/{lease}/pay-in-full` | Pay all pending payments |

### Room Management
| Route | Description |
|-------|-------------|
| `/rooms` | List all rooms |
| `/rooms/create` | Add new room |
| `/rooms/{room}/add-bed` | Increase bed capacity |
| `/rooms/{room}/remove-bed` | Decrease bed capacity |

### Electric Bills
| Route | Description |
|-------|-------------|
| `/electric-bills` | List all electric bills |
| `/electric-bills/room/{room}` | Room-specific bills |

### Payments
| Route | Description |
|-------|-------------|
| `/lease-payments/{payment}/pay` | Process payment |
| `/lease-payments/{payment}/invoice` | Download invoice PDF |
| `/lease-payments/{payment}/receipt` | Download receipt PDF |

### Reports
| Route | Description |
|-------|-------------|
| `/reports/income` | Income report view |
| `/reports/income/download` | Download Excel export |

### Admin Management
| Route | Description |
|-------|-------------|
| `/admins` | List admins |
| `/admins/create` | Create admin |
| `/admins/{admin}/edit` | Edit admin |

## Development

### Running Tests

```bash
php artisan test
```

### Running in Development Mode

Starts all required services concurrently:
```bash
composer dev
```

This runs:
- PHP development server
- Queue worker
- Error logging
- Vite dev server

### Code Style

```bash
# Fix code style
composer fix

# Or run manually
./vendor/bin/pint
```

### Clearing Cache

```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

## Configuration Files

| File | Purpose |
|------|---------|
| `.env` | Environment variables (DB, mail, etc.) |
| `vite.config.js` | Vite build configuration |
| `tailwind.config.js` | Tailwind CSS configuration |
| `config/app.php` | Application settings |
| `config/database.php` | Database configuration |
| `config/mail.php` | Mail configuration |

## Security Considerations

- Routes are protected with `auth` and `admin` middleware
- User passwords are hashed using bcrypt
- Email verification required for admin access
- Soft deletes prevent permanent data loss
- All user inputs are validated via Form Requests

## License

This project is proprietary software. All rights reserved.
