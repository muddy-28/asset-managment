-- Migration: Add activity_logs table for user activity tracking
-- Run this script to add activity logging to the Hospital Asset Management System

CREATE TABLE IF NOT EXISTS activity_logs (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    user_id     INT          NULL,
    user_name   VARCHAR(150) NULL,
    action_type VARCHAR(50)  NOT NULL,
    module      VARCHAR(100) NOT NULL,
    record_id   INT          NULL,
    description TEXT         NULL,
    ip_address  VARCHAR(45)  NULL,
    duration_ms INT          NULL,
    created_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE INDEX idx_activity_logs_user    ON activity_logs (user_id);
CREATE INDEX idx_activity_logs_module  ON activity_logs (module);
CREATE INDEX idx_activity_logs_created ON activity_logs (created_at);
