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

        <li class="sidebar-item <?php echo isActive('/modules/assets/'); ?>">
            <a href="/modules/assets/index.php" class="sidebar-link">
                <i class="fas fa-boxes"></i><span>Assets</span>
            </a>
        </li>
        <li class="sidebar-item <?php echo isActive('/modules/categories/'); ?>">
            <a href="/modules/categories/index.php" class="sidebar-link">
                <i class="fas fa-tags"></i><span>Categories</span>
            </a>
        </li>
        <li class="sidebar-item <?php echo isActive('/modules/subcategories/'); ?>">
            <a href="/modules/subcategories/index.php" class="sidebar-link">
                <i class="fas fa-layer-group"></i><span>Subcategories</span>
            </a>
        </li>
        <li class="sidebar-item <?php echo isActive('/modules/vendors/'); ?>">
            <a href="/modules/vendors/index.php" class="sidebar-link">
                <i class="fas fa-truck"></i><span>Vendors</span>
            </a>
        </li>

        <li class="sidebar-heading">Locations</li>

        <li class="sidebar-item <?php echo isActive('/modules/floors/'); ?>">
            <a href="/modules/floors/index.php" class="sidebar-link">
                <i class="fas fa-building"></i><span>Floors</span>
            </a>
        </li>
        <li class="sidebar-item <?php echo isActive('/modules/departments/'); ?>">
            <a href="/modules/departments/index.php" class="sidebar-link">
                <i class="fas fa-sitemap"></i><span>Departments</span>
            </a>
        </li>
        <li class="sidebar-item <?php echo isActive('/modules/locations/'); ?>">
            <a href="/modules/locations/index.php" class="sidebar-link">
                <i class="fas fa-map-marker-alt"></i><span>Locations</span>
            </a>
        </li>

        <li class="sidebar-heading">Operations</li>

        <li class="sidebar-item <?php echo isActive('/modules/assignments/'); ?>">
            <a href="/modules/assignments/index.php" class="sidebar-link">
                <i class="fas fa-clipboard-list"></i><span>Assignments</span>
            </a>
        </li>
        <li class="sidebar-item <?php echo isActive('/modules/transfers/'); ?>">
            <a href="/modules/transfers/index.php" class="sidebar-link">
                <i class="fas fa-exchange-alt"></i><span>Transfers</span>
            </a>
        </li>

        <li class="sidebar-heading">Maintenance</li>

        <li class="sidebar-item <?php echo isActive('/modules/maintenance/'); ?>">
            <a href="/modules/maintenance/index.php" class="sidebar-link">
                <i class="fas fa-calendar-check"></i><span>Maintenance Schedule</span>
            </a>
        </li>
        <li class="sidebar-item <?php echo isActive('/modules/maintenance_logs/'); ?>">
            <a href="/modules/maintenance_logs/index.php" class="sidebar-link">
                <i class="fas fa-wrench"></i><span>Maintenance Logs</span>
            </a>
        </li>
        <li class="sidebar-item <?php echo isActive('/modules/calibration/'); ?>">
            <a href="/modules/calibration/index.php" class="sidebar-link">
                <i class="fas fa-sliders-h"></i><span>Calibration</span>
            </a>
        </li>
        <li class="sidebar-item <?php echo isActive('/modules/disposal/'); ?>">
            <a href="/modules/disposal/index.php" class="sidebar-link">
                <i class="fas fa-trash-alt"></i><span>Disposal</span>
            </a>
        </li>

        <li class="sidebar-heading">Administration</li>

        <li class="sidebar-item <?php echo isActive('/modules/users/'); ?>">
            <a href="/modules/users/index.php" class="sidebar-link">
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
