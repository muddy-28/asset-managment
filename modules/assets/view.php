<?php
session_start();
require_once __DIR__ . '/../../middleware/auth_check.php';
require_once __DIR__ . '/../../config/database.php';

$pageTitle = 'View Asset';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $pdo->prepare("
    SELECT a.*, 
           ac.category_name, 
           asc2.subcategory_name, 
           v.vendor_name
    FROM assets a
    LEFT JOIN asset_categories ac ON a.category_id = ac.id
    LEFT JOIN asset_subcategories asc2 ON a.subcategory_id = asc2.id
    LEFT JOIN vendors v ON a.vendor_id = v.id
    WHERE a.id = :id
");
$stmt->execute([':id' => $id]);
$asset = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$asset) {
    $_SESSION['error_message'] = 'Asset not found.';
    header('Location: index.php');
    exit;
}

$assignStmt = $pdo->prepare("
    SELECT aa.*, 
           f.floor_name, 
           d.department_name, 
           l.location_name
    FROM asset_assignments aa
    LEFT JOIN floors f ON aa.floor_id = f.id
    LEFT JOIN departments d ON aa.department_id = d.id
    LEFT JOIN locations l ON aa.location_id = l.id
    WHERE aa.asset_id = :asset_id AND aa.status = 'active'
    ORDER BY aa.assigned_date DESC
");
$assignStmt->execute([':asset_id' => $id]);
$assignments = $assignStmt->fetchAll(PDO::FETCH_ASSOC);

$maintStmt = $pdo->prepare("
    SELECT * FROM asset_maintenance_logs
    WHERE asset_id = :asset_id
    ORDER BY maintenance_date DESC
");
$maintStmt->execute([':asset_id' => $id]);
$maintenanceLogs = $maintStmt->fetchAll(PDO::FETCH_ASSOC);

$statusClasses = [
    'active' => 'success',
    'maintenance' => 'warning',
    'disposed' => 'danger',
    'lost' => 'secondary',
];

require_once __DIR__ . '/../../views/header.php';
require_once __DIR__ . '/../../views/sidebar.php';
?>

<div class="main-content" id="mainContent">
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Asset Details</h1>
            <div>
                <a href="edit.php?id=<?php echo (int)$asset['id']; ?>" class="btn btn-primary">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="barcode.php?id=<?php echo (int)$asset['id']; ?>" class="btn btn-outline-dark">
                    <i class="fas fa-barcode"></i> Print Barcode
                </a>
                <a href="index.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>

        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?php echo htmlspecialchars($_SESSION['success_message'], ENT_QUOTES, 'UTF-8'); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <?php echo htmlspecialchars($asset['asset_name'], ENT_QUOTES, 'UTF-8'); ?>
                    <span class="badge bg-<?php echo $statusClasses[$asset['status']] ?? 'secondary'; ?> ms-2">
                        <?php echo htmlspecialchars(ucfirst($asset['status']), ENT_QUOTES, 'UTF-8'); ?>
                    </span>
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th class="text-muted" style="width: 40%;">Asset Tag</th>
                                <td><?php echo htmlspecialchars($asset['asset_tag'], ENT_QUOTES, 'UTF-8'); ?></td>
                            </tr>
                            <tr>
                                <th class="text-muted">Asset Name</th>
                                <td><?php echo htmlspecialchars($asset['asset_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                            </tr>
                            <tr>
                                <th class="text-muted">Model Number</th>
                                <td><?php echo htmlspecialchars($asset['model_number'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></td>
                            </tr>
                            <tr>
                                <th class="text-muted">Serial Number</th>
                                <td><?php echo htmlspecialchars($asset['serial_number'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></td>
                            </tr>
                            <tr>
                                <th class="text-muted">Barcode</th>
                                <td><?php echo htmlspecialchars($asset['barcode'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></td>
                            </tr>
                            <tr>
                                <th class="text-muted">Category</th>
                                <td><?php echo htmlspecialchars($asset['category_name'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></td>
                            </tr>
                            <tr>
                                <th class="text-muted">Subcategory</th>
                                <td><?php echo htmlspecialchars($asset['subcategory_name'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th class="text-muted" style="width: 40%;">Vendor</th>
                                <td><?php echo htmlspecialchars($asset['vendor_name'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></td>
                            </tr>
                            <tr>
                                <th class="text-muted">Purchase Date</th>
                                <td><?php echo $asset['purchase_date'] ? htmlspecialchars(date('M d, Y', strtotime($asset['purchase_date'])), ENT_QUOTES, 'UTF-8') : 'N/A'; ?></td>
                            </tr>
                            <tr>
                                <th class="text-muted">Purchase Cost</th>
                                <td><?php echo $asset['purchase_cost'] !== null ? '$' . number_format((float)$asset['purchase_cost'], 2) : 'N/A'; ?></td>
                            </tr>
                            <tr>
                                <th class="text-muted">Warranty Expiry</th>
                                <td><?php echo $asset['warranty_expiry'] ? htmlspecialchars(date('M d, Y', strtotime($asset['warranty_expiry'])), ENT_QUOTES, 'UTF-8') : 'N/A'; ?></td>
                            </tr>
                            <tr>
                                <th class="text-muted">Condition</th>
                                <td><?php echo htmlspecialchars(ucfirst($asset['asset_condition'] ?? 'N/A'), ENT_QUOTES, 'UTF-8'); ?></td>
                            </tr>
                            <tr>
                                <th class="text-muted">Status</th>
                                <td>
                                    <span class="badge bg-<?php echo $statusClasses[$asset['status']] ?? 'secondary'; ?>">
                                        <?php echo htmlspecialchars(ucfirst($asset['status']), ENT_QUOTES, 'UTF-8'); ?>
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th class="text-muted">Created At</th>
                                <td><?php echo $asset['created_at'] ? htmlspecialchars(date('M d, Y H:i', strtotime($asset['created_at'])), ENT_QUOTES, 'UTF-8') : 'N/A'; ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Current Assignment</h5>
            </div>
            <div class="card-body">
                <?php if (count($assignments) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Floor</th>
                                    <th>Department</th>
                                    <th>Location</th>
                                    <th>Assigned Date</th>
                                    <th>Assigned By</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($assignments as $assign): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($assign['floor_name'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars($assign['department_name'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars($assign['location_name'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo $assign['assigned_date'] ? htmlspecialchars(date('M d, Y', strtotime($assign['assigned_date'])), ENT_QUOTES, 'UTF-8') : 'N/A'; ?></td>
                                        <td><?php echo htmlspecialchars($assign['assigned_by'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted mb-0">No active assignment found for this asset.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Maintenance History</h5>
            </div>
            <div class="card-body">
                <?php if (count($maintenanceLogs) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Technician</th>
                                    <th>Work Performed</th>
                                    <th>Cost</th>
                                    <th>Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($maintenanceLogs as $log): ?>
                                    <tr>
                                        <td><?php echo $log['maintenance_date'] ? htmlspecialchars(date('M d, Y', strtotime($log['maintenance_date'])), ENT_QUOTES, 'UTF-8') : 'N/A'; ?></td>
                                        <td><?php echo htmlspecialchars(ucfirst($log['maintenance_type'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars($log['technician_name'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars($log['work_performed'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo $log['maintenance_cost'] !== null ? '$' . number_format((float)$log['maintenance_cost'], 2) : 'N/A'; ?></td>
                                        <td><?php echo htmlspecialchars($log['remarks'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted mb-0">No maintenance records found for this asset.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../views/footer.php'; ?>
