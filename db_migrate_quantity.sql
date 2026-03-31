-- =====================================================
-- Migration: Add quantity column to assets, assignments,
-- and transfer history tables
-- =====================================================

ALTER TABLE assets
    ADD COLUMN quantity INT NOT NULL DEFAULT 1
    AFTER warranty_expiry;

ALTER TABLE asset_assignments
    ADD COLUMN quantity INT NOT NULL DEFAULT 1
    AFTER assigned_by;

ALTER TABLE asset_transfer_history
    ADD COLUMN quantity INT NOT NULL DEFAULT 1
    AFTER transferred_by;
