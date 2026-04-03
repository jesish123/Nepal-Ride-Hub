<?php
include 'includes/header.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header('Location: login.php');
    exit;
}
require_once 'includes/db_connect.php';

$userId = $_SESSION['user_id'];

// Fetch User DB Data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

// Fetch Documents
$stmtDocs = $pdo->prepare("SELECT * FROM user_documents WHERE user_id = ?");
$stmtDocs->execute([$userId]);
$documents = $stmtDocs->fetchAll();

// Fetch Bookings
$stmtBookings = $pdo->prepare("
    SELECT b.*, v.name as vehicle_name, v.type as vehicle_type 
    FROM bookings b
    JOIN vehicles v ON b.vehicle_id = v.id
    WHERE b.user_id = ?
    ORDER BY b.created_at DESC
");
$stmtBookings->execute([$userId]);
$bookings = $stmtBookings->fetchAll();

$hasCitizenship = false;
$hasLicense = false;
foreach($documents as $doc) {
    if($doc['document_type'] === 'citizenship' && $doc['status'] !== 'rejected') $hasCitizenship = true;
    if($doc['document_type'] === 'license' && $doc['status'] !== 'rejected') $hasLicense = true;
}
?>

<section style="padding: 4rem 0;">
    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h2>Welcome, <?php echo htmlspecialchars($user['name']); ?></h2>
            <p><strong>Status:</strong> Customer Account</p>
        </div>

        <div class="alert alert-warning" style="margin-bottom: 2rem;">
            <i class="fas fa-exclamation-circle"></i> <strong>Important:</strong> Even with online verification, you <strong>MUST</strong> bring your original Citizenship/Passport and Driving License to our physical office to collect your vehicle. No exceptions.
        </div>

        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 2rem;">
            
            <!-- Sidebar: Document Status & Upload -->
            <div style="background: #fff; padding: 2rem; border-radius: 8px; box-shadow: var(--shadow); align-self: start;">
                <h3>Mandatory Documents</h3>
                <p style="font-size: 0.9rem; margin-bottom: 1rem;">To book a vehicle, verified citizenship and driving license are required.</p>
                
                <hr style="margin: 1.5rem 0;">

                <form id="documentUploadForm" enctype="multipart/form-data">
                    <div id="docAlert" class="alert" style="display:none;"></div>
                    <div class="form-group">
                        <label>Document Type</label>
                        <select name="document_type" required>
                            <option value="citizenship">Citizenship/Passport</option>
                            <option value="license">Driving License</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Expiry Date (if applicable)</label>
                        <input type="date" name="expiry_date">
                    </div>
                    <div class="form-group">
                        <label>Upload File (Image/PDF)</label>
                        <input type="file" name="document_file" accept=".jpg,.jpeg,.png,.pdf" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block" id="uploadBtn">Upload Document</button>
                </form>

                <h4 style="margin-top: 2rem; margin-bottom: 1rem;">Your Uploaded Documents</h4>
                <?php if (empty($documents)): ?>
                    <p style="font-size: 0.9rem; color: var(--danger);">No documents uploaded yet.</p>
                <?php else: ?>
                    <ul style="list-style: none;">
                        <?php foreach($documents as $doc): ?>
                            <li style="padding: 0.8rem; background: #f8f9fa; margin-bottom: 0.5rem; border-radius: 4px; display: flex; justify-content: space-between;">
                                <span><?php echo ucfirst($doc['document_type']); ?></span>
                                <?php 
                                    $color = $doc['status'] === 'verified' ? 'green' : ($doc['status'] === 'rejected' ? 'red' : 'orange');
                                    echo "<span style='color: $color; font-weight: 600;'>" . ucfirst($doc['status']) . "</span>";
                                ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>

            <!-- Main area: Bookings -->
            <div style="background: #fff; padding: 2rem; border-radius: 8px; box-shadow: var(--shadow);">
                <h3>Your Bookings</h3>
                <hr style="margin: 1.5rem 0;">

                <?php if (!$hasCitizenship || !$hasLicense): ?>
                    <div class="alert alert-danger">
                        <strong>Action Required:</strong> Please upload both your Citizenship and Driving License to enable booking features.
                    </div>
                <?php endif; ?>

                <?php if (empty($bookings)): ?>
                    <div style="text-align: center; padding: 2rem;">
                        <p style="color: var(--gray-text); margin-bottom: 1rem;">You don't have any bookings yet.</p>
                        <a href="vehicles.php" class="btn btn-outline">Browse Vehicles</a>
                    </div>
                <?php else: ?>
                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse; text-align: left;">
                            <thead>
                                <tr style="background: var(--light-bg); border-bottom: 2px solid var(--border-color);">
                                    <th style="padding: 1rem;">Vehicle</th>
                                    <th style="padding: 1rem;">Dates</th>
                                    <th style="padding: 1rem;">Total</th>
                                    <th style="padding: 1rem;">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($bookings as $b): ?>
                                    <tr style="border-bottom: 1px solid var(--border-color);">
                                        <td style="padding: 1rem;">
                                            <strong><?php echo htmlspecialchars($b['vehicle_name']); ?></strong><br>
                                            <small><?php echo ucfirst($b['vehicle_type']); ?> &bull; <?php echo ucfirst(str_replace('_', ' ', $b['purpose'] ?? 'travel')); ?></small>
                                        </td>
                                        <td style="padding: 1rem;">
                                            <?php echo $b['start_date']; ?> to <?php echo $b['end_date']; ?>
                                        </td>
                                        <td style="padding: 1rem;">Rs. <?php echo $b['total_amount']; ?></td>
                                        <td style="padding: 1rem;">
                                            <span style="padding: 0.3rem 0.6rem; border-radius: 20px; font-size: 0.85rem; font-weight: 600; background: <?php echo $b['status'] === 'confirmed' ? '#d4edda' : '#fff3cd'; ?>; color: <?php echo $b['status'] === 'confirmed' ? '#155724' : '#856404'; ?>;">
                                                <?php echo ucfirst($b['status']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const docForm = document.getElementById('documentUploadForm');
    if(docForm) {
        docForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = document.getElementById('uploadBtn');
            const alertEl = document.getElementById('docAlert');
            const formData = new FormData(docForm);

            btn.disabled = true;
            btn.innerHTML = 'Uploading...';

            try {
                const response = await fetch('api/manage_users.php?action=upload_document', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();
                
                alertEl.style.display = 'block';
                if(data.success) {
                    alertEl.className = 'alert alert-success';
                    alertEl.innerHTML = data.message;
                    setTimeout(() => location.reload(), 1500);
                } else {
                    alertEl.className = 'alert alert-danger';
                    alertEl.innerHTML = data.message;
                    btn.disabled = false;
                    btn.innerHTML = 'Upload Document';
                }
            } catch(err) {
                alertEl.className = 'alert alert-danger';
                alertEl.style.display = 'block';
                alertEl.innerHTML = 'Upload failed. Try again.';
                btn.disabled = false;
                btn.innerHTML = 'Upload Document';
            }
        });
    }
});
</script>

<?php include 'includes/footer.php'; ?>