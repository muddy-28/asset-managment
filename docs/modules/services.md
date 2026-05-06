# Services Module Guide

The **Services** module lets administrators manage the catalogue of services offered by the hospital. Each service record holds a title, an optional description, and an optional image, making it easy to present service information both internally and on public-facing pages.

---

## Table of Contents

1. [What Is a Service?](#1-what-is-a-service)
2. [Services List](#2-services-list)
3. [Adding a New Service](#3-adding-a-new-service)
4. [Editing a Service](#4-editing-a-service)
5. [Deleting a Service](#5-deleting-a-service)
6. [Image Uploads](#6-image-uploads)

---

## 1. What Is a Service?

A service record represents any distinct offering or programme provided by the hospital — for example:

- Outpatient consultation
- Emergency care
- Laboratory testing
- Physiotherapy
- Radiology & imaging

Each service record holds:
- **Title** — a short, descriptive name
- **Description** — an optional multi-line explanation of what the service involves
- **Image** — an optional representative photo or icon

---

## 2. Services List

**Path:** `Asset Management → Services`

The list page shows all active services in a DataTables table with the following columns:

| Column | Description |
|--------|-------------|
| # | Row number |
| Image | Thumbnail of the service image (if uploaded) |
| Title | Name of the service |
| Description | First 80 characters of the description |
| Created | Date and time the record was created |
| Actions | Edit / Delete buttons |

Use the **Search** box at the top right to filter by any column instantly.

---

## 3. Adding a New Service

**Path:** `Asset Management → Services → + Add Service`

Fill in the following form fields:

| Field | Required | Description |
|-------|----------|-------------|
| Title | ✅ | Short name of the service (e.g., "Physiotherapy") |
| Description | — | Detailed explanation of the service (free text, any length) |
| Image | — | Representative photo or icon — see [Image Uploads](#6-image-uploads) |

Click **Save** to create the record.

---

## 4. Editing a Service

**Path:** `Asset Management → Services → Edit (✏️)`

All fields from the creation form are pre-populated. Update any field and click **Update**.

- To replace the current image, upload a new file in the **Image** field. The old file is deleted automatically.
- Leave the **Image** field empty to keep the existing image.

---

## 5. Deleting a Service

**Path:** `Asset Management → Services → Delete (🗑️)`

A SweetAlert2 confirmation dialog appears. If you confirm:

- The service record is **soft-deleted** (`is_deleted = 1`) and no longer appears in the list.
- The associated image file is permanently removed from the server.

> ⚠️ Soft deletion means the row remains in the database for audit purposes but is invisible in the UI.

---

## 6. Image Uploads

Images are stored under:

```
assets/uploads/services/
```

**Accepted formats:** `jpg`, `jpeg`, `png`, `webp`, `gif`

Filenames are generated automatically using the record ID and a timestamp to prevent collisions (e.g., `service_42_1714000000.jpg`).

> **Tip:** Keep images under 2 MB and use a consistent aspect ratio (e.g., 16:9 or 1:1) for a uniform appearance in lists and cards.
