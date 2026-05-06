<?php
session_start();
require_once __DIR__ . '/../../middleware/auth_check.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/activity_logger.php';

$pageTitle = 'Add Service';
$pdo = getDBConnection();

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
        $imgPath = null;
        if (isset($_FILES['img']) && $_FILES['img']['error'] === UPLOAD_ERR_OK) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->file($_FILES['img']['tmp_name']);

            if (!in_array($mimeType, $allowedTypes)) {
                $_SESSION['error_message'] = 'Invalid image type. Only JPG, PNG, and WebP are allowed.';
                header('Location: create.php');
                exit;
            }

            $uploadDir = __DIR__ . '/../../assets/uploads/services/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $ext      = pathinfo($_FILES['img']['name'], PATHINFO_EXTENSION);
            $fileName = 'service_' . uniqid() . '.' . strtolower($ext);
            move_uploaded_file($_FILES['img']['tmp_name'], $uploadDir . $fileName);
            $imgPath = 'assets/uploads/services/' . $fileName;
        }

        $stmt = $pdo->prepare("INSERT INTO services (title, description, img) VALUES (?, ?, ?)");
        $stmt->execute([$title, $description, $imgPath]);
        $newId = (int)$pdo->lastInsertId();
        logActivity($pdo, 'create', 'services', $newId, 'Created service: ' . $title);
        $_SESSION['success_message'] = 'Service created successfully.';
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
                <h2 class="mb-0"><i class="fas fa-plus me-2"></i>Add Service</h2>
            </div>
        </div>
        <div class="card shadow-sm">
            <div class="card-body">
                <form method="POST" action="" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                    <div class="mb-3">
                        <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($_POST['title'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="img" class="form-label">Image</label>
                        <input type="file" class="form-control" id="img" name="img" accept="image/jpeg,image/png,image/webp">
                        <div class="form-text">Accepted formats: JPG, PNG, WebP. Max 4 MB.</div>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="4" placeholder="Enter service description"><?php echo htmlspecialchars($_POST['description'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
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
