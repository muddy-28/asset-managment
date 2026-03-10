<?php
session_start();
require_once __DIR__ . '/../../middleware/auth_check.php';
require_once __DIR__ . '/../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error_message'] = 'Invalid request method.';
    header('Location: index.php');
    exit;
}

if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['error_message'] = 'Invalid CSRF token.';
    header('Location: index.php');
    exit;
}

$pdo = getDBConnection();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    $_SESSION['error_message'] = 'Invalid asset ID.';
    header('Location: index.php');
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE assets SET is_deleted = 1 WHERE id = :id AND is_deleted = 0");
    $stmt->execute([':id' => $id]);

    if ($stmt->rowCount() > 0) {
        $_SESSION['success_message'] = 'Asset deleted successfully.';
    } else {
        $_SESSION['error_message'] = 'Asset not found.';
    }
} catch (PDOException $e) {
    if ($e->getCode() == 23000) {
        $_SESSION['error_message'] = 'Cannot delete this asset because it has related records (assignments or maintenance logs). Please remove those first.';
    } else {
        $_SESSION['error_message'] = 'Error deleting asset: ' . $e->getMessage();
    }
}

header('Location: index.php');
exit;
