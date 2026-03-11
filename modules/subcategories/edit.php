<?php
session_start();
require_once __DIR__ . '/../../middleware/auth_check.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/activity_logger.php';

$pageTitle = 'Edit Subcategory';
$pdo = getDBConnection();

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM asset_subcategories WHERE id = ? AND is_deleted = 0");
$stmt->execute([$id]);
$subcategory = $stmt->fetch();
if (!$subcategory) {
    $_SESSION['error_message'] = 'Subcategory not found.';
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare("SELECT id, category_name FROM asset_categories WHERE is_deleted = 0 ORDER BY category_name ASC");
$stmt->execute();
$categories = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error_message'] = 'Invalid CSRF token.';
        header('Location: index.php');
        exit;
    }
    $subcategory_name = trim($_POST['subcategory_name'] ?? '');
    $category_id = (int)($_POST['category_id'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    if ($subcategory_name === '') {
        $_SESSION['error_message'] = 'Subcategory name is required.';
    } else {
        $stmt = $pdo->prepare("UPDATE asset_subcategories SET subcategory_name = ?, category_id = ?, description = ? WHERE id = ?");
        $stmt->execute([$subcategory_name, $category_id, $description, $id]);
        $_SESSION['success_message'] = 'Subcategory updated successfully.';
        logActivity($pdo, 'update', 'subcategories', $id, 'Updated subcategory ID ' . $id . ': ' . $subcategory_name);
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
                <h2 class="mb-0"><i class="fas fa-edit me-2"></i>Edit Subcategory</h2>
            </div>
        </div>
        <div class="card shadow-sm">
            <div class="card-body">
                <form method="POST" action="">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                    <div class="mb-3">
                        <label for="subcategory_name" class="form-label">Subcategory Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="subcategory_name" name="subcategory_name" value="<?php echo htmlspecialchars($subcategory['subcategory_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="category_id" class="form-label">Category</label>
                        <select class="form-select" id="category_id" name="category_id">
                            <option value="0">-- Select Category --</option>
                            <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>" <?php echo ($cat['id'] == $subcategory['category_id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($cat['category_name'], ENT_QUOTES, 'UTF-8'); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($subcategory['description'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
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
