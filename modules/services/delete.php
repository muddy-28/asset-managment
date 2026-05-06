<?php
session_start();
require_once __DIR__ . '/../../middleware/auth_check.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/activity_logger.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['error_message'] = 'Invalid CSRF token.';
    header('Location: index.php');
    exit;
}

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    $_SESSION['error_message'] = 'Invalid ID.';
    header('Location: index.php');
    exit;
}

$pdo = getDBConnection();

try {
    // Fetch record to remove associated image file on delete
    $stmt = $pdo->prepare("SELECT img FROM services WHERE id = ? AND is_deleted = 0");
    $stmt->execute([$id]);
    $service = $stmt->fetch();

    if ($service && !empty($service['img'])) {
        $imgFile = __DIR__ . '/../../' . $service['img'];
        if (file_exists($imgFile)) {
            unlink($imgFile);
        }
    }

    $stmt = $pdo->prepare("UPDATE services SET is_deleted = 1 WHERE id = ? AND is_deleted = 0");
    $stmt->execute([$id]);
    logActivity($pdo, 'delete', 'services', $id, 'Deleted service ID ' . $id);
    $_SESSION['success_message'] = 'Service deleted successfully.';
} catch (PDOException $e) {
    $_SESSION['error_message'] = 'Cannot delete: ' . $e->getMessage();
}

header('Location: index.php');
exit;
