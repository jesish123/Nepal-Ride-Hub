<?php
// manage_reviews.php — Admin: Review Moderation Panel
include 'includes/header.php';
require_once 'includes/db_connect.php';
require_once 'includes/csrf.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: login.php');
    exit;
}

$csrfToken = generateCSRF();
$filterStatus = $_GET['status'] ?? 'pending';

// Stats
$counts = [];
foreach (['pending', 'approved', 'rejected'] as $s) {
    $c = $pdo->prepare("SELECT COUNT(*) FROM site_reviews WHERE status = ?");
    $c->execute([$s]);
    $counts[$s] = $c->fetchColumn();
}
$counts['all'] = array_sum($counts);
?>

<style>
    .admin-hdr {
        background: linear-gradient(135deg, #0a1628, #1a2f5e);
        padding: 3rem 0;
    }

    .admin-hdr h1 {
        font-family: 'Outfit', sans-serif;
        font-size: 2rem;
        font-weight: 800;
        color: #fff;
        margin: 0 0 0.4rem;
    }

    .admin-hdr p {
        color: #b0c4de;
    }

    .back-link {
        color: rgba(255, 255, 255, 0.6);
        font-size: 0.85rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        margin-bottom: 1rem;
    }

    .back-link:hover {
        color: #fff;
    }

    .stat-row {
        display: flex;
        gap: 1rem;
        margin: -2rem 0 2rem;
        flex-wrap: wrap;
    }

    .stat-c {
        flex: 1;
        min-width: 130px;
        background: #fff;
        border-radius: 12px;
        padding: 1.2rem 1.5rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        text-align: center;
        text-decoration: none;
        display: block;
        transition: 0.2s;
    }

    .stat-c:hover {
        transform: translateY(-2px);
    }

    .stat-c .n {
        font-family: 'Outfit', sans-serif;
        font-size: 1.8rem;
        font-weight: 800;
    }

    .stat-c .l {
        font-size: 0.78rem;
        color: #888;
        font-weight: 600;
        text-transform: uppercase;
    }

    .s-pend {
        border-top: 4px solid #ffc107;
    }

    .s-pend .n {
        color: #856404;
    }

    .s-appr {
        border-top: 4px solid #28a745;
    }

    .s-appr .n {
        color: #155724;
    }

    .s-reje {
        border-top: 4px solid #dc3545;
    }

    .s-reje .n {
        color: #721c24;
    }

    .s-all {
        border-top: 4px solid #3561ff;
    }

    .s-all .n {
        color: #3561ff;
    }

    .rev-card-admin {
        background: #fff;
        border-radius: 14px;
        padding: 1.5rem;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.07);
        margin-bottom: 1.2rem;
        display: flex;
        flex-direction: column;
        gap: 0.8rem;
    }

    .rca-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .rca-user {
        display: flex;
        gap: 0.7rem;
        align-items: center;
    }

    .rca-avatar {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: linear-gradient(135deg, #3561ff, #003893);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-weight: 700;
        font-size: 0.9rem;
    }

    .rca-name {
        font-weight: 700;
        font-size: 0.95rem;
        color: #111;
    }

    .rca-meta {
        font-size: 0.78rem;
        color: #888;
    }

    .rca-badge {
        display: flex;
        gap: 0.4rem;
        flex-wrap: wrap;
    }

    .badge {
        border-radius: 50px;
        padding: 0.2rem 0.7rem;
        font-size: 0.75rem;
        font-weight: 700;
    }

    .b-pending {
        background: #fff3cd;
        color: #856404;
    }

    .b-approved {
        background: #d4edda;
        color: #155724;
    }

    .b-rejected {
        background: #f8d7da;
        color: #721c24;
    }

    .rca-stars {
        display: flex;
        gap: 2px;
    }

    .rca-comment {
        font-size: 0.9rem;
        color: #555;
        line-height: 1.7;
        padding: 0.8rem;
        background: #f9fafb;
        border-radius: 8px;
    }

    .rca-actions {
        display: flex;
        gap: 0.6rem;
        flex-wrap: wrap;
    }

    .ra-btn {
        border: none;
        padding: 0.4rem 1rem;
        border-radius: 8px;
        font-size: 0.82rem;
        font-weight: 700;
        cursor: pointer;
        font-family: 'Inter', sans-serif;
        transition: 0.2s;
    }

    .ra-approve {
        background: #d4edda;
        color: #155724;
    }

    .ra-approve:hover {
        background: #28a745;
        color: #fff;
    }

    .ra-reject {
        background: #f8d7da;
        color: #721c24;
    }

    .ra-reject:hover {
        background: #dc3545;
        color: #fff;
    }

    .ra-delete {
        background: #e9ecef;
        color: #333;
    }

    .ra-delete:hover {
        background: #6c757d;
        color: #fff;
    }

    .ftab:hover,
    .ftab.on {
        background: #3561ff;
        border-color: #3561ff;
        color: #fff;
    }

    /* Admin Reply Styling */
    .rca-reply-section {
        margin-top: 1rem;
        border-top: 1px solid #eee;
        padding-top: 1rem;
    }

    .admin-reply-bubble {
        background: #eff6ff;
        border-left: 4px solid #3561ff;
        padding: 0.8rem 1rem;
        border-radius: 10px;
        font-size: 0.85rem;
        color: #1e40af;
        margin-bottom: 0.75rem;
    }

    .admin-reply-bubble b {
        display: block;
        margin-bottom: 0.2rem;
        color: #1e3a8a;
    }

    .reply-input-wrap {
        display: flex;
        gap: 0.5rem;
        margin-top: 0.5rem;
    }

    .reply-input-wrap textarea {
        flex: 1;
        padding: 0.6rem;
        border: 1.5px solid #e0e0e0;
        border-radius: 8px;
        font-size: 0.85rem;
        font-family: inherit;
        min-height: 60px;
    }

    .ra-btn-save {
        background: #3561ff;
        color: #fff;
        align-self: flex-end;
        border: none;
        padding: 0.4rem 1rem;
        border-radius: 8px;
        font-size: 0.82rem;
        font-weight: 700;
        cursor: pointer;
    }

    #globalMsg {
        display: none;
        margin-bottom: 1rem;
        border-radius: 8px;
    }
</style>

<div class="admin-hdr">
    <div class="container">
        <a href="admin_dashboard.php" class="back-link"><i class="fas fa-arrow-left"></i> Admin Dashboard</a>
        <h1><i class="fas fa-star" style="color:#f5a623;"></i> Review Moderation</h1>
        <p>Approve or reject user-submitted reviews before they go public.</p>
    </div>
</div>

<section style="background:#f0f4ff; padding:3rem 0 5rem;">
    <div class="container">
        <div class="stat-row">
            <a href="manage_reviews.php" class="stat-c s-all">
                <div class="n"><?php echo $counts['all']; ?></div>
                <div class="l">Total</div>
            </a>
            <a href="manage_reviews.php?status=pending" class="stat-c s-pend">
                <div class="n"><?php echo $counts['pending']; ?></div>
                <div class="l">Pending</div>
            </a>
            <a href="manage_reviews.php?status=approved" class="stat-c s-appr">
                <div class="n"><?php echo $counts['approved']; ?></div>
                <div class="l">Approved</div>
            </a>
            <a href="manage_reviews.php?status=rejected" class="stat-c s-reje">
                <div class="n"><?php echo $counts['rejected']; ?></div>
                <div class="l">Rejected</div>
            </a>
        </div>

        <div class="filter-bar-wrap">
            <a href="manage_reviews.php"
                class="ftab <?php echo !$filterStatus || $filterStatus === 'all' ? 'on' : ''; ?>">All</a>
            <a href="manage_reviews.php?status=pending"
                class="ftab <?php echo $filterStatus === 'pending' ? 'on' : ''; ?>">Pending</a>
            <a href="manage_reviews.php?status=approved"
                class="ftab <?php echo $filterStatus === 'approved' ? 'on' : ''; ?>">Approved</a>
            <a href="manage_reviews.php?status=rejected"
                class="ftab <?php echo $filterStatus === 'rejected' ? 'on' : ''; ?>">Rejected</a>
        </div>

        <div id="globalMsg" class="alert"></div>
        <div id="reviewsContainer">
            <p style="color:#aaa; text-align:center; padding:2rem;">Loading...</p>
        </div>
    </div>
</section>

<script>
    const CSRF = <?php echo json_encode($csrfToken); ?>;
    const STATUS = <?php echo json_encode($filterStatus); ?>;

    function stars(n) {
        return Array.from({ length: 5 }, (_, i) =>
            `<i class="fas fa-star" style="color:${i < n ? '#f5a623' : '#e0e0e0'}; font-size:0.85rem;"></i>`
        ).join('');
    }

    function esc(s) {
        const d = document.createElement('div');
        d.appendChild(document.createTextNode(s ?? ''));
        return d.innerHTML;
    }

    async function loadReviews() {
        const url = 'api/reviews.php?action=list' + (STATUS ? '&status=' + STATUS : '');
        const res = await fetch(url);
        const data = await res.json();
        const cont = document.getElementById('reviewsContainer');

        if (!data.success || data.reviews.length === 0) {
            cont.innerHTML = `<div style="text-align:center;padding:3rem;color:#aaa;">
            <i class="fas fa-comment-slash" style="font-size:2.5rem;margin-bottom:0.8rem;display:block;color:#ddd;"></i>
            No reviews found with status: <strong>${STATUS || 'all'}</strong>
        </div>`;
            return;
        }

        cont.innerHTML = data.reviews.map(r => `
        <div class="rev-card-admin" id="rev_${r.id}">
            <div class="rca-header">
                <div class="rca-user">
                    <div class="rca-avatar">${esc(r.reviewer_name?.charAt(0) || '?')}</div>
                    <div>
                        <div class="rca-name">${esc(r.reviewer_name)}</div>
                        <div class="rca-meta">${esc(r.email)} · ${new Date(r.created_at).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' })}</div>
                    </div>
                </div>
                <div class="rca-badge">
                    <div class="rca-stars">${stars(r.rating)}</div>
                    <span class="badge b-${r.status}">${r.status}</span>
                    <span class="badge" style="background:#e9ecef;color:#555;">${esc(r.service_type)}</span>
                </div>
            </div>
            <div class="rca-comment">${esc(r.comment)}</div>
            
            <div class="rca-reply-section">
                ${r.admin_reply ? `
                    <div class="admin-reply-bubble">
                        <b>Nepal Ride Hub Response:</b>
                        ${esc(r.admin_reply)}
                        <div style="font-size:0.7rem; color:#3b82f6; margin-top:0.4rem;">Replied on ${new Date(r.replied_at).toLocaleDateString()}</div>
                    </div>
                ` : ''}
                
                <div class="reply-input-wrap">
                    <textarea id="replyText_${r.id}" placeholder="${r.admin_reply ? 'Update your reply...' : 'Reply to this customer...'}"></textarea>
                    <button class="ra-btn ra-btn-save" onclick="saveReply(${r.id})">
                        <i class="fas fa-paper-plane"></i> ${r.admin_reply ? 'Update' : 'Reply'}
                    </button>
                </div>
            </div>

            <div class="rca-actions">
                ${r.status !== 'approved' ? `<button class="ra-btn ra-approve" onclick="moderate(${r.id},'approved')"><i class="fas fa-check"></i> Approve</button>` : ''}
                ${r.status !== 'rejected' ? `<button class="ra-btn ra-reject"  onclick="moderate(${r.id},'rejected')"><i class="fas fa-times"></i> Reject</button>` : ''}
                <button class="ra-btn ra-delete" onclick="deleteRev(${r.id})"><i class="fas fa-trash"></i> Delete</button>
            </div>
        </div>
    `).join('');
    }

    async function moderate(id, status) {
        const fd = new FormData();
        fd.append('csrf_token', CSRF);
        fd.append('review_id', id);
        fd.append('status', status);

        const res = await fetch('api/reviews.php?action=moderate', { method: 'POST', body: fd });
        const data = await res.json();
        showMsg(data.success, data.message);
        if (data.success) {
            const el = document.getElementById('rev_' + id);
            el.style.opacity = '0.4';
            setTimeout(() => { el.remove(); }, 600);
        }
    }

    async function deleteRev(id) {
        if (!confirm('Delete this review permanently?')) return;
        const fd = new FormData();
        fd.append('csrf_token', CSRF);
        fd.append('review_id', id);
        const res = await fetch('api/reviews.php?action=delete', { method: 'POST', body: fd });
        const data = await res.json();
        showMsg(data.success, data.message);
        if (data.success) document.getElementById('rev_' + id)?.remove();
    }

    async function saveReply(id) {
        const text = document.getElementById('replyText_' + id).value.trim();
        if (!text) return alert('Please enter a reply.');

        const fd = new FormData();
        fd.append('csrf_token', CSRF);
        fd.append('review_id', id);
        fd.append('reply_text', text);

        const res = await fetch('api/reviews.php?action=reply', { method: 'POST', body: fd });
        const data = await res.json();
        showMsg(data.success, data.message);
        if (data.success) {
            setTimeout(() => location.reload(), 800);
        }
    }

    function showMsg(ok, msg) {
        const el = document.getElementById('globalMsg');
        el.style.display = 'block';
        el.className = 'alert ' + (ok ? 'alert-success' : 'alert-danger');
        el.textContent = msg;
        setTimeout(() => el.style.display = 'none', 3000);
    }

    loadReviews();
</script>

<?php include 'includes/footer.php'; ?>