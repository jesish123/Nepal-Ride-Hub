<?php
session_start();
require_once '../includes/db_connect.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$action   = $_GET['action'] ?? '';
$userRole = $_SESSION['role'] ?? 'customer';

// -------------------------------------------------------
// ACTION: report  (customers only)
// -------------------------------------------------------
if ($action === 'report' && $_SERVER['REQUEST_METHOD'] === 'POST') {

    // Hard block for admins
    if ($userRole === 'admin') {
        echo json_encode(['success' => false, 'message' => 'Admins cannot submit emergency reports. This feature is for customers only.']);
        exit;
    }

    $userId    = $_SESSION['user_id'];
    $bookingId = !empty($_POST['booking_id']) ? (int)  $_POST['booking_id']    : null;
    $type      = trim($_POST['incident_type']  ?? '');
    $desc      = trim($_POST['description']    ?? '');
    $loc       = trim($_POST['location_text']  ?? '');
    $lat       = !empty($_POST['gps_lat'])     ? (float) $_POST['gps_lat']     : null;
    $lng       = !empty($_POST['gps_lng'])     ? (float) $_POST['gps_lng']     : null;

    if (empty($type) || empty($desc) || empty($loc)) {
        echo json_encode(['success' => false, 'message' => 'Nature, Details, and Location are required.']);
        exit;
    }

    try {
        $stmt = $pdo->prepare(
            "INSERT INTO emergency_incidents
                (user_id, booking_id, incident_type, description, location_text, gps_lat, gps_lng, status)
             VALUES (?, ?, ?, ?, ?, ?, ?, 'open')"
        );
        $stmt->execute([$userId, $bookingId, $type, $desc, $loc, $lat, $lng]);

        echo json_encode(['success' => true, 'message' => 'SOS Transmitted successfully! The management team has been alerted immediately.']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'System error recording transmission. Please call the emergency numbers instead.']);
    }

// -------------------------------------------------------
// ACTION: list  (admins only)
// -------------------------------------------------------
} elseif ($action === 'list' && $userRole === 'admin') {

    try {
        $rows = $pdo->query(
            "SELECT ei.id, ei.incident_type, ei.description, ei.location_text,
                    ei.gps_lat, ei.gps_lng, ei.status, ei.created_at,
                    u.name AS customer_name, u.email AS customer_email, u.phone AS customer_phone,
                    ei.booking_id
             FROM emergency_incidents ei
             JOIN users u ON ei.user_id = u.id
             ORDER BY ei.created_at DESC"
        )->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(['success' => true, 'reports' => $rows]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Could not load reports.']);
    }

// -------------------------------------------------------
// ACTION: update_status  (admins only)
// -------------------------------------------------------
} elseif ($action === 'update_status' && $userRole === 'admin' && $_SERVER['REQUEST_METHOD'] === 'POST') {

    $id     = (int) ($_POST['id']     ?? 0);
    $status = trim($_POST['status']   ?? '');
    $allowed = ['open', 'in_progress', 'resolved'];

    if (!$id || !in_array($status, $allowed)) {
        echo json_encode(['success' => false, 'message' => 'Invalid parameters.']);
        exit;
    }

    try {
        $pdo->prepare("UPDATE emergency_incidents SET status = ? WHERE id = ?")->execute([$status, $id]);
        echo json_encode(['success' => true, 'message' => 'Status updated.']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Failed to update status.']);
    }

} else {
    echo json_encode(['success' => false, 'message' => 'Invalid endpoint action.']);
}
?>
