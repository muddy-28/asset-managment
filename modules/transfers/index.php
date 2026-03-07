<?php
session_start();
require_once __DIR__ . '/../../middleware/auth_check.php';
require_once __DIR__ . '/../../config/database.php';

$pageTitle = 'Asset Transfers';
$pdo = getDBConnection();

$stmt = $pdo->prepare("
    SELECT t.*,
        a.asset_name, a.asset_tag,
        fd.department_name AS from_department_name,
        td.department_name AS to_department_name,
        fl.location_name AS from_location_name,
        tl.location_name AS to_location_name
    FROM asset_transfer_history t
    LEFT JOIN assets a ON t.asset_id = a.id
    LEFT JOIN departments fd ON t.from_department = fd.id
    LEFT JOIN departments td ON t.to_department = td.id
    LEFT JOIN locations fl ON t.from_location = fl.id
    LEFT JOIN locations tl ON t.to_location = tl.id
    ORDER BY t.id DESC
");
$stmt->execute();
$transfers = $stmt->fetchAll();

require_once __DIR__ . '/../../views/header.php';
require_once __DIR__ . '/../../views/sidebar.php';
?>

<div class="main-content" id="mainContent">
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <h2 class="mb-0"><i class="fas fa-exchange-alt me-2"></i>Asset Transfers</h2>
                <a href="create.php" class="btn btn-primary"><i class="fas fa-plus me-1"></i>New Transfer</a>
            </div>
        </div>

        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?php echo htmlspecialchars($_SESSION['success_message'], ENT_QUOTES, 'UTF-8'); unset($_SESSION['success_message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?php echo htmlspecialchars($_SESSION['error_message'], ENT_QUOTES, 'UTF-8'); unset($_SESSION['error_message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm">
            <div class="card-body">
                <table class="table table-striped table-hover datatable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Asset</th>
                            <th>From Department</th>
                            <th>To Department</th>
                            <th>From Location</th>
                            <th>To Location</th>
                            <th>Transfer Date</th>
                            <th>Transferred By</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($transfers as $index => $row): ?>
                            <tr>
                                <td><?php echo $index + 1; ?></td>
                                <td><?php echo htmlspecialchars(($row['asset_name'] ?? '') . ' (' . ($row['asset_tag'] ?? '') . ')', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($row['from_department_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($row['to_department_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($row['from_location_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($row['to_location_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($row['transfer_date'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($row['transferred_by'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td>
                                    <span class="text-muted"><i class="fas fa-eye"></i> View</span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../views/footer.php'; ?>
