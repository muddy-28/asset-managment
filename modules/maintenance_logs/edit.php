<?php
session_start();
require_once __DIR__ . '/../../middleware/auth_check.php';
require_once __DIR__ . '/../../config/database.php';

$pdo = getDBConnection();

$pageTitle = 'Edit Maintenance Log';

if (!isset($_GET['id'])) {
    $_SESSION['error_message'] = 'Invalid request.';
    header('Location: index.php');
    exit;
}

$id = (int)$_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM asset_maintenance_logs WHERE id = ? AND deleted_at IS NULL");
$stmt->execute([$id]);
$log = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$log) {
    $_SESSION['error_message'] = 'Log not found.';
    header('Location: index.php');
    exit;
}

$assets = $pdo->query("SELECT id, asset_name FROM assets WHERE deleted_at IS NULL ORDER BY asset_name")->fetchAll(PDO::FETCH_ASSOC);
$schedules = $pdo->query("
    SELECT s.id, a.asset_name, s.maintenance_type
    FROM asset_maintenance_schedule s
    LEFT JOIN assets a ON s.asset_id = a.id
    WHERE s.deleted_at IS NULL
    ORDER BY a.asset_name
")->fetchAll(PDO::FETCH_ASSOC);
$vendors = $pdo->query("SELECT id, vendor_name FROM vendors WHERE deleted_at IS NULL ORDER BY vendor_name")->fetchAll(PDO::FETCH_ASSOC);

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
        UPDATE asset_maintenance_logs
        SET asset_id = ?, schedule_id = ?, maintenance_date = ?, maintenance_type = ?, technician_name = ?, vendor_id = ?, issue_reported = ?, work_performed = ?, parts_replaced = ?, maintenance_cost = ?, downtime_hours = ?, next_due_date = ?, remarks = ?
        WHERE id = ?
    ");
    $stmt->execute([$asset_id, $schedule_id, $maintenance_date, $maintenance_type, $technician_name, $vendor_id, $issue_reported, $work_performed, $parts_replaced, $maintenance_cost, $downtime_hours, $next_due_date, $remarks, $id]);

    $_SESSION['success_message'] = 'Maintenance log updated successfully.';
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
                                    <option value="<?php echo (int)$asset['id']; ?>" <?php echo ($asset['id'] == $log['asset_id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($asset['asset_name'], ENT_QUOTES, 'UTF-8'); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="schedule_id" class="form-label">Schedule</label>
                            <select name="schedule_id" id="schedule_id" class="form-select">
                                <option value="">Select Schedule</option>
                                <?php foreach ($schedules as $sch): ?>
                                    <option value="<?php echo (int)$sch['id']; ?>" <?php echo ($sch['id'] == $log['schedule_id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars(($sch['asset_name'] ?? '') . ': ' . $sch['maintenance_type'], ENT_QUOTES, 'UTF-8'); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="maintenance_date" class="form-label">Maintenance Date <span class="text-danger">*</span></label>
                            <input type="date" name="maintenance_date" id="maintenance_date" class="form-control" required value="<?php echo htmlspecialchars($log['maintenance_date'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="maintenance_type" class="form-label">Maintenance Type <span class="text-danger">*</span></label>
                            <select name="maintenance_type" id="maintenance_type" class="form-select" required>
                                <option value="preventive" <?php echo ($log['maintenance_type'] === 'preventive') ? 'selected' : ''; ?>>Preventive</option>
                                <option value="repair" <?php echo ($log['maintenance_type'] === 'repair') ? 'selected' : ''; ?>>Repair</option>
                                <option value="calibration" <?php echo ($log['maintenance_type'] === 'calibration') ? 'selected' : ''; ?>>Calibration</option>
                                <option value="inspection" <?php echo ($log['maintenance_type'] === 'inspection') ? 'selected' : ''; ?>>Inspection</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="technician_name" class="form-label">Technician Name</label>
                            <input type="text" name="technician_name" id="technician_name" class="form-control" value="<?php echo htmlspecialchars($log['technician_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="vendor_id" class="form-label">Vendor</label>
                            <select name="vendor_id" id="vendor_id" class="form-select">
                                <option value="">Select Vendor</option>
                                <?php foreach ($vendors as $vendor): ?>
                                    <option value="<?php echo (int)$vendor['id']; ?>" <?php echo ($vendor['id'] == $log['vendor_id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($vendor['vendor_name'], ENT_QUOTES, 'UTF-8'); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="maintenance_cost" class="form-label">Maintenance Cost</label>
                            <input type="number" name="maintenance_cost" id="maintenance_cost" class="form-control" step="0.01" min="0" value="<?php echo htmlspecialchars($log['maintenance_cost'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="downtime_hours" class="form-label">Downtime Hours</label>
                            <input type="number" name="downtime_hours" id="downtime_hours" class="form-control" min="0" value="<?php echo htmlspecialchars($log['downtime_hours'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="issue_reported" class="form-label">Issue Reported</label>
                            <textarea name="issue_reported" id="issue_reported" class="form-control" rows="3"><?php echo htmlspecialchars($log['issue_reported'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="work_performed" class="form-label">Work Performed</label>
                            <textarea name="work_performed" id="work_performed" class="form-control" rows="3"><?php echo htmlspecialchars($log['work_performed'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="parts_replaced" class="form-label">Parts Replaced</label>
                            <textarea name="parts_replaced" id="parts_replaced" class="form-control" rows="3"><?php echo htmlspecialchars($log['parts_replaced'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="remarks" class="form-label">Remarks</label>
                            <textarea name="remarks" id="remarks" class="form-control" rows="3"><?php echo htmlspecialchars($log['remarks'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="next_due_date" class="form-label">Next Due Date</label>
                            <input type="date" name="next_due_date" id="next_due_date" class="form-control" value="<?php echo htmlspecialchars($log['next_due_date'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../views/footer.php'; ?>
