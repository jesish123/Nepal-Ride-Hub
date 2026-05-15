<?php
include 'includes/header.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}
require_once 'includes/db_connect.php';

// Fetch all customers
$stmt = $pdo->query("SELECT id, name, email, phone, created_at FROM users WHERE role = 'customer' ORDER BY created_at DESC");
$users = $stmt->fetchAll();
?>

<section style="padding: 4rem 0;">
    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h2>Manage Users</h2>
            <a href="admin_dashboard.php" class="btn btn-outline">Back to Dashboard</a>
        </div>

        <div style="background: #fff; padding: 2rem; border-radius: 8px; box-shadow: var(--shadow);">
            <?php if (empty($users)): ?>
                <p>No customers found.</p>
            <?php else: ?>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; text-align: left;">
                        <thead>
                            <tr style="background: var(--light-bg); border-bottom: 2px solid var(--border-color);">
                                <th style="padding: 1rem;">ID</th>
                                <th style="padding: 1rem;">Name</th>
                                <th style="padding: 1rem;">Email</th>
                                <th style="padding: 1rem;">Phone</th>
                                <th style="padding: 1rem;">Joined On</th>
                                <th style="padding: 1rem;">Document Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $u): ?>
                                <?php
                                // Fetch all docs for this user
                                $dStmt = $pdo->prepare("SELECT document_type, status FROM user_documents WHERE user_id=?");
                                $dStmt->execute([$u['id']]);
                                $allDocs = $dStmt->fetchAll();

                                $verified = [];
                                $pending = [];
                                foreach ($allDocs as $doc) {
                                    if ($doc['status'] === 'verified')
                                        $verified[] = $doc['document_type'];
                                    elseif ($doc['status'] === 'pending')
                                        $pending[] = $doc['document_type'];
                                }

                                $docTxt = [];
                                if (in_array('citizenship', $verified) || in_array('passport', $verified))
                                    $docTxt[] = "Citizenship";
                                if (in_array('license', $verified))
                                    $docTxt[] = "License";

                                if (!empty($docTxt)) {
                                    $docStr = "<span style='color:green;'>" . implode(", ", $docTxt) . "</span>";
                                } elseif (!empty($pending)) {
                                    $docStr = "<span style='color:orange;'>Pending Review (" . count($pending) . ")</span>";
                                } else {
                                    $docStr = "<span style='color:red;'>None Verified</span>";
                                }
                                ?>
                                <tr style="border-bottom: 1px solid var(--border-color);">
                                    <td style="padding: 1rem;"><?php echo $u['id']; ?></td>
                                    <td style="padding: 1rem;"><strong><?php echo htmlspecialchars($u['name']); ?></strong></td>
                                    <td style="padding: 1rem;"><?php echo htmlspecialchars($u['email']); ?></td>
                                    <td style="padding: 1rem;"><?php echo htmlspecialchars($u['phone']); ?></td>
                                    <td style="padding: 1rem;"><?php echo date('Y-m-d', strtotime($u['created_at'])); ?></td>
                                    <td style="padding: 1rem;">
                                        <button class="btn-view-docs"
                                            onclick="viewUserDocs(<?php echo $u['id']; ?>, '<?php echo addslashes($u['name']); ?>')"
                                            style="background: none; border: none; cursor: pointer; text-align: left; padding: 0;">
                                            <?php echo $docStr; ?>
                                            <i class="fa-solid fa-up-right-from-square"
                                                style="font-size: 0.7rem; margin-left: 5px; color: #888;"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Document Verification Modal -->
<div id="docModal"
    style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 1000; align-items: center; justify-content: center;">
    <div
        style="background: #fff; width: 90%; max-width: 800px; max-height: 90vh; border-radius: 20px; overflow: hidden; display: flex; flex-direction: column; box-shadow: 0 30px 60px rgba(0,0,0,0.3);">
        <div
            style="padding: 1.5rem 2rem; background: #f8f9fa; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center;">
            <h3 id="modalTitle" style="margin: 0; font-family: 'Outfit', sans-serif; font-weight: 800; color: #111;">
                User Documents</h3>
            <button onclick="closeModal()"
                style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #888;">&times;</button>
        </div>
        <div id="modalBody"
            style="padding: 2rem; overflow-y: auto; flex-grow: 1; display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.5rem;">
            <!-- Documents will be loaded here -->
            <p>Loading documents...</p>
        </div>
    </div>
</div>

<script>
    async function viewUserDocs(userId, userName) {
        const modal = document.getElementById('docModal');
        const modalTitle = document.getElementById('modalTitle');
        const modalBody = document.getElementById('modalBody');

        modalTitle.textContent = "Documents for " + userName;
        modalBody.innerHTML = "<p>Loading...</p>";
        modal.style.display = 'flex';

        try {
            const response = await fetch('api/manage_users.php?action=get_user_documents&user_id=' + userId);
            const data = await response.json();

            if (data.success) {
                if (data.documents.length === 0) {
                    modalBody.innerHTML = "<p style='grid-column: 1/-1; text-align: center; color: #888; padding: 2rem;'>No documents uploaded by this user.</p>";
                } else {
                    modalBody.innerHTML = "";
                    data.documents.forEach(doc => {
                        const statusColor = doc.status === 'verified' ? '#28a745' : (doc.status === 'rejected' ? '#da291c' : '#ff9800');
                        const card = document.createElement('div');
                        card.style.background = '#fcfcfc';
                        card.style.border = '1px solid #eee';
                        card.style.padding = '1.2rem';
                        card.style.borderRadius = '12px';

                        card.innerHTML = `
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                            <span style="font-weight: 800; text-transform: capitalize; color: #111;">${doc.document_type}</span>
                            <span style="font-size: 0.75rem; font-weight: 700; color: ${statusColor};">${doc.status.toUpperCase()}</span>
                        </div>
                        <a href="${doc.file_path}" target="_blank" style="display: block; width: 100%; height: 200px; background: url('${doc.file_path}') center/cover no-repeat; border-radius: 8px; border: 1px solid #eee; margin-bottom: 1rem; background-color: #eee; position: relative;">
                            ${doc.file_path.endsWith('.pdf') ? '<div style="position:absolute; top:0; left:0; width:100%; height:100%; display:flex; align-items:center; justify-content:center; background:rgba(255,255,255,0.8); font-weight:700; color:#333;">PDF File (Click to View)</div>' : ''}
                        </a>
                        <div style="display: flex; gap: 0.5rem;">
                            <button onclick="updateDocStatus(${doc.id}, 'verified')" style="flex: 1; padding: 0.6rem; background: #28a745; color: #fff; border: none; border-radius: 6px; font-weight: 700; cursor: pointer; font-size: 0.8rem;">Verify</button>
                            <button onclick="updateDocStatus(${doc.id}, 'rejected')" style="flex: 1; padding: 0.6rem; background: #da291c; color: #fff; border: none; border-radius: 6px; font-weight: 700; cursor: pointer; font-size: 0.8rem;">Reject</button>
                        </div>
                    `;
                        modalBody.appendChild(card);
                    });
                }
            } else {
                modalBody.innerHTML = "<p style='color: red;'>Error loading documents.</p>";
            }
        } catch (err) {
            modalBody.innerHTML = "<p style='color: red;'>Failed to fetch documents.</p>";
        }
    }

    async function updateDocStatus(docId, status) {
        if (!confirm("Are you sure you want to mark this document as " + status + "?")) return;

        const formData = new FormData();
        formData.append('document_id', docId);
        formData.append('status', status);

        try {
            const response = await fetch('api/manage_users.php?action=verify_document', {
                method: 'POST',
                body: formData
            });
            const data = await response.json();
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert(data.message);
            }
        } catch (err) {
            alert("Operation failed.");
        }
    }

    function closeModal() {
        document.getElementById('docModal').style.display = 'none';
    }

    // Close modal when clicking outside
    window.onclick = function (event) {
        const modal = document.getElementById('docModal');
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>

<?php include 'includes/footer.php'; ?>