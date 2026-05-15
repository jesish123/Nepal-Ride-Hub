<?php
// api/reviews.php — Reviews & Ratings API (submit, edit, delete, moderate)
require_once '../includes/db_connect.php';
require_once '../includes/csrf.php';
header('Content-Type: application/json; charset=UTF-8');

$action = $_GET['action'] ?? '';

// ── Submit Review ───────────────────────────────────────────────────────────
if ($action === 'submit') {
    if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'customer') {
        echo json_encode(['success' => false, 'message' => 'Only customers can submit reviews.']);
        exit;
    }

    requireValidCSRF();

    $userId = (int) $_SESSION['user_id'];
    $rating = intval($_POST['rating'] ?? 0);
    $comment = trim($_POST['comment'] ?? '');
    $type = trim($_POST['service_type'] ?? 'general');
    $serviceId = intval($_POST['service_id'] ?? 0);

    // Validate
    if ($rating < 1 || $rating > 5) {
        echo json_encode(['success' => false, 'message' => 'Please select a valid star rating (1–5).']);
        exit;
    }
    if (strlen($comment) < 20) {
        echo json_encode(['success' => false, 'message' => 'Review must be at least 20 characters long.']);
        exit;
    }
    if (strlen($comment) > 1000) {
        echo json_encode(['success' => false, 'message' => 'Review cannot exceed 1000 characters.']);
        exit;
    }

    // Whitelist service_type
    $allowedTypes = ['general', 'vehicle', 'customer_support'];
    if (!in_array($type, $allowedTypes, true)) {
        $type = 'general';
    }

    // Must have at least one completed booking
    $chk = $pdo->prepare("SELECT id FROM bookings WHERE user_id = ? AND status = 'completed' LIMIT 1");
    $chk->execute([$userId]);
    if (!$chk->fetch()) {
        echo json_encode(['success' => false, 'message' => 'You can only review after completing a booking.']);
        exit;
    }

    // Prevent duplicate pending/approved review per user per service type
    $dupChk = $pdo->prepare("
        SELECT id FROM site_reviews
        WHERE user_id = ? AND service_type = ? AND status IN ('pending','approved')
        LIMIT 1
    ");
    $dupChk->execute([$userId, $type]);
    if ($dupChk->fetch()) {
        echo json_encode(['success' => false, 'message' => 'You have already submitted a review for this service type.']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("
            INSERT INTO site_reviews (user_id, rating, comment, service_type, service_id, status)
            VALUES (:uid, :rat, :com, :typ, :sid, 'pending')
        ");
        $stmt->execute([
            'uid' => $userId,
            'rat' => $rating,
            'com' => $comment,
            'typ' => $type,
            'sid' => $serviceId ?: null
        ]);
        echo json_encode(['success' => true, 'message' => 'Thank you! Your review has been submitted and is awaiting moderation.']);
    } catch (PDOException $e) {
        $msg = (getenv('APP_ENV') === 'development') ? 'Database Error: ' . $e->getMessage() : 'Database Error occurred.';
        echo json_encode(['success' => false, 'message' => $msg]);
    }
    exit;
}

// ── Edit Review ────────────────────────────────────────────────────────────
if ($action === 'edit') {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'Not logged in.']);
        exit;
    }

    requireValidCSRF();

    $userId = (int) $_SESSION['user_id'];
    $reviewId = intval($_POST['review_id'] ?? 0);
    $rating = intval($_POST['rating'] ?? 0);
    $comment = trim($_POST['comment'] ?? '');

    if ($rating < 1 || $rating > 5) {
        echo json_encode(['success' => false, 'message' => 'Invalid rating.']);
        exit;
    }
    if (strlen($comment) < 20) {
        echo json_encode(['success' => false, 'message' => 'Review must be at least 20 characters.']);
        exit;
    }
    if (strlen($comment) > 1000) {
        echo json_encode(['success' => false, 'message' => 'Review cannot exceed 1000 characters.']);
        exit;
    }

    // Verify ownership
    $own = $pdo->prepare("SELECT id FROM site_reviews WHERE id = ? AND user_id = ?");
    $own->execute([$reviewId, $userId]);
    if (!$own->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Review not found or access denied.']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("UPDATE site_reviews SET rating = ?, comment = ?, status = 'pending', created_at = NOW() WHERE id = ? AND user_id = ?");
        $stmt->execute([$rating, $comment, $reviewId, $userId]);
        echo json_encode(['success' => true, 'message' => 'Review updated and resubmitted for moderation.']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error.']);
    }
    exit;
}

// ── Delete Review ───────────────────────────────────────────────────────────
if ($action === 'delete') {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'Not logged in.']);
        exit;
    }

    requireValidCSRF();

    $userId = (int) $_SESSION['user_id'];
    $reviewId = intval($_POST['review_id'] ?? 0);

    // Allow user to delete own, or admin to delete any
    if (($_SESSION['role'] ?? '') === 'admin') {
        $stmt = $pdo->prepare("DELETE FROM site_reviews WHERE id = ?");
        $stmt->execute([$reviewId]);
    } else {
        $stmt = $pdo->prepare("DELETE FROM site_reviews WHERE id = ? AND user_id = ?");
        $stmt->execute([$reviewId, $userId]);
    }

    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Review deleted.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Review not found or access denied.']);
    }
    exit;
}

// ── Moderate Review (Admin only) ────────────────────────────────────────────
if ($action === 'moderate') {
    if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Unauthorised.']);
        exit;
    }

    requireValidCSRF();

    $reviewId = intval($_POST['review_id'] ?? 0);
    $newStatus = in_array($_POST['status'] ?? '', ['approved', 'rejected']) ? $_POST['status'] : null;

    if (!$reviewId || !$newStatus) {
        echo json_encode(['success' => false, 'message' => 'Invalid parameters.']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("UPDATE site_reviews SET status = ? WHERE id = ?");
        $stmt->execute([$newStatus, $reviewId]);
        echo json_encode(['success' => true, 'message' => 'Review ' . $newStatus . '.']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error.']);
    }
    exit;
}

// ── Admin Reply (New Action) ────────────────────────────────────────────────
if ($action === 'reply') {
    if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Unauthorised.']);
        exit;
    }

    requireValidCSRF();

    $reviewId = intval($_POST['review_id'] ?? 0);
    $replyText = trim($_POST['reply_text'] ?? '');

    if (!$reviewId || !$replyText) {
        echo json_encode(['success' => false, 'message' => 'Review ID and reply text are required.']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("UPDATE site_reviews SET admin_reply = ?, replied_at = NOW() WHERE id = ?");
        $stmt->execute([$replyText, $reviewId]);
        echo json_encode(['success' => true, 'message' => 'Reply saved successfully.']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error.']);
    }
    exit;
}

// ── List Reviews (Admin) ────────────────────────────────────────────────────
if ($action === 'list') {
    if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Unauthorised.']);
        exit;
    }

    $status = $_GET['status'] ?? '';
    $where = $status ? "WHERE sr.status = ?" : '';
    $params = $status ? [$status] : [];

    $stmt = $pdo->prepare("
        SELECT sr.*, u.name AS reviewer_name, u.email
        FROM site_reviews sr
        JOIN users u ON sr.user_id = u.id
        $where
        ORDER BY sr.created_at DESC
        LIMIT 100
    ");
    $stmt->execute($params);
    echo json_encode(['success' => true, 'reviews' => $stmt->fetchAll()]);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Unknown action.']);
