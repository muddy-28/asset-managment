# Installation Guide

This guide explains how to install the Hospital Asset Management System on a local development machine (using XAMPP) and on a Linux/production server.

---

## Table of Contents

1. [Prerequisites](#1-prerequisites)
2. [XAMPP Installation (Windows / macOS)](#2-xampp-installation-windows--macos)
3. [Linux / macOS (Built-in PHP & MySQL)](#3-linux--macos-built-in-php--mysql)
4. [Database Setup](#4-database-setup)
5. [First Login](#5-first-login)
6. [Verifying the Installation](#6-verifying-the-installation)

---

## 1. Prerequisites

| Requirement | Version | Notes |
|-------------|---------|-------|
| PHP | 8.0 or higher | Required for PDO, named arguments, etc. |
| MySQL / MariaDB | 5.7+ / 10.4+ | Any compatible version |
| Apache Web Server | 2.4+ | Or any server that supports PHP |
| Git (optional) | Any | For cloning the repository |
| Web browser | Modern | Chrome, Firefox, Edge, Safari |

> **No Composer or npm dependencies are required.** All frontend libraries are loaded from CDNs.

---

## 2. XAMPP Installation (Windows / macOS)

XAMPP bundles Apache, PHP, and MySQL into a single installer — the simplest way to run the system on a local machine.

### Step 1 — Download and Install XAMPP

1. Visit [https://www.apachefriends.org/](https://www.apachefriends.org/)
2. Download the XAMPP installer for your operating system (choose the PHP 8.x version).
3. Run the installer and make sure the following components are selected:
   - ☑️ Apache
   - ☑️ MySQL
   - ☑️ PHP
   - ☑️ phpMyAdmin
4. Complete the installation (default path is `C:\xampp` on Windows).

### Step 2 — Start Apache and MySQL

1. Open the **XAMPP Control Panel**.
2. Click **Start** next to **Apache**.
3. Click **Start** next to **MySQL**.
4. Both status lights should turn green.

### Step 3 — Download the Project

**Option A — Git clone (recommended)**

```bash
cd C:\xampp\htdocs
git clone https://github.com/muddy-28/asset-managment.git
```

**Option B — Download ZIP**

1. Download the ZIP from the GitHub repository.
2. Extract it into `C:\xampp\htdocs\asset-managment\`.

The final path should be:

```
C:\xampp\htdocs\asset-managment\
├── index.php
├── db.sql
├── README.md
├── config\
├── auth\
├── modules\
└── ...
```

### Step 4 — Import the Database

See [Section 4 — Database Setup](#4-database-setup).

### Step 5 — Access the Application

Open your browser and navigate to:

```
http://localhost/asset-managment/
```

You will be redirected to the login page automatically.

---

## 3. Linux / macOS (Built-in PHP & MySQL)

Use this approach when you already have PHP and MySQL installed natively.

### Step 1 — Install Dependencies (if needed)

**Ubuntu / Debian:**

```bash
sudo apt update
sudo apt install php8.1 php8.1-mysql mysql-server apache2 -y
sudo systemctl start apache2 mysql
```

**macOS (Homebrew):**

```bash
brew install php mysql
brew services start mysql
```

### Step 2 — Clone the Repository

```bash
# Replace /var/www/html with your web root
cd /var/www/html
sudo git clone https://github.com/muddy-28/asset-managment.git
sudo chown -R www-data:www-data asset-managment   # Ubuntu
```

**macOS / development-only (using PHP built-in server):**

```bash
git clone https://github.com/muddy-28/asset-managment.git
cd asset-managment
```

### Step 3 — Import the Database

See [Section 4 — Database Setup](#4-database-setup).

### Step 4 — Start the Application

**Apache:** Navigate to `http://localhost/asset-managment/`

**PHP built-in server (development only):**

```bash
cd /path/to/asset-managment
php -S localhost:8000
```

Then open `http://localhost:8000/` in your browser.

> ⚠️ The PHP built-in server is for **development only**. Do not use it in production.

---

## 4. Database Setup

### Option A — phpMyAdmin (Graphical)

1. Open `http://localhost/phpmyadmin` in your browser.
2. Log in with your MySQL credentials (default: user `root`, password empty).
3. Click the **Import** tab in the top navigation.
4. Click **Browse** and select the `db.sql` file from the project root.
5. Leave all other settings at their defaults.
6. Click **Go**.

phpMyAdmin will create the `hospital_assets` database and import all 15 tables automatically.

### Option B — Command Line

```bash
# Creates the database and imports all tables
mysql -u root < /path/to/asset-managment/db.sql
```

If your MySQL root user has a password:

```bash
mysql -u root -p < /path/to/asset-managment/db.sql
```

### Option C — MySQL Shell

```sql
source /path/to/asset-managment/db.sql;
```

### Verifying the Import

```sql
USE hospital_assets;
SHOW TABLES;
```

You should see 15 tables:

```
asset_assignments
asset_calibration
asset_categories
asset_disposal
asset_maintenance_logs
asset_maintenance_schedule
asset_subcategories
asset_transfer_history
assets
departments
floors
locations
maintenance_reminders
users
vendors
```

---

## 5. First Login

| Field | Value |
|-------|-------|
| Email | `admin@hospital.com` |
| Password | `admin123` |

> ⚠️ **Security Notice:** Change the default password immediately after your first login. Navigate to **Administration → Users**, find the Administrator account, and update the password.

---

## 6. Verifying the Installation

Use the checklist below to confirm everything is working:

- [ ] Apache is running (green in XAMPP Control Panel or `systemctl status apache2`)
- [ ] MySQL is running (green in XAMPP Control Panel or `systemctl status mysql`)
- [ ] `http://localhost/asset-managment/` loads and redirects to the login page
- [ ] Login with `admin@hospital.com` / `admin123` succeeds
- [ ] Dashboard shows statistics cards (may show zeros if no data added yet)
- [ ] Sidebar navigation is visible and all links are clickable
- [ ] No PHP errors displayed on screen (error reporting is enabled in development mode)
- [ ] Browser console (F12) shows no critical JavaScript errors

---

## Next Steps

- [Configure the database connection](configuration.md) if your MySQL credentials differ from the defaults.
- Follow the [User Guide](user-guide.md) to start adding floors, departments, and assets.
- Review [Users & Roles](modules/users-and-roles.md) to set up additional staff accounts.
