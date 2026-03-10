-- =====================================================
-- Soft Delete Migration
-- Adds is_deleted column to all module tables
-- Run this script against the hospital_assets database
-- =====================================================

USE hospital_assets;

ALTER TABLE asset_categories        ADD COLUMN is_deleted TINYINT(1) DEFAULT 0;
ALTER TABLE asset_subcategories     ADD COLUMN is_deleted TINYINT(1) DEFAULT 0;
ALTER TABLE vendors                  ADD COLUMN is_deleted TINYINT(1) DEFAULT 0;
ALTER TABLE floors                   ADD COLUMN is_deleted TINYINT(1) DEFAULT 0;
ALTER TABLE departments              ADD COLUMN is_deleted TINYINT(1) DEFAULT 0;
ALTER TABLE locations                ADD COLUMN is_deleted TINYINT(1) DEFAULT 0;
ALTER TABLE assets                   ADD COLUMN is_deleted TINYINT(1) DEFAULT 0;
ALTER TABLE asset_assignments        ADD COLUMN is_deleted TINYINT(1) DEFAULT 0;
ALTER TABLE asset_maintenance_schedule ADD COLUMN is_deleted TINYINT(1) DEFAULT 0;
ALTER TABLE asset_maintenance_logs   ADD COLUMN is_deleted TINYINT(1) DEFAULT 0;
ALTER TABLE asset_calibration        ADD COLUMN is_deleted TINYINT(1) DEFAULT 0;
ALTER TABLE asset_disposal           ADD COLUMN is_deleted TINYINT(1) DEFAULT 0;
ALTER TABLE asset_transfer_history   ADD COLUMN is_deleted TINYINT(1) DEFAULT 0;
ALTER TABLE users                    ADD COLUMN is_deleted TINYINT(1) DEFAULT 0;
