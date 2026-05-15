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

// Fetch User Data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

// Fetch Documents
$stmtDocs = $pdo->prepare("SELECT * FROM user_documents WHERE user_id = ? ORDER BY uploaded_at DESC");
$stmtDocs->execute([$userId]);
$documents = $stmtDocs->fetchAll();

$isVerified = true;
$role = $_SESSION['role'] ?? 'customer';

if ($role === 'customer') {
    $docTypes = ['citizenship', 'license'];
    foreach ($docTypes as $type) {
        $found = false;
        foreach ($documents as $doc) {
            if ($doc['document_type'] === $type && $doc['status'] === 'verified') {
                $found = true;
                break;
            }
        }
        if (!$found)
            $isVerified = false;
    }
}
?>

<section style="padding: 4rem 0; background: #f8f9fa; min-height: 80vh;">
    <div class="container">
        <div style="max-width: 900px; margin: 0 auto;">

            <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 2rem;">
                <div>
                    <h2
                        style="font-family: 'Outfit', sans-serif; font-size: 2.2rem; font-weight: 800; color: #111; margin-bottom: 0.5rem;">
                        My Profile</h2>
                    <p style="color: #666;">Manage your personal details and verification documents.</p>
                </div>
                <?php if ($role === 'customer'): ?>
                    <div
                        style="padding: 0.8rem 1.4rem; border-radius: 50px; font-weight: 700; font-size: 0.9rem; background: <?php echo $isVerified ? '#d4edda' : '#fff3cd'; ?>; color: <?php echo $isVerified ? '#155724' : '#856404'; ?>; display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fa-solid <?php echo $isVerified ? 'fa-circle-check' : 'fa-circle-exclamation'; ?>"></i>
                        <?php echo $isVerified ? 'Fully Verified' : 'Verification Required'; ?>
                    </div>
                <?php else: ?>
                    <div
                        style="padding: 0.8rem 1.4rem; border-radius: 50px; font-weight: 700; font-size: 0.9rem; background: #e0e7ff; color: #3561ff; display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fa-solid fa-user-shield"></i>
                        Official Administrator
                    </div>
                <?php endif; ?>
            </div>

            <div
                style="display: grid; grid-template-columns: <?php echo $role === 'admin' ? '1fr' : '1fr 1.5fr'; ?>; gap: 2rem;">

                <!-- Left: Account Details & Edit -->
                <div
                    style="background: #fff; padding: 2.5rem; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05);">
                    <div style="text-align: center; margin-bottom: 2rem;">
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($user['name']); ?>&background=3561ff&color=fff&size=128"
                            alt="Avatar"
                            style="width: 100px; height: 100px; border-radius: 50%; margin-bottom: 1rem; border: 4px solid #f0f4ff;">
                        <h3 style="font-weight: 800; color: #111; margin-bottom: 0.2rem;">
                            <?php echo htmlspecialchars($user['name']); ?>
                        </h3>
                        <p style="color: #888; font-size: 0.9rem;"><?php echo htmlspecialchars($user['email']); ?></p>
                    </div>

                    <form id="profileForm">
                        <div id="profileAlert" class="alert"
                            style="display:none; font-size: 0.85rem; padding: 0.8rem; margin-bottom: 1.5rem;"></div>

                        <div style="margin-bottom: 1.2rem;">
                            <label
                                style="display: block; font-weight: 700; font-size: 0.85rem; color: #444; margin-bottom: 0.5rem; text-transform: uppercase;">Full
                                Name</label>
                            <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>"
                                required
                                style="width: 100%; padding: 0.8rem 1rem; border: 1.5px solid #eee; border-radius: 10px; font-family: 'Inter', sans-serif;">
                        </div>

                        <div style="margin-bottom: 1.5rem;">
                            <label
                                style="display: block; font-weight: 700; font-size: 0.85rem; color: #444; margin-bottom: 0.5rem; text-transform: uppercase;">Phone
                                Number</label>
                            <input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>"
                                required
                                style="width: 100%; padding: 0.8rem 1rem; border: 1.5px solid #eee; border-radius: 10px; font-family: 'Inter', sans-serif;">
                        </div>

                        <div style="margin-bottom: 1.2rem;">
                            <label
                                style="display: block; font-weight: 700; font-size: 0.85rem; color: #444; margin-bottom: 0.5rem; text-transform: uppercase;">Email
                                Address</label>
                            <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>"
                                required
                                style="width: 100%; padding: 0.8rem 1rem; border: 1.5px solid #eee; border-radius: 10px; font-family: 'Inter', sans-serif;">
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                            <div>
                                <label
                                    style="display: block; font-weight: 700; font-size: 0.85rem; color: #444; margin-bottom: 0.5rem; text-transform: uppercase;">Location</label>
                                <input type="text" name="location"
                                    value="<?php echo htmlspecialchars($user['location'] ?? ''); ?>"
                                    placeholder="e.g. Kathmandu"
                                    style="width: 100%; padding: 0.8rem 1rem; border: 1.5px solid #eee; border-radius: 10px; font-family: 'Inter', sans-serif;">
                            </div>
                            <div>
                                <label
                                    style="display: block; font-weight: 700; font-size: 0.85rem; color: #444; margin-bottom: 0.5rem; text-transform: uppercase;">Country</label>
                                <input type="text" name="country"
                                    value="<?php echo htmlspecialchars($user['country'] ?? ''); ?>" placeholder="Nepal"
                                    style="width: 100%; padding: 0.8rem 1rem; border: 1.5px solid #eee; border-radius: 10px; font-family: 'Inter', sans-serif;">
                            </div>
                        </div>

                        <button type="submit" class="btn-blue-solid"
                            style="width: 100%; padding: 1rem; border-radius: 10px; font-weight: 700;">Update
                            Profile</button>
                    </form>
                </div>

                <?php if ($role === 'customer'): ?>
                    <!-- Right: Documents Management -->
                    <div
                        style="background: #fff; padding: 2.5rem; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05);">
                        <h3
                            style="font-weight: 800; color: #111; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem;">
                            <i class="fa-solid fa-file-shield" style="color: #3561ff;"></i> Verification Documents
                        </h3>

                        <form id="docUploadForm" enctype="multipart/form-data"
                            style="margin-bottom: 2.5rem; padding: 1.5rem; background: #f8fbff; border-radius: 15px; border: 1px dashed #3561ff;">
                            <div id="docAlert" class="alert"
                                style="display:none; font-size: 0.85rem; padding: 0.8rem; margin-bottom: 1rem;"></div>

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                                <div>
                                    <label
                                        style="display: block; font-weight: 700; font-size: 0.75rem; color: #555; margin-bottom: 0.4rem; text-transform: uppercase;">Document
                                        Type</label>
                                    <select name="document_type" required
                                        style="width: 100%; padding: 0.7rem; border: 1px solid #ddd; border-radius: 8px;">
                                        <option value="citizenship">Citizenship/Passport</option>
                                        <option value="license">Driving License</option>
                                    </select>
                                </div>
                                <div>
                                    <label
                                        style="display: block; font-weight: 700; font-size: 0.75rem; color: #555; margin-bottom: 0.4rem; text-transform: uppercase;">File
                                        (JPG/PNG/PDF)</label>
                                    <input type="file" name="document_file" accept=".jpg,.jpeg,.png,.pdf" required
                                        style="font-size: 0.8rem;">
                                </div>
                            </div>
                            <button type="submit" class="btn-blue-solid"
                                style="width: 100%; background: #111; padding: 0.8rem;">Upload & Verify</button>
                        </form>

                        <h4 style="font-weight: 800; font-size: 1rem; color: #111; margin-bottom: 1rem;">Document History
                        </h4>
                        <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                            <?php if (empty($documents)): ?>
                                <p style="color: #888; font-size: 0.9rem;">No documents uploaded yet.</p>
                            <?php else: ?>
                                <?php foreach ($documents as $doc): ?>
                                    <div
                                        style="display: flex; justify-content: space-between; align-items: center; padding: 1rem 1.2rem; background: #fff; border: 1px solid #f0f0f0; border-radius: 12px;">
                                        <div style="display: flex; align-items: center; gap: 1rem;">
                                            <div
                                                style="width: 40px; height: 40px; background: #f0f4ff; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #3561ff;">
                                                <i
                                                    class="fa-solid <?php echo $doc['document_type'] === 'license' ? 'fa-id-card' : 'fa-id-badge'; ?>"></i>
                                            </div>
                                            <div>
                                                <span
                                                    style="display: block; font-weight: 700; color: #111; font-size: 0.95rem; text-transform: capitalize;"><?php echo $doc['document_type']; ?></span>
                                                <span style="font-size: 0.75rem; color: #999;">Uploaded:
                                                    <?php echo date('M d, Y', strtotime($doc['uploaded_at'])); ?></span>
                                            </div>
                                        </div>
                                        <div style="text-align: right;">
                                            <?php
                                            $col = $doc['status'] === 'verified' ? '#28a745' : ($doc['status'] === 'rejected' ? '#da291c' : '#ff9800');
                                            $bg = $doc['status'] === 'verified' ? '#e8f5e9' : ($doc['status'] === 'rejected' ? '#fbe9e7' : '#fff3e0');
                                            ?>
                                            <span
                                                style="display: inline-block; padding: 0.3rem 0.8rem; border-radius: 20px; font-size: 0.75rem; font-weight: 800; background: <?php echo $bg; ?>; color: <?php echo $col; ?>; margin-bottom: 0.3rem;">
                                                <?php echo strtoupper($doc['status']); ?>
                                            </span>
                                            <a href="<?php echo htmlspecialchars($doc['file_path']); ?>" target="_blank"
                                                style="display: block; font-size: 0.75rem; color: #3561ff; text-decoration: none; font-weight: 600;">View
                                                File <i class="fa-solid fa-arrow-up-right-from-square"></i></a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Tab profile update
        const profileForm = document.getElementById('profileForm');
        profileForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const alertEl = document.getElementById('profileAlert');
            const formData = new FormData(profileForm);

            const response = await fetch('api/manage_users.php?action=update_profile', {
                method: 'POST',
                body: formData
            });
            const data = await response.json();

            alertEl.style.display = 'block';
            alertEl.className = 'alert ' + (data.success ? 'alert-success' : 'alert-danger');
            alertEl.textContent = data.message;
            if (data.success) setTimeout(() => location.reload(), 1500);
        });

        // Document upload
        const docForm = document.getElementById('docUploadForm');
        docForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const alertEl = document.getElementById('docAlert');
            const formData = new FormData(docForm);

            const response = await fetch('api/manage_users.php?action=upload_document', {
                method: 'POST',
                body: formData
            });
            const data = await response.json();

            alertEl.style.display = 'block';
            alertEl.className = 'alert ' + (data.success ? 'alert-success' : 'alert-danger');
            alertEl.textContent = data.message;
            if (data.success) setTimeout(() => location.reload(), 1500);
        });
    });
</script>

<?php include 'includes/footer.php'; ?>