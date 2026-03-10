<?php
session_start();
require_once __DIR__ . '/../../middleware/auth_check.php';
require_once __DIR__ . '/../../config/database.php';

$pageTitle = 'Edit Floor';
$pdo = getDBConnection();

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM floors WHERE id = ? AND is_deleted = 0");
$stmt->execute([$id]);
$floor = $stmt->fetch();
if (!$floor) {
    $_SESSION['error_message'] = 'Floor not found.';
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error_message'] = 'Invalid CSRF token.';
        header('Location: index.php');
        exit;
    }
    $floor_name = trim($_POST['floor_name'] ?? '');
    $floor_code = trim($_POST['floor_code'] ?? '');
    $building = trim($_POST['building'] ?? '');
    if ($floor_name === '') {
        $_SESSION['error_message'] = 'Floor name is required.';
    } else {
        $stmt = $pdo->prepare("UPDATE floors SET floor_name = ?, floor_code = ?, building = ? WHERE id = ?");
        $stmt->execute([$floor_name, $floor_code, $building, $id]);
        $_SESSION['success_message'] = 'Floor updated successfully.';
        header('Location: index.php');
        exit;
    }
}

require_once __DIR__ . '/../../views/header.php';
require_once __DIR__ . '/../../views/sidebar.php';
?>

<div class="main-content" id="mainContent">
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="mb-0"><i class="fas fa-edit me-2"></i>Edit Floor</h2>
            </div>
        </div>
        <div class="card shadow-sm">
            <div class="card-body">
                <form method="POST" action="">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                    <div class="mb-3">
                        <label for="floor_name" class="form-label">Floor Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="floor_name" name="floor_name" value="<?php echo htmlspecialchars($floor['floor_name'], ENT_QUOTES, 'UTF-8'); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="floor_code" class="form-label">Floor Code</label>
                        <input type="text" class="form-control" id="floor_code" name="floor_code" value="<?php echo htmlspecialchars($floor['floor_code'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="building" class="form-label">Building</label>
                        <input type="text" class="form-control" id="building" name="building" value="<?php echo htmlspecialchars($floor['building'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Update</button>
                        <a href="index.php" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../views/footer.php'; ?>
