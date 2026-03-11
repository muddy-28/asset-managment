<?php
session_start();
require_once __DIR__ . '/../../middleware/auth_check.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/activity_logger.php';

$pageTitle = 'Add Floor';
$pdo = getDBConnection();

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
        $stmt = $pdo->prepare("INSERT INTO floors (floor_name, floor_code, building) VALUES (?, ?, ?)");
        $stmt->execute([$floor_name, $floor_code, $building]);
        $_SESSION['success_message'] = 'Floor created successfully.';
        $newId = (int)$pdo->lastInsertId();
        logActivity($pdo, 'create', 'floors', $newId, 'Created floor: ' . $floor_name);
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
                <h2 class="mb-0"><i class="fas fa-plus me-2"></i>Add Floor</h2>
            </div>
        </div>
        <div class="card shadow-sm">
            <div class="card-body">
                <form method="POST" action="">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                    <div class="mb-3">
                        <label for="floor_name" class="form-label">Floor Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="floor_name" name="floor_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="floor_code" class="form-label">Floor Code</label>
                        <input type="text" class="form-control" id="floor_code" name="floor_code">
                    </div>
                    <div class="mb-3">
                        <label for="building" class="form-label">Building</label>
                        <input type="text" class="form-control" id="building" name="building">
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Save</button>
                        <a href="index.php" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../views/footer.php'; ?>
