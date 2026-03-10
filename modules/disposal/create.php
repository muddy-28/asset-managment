<?php
session_start();
require_once __DIR__ . '/../../middleware/auth_check.php';
require_once __DIR__ . '/../../config/database.php';

$pdo = getDBConnection();

$pageTitle = 'Add Asset Disposal';

$assets = $pdo->query("SELECT id, asset_name FROM assets WHERE status = 'active' AND deleted_at IS NULL ORDER BY asset_name")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error_message'] = 'Invalid CSRF token.';
        header('Location: index.php');
        exit;
    }

    $asset_id = (int)$_POST['asset_id'];
    $disposal_date = $_POST['disposal_date'];
    $disposal_method = trim($_POST['disposal_method']);
    $remarks = trim($_POST['remarks']);
    $approved_by = trim($_POST['approved_by']);

    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare("
            INSERT INTO asset_disposal (asset_id, disposal_date, disposal_method, remarks, approved_by)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([$asset_id, $disposal_date, $disposal_method, $remarks, $approved_by]);

        $stmt = $pdo->prepare("UPDATE assets SET status = 'disposed' WHERE id = ?");
        $stmt->execute([$asset_id]);

        $pdo->commit();
        $_SESSION['success_message'] = 'Asset disposal recorded successfully.';
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['error_message'] = 'Error recording disposal.';
    }

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
                            <label for="disposal_date" class="form-label">Disposal Date <span class="text-danger">*</span></label>
                            <input type="date" name="disposal_date" id="disposal_date" class="form-control" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="disposal_method" class="form-label">Disposal Method <span class="text-danger">*</span></label>
                            <input type="text" name="disposal_method" id="disposal_method" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="approved_by" class="form-label">Approved By</label>
                            <input type="text" name="approved_by" id="approved_by" class="form-control">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="remarks" class="form-label">Remarks</label>
                        <textarea name="remarks" id="remarks" class="form-control" rows="3"></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../views/footer.php'; ?>
