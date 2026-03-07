# Maintenance Guide

This guide covers all maintenance-related features: scheduling recurring maintenance, logging completed work, recording calibrations, and monitoring upcoming reminders.

---

## Table of Contents

1. [Maintenance Schedule](#1-maintenance-schedule)
2. [Maintenance Logs](#2-maintenance-logs)
3. [Calibration Records](#3-calibration-records)
4. [Maintenance Reminders](#4-maintenance-reminders)
5. [Maintenance Workflow](#5-maintenance-workflow)

---

## 1. Maintenance Schedule

**Path:** `Maintenance → Schedule`

The Maintenance Schedule module lets you define **recurring maintenance plans** for each asset. The system automatically calculates the next due date based on the frequency you set.

### Schedule Fields

| Field | Required | Description |
|-------|----------|-------------|
| Asset | ✅ | Asset to schedule maintenance for (dropdown) |
| Maintenance Type | ✅ | `preventive`, `calibration`, `inspection`, or `cleaning` |
| Frequency Value | ✅ | Numeric value (e.g., `3`) |
| Frequency Unit | ✅ | `days`, `weeks`, `months`, or `years` |
| Last Maintenance Date | ✅ | Date maintenance was last performed |
| Reminder Days Before | — | How many days in advance to show a reminder (default: 7) |
| Responsible Department | — | Department responsible for this maintenance |
| Responsible Person | — | Name of the person responsible |
| Status | ✅ | `active` or `inactive` |

### How Next Due Date Is Calculated

The system automatically calculates `next_due_date` from `last_maintenance_date + frequency`:

| Frequency Setting | Last Maintenance | Next Due |
|-------------------|-----------------|---------|
| 3 months | 2024-01-15 | 2024-04-15 |
| 2 weeks | 2024-03-01 | 2024-03-15 |
| 1 year | 2024-06-01 | 2025-06-01 |
| 30 days | 2024-02-28 | 2024-03-29 |

### Managing Schedules

- **Add:** Click **+ Add Schedule**, fill in the form, click **Save**. The next due date is computed automatically.
- **Edit:** Click ✏️ to update the schedule. Changing the last maintenance date will recalculate the next due date.
- **Delete:** Click 🗑️ to remove a schedule. This does not delete historical maintenance logs.

---

## 2. Maintenance Logs

**Path:** `Maintenance → Logs`

Maintenance Logs record every individual maintenance event — whether scheduled or unscheduled. Use this module to document who performed the work, what was done, and what it cost.

### Log Fields

| Field | Required | Description |
|-------|----------|-------------|
| Asset | ✅ | Which asset was maintained |
| Schedule | — | Link to an existing maintenance schedule (optional for ad-hoc work) |
| Maintenance Date | ✅ | Date work was performed |
| Maintenance Type | ✅ | `preventive`, `corrective`, `calibration`, `inspection`, or `cleaning` |
| Technician Name | ✅ | Name of the person who performed the maintenance |
| Vendor | — | External service vendor (if outsourced) |
| Issue Reported | — | Problem description that triggered the maintenance |
| Work Performed | ✅ | Detailed description of work done |
| Parts Replaced | — | List of parts used or replaced |
| Maintenance Cost | — | Total cost (labour + parts) |
| Downtime Hours | — | How long the asset was out of service |
| Next Due Date | — | Override the calculated next due date if needed |
| Attachment | — | File attachment (certificate, invoice, report) |
| Remarks | — | Additional notes |

### Managing Logs

- **Add:** Click **+ Add Log**, fill in the form, click **Save**.
- **Edit:** Click ✏️ to correct or update a log entry.
- **Delete:** Click 🗑️ to permanently remove a log.

> **Best Practice:** After completing a scheduled maintenance task, add a log entry *and* update the maintenance schedule's **Last Maintenance Date** so the next due date recalculates correctly.

---

## 3. Calibration Records

**Path:** `Maintenance → Calibration`

The Calibration module tracks when regulated assets (e.g., weighing scales, blood pressure monitors, analytical instruments) were calibrated and when the next calibration is due.

### Calibration Fields

| Field | Required | Description |
|-------|----------|-------------|
| Asset | ✅ | Asset that was calibrated |
| Calibration Date | ✅ | Date calibration was performed |
| Calibration Due | ✅ | Date next calibration is due |
| Certificate Number | — | Reference number on the calibration certificate |
| Vendor | — | Calibration service provider |
| Remarks | — | Notes (accreditation body, pass/fail result, etc.) |

### Managing Calibration Records

- **Add:** Click **+ Add Calibration**, fill in the form, click **Save**.
- **Edit:** Click ✏️ to update the record (e.g., after receiving a corrected certificate).
- **Delete:** Click 🗑️ to remove a record.

> **Tip:** Monitor the **Calibration Due** dates regularly to avoid operating out-of-calibration equipment. Consider adding these to your maintenance schedule as well to get automatic reminders.

---

## 4. Maintenance Reminders

**Path:** `Maintenance → Schedule → Reminders`

The Reminders page shows all maintenance schedules whose `next_due_date` falls **within the next 7 days**. It is a quick read-only view — no editing is done here.

### What Is Shown

| Column | Description |
|--------|-------------|
| Asset | Asset name and tag |
| Maintenance Type | Type of maintenance due |
| Next Due Date | Exact date due |
| Days Until Due | How many days remain |
| Responsible Person | Who should perform it |
| Responsible Department | Which department is responsible |

### Recommended Use

- Check the Reminders page every morning as part of a daily workflow.
- Assign upcoming tasks to technicians.
- After completing a task, add a Maintenance Log and update the Schedule.

---

## 5. Maintenance Workflow

The recommended end-to-end process for recurring maintenance:

```
1. Set up a Maintenance Schedule for the asset
   (Maintenance → Schedule → + Add Schedule)

2. Monitor Reminders daily or weekly
   (Maintenance → Schedule → Reminders)

3. Perform the maintenance work

4. Record the completed work in Maintenance Logs
   (Maintenance → Logs → + Add Log)

5. Update the Schedule's "Last Maintenance Date" to today
   (Maintenance → Schedule → Edit)
   → The system recalculates "Next Due Date" automatically

6. Repeat from step 2
```

### Unscheduled / Ad-hoc Maintenance

If an asset breaks down unexpectedly:

1. Log the incident in **Maintenance Logs** without linking it to a schedule.
2. Set the asset **Status** to `maintenance` in the Assets module.
3. After repair, update the asset status back to `active`.
4. Optionally create or update the maintenance schedule if a recurring pattern is identified.
