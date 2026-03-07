# Hospital Asset Management System — Documentation

Welcome to the official documentation for the **Hospital Asset Management System** (HAMS), a production-ready PHP web application for tracking and managing all physical assets within a hospital environment.

---

## Table of Contents

| Document | Description |
|----------|-------------|
| [Installation Guide](installation.md) | How to install and set up the system on XAMPP, Linux, or a production server |
| [Configuration Guide](configuration.md) | Database connection, environment settings, and customization options |
| [User Guide](user-guide.md) | Step-by-step guide for logging in, navigating the dashboard, and daily use |
| [Assets Module](modules/assets.md) | Managing assets: create, view, edit, assign, transfer, print barcode |
| [Departments & Locations](modules/departments-and-locations.md) | Managing floors, departments, and rooms/locations |
| [Maintenance Guide](modules/maintenance.md) | Maintenance schedules, logs, calibration, and reminders |
| [Users & Roles](modules/users-and-roles.md) | User management, roles, and permissions |
| [Database Schema](database-schema.md) | Full reference of all 15 database tables and their columns |
| [Troubleshooting](troubleshooting.md) | Common problems and how to resolve them |

---

## What Is This System?

The Hospital Asset Management System helps healthcare facilities:

- **Track** every physical asset (medical equipment, furniture, IT hardware, etc.) from purchase to disposal.
- **Organize** assets by floor, department, and room with a clear location hierarchy.
- **Schedule and log** preventive maintenance, repairs, and calibrations.
- **Audit** asset transfers between departments.
- **Manage** users with role-based access control so each staff member sees only what they need.

---

## Key Features

| Feature | Description |
|---------|-------------|
| 📊 **Dashboard** | At-a-glance statistics, asset-status chart, and category distribution chart |
| 🏷️ **Asset Management** | Full lifecycle tracking from acquisition to disposal, including barcode labels |
| 🏢 **Location Hierarchy** | Floors → Departments → Rooms for precise asset tracking |
| 🔄 **Assignments & Transfers** | Assign assets to a specific room; move them with a full transfer history |
| 🔧 **Maintenance Scheduling** | Create recurring maintenance schedules; get 7-day advance reminders |
| 📋 **Maintenance Logs** | Record every maintenance event: technician, cost, downtime, parts replaced |
| 📐 **Calibration Records** | Track calibration dates and certificate numbers for regulated equipment |
| ♻️ **Disposal Management** | Log asset disposals with approval and method details |
| 👥 **User Management** | Admin-only CRUD for users with three roles: Admin, Manager, Technician |
| 🔒 **Security** | CSRF tokens, PDO prepared statements, bcrypt passwords, session regeneration |

---

## Technology Stack

| Layer | Technology |
|-------|-----------|
| Backend | PHP 8.0+ (plain PHP, no framework) |
| Database | MySQL / MariaDB via PDO |
| Web Server | Apache (XAMPP recommended) |
| CSS Framework | Bootstrap 5.3 |
| JavaScript | jQuery 3.7, DataTables 1.13, SweetAlert2 11, Chart.js 4.4 |
| Icons | Font Awesome 6.4 |

---

## User Roles at a Glance

| Role | Dashboard | Assets & Ops | Maintenance | User Mgmt |
|------|-----------|-------------|-------------|-----------|
| **Admin** | ✅ | ✅ | ✅ | ✅ |
| **Manager** | ✅ | ✅ | ✅ | ❌ |
| **Technician** | ✅ | ✅ | ✅ | ❌ |

See the [Users & Roles guide](modules/users-and-roles.md) for full permission details.

---

## Quick Start

1. [Install the system](installation.md)
2. Open your browser and go to `http://localhost/asset-managment/`
3. Log in with `admin@hospital.com` / `admin123`
4. Change the default password immediately in the **Users** module
5. Follow the [User Guide](user-guide.md) to populate floors, departments, and assets

---

## Contributing & License

This project is provided for educational and internal use.  
See the root [`README.md`](../README.md) for license information.
