<?php
session_start();
require_once __DIR__ . '/../../middleware/auth_check.php';
require_once __DIR__ . '/../../config/database.php';

$pageTitle = 'Print Barcode';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $pdo->prepare("SELECT * FROM assets WHERE id = :id");
$stmt->execute([':id' => $id]);
$asset = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$asset) {
    $_SESSION['error_message'] = 'Asset not found.';
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/../../views/header.php';
require_once __DIR__ . '/../../views/sidebar.php';
?>

<style>
@media print {
    .navbar, .sidebar, .no-print, #sidebarOverlay {
        display: none !important;
    }
    .main-content {
        margin-left: 0 !important;
        padding: 0 !important;
    }
    .container-fluid {
        padding: 0 !important;
    }
    .card {
        border: none !important;
        box-shadow: none !important;
    }
    .barcode-print-area {
        text-align: center;
        padding: 20px;
    }
}
</style>

<div class="main-content" id="mainContent">
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4 no-print">
            <h1 class="h3 mb-0">Print Barcode</h1>
            <div>
                <button onclick="window.print();" class="btn btn-dark">
                    <i class="fas fa-print"></i> Print
                </button>
                <a href="view.php?id=<?php echo (int)$asset['id']; ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Asset
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-body barcode-print-area text-center py-5">
                <div class="mb-3">
                    <h2 class="fw-bold mb-1" style="font-size: 2rem;">
                        <?php echo htmlspecialchars($asset['asset_tag'], ENT_QUOTES, 'UTF-8'); ?>
                    </h2>
                </div>

                <div class="mb-3">
                    <h4 class="text-muted">
                        <?php echo htmlspecialchars($asset['asset_name'], ENT_QUOTES, 'UTF-8'); ?>
                    </h4>
                </div>

                <?php if (!empty($asset['model_number'])): ?>
                    <div class="mb-2">
                        <span class="text-muted">Model:</span>
                        <span class="fw-semibold"><?php echo htmlspecialchars($asset['model_number'], ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                <?php endif; ?>

                <?php if (!empty($asset['serial_number'])): ?>
                    <div class="mb-3">
                        <span class="text-muted">S/N:</span>
                        <span class="fw-semibold"><?php echo htmlspecialchars($asset['serial_number'], ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                <?php endif; ?>

                <?php if (!empty($asset['barcode'])): ?>
                    <div class="mx-auto mt-4" style="max-width: 400px;">
                        <div style="border: 2px solid #333; padding: 15px 25px; display: inline-block; font-family: 'Courier New', Courier, monospace; font-size: 1.5rem; letter-spacing: 4px; font-weight: bold;">
                            <?php echo htmlspecialchars($asset['barcode'], ENT_QUOTES, 'UTF-8'); ?>
                        </div>
                        <div class="mt-2 text-muted" style="font-size: 0.85rem;">Barcode</div>
                    </div>
                <?php else: ?>
                    <div class="mt-4 text-muted">
                        <em>No barcode value assigned</em>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../views/footer.php'; ?>
