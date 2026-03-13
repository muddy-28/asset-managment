-- =====================================================
-- Hospital Asset Management System
-- Consolidated Deployment Script
-- =====================================================
-- This single file creates the full database schema
-- (incorporating all migrations) and loads reference
-- seed data.  Run it once on a fresh MySQL/MariaDB
-- instance:
--
--   mysql -u root -p < deploy.sql
--
-- All previous migration files are superseded by this
-- script:
--   • db_migrate_soft_delete.sql   (integrated below)
--   • db_migrate_activity_logs.sql (integrated below)
-- =====================================================

-- =====================================================
-- DATABASE
-- =====================================================

DROP DATABASE IF EXISTS hospital_assets;
CREATE DATABASE hospital_assets
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;
USE hospital_assets;

-- =====================================================
-- FLOORS
-- =====================================================

CREATE TABLE floors (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    floor_name VARCHAR(100) NOT NULL,
    floor_code VARCHAR(20),
    building   VARCHAR(150),
    is_deleted TINYINT(1)  DEFAULT 0,
    created_at TIMESTAMP   DEFAULT CURRENT_TIMESTAMP
);

-- =====================================================
-- DEPARTMENTS
-- =====================================================

CREATE TABLE departments (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    department_name VARCHAR(150) NOT NULL,
    department_code VARCHAR(50),
    floor_id        INT,
    description     TEXT,
    status          ENUM('active','inactive') DEFAULT 'active',
    is_deleted      TINYINT(1) DEFAULT 0,
    created_at      TIMESTAMP  DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (floor_id) REFERENCES floors(id)
);

-- =====================================================
-- LOCATIONS / ROOMS
-- =====================================================

CREATE TABLE locations (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    floor_id      INT,
    department_id INT,
    room_number   VARCHAR(50),
    location_name VARCHAR(150),
    description   TEXT,
    is_deleted    TINYINT(1) DEFAULT 0,
    created_at    TIMESTAMP  DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (floor_id)      REFERENCES floors(id),
    FOREIGN KEY (department_id) REFERENCES departments(id)
);

-- =====================================================
-- ASSET CATEGORIES
-- =====================================================

CREATE TABLE asset_categories (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(150) NOT NULL,
    description   TEXT,
    is_deleted    TINYINT(1) DEFAULT 0
);

-- =====================================================
-- ASSET SUBCATEGORIES
-- =====================================================

CREATE TABLE asset_subcategories (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    category_id      INT,
    subcategory_name VARCHAR(150),
    description      TEXT,
    is_deleted       TINYINT(1) DEFAULT 0,
    FOREIGN KEY (category_id) REFERENCES asset_categories(id)
);

-- =====================================================
-- VENDORS / SUPPLIERS
-- =====================================================

CREATE TABLE vendors (
    id             INT AUTO_INCREMENT PRIMARY KEY,
    vendor_name    VARCHAR(200),
    contact_person VARCHAR(150),
    phone          VARCHAR(50),
    email          VARCHAR(150),
    address        TEXT,
    is_deleted     TINYINT(1) DEFAULT 0,
    created_at     TIMESTAMP  DEFAULT CURRENT_TIMESTAMP
);

-- =====================================================
-- ASSETS
-- =====================================================

CREATE TABLE assets (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    asset_tag       VARCHAR(100) UNIQUE,
    asset_name      VARCHAR(200),
    model_number    VARCHAR(100),
    serial_number   VARCHAR(150),
    barcode         VARCHAR(150),
    category_id     INT,
    subcategory_id  INT,
    purchase_date   DATE,
    purchase_cost   DECIMAL(12,2),
    vendor_id       INT,
    warranty_expiry DATE,
    asset_condition ENUM('new','good','fair','poor','damaged'),
    status          ENUM('active','maintenance','disposed','lost') DEFAULT 'active',
    is_deleted      TINYINT(1) DEFAULT 0,
    created_at      TIMESTAMP  DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id)    REFERENCES asset_categories(id),
    FOREIGN KEY (subcategory_id) REFERENCES asset_subcategories(id),
    FOREIGN KEY (vendor_id)      REFERENCES vendors(id)
);

-- =====================================================
-- ASSET ASSIGNMENTS (Department / Floor / Room)
-- =====================================================

CREATE TABLE asset_assignments (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    asset_id      INT,
    floor_id      INT,
    department_id INT,
    location_id   INT,
    assigned_date DATE,
    assigned_by   VARCHAR(150),
    status        ENUM('active','moved') DEFAULT 'active',
    is_deleted    TINYINT(1) DEFAULT 0,
    FOREIGN KEY (asset_id)      REFERENCES assets(id),
    FOREIGN KEY (floor_id)      REFERENCES floors(id),
    FOREIGN KEY (department_id) REFERENCES departments(id),
    FOREIGN KEY (location_id)   REFERENCES locations(id)
);

-- =====================================================
-- ASSET TRANSFER HISTORY
-- =====================================================

CREATE TABLE asset_transfer_history (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    asset_id         INT,
    from_department  INT,
    to_department    INT,
    from_location    INT,
    to_location      INT,
    transfer_date    DATE,
    transferred_by   VARCHAR(150),
    remarks          TEXT,
    is_deleted       TINYINT(1) DEFAULT 0,
    FOREIGN KEY (asset_id)         REFERENCES assets(id),
    FOREIGN KEY (from_department)  REFERENCES departments(id),
    FOREIGN KEY (to_department)    REFERENCES departments(id),
    FOREIGN KEY (from_location)    REFERENCES locations(id),
    FOREIGN KEY (to_location)      REFERENCES locations(id)
);

-- =====================================================
-- MAINTENANCE SCHEDULE
-- =====================================================

CREATE TABLE asset_maintenance_schedule (
    id                     INT AUTO_INCREMENT PRIMARY KEY,
    asset_id               INT,
    maintenance_type       ENUM('preventive','calibration','inspection','cleaning') DEFAULT 'preventive',
    frequency_value        INT,
    frequency_unit         ENUM('days','weeks','months','years'),
    last_maintenance_date  DATE,
    next_due_date          DATE,
    reminder_days_before   INT DEFAULT 7,
    responsible_department INT,
    responsible_person     VARCHAR(150),
    status                 ENUM('active','inactive') DEFAULT 'active',
    is_deleted             TINYINT(1) DEFAULT 0,
    created_at             TIMESTAMP  DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (asset_id) REFERENCES assets(id)
);

-- =====================================================
-- MAINTENANCE LOGS
-- =====================================================

CREATE TABLE asset_maintenance_logs (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    asset_id         INT,
    schedule_id      INT,
    maintenance_date DATE,
    maintenance_type ENUM('preventive','repair','calibration','inspection'),
    technician_name  VARCHAR(150),
    vendor_id        INT,
    issue_reported   TEXT,
    work_performed   TEXT,
    parts_replaced   TEXT,
    maintenance_cost DECIMAL(10,2),
    downtime_hours   INT,
    next_due_date    DATE,
    attachment       VARCHAR(255),
    remarks          TEXT,
    is_deleted       TINYINT(1) DEFAULT 0,
    created_at       TIMESTAMP  DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (asset_id)    REFERENCES assets(id),
    FOREIGN KEY (schedule_id) REFERENCES asset_maintenance_schedule(id),
    FOREIGN KEY (vendor_id)   REFERENCES vendors(id)
);

-- =====================================================
-- CALIBRATION RECORDS
-- =====================================================

CREATE TABLE asset_calibration (
    id                 INT AUTO_INCREMENT PRIMARY KEY,
    asset_id           INT,
    calibration_date   DATE,
    calibration_due    DATE,
    certificate_number VARCHAR(100),
    vendor_id          INT,
    remarks            TEXT,
    is_deleted         TINYINT(1) DEFAULT 0,
    created_at         TIMESTAMP  DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (asset_id)  REFERENCES assets(id),
    FOREIGN KEY (vendor_id) REFERENCES vendors(id)
);

-- =====================================================
-- MAINTENANCE REMINDERS
-- =====================================================

CREATE TABLE maintenance_reminders (
    id                  INT AUTO_INCREMENT PRIMARY KEY,
    schedule_id         INT,
    asset_id            INT,
    reminder_date       DATE,
    notified            ENUM('no','yes') DEFAULT 'no',
    notification_method ENUM('email','sms','system'),
    created_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (schedule_id) REFERENCES asset_maintenance_schedule(id),
    FOREIGN KEY (asset_id)    REFERENCES assets(id)
);

-- =====================================================
-- ASSET DISPOSAL
-- =====================================================

CREATE TABLE asset_disposal (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    asset_id        INT,
    disposal_date   DATE,
    disposal_method VARCHAR(150),
    remarks         TEXT,
    approved_by     VARCHAR(150),
    is_deleted      TINYINT(1) DEFAULT 0,
    created_at      TIMESTAMP  DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (asset_id) REFERENCES assets(id)
);

-- =====================================================
-- USERS
-- =====================================================

CREATE TABLE users (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(150) NOT NULL,
    email      VARCHAR(150) NOT NULL UNIQUE,
    password   VARCHAR(255) NOT NULL,
    role       ENUM('admin','manager','technician') DEFAULT 'technician',
    status     ENUM('active','inactive') DEFAULT 'active',
    is_deleted TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP  DEFAULT CURRENT_TIMESTAMP
);

-- =====================================================
-- ACTIVITY LOGS
-- =====================================================

CREATE TABLE activity_logs (
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

-- =====================================================
-- INDEXES FOR PERFORMANCE
-- =====================================================

CREATE INDEX idx_asset_tag            ON assets(asset_tag);
CREATE INDEX idx_asset_category       ON assets(category_id);
CREATE INDEX idx_asset_department     ON asset_assignments(department_id);
CREATE INDEX idx_maintenance_due      ON asset_maintenance_schedule(next_due_date);
CREATE INDEX idx_activity_logs_user   ON activity_logs(user_id);
CREATE INDEX idx_activity_logs_module ON activity_logs(module);
CREATE INDEX idx_activity_logs_created ON activity_logs(created_at);

-- =====================================================
-- DEFAULT ADMIN USER  (password: admin123)
-- =====================================================

INSERT INTO users (name, email, password, role, status) VALUES
('Administrator', 'admin@hospital.com',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 'admin', 'active');

-- =====================================================
-- SEED DATA – FLOORS
-- =====================================================

INSERT INTO floors (floor_name, floor_code, building) VALUES
('Basement',  'BF', 'Main Hospital Building'),
('1st Floor', 'F1', 'Main Hospital Building'),
('2nd Floor', 'F2', 'Main Hospital Building'),
('3rd Floor', 'F3', 'Main Hospital Building');

-- =====================================================
-- SEED DATA – DEPARTMENTS
-- =====================================================

-- Basement
INSERT INTO departments (department_name, department_code, floor_id, description, status) VALUES
('Maintenance & Engineering',  'MNT',  1, 'Handles building maintenance, repairs, and engineering works',                    'active'),
('Medical Waste Management',   'MWM',  1, 'Responsible for safe collection, storage, and disposal of medical waste',        'active'),
('Laundry & Linen Services',   'LLS',  1, 'Manages cleaning, laundering, and distribution of hospital linen',              'active'),
('Central Pharmacy Store',     'CPS',  1, 'Bulk storage and inventory management of pharmaceutical supplies',              'active'),
('Generator & Power Plant',    'GPP',  1, 'Operates and maintains backup generators and the hospital power infrastructure', 'active');

-- 1st Floor
INSERT INTO departments (department_name, department_code, floor_id, description, status) VALUES
('Emergency Department',    'ER',  2, 'Provides immediate care for acute illnesses and injuries 24/7',                    'active'),
('Out-Patient Department',  'OPD', 2, 'Handles scheduled consultations and follow-up visits',                             'active'),
('Reception & Admissions',  'REC', 2, 'Manages patient registration, admissions, and general enquiries',                  'active'),
('Pharmacy (Dispensing)',   'PHR', 2, 'Dispenses prescribed medications to in- and out-patients',                         'active'),
('Laboratory Services',     'LAB', 2, 'Performs diagnostic tests including haematology, biochemistry, and microbiology',  'active'),
('Radiology & Imaging',     'RAD', 2, 'Operates X-ray, ultrasound, CT scanning, and other imaging services',             'active'),
('Administration & HR',     'ADM', 2, 'Oversees hospital administration, human resources, and staff affairs',             'active'),
('Finance & Billing',       'FIN', 2, 'Manages billing, payments, insurance claims, and financial records',               'active');

-- 2nd Floor
INSERT INTO departments (department_name, department_code, floor_id, description, status) VALUES
('Intensive Care Unit',            'ICU',  3, 'Provides critical care for severely ill patients requiring continuous monitoring', 'active'),
('High Dependency Unit',           'HDU',  3, 'Step-down unit for patients needing close monitoring but not full ICU care',      'active'),
('Operating Theatres',             'OT',   3, 'Facilities for surgical procedures including elective and emergency operations',   'active'),
('Central Sterile Supply Dept.',   'CSSD', 3, 'Sterilises and distributes surgical instruments and medical devices',             'active'),
('Blood Bank & Transfusion',       'BB',   3, 'Stores, cross-matches, and issues blood and blood products',                      'active'),
('Cardiology Unit',                'CARD', 3, 'Diagnoses and treats heart and cardiovascular conditions',                        'active');

-- 3rd Floor
INSERT INTO departments (department_name, department_code, floor_id, description, status) VALUES
('General Ward – Male',     'GWM',  4, 'In-patient ward for adult male patients',                                          'active'),
('General Ward – Female',   'GWF',  4, 'In-patient ward for adult female patients',                                        'active'),
('Maternity & Obstetrics',  'MAT',  4, 'Provides antenatal, intrapartum, and postnatal care',                              'active'),
('Paediatrics Ward',        'PAED', 4, 'In-patient care for infants, children, and adolescents',                           'active'),
('Orthopaedic Ward',        'ORTH', 4, 'Treats musculoskeletal injuries, fractures, and post-surgical orthopaedic cases',  'active'),
('Private & VIP Rooms',     'PVT',  4, 'Premium single-occupancy rooms for patients requiring additional privacy',          'active');

-- =====================================================
-- SEED DATA – ASSET CATEGORIES
-- =====================================================

INSERT INTO asset_categories (category_name, description) VALUES
('Medical Equipment',                'Clinical devices used directly in patient diagnosis, monitoring, and treatment'),
('IT & Communication Equipment',     'Computers, servers, networking hardware, and communication devices'),
('Furniture & Fixtures',             'Office, ward, and waiting-area furniture plus fixed building fixtures'),
('Electrical & HVAC Equipment',      'Power generation, uninterruptible power supplies, air-conditioning, and ventilation'),
('Laboratory Equipment',             'Analytical, diagnostic, and sample-processing instruments used in the lab'),
('Radiology & Imaging Equipment',    'X-ray, ultrasound, CT, MRI, and other medical imaging systems'),
('Vehicles & Patient Transport',     'Ambulances, administrative vehicles, and internal patient-transport equipment'),
('Office & Administrative Equipment','Copiers, shredders, safes, security cameras, and other administrative tools');

-- =====================================================
-- SEED DATA – ASSET SUBCATEGORIES
-- =====================================================

-- Medical Equipment (category_id = 1)
INSERT INTO asset_subcategories (category_id, subcategory_name, description) VALUES
(1, 'Diagnostic Equipment',         'Devices used to diagnose medical conditions, e.g. stethoscopes, ECG machines'),
(1, 'Life Support Equipment',       'Ventilators, infusion pumps, defibrillators, and similar life-critical devices'),
(1, 'Surgical Equipment',           'Instruments and tools used during surgical procedures'),
(1, 'Patient Monitoring Equipment', 'Bedside monitors, pulse oximeters, vital-signs monitors'),
(1, 'Rehabilitation Equipment',     'Physiotherapy beds, exercise equipment, and therapy aids');

-- IT & Communication (category_id = 2)
INSERT INTO asset_subcategories (category_id, subcategory_name, description) VALUES
(2, 'Computers & Laptops',     'Desktop PCs, laptops, and workstations'),
(2, 'Servers & Networking',    'File servers, switches, routers, and network infrastructure'),
(2, 'Printers & Scanners',     'Document printers, barcode scanners, and label printers'),
(2, 'Communication Devices',   'Telephones, intercoms, pagers, and two-way radios'),
(2, 'Audio Visual Equipment',  'Projectors, display screens, and video-conferencing systems');

-- Furniture & Fixtures (category_id = 3)
INSERT INTO asset_subcategories (category_id, subcategory_name, description) VALUES
(3, 'Office Furniture',          'Desks, chairs, filing cabinets, and workstation accessories'),
(3, 'Ward & Patient Furniture',  'Hospital beds, bedside lockers, over-bed tables, and patient chairs'),
(3, 'Storage & Shelving',        'Medication trolleys, supply shelves, and storage cabinets'),
(3, 'Waiting Area Furniture',    'Benches, sofas, and chairs for patient waiting areas');

-- Electrical & HVAC (category_id = 4)
INSERT INTO asset_subcategories (category_id, subcategory_name, description) VALUES
(4, 'Power Generation & UPS', 'Generators, automatic voltage regulators, and UPS units'),
(4, 'HVAC & Ventilation',     'Air-conditioning units, fans, and ducted ventilation systems'),
(4, 'Lighting Equipment',     'Emergency lighting, theatre lights, and specialised examination lamps');

-- Laboratory Equipment (category_id = 5)
INSERT INTO asset_subcategories (category_id, subcategory_name, description) VALUES
(5, 'Analytical Equipment',        'Analysers for blood chemistry, haematology, and urinalysis'),
(5, 'Sample Processing Equipment', 'Centrifuges, incubators, water baths, and autoclaves'),
(5, 'Microscopy Equipment',        'Light microscopes, electron microscopes, and slide staining equipment');

-- Radiology & Imaging (category_id = 6)
INSERT INTO asset_subcategories (category_id, subcategory_name, description) VALUES
(6, 'X-Ray Equipment',    'Fixed and mobile X-ray machines, digital radiography systems'),
(6, 'Ultrasound Equipment','Ultrasound scanners and Doppler devices'),
(6, 'CT & MRI Equipment', 'Computed tomography and magnetic resonance imaging systems');

-- Vehicles & Patient Transport (category_id = 7)
INSERT INTO asset_subcategories (category_id, subcategory_name, description) VALUES
(7, 'Ambulances',               'Emergency and patient transport ambulances'),
(7, 'Administrative Vehicles',  'Hospital cars and vans used for administrative and supply purposes'),
(7, 'Internal Patient Transport','Wheelchairs, stretchers, and patient trolleys');

-- Office & Administrative Equipment (category_id = 8)
INSERT INTO asset_subcategories (category_id, subcategory_name, description) VALUES
(8, 'Copiers & Duplicators',        'Photocopiers and multi-function office printers'),
(8, 'Security Equipment',           'CCTV cameras, access-control readers, and alarm systems'),
(8, 'Kitchen & Catering Equipment', 'Refrigerators, microwaves, and catering appliances in staff areas');

-- =====================================================
-- SEED DATA – VENDORS / SUPPLIERS
-- =====================================================

INSERT INTO vendors (vendor_name, contact_person, phone, email, address) VALUES
('MedEquip Solutions Ltd',        'Ahmed Hassan',  '+251-911-100-001', 'sales@medequip.et',        'Bole Road, Addis Ababa, Ethiopia'),
('TechMed International',         'Sara Tesfaye',  '+251-911-200-002', 'info@techmed.et',          'Mexico Square, Addis Ababa, Ethiopia'),
('HospiFurniture Co.',            'Kebede Alemu',  '+251-911-300-003', 'orders@hospifurniture.et', 'Piazza, Addis Ababa, Ethiopia'),
('PharmaTech Supplies',           'Tigist Worku',  '+251-911-400-004', 'supply@pharmatech.et',     'Merkato, Addis Ababa, Ethiopia'),
('ElectroMed Systems',            'Dawit Bekele',  '+251-911-500-005', 'support@electromed.et',    'Kazanchis, Addis Ababa, Ethiopia'),
('Global Medical Devices',        'Meron Girma',   '+251-911-600-006', 'gmd@globalmed.et',         'CMC Area, Addis Ababa, Ethiopia'),
('OfficeWorks Hospital Supplies', 'Yonas Tadesse', '+251-911-700-007', 'info@officeworks.et',      'Gerji, Addis Ababa, Ethiopia'),
('Radiology Tech Africa',         'Hiwot Solomon', '+251-911-800-008', 'rta@radtech.et',           'Sarbet, Addis Ababa, Ethiopia');

-- =====================================================
-- SEED DATA – LOCATIONS / ROOMS
-- =====================================================

-- Basement (floor_id = 1)
INSERT INTO locations (floor_id, department_id, room_number, location_name, description) VALUES
(1, 1, 'B-101', 'Main Workshop',           'Central maintenance workshop for tools and equipment'),
(1, 1, 'B-102', 'Electrical Control Room', 'Houses main electrical panels and distribution boards'),
(1, 2, 'B-201', 'Medical Waste Storage',   'Secure room for temporary storage of segregated medical waste'),
(1, 3, 'B-301', 'Laundry Processing Room', 'Washing and drying of all hospital linen'),
(1, 4, 'B-401', 'Pharmacy Bulk Store',     'Temperature-controlled storage for pharmaceutical stock'),
(1, 5, 'B-501', 'Generator Room',          'Houses backup diesel generators and UPS systems');

-- 1st Floor (floor_id = 2)
INSERT INTO locations (floor_id, department_id, room_number, location_name, description) VALUES
(2, 6,  'F1-101', 'ER Triage Area',            'Initial assessment of emergency patients on arrival'),
(2, 6,  'F1-102', 'Resuscitation Room',        'Critical resuscitation bay with full life-support equipment'),
(2, 7,  'F1-201', 'OPD Consultation Room 1',   'Doctor consultation room for out-patients'),
(2, 7,  'F1-202', 'OPD Consultation Room 2',   'Doctor consultation room for out-patients'),
(2, 8,  'F1-301', 'Patient Registration Desk', 'Front-desk area for patient registration and admissions'),
(2, 9,  'F1-401', 'Dispensing Counter',        'Main pharmacy dispensing counter for out-patients'),
(2, 10, 'F1-501', 'Specimen Reception',        'Receives and logs laboratory specimens'),
(2, 10, 'F1-502', 'Biochemistry Lab',          'Performs blood chemistry and metabolic panel tests'),
(2, 11, 'F1-601', 'X-Ray Room 1',              'Fixed digital X-ray room'),
(2, 11, 'F1-602', 'Ultrasound Room',           'Ultrasound scanning room'),
(2, 12, 'F1-701', 'HR Office',                 'Human Resources administration office'),
(2, 13, 'F1-801', 'Billing Counter',           'Patient billing and insurance processing counter');

-- 2nd Floor (floor_id = 3)
INSERT INTO locations (floor_id, department_id, room_number, location_name, description) VALUES
(3, 14, 'F2-101', 'ICU Bay 1',            'Four-bed intensive care bay with full monitoring'),
(3, 14, 'F2-102', 'ICU Bay 2',            'Four-bed intensive care bay with full monitoring'),
(3, 15, 'F2-201', 'HDU Ward',             'Six-bed high dependency unit'),
(3, 16, 'F2-301', 'Operating Theatre 1',  'Main general surgery theatre'),
(3, 16, 'F2-302', 'Operating Theatre 2',  'Orthopaedic and trauma surgery theatre'),
(3, 16, 'F2-303', 'Operating Theatre 3',  'Minor procedures and day-case theatre'),
(3, 17, 'F2-401', 'CSSD Processing Room', 'Decontamination and sterilisation of surgical instruments'),
(3, 18, 'F2-501', 'Blood Bank Lab',       'Blood cross-matching and component storage'),
(3, 19, 'F2-601', 'Cardiology Clinic',    'ECG, stress testing, and cardiac consultation room');

-- 3rd Floor (floor_id = 4)
INSERT INTO locations (floor_id, department_id, room_number, location_name, description) VALUES
(4, 20, 'F3-101', 'Male General Ward',    '20-bed open ward for adult male in-patients'),
(4, 21, 'F3-201', 'Female General Ward',  '20-bed open ward for adult female in-patients'),
(4, 22, 'F3-301', 'Antenatal Clinic Room','Antenatal checks and foetal monitoring'),
(4, 22, 'F3-302', 'Delivery Suite',       'Labour and delivery room'),
(4, 23, 'F3-401', 'Paediatric Ward',      '15-bed ward for child in-patients'),
(4, 23, 'F3-402', 'Neonatal Corner',      'Dedicated area for newborn care within paediatrics'),
(4, 24, 'F3-501', 'Orthopaedic Ward',     '12-bed ward for orthopaedic in-patients'),
(4, 25, 'F3-601', 'Private Room 1',       'Single-occupancy private room'),
(4, 25, 'F3-602', 'Private Room 2',       'Single-occupancy private room'),
(4, 25, 'F3-603', 'VIP Suite',            'Premium suite with lounge and ensuite bathroom');
