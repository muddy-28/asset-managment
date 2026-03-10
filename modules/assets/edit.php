<?php
session_start();
require_once __DIR__ . '/../../middleware/auth_check.php';
require_once __DIR__ . '/../../config/database.php';

$pageTitle = 'Edit Asset';

$pdo = getDBConnection();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);

    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error_message'] = 'Invalid CSRF token.';
        header('Location: edit.php?id=' . $id);
        exit;
    }

    $asset_tag = trim($_POST['asset_tag'] ?? '');
    $asset_name = trim($_POST['asset_name'] ?? '');
    $model_number = trim($_POST['model_number'] ?? '');
    $serial_number = trim($_POST['serial_number'] ?? '');
    $barcode = trim($_POST['barcode'] ?? '');
    $category_id = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null;
    $subcategory_id = !empty($_POST['subcategory_id']) ? (int)$_POST['subcategory_id'] : null;
    $purchase_date = !empty($_POST['purchase_date']) ? $_POST['purchase_date'] : null;
    $purchase_cost = !empty($_POST['purchase_cost']) ? $_POST['purchase_cost'] : null;
    $vendor_id = !empty($_POST['vendor_id']) ? (int)$_POST['vendor_id'] : null;
    $warranty_expiry = !empty($_POST['warranty_expiry']) ? $_POST['warranty_expiry'] : null;
    $asset_condition = $_POST['asset_condition'] ?? 'new';
    $status = $_POST['status'] ?? 'active';

    if ($asset_tag === '' || $asset_name === '') {
        $_SESSION['error_message'] = 'Asset Tag and Asset Name are required.';
        header('Location: edit.php?id=' . $id);
        exit;
    }

    try {
        $stmt = $pdo->prepare("
            UPDATE assets SET
                asset_tag = :asset_tag,
                asset_name = :asset_name,
                model_number = :model_number,
                serial_number = :serial_number,
                barcode = :barcode,
                category_id = :category_id,
                subcategory_id = :subcategory_id,
                purchase_date = :purchase_date,
                purchase_cost = :purchase_cost,
                vendor_id = :vendor_id,
                warranty_expiry = :warranty_expiry,
                asset_condition = :asset_condition,
                status = :status
            WHERE id = :id
        ");
        $stmt->execute([
            ':asset_tag' => $asset_tag,
            ':asset_name' => $asset_name,
            ':model_number' => $model_number ?: null,
            ':serial_number' => $serial_number ?: null,
            ':barcode' => $barcode ?: null,
            ':category_id' => $category_id,
            ':subcategory_id' => $subcategory_id,
            ':purchase_date' => $purchase_date,
            ':purchase_cost' => $purchase_cost,
            ':vendor_id' => $vendor_id,
            ':warranty_expiry' => $warranty_expiry,
            ':asset_condition' => $asset_condition,
            ':status' => $status,
            ':id' => $id,
        ]);

        $_SESSION['success_message'] = 'Asset updated successfully.';
        header('Location: index.php');
        exit;
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            $_SESSION['error_message'] = 'Asset Tag already exists. Please use a unique tag.';
        } else {
            $_SESSION['error_message'] = 'Error updating asset: ' . $e->getMessage();
        }
        header('Location: edit.php?id=' . $id);
        exit;
    }
}

$stmt = $pdo->prepare("SELECT * FROM assets WHERE id = :id AND deleted_at IS NULL");
$stmt->execute([':id' => $id]);
$asset = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$asset) {
    $_SESSION['error_message'] = 'Asset not found.';
    header('Location: index.php');
    exit;
}

$categories = $pdo->query("SELECT id, category_name FROM asset_categories WHERE deleted_at IS NULL ORDER BY category_name")->fetchAll(PDO::FETCH_ASSOC);
$subcategories = $pdo->query("SELECT id, subcategory_name, category_id FROM asset_subcategories WHERE deleted_at IS NULL ORDER BY subcategory_name")->fetchAll(PDO::FETCH_ASSOC);
$vendors = $pdo->query("SELECT id, vendor_name FROM vendors WHERE deleted_at IS NULL ORDER BY vendor_name")->fetchAll(PDO::FETCH_ASSOC);

require_once __DIR__ . '/../../views/header.php';
require_once __DIR__ . '/../../views/sidebar.php';
?>

<div class="main-content" id="mainContent">
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Edit Asset</h1>
            <a href="index.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?php echo htmlspecialchars($_SESSION['error_message'], ENT_QUOTES, 'UTF-8'); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <form method="POST" action="edit.php?id=<?php echo (int)$asset['id']; ?>">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                    <input type="hidden" name="id" value="<?php echo (int)$asset['id']; ?>">

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="asset_tag" class="form-label">Asset Tag <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="asset_tag" name="asset_tag"
                                   value="<?php echo htmlspecialchars($asset['asset_tag'], ENT_QUOTES, 'UTF-8'); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="asset_name" class="form-label">Asset Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="asset_name" name="asset_name"
                                   value="<?php echo htmlspecialchars($asset['asset_name'], ENT_QUOTES, 'UTF-8'); ?>" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="model_number" class="form-label">Model Number</label>
                            <input type="text" class="form-control" id="model_number" name="model_number"
                                   value="<?php echo htmlspecialchars($asset['model_number'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="serial_number" class="form-label">Serial Number</label>
                            <input type="text" class="form-control" id="serial_number" name="serial_number"
                                   value="<?php echo htmlspecialchars($asset['serial_number'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="barcode" class="form-label">Barcode</label>
                            <input type="text" class="form-control" id="barcode" name="barcode"
                                   value="<?php echo htmlspecialchars($asset['barcode'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="category_id" class="form-label">Category</label>
                            <select class="form-select" id="category_id" name="category_id">
                                <option value="">-- Select Category --</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo (int)$cat['id']; ?>"
                                        <?php echo ($asset['category_id'] == $cat['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($cat['category_name'], ENT_QUOTES, 'UTF-8'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="subcategory_id" class="form-label">Subcategory</label>
                            <select class="form-select" id="subcategory_id" name="subcategory_id">
                                <option value="">-- Select Subcategory --</option>
                                <?php foreach ($subcategories as $sub): ?>
                                    <option value="<?php echo (int)$sub['id']; ?>"
                                        data-category="<?php echo (int)$sub['category_id']; ?>"
                                        <?php echo ($asset['subcategory_id'] == $sub['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($sub['subcategory_name'], ENT_QUOTES, 'UTF-8'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="vendor_id" class="form-label">Vendor</label>
                            <select class="form-select" id="vendor_id" name="vendor_id">
                                <option value="">-- Select Vendor --</option>
                                <?php foreach ($vendors as $vendor): ?>
                                    <option value="<?php echo (int)$vendor['id']; ?>"
                                        <?php echo ($asset['vendor_id'] == $vendor['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($vendor['vendor_name'], ENT_QUOTES, 'UTF-8'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="purchase_date" class="form-label">Purchase Date</label>
                            <input type="date" class="form-control" id="purchase_date" name="purchase_date"
                                   value="<?php echo htmlspecialchars($asset['purchase_date'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="purchase_cost" class="form-label">Purchase Cost</label>
                            <input type="number" class="form-control" id="purchase_cost" name="purchase_cost"
                                   step="0.01" min="0"
                                   value="<?php echo htmlspecialchars($asset['purchase_cost'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="warranty_expiry" class="form-label">Warranty Expiry</label>
                            <input type="date" class="form-control" id="warranty_expiry" name="warranty_expiry"
                                   value="<?php echo htmlspecialchars($asset['warranty_expiry'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="asset_condition" class="form-label">Condition</label>
                            <select class="form-select" id="asset_condition" name="asset_condition">
                                <?php
                                $conditions = ['new', 'good', 'fair', 'poor', 'damaged'];
                                foreach ($conditions as $cond): ?>
                                    <option value="<?php echo $cond; ?>"
                                        <?php echo ($asset['asset_condition'] === $cond) ? 'selected' : ''; ?>>
                                        <?php echo ucfirst($cond); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <?php
                                $statuses = ['active', 'maintenance', 'disposed', 'lost'];
                                foreach ($statuses as $st): ?>
                                    <option value="<?php echo $st; ?>"
                                        <?php echo ($asset['status'] === $st) ? 'selected' : ''; ?>>
                                        <?php echo ucfirst($st); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Asset
                        </button>
                        <a href="index.php" class="btn btn-secondary ms-2">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('category_id').addEventListener('change', function() {
    const categoryId = this.value;
    const subcategorySelect = document.getElementById('subcategory_id');
    const options = subcategorySelect.querySelectorAll('option[data-category]');

    options.forEach(function(option) {
        if (categoryId === '' || option.getAttribute('data-category') === categoryId) {
            option.style.display = '';
        } else {
            option.style.display = 'none';
            if (option.selected) {
                subcategorySelect.value = '';
            }
        }
    });
});
</script>

<?php require_once __DIR__ . '/../../views/footer.php'; ?>
