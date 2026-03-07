<?php
session_start();
require_once __DIR__ . '/../../middleware/auth_check.php';
checkRole(['admin']);
require_once __DIR__ . '/../../config/database.php';

$pdo = getDBConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error_message'] = 'Invalid CSRF token.';
        header('Location: index.php');
        exit;
    }

    $id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);

    if ($id === (int)$_SESSION['user_id']) {
        $_SESSION['error_message'] = 'You cannot delete your own account.';
        header('Location: index.php');
        exit;
    }

    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$id]);

    $_SESSION['success_message'] = 'User deleted successfully.';
    header('Location: index.php');
    exit;
}

$_SESSION['error_message'] = 'Invalid request.';
header('Location: index.php');
exit;
