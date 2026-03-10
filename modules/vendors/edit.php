<?php
session_start();
require_once __DIR__ . '/../../middleware/auth_check.php';
require_once __DIR__ . '/../../config/database.php';

$pageTitle = 'Edit Vendor';
$pdo = getDBConnection();

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM vendors WHERE id = ? AND deleted_at IS NULL");
$stmt->execute([$id]);
$vendor = $stmt->fetch();
if (!$vendor) {
    $_SESSION['error_message'] = 'Vendor not found.';
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error_message'] = 'Invalid CSRF token.';
        header('Location: index.php');
        exit;
    }
    $vendor_name = trim($_POST['vendor_name'] ?? '');
    $contact_person = trim($_POST['contact_person'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $address = trim($_POST['address'] ?? '');
    if ($vendor_name === '') {
        $_SESSION['error_message'] = 'Vendor name is required.';
    } else {
        $stmt = $pdo->prepare("UPDATE vendors SET vendor_name = ?, contact_person = ?, phone = ?, email = ?, address = ? WHERE id = ?");
        $stmt->execute([$vendor_name, $contact_person, $phone, $email, $address, $id]);
        $_SESSION['success_message'] = 'Vendor updated successfully.';
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
                <h2 class="mb-0"><i class="fas fa-edit me-2"></i>Edit Vendor</h2>
            </div>
        </div>
        <div class="card shadow-sm">
            <div class="card-body">
                <form method="POST" action="">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                    <div class="mb-3">
                        <label for="vendor_name" class="form-label">Vendor Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="vendor_name" name="vendor_name" value="<?php echo htmlspecialchars($vendor['vendor_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="contact_person" class="form-label">Contact Person</label>
                        <input type="text" class="form-control" id="contact_person" name="contact_person" value="<?php echo htmlspecialchars($vendor['contact_person'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($vendor['phone'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($vendor['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control" id="address" name="address" rows="3"><?php echo htmlspecialchars($vendor['address'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
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
