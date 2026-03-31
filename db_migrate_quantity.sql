-- =====================================================
-- Migration: Add quantity column to assets table
-- =====================================================

ALTER TABLE assets
    ADD COLUMN quantity INT NOT NULL DEFAULT 1
    AFTER warranty_expiry;
