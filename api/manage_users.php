<?php
// api/manage_users.php
session_start();
require_once '../includes/db_connect.php';
header('Content-Type: application/json');

$action = $_GET['action'] ?? '';
$isPost = $_SERVER['REQUEST_METHOD'] === 'POST';

// Customer: Upload document
if ($action === 'upload_document' && $isPost) {
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }
    
    $userId = $_SESSION['user_id'];
    $docType = $_POST['document_type'] ?? '';
    $expiry = !empty($_POST['expiry_date']) ? $_POST['expiry_date'] : null;
    
    if (!in_array($docType, ['citizenship', 'license', 'passport'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid document type']);
        exit;
    }

    if (!isset($_FILES['document_file']) || $_FILES['document_file']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['success' => false, 'message' => 'File upload error']);
        exit;
    }

    $fileTmpPath = $_FILES['document_file']['tmp_name'];
    $fileName = $_FILES['document_file']['name'];
    $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'pdf'];

    if (!in_array($extension, $allowed)) {
        echo json_encode(['success' => false, 'message' => 'Invalid file format. Ensure it is JPG, PNG, or PDF']);
        exit;
    }

    // Ensure directory exists
    $uploadDir = '../uploads/documents/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    $newFileName = $userId . '_' . $docType . '_' . time() . '.' . $extension;
    $destPath = $uploadDir . $newFileName;
    $dbPath = 'uploads/documents/' . $newFileName;

    if (move_uploaded_file($fileTmpPath, $destPath)) {
        try {
            // Delete previous pending/rejected of same type if re-uploading
            $pdo->prepare("DELETE FROM user_documents WHERE user_id=? AND document_type=? AND status!='verified'")
                ->execute([$userId, $docType]);

            $stmt = $pdo->prepare("INSERT INTO user_documents (user_id, document_type, file_path, status, expiry_date) VALUES (?, ?, ?, 'pending', ?)");
            $stmt->execute([$userId, $docType, $dbPath, $expiry]);
            
            echo json_encode(['success' => true, 'message' => 'Document uploaded explicitly. Waiting for Admin verification.']);
        } catch(PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'DB Error: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to move uploaded file.']);
    }
}
// Admin: Fetch pending docs
elseif ($action === 'list_pending_docs') {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }
    try {
        $stmt = $pdo->query("
            SELECT d.*, u.name as user_name, u.email 
            FROM user_documents d 
            JOIN users u ON d.user_id = u.id 
            WHERE d.status = 'pending'
            ORDER BY d.uploaded_at ASC
        ");
        echo json_encode(['success' => true, 'documents' => $stmt->fetchAll()]);
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'DB Error']);
    }
}
// Admin: Verify/Reject doc
elseif ($action === 'verify_document' && $isPost) {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }
    
    $docId = $_POST['document_id'] ?? 0;
    $status = $_POST['status'] ?? '';
    
    if (!in_array($status, ['verified', 'rejected'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid status']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("UPDATE user_documents SET status = ? WHERE id = ?");
        $stmt->execute([$status, $docId]);
        echo json_encode(['success' => true, 'message' => 'Document ' . $status . ' successfully.']);
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'DB Error']);
    }
} 
// Admin: Fetch documents for a specific user
elseif ($action === 'get_user_documents') {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }
    
    $userId = $_GET['user_id'] ?? 0;
    try {
        $stmt = $pdo->prepare("SELECT * FROM user_documents WHERE user_id = ? ORDER BY uploaded_at DESC");
        $stmt->execute([$userId]);
        echo json_encode(['success' => true, 'documents' => $stmt->fetchAll()]);
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'DB Error']);
    }
}
// Customer: Update profile information
elseif ($action === 'update_profile' && $isPost) {
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }
    
    $userId = $_SESSION['user_id'];
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    
    if (empty($name) || empty($phone)) {
        echo json_encode(['success' => false, 'message' => 'Name and phone are required.']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("UPDATE users SET name = ?, phone = ? WHERE id = ?");
        $stmt->execute([$name, $phone, $userId]);
        
        // Update session name for the header
        $_SESSION['name'] = $name;
        
        echo json_encode(['success' => true, 'message' => 'Profile updated successfully!']);
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'DB Error']);
    }
}
else {
    echo json_encode(['success' => false, 'message' => 'Invalid Action']);
}
?>