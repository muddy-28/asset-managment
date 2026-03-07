# User Guide

This guide walks you through the day-to-day use of the Hospital Asset Management System, covering login, the dashboard, and how each section of the application works.

---

## Table of Contents

1. [Logging In](#1-logging-in)
2. [The Dashboard](#2-the-dashboard)
3. [Navigating the Sidebar](#3-navigating-the-sidebar)
4. [Working with Lists (DataTables)](#4-working-with-lists-datatables)
5. [Adding and Editing Records](#5-adding-and-editing-records)
6. [Deleting Records](#6-deleting-records)
7. [Printing Barcode Labels](#7-printing-barcode-labels)
8. [Recommended Workflow for New Setups](#8-recommended-workflow-for-new-setups)
9. [Logging Out](#9-logging-out)

---

## 1. Logging In

1. Open your browser and navigate to `http://localhost/asset-managment/` (or the URL provided by your administrator).
2. You will be automatically redirected to the login page.
3. Enter your **email address** and **password**.
4. Click **Login**.

If your credentials are correct you will be taken to the **Dashboard**. If the login fails, a SweetAlert2 dialog will show an error message.

> **Default credentials (change after first login):**  
> Email: `admin@hospital.com`  
> Password: `admin123`

---

## 2. The Dashboard

The dashboard is the home screen of the application. It provides a live overview of the asset inventory.

### Statistics Cards

Nine summary cards are displayed at the top of the dashboard:

| Card | Description |
|------|-------------|
| Total Assets | Count of all registered assets |
| Active Assets | Assets with status = *active* |
| In Maintenance | Assets currently in maintenance |
| Disposed Assets | Assets that have been disposed |
| Departments | Number of departments configured |
| Locations | Number of rooms/locations |
| Vendors | Number of registered vendors |
| Pending Maintenance | Maintenance schedules due soon |
| Users | Total system users (admin view) |

### Charts

Two interactive Chart.js charts are displayed below the statistics:

- **Asset Status Distribution** — Pie/doughnut chart showing the breakdown of assets by status (active, maintenance, disposed, lost).
- **Assets by Category** — Bar chart showing how many assets fall into each category.

Hovering over chart segments shows exact counts and percentages.

---

## 3. Navigating the Sidebar

The collapsible left sidebar groups all modules into logical sections:

| Section | Links |
|---------|-------|
| **Dashboard** | Home |
| **Asset Management** | Assets, Categories, Subcategories, Vendors |
| **Locations** | Floors, Departments, Locations |
| **Operations** | Assignments, Transfers |
| **Maintenance** | Schedule, Logs, Calibration, Disposal |
| **Administration** | Users (admin only), Logout |

On small screens (mobile / tablet) the sidebar collapses. Tap the ☰ **hamburger icon** in the top-left of the navbar to toggle it.

The currently active page is highlighted in the sidebar so you always know where you are.

---

## 4. Working with Lists (DataTables)

Every index (list) page uses **DataTables** to display records in a sortable, searchable, and paginated table.

### Common Controls

| Control | Location | Purpose |
|---------|----------|---------|
| **Search box** | Top-right of table | Filter rows in real time by any column |
| **Column headers** | Table header row | Click to sort ascending/descending |
| **Show entries** | Top-left of table | Change rows per page (10, 25, 50, 100) |
| **Pagination** | Bottom-right | Navigate between pages |
| **Action buttons** | Last column | View / Edit / Delete for each row |

---

## 5. Adding and Editing Records

All modules follow the same pattern for creating and updating records.

### Adding a New Record

1. Click the **+ Add [Record Name]** button (top-right or top of the list page).
2. Fill in the form fields. Required fields are marked with a red asterisk (`*`).
3. Click **Save** (or **Submit**).
4. A success notification will appear and you will be redirected back to the list.

### Editing an Existing Record

1. Find the record in the list.
2. Click the ✏️ **Edit** button in the row's action column.
3. The form pre-populates with the existing values.
4. Make your changes and click **Save**.

### Form Validation

- Client-side: HTML5 `required` attributes prevent empty submissions.
- Server-side: All inputs are validated and sanitized before writing to the database.
- Error messages are shown as SweetAlert2 dialogs or inline alerts.

---

## 6. Deleting Records

1. Find the record in the list.
2. Click the 🗑️ **Delete** button in the row's action column.
3. A SweetAlert2 confirmation dialog will appear: *"Are you sure you want to delete this record?"*
4. Click **Yes, delete it** to confirm or **Cancel** to abort.

> ⚠️ **Deletion is permanent.** Some records (e.g., assets with linked assignments) may not be deletable until related records are removed first.

---

## 7. Printing Barcode Labels

Each asset has a printable barcode label page.

1. Go to **Asset Management → Assets**.
2. Find the asset in the list.
3. Click the 👁️ **View** button to open the asset detail page.
4. Click **Print Barcode Label** (or navigate directly to `modules/assets/barcode.php?id=<asset_id>`).
5. A print-optimized page will open. Use your browser's print function (Ctrl+P / Cmd+P) to print.

The label includes:
- Asset name and asset tag
- Barcode (generated from the asset tag)
- Model number and serial number
- Category and department

---

## 8. Recommended Workflow for New Setups

Follow this sequence when setting up the system for the first time to avoid foreign-key dependency errors:

```
Step 1  →  Create Floors          (Locations → Floors)
Step 2  →  Create Departments     (Locations → Departments)
Step 3  →  Create Locations/Rooms (Locations → Locations)
Step 4  →  Create Categories      (Asset Management → Categories)
Step 5  →  Create Subcategories   (Asset Management → Subcategories)
Step 6  →  Create Vendors         (Asset Management → Vendors)
Step 7  →  Add Assets             (Asset Management → Assets)
Step 8  →  Assign Assets          (Operations → Assignments)
Step 9  →  Set Up Maintenance     (Maintenance → Schedule)
Step 10 →  Create Additional Users (Administration → Users)
```

> **Why this order?** Assets reference categories, subcategories, and vendors. Assignments reference assets, floors, departments, and locations. Setting up the supporting data first prevents "not found" errors in dropdowns.

---

## 9. Logging Out

- Click your **username** in the top-right navbar to open the user dropdown.
- Click **Logout**.
- Or navigate directly to `auth/logout.php`.

Your session will be destroyed and you will be redirected to the login page.

> For security, always log out when using a shared computer.
