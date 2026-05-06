<?php
session_start();
require_once __DIR__ . '/../../middleware/auth_check.php';
require_once __DIR__ . '/../../config/database.php';

$pageTitle = 'Services';
$pdo = getDBConnection();

$stmt = $pdo->prepare("SELECT * FROM services WHERE is_deleted = 0 ORDER BY id DESC");
$stmt->execute();
$services = $stmt->fetchAll();

require_once __DIR__ . '/../../views/header.php';
require_once __DIR__ . '/../../views/sidebar.php';
?>

<div class="main-content" id="mainContent">
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <h2 class="mb-0"><i class="fas fa-concierge-bell me-2"></i>Services</h2>
                <a href="create.php" class="btn btn-primary"><i class="fas fa-plus me-1"></i>Add Service</a>
            </div>
        </div>
        <div class="card shadow-sm">
            <div class="card-body">
                <table class="table table-striped table-hover datatable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Image</th>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($services as $i => $row): ?>
                        <tr>
                            <td><?php echo $i + 1; ?></td>
                            <td>
                                <?php if (!empty($row['img'])): ?>
                                    <img src="<?php echo htmlspecialchars(BASE_URL . '/' . $row['img'], ENT_QUOTES, 'UTF-8'); ?>"
                                         alt="<?php echo htmlspecialchars($row['title'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                         style="width:50px;height:50px;object-fit:cover;border-radius:4px;">
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($row['title'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars(mb_strimwidth($row['description'] ?? '', 0, 80, '…'), ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($row['created_at'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td>
                                <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                <a href="#" class="btn btn-sm btn-danger btn-delete"
                                   data-url="delete.php?id=<?php echo $row['id']; ?>"
                                   data-name="<?php echo htmlspecialchars($row['title'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
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

<?php require_once __DIR__ . '/../../views/footer.php'; ?>
