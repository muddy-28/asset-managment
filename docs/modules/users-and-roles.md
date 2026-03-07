# Users & Roles Guide

This guide explains how to manage user accounts and what each role can and cannot do in the Hospital Asset Management System.

---

## Table of Contents

1. [Role Overview](#1-role-overview)
2. [Permission Matrix](#2-permission-matrix)
3. [Managing Users (Admin Only)](#3-managing-users-admin-only)
4. [Adding a New User](#4-adding-a-new-user)
5. [Editing a User](#5-editing-a-user)
6. [Deactivating a User](#6-deactivating-a-user)
7. [Deleting a User](#7-deleting-a-user)
8. [Changing Your Own Password](#8-changing-your-own-password)
9. [Security Best Practices](#9-security-best-practices)

---

## 1. Role Overview

The system has three built-in roles:

| Role | Typical User | Summary |
|------|-------------|---------|
| **admin** | System Administrator | Full access, including user management |
| **manager** | Department Manager, Biomedical Engineer | Full access to all asset and maintenance features, no user management |
| **technician** | Maintenance Technician, IT Support | Full access to all asset and maintenance features, no user management |

All roles can:
- Log in and view the dashboard
- View, add, edit, and delete assets, categories, vendors, floors, departments, locations
- Create and manage assignments and transfers
- Manage maintenance schedules, logs, calibration records, and disposal records

Only **admin** can:
- View, add, edit, and delete user accounts

---

## 2. Permission Matrix

| Module / Feature | Admin | Manager | Technician |
|-----------------|-------|---------|-----------|
| Dashboard | ✅ | ✅ | ✅ |
| Assets (CRUD) | ✅ | ✅ | ✅ |
| Categories (CRUD) | ✅ | ✅ | ✅ |
| Subcategories (CRUD) | ✅ | ✅ | ✅ |
| Vendors (CRUD) | ✅ | ✅ | ✅ |
| Floors (CRUD) | ✅ | ✅ | ✅ |
| Departments (CRUD) | ✅ | ✅ | ✅ |
| Locations (CRUD) | ✅ | ✅ | ✅ |
| Assignments (CRUD) | ✅ | ✅ | ✅ |
| Transfers (Create/View) | ✅ | ✅ | ✅ |
| Maintenance Schedule (CRUD) | ✅ | ✅ | ✅ |
| Maintenance Logs (CRUD) | ✅ | ✅ | ✅ |
| Calibration (CRUD) | ✅ | ✅ | ✅ |
| Disposal (CRUD) | ✅ | ✅ | ✅ |
| **User Management** | ✅ | ❌ | ❌ |

---

## 3. Managing Users (Admin Only)

**Path:** `Administration → Users`

The Users list page shows all registered accounts with their name, email, role, status, and creation date.

> ⚠️ This module is only visible in the sidebar when logged in as an **admin**. Non-admin users who attempt to access it directly are redirected by the auth middleware.

---

## 4. Adding a New User

**Path:** `Administration → Users → + Add User`

| Field | Required | Description |
|-------|----------|-------------|
| Name | ✅ | Full name of the user |
| Email | ✅ | Unique email address — used as the login username |
| Password | ✅ | Must be entered when creating a new account |
| Role | ✅ | `admin`, `manager`, or `technician` |
| Status | ✅ | `active` (can log in) or `inactive` (blocked) |

Passwords are hashed with `password_hash()` using PHP's default bcrypt algorithm. Plain-text passwords are never stored in the database.

---

## 5. Editing a User

**Path:** `Administration → Users → Edit (✏️)`

All fields from the creation form are editable. Leave the **Password** field blank to keep the current password unchanged. If you enter a new password, it will be hashed and saved.

### Common Use Cases

- **Promote a technician to manager:** Change the role dropdown and click Save.
- **Temporarily block a user:** Set status to `inactive` — the system will reject their login attempts.
- **Reset a password:** Enter the new password in the password field and save.

---

## 6. Deactivating a User

Setting a user's **Status** to `inactive` prevents them from logging in without permanently deleting their account or their audit history. This is the recommended approach when:

- An employee is on extended leave.
- An employee's account is under review.
- A contractor's access needs to be suspended.

**Steps:**

1. Go to `Administration → Users`.
2. Click ✏️ **Edit** next to the user.
3. Change **Status** from `active` to `inactive`.
4. Click **Save**.

The user will be blocked immediately. Their historical records (maintenance logs, transfers, etc.) are preserved.

---

## 7. Deleting a User

**Path:** `Administration → Users → Delete (🗑️)`

Permanently removes the user account. Use with caution — deletion cannot be undone and may orphan records that reference the user.

> **Recommendation:** Use [deactivation](#6-deactivating-a-user) instead of deletion for most cases.

---

## 8. Changing Your Own Password

Currently there is no dedicated "change my password" profile page. An admin must update the password for any account via the **Edit User** page.

**Workaround for non-admin users:**

1. Contact your system administrator.
2. The admin navigates to `Administration → Users → Edit [your name]`, enters a new password, and saves.

---

## 9. Security Best Practices

- **Change the default admin password** (`admin123`) immediately after first login.
- **Use strong passwords** — at least 12 characters combining upper/lowercase, numbers, and symbols.
- **Create individual accounts** for each staff member — do not share login credentials.
- **Set inactive users to `inactive` status** when they leave the organization.
- **Limit admin accounts** — assign the `admin` role only to staff responsible for system administration. Day-to-day users should be `manager` or `technician`.
- **Log out** after each session, especially on shared workstations.
- **Never share your password** with colleagues — each person should have their own login.
