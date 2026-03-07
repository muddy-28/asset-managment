# Troubleshooting Guide

This guide covers the most common problems encountered when installing or using the Hospital Asset Management System, and explains how to resolve them.

---

## Table of Contents

1. [Cannot Access the Application](#1-cannot-access-the-application)
2. [Login Issues](#2-login-issues)
3. [Database Connection Errors](#3-database-connection-errors)
4. [Blank Page or PHP Errors](#4-blank-page-or-php-errors)
5. [Sidebar or Styles Not Loading](#5-sidebar-or-styles-not-loading)
6. [DataTables Not Working](#6-datatables-not-working)
7. [Records Not Saving (Form Issues)](#7-records-not-saving-form-issues)
8. [Delete Confirmation Not Appearing](#8-delete-confirmation-not-appearing)
9. [Barcode Label Not Printing Correctly](#9-barcode-label-not-printing-correctly)
10. [Maintenance Reminders Not Showing](#10-maintenance-reminders-not-showing)
11. [User Cannot Log In After Creation](#11-user-cannot-log-in-after-creation)
12. [Frequently Asked Questions](#12-frequently-asked-questions)

---

## 1. Cannot Access the Application

### Symptom
Browser shows "This site can't be reached" or "Connection refused."

### Causes and Solutions

| Cause | Solution |
|-------|----------|
| Apache is not running | Open XAMPP Control Panel and click **Start** next to Apache. On Linux: `sudo systemctl start apache2` |
| Wrong URL | Ensure the URL is `http://localhost/asset-managment/` (note the spelling — no `e` at the end of "management") |
| Port conflict | Another application is using port 80. In XAMPP, click **Config → Apache → httpd.conf** and change `Listen 80` to `Listen 8080`. Then access `http://localhost:8080/asset-managment/` |
| Firewall blocking | Temporarily disable the firewall to test; then add an exception for port 80 |

---

## 2. Login Issues

### Symptom: "Invalid email or password"

| Cause | Solution |
|-------|----------|
| Wrong credentials | Verify with the admin that your email and password are correct. Default admin: `admin@hospital.com` / `admin123` |
| Caps Lock is on | Turn off Caps Lock — passwords are case-sensitive |
| Account is inactive | Ask an admin to set your account status to `active` in **Administration → Users** |
| Database not imported | The `users` table may be empty. Re-import `db.sql` |

### Symptom: Redirect loop (login page keeps reloading)

| Cause | Solution |
|-------|----------|
| Sessions not saving | Ensure the PHP session directory is writable. On Linux: `chmod 777 /var/lib/php/sessions` |
| Cookies disabled | Enable cookies in your browser |
| PHP session misconfiguration | Add `session.save_path = "/tmp"` to `php.ini` |

---

## 3. Database Connection Errors

### Symptom: "SQLSTATE[HY000] [1045] Access denied for user 'root'@'localhost'"

Your MySQL credentials in `config/database.php` are wrong.

**Fix:**

1. Open `config/database.php`.
2. Update `$username` and `$password` to match your MySQL credentials.
3. Test the connection by accessing phpMyAdmin at `http://localhost/phpmyadmin`.

### Symptom: "SQLSTATE[HY000] [1049] Unknown database 'hospital_assets'"

The database hasn't been created or the name is wrong.

**Fix:**

1. Open phpMyAdmin.
2. Check whether `hospital_assets` appears in the left panel.
3. If not, import `db.sql` (see the [Installation Guide](installation.md#4-database-setup)).

### Symptom: "SQLSTATE[HY000] [2002] Connection refused"

MySQL is not running.

**Fix:**

- XAMPP: Click **Start** next to MySQL in the Control Panel.
- Linux: `sudo systemctl start mysql`

---

## 4. Blank Page or PHP Errors

### Symptom: White blank page

PHP is probably encountering a fatal error but display is suppressed.

**Fix:**

1. Open `php.ini` (in XAMPP: `C:\xampp\php\php.ini`).
2. Set `display_errors = On` and `error_reporting = E_ALL`.
3. Restart Apache.
4. Reload the page — the error message will now be visible.

### Symptom: "Undefined function" or "Call to undefined method"

Usually means you are running an older PHP version.

**Fix:** Ensure PHP 8.0 or higher is installed. Check with:

```bash
php -v
```

### Symptom: "Headers already sent"

A PHP file has whitespace or output before `<?php`.

**Fix:** Check all PHP files for trailing whitespace before `<?php` or after `?>`. In most cases, removing the closing `?>` from include files resolves this.

---

## 5. Sidebar or Styles Not Loading

### Symptom: Plain HTML, no Bootstrap styling, no icons

CDN resources failed to load (no internet connection, or CDN is blocked).

**Fix:**

1. Check your internet connection — all frontend libraries (Bootstrap, Font Awesome, etc.) are loaded from CDNs.
2. If deploying on an air-gapped network, download the libraries locally and update the `<link>` and `<script>` tags in `views/header.php` and `views/footer.php` to point to local files.

### Symptom: Sidebar is hidden on large screens

Custom CSS may be missing.

**Fix:** Verify that `assets/css/style.css` exists and is accessible at `http://localhost/asset-managment/assets/css/style.css`.

---

## 6. DataTables Not Working

### Symptom: Tables display but search/sort/pagination do not work

JavaScript failed to initialize DataTables.

**Fix:**

1. Open the browser developer console (F12 → Console tab).
2. Look for JavaScript errors.
3. Common cause: jQuery or DataTables CDN failed to load. Check your internet connection or switch to local copies.

### Symptom: Table shows "No data available"

The database table is empty, or a query returned no rows.

**Fix:** Add records through the corresponding module's "Add" form.

---

## 7. Records Not Saving (Form Issues)

### Symptom: Form submits but record is not created; page reloads silently

| Cause | Solution |
|-------|----------|
| CSRF token mismatch | The session may have expired. Log out and log in again, then retry |
| Required field left empty | Check all fields marked with `*` are filled |
| Duplicate unique value | The `asset_tag` or user `email` already exists — use a different value |
| PHP error during save | Enable `display_errors` (see [Section 4](#4-blank-page-or-php-errors)) to see the error |

---

## 8. Delete Confirmation Not Appearing

### Symptom: Clicking Delete immediately deletes the record without confirmation

SweetAlert2 failed to load.

**Fix:**

1. Open browser console (F12) and look for script errors.
2. Check whether `https://cdn.jsdelivr.net/npm/sweetalert2@11` is loading (Network tab in DevTools).
3. If CDN is blocked, download SweetAlert2 and host it locally.

---

## 9. Barcode Label Not Printing Correctly

### Symptom: Barcode label is cut off or does not fill the paper

**Fix:**

1. In the browser print dialog, set **Margins** to **None** or **Minimum**.
2. Disable **Headers and footers** in the print dialog.
3. Set **Scale** to 100%.
4. Use Chrome or Edge for best print rendering.

### Symptom: Barcode image is missing

The barcode generation relies on the value stored in the `barcode` field of the asset.

**Fix:** Edit the asset and ensure the **Barcode** field is not empty.

---

## 10. Maintenance Reminders Not Showing

### Symptom: Reminders page is empty even though schedules exist

**Causes:**

| Cause | Solution |
|-------|----------|
| `next_due_date` is not set | Edit the maintenance schedule and ensure **Last Maintenance Date** is set — the system calculates `next_due_date` from it |
| Due date is more than 7 days away | Reminders only show tasks due within **7 days**. This is working as intended |
| Schedule is set to `inactive` | Edit the schedule and set **Status** to `active` |

---

## 11. User Cannot Log In After Creation

### Symptom: Newly created user says "Invalid email or password"

| Cause | Solution |
|-------|----------|
| Account status is `inactive` | Edit the user and set Status to `active` |
| Password was not set | Edit the user, enter a new password, and save |
| Email typo | Edit the user and verify the email address is spelled correctly |

---

## 12. Frequently Asked Questions

**Q: Can I run this on Windows without XAMPP?**  
A: Yes. You can use [Laragon](https://laragon.org/), [WampServer](https://www.wampserver.com/), or IIS with PHP and MySQL. XAMPP is just the simplest option.

**Q: Can I use PostgreSQL instead of MySQL?**  
A: No, not without code changes. The SQL schema uses MySQL-specific syntax (ENUM types, AUTO_INCREMENT, etc.). The application would require schema and query modifications to support PostgreSQL.

**Q: Is there an API for integration with other systems?**  
A: No REST API is included in the current version. The system is a traditional server-rendered PHP application.

**Q: How do I back up the database?**  
A:
```bash
# Command line backup
mysqldump -u root hospital_assets > backup_$(date +%Y%m%d).sql

# Restore from backup
mysql -u root hospital_assets < backup_20240101.sql
```

**Q: How do I add more roles?**  
A: The `role` column in the `users` table is an ENUM with three values: `admin`, `manager`, `technician`. To add a new role, alter the table and update the middleware logic in `middleware/auth_check.php`.

**Q: Can multiple users log in at the same time?**  
A: Yes. Each user has an independent PHP session. The system supports concurrent users.

**Q: Where are uploaded files (attachments) stored?**  
A: The maintenance log has an `attachment` column but the current version stores only the file path string. File upload functionality should be implemented as a project enhancement.

---

## Still Having Issues?

1. Enable PHP error display (`display_errors = On` in `php.ini`).
2. Check the Apache error log:
   - XAMPP Windows: `C:\xampp\apache\logs\error.log`
   - Linux: `/var/log/apache2/error.log`
3. Check the MySQL error log:
   - XAMPP Windows: `C:\xampp\mysql\data\mysql_error.log`
   - Linux: `/var/log/mysql/error.log`
4. Open the browser developer console (F12) for JavaScript errors.
