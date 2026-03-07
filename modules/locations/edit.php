<?php
session_start();
require_once __DIR__ . '/../../middleware/auth_check.php';
require_once __DIR__ . '/../../config/database.php';

$pageTitle = 'Edit Location';
$pdo = getDBConnection();

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    $_SESSION['error_message'] = 'Invalid location ID.';
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM locations WHERE id = ?");
$stmt->execute([$id]);
$location = $stmt->fetch();

if (!$location) {
    $_SESSION['error_message'] = 'Location not found.';
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare("SELECT id, floor_name FROM floors ORDER BY floor_name");
$stmt->execute();
$floors = $stmt->fetchAll();

$stmt = $pdo->prepare("SELECT id, department_name FROM departments ORDER BY department_name");
$stmt->execute();
$departments = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error_message'] = 'Invalid CSRF token.';
        header('Location: index.php');
        exit;
    }

    $location_name = trim($_POST['location_name'] ?? '');
    $room_number = trim($_POST['room_number'] ?? '');
    $floor_id = !empty($_POST['floor_id']) ? (int)$_POST['floor_id'] : null;
    $department_id = !empty($_POST['department_id']) ? (int)$_POST['department_id'] : null;
    $description = trim($_POST['description'] ?? '');

    if ($location_name === '') {
        $_SESSION['error_message'] = 'Location name is required.';
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE locations SET location_name = ?, room_number = ?, floor_id = ?, department_id = ?, description = ? WHERE id = ?");
            $stmt->execute([$location_name, $room_number, $floor_id, $department_id, $description, $id]);
            $_SESSION['success_message'] = 'Location updated successfully.';
            header('Location: index.php');
            exit;
        } catch (PDOException $e) {
            $_SESSION['error_message'] = 'Error updating location: ' . $e->getMessage();
        }
    }

    $location['location_name'] = $location_name;
    $location['room_number'] = $room_number;
    $location['floor_id'] = $floor_id;
    $location['department_id'] = $department_id;
    $location['description'] = $description;
}

require_once __DIR__ . '/../../views/header.php';
require_once __DIR__ . '/../../views/sidebar.php';
?>

<div class="main-content" id="mainContent">
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="mb-0"><i class="fas fa-edit me-2"></i>Edit Location</h2>
            </div>
        </div>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?php echo htmlspecialchars($_SESSION['error_message'], ENT_QUOTES, 'UTF-8'); unset($_SESSION['error_message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm">
            <div class="card-body">
                <form method="POST" action="edit.php?id=<?php echo $id; ?>">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

                    <div class="mb-3">
                        <label for="location_name" class="form-label">Location Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="location_name" name="location_name" required value="<?php echo htmlspecialchars($location['location_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                    </div>

                    <div class="mb-3">
                        <label for="room_number" class="form-label">Room Number</label>
                        <input type="text" class="form-control" id="room_number" name="room_number" value="<?php echo htmlspecialchars($location['room_number'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                    </div>

                    <div class="mb-3">
                        <label for="floor_id" class="form-label">Floor</label>
                        <select class="form-select" id="floor_id" name="floor_id">
                            <option value="">-- Select Floor --</option>
                            <?php foreach ($floors as $floor): ?>
                                <option value="<?php echo $floor['id']; ?>" <?php echo ($location['floor_id'] == $floor['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($floor['floor_name'], ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="department_id" class="form-label">Department</label>
                        <select class="form-select" id="department_id" name="department_id">
                            <option value="">-- Select Department --</option>
                            <?php foreach ($departments as $dept): ?>
                                <option value="<?php echo $dept['id']; ?>" <?php echo ($location['department_id'] == $dept['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($dept['department_name'], ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($location['description'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Update</button>
                        <a href="index.php" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i>Back</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../views/footer.php'; ?>
