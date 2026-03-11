<?php
session_start();
require_once __DIR__ . '/../../middleware/auth_check.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/activity_logger.php';

$pageTitle = 'Edit Assignment';
$pdo = getDBConnection();

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    $_SESSION['error_message'] = 'Invalid assignment ID.';
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM asset_assignments WHERE id = ? AND is_deleted = 0");
$stmt->execute([$id]);
$assignment = $stmt->fetch();

if (!$assignment) {
    $_SESSION['error_message'] = 'Assignment not found.';
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare("SELECT id, asset_name, asset_tag FROM assets WHERE is_deleted = 0 ORDER BY asset_name");
$stmt->execute();
$assets = $stmt->fetchAll();

$stmt = $pdo->prepare("SELECT id, floor_name FROM floors WHERE is_deleted = 0 ORDER BY floor_name");
$stmt->execute();
$floors = $stmt->fetchAll();

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
    $floor_id = !empty($_POST['floor_id']) ? (int)$_POST['floor_id'] : null;
    $department_id = !empty($_POST['department_id']) ? (int)$_POST['department_id'] : null;
    $location_id = !empty($_POST['location_id']) ? (int)$_POST['location_id'] : null;
    $assigned_date = trim($_POST['assigned_date'] ?? '');
    $assigned_by = trim($_POST['assigned_by'] ?? '');
    $status = $_POST['status'] ?? 'active';

    if (!$asset_id) {
        $_SESSION['error_message'] = 'Please select an asset.';
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE asset_assignments SET asset_id = ?, floor_id = ?, department_id = ?, location_id = ?, assigned_date = ?, assigned_by = ?, status = ? WHERE id = ?");
            $stmt->execute([$asset_id, $floor_id, $department_id, $location_id, $assigned_date ?: null, $assigned_by, $status, $id]);
            $_SESSION['success_message'] = 'Assignment updated successfully.';
            logActivity($pdo, 'update', 'assignments', $id, 'Updated assignment ID ' . $id);
            header('Location: index.php');
            exit;
        } catch (PDOException $e) {
            $_SESSION['error_message'] = 'Error updating assignment: ' . $e->getMessage();
        }
    }

    $assignment['asset_id'] = $asset_id;
    $assignment['floor_id'] = $floor_id;
    $assignment['department_id'] = $department_id;
    $assignment['location_id'] = $location_id;
    $assignment['assigned_date'] = $assigned_date;
    $assignment['assigned_by'] = $assigned_by;
    $assignment['status'] = $status;
}

require_once __DIR__ . '/../../views/header.php';
require_once __DIR__ . '/../../views/sidebar.php';
?>

<div class="main-content" id="mainContent">
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="mb-0"><i class="fas fa-edit me-2"></i>Edit Assignment</h2>
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
                <form method="POST" action="edit.php?id=<?php echo $id; ?>">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

                    <div class="mb-3">
                        <label for="asset_id" class="form-label">Asset <span class="text-danger">*</span></label>
                        <select class="form-select" id="asset_id" name="asset_id" required>
                            <option value="">-- Select Asset --</option>
                            <?php foreach ($assets as $asset): ?>
                                <option value="<?php echo $asset['id']; ?>" <?php echo ($assignment['asset_id'] == $asset['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($asset['asset_name'] . ' (' . $asset['asset_tag'] . ')', ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="floor_id" class="form-label">Floor</label>
                        <select class="form-select" id="floor_id" name="floor_id">
                            <option value="">-- Select Floor --</option>
                            <?php foreach ($floors as $floor): ?>
                                <option value="<?php echo $floor['id']; ?>" <?php echo ($assignment['floor_id'] == $floor['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($floor['floor_name'], ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="department_id" class="form-label">Department</label>
                        <select class="form-select" id="department_id" name="department_id">
                            <option value="">-- Select Department --</option>
                            <?php foreach ($departments as $dept): ?>
                                <option value="<?php echo $dept['id']; ?>" <?php echo ($assignment['department_id'] == $dept['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($dept['department_name'], ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="location_id" class="form-label">Location</label>
                        <select class="form-select" id="location_id" name="location_id">
                            <option value="">-- Select Location --</option>
                            <?php foreach ($locations as $loc): ?>
                                <option value="<?php echo $loc['id']; ?>" <?php echo ($assignment['location_id'] == $loc['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($loc['location_name'], ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="assigned_date" class="form-label">Assigned Date</label>
                        <input type="date" class="form-control" id="assigned_date" name="assigned_date" value="<?php echo htmlspecialchars($assignment['assigned_date'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                    </div>

                    <div class="mb-3">
                        <label for="assigned_by" class="form-label">Assigned By</label>
                        <input type="text" class="form-control" id="assigned_by" name="assigned_by" value="<?php echo htmlspecialchars($assignment['assigned_by'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="active" <?php echo ($assignment['status'] === 'active') ? 'selected' : ''; ?>>Active</option>
                            <option value="moved" <?php echo ($assignment['status'] === 'moved') ? 'selected' : ''; ?>>Moved</option>
                        </select>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Update</button>
                        <a href="index.php" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i>Back</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../views/footer.php'; ?>
