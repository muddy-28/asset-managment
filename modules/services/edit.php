<?php
session_start();
require_once __DIR__ . '/../../middleware/auth_check.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/activity_logger.php';

$pageTitle = 'Edit Service';
$pdo = getDBConnection();

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM services WHERE id = ? AND is_deleted = 0");
$stmt->execute([$id]);
$service = $stmt->fetch();

if (!$service) {
    $_SESSION['error_message'] = 'Service not found.';
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error_message'] = 'Invalid CSRF token.';
        header('Location: index.php');
        exit;
    }

    $title       = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if ($title === '') {
        $_SESSION['error_message'] = 'Service title is required.';
    } else {
        $imgPath = $service['img'];

        if (isset($_FILES['img']) && $_FILES['img']['error'] === UPLOAD_ERR_OK) {
            $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
            $ext     = strtolower(pathinfo($_FILES['img']['name'], PATHINFO_EXTENSION));

            if (!in_array($ext, $allowed, true)) {
                $_SESSION['error_message'] = 'Invalid image format. Allowed: jpg, jpeg, png, webp, gif.';
                header("Location: edit.php?id={$id}");
                exit;
            }

            $uploadDir = __DIR__ . '/../../assets/uploads/services/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            // Remove the old image if it exists
            if (!empty($service['img'])) {
                $oldFile = __DIR__ . '/../../' . $service['img'];
                if (file_exists($oldFile)) {
                    unlink($oldFile);
                }
            }

            $fileName = 'service_' . $id . '_' . time() . '.' . $ext;
            if (move_uploaded_file($_FILES['img']['tmp_name'], $uploadDir . $fileName)) {
                $imgPath = 'assets/uploads/services/' . $fileName;
            }
        }

        $stmt = $pdo->prepare("UPDATE services SET title = ?, description = ?, img = ? WHERE id = ?");
        $stmt->execute([$title, $description ?: null, $imgPath, $id]);
        logActivity($pdo, 'update', 'services', $id, 'Updated service ID ' . $id . ': ' . $title);
        $_SESSION['success_message'] = 'Service updated successfully.';
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
                <h2 class="mb-0"><i class="fas fa-edit me-2"></i>Edit Service</h2>
            </div>
        </div>
        <div class="card shadow-sm">
            <div class="card-body">
                <form method="POST" action="" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
                    <div class="mb-3">
                        <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="title" name="title"
                               value="<?php echo htmlspecialchars($_POST['title'] ?? $service['title'], ENT_QUOTES, 'UTF-8'); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="4"
                                  placeholder="Enter service description"><?php echo htmlspecialchars($_POST['description'] ?? ($service['description'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="img" class="form-label">Image</label>
                        <?php if (!empty($service['img'])): ?>
                            <div class="mb-2">
                                <img src="<?php echo htmlspecialchars(BASE_URL . '/' . $service['img'], ENT_QUOTES, 'UTF-8'); ?>"
                                     alt="Current image"
                                     style="width:80px;height:80px;object-fit:cover;border-radius:4px;">
                                <small class="text-muted ms-2">Current image</small>
                            </div>
                        <?php endif; ?>
                        <input type="file" class="form-control" id="img" name="img" accept="image/*">
                        <div class="form-text">Upload a new image to replace the current one. Accepted formats: jpg, jpeg, png, webp, gif.</div>
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
