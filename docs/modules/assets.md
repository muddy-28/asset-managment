# Assets Module Guide

The **Assets** module is the core of the system. It stores every physical item the hospital owns, tracks its lifecycle from purchase to disposal, and provides barcode labels for easy scanning.

---

## Table of Contents

1. [What Is an Asset?](#1-what-is-an-asset)
2. [Asset List](#2-asset-list)
3. [Adding a New Asset](#3-adding-a-new-asset)
4. [Viewing Asset Details](#4-viewing-asset-details)
5. [Editing an Asset](#5-editing-an-asset)
6. [Deleting an Asset](#6-deleting-an-asset)
7. [Assigning an Asset to a Location](#7-assigning-an-asset-to-a-location)
8. [Transferring an Asset](#8-transferring-an-asset)
9. [Printing a Barcode Label](#9-printing-a-barcode-label)
10. [Categories and Subcategories](#10-categories-and-subcategories)
11. [Vendors](#11-vendors)
12. [Asset Disposal](#12-asset-disposal)

---

## 1. What Is an Asset?

An asset is any physical item tracked by the hospital — for example:

- Medical equipment (ventilator, X-ray machine, infusion pump)
- IT hardware (desktop PC, printer, server)
- Furniture (bed, chair, cabinet)
- Facilities equipment (air conditioner, generator)

Each asset record holds:
- Identity information (name, tag, model, serial number)
- Purchase and warranty details
- Category and vendor links
- Current status and condition

---

## 2. Asset List

**Path:** `Asset Management → Assets`

The list page shows all assets in a DataTables table with the following columns:

| Column | Description |
|--------|-------------|
| # | Row number |
| Asset Tag | Unique identifier printed on the barcode label |
| Asset Name | Descriptive name |
| Category | Asset category |
| Condition | `new`, `good`, `fair`, `poor`, or `damaged` |
| Status | `active`, `maintenance`, `disposed`, or `lost` |
| Purchase Date | Date of acquisition |
| Actions | View / Edit / Delete buttons |

Use the **Search** box at the top right to filter by any column instantly.

---

## 3. Adding a New Asset

**Path:** `Asset Management → Assets → + Add Asset`

Fill in the following form fields:

| Field | Required | Description |
|-------|----------|-------------|
| Asset Name | ✅ | Descriptive name (e.g., "Infusion Pump Model X") |
| Asset Tag | ✅ | Unique alphanumeric identifier (e.g., `MED-0042`) |
| Model Number | ✅ | Manufacturer model number |
| Serial Number | ✅ | Manufacturer serial number |
| Barcode | ✅ | Value encoded in the barcode (often same as Asset Tag) |
| Category | ✅ | Select from the category dropdown |
| Subcategory | ✅ | Select from the subcategory dropdown (filtered by category) |
| Vendor | ✅ | Select the supplier/vendor |
| Purchase Date | ✅ | Date the asset was acquired |
| Purchase Cost | ✅ | Acquisition cost |
| Warranty Expiry | — | Date the warranty expires (leave blank if unknown) |
| Asset Condition | ✅ | Current physical condition |
| Status | ✅ | Current lifecycle status |

Click **Save Asset** to create the record.

> **Tip:** Asset tags must be unique across all assets. Consider a naming convention like `[CATEGORY_CODE]-[SEQUENCE]`, e.g., `MEDICAL-0001`.

---

## 4. Viewing Asset Details

**Path:** `Asset Management → Assets → View (👁️)`

The asset detail page displays all stored information in a structured layout and provides two action buttons:

- **Edit** — opens the edit form
- **Print Barcode Label** — opens the print-ready barcode page

---

## 5. Editing an Asset

**Path:** `Asset Management → Assets → Edit (✏️)`

All fields from the creation form are pre-populated. Update any field and click **Update Asset**.

> **Note:** Changing the `Status` to `disposed` does **not** automatically create a disposal record. Use the [Disposal module](#12-asset-disposal) for that.

---

## 6. Deleting an Asset

**Path:** `Asset Management → Assets → Delete (🗑️)`

A confirmation dialog appears. If you confirm:

- The asset record is permanently removed.
- Related assignment and transfer records may also be removed (cascade delete).

> ⚠️ Consider setting the status to `disposed` or `lost` instead of deleting, so the history is preserved.

---

## 7. Assigning an Asset to a Location

**Path:** `Operations → Assignments → + New Assignment`

Assignments link an asset to a specific floor, department, and room.

| Field | Description |
|-------|-------------|
| Asset | The asset to assign |
| Floor | Which floor the asset will be on |
| Department | Which department on that floor |
| Location / Room | The specific room |
| Assigned Date | Date the assignment takes effect |
| Assigned By | Name of the person making the assignment |

Only one active assignment per asset is expected. Creating a new assignment for an already-assigned asset marks the previous assignment as `moved` (a soft reassignment — use [Transfers](#8-transferring-an-asset) to generate a formal transfer history record).

---

## 8. Transferring an Asset

**Path:** `Operations → Transfers → + New Transfer`

Use transfers when an asset is physically moved from one department or room to another. A transfer creates an audit-trail record.

| Field | Description |
|-------|-------------|
| Asset | The asset being moved |
| From Department / Location | Current location (filled automatically if assigned) |
| To Department / Location | Destination |
| Transfer Date | Date of the move |
| Transferred By | Name of the person authorizing the move |
| Remarks | Optional notes (reason for transfer, etc.) |

After saving, the transfer history is visible on the **Transfers** list page.

---

## 9. Printing a Barcode Label

**Path:** `Asset Management → Assets → View → Print Barcode Label`

The barcode label page is optimized for printing on standard label paper (A4 or letter). It displays:

- Asset name and tag
- Barcode (using the barcode field value)
- Model and serial number
- Category

**Steps to print:**

1. Open the asset's **View** page.
2. Click **Print Barcode Label**.
3. Press **Ctrl+P** (Windows/Linux) or **Cmd+P** (macOS).
4. Set margins to **none/minimum** in the print dialog.
5. Click **Print**.

---

## 10. Categories and Subcategories

Assets are organized in a two-level hierarchy:

```
Category  (e.g., "Medical Equipment")
└── Subcategory  (e.g., "Diagnostic Devices")
```

### Managing Categories

**Path:** `Asset Management → Categories`

| Field | Description |
|-------|-------------|
| Category Name | Short name (e.g., "IT Hardware") |
| Description | Optional longer description |

### Managing Subcategories

**Path:** `Asset Management → Subcategories`

| Field | Description |
|-------|-------------|
| Category | Parent category (dropdown) |
| Subcategory Name | Short name (e.g., "Laptops") |
| Description | Optional longer description |

> Create categories before subcategories, since subcategories require a parent category to be selected.

---

## 11. Vendors

**Path:** `Asset Management → Vendors`

Vendors are the suppliers or service providers associated with assets.

| Field | Description |
|-------|-------------|
| Vendor Name | Company name |
| Contact Person | Primary contact at the vendor |
| Phone | Contact phone number |
| Email | Contact email address |
| Address | Physical or mailing address |

Vendors are referenced when:
- Creating an asset (purchase vendor)
- Logging maintenance work (external service vendor)
- Recording calibration (calibration vendor)

---

## 12. Asset Disposal

**Path:** `Maintenance → Disposal`

When an asset reaches end-of-life, record its disposal here.

| Field | Description |
|-------|-------------|
| Asset | The asset being disposed |
| Disposal Date | Date of disposal |
| Disposal Method | How it was disposed (sold, scrapped, donated, etc.) |
| Approved By | Name of the approving authority |
| Remarks | Additional notes |

> After creating a disposal record, manually update the asset's **Status** to `disposed` in the asset edit form to reflect the change on the dashboard.
