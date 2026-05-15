<?php
// reviews.php — VRS-60: Reviews & Ratings Page
include 'includes/header.php';
require_once 'includes/db_connect.php';
require_once 'includes/csrf.php';

$isLoggedIn = isset($_SESSION['user_id']);
$userId = $isLoggedIn ? (int) $_SESSION['user_id'] : 0;
$csrfToken = generateCSRF();

// Check if logged-in user has a completed booking (to allow review submission)
$canReview = false;
if ($isLoggedIn) {
    $chk = $pdo->prepare("SELECT id FROM bookings WHERE user_id = ? AND status = 'completed' LIMIT 1");
    $chk->execute([$userId]);
    $canReview = (bool) $chk->fetch();
}

// Fetch user's own reviews
$myReviews = [];
if ($isLoggedIn) {
    $myStmt = $pdo->prepare("SELECT * FROM site_reviews WHERE user_id = ? ORDER BY created_at DESC");
    $myStmt->execute([$userId]);
    $myReviews = $myStmt->fetchAll();
}

// Rating filter
$ratingFilter = intval($_GET['rating'] ?? 0);
$where = "WHERE sr.status = 'approved'";
$params = [];
if ($ratingFilter >= 1 && $ratingFilter <= 5) {
    $where .= " AND sr.rating = ?";
    $params[] = $ratingFilter;
}

// Fetch approved public reviews
$revStmt = $pdo->prepare("
    SELECT sr.*, u.name AS reviewer_name
    FROM site_reviews sr
    JOIN users u ON sr.user_id = u.id
    $where
    ORDER BY sr.created_at DESC
    LIMIT 50
");
$revStmt->execute($params);
$reviews = $revStmt->fetchAll();

// Average rating
$avgStmt = $pdo->query("SELECT AVG(rating) as avg, COUNT(*) as total FROM site_reviews WHERE status='approved'");
$avgData = $avgStmt->fetch();
$avgRating = $avgData['avg'] ? round($avgData['avg'], 1) : 0;
$totalReviews = $avgData['total'] ?? 0;

// Rating distribution
$distStmt = $pdo->query("SELECT rating, COUNT(*) as cnt FROM site_reviews WHERE status='approved' GROUP BY rating ORDER BY rating DESC");
$distribution = $distStmt->fetchAll();
$distMap = [];
foreach ($distribution as $d) {
    $distMap[$d['rating']] = $d['cnt'];
}
?>
<style>
    /* ── Reviews & Ratings Page ── */
    .reviews-hero {
        background: linear-gradient(135deg, #0a1628 0%, #1a2f5e 50%, #0d3b6b 100%);
        padding: 4.5rem 0;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .reviews-hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at 50% 0%, rgba(245, 166, 35, 0.15) 0%, transparent 60%);
    }

    .reviews-hero h1 {
        font-family: 'Outfit', sans-serif;
        font-size: 2.8rem;
        font-weight: 800;
        color: #fff;
        margin: 0 0 0.8rem;
    }

    .reviews-hero h1 span {
        color: #f5a623;
    }

    .reviews-hero p {
        color: #b0c4de;
        max-width: 540px;
        margin: 0 auto;
    }

    /* Summary bar */
    .reviews-summary {
        background: #fff;
        padding: 2.5rem;
        display: flex;
        align-items: center;
        gap: 3rem;
        flex-wrap: wrap;
        border-radius: 20px;
        margin: -2rem 0 2.5rem;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
        position: relative;
        z-index: 10;
    }

    .avg-score {
        text-align: center;
    }

    .avg-big {
        font-family: 'Outfit', sans-serif;
        font-size: 4rem;
        font-weight: 800;
        color: #111;
        line-height: 1;
    }

    .avg-stars {
        display: flex;
        gap: 3px;
        justify-content: center;
        margin: 0.3rem 0;
    }

    .avg-stars i {
        font-size: 1.1rem;
    }

    .avg-total {
        font-size: 0.8rem;
        color: #888;
    }

    .star-filled {
        color: #f5a623;
    }

    .star-empty {
        color: #e0e0e0;
    }

    .rating-dist {
        flex: 1;
        min-width: 220px;
    }

    .dist-row {
        display: flex;
        align-items: center;
        gap: 0.8rem;
        margin-bottom: 0.4rem;
    }

    .dist-label {
        font-size: 0.8rem;
        font-weight: 700;
        color: #555;
        width: 40px;
        text-align: right;
    }

    .dist-bar-bg {
        flex: 1;
        background: #f0f0f0;
        border-radius: 50px;
        height: 8px;
        overflow: hidden;
    }

    .dist-bar-fill {
        height: 100%;
        border-radius: 50px;
        background: linear-gradient(90deg, #f5a623, #ff9500);
        transition: width 0.8s ease;
    }

    .dist-count {
        font-size: 0.75rem;
        color: #888;
        width: 24px;
    }

    .reviews-write-cta {
        margin-left: auto;
        background: linear-gradient(135deg, #3561ff, #264ac9);
        color: #fff;
        border: none;
        border-radius: 12px;
        padding: 1rem 2rem;
        font-weight: 700;
        font-size: 0.95rem;
        cursor: pointer;
        font-family: 'Inter', sans-serif;
        display: flex;
        align-items: center;
        gap: 0.6rem;
        transition: 0.2s;
        white-space: nowrap;
    }

    .reviews-write-cta:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(53, 97, 255, 0.35);
    }

    /* Layout */
    .reviews-layout {
        display: grid;
        grid-template-columns: 1fr 340px;
        gap: 2rem;
        padding-bottom: 5rem;
    }

    /* Review cards */
    .review-filter-bar {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
    }

    .rev-filter-btn {
        padding: 0.4rem 1rem;
        border-radius: 50px;
        border: 1.5px solid #e0e0e0;
        font-size: 0.8rem;
        font-weight: 700;
        color: #666;
        cursor: pointer;
        background: #fff;
        text-decoration: none;
        transition: 0.2s;
    }

    .rev-filter-btn:hover,
    .rev-filter-btn.active {
        background: #f5a623;
        border-color: #f5a623;
        color: #fff;
    }

    .review-card {
        background: #fff;
        border-radius: 14px;
        padding: 1.5rem;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.06);
        margin-bottom: 1.2rem;
        transition: 0.2s;
    }

    .review-card:hover {
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }

    .review-card-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        margin-bottom: 0.8rem;
    }

    .reviewer-info {
        display: flex;
        align-items: center;
        gap: 0.8rem;
    }

    .reviewer-avatar {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        background: linear-gradient(135deg, #3561ff, #003893);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        color: #fff;
        font-weight: 700;
        flex-shrink: 0;
    }

    .reviewer-name {
        font-weight: 700;
        color: #111;
        font-size: 0.95rem;
    }

    .reviewer-date {
        font-size: 0.78rem;
        color: #aaa;
    }

    .review-stars-display {
        display: flex;
        gap: 2px;
        margin-top: 2px;
    }

    .review-comment {
        font-size: 0.9rem;
        color: #555;
        line-height: 1.7;
        margin-bottom: 0.8rem;
    }

    .review-tags {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .review-tag {
        background: #eff6ff;
        color: #3561ff;
        border-radius: 50px;
        padding: 0.2rem 0.7rem;
        font-size: 0.75rem;
        font-weight: 600;
    }

    /* Own review card */
    .my-review-card {
        background: linear-gradient(135deg, #eff6ff, #dbeafe);
        border: 1.5px solid #bfdbfe;
        border-radius: 14px;
        padding: 1.5rem;
        margin-bottom: 1.2rem;
    }

    .my-review-actions {
        display: flex;
        gap: 0.5rem;
        margin-top: 0.8rem;
    }

    .btn-edit-review,
    .btn-delete-review {
        border: none;
        border-radius: 6px;
        padding: 0.35rem 0.9rem;
        font-size: 0.8rem;
        font-weight: 700;
        cursor: pointer;
        font-family: 'Inter', sans-serif;
        transition: 0.2s;
    }

    .btn-edit-review {
        background: #e9ecef;
        color: #333;
    }

    .btn-edit-review:hover {
        background: #3561ff;
        color: #fff;
    }

    .btn-delete-review {
        background: #f8d7da;
        color: #721c24;
    }

    .btn-delete-review:hover {
        background: #dc3545;
        color: #fff;
    }

    .pending-pill {
        background: #fff3cd;
        color: #856404;
        padding: 0.2rem 0.7rem;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 700;
    }

    /* Admin Response Box */
    .admin-response-wrap {
        margin-top: 1rem;
        background: #f8fafc;
        border-left: 4px solid #3561ff;
        padding: 1.2rem;
        border-radius: 12px;
        position: relative;
    }
    .admin-response-wrap::before {
        content: '\f3e5'; /* FontAwesome reply icon */
        font-family: 'Font Awesome 6 Free';
        font-weight: 900;
        position: absolute;
        top: -10px;
        left: 20px;
        background: #3561ff;
        color: #fff;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        font-size: 0.7rem;
    }
    .admin-response-title {
        font-weight: 800;
        color: #1e3a8a;
        font-size: 0.85rem;
        margin-bottom: 0.4rem;
        display: block;
    }
    .admin-response-text {
        color: #475569;
        font-size: 0.88rem;
        line-height: 1.6;
    }
    .admin-response-date {
        display: block;
        margin-top: 0.5rem;
        font-size: 0.75rem;
        color: #94a3b8;
    }

    /* Sidebar – submit form */
    .submit-sidebar {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .submit-card {
        background: #fff;
        border-radius: 16px;
        padding: 2rem;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
        align-self: start;
        position: sticky;
        top: 100px;
    }

    .submit-card h3 {
        font-family: 'Outfit', sans-serif;
        color: #111;
        margin: 0 0 1.2rem;
    }

    /* Star picker */
    .star-picker {
        display: flex;
        gap: 6px;
        margin-bottom: 1.2rem;
    }

    .star-picker i {
        font-size: 1.8rem;
        cursor: pointer;
        color: #e0e0e0;
        transition: 0.15s;
    }

    .star-picker i.selected,
    .star-picker i.hovered {
        color: #f5a623;
        transform: scale(1.15);
    }

    .rev-form .fld {
        margin-bottom: 1rem;
    }

    .rev-form .fld label {
        font-size: 0.75rem;
        font-weight: 700;
        color: #555;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: block;
        margin-bottom: 0.35rem;
    }

    .rev-form .fld select,
    .rev-form .fld textarea {
        width: 100%;
        padding: 0.7rem 1rem;
        border: 1.5px solid #eee;
        border-radius: 8px;
        font-family: 'Inter', sans-serif;
        font-size: 0.9rem;
        background: #fcfcfc;
        transition: 0.2s;
        box-sizing: border-box;
    }

    .rev-form .fld textarea {
        min-height: 110px;
        resize: vertical;
    }

    .rev-form .fld select:focus,
    .rev-form .fld textarea:focus {
        outline: none;
        border-color: #3561ff;
    }

    .btn-submit-review {
        width: 100%;
        background: linear-gradient(135deg, #3561ff, #264ac9);
        color: #fff;
        border: none;
        border-radius: 10px;
        padding: 0.85rem;
        font-size: 0.95rem;
        font-weight: 700;
        cursor: pointer;
        font-family: 'Inter', sans-serif;
        transition: 0.3s;
        margin-top: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .btn-submit-review:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(53, 97, 255, 0.35);
    }

    .btn-submit-review:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    #reviewAlert {
        display: none;
        margin-top: 0.8rem;
        border-radius: 8px;
        font-size: 0.85rem;
    }

    .no-reviews-msg {
        text-align: center;
        padding: 3rem 1rem;
        color: #aaa;
    }

    .no-reviews-msg i {
        font-size: 3rem;
        color: #ddd;
        margin-bottom: 1rem;
        display: block;
    }

    @media(max-width:900px) {
        .reviews-layout {
            grid-template-columns: 1fr;
        }

        .submit-card {
            position: static;
        }

        .reviews-summary {
            flex-direction: column;
            gap: 1.5rem;
        }

        .reviews-write-cta {
            margin-left: 0;
        }

        .reviews-hero h1 {
            font-size: 2rem;
        }
    }
</style>

<!-- Hero -->
<section class="reviews-hero">
    <div class="container">
        <h1>Customer <span>Reviews</span></h1>
        <p>Real experiences from real travellers. Read what customers have to say about Nepal Ride Hub.</p>
    </div>
</section>

<!-- Main -->
<section style="background:#f0f4ff; padding:3rem 0 2rem;">
    <div class="container">

        <!-- Summary bar -->
        <div class="reviews-summary">
            <div class="avg-score">
                <div class="avg-big"><?php echo $avgRating ?: '—'; ?></div>
                <div class="avg-stars">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <i class="fas fa-star <?php echo $i <= round($avgRating) ? 'star-filled' : 'star-empty'; ?>"></i>
                    <?php endfor; ?>
                </div>
                <div class="avg-total"><?php echo $totalReviews; ?> review<?php echo $totalReviews != 1 ? 's' : ''; ?>
                </div>
            </div>

            <div class="rating-dist">
                <?php for ($s = 5; $s >= 1; $s--): ?>
                    <?php $cnt = $distMap[$s] ?? 0;
                    $pct = $totalReviews > 0 ? round($cnt / $totalReviews * 100) : 0; ?>
                    <div class="dist-row">
                        <span class="dist-label"><?php echo $s; ?> <i class="fas fa-star star-filled"
                                style="font-size:0.7rem;"></i></span>
                        <div class="dist-bar-bg">
                            <div class="dist-bar-fill" style="width:<?php echo $pct; ?>%;"></div>
                        </div>
                        <span class="dist-count"><?php echo $cnt; ?></span>
                    </div>
                <?php endfor; ?>
            </div>

            <?php if ($isLoggedIn && $canReview): ?>
                <button class="reviews-write-cta"
                    onclick="document.getElementById('submitCard').scrollIntoView({behavior:'smooth'})">
                    <i class="fas fa-pen"></i> Write a Review
                </button>
            <?php endif; ?>
        </div>

        <div class="reviews-layout">
            <!-- Left: Reviews list -->
            <div>
                <!-- My reviews -->
                <?php if (!empty($myReviews)): ?>
                    <h3 style="font-family:'Outfit',sans-serif; color:#111; margin-bottom:1rem;">Your Reviews</h3>
                    <?php foreach ($myReviews as $mr): ?>
                        <div class="my-review-card" id="myReview_<?php echo $mr['id']; ?>">
                            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.5rem;">
                                <div style="display:flex; gap:3px;">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="fas fa-star"
                                            style="color:<?php echo $i <= $mr['rating'] ? '#f5a623' : '#e0e0e0'; ?>;"></i>
                                    <?php endfor; ?>
                                </div>
                                <span class="<?php echo $mr['status'] === 'approved' ? '' : 'pending-pill'; ?>"
                                    style="<?php echo $mr['status'] === 'approved' ? 'color:#155724;font-weight:700;font-size:0.8rem;' : '' ?>">
                                    <?php echo $mr['status'] === 'approved' ? '<i class="fas fa-check-circle"></i> Published' : 'Pending Approval'; ?>
                                </span>
                            </div>
                            <p class="review-comment"><?php echo htmlspecialchars($mr['comment'], ENT_QUOTES, 'UTF-8'); ?></p>
                            
                            <?php if ($mr['admin_reply']): ?>
                                <div class="admin-response-wrap">
                                    <span class="admin-response-title">Nepal Ride Hub Response:</span>
                                    <p class="admin-response-text"><?php echo htmlspecialchars($mr['admin_reply'], ENT_QUOTES, 'UTF-8'); ?></p>
                                    <span class="admin-response-date">Replied on <?php echo date('M d, Y', strtotime($mr['replied_at'])); ?></span>
                                </div>
                            <?php endif; ?>

                            <small style="color:#aaa;"><?php echo date('M d, Y', strtotime($mr['created_at'])); ?></small>
                            <div class="my-review-actions">
                                <button class="btn-edit-review"
                                    onclick='openEditModal(<?php echo $mr['id']; ?>, <?php echo $mr['rating']; ?>, <?php echo htmlspecialchars(json_encode($mr['comment']), ENT_QUOTES, "UTF-8"); ?>)'><i
                                        class="fas fa-pen"></i> Edit</button>
                                <button class="btn-delete-review" onclick="deleteReview(<?php echo $mr['id']; ?>)"><i
                                        class="fas fa-trash"></i> Delete</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <hr style="margin:2rem 0; border:none; border-top:2px solid #e8eeff;">
                <?php endif; ?>

                <!-- Public reviews -->
                <div class="review-filter-bar">
                    <a href="reviews.php" class="rev-filter-btn <?php echo !$ratingFilter ? 'active' : ''; ?>">All</a>
                    <?php for ($s = 5; $s >= 1; $s--): ?>
                        <a href="reviews.php?rating=<?php echo $s; ?>"
                            class="rev-filter-btn <?php echo $ratingFilter === $s ? 'active' : ''; ?>">
                            <?php echo $s; ?> <i class="fas fa-star" style="color:#f5a623; font-size:0.75rem;"></i>
                        </a>
                    <?php endfor; ?>
                </div>

                <?php if (empty($reviews)): ?>
                    <div class="no-reviews-msg">
                        <i class="fas fa-comment-slash"></i>
                        <h3>No reviews yet</h3>
                        <p>Be the first to share your experience!</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($reviews as $rev): ?>
                        <div class="review-card">
                            <div class="review-card-header">
                                <div class="reviewer-info">
                                    <div class="reviewer-avatar"><?php echo strtoupper(substr($rev['reviewer_name'], 0, 1)); ?>
                                    </div>
                                    <div>
                                        <div class="reviewer-name">
                                            <?php echo htmlspecialchars($rev['reviewer_name'], ENT_QUOTES, 'UTF-8'); ?></div>
                                        <div class="reviewer-date"><?php echo date('M d, Y', strtotime($rev['created_at'])); ?>
                                        </div>
                                        <div class="review-stars-display">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i class="fas fa-star"
                                                    style="color:<?php echo $i <= $rev['rating'] ? '#f5a623' : '#e0e0e0'; ?>; font-size:0.85rem;"></i>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <p class="review-comment"><?php echo htmlspecialchars($rev['comment'], ENT_QUOTES, 'UTF-8'); ?></p>
                            
                            <?php if ($rev['admin_reply']): ?>
                                <div class="admin-response-wrap">
                                    <span class="admin-response-title">Response from Nepal Ride Hub:</span>
                                    <p class="admin-response-text"><?php echo htmlspecialchars($rev['admin_reply'], ENT_QUOTES, 'UTF-8'); ?></p>
                                    <span class="admin-response-date">Replied on <?php echo date('M d, Y', strtotime($rev['replied_at'])); ?></span>
                                </div>
                            <?php endif; ?>
                            <?php if ($rev['service_type'] && $rev['service_type'] !== 'general'): ?>
                                <div class="review-tags"><span class="review-tag"><i class="fas fa-tag"></i>
                                        <?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $rev['service_type'])), ENT_QUOTES, 'UTF-8'); ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Right: Submit form -->
            <div class="submit-sidebar" id="submitCard">
                <?php if ($isLoggedIn && $canReview): ?>
                    <div class="submit-card">
                        <h3><i class="fas fa-pen-to-square" style="color:#3561ff;"></i> Write a Review</h3>
                        <form class="rev-form" id="reviewForm">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
                            <input type="hidden" name="rating" id="ratingInput" value="0">
                            <div class="fld">
                                <label>Your Rating <span style="color:#dc3545;">*</span></label>
                                <div class="star-picker" id="starPicker">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="fas fa-star" data-val="<?php echo $i; ?>"></i>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            <div class="fld">
                                <label>Service Type</label>
                                <select name="service_type">
                                    <option value="general">General Experience</option>
                                    <option value="vehicle">Vehicle Quality</option>
                                    <option value="customer_support">Customer Support</option>
                                </select>
                            </div>
                            <div class="fld">
                                <label>Your Review <span style="color:#dc3545;">*</span></label>
                                <textarea name="comment" id="reviewComment" placeholder="Share your experience in detail..."
                                    required minlength="20" maxlength="1000"></textarea>
                                <small style="color:#aaa; font-size:0.75rem;" id="charCount">0 / 1000 characters</small>
                            </div>
                            <button type="submit" class="btn-submit-review" id="submitReviewBtn">
                                <i class="fas fa-paper-plane"></i> Submit Review
                            </button>
                            <div id="reviewAlert" class="alert"></div>
                        </form>
                    </div>

                <?php elseif ($isLoggedIn && ($_SESSION['role'] ?? '') === 'customer' && !$canReview): ?>
                    <div class="submit-card" style="text-align:center;">
                        <i class="fas fa-lock" style="font-size:2.5rem; color:#aaa; margin-bottom:1rem;"></i>
                        <h3>Complete a Booking First</h3>
                        <p style="color:#888; font-size:0.9rem;">You can only submit a review after completing a rental booking.</p>
                        <a href="vehicles.php" class="btn-submit-review" style="text-decoration:none; margin-top:1rem;">
                            <i class="fas fa-car"></i> Browse Vehicles
                        </a>
                    </div>

                <?php elseif ($isLoggedIn && ($_SESSION['role'] ?? '') === 'admin'): ?>
                    <div class="submit-card" style="text-align:center;">
                        <i class="fas fa-user-shield" style="font-size:2.5rem; color:#3561ff; margin-bottom:1rem;"></i>
                        <h3>Admin Access</h3>
                        <p style="color:#888; font-size:0.9rem;">You are currently signed in as an administrator. To moderate reviews or reply to customers, please visit the Moderation Panel.</p>
                        <a href="manage_reviews.php" class="btn-submit-review" style="text-decoration:none; margin-top:1rem;">
                            <i class="fas fa-tasks"></i> Go to Moderation
                        </a>
                    </div>
                <?php else: ?>
                    <div class="submit-card" style="text-align:center;">
                        <i class="fas fa-user-lock" style="font-size:2.5rem; color:#aaa; margin-bottom:1rem;"></i>
                        <h3>Login to Review</h3>
                        <p style="color:#888; font-size:0.9rem;">Sign in to share your experience with Nepal Ride Hub.</p>
                        <a href="login.php" class="btn-submit-review" style="text-decoration:none; margin-top:1rem;">
                            <i class="fas fa-sign-in-alt"></i> Login Now
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Edit Modal -->
<div class="modal-backdrop" id="editModal"
    style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:500; align-items:center; justify-content:center;">
    <div style="background:#fff; border-radius:16px; padding:2rem; max-width:480px; width:90%;">
        <h3 style="font-family:'Outfit',sans-serif; margin:0 0 1.2rem;">Edit Review</h3>
        <input type="hidden" id="editReviewId">
        <input type="hidden" id="editRatingInput" value="0">
        <div class="star-picker" id="editStarPicker" style="margin-bottom:1rem;">
            <?php for ($i = 1; $i <= 5; $i++): ?>
                <i class="fas fa-star" data-val="<?php echo $i; ?>"></i>
            <?php endfor; ?>
        </div>
        <textarea id="editCommentInput"
            style="width:100%;padding:0.7rem;border:1.5px solid #eee;border-radius:8px;font-family:'Inter',sans-serif;min-height:100px;margin-bottom:1rem;box-sizing:border-box;"></textarea>
        <div id="editAlert" class="alert" style="display:none; margin-bottom:0.8rem;"></div>
        <div style="display:flex; gap:0.8rem;">
            <button onclick="submitEdit()"
                style="flex:1; padding:0.7rem; background:#3561ff; color:#fff; border:none; border-radius:8px; font-weight:700; cursor:pointer; font-family:'Inter',sans-serif;">Save
                Changes</button>
            <button onclick="closeEditModal()"
                style="flex:1; padding:0.7rem; background:#e9ecef; color:#333; border:none; border-radius:8px; font-weight:700; cursor:pointer; font-family:'Inter',sans-serif;">Cancel</button>
        </div>
    </div>
</div>

<script>
    const CSRF_TOKEN = <?php echo json_encode($csrfToken); ?>;

    // ── Star Picker ──────────────────────────────────────────────────────────
    function initStarPicker(pickerEl, inputEl) {
        const stars = pickerEl.querySelectorAll('i');
        stars.forEach(star => {
            star.addEventListener('mouseover', () => {
                stars.forEach(s => s.classList.toggle('hovered', parseInt(s.dataset.val) <= parseInt(star.dataset.val)));
            });
            star.addEventListener('mouseout', () => {
                stars.forEach(s => s.classList.remove('hovered'));
            });
            star.addEventListener('click', () => {
                const val = star.dataset.val;
                inputEl.value = val;
                stars.forEach(s => s.classList.toggle('selected', parseInt(s.dataset.val) <= parseInt(val)));
            });
        });
    }

    initStarPicker(document.getElementById('starPicker'), document.getElementById('ratingInput'));
    initStarPicker(document.getElementById('editStarPicker'), document.getElementById('editRatingInput'));

    // Char count
    document.getElementById('reviewComment')?.addEventListener('input', function () {
        document.getElementById('charCount').textContent = this.value.length + ' / 1000 characters';
    });

    // ── Submit Review ────────────────────────────────────────────────────────
    document.getElementById('reviewForm')?.addEventListener('submit', async (e) => {
        e.preventDefault();
        const alertEl = document.getElementById('reviewAlert');
        const btn = document.getElementById('submitReviewBtn');

        if (parseInt(document.getElementById('ratingInput').value) === 0) {
            alertEl.style.display = 'block';
            alertEl.className = 'alert alert-danger';
            alertEl.textContent = 'Please select a star rating.';
            return;
        }

        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';

        try {
            const res = await fetch('api/reviews.php?action=submit', { method: 'POST', body: new FormData(e.target) });
            const data = await res.json();
            alertEl.style.display = 'block';
            alertEl.className = 'alert ' + (data.success ? 'alert-success' : 'alert-danger');
            alertEl.textContent = data.message;
            if (data.success) setTimeout(() => location.reload(), 2000);
            else { btn.disabled = false; btn.innerHTML = '<i class="fas fa-paper-plane"></i> Submit Review'; }
        } catch (err) {
            alertEl.style.display = 'block';
            alertEl.className = 'alert alert-danger';
            alertEl.textContent = 'Submission Failed. Please check your connection or contact support.';
            console.error("Submission Error:", err);
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-paper-plane"></i> Submit Review';
        }
    });

    // ── Delete Review ────────────────────────────────────────────────────────
    async function deleteReview(id) {
        if (!confirm('Are you sure you want to delete this review?')) return;
        const fd = new FormData();
        fd.append('csrf_token', CSRF_TOKEN);
        fd.append('review_id', id);
        const res = await fetch('api/reviews.php?action=delete', { method: 'POST', body: fd });
        const data = await res.json();
        if (data.success) {
            document.getElementById('myReview_' + id)?.remove();
            alert('Review deleted.');
        } else {
            alert(data.message);
        }
    }

    // ── Edit Modal ───────────────────────────────────────────────────────────
    function openEditModal(id, rating, comment) {
        document.getElementById('editReviewId').value = id;
        document.getElementById('editCommentInput').value = comment;
        document.getElementById('editRatingInput').value = rating;
        document.getElementById('editModal').style.display = 'flex';

        // Sync stars
        const stars = document.querySelectorAll('#editStarPicker i');
        stars.forEach(s => s.classList.toggle('selected', parseInt(s.dataset.val) <= rating));
    }

    function closeEditModal() {
        document.getElementById('editModal').style.display = 'none';
    }

    async function submitEdit() {
        const alertEl = document.getElementById('editAlert');
        const fd = new FormData();
        fd.append('csrf_token', CSRF_TOKEN);
        fd.append('review_id', document.getElementById('editReviewId').value);
        fd.append('rating', document.getElementById('editRatingInput').value);
        fd.append('comment', document.getElementById('editCommentInput').value);

        const res = await fetch('api/reviews.php?action=edit', { method: 'POST', body: fd });
        const data = await res.json();
        alertEl.style.display = 'block';
        alertEl.className = 'alert ' + (data.success ? 'alert-success' : 'alert-danger');
        alertEl.textContent = data.message;
        if (data.success) setTimeout(() => { closeEditModal(); location.reload(); }, 1500);
    }

    document.getElementById('editModal')?.addEventListener('click', (e) => {
        if (e.target === e.currentTarget) closeEditModal();
    });
</script>
<?php include 'includes/footer.php'; ?>
