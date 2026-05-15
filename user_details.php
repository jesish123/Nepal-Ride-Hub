<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
include 'includes/header.php';
require_once 'includes/db_connect.php';

$userId = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

$role = $user['role'] ?? 'customer';
if ($role === 'admin') {
    $isFullyVerified = true; // Admins are inherently trusted
} else {
    // Check Verification Status for customers
    $stmtDocs = $pdo->prepare("SELECT COUNT(*) FROM user_documents WHERE user_id = ? AND status = 'verified'");
    $stmtDocs->execute([$userId]);
    $verifiedDocs = $stmtDocs->fetchColumn();
    $isFullyVerified = $verifiedDocs >= 2; // Assuming citizenship and license
}
?>
<style>
    .details-page {
        padding: 5rem 0;
        background: #f8fbff;
        min-height: 80vh;
    }

    .profile-summary-card {
        background: #fff;
        padding: 4rem;
        border-radius: 30px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.05);
        max-width: 800px;
        margin: 0 auto;
    }

    .profile-header {
        display: flex;
        align-items: center;
        gap: 2.5rem;
        margin-bottom: 4rem;
        padding-bottom: 3rem;
        border-bottom: 1px solid #f0f0f0;
    }

    .profile-avatar {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        border: 5px solid #f0f4ff;
    }

    .info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 3rem;
    }

    .info-block label {
        display: block;
        font-weight: 700;
        font-size: 0.8rem;
        color: #888;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 0.8rem;
    }

    .info-block p {
        font-size: 1.1rem;
        font-weight: 600;
        color: #111;
    }
</style>

<div class="details-page">
    <div class="container">
        <div class="profile-summary-card">
            <div class="profile-header">
                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($user['name']); ?>&background=3561ff&color=fff&size=200"
                    alt="Avatar" class="profile-avatar">
                <div>
                    <h1 style="font-size: 2.2rem; font-weight: 800; color: #111; margin-bottom: 0.5rem;">
                        <?php echo htmlspecialchars($user['name']); ?>
                    </h1>
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <span
                            style="background: #eef2f3; padding: 0.4rem 1rem; border-radius: 50px; font-size: 0.8rem; font-weight: 700; color: #555;">
                            <?php echo ucfirst($user['role']); ?> Account
                        </span>
                        <?php if ($role === 'admin'): ?>
                            <span
                                style="color: #3561ff; font-weight: 700; font-size: 0.85rem; display: flex; align-items: center; gap: 0.3rem;">
                                <i class="fa-solid fa-user-shield"></i> Official Account
                            </span>
                        <?php elseif ($isFullyVerified): ?>
                            <span
                                style="color: #28a745; font-weight: 700; font-size: 0.85rem; display: flex; align-items: center; gap: 0.3rem;">
                                <i class="fa-solid fa-circle-check"></i> Verified
                            </span>
                        <?php else: ?>
                            <span
                                style="color: #ff9800; font-weight: 700; font-size: 0.85rem; display: flex; align-items: center; gap: 0.3rem;">
                                <i class="fa-solid fa-circle-exclamation"></i> Action Required
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="info-grid">
                <div class="info-block">
                    <label>Full Name</label>
                    <p><?php echo htmlspecialchars($user['name']); ?></p>
                </div>
                <div class="info-block">
                    <label>Email Address</label>
                    <p><?php echo htmlspecialchars($user['email']); ?></p>
                </div>
                <div class="info-block">
                    <label>Phone Number</label>
                    <p><?php echo htmlspecialchars($user['phone'] ?: 'Not Provided'); ?></p>
                </div>
                <div class="info-block">
                    <label>Location</label>
                    <p><?php echo htmlspecialchars($user['location'] ?: 'Not Set'); ?></p>
                </div>
                <div class="info-block">
                    <label>Country</label>
                    <p><?php echo htmlspecialchars($user['country'] ?: 'Not Set'); ?></p>
                </div>
                <div class="info-block">
                    <label>Member Since</label>
                    <p><?php echo date('F d, Y', strtotime($user['created_at'])); ?></p>
                </div>
            </div>

            <div style="margin-top: 5rem; display: flex; gap: 1.5rem;">
                <a href="profile.php" class="btn-blue-solid" style="flex: 1; text-align: center; padding: 1.2rem;">Edit
                    My Profile</a>
                <a href="<?php echo ($user['role'] === 'admin' ? 'admin_dashboard.php' : 'customer_dashboard.php'); ?>"
                    class="btn-outline"
                    style="flex: 1; text-align: center; padding: 1.2rem; border-radius: 12px; font-weight: 700;">Back to
                    Dashboard</a>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>