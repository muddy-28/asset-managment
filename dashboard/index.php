<?php
/**
 * Dashboard - Hospital Asset Management System
 */

require_once __DIR__ . '/../middleware/auth_check.php';
require_once __DIR__ . '/../config/database.php';

$pageTitle = 'Dashboard';

$pdo = getDBConnection();

// Fetch summary counts
$counts = [];
$tables = [
    'assets'                     => 'Total Assets',
    'asset_categories'           => 'Categories',
    'vendors'                    => 'Vendors',
    'departments'                => 'Departments',
    'floors'                     => 'Floors',
    'locations'                  => 'Locations',
    'asset_assignments'          => 'Assignments',
    'asset_maintenance_schedule' => 'Maintenance Schedules',
    'users'                      => 'Users',
];

// Each count uses a separate hardcoded query to avoid dynamic table names in SQL
$countQueries = [
    'assets'                     => 'SELECT COUNT(*) as total FROM assets WHERE deleted_at IS NULL',
    'asset_categories'           => 'SELECT COUNT(*) as total FROM asset_categories WHERE deleted_at IS NULL',
    'vendors'                    => 'SELECT COUNT(*) as total FROM vendors WHERE deleted_at IS NULL',
    'departments'                => 'SELECT COUNT(*) as total FROM departments WHERE deleted_at IS NULL',
    'floors'                     => 'SELECT COUNT(*) as total FROM floors WHERE deleted_at IS NULL',
    'locations'                  => 'SELECT COUNT(*) as total FROM locations WHERE deleted_at IS NULL',
    'asset_assignments'          => 'SELECT COUNT(*) as total FROM asset_assignments WHERE deleted_at IS NULL',
    'asset_maintenance_schedule' => 'SELECT COUNT(*) as total FROM asset_maintenance_schedule WHERE deleted_at IS NULL',
    'users'                      => 'SELECT COUNT(*) as total FROM users WHERE deleted_at IS NULL',
];

foreach ($tables as $table => $label) {
    try {
        $stmt = $pdo->query($countQueries[$table]);
        $counts[$table] = $stmt->fetch()['total'];
    } catch (PDOException $e) {
        $counts[$table] = 0;
    }
}

// Icons & colors for stat cards
$cardConfig = [
    'assets'                     => ['icon' => 'fa-boxes',          'color' => 'primary'],
    'asset_categories'           => ['icon' => 'fa-tags',           'color' => 'success'],
    'vendors'                    => ['icon' => 'fa-truck',          'color' => 'info'],
    'departments'                => ['icon' => 'fa-sitemap',        'color' => 'warning'],
    'floors'                     => ['icon' => 'fa-building',       'color' => 'secondary'],
    'locations'                  => ['icon' => 'fa-map-marker-alt', 'color' => 'danger'],
    'asset_assignments'          => ['icon' => 'fa-clipboard-list', 'color' => 'primary'],
    'asset_maintenance_schedule' => ['icon' => 'fa-calendar-check', 'color' => 'success'],
    'users'                      => ['icon' => 'fa-users-cog',      'color' => 'info'],
];

require_once __DIR__ . '/../views/header.php';
require_once __DIR__ . '/../views/sidebar.php';
?>

<div class="main-content" id="mainContent">
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="mb-1"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</h2>
                <p class="text-muted mb-0">Welcome back, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User', ENT_QUOTES, 'UTF-8'); ?>!</p>
            </div>
        </div>

        <!-- Stat Cards -->
        <div class="row g-3 mb-4">
            <?php foreach ($tables as $table => $label):
                $icon  = $cardConfig[$table]['icon'];
                $color = $cardConfig[$table]['color'];
                $count = $counts[$table];
            ?>
            <div class="col-6 col-md-4 col-xl-3">
                <div class="card stat-card h-100" style="border-left-color: var(--bs-<?php echo $color; ?>);">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted text-uppercase mb-1" style="font-size: 0.75rem;">
                                <?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?>
                            </h6>
                            <h3 class="mb-0 fw-bold"><?php echo (int) $count; ?></h3>
                        </div>
                        <div class="stat-icon text-<?php echo $color; ?>">
                            <i class="fas <?php echo $icon; ?>"></i>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Charts Row -->
        <div class="row g-3">
            <div class="col-lg-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h6 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Asset Status Distribution</h6>
                    </div>
                    <div class="card-body">
                        <canvas id="assetStatusChart" height="260"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Assets by Category</h6>
                    </div>
                    <div class="card-body">
                        <canvas id="categoryChart" height="260"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Fetch chart data
$statusData = [];
try {
    $stmt = $pdo->prepare("SELECT status, COUNT(*) as total FROM assets WHERE deleted_at IS NULL GROUP BY status");
    $stmt->execute();
    while ($row = $stmt->fetch()) {
        $statusData[$row['status']] = (int) $row['total'];
    }
} catch (PDOException $e) {
    $statusData = [];
}

$categoryData = [];
try {
    $stmt = $pdo->prepare(
        "SELECT c.category_name, COUNT(a.id) as total
         FROM asset_categories c
         LEFT JOIN assets a ON a.category_id = c.id AND a.deleted_at IS NULL
         WHERE c.deleted_at IS NULL
         GROUP BY c.id, c.category_name
         ORDER BY total DESC
         LIMIT 10"
    );
    $stmt->execute();
    while ($row = $stmt->fetch()) {
        $categoryData[$row['category_name']] = (int) $row['total'];
    }
} catch (PDOException $e) {
    $categoryData = [];
}
?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Asset Status Doughnut Chart
    var statusLabels = <?php echo json_encode(array_keys($statusData)); ?>;
    var statusValues = <?php echo json_encode(array_values($statusData)); ?>;
    var statusColors = ['#0d6efd', '#ffc107', '#dc3545', '#6c757d'];

    if (document.getElementById('assetStatusChart')) {
        new Chart(document.getElementById('assetStatusChart'), {
            type: 'doughnut',
            data: {
                labels: statusLabels.length ? statusLabels : ['No Data'],
                datasets: [{
                    data: statusValues.length ? statusValues : [1],
                    backgroundColor: statusValues.length ? statusColors.slice(0, statusLabels.length) : ['#e9ecef'],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    }

    // Category Bar Chart
    var catLabels = <?php echo json_encode(array_keys($categoryData)); ?>;
    var catValues = <?php echo json_encode(array_values($categoryData)); ?>;

    if (document.getElementById('categoryChart')) {
        new Chart(document.getElementById('categoryChart'), {
            type: 'bar',
            data: {
                labels: catLabels.length ? catLabels : ['No Data'],
                datasets: [{
                    label: 'Assets',
                    data: catValues.length ? catValues : [0],
                    backgroundColor: '#0dcaf0',
                    borderColor: '#0d6efd',
                    borderWidth: 1,
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0 } }
                }
            }
        });
    }
});
</script>

<?php require_once __DIR__ . '/../views/footer.php'; ?>
