-- =====================================================
-- Hospital Asset Management System Database
-- =====================================================

DROP DATABASE IF EXISTS hospital_assets;
CREATE DATABASE hospital_assets;
USE hospital_assets;

-- =====================================================
-- FLOORS
-- =====================================================

CREATE TABLE floors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    floor_name VARCHAR(100) NOT NULL,
    floor_code VARCHAR(20),
    building VARCHAR(150),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =====================================================
-- DEPARTMENTS
-- =====================================================

CREATE TABLE departments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    department_name VARCHAR(150) NOT NULL,
    department_code VARCHAR(50),
    floor_id INT,
    description TEXT,
    status ENUM('active','inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (floor_id) REFERENCES floors(id)
);

-- =====================================================
-- LOCATIONS / ROOMS
-- =====================================================

CREATE TABLE locations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    floor_id INT,
    department_id INT,
    room_number VARCHAR(50),
    location_name VARCHAR(150),
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (floor_id) REFERENCES floors(id),
    FOREIGN KEY (department_id) REFERENCES departments(id)
);

-- =====================================================
-- ASSET CATEGORIES
-- =====================================================

CREATE TABLE asset_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(150) NOT NULL,
    description TEXT
);

-- =====================================================
-- ASSET SUBCATEGORIES
-- =====================================================

CREATE TABLE asset_subcategories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    subcategory_name VARCHAR(150),
    description TEXT,
    FOREIGN KEY (category_id) REFERENCES asset_categories(id)
);

-- =====================================================
-- VENDORS / SUPPLIERS
-- =====================================================

CREATE TABLE vendors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    vendor_name VARCHAR(200),
    contact_person VARCHAR(150),
    phone VARCHAR(50),
    email VARCHAR(150),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =====================================================
-- ASSETS
-- =====================================================

CREATE TABLE assets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    asset_tag VARCHAR(100) UNIQUE,
    asset_name VARCHAR(200),
    model_number VARCHAR(100),
    serial_number VARCHAR(150),
    barcode VARCHAR(150),

    category_id INT,
    subcategory_id INT,

    purchase_date DATE,
    purchase_cost DECIMAL(12,2),

    vendor_id INT,

    warranty_expiry DATE,

    asset_condition ENUM('new','good','fair','poor','damaged'),
    status ENUM('active','maintenance','disposed','lost') DEFAULT 'active',

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (category_id) REFERENCES asset_categories(id),
    FOREIGN KEY (subcategory_id) REFERENCES asset_subcategories(id),
    FOREIGN KEY (vendor_id) REFERENCES vendors(id)
);

-- =====================================================
-- ASSET ASSIGNMENTS (Department / Floor / Room)
-- =====================================================

CREATE TABLE asset_assignments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    asset_id INT,
    floor_id INT,
    department_id INT,
    location_id INT,

    assigned_date DATE,
    assigned_by VARCHAR(150),

    status ENUM('active','moved') DEFAULT 'active',

    FOREIGN KEY (asset_id) REFERENCES assets(id),
    FOREIGN KEY (floor_id) REFERENCES floors(id),
    FOREIGN KEY (department_id) REFERENCES departments(id),
    FOREIGN KEY (location_id) REFERENCES locations(id)
);

-- =====================================================
-- ASSET TRANSFER HISTORY
-- =====================================================

CREATE TABLE asset_transfer_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    asset_id INT,

    from_department INT,
    to_department INT,

    from_location INT,
    to_location INT,

    transfer_date DATE,
    transferred_by VARCHAR(150),
    remarks TEXT,

    FOREIGN KEY (asset_id) REFERENCES assets(id)
);

-- =====================================================
-- MAINTENANCE SCHEDULE
-- =====================================================

CREATE TABLE asset_maintenance_schedule (
    id INT AUTO_INCREMENT PRIMARY KEY,

    asset_id INT,

    maintenance_type ENUM(
        'preventive',
        'calibration',
        'inspection',
        'cleaning'
    ) DEFAULT 'preventive',

    frequency_value INT,
    frequency_unit ENUM('days','weeks','months','years'),

    last_maintenance_date DATE,
    next_due_date DATE,

    reminder_days_before INT DEFAULT 7,

    responsible_department INT,
    responsible_person VARCHAR(150),

    status ENUM('active','inactive') DEFAULT 'active',

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (asset_id) REFERENCES assets(id)
);

-- =====================================================
-- MAINTENANCE LOGS
-- =====================================================

CREATE TABLE asset_maintenance_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,

    asset_id INT,
    schedule_id INT,

    maintenance_date DATE,

    maintenance_type ENUM(
        'preventive',
        'repair',
        'calibration',
        'inspection'
    ),

    technician_name VARCHAR(150),

    vendor_id INT,

    issue_reported TEXT,
    work_performed TEXT,

    parts_replaced TEXT,

    maintenance_cost DECIMAL(10,2),

    downtime_hours INT,

    next_due_date DATE,

    attachment VARCHAR(255),

    remarks TEXT,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (asset_id) REFERENCES assets(id),
    FOREIGN KEY (schedule_id) REFERENCES asset_maintenance_schedule(id)
);

-- =====================================================
-- CALIBRATION RECORDS
-- =====================================================

CREATE TABLE asset_calibration (
    id INT AUTO_INCREMENT PRIMARY KEY,

    asset_id INT,

    calibration_date DATE,
    calibration_due DATE,

    certificate_number VARCHAR(100),

    vendor_id INT,

    remarks TEXT,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (asset_id) REFERENCES assets(id),
    FOREIGN KEY (vendor_id) REFERENCES vendors(id)
);

-- =====================================================
-- MAINTENANCE REMINDERS
-- =====================================================

CREATE TABLE maintenance_reminders (
    id INT AUTO_INCREMENT PRIMARY KEY,

    schedule_id INT,
    asset_id INT,

    reminder_date DATE,

    notified ENUM('no','yes') DEFAULT 'no',

    notification_method ENUM('email','sms','system'),

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (schedule_id) REFERENCES asset_maintenance_schedule(id),
    FOREIGN KEY (asset_id) REFERENCES assets(id)
);

-- =====================================================
-- ASSET DISPOSAL
-- =====================================================

CREATE TABLE asset_disposal (
    id INT AUTO_INCREMENT PRIMARY KEY,

    asset_id INT,

    disposal_date DATE,

    disposal_method VARCHAR(150),

    remarks TEXT,

    approved_by VARCHAR(150),

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (asset_id) REFERENCES assets(id)
);

-- =====================================================
-- INDEXES FOR PERFORMANCE
-- =====================================================

CREATE INDEX idx_asset_tag ON assets(asset_tag);
CREATE INDEX idx_asset_category ON assets(category_id);
CREATE INDEX idx_asset_department ON asset_assignments(department_id);
CREATE INDEX idx_maintenance_due ON asset_maintenance_schedule(next_due_date);

-- =====================================================
-- USERS
-- =====================================================

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150),
    email VARCHAR(150) UNIQUE,
    password VARCHAR(255),
    role ENUM('admin','manager','technician') DEFAULT 'technician',
    status ENUM('active','inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default admin user (password: admin123)
INSERT INTO users (name, email, password, role, status) VALUES
('Administrator', 'admin@hospital.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'active');
