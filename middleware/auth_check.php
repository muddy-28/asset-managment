<?php
/**
 * Authentication Middleware
 * Include this file at the top of any page that requires authentication.
 */

require_once __DIR__ . '/../config/app.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit;
}

/**
 * Check if the current user's role is in the allowed roles array.
 * Redirects with an "Unauthorized" message if not permitted.
 *
 * @param array $allowedRoles Array of allowed role strings.
 */
function checkRole(array $allowedRoles): void
{
    if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], $allowedRoles, true)) {
        $_SESSION['error_message'] = 'Unauthorized: You do not have permission to access this page.';
        header('Location: ' . BASE_URL . '/dashboard/index.php');
        exit;
    }
}
