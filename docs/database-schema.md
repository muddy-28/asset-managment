# Database Schema Reference

This document describes all 15 tables in the `hospital_assets` database, their columns, data types, and relationships.

---

## Table of Contents

1. [Entity Relationship Overview](#1-entity-relationship-overview)
2. [users](#2-users)
3. [floors](#3-floors)
4. [departments](#4-departments)
5. [locations](#5-locations)
6. [asset_categories](#6-asset_categories)
7. [asset_subcategories](#7-asset_subcategories)
8. [vendors](#8-vendors)
9. [assets](#9-assets)
10. [asset_assignments](#10-asset_assignments)
11. [asset_transfer_history](#11-asset_transfer_history)
12. [asset_maintenance_schedule](#12-asset_maintenance_schedule)
13. [asset_maintenance_logs](#13-asset_maintenance_logs)
14. [asset_calibration](#14-asset_calibration)
15. [maintenance_reminders](#15-maintenance_reminders)
16. [asset_disposal](#16-asset_disposal)
17. [Indexes](#17-indexes)

---

## 1. Entity Relationship Overview

```
floors
  └── departments  (floor_id → floors.id)
        └── locations  (floor_id, department_id)

asset_categories
  └── asset_subcategories  (category_id → asset_categories.id)

vendors

assets  (category_id, subcategory_id, vendor_id)
  ├── asset_assignments  (asset_id, floor_id, department_id, location_id)
  ├── asset_transfer_history  (asset_id)
  ├── asset_maintenance_schedule  (asset_id)
  │     └── asset_maintenance_logs  (asset_id, schedule_id)
  │     └── maintenance_reminders   (schedule_id, asset_id)
  ├── asset_calibration  (asset_id, vendor_id)
  └── asset_disposal  (asset_id)

users  (standalone — no FK from other tables, but referenced by name in logs)
```

---

## 2. users

Stores system user accounts for authentication and role-based access control.

| Column | Type | Nullable | Default | Notes |
|--------|------|----------|---------|-------|
| `id` | INT UNSIGNED | No | AUTO_INCREMENT | Primary key |
| `name` | VARCHAR(100) | No | — | Full name |
| `email` | VARCHAR(150) | No | — | UNIQUE — login username |
| `password` | VARCHAR(255) | No | — | bcrypt hash |
| `role` | ENUM('admin','manager','technician') | No | 'technician' | Access level |
| `status` | ENUM('active','inactive') | No | 'active' | Login permission |
| `created_at` | TIMESTAMP | No | CURRENT_TIMESTAMP | Record creation time |

---

## 3. floors

Represents physical floors or buildings in the hospital.

| Column | Type | Nullable | Default | Notes |
|--------|------|----------|---------|-------|
| `id` | INT UNSIGNED | No | AUTO_INCREMENT | Primary key |
| `floor_name` | VARCHAR(100) | No | — | Descriptive name |
| `floor_code` | VARCHAR(20) | No | — | Short unique code |
| `building` | VARCHAR(100) | Yes | NULL | Optional building name |
| `created_at` | TIMESTAMP | No | CURRENT_TIMESTAMP | |

---

## 4. departments

Represents functional units within the hospital, each linked to a floor.

| Column | Type | Nullable | Default | Notes |
|--------|------|----------|---------|-------|
| `id` | INT UNSIGNED | No | AUTO_INCREMENT | Primary key |
| `department_name` | VARCHAR(100) | No | — | Full name |
| `department_code` | VARCHAR(20) | No | — | Short unique code |
| `floor_id` | INT UNSIGNED | No | — | FK → floors.id |
| `description` | TEXT | Yes | NULL | Optional description |
| `status` | ENUM('active','inactive') | No | 'active' | |
| `created_at` | TIMESTAMP | No | CURRENT_TIMESTAMP | |

---

## 5. locations

Rooms or specific spaces, linked to a floor and department.

| Column | Type | Nullable | Default | Notes |
|--------|------|----------|---------|-------|
| `id` | INT UNSIGNED | No | AUTO_INCREMENT | Primary key |
| `floor_id` | INT UNSIGNED | No | — | FK → floors.id |
| `department_id` | INT UNSIGNED | No | — | FK → departments.id |
| `room_number` | VARCHAR(20) | No | — | Official room identifier |
| `location_name` | VARCHAR(100) | No | — | Descriptive name |
| `description` | TEXT | Yes | NULL | Optional notes |
| `created_at` | TIMESTAMP | No | CURRENT_TIMESTAMP | |

---

## 6. asset_categories

Top-level classification of assets.

| Column | Type | Nullable | Default | Notes |
|--------|------|----------|---------|-------|
| `id` | INT UNSIGNED | No | AUTO_INCREMENT | Primary key |
| `category_name` | VARCHAR(100) | No | — | |
| `description` | TEXT | Yes | NULL | |

---

## 7. asset_subcategories

Second-level classification, each linked to a category.

| Column | Type | Nullable | Default | Notes |
|--------|------|----------|---------|-------|
| `id` | INT UNSIGNED | No | AUTO_INCREMENT | Primary key |
| `category_id` | INT UNSIGNED | No | — | FK → asset_categories.id |
| `subcategory_name` | VARCHAR(100) | No | — | |
| `description` | TEXT | Yes | NULL | |

---

## 8. vendors

Supplier and service provider information.

| Column | Type | Nullable | Default | Notes |
|--------|------|----------|---------|-------|
| `id` | INT UNSIGNED | No | AUTO_INCREMENT | Primary key |
| `vendor_name` | VARCHAR(100) | No | — | Company name |
| `contact_person` | VARCHAR(100) | Yes | NULL | Primary contact name |
| `phone` | VARCHAR(30) | Yes | NULL | |
| `email` | VARCHAR(150) | Yes | NULL | |
| `address` | TEXT | Yes | NULL | |
| `created_at` | TIMESTAMP | No | CURRENT_TIMESTAMP | |

---

## 9. assets

Core asset records — the heart of the system.

| Column | Type | Nullable | Default | Notes |
|--------|------|----------|---------|-------|
| `id` | INT UNSIGNED | No | AUTO_INCREMENT | Primary key |
| `asset_tag` | VARCHAR(50) | No | — | UNIQUE identifier |
| `asset_name` | VARCHAR(150) | No | — | Descriptive name |
| `model_number` | VARCHAR(100) | Yes | NULL | Manufacturer model |
| `serial_number` | VARCHAR(100) | Yes | NULL | Manufacturer serial |
| `barcode` | VARCHAR(100) | Yes | NULL | Barcode value (often = asset_tag) |
| `category_id` | INT UNSIGNED | Yes | NULL | FK → asset_categories.id |
| `subcategory_id` | INT UNSIGNED | Yes | NULL | FK → asset_subcategories.id |
| `purchase_date` | DATE | Yes | NULL | |
| `purchase_cost` | DECIMAL(12,2) | Yes | NULL | |
| `vendor_id` | INT UNSIGNED | Yes | NULL | FK → vendors.id |
| `warranty_expiry` | DATE | Yes | NULL | |
| `asset_condition` | ENUM('new','good','fair','poor','damaged') | No | 'good' | Physical condition |
| `status` | ENUM('active','maintenance','disposed','lost') | No | 'active' | Lifecycle status |
| `created_at` | TIMESTAMP | No | CURRENT_TIMESTAMP | |

---

## 10. asset_assignments

Links an asset to a specific floor, department, and location.

| Column | Type | Nullable | Default | Notes |
|--------|------|----------|---------|-------|
| `id` | INT UNSIGNED | No | AUTO_INCREMENT | Primary key |
| `asset_id` | INT UNSIGNED | No | — | FK → assets.id |
| `floor_id` | INT UNSIGNED | No | — | FK → floors.id |
| `department_id` | INT UNSIGNED | No | — | FK → departments.id |
| `location_id` | INT UNSIGNED | No | — | FK → locations.id |
| `assigned_date` | DATE | No | — | When assigned |
| `assigned_by` | VARCHAR(100) | Yes | NULL | Name of assigning person |
| `status` | ENUM('active','moved') | No | 'active' | `moved` when a transfer occurs |

---

## 11. asset_transfer_history

Audit trail of every asset movement between departments/locations.

| Column | Type | Nullable | Default | Notes |
|--------|------|----------|---------|-------|
| `id` | INT UNSIGNED | No | AUTO_INCREMENT | Primary key |
| `asset_id` | INT UNSIGNED | No | — | FK → assets.id |
| `from_department` | VARCHAR(100) | Yes | NULL | Department name at time of transfer |
| `to_department` | VARCHAR(100) | Yes | NULL | |
| `from_location` | VARCHAR(100) | Yes | NULL | Room name at time of transfer |
| `to_location` | VARCHAR(100) | Yes | NULL | |
| `transfer_date` | DATE | No | — | |
| `transferred_by` | VARCHAR(100) | Yes | NULL | |
| `remarks` | TEXT | Yes | NULL | |

---

## 12. asset_maintenance_schedule

Defines recurring maintenance plans for assets.

| Column | Type | Nullable | Default | Notes |
|--------|------|----------|---------|-------|
| `id` | INT UNSIGNED | No | AUTO_INCREMENT | Primary key |
| `asset_id` | INT UNSIGNED | No | — | FK → assets.id |
| `maintenance_type` | ENUM('preventive','calibration','inspection','cleaning') | No | 'preventive' | |
| `frequency_value` | INT | No | — | Numeric interval |
| `frequency_unit` | ENUM('days','weeks','months','years') | No | 'months' | |
| `last_maintenance_date` | DATE | Yes | NULL | |
| `next_due_date` | DATE | Yes | NULL | Computed automatically |
| `reminder_days_before` | INT | No | 7 | Days before due to show reminder |
| `responsible_department` | VARCHAR(100) | Yes | NULL | |
| `responsible_person` | VARCHAR(100) | Yes | NULL | |
| `status` | ENUM('active','inactive') | No | 'active' | |
| `created_at` | TIMESTAMP | No | CURRENT_TIMESTAMP | |

---

## 13. asset_maintenance_logs

Records each individual maintenance event.

| Column | Type | Nullable | Default | Notes |
|--------|------|----------|---------|-------|
| `id` | INT UNSIGNED | No | AUTO_INCREMENT | Primary key |
| `asset_id` | INT UNSIGNED | No | — | FK → assets.id |
| `schedule_id` | INT UNSIGNED | Yes | NULL | FK → asset_maintenance_schedule.id (nullable for ad-hoc) |
| `maintenance_date` | DATE | No | — | |
| `maintenance_type` | VARCHAR(50) | No | — | Free text or ENUM value |
| `technician_name` | VARCHAR(100) | Yes | NULL | |
| `vendor_id` | INT UNSIGNED | Yes | NULL | FK → vendors.id (external service) |
| `issue_reported` | TEXT | Yes | NULL | |
| `work_performed` | TEXT | Yes | NULL | |
| `parts_replaced` | TEXT | Yes | NULL | |
| `maintenance_cost` | DECIMAL(10,2) | Yes | NULL | |
| `downtime_hours` | DECIMAL(6,2) | Yes | NULL | |
| `next_due_date` | DATE | Yes | NULL | Override of schedule's next_due_date |
| `attachment` | VARCHAR(255) | Yes | NULL | File path (if applicable) |
| `remarks` | TEXT | Yes | NULL | |
| `created_at` | TIMESTAMP | No | CURRENT_TIMESTAMP | |

---

## 14. asset_calibration

Tracks calibration events for regulated assets.

| Column | Type | Nullable | Default | Notes |
|--------|------|----------|---------|-------|
| `id` | INT UNSIGNED | No | AUTO_INCREMENT | Primary key |
| `asset_id` | INT UNSIGNED | No | — | FK → assets.id |
| `calibration_date` | DATE | No | — | Date performed |
| `calibration_due` | DATE | Yes | NULL | Next calibration due date |
| `certificate_number` | VARCHAR(100) | Yes | NULL | Calibration certificate reference |
| `vendor_id` | INT UNSIGNED | Yes | NULL | FK → vendors.id (calibration provider) |
| `remarks` | TEXT | Yes | NULL | |
| `created_at` | TIMESTAMP | No | CURRENT_TIMESTAMP | |

---

## 15. maintenance_reminders

System-generated or manually created reminders linked to a maintenance schedule.

| Column | Type | Nullable | Default | Notes |
|--------|------|----------|---------|-------|
| `id` | INT UNSIGNED | No | AUTO_INCREMENT | Primary key |
| `schedule_id` | INT UNSIGNED | No | — | FK → asset_maintenance_schedule.id |
| `asset_id` | INT UNSIGNED | No | — | FK → assets.id (denormalized for fast queries) |
| `reminder_date` | DATE | No | — | Date the reminder fires |
| `notified` | ENUM('yes','no') | No | 'no' | Whether notification was sent |
| `notification_method` | ENUM('email','sms','system') | No | 'system' | How the reminder is delivered |
| `created_at` | TIMESTAMP | No | CURRENT_TIMESTAMP | |

---

## 16. asset_disposal

Records the end-of-life disposal of an asset.

| Column | Type | Nullable | Default | Notes |
|--------|------|----------|---------|-------|
| `id` | INT UNSIGNED | No | AUTO_INCREMENT | Primary key |
| `asset_id` | INT UNSIGNED | No | — | FK → assets.id |
| `disposal_date` | DATE | No | — | Date of disposal |
| `disposal_method` | VARCHAR(100) | Yes | NULL | E.g., "Sold", "Scrapped", "Donated" |
| `remarks` | TEXT | Yes | NULL | |
| `approved_by` | VARCHAR(100) | Yes | NULL | Authorizing person |
| `created_at` | TIMESTAMP | No | CURRENT_TIMESTAMP | |

---

## 17. Indexes

Performance indexes created on the most frequently queried columns:

| Index Name | Table | Column | Purpose |
|------------|-------|--------|---------|
| `idx_asset_tag` | `assets` | `asset_tag` | Fast lookup by unique tag |
| `idx_asset_category` | `assets` | `category_id` | Filter assets by category |
| `idx_asset_department` | `asset_assignments` | `department_id` | Filter assignments by department |
| `idx_maintenance_due` | `asset_maintenance_schedule` | `next_due_date` | Efficient reminders query |
