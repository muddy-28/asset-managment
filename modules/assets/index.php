<?php
session_start();
require_once __DIR__ . '/../../middleware/auth_check.php';
require_once __DIR__ . '/../../config/database.php';

$pageTitle = 'Asset Management';

$pdo = getDBConnection();

$stmt = $pdo->query("
    SELECT a.*, 
           ac.category_name, 
           asc2.subcategory_name, 
           v.vendor_name
    FROM assets a
    LEFT JOIN asset_categories ac ON a.category_id = ac.id
    LEFT JOIN asset_subcategories asc2 ON a.subcategory_id = asc2.id
    LEFT JOIN vendors v ON a.vendor_id = v.id
    WHERE a.deleted_at IS NULL
    ORDER BY a.id DESC
");
$assets = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once __DIR__ . '/../../views/header.php';
require_once __DIR__ . '/../../views/sidebar.php';
?>

<div class="main-content" id="mainContent">
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Asset Management</h1>
            <a href="create.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New Asset
            </a>
        </div>

        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?php echo htmlspecialchars($_SESSION['success_message'], ENT_QUOTES, 'UTF-8'); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?php echo htmlspecialchars($_SESSION['error_message'], ENT_QUOTES, 'UTF-8'); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover datatable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Asset Tag</th>
                                <th>Asset Name</th>
                                <th>Category</th>
                                <th>Vendor</th>
                                <th>Status</th>
                                <th>Condition</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($assets as $index => $asset): ?>
                                <tr>
                                    <td><?php echo $index + 1; ?></td>
                                    <td><?php echo htmlspecialchars($asset['asset_tag'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars($asset['asset_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td>
                                        <?php echo htmlspecialchars($asset['category_name'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?>
                                        <?php if ($asset['subcategory_name']): ?>
                                            <br><small class="text-muted"><?php echo htmlspecialchars($asset['subcategory_name'], ENT_QUOTES, 'UTF-8'); ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($asset['vendor_name'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td>
                                        <?php
                                        $statusClasses = [
                                            'active' => 'success',
                                            'maintenance' => 'warning',
                                            'disposed' => 'danger',
                                            'lost' => 'secondary',
                                        ];
                                        $badgeClass = $statusClasses[$asset['status']] ?? 'secondary';
                                        ?>
                                        <span class="badge bg-<?php echo $badgeClass; ?>">
                                            <?php echo htmlspecialchars(ucfirst($asset['status']), ENT_QUOTES, 'UTF-8'); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars(ucfirst($asset['asset_condition'] ?? 'N/A'), ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td>
                                        <a href="view.php?id=<?php echo (int)$asset['id']; ?>" class="btn btn-sm btn-info" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="edit.php?id=<?php echo (int)$asset['id']; ?>" class="btn btn-sm btn-primary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="#" class="btn btn-sm btn-danger btn-delete"
                                           data-url="delete.php?id=<?php echo (int)$asset['id']; ?>"
                                           data-name="<?php echo htmlspecialchars($asset['asset_name'], ENT_QUOTES, 'UTF-8'); ?>">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../views/footer.php'; ?>
