<?php
session_start();
require_once __DIR__ . '/../../middleware/auth_check.php';
require_once __DIR__ . '/../../config/database.php';

$pageTitle = 'Edit Department';
$pdo = getDBConnection();

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    $_SESSION['error_message'] = 'Invalid department ID.';
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM departments WHERE id = ? AND deleted_at IS NULL");
$stmt->execute([$id]);
$department = $stmt->fetch();

if (!$department) {
    $_SESSION['error_message'] = 'Department not found.';
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare("SELECT id, floor_name FROM floors WHERE deleted_at IS NULL ORDER BY floor_name");
$stmt->execute();
$floors = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error_message'] = 'Invalid CSRF token.';
        header('Location: index.php');
        exit;
    }

    $department_name = trim($_POST['department_name'] ?? '');
    $department_code = trim($_POST['department_code'] ?? '');
    $floor_id = !empty($_POST['floor_id']) ? (int)$_POST['floor_id'] : null;
    $description = trim($_POST['description'] ?? '');
    $status = $_POST['status'] ?? 'active';

    if ($department_name === '') {
        $_SESSION['error_message'] = 'Department name is required.';
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE departments SET department_name = ?, department_code = ?, floor_id = ?, description = ?, status = ? WHERE id = ?");
            $stmt->execute([$department_name, $department_code, $floor_id, $description, $status, $id]);
            $_SESSION['success_message'] = 'Department updated successfully.';
            header('Location: index.php');
            exit;
        } catch (PDOException $e) {
            $_SESSION['error_message'] = 'Error updating department: ' . $e->getMessage();
        }
    }

    $department['department_name'] = $department_name;
    $department['department_code'] = $department_code;
    $department['floor_id'] = $floor_id;
    $department['description'] = $description;
    $department['status'] = $status;
}

require_once __DIR__ . '/../../views/header.php';
require_once __DIR__ . '/../../views/sidebar.php';
?>

<div class="main-content" id="mainContent">
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="mb-0"><i class="fas fa-edit me-2"></i>Edit Department</h2>
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
                        <label for="department_name" class="form-label">Department Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="department_name" name="department_name" required value="<?php echo htmlspecialchars($department['department_name'], ENT_QUOTES, 'UTF-8'); ?>">
                    </div>

                    <div class="mb-3">
                        <label for="department_code" class="form-label">Department Code</label>
                        <input type="text" class="form-control" id="department_code" name="department_code" value="<?php echo htmlspecialchars($department['department_code'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                    </div>

                    <div class="mb-3">
                        <label for="floor_id" class="form-label">Floor</label>
                        <select class="form-select" id="floor_id" name="floor_id">
                            <option value="">-- Select Floor --</option>
                            <?php foreach ($floors as $floor): ?>
                                <option value="<?php echo $floor['id']; ?>" <?php echo ($department['floor_id'] == $floor['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($floor['floor_name'], ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($department['description'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="active" <?php echo ($department['status'] === 'active') ? 'selected' : ''; ?>>Active</option>
                            <option value="inactive" <?php echo ($department['status'] === 'inactive') ? 'selected' : ''; ?>>Inactive</option>
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
