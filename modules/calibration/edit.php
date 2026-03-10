<?php
session_start();
require_once __DIR__ . '/../../middleware/auth_check.php';
require_once __DIR__ . '/../../config/database.php';

$pdo = getDBConnection();

$pageTitle = 'Edit Calibration';

if (!isset($_GET['id'])) {
    $_SESSION['error_message'] = 'Invalid request.';
    header('Location: index.php');
    exit;
}

$id = (int)$_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM asset_calibration WHERE id = ? AND is_deleted = 0");
$stmt->execute([$id]);
$calibration = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$calibration) {
    $_SESSION['error_message'] = 'Calibration record not found.';
    header('Location: index.php');
    exit;
}

$assets = $pdo->query("SELECT id, asset_name FROM assets WHERE is_deleted = 0 ORDER BY asset_name")->fetchAll(PDO::FETCH_ASSOC);
$vendors = $pdo->query("SELECT id, vendor_name FROM vendors WHERE is_deleted = 0 ORDER BY vendor_name")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error_message'] = 'Invalid CSRF token.';
        header('Location: index.php');
        exit;
    }

    $asset_id = (int)$_POST['asset_id'];
    $calibration_date = $_POST['calibration_date'];
    $calibration_due = $_POST['calibration_due'];
    $certificate_number = trim($_POST['certificate_number']);
    $vendor_id = !empty($_POST['vendor_id']) ? (int)$_POST['vendor_id'] : null;
    $remarks = trim($_POST['remarks']);

    $stmt = $pdo->prepare("
        UPDATE asset_calibration
        SET asset_id = ?, calibration_date = ?, calibration_due = ?, certificate_number = ?, vendor_id = ?, remarks = ?
        WHERE id = ?
    ");
    $stmt->execute([$asset_id, $calibration_date, $calibration_due, $certificate_number, $vendor_id, $remarks, $id]);

    $_SESSION['success_message'] = 'Calibration record updated successfully.';
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
                                    <option value="<?php echo (int)$asset['id']; ?>" <?php echo ($asset['id'] == $calibration['asset_id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($asset['asset_name'], ENT_QUOTES, 'UTF-8'); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="certificate_number" class="form-label">Certificate Number</label>
                            <input type="text" name="certificate_number" id="certificate_number" class="form-control" value="<?php echo htmlspecialchars($calibration['certificate_number'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="calibration_date" class="form-label">Calibration Date <span class="text-danger">*</span></label>
                            <input type="date" name="calibration_date" id="calibration_date" class="form-control" required value="<?php echo htmlspecialchars($calibration['calibration_date'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="calibration_due" class="form-label">Calibration Due Date <span class="text-danger">*</span></label>
                            <input type="date" name="calibration_due" id="calibration_due" class="form-control" required value="<?php echo htmlspecialchars($calibration['calibration_due'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="vendor_id" class="form-label">Vendor</label>
                            <select name="vendor_id" id="vendor_id" class="form-select">
                                <option value="">Select Vendor</option>
                                <?php foreach ($vendors as $vendor): ?>
                                    <option value="<?php echo (int)$vendor['id']; ?>" <?php echo ($vendor['id'] == $calibration['vendor_id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($vendor['vendor_name'], ENT_QUOTES, 'UTF-8'); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="remarks" class="form-label">Remarks</label>
                        <textarea name="remarks" id="remarks" class="form-control" rows="3"><?php echo htmlspecialchars($calibration['remarks'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../views/footer.php'; ?>
