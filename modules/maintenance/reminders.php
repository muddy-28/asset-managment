<?php
session_start();
require_once __DIR__ . '/../../middleware/auth_check.php';
require_once __DIR__ . '/../../config/database.php';

$pdo = getDBConnection();

$pageTitle = 'Maintenance Reminders';

$stmt = $pdo->query("
    SELECT s.*, a.asset_name, DATEDIFF(s.next_due_date, CURDATE()) AS days_remaining
    FROM asset_maintenance_schedule s
    LEFT JOIN assets a ON s.asset_id = a.id
    WHERE s.next_due_date <= DATE_ADD(CURDATE(), INTERVAL 7 DAY)
      AND s.status = 'active'
      AND s.deleted_at IS NULL
    ORDER BY s.next_due_date ASC
");
$reminders = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once __DIR__ . '/../../views/header.php';
require_once __DIR__ . '/../../views/sidebar.php';
?>

<div class="main-content" id="mainContent">
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0"><?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?></h1>
            <a href="index.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to Schedules</a>
        </div>

        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?php echo htmlspecialchars($_SESSION['success_message'], ENT_QUOTES, 'UTF-8'); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <?php if (empty($reminders)): ?>
                    <div class="alert alert-info mb-0">No upcoming maintenance reminders.</div>
                <?php else: ?>
                    <table class="table table-striped datatable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Asset Name</th>
                                <th>Maintenance Type</th>
                                <th>Next Due Date</th>
                                <th>Days Remaining</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reminders as $i => $row): ?>
                                <?php
                                $rowClass = '';
                                if ($row['days_remaining'] < 0) {
                                    $rowClass = 'table-danger';
                                } elseif ($row['days_remaining'] <= 3) {
                                    $rowClass = 'table-warning';
                                }
                                ?>
                                <tr class="<?php echo $rowClass; ?>">
                                    <td><?php echo $i + 1; ?></td>
                                    <td><?php echo htmlspecialchars($row['asset_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars($row['maintenance_type'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars($row['next_due_date'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td>
                                        <?php if ($row['days_remaining'] < 0): ?>
                                            <span class="badge bg-danger">Overdue by <?php echo abs((int)$row['days_remaining']); ?> day(s)</span>
                                        <?php else: ?>
                                            <?php echo (int)$row['days_remaining']; ?> day(s)
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../views/footer.php'; ?>
