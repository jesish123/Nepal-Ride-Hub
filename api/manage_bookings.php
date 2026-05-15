<?php
session_start();
require_once '../includes/db_connect.php';
header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

if ($action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }

    $userId = $_SESSION['user_id'];
    $vehId = (int) ($_POST['vehicle_id'] ?? 0);
    $start = $_POST['start_date'] ?? '';
    $end = $_POST['end_date'] ?? '';
    $purpose = $_POST['purpose'] ?? 'travel';
    $pickup = trim($_POST['pickup_location'] ?? '');
    $dropoff = trim($_POST['dropoff_location'] ?? '');

    if (empty($start) || empty($end) || $vehId <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid data.']);
        exit;
    }

    $startDate = new DateTime($start);
    $endDate = new DateTime($end);
    if ($endDate < $startDate) {
        echo json_encode(['success' => false, 'message' => 'End date cannot be before start date.']);
        exit;
    }

    try {
        // Double check documents
        $docStmt = $pdo->prepare("SELECT SUM(CASE WHEN document_type='citizenship' AND status='verified' THEN 1 ELSE 0 END) as c_ok, SUM(CASE WHEN document_type='license' AND status='verified' THEN 1 ELSE 0 END) as l_ok FROM user_documents WHERE user_id = ?");
        $docStmt->execute([$userId]);
        $docs = $docStmt->fetch();
        if ($docs['c_ok'] == 0 || $docs['l_ok'] == 0) {
            echo json_encode(['success' => false, 'message' => 'Verified documents missing.']);
            exit;
        }

        // Get vehicle
        $vStmt = $pdo->prepare("SELECT price_per_day, status FROM vehicles WHERE id=?");
        $vStmt->execute([$vehId]);
        $veh = $vStmt->fetch();

        if (!$veh || $veh['status'] !== 'available') {
            echo json_encode(['success' => false, 'message' => 'Vehicle is not available.']);
            exit;
        }

        // Check if overlaps with confirmed booking
        $olapStmt = $pdo->prepare("SELECT id FROM bookings WHERE vehicle_id=? AND status IN ('confirmed') AND (start_date <= ? AND end_date >= ?)");
        $olapStmt->execute([$vehId, $end, $start]);
        if ($olapStmt->rowCount() > 0) {
            echo json_encode(['success' => false, 'message' => 'Vehicle already booked for these dates.']);
            exit;
        }

        $withDriver = isset($_POST['with_driver']) && $_POST['with_driver'] == '1' ? 1 : 0;
        $diff = $startDate->diff($endDate)->days;
        if ($diff == 0)
            $diff = 1;
        $total = $diff * $veh['price_per_day'];

        $ins = $pdo->prepare("INSERT INTO bookings (user_id, vehicle_id, start_date, end_date, purpose, pickup_location, dropoff_location, with_driver, total_amount, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')");
        $ins->execute([$userId, $vehId, $start, $end, $purpose, $pickup, $dropoff, $withDriver, $total]);

        echo json_encode(['success' => true, 'message' => 'Booking request sent successfully to Admin.']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'DB Error']);
    }
} elseif ($action === 'update_status' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }

    $bookingId = (int) ($_POST['booking_id'] ?? 0);
    $status = $_POST['status'] ?? '';

    if (!in_array($status, ['confirmed', 'cancelled', 'completed'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid status.']);
        exit;
    }

    try {
        // If confirmed, make vehicle 'booked'. If completed/cancelled, make vehicle 'available'.
        $pdo->beginTransaction();

        $bStmt = $pdo->prepare("SELECT vehicle_id FROM bookings WHERE id=?");
        $bStmt->execute([$bookingId]);
        $bk = $bStmt->fetch();

        if ($bk) {
            $pdo->prepare("UPDATE bookings SET status=? WHERE id=?")->execute([$status, $bookingId]);
            $vehStatus = 'available';
            if ($status === 'confirmed')
                $vehStatus = 'booked';

            $pdo->prepare("UPDATE vehicles SET status=? WHERE id=?")->execute([$vehStatus, $bk['vehicle_id']]);
            $pdo->commit();

            echo json_encode(['success' => true, 'message' => 'Booking status updated.']);
        } else {
            $pdo->rollBack();
            echo json_encode(['success' => false, 'message' => 'Booking not found.']);
        }
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'DB Error']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid Action']);
}
?>