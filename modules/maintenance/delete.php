<?php
session_start();
require_once __DIR__ . '/../../middleware/auth_check.php';
require_once __DIR__ . '/../../config/database.php';

$pdo = getDBConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error_message'] = 'Invalid CSRF token.';
        header('Location: index.php');
        exit;
    }

    $id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);

    $stmt = $pdo->prepare("UPDATE asset_maintenance_schedule SET is_deleted = 1 WHERE id = ? AND is_deleted = 0");
    $stmt->execute([$id]);

    $_SESSION['success_message'] = 'Maintenance schedule deleted successfully.';
    header('Location: index.php');
    exit;
}

$_SESSION['error_message'] = 'Invalid request.';
header('Location: index.php');
exit;
