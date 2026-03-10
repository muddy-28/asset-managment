<?php
/**
 * Root index - Redirect to dashboard
 */
require_once __DIR__ . '/config/app.php';
header('Location: ' . BASE_URL . '/dashboard/index.php');
exit;
