# Departments & Locations Guide

The system uses a three-level location hierarchy to describe where each asset is physically located:

```
Floor (building / floor level)
└── Department (e.g., Radiology, ICU)
    └── Location / Room (e.g., Room 204, Server Room)
```

You must set up these three levels **before** assigning assets to locations.

---

## Table of Contents

1. [Floors](#1-floors)
2. [Departments](#2-departments)
3. [Locations (Rooms)](#3-locations-rooms)
4. [Relationship Summary](#4-relationship-summary)
5. [Recommended Setup Order](#5-recommended-setup-order)

---

## 1. Floors

**Path:** `Locations → Floors`

Floors represent physical building levels or buildings themselves. They are the top of the location hierarchy.

### Floor Fields

| Field | Required | Description |
|-------|----------|-------------|
| Floor Name | ✅ | Descriptive name (e.g., "Ground Floor", "Building A – Level 2") |
| Floor Code | ✅ | Short unique code used in reports (e.g., `GF`, `A2`) |
| Building | — | Optional building name if the hospital has multiple buildings |

### Managing Floors

- **Add:** Click **+ Add Floor**, fill in the form, click **Save**.
- **Edit:** Click the ✏️ icon next to a floor to update its details.
- **Delete:** Click 🗑️ to delete. You cannot delete a floor that has departments assigned to it.

---

## 2. Departments

**Path:** `Locations → Departments`

Departments represent organisational or functional units within the hospital (e.g., ICU, Radiology, Pharmacy). Each department belongs to exactly one floor.

### Department Fields

| Field | Required | Description |
|-------|----------|-------------|
| Department Name | ✅ | Full name (e.g., "Intensive Care Unit") |
| Department Code | ✅ | Short code for reports (e.g., `ICU`) |
| Floor | ✅ | Which floor this department is on (dropdown) |
| Description | — | Optional notes about the department |
| Status | ✅ | `active` or `inactive` |

### Managing Departments

- **Add:** Click **+ Add Department**, select the parent floor, fill in the form, click **Save**.
- **Edit:** Click ✏️ to change the floor assignment or any other field.
- **Delete:** Click 🗑️ to delete. Departments linked to active locations or asset assignments cannot be deleted.

> **Tip:** Use consistent department codes throughout the system — they appear in transfer history reports and maintenance records.

---

## 3. Locations (Rooms)

**Path:** `Locations → Locations`

Locations are the most specific level — individual rooms, labs, corridors, or storage areas. Each location belongs to a floor and optionally to a department.

### Location Fields

| Field | Required | Description |
|-------|----------|-------------|
| Floor | ✅ | Which floor this room is on (dropdown) |
| Department | ✅ | Which department this room belongs to (dropdown, filtered by floor) |
| Room Number | ✅ | Official room identifier (e.g., `204`, `OR-1`, `Lab-B`) |
| Location Name | ✅ | Descriptive name (e.g., "Operating Room 1", "Chemistry Lab") |
| Description | — | Optional notes (capacity, special notes, etc.) |

### Managing Locations

- **Add:** Click **+ Add Location**, select floor and department, fill in the room details, click **Save**.
- **Edit:** Click ✏️ to update any field, including re-assigning to a different department.
- **Delete:** Click 🗑️ to delete. Locations linked to active asset assignments cannot be deleted.

---

## 4. Relationship Summary

| Level | Table | References |
|-------|-------|-----------|
| Floor | `floors` | — (top of hierarchy) |
| Department | `departments` | `floors.id` |
| Location/Room | `locations` | `floors.id`, `departments.id` |
| Asset Assignment | `asset_assignments` | `assets.id`, `floors.id`, `departments.id`, `locations.id` |

The location hierarchy is also used in:
- **Transfer history** — records from/to department and from/to location.
- **Maintenance schedules** — the `responsible_department` field references a department.

---

## 5. Recommended Setup Order

```
1. Create Floors first
   (example: Ground Floor, First Floor, Second Floor)

2. Create Departments next
   (each must reference an existing floor)

3. Create Locations last
   (each must reference an existing floor and department)
```

Attempting to create a department without any floors in the system, or a location without any departments, will result in empty dropdowns and the form cannot be saved correctly.

### Example Setup

```
Floor: Ground Floor  (GF)
├── Department: Emergency  (ER)
│   ├── Location: Triage Room  (Room: ER-01)
│   └── Location: Resuscitation Bay  (Room: ER-02)
│
├── Department: Pharmacy  (PH)
│   └── Location: Dispensary  (Room: PH-01)

Floor: First Floor  (1F)
├── Department: Radiology  (RAD)
│   ├── Location: X-Ray Suite  (Room: RAD-01)
│   └── Location: MRI Suite  (Room: RAD-02)
│
└── Department: ICU  (ICU)
    ├── Location: ICU Bed Area  (Room: ICU-01)
    └── Location: ICU Nurses Station  (Room: ICU-02)
```
