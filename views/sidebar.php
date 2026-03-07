<?php
/**
 * Sidebar Navigation
 */

$currentScript = $_SERVER['SCRIPT_NAME'];

/**
 * Helper to check if a nav link should be marked active.
 */
function isActive(string $path): string
{
    global $currentScript;
    return (strpos($currentScript, $path) !== false) ? 'active' : '';
}
?>

<!-- Sidebar Overlay for mobile -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- Sidebar -->
<nav class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <i class="fas fa-hospital-alt me-2"></i>
        <span>Hospital AMS</span>
    </div>
    <ul class="sidebar-nav">
        <li class="sidebar-item <?php echo isActive('/dashboard/'); ?>">
            <a href="/dashboard/index.php" class="sidebar-link">
                <i class="fas fa-tachometer-alt"></i><span>Dashboard</span>
            </a>
        </li>

        <li class="sidebar-heading">Asset Management</li>

        <li class="sidebar-item <?php echo isActive('/assets/index'); ?>">
            <a href="/assets/index.php" class="sidebar-link">
                <i class="fas fa-boxes"></i><span>Assets</span>
            </a>
        </li>
        <li class="sidebar-item <?php echo isActive('/categories/'); ?>">
            <a href="/categories/index.php" class="sidebar-link">
                <i class="fas fa-tags"></i><span>Categories</span>
            </a>
        </li>
        <li class="sidebar-item <?php echo isActive('/vendors/'); ?>">
            <a href="/vendors/index.php" class="sidebar-link">
                <i class="fas fa-truck"></i><span>Vendors</span>
            </a>
        </li>

        <li class="sidebar-heading">Locations</li>

        <li class="sidebar-item <?php echo isActive('/floors/'); ?>">
            <a href="/floors/index.php" class="sidebar-link">
                <i class="fas fa-building"></i><span>Floors</span>
            </a>
        </li>
        <li class="sidebar-item <?php echo isActive('/departments/'); ?>">
            <a href="/departments/index.php" class="sidebar-link">
                <i class="fas fa-sitemap"></i><span>Departments</span>
            </a>
        </li>
        <li class="sidebar-item <?php echo isActive('/locations/'); ?>">
            <a href="/locations/index.php" class="sidebar-link">
                <i class="fas fa-map-marker-alt"></i><span>Locations</span>
            </a>
        </li>

        <li class="sidebar-heading">Operations</li>

        <li class="sidebar-item <?php echo isActive('/assignments/'); ?>">
            <a href="/assignments/index.php" class="sidebar-link">
                <i class="fas fa-clipboard-list"></i><span>Assignments</span>
            </a>
        </li>
        <li class="sidebar-item <?php echo isActive('/transfers/'); ?>">
            <a href="/transfers/index.php" class="sidebar-link">
                <i class="fas fa-exchange-alt"></i><span>Transfers</span>
            </a>
        </li>

        <li class="sidebar-heading">Maintenance</li>

        <li class="sidebar-item <?php echo isActive('/maintenance-schedule/'); ?>">
            <a href="/maintenance-schedule/index.php" class="sidebar-link">
                <i class="fas fa-calendar-check"></i><span>Maintenance Schedule</span>
            </a>
        </li>
        <li class="sidebar-item <?php echo isActive('/maintenance-logs/'); ?>">
            <a href="/maintenance-logs/index.php" class="sidebar-link">
                <i class="fas fa-wrench"></i><span>Maintenance Logs</span>
            </a>
        </li>
        <li class="sidebar-item <?php echo isActive('/calibration/'); ?>">
            <a href="/calibration/index.php" class="sidebar-link">
                <i class="fas fa-sliders-h"></i><span>Calibration</span>
            </a>
        </li>
        <li class="sidebar-item <?php echo isActive('/disposal/'); ?>">
            <a href="/disposal/index.php" class="sidebar-link">
                <i class="fas fa-trash-alt"></i><span>Disposal</span>
            </a>
        </li>

        <li class="sidebar-heading">Administration</li>

        <li class="sidebar-item <?php echo isActive('/users/'); ?>">
            <a href="/users/index.php" class="sidebar-link">
                <i class="fas fa-users-cog"></i><span>Users</span>
            </a>
        </li>
        <li class="sidebar-item">
            <a href="/auth/logout.php" class="sidebar-link text-danger">
                <i class="fas fa-sign-out-alt"></i><span>Logout</span>
            </a>
        </li>
    </ul>
</nav>
