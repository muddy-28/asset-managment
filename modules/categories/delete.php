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
    $stmt = $pdo->prepare("UPDATE asset_categories SET is_deleted = 1 WHERE id = ? AND is_deleted = 0");
    $stmt->execute([$id]);
    $_SESSION['success_message'] = 'Category deleted successfully.';
    logActivity($pdo, 'delete', 'categories', $id, 'Deleted category ID ' . $id);
} catch (PDOException $e) {
    $_SESSION['error_message'] = 'Cannot delete: this record may be referenced by other data.';
}

header('Location: index.php');
exit;
