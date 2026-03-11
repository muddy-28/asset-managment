<?php
/**
 * Activity Logger - Hospital Asset Management System
 *
 * Provides a helper function to record user actions in the activity_logs table,
 * including timing information for each logged operation.
 */

/**
 * Log a user action to the activity_logs table.
 *
 * @param PDO         $pdo         Active database connection
 * @param string      $actionType  Action performed: 'login', 'logout', 'create', 'update', 'delete'
 * @param string      $module      Module name, e.g. 'assets', 'users'
 * @param int|null    $recordId    Primary key of the affected record (null for global actions)
 * @param string      $description Human-readable description of the action
 */
function logActivity(PDO $pdo, string $actionType, string $module, ?int $recordId, string $description): void
{
    // Calculate duration in milliseconds from request start
    $durationMs = isset($_SERVER['REQUEST_TIME_FLOAT'])
        ? (int)round((microtime(true) - $_SERVER['REQUEST_TIME_FLOAT']) * 1000)
        : null;

    $userId   = isset($_SESSION['user_id'])   ? (int)$_SESSION['user_id']   : null;
    $userName = isset($_SESSION['user_name']) ? $_SESSION['user_name']       : null;

    // Collect client IP, respecting proxy headers
    $ipAddress = $_SERVER['HTTP_X_FORWARDED_FOR']
        ?? $_SERVER['HTTP_CLIENT_IP']
        ?? $_SERVER['REMOTE_ADDR']
        ?? null;

    // When a proxy adds a list of IPs, take only the first (client) one
    if ($ipAddress !== null && strpos($ipAddress, ',') !== false) {
        $ipAddress = trim(explode(',', $ipAddress)[0]);
    }

    try {
        $stmt = $pdo->prepare("
            INSERT INTO activity_logs
                (user_id, user_name, action_type, module, record_id, description, ip_address, duration_ms)
            VALUES
                (:user_id, :user_name, :action_type, :module, :record_id, :description, :ip_address, :duration_ms)
        ");
        $stmt->execute([
            ':user_id'     => $userId,
            ':user_name'   => $userName,
            ':action_type' => $actionType,
            ':module'      => $module,
            ':record_id'   => $recordId,
            ':description' => $description,
            ':ip_address'  => $ipAddress,
            ':duration_ms' => $durationMs,
        ]);
    } catch (PDOException $e) {
        // Never let logging failures break the main application flow
        error_log('Activity log write failed: ' . $e->getMessage());
    }
}
