<?php
session_start();
require_once __DIR__ . '/../../middleware/auth_check.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/activity_logger.php';

$pdo = getDBConnection();

$pageTitle = 'Add Maintenance Log';

$assets = $pdo->query("SELECT id, asset_name FROM assets WHERE is_deleted = 0 ORDER BY asset_name")->fetchAll(PDO::FETCH_ASSOC);
$schedules = $pdo->query("
    SELECT s.id, a.asset_name, s.maintenance_type
    FROM asset_maintenance_schedule s
    LEFT JOIN assets a ON s.asset_id = a.id
    WHERE s.is_deleted = 0
    ORDER BY a.asset_name
")->fetchAll(PDO::FETCH_ASSOC);
$vendors = $pdo->query("SELECT id, vendor_name FROM vendors WHERE is_deleted = 0 ORDER BY vendor_name")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error_message'] = 'Invalid CSRF token.';
        header('Location: index.php');
        exit;
    }

    $asset_id = (int)$_POST['asset_id'];
    $schedule_id = !empty($_POST['schedule_id']) ? (int)$_POST['schedule_id'] : null;
    $maintenance_date = $_POST['maintenance_date'];
    $maintenance_type = $_POST['maintenance_type'];
    $technician_name = trim($_POST['technician_name']);
    $vendor_id = !empty($_POST['vendor_id']) ? (int)$_POST['vendor_id'] : null;
    $issue_reported = trim($_POST['issue_reported']);
    $work_performed = trim($_POST['work_performed']);
    $parts_replaced = trim($_POST['parts_replaced']);
    $maintenance_cost = !empty($_POST['maintenance_cost']) ? (float)$_POST['maintenance_cost'] : null;
    $downtime_hours = !empty($_POST['downtime_hours']) ? (int)$_POST['downtime_hours'] : null;
    $next_due_date = !empty($_POST['next_due_date']) ? $_POST['next_due_date'] : null;
    $remarks = trim($_POST['remarks']);

    $stmt = $pdo->prepare("
        INSERT INTO asset_maintenance_logs
        (asset_id, schedule_id, maintenance_date, maintenance_type, technician_name, vendor_id, issue_reported, work_performed, parts_replaced, maintenance_cost, downtime_hours, next_due_date, remarks)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([$asset_id, $schedule_id, $maintenance_date, $maintenance_type, $technician_name, $vendor_id, $issue_reported, $work_performed, $parts_replaced, $maintenance_cost, $downtime_hours, $next_due_date, $remarks]);

    $_SESSION['success_message'] = 'Maintenance log created successfully.';
    $newId = (int)$pdo->lastInsertId();
    logActivity($pdo, 'create', 'maintenance_logs', $newId, 'Created maintenance log for asset ID ' . $asset_id);
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/../../views/header.php';
require_once __DIR__ . '/../../views/sidebar.php';
?>

<div class="main-content" id="mainContent">
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0"><?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?></h1>
            <a href="index.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
        </div>

        <div class="card">
            <div class="card-body">
                <form method="POST" action="">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="asset_id" class="form-label">Asset <span class="text-danger">*</span></label>
                            <select name="asset_id" id="asset_id" class="form-select" required>
                                <option value="">Select Asset</option>
                                <?php foreach ($assets as $asset): ?>
                                    <option value="<?php echo (int)$asset['id']; ?>"><?php echo htmlspecialchars($asset['asset_name'], ENT_QUOTES, 'UTF-8'); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="schedule_id" class="form-label">Schedule</label>
                            <select name="schedule_id" id="schedule_id" class="form-select">
                                <option value="">Select Schedule</option>
                                <?php foreach ($schedules as $sch): ?>
                                    <option value="<?php echo (int)$sch['id']; ?>"><?php echo htmlspecialchars(($sch['asset_name'] ?? '') . ': ' . $sch['maintenance_type'], ENT_QUOTES, 'UTF-8'); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="maintenance_date" class="form-label">Maintenance Date <span class="text-danger">*</span></label>
                            <input type="date" name="maintenance_date" id="maintenance_date" class="form-control" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="maintenance_type" class="form-label">Maintenance Type <span class="text-danger">*</span></label>
                            <select name="maintenance_type" id="maintenance_type" class="form-select" required>
                                <option value="preventive">Preventive</option>
                                <option value="repair">Repair</option>
                                <option value="calibration">Calibration</option>
                                <option value="inspection">Inspection</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="technician_name" class="form-label">Technician Name</label>
                            <input type="text" name="technician_name" id="technician_name" class="form-control">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="vendor_id" class="form-label">Vendor</label>
                            <select name="vendor_id" id="vendor_id" class="form-select">
                                <option value="">Select Vendor</option>
                                <?php foreach ($vendors as $vendor): ?>
                                    <option value="<?php echo (int)$vendor['id']; ?>"><?php echo htmlspecialchars($vendor['vendor_name'], ENT_QUOTES, 'UTF-8'); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="maintenance_cost" class="form-label">Maintenance Cost</label>
                            <input type="number" name="maintenance_cost" id="maintenance_cost" class="form-control" step="0.01" min="0">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="downtime_hours" class="form-label">Downtime Hours</label>
                            <input type="number" name="downtime_hours" id="downtime_hours" class="form-control" min="0">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="issue_reported" class="form-label">Issue Reported</label>
                            <textarea name="issue_reported" id="issue_reported" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="work_performed" class="form-label">Work Performed</label>
                            <textarea name="work_performed" id="work_performed" class="form-control" rows="3"></textarea>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="parts_replaced" class="form-label">Parts Replaced</label>
                            <textarea name="parts_replaced" id="parts_replaced" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="remarks" class="form-label">Remarks</label>
                            <textarea name="remarks" id="remarks" class="form-control" rows="3"></textarea>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="next_due_date" class="form-label">Next Due Date</label>
                            <input type="date" name="next_due_date" id="next_due_date" class="form-control">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../views/footer.php'; ?>
