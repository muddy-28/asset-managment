<?php
session_start();
require_once __DIR__ . '/../../middleware/auth_check.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/activity_logger.php';

$pageTitle = 'New Transfer';
$pdo = getDBConnection();

$stmt = $pdo->prepare("SELECT id, asset_name, asset_tag FROM assets WHERE is_deleted = 0 ORDER BY asset_name");
$stmt->execute();
$assets = $stmt->fetchAll();

$stmt = $pdo->prepare("SELECT id, department_name FROM departments WHERE is_deleted = 0 ORDER BY department_name");
$stmt->execute();
$departments = $stmt->fetchAll();

$stmt = $pdo->prepare("SELECT id, location_name FROM locations WHERE is_deleted = 0 ORDER BY location_name");
$stmt->execute();
$locations = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error_message'] = 'Invalid CSRF token.';
        header('Location: index.php');
        exit;
    }

    $asset_id = !empty($_POST['asset_id']) ? (int)$_POST['asset_id'] : null;
    $from_department = !empty($_POST['from_department']) ? (int)$_POST['from_department'] : null;
    $to_department = !empty($_POST['to_department']) ? (int)$_POST['to_department'] : null;
    $from_location = !empty($_POST['from_location']) ? (int)$_POST['from_location'] : null;
    $to_location = !empty($_POST['to_location']) ? (int)$_POST['to_location'] : null;
    $transfer_date = trim($_POST['transfer_date'] ?? '');
    $transferred_by = trim($_POST['transferred_by'] ?? '');
    $remarks = trim($_POST['remarks'] ?? '');

    if (!$asset_id) {
        $_SESSION['error_message'] = 'Please select an asset.';
    } else {
        try {
            $pdo->beginTransaction();

            // 1. Insert into asset_transfer_history
            $stmt = $pdo->prepare("INSERT INTO asset_transfer_history (asset_id, from_department, to_department, from_location, to_location, transfer_date, transferred_by, remarks) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$asset_id, $from_department, $to_department, $from_location, $to_location, $transfer_date !== '' ? $transfer_date : null, $transferred_by, $remarks]);
            $newId = (int)$pdo->lastInsertId();

            // 2. Mark existing active assignment for this asset as 'moved'
            $stmt = $pdo->prepare("UPDATE asset_assignments SET status = 'moved' WHERE asset_id = ? AND status = 'active'");
            $stmt->execute([$asset_id]);

            // 3. Look up floor_id from the to_location's floor, or from to_department's floor
            $floor_id = null;
            if ($to_location) {
                $stmt = $pdo->prepare("SELECT floor_id FROM locations WHERE id = ?");
                $stmt->execute([$to_location]);
                $locRow = $stmt->fetch();
                if ($locRow && $locRow['floor_id']) {
                    $floor_id = (int)$locRow['floor_id'];
                }
            }
            if (!$floor_id && $to_department) {
                $stmt = $pdo->prepare("SELECT floor_id FROM departments WHERE id = ?");
                $stmt->execute([$to_department]);
                $deptRow = $stmt->fetch();
                if ($deptRow && $deptRow['floor_id']) {
                    $floor_id = (int)$deptRow['floor_id'];
                }
            }

            // 4. Insert new active assignment with destination info
            $stmt = $pdo->prepare("INSERT INTO asset_assignments (asset_id, floor_id, department_id, location_id, assigned_date, assigned_by, status) VALUES (?, ?, ?, ?, ?, ?, 'active')");
            $stmt->execute([$asset_id, $floor_id, $to_department, $to_location, $transfer_date ?: null, $transferred_by]);

            $pdo->commit();
            $_SESSION['success_message'] = 'Transfer recorded successfully.';
            logActivity($pdo, 'create', 'transfers', $newId, 'Created transfer for asset ID ' . $asset_id);
            header('Location: index.php');
            exit;
        } catch (PDOException $e) {
            $pdo->rollBack();
            $_SESSION['error_message'] = 'Error recording transfer: ' . $e->getMessage();
        }
    }
}

require_once __DIR__ . '/../../views/header.php';
require_once __DIR__ . '/../../views/sidebar.php';
?>

<div class="main-content" id="mainContent">
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="mb-0"><i class="fas fa-exchange-alt me-2"></i>New Transfer</h2>
            </div>
        </div>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?php echo htmlspecialchars($_SESSION['error_message'], ENT_QUOTES, 'UTF-8'); unset($_SESSION['error_message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm">
            <div class="card-body">
                <form method="POST" action="">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

                    <div class="mb-3">
                        <label for="asset_id" class="form-label">Asset <span class="text-danger">*</span></label>
                        <select class="form-select" id="asset_id" name="asset_id" required>
                            <option value="">-- Select Asset --</option>
                            <?php foreach ($assets as $asset): ?>
                                <option value="<?php echo $asset['id']; ?>" <?php echo (isset($_POST['asset_id']) && $_POST['asset_id'] == $asset['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($asset['asset_name'] . ' (' . $asset['asset_tag'] . ')', ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="from_department" class="form-label">From Department</label>
                            <select class="form-select" id="from_department" name="from_department">
                                <option value="">-- Select Department --</option>
                                <?php foreach ($departments as $dept): ?>
                                    <option value="<?php echo $dept['id']; ?>" <?php echo (isset($_POST['from_department']) && $_POST['from_department'] == $dept['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($dept['department_name'], ENT_QUOTES, 'UTF-8'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="to_department" class="form-label">To Department</label>
                            <select class="form-select" id="to_department" name="to_department">
                                <option value="">-- Select Department --</option>
                                <?php foreach ($departments as $dept): ?>
                                    <option value="<?php echo $dept['id']; ?>" <?php echo (isset($_POST['to_department']) && $_POST['to_department'] == $dept['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($dept['department_name'], ENT_QUOTES, 'UTF-8'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="from_location" class="form-label">From Location</label>
                            <select class="form-select" id="from_location" name="from_location">
                                <option value="">-- Select Location --</option>
                                <?php foreach ($locations as $loc): ?>
                                    <option value="<?php echo $loc['id']; ?>" <?php echo (isset($_POST['from_location']) && $_POST['from_location'] == $loc['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($loc['location_name'], ENT_QUOTES, 'UTF-8'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="to_location" class="form-label">To Location</label>
                            <select class="form-select" id="to_location" name="to_location">
                                <option value="">-- Select Location --</option>
                                <?php foreach ($locations as $loc): ?>
                                    <option value="<?php echo $loc['id']; ?>" <?php echo (isset($_POST['to_location']) && $_POST['to_location'] == $loc['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($loc['location_name'], ENT_QUOTES, 'UTF-8'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="transfer_date" class="form-label">Transfer Date</label>
                        <input type="date" class="form-control" id="transfer_date" name="transfer_date" value="<?php echo htmlspecialchars($_POST['transfer_date'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                    </div>

                    <div class="mb-3">
                        <label for="transferred_by" class="form-label">Transferred By</label>
                        <input type="text" class="form-control" id="transferred_by" name="transferred_by" value="<?php echo htmlspecialchars($_POST['transferred_by'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                    </div>

                    <div class="mb-3">
                        <label for="remarks" class="form-label">Remarks</label>
                        <textarea class="form-control" id="remarks" name="remarks" rows="3"><?php echo htmlspecialchars($_POST['remarks'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Record Transfer</button>
                        <a href="index.php" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i>Back</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../views/footer.php'; ?>
