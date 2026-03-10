<?php
/**
 * Database Seeder
 *
 * Populates the hospital_assets database with sample data:
 *   - Building floors  (Basement, 1st, 2nd, 3rd)
 *   - Hospital departments per floor
 *   - Asset categories and sub-categories
 *   - Sample vendors / suppliers
 *   - Room / location records
 *
 * Access: admin users only.
 * Usage : open this page in a browser while logged in as an admin and
 *         click "Run Seeder".  The page is safe to run multiple times
 *         because it checks for existing rows before inserting.
 */

session_start();
require_once __DIR__ . '/middleware/auth_check.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/app.php';

// Only administrators may run the seeder
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['error_message'] = 'Access denied. Administrator role required to run the seeder.';
    header('Location: ' . BASE_URL . '/dashboard/index.php');
    exit;
}

$pageTitle = 'Database Seeder';
$results   = [];
$ran       = false;

// -------------------------------------------------------
// Seed data definitions
// -------------------------------------------------------

$floors = [
    ['Basement',  'BF', 'Main Hospital Building'],
    ['1st Floor', 'F1', 'Main Hospital Building'],
    ['2nd Floor', 'F2', 'Main Hospital Building'],
    ['3rd Floor', 'F3', 'Main Hospital Building'],
];

// Departments: [name, code, floor_index (0-based), description]
$departments = [
    // Basement (floor index 0)
    ['Maintenance & Engineering',   'MNT',  0, 'Handles building maintenance, repairs, and engineering works'],
    ['Medical Waste Management',    'MWM',  0, 'Responsible for safe collection, storage, and disposal of medical waste'],
    ['Laundry & Linen Services',    'LLS',  0, 'Manages cleaning, laundering, and distribution of hospital linen'],
    ['Central Pharmacy Store',      'CPS',  0, 'Bulk storage and inventory management of pharmaceutical supplies'],
    ['Generator & Power Plant',     'GPP',  0, 'Operates and maintains backup generators and the hospital power infrastructure'],
    // 1st Floor (floor index 1)
    ['Emergency Department',        'ER',   1, 'Provides immediate care for acute illnesses and injuries 24/7'],
    ['Out-Patient Department',      'OPD',  1, 'Handles scheduled consultations and follow-up visits'],
    ['Reception & Admissions',      'REC',  1, 'Manages patient registration, admissions, and general enquiries'],
    ['Pharmacy (Dispensing)',       'PHR',  1, 'Dispenses prescribed medications to in- and out-patients'],
    ['Laboratory Services',         'LAB',  1, 'Performs diagnostic tests including haematology, biochemistry, and microbiology'],
    ['Radiology & Imaging',         'RAD',  1, 'Operates X-ray, ultrasound, CT scanning, and other imaging services'],
    ['Administration & HR',         'ADM',  1, 'Oversees hospital administration, human resources, and staff affairs'],
    ['Finance & Billing',           'FIN',  1, 'Manages billing, payments, insurance claims, and financial records'],
    // 2nd Floor (floor index 2)
    ['Intensive Care Unit',             'ICU',  2, 'Provides critical care for severely ill patients requiring continuous monitoring'],
    ['High Dependency Unit',            'HDU',  2, 'Step-down unit for patients needing close monitoring but not full ICU care'],
    ['Operating Theatres',              'OT',   2, 'Facilities for surgical procedures including elective and emergency operations'],
    ['Central Sterile Supply Dept.',    'CSSD', 2, 'Sterilises and distributes surgical instruments and medical devices'],
    ['Blood Bank & Transfusion',        'BB',   2, 'Stores, cross-matches, and issues blood and blood products'],
    ['Cardiology Unit',                 'CARD', 2, 'Diagnoses and treats heart and cardiovascular conditions'],
    // 3rd Floor (floor index 3)
    ['General Ward – Male',         'GWM',  3, 'In-patient ward for adult male patients'],
    ['General Ward – Female',       'GWF',  3, 'In-patient ward for adult female patients'],
    ['Maternity & Obstetrics',      'MAT',  3, 'Provides antenatal, intrapartum, and postnatal care'],
    ['Paediatrics Ward',            'PAED', 3, 'In-patient care for infants, children, and adolescents'],
    ['Orthopaedic Ward',            'ORTH', 3, 'Treats musculoskeletal injuries, fractures, and post-surgical orthopaedic cases'],
    ['Private & VIP Rooms',         'PVT',  3, 'Premium single-occupancy rooms for patients requiring additional privacy'],
];

// Categories: [name, description]
$categories = [
    ['Medical Equipment',               'Clinical devices used directly in patient diagnosis, monitoring, and treatment'],
    ['IT & Communication Equipment',    'Computers, servers, networking hardware, and communication devices'],
    ['Furniture & Fixtures',            'Office, ward, and waiting-area furniture plus fixed building fixtures'],
    ['Electrical & HVAC Equipment',     'Power generation, UPS, air-conditioning, and ventilation'],
    ['Laboratory Equipment',            'Analytical, diagnostic, and sample-processing instruments used in the lab'],
    ['Radiology & Imaging Equipment',   'X-ray, ultrasound, CT, MRI, and other medical imaging systems'],
    ['Vehicles & Patient Transport',    'Ambulances, administrative vehicles, and internal patient-transport equipment'],
    ['Office & Administrative Equipment', 'Copiers, shredders, safes, security cameras, and other administrative tools'],
];

// Subcategories: [category_index (0-based), name, description]
$subcategories = [
    // Medical Equipment (cat 0)
    [0, 'Diagnostic Equipment',         'Devices used to diagnose medical conditions, e.g. stethoscopes, ECG machines'],
    [0, 'Life Support Equipment',        'Ventilators, infusion pumps, defibrillators, and similar life-critical devices'],
    [0, 'Surgical Equipment',            'Instruments and tools used during surgical procedures'],
    [0, 'Patient Monitoring Equipment',  'Bedside monitors, pulse oximeters, vital-signs monitors'],
    [0, 'Rehabilitation Equipment',      'Physiotherapy beds, exercise equipment, and therapy aids'],
    // IT & Communication (cat 1)
    [1, 'Computers & Laptops',           'Desktop PCs, laptops, and workstations'],
    [1, 'Servers & Networking',          'File servers, switches, routers, and network infrastructure'],
    [1, 'Printers & Scanners',           'Document printers, barcode scanners, and label printers'],
    [1, 'Communication Devices',         'Telephones, intercoms, pagers, and two-way radios'],
    [1, 'Audio Visual Equipment',        'Projectors, display screens, and video-conferencing systems'],
    // Furniture (cat 2)
    [2, 'Office Furniture',              'Desks, chairs, filing cabinets, and workstation accessories'],
    [2, 'Ward & Patient Furniture',      'Hospital beds, bedside lockers, over-bed tables, and patient chairs'],
    [2, 'Storage & Shelving',            'Medication trolleys, supply shelves, and storage cabinets'],
    [2, 'Waiting Area Furniture',        'Benches, sofas, and chairs for patient waiting areas'],
    // Electrical & HVAC (cat 3)
    [3, 'Power Generation & UPS',        'Generators, automatic voltage regulators, and uninterruptible power supply (UPS) units'],
    [3, 'HVAC & Ventilation',            'Air-conditioning units, fans, and ducted ventilation systems'],
    [3, 'Lighting Equipment',            'Emergency lighting, theatre lights, and specialised examination lamps'],
    // Laboratory (cat 4)
    [4, 'Analytical Equipment',          'Analysers for blood chemistry, haematology, and urinalysis'],
    [4, 'Sample Processing Equipment',   'Centrifuges, incubators, water baths, and autoclaves'],
    [4, 'Microscopy Equipment',          'Light microscopes, electron microscopes, and slide staining equipment'],
    // Radiology (cat 5)
    [5, 'X-Ray Equipment',               'Fixed and mobile X-ray machines, digital radiography systems'],
    [5, 'Ultrasound Equipment',          'Ultrasound scanners and Doppler devices'],
    [5, 'CT & MRI Equipment',            'Computed tomography and magnetic resonance imaging systems'],
    // Vehicles (cat 6)
    [6, 'Ambulances',                    'Emergency and patient transport ambulances'],
    [6, 'Administrative Vehicles',       'Hospital cars and vans used for administrative and supply purposes'],
    [6, 'Internal Patient Transport',    'Wheelchairs, stretchers, and patient trolleys'],
    // Office & Admin (cat 7)
    [7, 'Copiers & Duplicators',         'Photocopiers and multi-function office printers'],
    [7, 'Security Equipment',            'CCTV cameras, access-control readers, and alarm systems'],
    [7, 'Kitchen & Catering Equipment',  'Refrigerators, microwaves, and catering appliances in staff areas'],
];

// Vendors: [name, contact, phone, email, address]
$vendors = [
    ['MedEquip Solutions Ltd',        'Ahmed Hassan',  '+251-911-100-001', 'sales@medequip.et',        'Bole Road, Addis Ababa, Ethiopia'],
    ['TechMed International',         'Sara Tesfaye',  '+251-911-200-002', 'info@techmed.et',          'Mexico Square, Addis Ababa, Ethiopia'],
    ['HospiFurniture Co.',            'Kebede Alemu',  '+251-911-300-003', 'orders@hospifurniture.et', 'Piazza, Addis Ababa, Ethiopia'],
    ['PharmaTech Supplies',           'Tigist Worku',  '+251-911-400-004', 'supply@pharmatech.et',     'Merkato, Addis Ababa, Ethiopia'],
    ['ElectroMed Systems',            'Dawit Bekele',  '+251-911-500-005', 'support@electromed.et',    'Kazanchis, Addis Ababa, Ethiopia'],
    ['Global Medical Devices',        'Meron Girma',   '+251-911-600-006', 'gmd@globalmed.et',         'CMC Area, Addis Ababa, Ethiopia'],
    ['OfficeWorks Hospital Supplies', 'Yonas Tadesse', '+251-911-700-007', 'info@officeworks.et',      'Gerji, Addis Ababa, Ethiopia'],
    ['Radiology Tech Africa',         'Hiwot Solomon', '+251-911-800-008', 'rta@radtech.et',           'Sarbet, Addis Ababa, Ethiopia'],
];

// Locations: [floor_index, dept_index, room_no, name, description]
$locations = [
    // Basement
    [0, 0, 'B-101', 'Main Workshop',               'Central maintenance workshop for tools and equipment'],
    [0, 0, 'B-102', 'Electrical Control Room',     'Houses main electrical panels and distribution boards'],
    [0, 1, 'B-201', 'Medical Waste Storage',       'Secure room for temporary storage of segregated medical waste'],
    [0, 2, 'B-301', 'Laundry Processing Room',     'Washing and drying of all hospital linen'],
    [0, 3, 'B-401', 'Pharmacy Bulk Store',         'Temperature-controlled storage for pharmaceutical stock'],
    [0, 4, 'B-501', 'Generator Room',              'Houses backup diesel generators and UPS systems'],
    // 1st Floor
    [1, 5, 'F1-101', 'ER Triage Area',             'Initial assessment of emergency patients on arrival'],
    [1, 5, 'F1-102', 'Resuscitation Room',         'Critical resuscitation bay with full life-support equipment'],
    [1, 6, 'F1-201', 'OPD Consultation Room 1',    'Doctor consultation room for out-patients'],
    [1, 6, 'F1-202', 'OPD Consultation Room 2',    'Doctor consultation room for out-patients'],
    [1, 7, 'F1-301', 'Patient Registration Desk',  'Front-desk area for patient registration and admissions'],
    [1, 8, 'F1-401', 'Dispensing Counter',         'Main pharmacy dispensing counter for out-patients'],
    [1, 9, 'F1-501', 'Specimen Reception',         'Receives and logs laboratory specimens'],
    [1, 9, 'F1-502', 'Biochemistry Lab',           'Performs blood chemistry and metabolic panel tests'],
    [1, 10, 'F1-601', 'X-Ray Room 1',              'Fixed digital X-ray room'],
    [1, 10, 'F1-602', 'Ultrasound Room',           'Ultrasound scanning room'],
    [1, 11, 'F1-701', 'HR Office',                 'Human Resources administration office'],
    [1, 12, 'F1-801', 'Billing Counter',           'Patient billing and insurance processing counter'],
    // 2nd Floor
    [2, 13, 'F2-101', 'ICU Bay 1',                 'Four-bed intensive care bay with full monitoring'],
    [2, 13, 'F2-102', 'ICU Bay 2',                 'Four-bed intensive care bay with full monitoring'],
    [2, 14, 'F2-201', 'HDU Ward',                  'Six-bed high dependency unit'],
    [2, 15, 'F2-301', 'Operating Theatre 1',       'Main general surgery theatre'],
    [2, 15, 'F2-302', 'Operating Theatre 2',       'Orthopaedic and trauma surgery theatre'],
    [2, 15, 'F2-303', 'Operating Theatre 3',       'Minor procedures and day-case theatre'],
    [2, 16, 'F2-401', 'CSSD Processing Room',      'Decontamination and sterilisation of surgical instruments'],
    [2, 17, 'F2-501', 'Blood Bank Lab',            'Blood cross-matching and component storage'],
    [2, 18, 'F2-601', 'Cardiology Clinic',         'ECG, stress testing, and cardiac consultation room'],
    // 3rd Floor
    [3, 19, 'F3-101', 'Male General Ward',         '20-bed open ward for adult male in-patients'],
    [3, 20, 'F3-201', 'Female General Ward',       '20-bed open ward for adult female in-patients'],
    [3, 21, 'F3-301', 'Antenatal Clinic Room',     'Antenatal checks and foetal monitoring'],
    [3, 21, 'F3-302', 'Delivery Suite',            'Labour and delivery room'],
    [3, 22, 'F3-401', 'Paediatric Ward',           '15-bed ward for child in-patients'],
    [3, 22, 'F3-402', 'Neonatal Corner',           'Dedicated area for newborn care within paediatrics'],
    [3, 23, 'F3-501', 'Orthopaedic Ward',          '12-bed ward for orthopaedic in-patients'],
    [3, 24, 'F3-601', 'Private Room 1',            'Single-occupancy private room'],
    [3, 24, 'F3-602', 'Private Room 2',            'Single-occupancy private room'],
    [3, 24, 'F3-603', 'VIP Suite',                 'Premium suite with lounge and ensuite bathroom'],
];

// -------------------------------------------------------
// Run seeder when form is submitted
// -------------------------------------------------------

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_seed'])) {
    $pdo = getDBConnection();
    $pdo->beginTransaction();

    try {
        // -- Floors -------------------------------------------------------
        $insertedFloors   = 0;
        $skippedFloors    = 0;
        $floorIds         = [];   // floor_index => DB id

        $stmtCheck  = $pdo->prepare("SELECT id FROM floors WHERE floor_code = ? AND building = ?");
        $stmtInsert = $pdo->prepare(
            "INSERT INTO floors (floor_name, floor_code, building) VALUES (?, ?, ?)"
        );

        foreach ($floors as $idx => $f) {
            $stmtCheck->execute([$f[1], $f[2]]);
            $existing = $stmtCheck->fetchColumn();
            if ($existing) {
                $floorIds[$idx] = (int) $existing;
                $skippedFloors++;
            } else {
                $stmtInsert->execute([$f[0], $f[1], $f[2]]);
                $floorIds[$idx] = (int) $pdo->lastInsertId();
                $insertedFloors++;
            }
        }

        $results[] = [
            'table'    => 'floors',
            'inserted' => $insertedFloors,
            'skipped'  => $skippedFloors,
        ];

        // -- Departments --------------------------------------------------
        $insertedDepts  = 0;
        $skippedDepts   = 0;
        $deptIds        = [];   // dept_index => DB id

        $stmtCheck  = $pdo->prepare("SELECT id FROM departments WHERE department_code = ?");
        $stmtInsert = $pdo->prepare(
            "INSERT INTO departments (department_name, department_code, floor_id, description, status)
             VALUES (?, ?, ?, ?, 'active')"
        );

        foreach ($departments as $idx => $d) {
            $stmtCheck->execute([$d[1]]);
            $existing = $stmtCheck->fetchColumn();
            if ($existing) {
                $deptIds[$idx] = (int) $existing;
                $skippedDepts++;
            } else {
                $floorDbId = $floorIds[$d[2]] ?? null;
                $stmtInsert->execute([$d[0], $d[1], $floorDbId, $d[3]]);
                $deptIds[$idx] = (int) $pdo->lastInsertId();
                $insertedDepts++;
            }
        }

        $results[] = [
            'table'    => 'departments',
            'inserted' => $insertedDepts,
            'skipped'  => $skippedDepts,
        ];

        // -- Asset Categories ---------------------------------------------
        $insertedCats  = 0;
        $skippedCats   = 0;
        $catIds        = [];   // cat_index => DB id

        $stmtCheck  = $pdo->prepare("SELECT id FROM asset_categories WHERE category_name = ?");
        $stmtInsert = $pdo->prepare(
            "INSERT INTO asset_categories (category_name, description) VALUES (?, ?)"
        );

        foreach ($categories as $idx => $c) {
            $stmtCheck->execute([$c[0]]);
            $existing = $stmtCheck->fetchColumn();
            if ($existing) {
                $catIds[$idx] = (int) $existing;
                $skippedCats++;
            } else {
                $stmtInsert->execute([$c[0], $c[1]]);
                $catIds[$idx] = (int) $pdo->lastInsertId();
                $insertedCats++;
            }
        }

        $results[] = [
            'table'    => 'asset_categories',
            'inserted' => $insertedCats,
            'skipped'  => $skippedCats,
        ];

        // -- Asset Subcategories ------------------------------------------
        $insertedSubs  = 0;
        $skippedSubs   = 0;

        $stmtCheck  = $pdo->prepare(
            "SELECT id FROM asset_subcategories WHERE subcategory_name = ? AND category_id = ?"
        );
        $stmtInsert = $pdo->prepare(
            "INSERT INTO asset_subcategories (category_id, subcategory_name, description) VALUES (?, ?, ?)"
        );

        foreach ($subcategories as $s) {
            $catDbId = $catIds[$s[0]] ?? null;
            if ($catDbId === null) {
                continue;
            }
            $stmtCheck->execute([$s[1], $catDbId]);
            if ($stmtCheck->fetchColumn()) {
                $skippedSubs++;
            } else {
                $stmtInsert->execute([$catDbId, $s[1], $s[2]]);
                $insertedSubs++;
            }
        }

        $results[] = [
            'table'    => 'asset_subcategories',
            'inserted' => $insertedSubs,
            'skipped'  => $skippedSubs,
        ];

        // -- Vendors ------------------------------------------------------
        $insertedVendors  = 0;
        $skippedVendors   = 0;

        $stmtCheck  = $pdo->prepare("SELECT id FROM vendors WHERE email = ?");
        $stmtInsert = $pdo->prepare(
            "INSERT INTO vendors (vendor_name, contact_person, phone, email, address) VALUES (?, ?, ?, ?, ?)"
        );

        foreach ($vendors as $v) {
            $stmtCheck->execute([$v[3]]);
            if ($stmtCheck->fetchColumn()) {
                $skippedVendors++;
            } else {
                $stmtInsert->execute($v);
                $insertedVendors++;
            }
        }

        $results[] = [
            'table'    => 'vendors',
            'inserted' => $insertedVendors,
            'skipped'  => $skippedVendors,
        ];

        // -- Locations ----------------------------------------------------
        $insertedLocs  = 0;
        $skippedLocs   = 0;

        $stmtCheck  = $pdo->prepare("SELECT id FROM locations WHERE room_number = ?");
        $stmtInsert = $pdo->prepare(
            "INSERT INTO locations (floor_id, department_id, room_number, location_name, description)
             VALUES (?, ?, ?, ?, ?)"
        );

        foreach ($locations as $l) {
            $stmtCheck->execute([$l[2]]);
            if ($stmtCheck->fetchColumn()) {
                $skippedLocs++;
            } else {
                $floorDbId = $floorIds[$l[0]] ?? null;
                $deptDbId  = $deptIds[$l[1]] ?? null;
                $stmtInsert->execute([$floorDbId, $deptDbId, $l[2], $l[3], $l[4]]);
                $insertedLocs++;
            }
        }

        $results[] = [
            'table'    => 'locations',
            'inserted' => $insertedLocs,
            'skipped'  => $skippedLocs,
        ];

        $pdo->commit();
        $ran = true;

    } catch (Exception $e) {
        $pdo->rollBack();
        $errorMsg = $e->getMessage();
    }
}

require_once __DIR__ . '/views/header.php';
require_once __DIR__ . '/views/sidebar.php';
?>

<div class="main-content" id="mainContent">
    <div class="container-fluid py-4">

        <div class="row mb-4">
            <div class="col-12">
                <h2 class="mb-0"><i class="fas fa-database me-2"></i>Database Seeder</h2>
                <p class="text-muted mt-1">Populate the database with sample hospital structure data.</p>
            </div>
        </div>

        <?php if (isset($errorMsg)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle me-2"></i>
                <strong>Error:</strong> <?php echo htmlspecialchars($errorMsg, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <?php if ($ran): ?>
            <!-- Results table -->
            <div class="alert alert-success">
                <i class="fas fa-check-circle me-2"></i>
                <strong>Seeder completed successfully.</strong>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <i class="fas fa-table me-2"></i>Seeder Results
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Table</th>
                                <th class="text-center">Inserted</th>
                                <th class="text-center">Skipped (already existed)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($results as $r): ?>
                            <tr>
                                <td><code><?php echo htmlspecialchars($r['table'], ENT_QUOTES, 'UTF-8'); ?></code></td>
                                <td class="text-center">
                                    <span class="badge bg-success"><?php echo (int) $r['inserted']; ?></span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-secondary"><?php echo (int) $r['skipped']; ?></span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <a href="<?php echo BASE_URL; ?>/dashboard/index.php" class="btn btn-primary">
                <i class="fas fa-tachometer-alt me-1"></i>Go to Dashboard
            </a>

        <?php else: ?>
            <!-- Confirmation form -->
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <i class="fas fa-info-circle me-2"></i>What will be seeded
                </div>
                <div class="card-body">
                    <p>The seeder will insert the following reference data into the database.
                       Rows that already exist (matched by a unique field) will be skipped,
                       so it is safe to run more than once.</p>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="card border-primary">
                                <div class="card-body">
                                    <h6 class="card-title text-primary"><i class="fas fa-building me-2"></i>Floors</h6>
                                    <ul class="list-unstyled mb-0 small">
                                        <li><i class="fas fa-angle-right me-1"></i>Basement</li>
                                        <li><i class="fas fa-angle-right me-1"></i>1st Floor</li>
                                        <li><i class="fas fa-angle-right me-1"></i>2nd Floor</li>
                                        <li><i class="fas fa-angle-right me-1"></i>3rd Floor</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-info">
                                <div class="card-body">
                                    <h6 class="card-title text-info"><i class="fas fa-sitemap me-2"></i>Departments (25)</h6>
                                    <ul class="list-unstyled mb-0 small">
                                        <li><i class="fas fa-angle-right me-1"></i>5 Basement depts.</li>
                                        <li><i class="fas fa-angle-right me-1"></i>8 on 1st Floor</li>
                                        <li><i class="fas fa-angle-right me-1"></i>6 on 2nd Floor</li>
                                        <li><i class="fas fa-angle-right me-1"></i>6 on 3rd Floor</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-success">
                                <div class="card-body">
                                    <h6 class="card-title text-success"><i class="fas fa-tags me-2"></i>Categories (8) &amp; Subcategories (29)</h6>
                                    <ul class="list-unstyled mb-0 small">
                                        <li><i class="fas fa-angle-right me-1"></i>Medical Equipment</li>
                                        <li><i class="fas fa-angle-right me-1"></i>IT &amp; Communication</li>
                                        <li><i class="fas fa-angle-right me-1"></i>Furniture &amp; Fixtures</li>
                                        <li><i class="fas fa-angle-right me-1"></i>+ 5 more categories</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-warning">
                                <div class="card-body">
                                    <h6 class="card-title text-warning"><i class="fas fa-truck me-2"></i>Vendors (8)</h6>
                                    <ul class="list-unstyled mb-0 small">
                                        <li><i class="fas fa-angle-right me-1"></i>MedEquip Solutions Ltd</li>
                                        <li><i class="fas fa-angle-right me-1"></i>TechMed International</li>
                                        <li><i class="fas fa-angle-right me-1"></i>ElectroMed Systems</li>
                                        <li><i class="fas fa-angle-right me-1"></i>+ 5 more vendors</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-secondary">
                                <div class="card-body">
                                    <h6 class="card-title text-secondary"><i class="fas fa-door-open me-2"></i>Locations / Rooms (37)</h6>
                                    <ul class="list-unstyled mb-0 small">
                                        <li><i class="fas fa-angle-right me-1"></i>6 Basement rooms</li>
                                        <li><i class="fas fa-angle-right me-1"></i>12 on 1st Floor</li>
                                        <li><i class="fas fa-angle-right me-1"></i>9 on 2nd Floor</li>
                                        <li><i class="fas fa-angle-right me-1"></i>10 on 3rd Floor</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <form method="POST">
                <button type="submit" name="confirm_seed" value="1" class="btn btn-primary btn-lg">
                    <i class="fas fa-play-circle me-2"></i>Run Seeder
                </button>
                <a href="<?php echo BASE_URL; ?>/dashboard/index.php" class="btn btn-secondary btn-lg ms-2">
                    <i class="fas fa-times me-1"></i>Cancel
                </a>
            </form>
        <?php endif; ?>

    </div>
</div>

<?php require_once __DIR__ . '/views/footer.php'; ?>
