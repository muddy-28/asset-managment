-- =====================================================
-- Soft Delete Migration
-- Adds deleted_at column to all module tables
-- Run this script against the hospital_assets database
-- =====================================================

USE hospital_assets;

ALTER TABLE asset_categories        ADD COLUMN deleted_at TIMESTAMP NULL DEFAULT NULL;
ALTER TABLE asset_subcategories     ADD COLUMN deleted_at TIMESTAMP NULL DEFAULT NULL;
ALTER TABLE vendors                  ADD COLUMN deleted_at TIMESTAMP NULL DEFAULT NULL;
ALTER TABLE floors                   ADD COLUMN deleted_at TIMESTAMP NULL DEFAULT NULL;
ALTER TABLE departments              ADD COLUMN deleted_at TIMESTAMP NULL DEFAULT NULL;
ALTER TABLE locations                ADD COLUMN deleted_at TIMESTAMP NULL DEFAULT NULL;
ALTER TABLE assets                   ADD COLUMN deleted_at TIMESTAMP NULL DEFAULT NULL;
ALTER TABLE asset_assignments        ADD COLUMN deleted_at TIMESTAMP NULL DEFAULT NULL;
ALTER TABLE asset_maintenance_schedule ADD COLUMN deleted_at TIMESTAMP NULL DEFAULT NULL;
ALTER TABLE asset_maintenance_logs   ADD COLUMN deleted_at TIMESTAMP NULL DEFAULT NULL;
ALTER TABLE asset_calibration        ADD COLUMN deleted_at TIMESTAMP NULL DEFAULT NULL;
ALTER TABLE asset_disposal           ADD COLUMN deleted_at TIMESTAMP NULL DEFAULT NULL;
ALTER TABLE asset_transfer_history   ADD COLUMN deleted_at TIMESTAMP NULL DEFAULT NULL;
ALTER TABLE users                    ADD COLUMN deleted_at TIMESTAMP NULL DEFAULT NULL;
