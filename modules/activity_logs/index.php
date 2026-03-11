<?php
session_start();
require_once __DIR__ . '/../../middleware/auth_check.php';
checkRole(['admin', 'manager']);
require_once __DIR__ . '/../../config/database.php';

$pageTitle = 'Activity Logs';

$pdo = getDBConnection();

// Optional filters
$filterModule = trim($_GET['module'] ?? '');
$filterAction = trim($_GET['action_type'] ?? '');
$filterUser   = trim($_GET['user_name'] ?? '');
$filterDate   = trim($_GET['date'] ?? '');

$where  = [];
$params = [];

if ($filterModule !== '') {
    $where[]           = 'al.module = :module';
    $params[':module'] = $filterModule;
}
if ($filterAction !== '') {
    $where[]                = 'al.action_type = :action_type';
    $params[':action_type'] = $filterAction;
}
if ($filterUser !== '') {
    $where[]              = 'al.user_name LIKE :user_name';
    $params[':user_name'] = '%' . $filterUser . '%';
}
if ($filterDate !== '') {
    $where[]          = 'DATE(al.created_at) = :date';
    $params[':date']  = $filterDate;
}

$whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$stmt = $pdo->prepare("
    SELECT al.*
    FROM   activity_logs al
    $whereSQL
    ORDER  BY al.created_at DESC
    LIMIT  1000
");
$stmt->execute($params);
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Distinct modules for filter dropdown
$modules     = $pdo->query("SELECT DISTINCT module      FROM activity_logs ORDER BY module")->fetchAll(PDO::FETCH_COLUMN);
$actionTypes = $pdo->query("SELECT DISTINCT action_type FROM activity_logs ORDER BY action_type")->fetchAll(PDO::FETCH_COLUMN);

require_once __DIR__ . '/../../views/header.php';
require_once __DIR__ . '/../../views/sidebar.php';
?>

<div class="main-content" id="mainContent">
    <div class="container-fluid py-4">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0"><i class="fas fa-history me-2"></i>Activity Logs</h1>
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="index.php" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Module</label>
                        <select name="module" class="form-select">
                            <option value="">All Modules</option>
                            <?php foreach ($modules as $mod): ?>
                                <option value="<?php echo htmlspecialchars($mod, ENT_QUOTES, 'UTF-8'); ?>"
                                    <?php echo ($filterModule === $mod) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $mod)), ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Action</label>
                        <select name="action_type" class="form-select">
                            <option value="">All Actions</option>
                            <?php foreach ($actionTypes as $at): ?>
                                <option value="<?php echo htmlspecialchars($at, ENT_QUOTES, 'UTF-8'); ?>"
                                    <?php echo ($filterAction === $at) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars(ucfirst($at), ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">User</label>
                        <input type="text" name="user_name" class="form-control"
                               value="<?php echo htmlspecialchars($filterUser, ENT_QUOTES, 'UTF-8'); ?>"
                               placeholder="Search by user name">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Date</label>
                        <input type="date" name="date" class="form-control"
                               value="<?php echo htmlspecialchars($filterDate, ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                        <a href="index.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Logs Table -->
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0 datatable">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Date &amp; Time</th>
                                <th>User</th>
                                <th>Action</th>
                                <th>Module</th>
                                <th>Record ID</th>
                                <th>Description</th>
                                <th>IP Address</th>
                                <th>Duration (ms)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($logs)): ?>
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-4">
                                        No activity logs found.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($logs as $log): ?>
                                    <?php
                                    $badgeClass = match($log['action_type']) {
                                        'login'  => 'bg-success',
                                        'logout' => 'bg-secondary',
                                        'create' => 'bg-primary',
                                        'update' => 'bg-warning text-dark',
                                        'delete' => 'bg-danger',
                                        default  => 'bg-info',
                                    };
                                    ?>
                                    <tr>
                                        <td><?php echo (int)$log['id']; ?></td>
                                        <td class="text-nowrap">
                                            <?php echo htmlspecialchars($log['created_at'], ENT_QUOTES, 'UTF-8'); ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($log['user_name'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td>
                                            <span class="badge <?php echo $badgeClass; ?>">
                                                <?php echo htmlspecialchars(ucfirst($log['action_type']), ENT_QUOTES, 'UTF-8'); ?>
                                            </span>
                                        </td>
                                        <td><?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $log['module'])), ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo $log['record_id'] !== null ? (int)$log['record_id'] : '—'; ?></td>
                                        <td><?php echo htmlspecialchars($log['description'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td class="text-nowrap"><?php echo htmlspecialchars($log['ip_address'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo $log['duration_ms'] !== null ? (int)$log['duration_ms'] . ' ms' : '—'; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<?php require_once __DIR__ . '/../../views/footer.php'; ?>
