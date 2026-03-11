<?php
/**
 * Logout - Destroy session and redirect to login
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/app.php';

// Log the logout action before session is destroyed
if (isset($_SESSION['user_id'])) {
    require_once __DIR__ . '/../config/database.php';
    require_once __DIR__ . '/../config/activity_logger.php';
    $pdo = getDBConnection();
    logActivity($pdo, 'logout', 'auth', (int)$_SESSION['user_id'], 'User logged out: ' . ($_SESSION['user_name'] ?? ''));
}

$_SESSION = [];

if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

session_destroy();

header('Location: ' . BASE_URL . '/auth/login.php');
exit;
