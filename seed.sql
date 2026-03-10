-- =====================================================
-- Hospital Asset Management System - Data Seeder
-- =====================================================
-- Run this AFTER db.sql to populate the database with
-- sample data representing a typical hospital setup:
--   • Building floors  (Basement, 1st, 2nd, 3rd)
--   • General hospital departments
--   • Asset categories and sub-categories
--   • Sample vendors / suppliers
--   • Representative room / location records
-- =====================================================

USE hospital_assets;

-- =====================================================
-- FLOORS
-- =====================================================

INSERT INTO floors (floor_name, floor_code, building) VALUES
('Basement',   'BF', 'Main Hospital Building'),
('1st Floor',  'F1', 'Main Hospital Building'),
('2nd Floor',  'F2', 'Main Hospital Building'),
('3rd Floor',  'F3', 'Main Hospital Building');

-- =====================================================
-- DEPARTMENTS
-- =====================================================
-- Basement departments

INSERT INTO departments (department_name, department_code, floor_id, description, status) VALUES
('Maintenance & Engineering',    'MNT',  1, 'Handles building maintenance, repairs, and engineering works',                           'active'),
('Medical Waste Management',     'MWM',  1, 'Responsible for safe collection, storage, and disposal of medical waste',               'active'),
('Laundry & Linen Services',     'LLS',  1, 'Manages cleaning, laundering, and distribution of hospital linen',                      'active'),
('Central Pharmacy Store',       'CPS',  1, 'Bulk storage and inventory management of pharmaceutical supplies',                      'active'),
('Generator & Power Plant',      'GPP',  1, 'Operates and maintains backup generators and the hospital power infrastructure',        'active');

-- 1st Floor departments

INSERT INTO departments (department_name, department_code, floor_id, description, status) VALUES
('Emergency Department',         'ER',   2, 'Provides immediate care for acute illnesses and injuries 24/7',                         'active'),
('Out-Patient Department',       'OPD',  2, 'Handles scheduled consultations and follow-up visits',                                  'active'),
('Reception & Admissions',       'REC',  2, 'Manages patient registration, admissions, and general enquiries',                       'active'),
('Pharmacy (Dispensing)',        'PHR',  2, 'Dispenses prescribed medications to in- and out-patients',                              'active'),
('Laboratory Services',          'LAB',  2, 'Performs diagnostic tests including haematology, biochemistry, and microbiology',       'active'),
('Radiology & Imaging',          'RAD',  2, 'Operates X-ray, ultrasound, CT scanning, and other imaging services',                  'active'),
('Administration & HR',          'ADM',  2, 'Oversees hospital administration, human resources, and staff affairs',                  'active'),
('Finance & Billing',            'FIN',  2, 'Manages billing, payments, insurance claims, and financial records',                    'active');

-- 2nd Floor departments

INSERT INTO departments (department_name, department_code, floor_id, description, status) VALUES
('Intensive Care Unit',              'ICU',  3, 'Provides critical care for severely ill patients requiring continuous monitoring',  'active'),
('High Dependency Unit',             'HDU',  3, 'Step-down unit for patients needing close monitoring but not full ICU care',        'active'),
('Operating Theatres',               'OT',   3, 'Facilities for surgical procedures including elective and emergency operations',    'active'),
('Central Sterile Supply Dept.',     'CSSD', 3, 'Sterilises and distributes surgical instruments and medical devices',              'active'),
('Blood Bank & Transfusion',         'BB',   3, 'Stores, cross-matches, and issues blood and blood products',                       'active'),
('Cardiology Unit',                  'CARD', 3, 'Diagnoses and treats heart and cardiovascular conditions',                         'active');

-- 3rd Floor departments

INSERT INTO departments (department_name, department_code, floor_id, description, status) VALUES
('General Ward – Male',          'GWM',  4, 'In-patient ward for adult male patients',                                              'active'),
('General Ward – Female',        'GWF',  4, 'In-patient ward for adult female patients',                                            'active'),
('Maternity & Obstetrics',       'MAT',  4, 'Provides antenatal, intrapartum, and postnatal care',                                  'active'),
('Paediatrics Ward',             'PAED', 4, 'In-patient care for infants, children, and adolescents',                              'active'),
('Orthopaedic Ward',             'ORTH', 4, 'Treats musculoskeletal injuries, fractures, and post-surgical orthopaedic cases',     'active'),
('Private & VIP Rooms',          'PVT',  4, 'Premium single-occupancy rooms for patients requiring additional privacy',             'active');

-- =====================================================
-- ASSET CATEGORIES
-- =====================================================

INSERT INTO asset_categories (category_name, description) VALUES
('Medical Equipment',               'Clinical devices used directly in patient diagnosis, monitoring, and treatment'),
('IT & Communication Equipment',    'Computers, servers, networking hardware, and communication devices'),
('Furniture & Fixtures',            'Office, ward, and waiting-area furniture plus fixed building fixtures'),
('Electrical & HVAC Equipment',     'Power generation, uninterruptible power supplies, air-conditioning, and ventilation'),
('Laboratory Equipment',            'Analytical, diagnostic, and sample-processing instruments used in the lab'),
('Radiology & Imaging Equipment',   'X-ray, ultrasound, CT, MRI, and other medical imaging systems'),
('Vehicles & Patient Transport',    'Ambulances, administrative vehicles, and internal patient-transport equipment'),
('Office & Administrative Equipment','Copiers, shredders, safes, security cameras, and other administrative tools');

-- =====================================================
-- ASSET SUBCATEGORIES
-- =====================================================

-- Medical Equipment subcategories (category_id = 1)
INSERT INTO asset_subcategories (category_id, subcategory_name, description) VALUES
(1, 'Diagnostic Equipment',             'Devices used to diagnose medical conditions, e.g. stethoscopes, ECG machines'),
(1, 'Life Support Equipment',           'Ventilators, infusion pumps, defibrillators, and similar life-critical devices'),
(1, 'Surgical Equipment',               'Instruments and tools used during surgical procedures'),
(1, 'Patient Monitoring Equipment',     'Bedside monitors, pulse oximeters, vital-signs monitors'),
(1, 'Rehabilitation Equipment',         'Physiotherapy beds, exercise equipment, and therapy aids');

-- IT & Communication subcategories (category_id = 2)
INSERT INTO asset_subcategories (category_id, subcategory_name, description) VALUES
(2, 'Computers & Laptops',              'Desktop PCs, laptops, and workstations'),
(2, 'Servers & Networking',             'File servers, switches, routers, and network infrastructure'),
(2, 'Printers & Scanners',              'Document printers, barcode scanners, and label printers'),
(2, 'Communication Devices',            'Telephones, intercoms, pagers, and two-way radios'),
(2, 'Audio Visual Equipment',           'Projectors, display screens, and video-conferencing systems');

-- Furniture & Fixtures subcategories (category_id = 3)
INSERT INTO asset_subcategories (category_id, subcategory_name, description) VALUES
(3, 'Office Furniture',                 'Desks, chairs, filing cabinets, and workstation accessories'),
(3, 'Ward & Patient Furniture',         'Hospital beds, bedside lockers, over-bed tables, and patient chairs'),
(3, 'Storage & Shelving',               'Medication trolleys, supply shelves, and storage cabinets'),
(3, 'Waiting Area Furniture',           'Benches, sofas, and chairs for patient waiting areas');

-- Electrical & HVAC subcategories (category_id = 4)
INSERT INTO asset_subcategories (category_id, subcategory_name, description) VALUES
(4, 'Power Generation & UPS',           'Generators, automatic voltage regulators, and UPS units'),
(4, 'HVAC & Ventilation',               'Air-conditioning units, fans, and ducted ventilation systems'),
(4, 'Lighting Equipment',               'Emergency lighting, theatre lights, and specialised examination lamps');

-- Laboratory Equipment subcategories (category_id = 5)
INSERT INTO asset_subcategories (category_id, subcategory_name, description) VALUES
(5, 'Analytical Equipment',             'Analysers for blood chemistry, haematology, and urinalysis'),
(5, 'Sample Processing Equipment',      'Centrifuges, incubators, water baths, and autoclaves'),
(5, 'Microscopy Equipment',             'Light microscopes, electron microscopes, and slide staining equipment');

-- Radiology & Imaging subcategories (category_id = 6)
INSERT INTO asset_subcategories (category_id, subcategory_name, description) VALUES
(6, 'X-Ray Equipment',                  'Fixed and mobile X-ray machines, digital radiography systems'),
(6, 'Ultrasound Equipment',             'Ultrasound scanners and Doppler devices'),
(6, 'CT & MRI Equipment',               'Computed tomography and magnetic resonance imaging systems');

-- Vehicles subcategories (category_id = 7)
INSERT INTO asset_subcategories (category_id, subcategory_name, description) VALUES
(7, 'Ambulances',                       'Emergency and patient transport ambulances'),
(7, 'Administrative Vehicles',          'Hospital cars and vans used for administrative and supply purposes'),
(7, 'Internal Patient Transport',       'Wheelchairs, stretchers, and patient trolleys');

-- Office & Administrative Equipment subcategories (category_id = 8)
INSERT INTO asset_subcategories (category_id, subcategory_name, description) VALUES
(8, 'Copiers & Duplicators',            'Photocopiers and multi-function office printers'),
(8, 'Security Equipment',               'CCTV cameras, access-control readers, and alarm systems'),
(8, 'Kitchen & Catering Equipment',     'Refrigerators, microwaves, and catering appliances in staff areas');

-- =====================================================
-- VENDORS / SUPPLIERS
-- =====================================================

INSERT INTO vendors (vendor_name, contact_person, phone, email, address) VALUES
('MedEquip Solutions Ltd',        'Ahmed Hassan',       '+251-911-100-001', 'sales@medequip.et',       'Bole Road, Addis Ababa, Ethiopia'),
('TechMed International',         'Sara Tesfaye',       '+251-911-200-002', 'info@techmed.et',         'Mexico Square, Addis Ababa, Ethiopia'),
('HospiFurniture Co.',            'Kebede Alemu',       '+251-911-300-003', 'orders@hospifurniture.et','Piazza, Addis Ababa, Ethiopia'),
('PharmaTech Supplies',           'Tigist Worku',       '+251-911-400-004', 'supply@pharmatech.et',    'Merkato, Addis Ababa, Ethiopia'),
('ElectroMed Systems',            'Dawit Bekele',       '+251-911-500-005', 'support@electromed.et',   'Kazanchis, Addis Ababa, Ethiopia'),
('Global Medical Devices',        'Meron Girma',        '+251-911-600-006', 'gmd@globalmed.et',        'CMC Area, Addis Ababa, Ethiopia'),
('OfficeWorks Hospital Supplies', 'Yonas Tadesse',      '+251-911-700-007', 'info@officeworks.et',     'Gerji, Addis Ababa, Ethiopia'),
('Radiology Tech Africa',         'Hiwot Solomon',      '+251-911-800-008', 'rta@radtech.et',          'Sarbet, Addis Ababa, Ethiopia');

-- =====================================================
-- LOCATIONS / ROOMS
-- =====================================================

-- Basement rooms (floor_id = 1)
INSERT INTO locations (floor_id, department_id, room_number, location_name, description) VALUES
(1, 1, 'B-101', 'Main Workshop',               'Central maintenance workshop for tools and equipment'),
(1, 1, 'B-102', 'Electrical Control Room',     'Houses main electrical panels and distribution boards'),
(1, 2, 'B-201', 'Medical Waste Storage',       'Secure room for temporary storage of segregated medical waste'),
(1, 3, 'B-301', 'Laundry Processing Room',     'Washing and drying of all hospital linen'),
(1, 4, 'B-401', 'Pharmacy Bulk Store',         'Temperature-controlled storage for pharmaceutical stock'),
(1, 5, 'B-501', 'Generator Room',              'Houses backup diesel generators and UPS systems');

-- 1st Floor rooms (floor_id = 2)
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

-- 2nd Floor rooms (floor_id = 3)
INSERT INTO locations (floor_id, department_id, room_number, location_name, description) VALUES
(3, 14, 'F2-101', 'ICU Bay 1',                 'Four-bed intensive care bay with full monitoring'),
(3, 14, 'F2-102', 'ICU Bay 2',                 'Four-bed intensive care bay with full monitoring'),
(3, 15, 'F2-201', 'HDU Ward',                  'Six-bed high dependency unit'),
(3, 16, 'F2-301', 'Operating Theatre 1',       'Main general surgery theatre'),
(3, 16, 'F2-302', 'Operating Theatre 2',       'Orthopaedic and trauma surgery theatre'),
(3, 16, 'F2-303', 'Operating Theatre 3',       'Minor procedures and day-case theatre'),
(3, 17, 'F2-401', 'CSSD Processing Room',      'Decontamination and sterilisation of surgical instruments'),
(3, 18, 'F2-501', 'Blood Bank Lab',            'Blood cross-matching and component storage'),
(3, 19, 'F2-601', 'Cardiology Clinic',         'ECG, stress testing, and cardiac consultation room');

-- 3rd Floor rooms (floor_id = 4)
INSERT INTO locations (floor_id, department_id, room_number, location_name, description) VALUES
(4, 20, 'F3-101', 'Male General Ward',         '20-bed open ward for adult male in-patients'),
(4, 21, 'F3-201', 'Female General Ward',       '20-bed open ward for adult female in-patients'),
(4, 22, 'F3-301', 'Antenatal Clinic Room',     'Antenatal checks and foetal monitoring'),
(4, 22, 'F3-302', 'Delivery Suite',            'Labour and delivery room'),
(4, 23, 'F3-401', 'Paediatric Ward',           '15-bed ward for child in-patients'),
(4, 23, 'F3-402', 'Neonatal Corner',           'Dedicated area for newborn care within paediatrics'),
(4, 24, 'F3-501', 'Orthopaedic Ward',          '12-bed ward for orthopaedic in-patients'),
(4, 25, 'F3-601', 'Private Room 1',            'Single-occupancy private room'),
(4, 25, 'F3-602', 'Private Room 2',            'Single-occupancy private room'),
(4, 25, 'F3-603', 'VIP Suite',                 'Premium suite with lounge and ensuite bathroom');
