<?php
session_start();
require_once __DIR__ . '/../../middleware/auth_check.php';
require_once __DIR__ . '/../../config/database.php';

$pageTitle = 'Add Maintenance Schedule';

$assets = $pdo->query("SELECT id, asset_name FROM assets ORDER BY asset_name")->fetchAll(PDO::FETCH_ASSOC);
$departments = $pdo->query("SELECT id, department_name FROM departments ORDER BY department_name")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error_message'] = 'Invalid CSRF token.';
        header('Location: index.php');
        exit;
    }

    $asset_id = (int)$_POST['asset_id'];
    $maintenance_type = $_POST['maintenance_type'];
    $frequency_value = (int)$_POST['frequency_value'];
    $frequency_unit = $_POST['frequency_unit'];
    $last_maintenance_date = $_POST['last_maintenance_date'];
    $next_due_date = $_POST['next_due_date'];
    $reminder_days_before = (int)$_POST['reminder_days_before'];
    $responsible_department = !empty($_POST['responsible_department']) ? (int)$_POST['responsible_department'] : null;
    $responsible_person = trim($_POST['responsible_person']);
    $status = $_POST['status'];

    $stmt = $pdo->prepare("
        INSERT INTO asset_maintenance_schedule
        (asset_id, maintenance_type, frequency_value, frequency_unit, last_maintenance_date, next_due_date, reminder_days_before, responsible_department, responsible_person, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([$asset_id, $maintenance_type, $frequency_value, $frequency_unit, $last_maintenance_date, $next_due_date, $reminder_days_before, $responsible_department, $responsible_person, $status]);

    $_SESSION['success_message'] = 'Maintenance schedule created successfully.';
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
                            <label for="maintenance_type" class="form-label">Maintenance Type <span class="text-danger">*</span></label>
                            <select name="maintenance_type" id="maintenance_type" class="form-select" required>
                                <option value="preventive">Preventive</option>
                                <option value="calibration">Calibration</option>
                                <option value="inspection">Inspection</option>
                                <option value="cleaning">Cleaning</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="frequency_value" class="form-label">Frequency Value <span class="text-danger">*</span></label>
                            <input type="number" name="frequency_value" id="frequency_value" class="form-control" min="1" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="frequency_unit" class="form-label">Frequency Unit <span class="text-danger">*</span></label>
                            <select name="frequency_unit" id="frequency_unit" class="form-select" required>
                                <option value="days">Days</option>
                                <option value="weeks">Weeks</option>
                                <option value="months">Months</option>
                                <option value="years">Years</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="last_maintenance_date" class="form-label">Last Maintenance Date</label>
                            <input type="date" name="last_maintenance_date" id="last_maintenance_date" class="form-control">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="next_due_date" class="form-label">Next Due Date</label>
                            <input type="date" name="next_due_date" id="next_due_date" class="form-control">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="reminder_days_before" class="form-label">Reminder Days Before</label>
                            <input type="number" name="reminder_days_before" id="reminder_days_before" class="form-control" value="7" min="0">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="responsible_department" class="form-label">Responsible Department</label>
                            <select name="responsible_department" id="responsible_department" class="form-select">
                                <option value="">Select Department</option>
                                <?php foreach ($departments as $dept): ?>
                                    <option value="<?php echo (int)$dept['id']; ?>"><?php echo htmlspecialchars($dept['department_name'], ENT_QUOTES, 'UTF-8'); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="responsible_person" class="form-label">Responsible Person</label>
                            <input type="text" name="responsible_person" id="responsible_person" class="form-control">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select name="status" id="status" class="form-select">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../views/footer.php'; ?>
