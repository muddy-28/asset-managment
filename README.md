# Hospital Asset Management System

A complete production-ready PHP web application for managing hospital assets, departments, maintenance schedules, and more.

## Tech Stack

- **Backend:** PHP 8+ (Plain PHP, no framework)
- **Database:** MySQL
- **Frontend:** Bootstrap 5 Admin Dashboard
- **JS Libraries:** jQuery, DataTables, SweetAlert2, Chart.js
- **Icons:** Font Awesome 6

## Features

- **Dashboard** — Statistics cards and Chart.js charts (asset status distribution, assets by category)
- **Asset Management** — Full CRUD with categories, subcategories, vendors, barcode labels
- **Floor Management** — CRUD for hospital floors/buildings
- **Department Management** — CRUD for departments linked to floors
- **Location/Room Management** — CRUD for rooms linked to floors and departments
- **Vendor Management** — CRUD for suppliers/vendors
- **Category & Subcategory Management** — Organize assets by category
- **Asset Assignment** — Assign assets to floors, departments, and rooms
- **Asset Transfer** — Move assets between departments/locations with history tracking
- **Maintenance Scheduler** — Create maintenance schedules with auto-calculated due dates
- **Maintenance Logs** — Track maintenance work, parts replaced, costs, downtime
- **Calibration Records** — Track asset calibration with certificate numbers
- **Disposal Management** — Record asset disposals with automatic status updates
- **User Management** — Admin-only user CRUD with role-based access control
- **Barcode Label Printing** — Print-friendly barcode page for each asset
- **Maintenance Reminders** — View assets with maintenance due within 7 days

## Security

- PDO prepared statements for all database queries
- CSRF token protection on all forms
- Password hashing with `password_hash()` / `password_verify()`
- Session-based authentication with `session_regenerate_id()`
- Output escaping with `htmlspecialchars()` throughout
- Role-based access control (admin, manager, technician)
- Auth middleware on all protected pages

## Installation (XAMPP)

### Prerequisites

- [XAMPP](https://www.apachefriends.org/) with PHP 8.0+ and MySQL/MariaDB
- A web browser

### Steps

1. **Install XAMPP**
   - Download and install XAMPP from https://www.apachefriends.org/
   - Start Apache and MySQL from the XAMPP Control Panel

2. **Clone or copy the project**
   ```bash
   # Clone into XAMPP's htdocs directory
   cd C:\xampp\htdocs
   git clone https://github.com/muddy-28/asset-managment.git
   ```
   Or copy the project files directly into `C:\xampp\htdocs\asset-managment\`

3. **Import the database**
   - Open phpMyAdmin at http://localhost/phpmyadmin
   - Click "Import" tab
   - Select the `db.sql` file from the project root
   - Click "Go" to import

   Or via command line:
   ```bash
   mysql -u root < db.sql
   ```

4. **Configure the database connection** (optional)
   - Edit `config/database.php` if your MySQL credentials differ from the defaults
   - Default: host=`localhost`, user=`root`, password=`` (empty), database=`hospital_assets`

5. **Access the application**
   - Navigate to http://localhost/asset-managment/
   - You will be redirected to the login page

6. **Login with default credentials**
   - **Email:** `admin@hospital.com`
   - **Password:** `admin123`

## Project Structure

```
/
├── index.php                    # Root redirect to dashboard
├── db.sql                       # Complete database schema
├── README.md                    # This file
│
├── config/
│   └── database.php             # PDO database connection
│
├── auth/
│   ├── login.php                # Login page
│   ├── logout.php               # Logout handler
│   └── register.php             # User registration (admin only)
│
├── middleware/
│   └── auth_check.php           # Authentication & role middleware
│
├── views/
│   ├── header.php               # HTML head, CSS, top navbar
│   ├── sidebar.php              # Sidebar navigation
│   └── footer.php               # Scripts, flash messages, closing tags
│
├── dashboard/
│   └── index.php                # Dashboard with stats & charts
│
├── modules/
│   ├── assets/                  # Asset CRUD + view + barcode
│   │   ├── index.php
│   │   ├── create.php
│   │   ├── edit.php
│   │   ├── delete.php
│   │   ├── view.php
│   │   └── barcode.php
│   │
│   ├── categories/              # Category CRUD
│   ├── subcategories/           # Subcategory CRUD
│   ├── vendors/                 # Vendor CRUD
│   ├── floors/                  # Floor CRUD
│   ├── departments/             # Department CRUD
│   ├── locations/               # Location/Room CRUD
│   ├── assignments/             # Asset Assignment CRUD
│   ├── transfers/               # Asset Transfer (list + create)
│   ├── maintenance/             # Maintenance Schedule CRUD + reminders
│   ├── maintenance_logs/        # Maintenance Log CRUD
│   ├── calibration/             # Calibration Record CRUD
│   ├── disposal/                # Disposal Record CRUD
│   └── users/                   # User Management CRUD (admin only)
│
└── assets/
    ├── css/
    │   └── style.css            # Custom dashboard styles
    └── js/
        └── app.js               # DataTables, CSRF, SweetAlert helpers
```

## User Roles

| Role | Permissions |
|------|-------------|
| **admin** | Full access to all modules including user management |
| **manager** | Access to all modules except user management |
| **technician** | Access to all modules except user management |

## Default Admin Account

| Field | Value |
|-------|-------|
| Name | Administrator |
| Email | admin@hospital.com |
| Password | admin123 |
| Role | admin |

## Database Schema

The system uses 15 tables:

- `users` — User authentication and roles
- `floors` — Hospital floors/buildings
- `departments` — Hospital departments
- `locations` — Rooms/locations within floors and departments
- `asset_categories` — Asset category classification
- `asset_subcategories` — Asset subcategory classification
- `vendors` — Supplier/vendor information
- `assets` — Core asset records
- `asset_assignments` — Asset-to-location assignments
- `asset_transfer_history` — Asset transfer audit trail
- `asset_maintenance_schedule` — Maintenance scheduling
- `asset_maintenance_logs` — Maintenance work records
- `asset_calibration` — Calibration tracking
- `maintenance_reminders` — Maintenance notification records
- `asset_disposal` — Asset disposal records

## License

This project is provided as-is for educational and internal use.