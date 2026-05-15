<?php
session_start();
require_once '../includes/db_connect.php';
header('Content-Type: application/json');

if (!isset($_SESSION['role'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$action = $_GET['action'] ?? '';
$isAdmin = ($_SESSION['role'] === 'admin');

// ── ACTIONS ALLOWED FOR ALL LOGGED-IN USERS ─────────────────────────────────

// ── LIST active trips for tracking ───────────────────────────────────────────
if ($action === 'list_active_tracking') {
    try {
        $params = [];
        $query = "
            SELECT b.id as booking_id, b.status as booking_status,
                   v.id as vehicle_id, v.name as vehicle_name, v.type as vehicle_type,
                   v.image_path, v.gps_lat, v.gps_lng,
                   u.name as customer_name, u.phone as customer_phone
            FROM bookings b
            LEFT JOIN vehicles v ON b.vehicle_id = v.id
            LEFT JOIN users u ON b.user_id = u.id
            WHERE b.status != 'completed' AND b.status != 'cancelled'
        ";

        // Customers only see their own trips
        if (!$isAdmin) {
            $query .= " AND b.user_id = ?";
            $params[] = $_SESSION['user_id'];
        }

        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $trips = $stmt->fetchAll();

        // Fallback: If GPS is missing, assign a random central location so they at least show up
        foreach ($trips as &$t) {
            if (!$t['gps_lat'] || !$t['gps_lng']) {
                $t['gps_lat'] = 27.7120 + (rand(-100, 100) / 1000); // Randomly near Kathmandu
                $t['gps_lng'] = 85.3130 + (rand(-100, 100) / 1000);
                $t['no_signal'] = true;
            }
        }

        echo json_encode(['success' => true, 'trips' => $trips]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'DB Error: ' . $e->getMessage()]);
    }
    exit;
}

// ── UPDATE current device location (for customer) ──────────────────────────
if ($action === 'update_current_location' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'Not logged in']);
        exit;
    }

    $lat = (float) ($_POST['lat'] ?? 0);
    $lng = (float) ($_POST['lng'] ?? 0);
    $userId = $_SESSION['user_id'];

    if (!$lat || !$lng) {
        echo json_encode(['success' => false, 'message' => 'Invalid coordinates']);
        exit;
    }

    try {
        // Find the vehicle this user is currently using (confirmed booking)
        // We look for the most recent confirmed booking
        $stmt = $pdo->prepare("
            SELECT vehicle_id 
            FROM bookings 
            WHERE user_id = ? AND status = 'confirmed' 
            ORDER BY created_at DESC LIMIT 1
        ");
        $stmt->execute([$userId]);
        $booking = $stmt->fetch();

        if ($booking) {
            $pdo->prepare("UPDATE vehicles SET gps_lat = ?, gps_lng = ? WHERE id = ?")
                ->execute([$lat, $lng, $booking['vehicle_id']]);
            echo json_encode(['success' => true, 'message' => 'Location updated']);
        } else {
            echo json_encode(['success' => false, 'message' => 'No active booking found']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'DB Error']);
    }
    exit;
}

// ── GET single vehicle (for edit form pre-fill) ──────────────────────────────
if ($action === 'get_vehicle') {
    $id = (int) ($_GET['id'] ?? 0);
    $stmt = $pdo->prepare("SELECT * FROM vehicles WHERE id = ?");
    $stmt->execute([$id]);
    $v = $stmt->fetch();
    if ($v) {
        echo json_encode(['success' => true, 'vehicle' => $v]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Vehicle not found.']);
    }
    exit;
}

// ── ADMINISTRATIVE ACTIONS (ONLY FOR ADMINS) ────────────────────────────────

if (!$isAdmin && in_array($action, ['update_status', 'edit_vehicle', 'add_vehicle', 'delete_vehicle'])) {
    echo json_encode(['success' => false, 'message' => 'Admin privileges required.']);
    exit;
}


// ── UPDATE STATUS only ───────────────────────────────────────────────────────
if ($action === 'update_status' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) ($_POST['id'] ?? 0);
    $status = trim($_POST['status'] ?? '');
    $allowed = ['available', 'booked', 'maintenance'];
    if (!$id || !in_array($status, $allowed, true)) {
        echo json_encode(['success' => false, 'message' => 'Invalid parameters.']);
        exit;
    }
    try {
        $pdo->prepare("UPDATE vehicles SET status = ? WHERE id = ?")->execute([$status, $id]);
        echo json_encode(['success' => true, 'message' => 'Status updated to ' . $status . '.']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'DB Error: ' . $e->getMessage()]);
    }
    exit;
}

// ── EDIT VEHICLE ─────────────────────────────────────────────────────────────
if ($action === 'edit_vehicle' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) ($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $type = trim($_POST['type'] ?? '');
    $condition = trim($_POST['condition_type'] ?? 'city');
    $brand = trim($_POST['brand'] ?? '');
    $model_year = (int) ($_POST['model_year'] ?? 0);
    $price = (float) ($_POST['price_per_day'] ?? 0);
    $desc = trim($_POST['description'] ?? '');
    $status = trim($_POST['status'] ?? 'available');

    $allowed_types = ['car', 'bike', 'bus', 'taxi', 'jeep', 'van'];
    $allowed_status = ['available', 'booked', 'maintenance'];
    $allowed_terrain = ['city', 'offroad', 'highway', 'all-terrain'];

    if (!$id || !$name || !$type || !$price) {
        echo json_encode(['success' => false, 'message' => 'Missing required fields.']);
        exit;
    }
    if (!in_array($type, $allowed_types, true)) {
        echo json_encode(['success' => false, 'message' => 'Invalid vehicle type.']);
        exit;
    }
    if (!in_array($status, $allowed_status, true)) {
        echo json_encode(['success' => false, 'message' => 'Invalid status value.']);
        exit;
    }

    // Check for new image upload
    $newImagePath = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true)) {
            echo json_encode(['success' => false, 'message' => 'Image must be JPG, PNG, or WEBP.']);
            exit;
        }
        // Validate MIME type
        if (class_exists('finfo')) {
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->file($_FILES['image']['tmp_name']);
            if (!in_array($mime, ['image/jpeg', 'image/png', 'image/webp'], true)) {
                echo json_encode(['success' => false, 'message' => 'Invalid image content.']);
                exit;
            }
        }

        if ($_FILES['image']['size'] > 5 * 1024 * 1024) {
            echo json_encode(['success' => false, 'message' => 'Image must be under 5 MB.']);
            exit;
        }

        $uploadDir = '../uploads/vehicles/';
        if (!is_dir($uploadDir))
            mkdir($uploadDir, 0755, true);

        // Delete old image
        $oldStmt = $pdo->prepare("SELECT image_path FROM vehicles WHERE id = ?");
        $oldStmt->execute([$id]);
        $old = $oldStmt->fetch();
        if ($old && $old['image_path']) {
            $oldFile = '../' . $old['image_path'];
            if (file_exists($oldFile) && !is_dir($oldFile))
                unlink($oldFile);
        }

        $newName = 'veh_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
        $dest = $uploadDir . $newName;
        move_uploaded_file($_FILES['image']['tmp_name'], $dest);
        $newImagePath = 'uploads/vehicles/' . $newName;
    }

    try {
        if ($newImagePath) {
            $stmt = $pdo->prepare("
                UPDATE vehicles SET name=?, type=?, condition_type=?, brand=?, model_year=?,
                    price_per_day=?, description=?, status=?, image_path=?
                WHERE id=?
            ");
            $stmt->execute([$name, $type, $condition, $brand, $model_year, $price, $desc, $status, $newImagePath, $id]);
        } else {
            $stmt = $pdo->prepare("
                UPDATE vehicles SET name=?, type=?, condition_type=?, brand=?, model_year=?,
                    price_per_day=?, description=?, status=?
                WHERE id=?
            ");
            $stmt->execute([$name, $type, $condition, $brand, $model_year, $price, $desc, $status, $id]);
        }
        echo json_encode(['success' => true, 'message' => 'Vehicle updated successfully.']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'DB Error: ' . $e->getMessage()]);
    }
    exit;
}

// ── ADD VEHICLE ──────────────────────────────────────────────────────────────

if ($action === 'add_vehicle' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $type = $_POST['type'] ?? '';
    $condition = $_POST['condition_type'] ?? 'city';
    $brand = trim($_POST['brand'] ?? '');
    $model_year = (int) ($_POST['model_year'] ?? 0);
    $price = (float) ($_POST['price_per_day'] ?? 0);
    $desc = trim($_POST['description'] ?? '');

    if (empty($name) || empty($type) || empty($price)) {
        echo json_encode(['success' => false, 'message' => 'Missing required fields.']);
        exit;
    }

    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['success' => false, 'message' => 'Valid vehicle image is required.']);
        exit;
    }

    $uploadDir = '../uploads/vehicles/';
    if (!is_dir($uploadDir))
        mkdir($uploadDir, 0777, true);

    $fileName = $_FILES['image']['name'];
    $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
        echo json_encode(['success' => false, 'message' => 'Image must be JPG, PNG, or WEBP.']);
        exit;
    }

    $newImgName = 'veh_' . time() . '.' . $ext;
    $dest = $uploadDir . $newImgName;
    $dbImgPath = 'uploads/vehicles/' . $newImgName;

    if (move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO vehicles (name, type, condition_type, brand, model_year, price_per_day, description, image_path, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'available')");
            $stmt->execute([$name, $type, $condition, $brand, $model_year, $price, $desc, $dbImgPath]);

            echo json_encode(['success' => true, 'message' => 'Vehicle added.']);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'DB Error']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to upload image.']);
    }
} elseif ($action === 'delete_vehicle' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) ($_POST['id'] ?? 0);
    try {
        // Find image path beforehand to delete file if needed
        $stmt = $pdo->prepare("SELECT image_path FROM vehicles WHERE id=?");
        $stmt->execute([$id]);
        $veh = $stmt->fetch();

        if ($veh) {
            $path = '../' . $veh['image_path'];
            if (file_exists($path) && !is_dir($path))
                unlink($path);

            $pdo->prepare("DELETE FROM vehicles WHERE id = ?")->execute([$id]);
            echo json_encode(['success' => true, 'message' => 'Deleted']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Vehicle not found']);
        }
    } catch (PDOException $e) {
        // Will fail if bookings exist (unless ON DELETE CASCADE is set, which it is)
        echo json_encode(['success' => false, 'message' => 'DB Error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid Request']);
}
?>