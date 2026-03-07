# Configuration Guide

This guide explains all configuration options available in the Hospital Asset Management System.

---

## Table of Contents

1. [Database Connection](#1-database-connection)
2. [Authentication Settings](#2-authentication-settings)
3. [Session Configuration](#3-session-configuration)
4. [Application URL (Production)](#4-application-url-production)
5. [Environment Checklist](#5-environment-checklist)

---

## 1. Database Connection

The database connection is configured in a single file:

```
config/database.php
```

### Default Configuration

```php
function getDBConnection(): PDO
{
    $host    = 'localhost';
    $dbname  = 'hospital_assets';
    $username = 'root';
    $password = '';
    $charset  = 'utf8mb4';

    $dsn = "mysql:host={$host};dbname={$dbname};charset={$charset}";

    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    return new PDO($dsn, $username, $password, $options);
}
```

### Configuration Parameters

| Parameter | Default | Description |
|-----------|---------|-------------|
| `$host` | `localhost` | MySQL server hostname or IP address |
| `$dbname` | `hospital_assets` | Name of the database to connect to |
| `$username` | `root` | MySQL user account |
| `$password` | `''` (empty) | MySQL user password |
| `$charset` | `utf8mb4` | Character set — supports Unicode and emoji |

### Changing the Configuration

Open `config/database.php` and update the variables at the top of the `getDBConnection()` function:

```php
// Example: production database with a dedicated user
$host     = '192.168.1.100';     // remote MySQL server
$dbname   = 'hams_prod';
$username = 'hams_user';
$password = 'StrongPassword123!';
```

### PDO Options Explained

| Option | Value | Effect |
|--------|-------|--------|
| `ATTR_ERRMODE` | `ERRMODE_EXCEPTION` | Throws exceptions on SQL errors instead of silent failures |
| `ATTR_DEFAULT_FETCH_MODE` | `FETCH_ASSOC` | Results returned as associative arrays |
| `ATTR_EMULATE_PREPARES` | `false` | Uses true prepared statements for maximum SQL-injection protection |

> **Security tip:** Never commit real passwords to version control. On a production server, consider reading credentials from environment variables:
>
> ```php
> $password = getenv('DB_PASSWORD') ?: '';
> ```

---

## 2. Authentication Settings

Authentication settings are embedded in `auth/login.php` and `middleware/auth_check.php`. There is no separate auth config file, but the following behaviors can be adjusted by editing those files:

### Session Variables Set on Login

| Variable | Description |
|----------|-------------|
| `$_SESSION['user_id']` | Database ID of the logged-in user |
| `$_SESSION['user_name']` | Full name of the user |
| `$_SESSION['user_email']` | Email address of the user |
| `$_SESSION['user_role']` | Role: `admin`, `manager`, or `technician` |

### CSRF Token

A CSRF token is generated once per session and stored in `$_SESSION['csrf_token']`. It is embedded in every form as a hidden field and validated on every POST request.

To regenerate the token, simply destroy the session (logout) and log in again.

---

## 3. Session Configuration

The application relies on PHP's default session configuration. For better security on a production server, add the following to your `php.ini` or to the top of `config/database.php`:

```ini
; php.ini recommended settings for production
session.cookie_httponly = 1
session.cookie_secure   = 1     ; requires HTTPS
session.use_strict_mode = 1
session.gc_maxlifetime  = 1800  ; 30-minute idle timeout
```

Or set them in PHP code (add to `config/database.php` or a new `config/session.php`):

```php
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);
session_set_cookie_params(['samesite' => 'Strict']);
```

---

## 4. Application URL (Production)

If deploying the application in a subdirectory other than `/asset-managment/`, update any hardcoded redirect paths.

The sidebar navigation in `views/sidebar.php` and redirect statements throughout the modules use relative paths (e.g., `/asset-managment/modules/assets/`). If you change the deployment directory name, do a global search-and-replace for `/asset-managment/` in all PHP files.

**Example — Rename subdirectory from `asset-managment` to `hams`:**

```bash
# Linux/macOS
grep -rl '/asset-managment/' . --include="*.php" | xargs sed -i 's|/asset-managment/|/hams/|g'
```

---

## 5. Environment Checklist

Use this checklist before going to production:

- [ ] Change `$password` in `config/database.php` to a strong, unique password.
- [ ] Create a dedicated MySQL user with only the required permissions (SELECT, INSERT, UPDATE, DELETE on `hospital_assets`).
- [ ] Change the default admin password (`admin123`) after the first login.
- [ ] Enable HTTPS and set `session.cookie_secure = 1`.
- [ ] Disable PHP error display in `php.ini` (`display_errors = Off`; keep `log_errors = On`).
- [ ] Set appropriate file permissions (`chmod 640` for PHP files; `chmod 755` for directories).
- [ ] Restrict direct access to `config/` via Apache `.htaccess`:

  ```apache
  # config/.htaccess
  Deny from all
  ```

- [ ] Set up regular database backups (daily `mysqldump` recommended).
