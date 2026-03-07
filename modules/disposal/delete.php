<?php
session_start();
require_once __DIR__ . '/../../middleware/auth_check.php';
require_once __DIR__ . '/../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error_message'] = 'Invalid CSRF token.';
        header('Location: index.php');
        exit;
    }

    $id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);

    $stmt = $pdo->prepare("DELETE FROM asset_disposal WHERE id = ?");
    $stmt->execute([$id]);

    $_SESSION['success_message'] = 'Disposal record deleted successfully.';
    header('Location: index.php');
    exit;
}

$_SESSION['error_message'] = 'Invalid request.';
header('Location: index.php');
exit;
