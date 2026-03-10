<?php
session_start();
require_once __DIR__ . '/../../middleware/auth_check.php';
require_once __DIR__ . '/../../config/database.php';

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
    $_SESSION['error_message'] = 'Invalid assignment ID.';
    header('Location: index.php');
    exit;
}

$pdo = getDBConnection();

try {
    $stmt = $pdo->prepare("UPDATE asset_assignments SET is_deleted = 1 WHERE id = ? AND is_deleted = 0");
    $stmt->execute([$id]);
    $_SESSION['success_message'] = 'Assignment deleted successfully.';
} catch (PDOException $e) {
    $_SESSION['error_message'] = 'Cannot delete: this assignment may be referenced by other data.';
}

header('Location: index.php');
exit;
