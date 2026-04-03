<<<<<<< HEAD
<?php include 'includes/header.php'; ?>
<?php 
// Only allow access if temporary session is set
if (!isset($_SESSION['temp_user_id']) || !isset($_SESSION['verification_code'])) {
    header("Location: login.php");
    exit;
}
?>
<?php include 'includes/footer.php'; ?>
=======
<section class="auth-section">
    <div class="container auth-container">
        <div class="auth-card">
            <h2>Two-Step Verification</h2>
            <p>We've sent a 6-digit verification code to your email.</p>
            <div id="verifyAlert" class="alert" style="display: none;"></div>
            
            <form id="verifyForm">
                <div class="form-group">
                    <label for="code">Verification Code</label>
                    <input type="text" id="code" name="code" required placeholder="Enter 6-digit code" maxlength="6" pattern="\d{6}">
                </div>
                <button type="submit" class="btn btn-primary btn-block" id="verifyBtn">Verify Code <i class="fas fa-check-circle"></i></button>
            </form>
            <div class="auth-links">
                <p>Didn't receive the email? Check your spam folder or <a href="login.php">login again</a>.</p>
            </div>
            
            <?php
            // FOR DEVELOPMENT ONLY: Show the code on screen if XAMPP mail is not configured.
            // In a real production environment, the mail() function handles this, and you would remove this block.
            echo '<div style="margin-top: 1.5rem; padding: 1rem; background: #fff3cd; color: #856404; font-size: 0.85rem; border-radius: 4px;">';
            echo '<strong>Local Testing Note:</strong> Since XAMPP may not have an SMTP server configured to send real emails, your verification code is: <strong>' . $_SESSION['verification_code'] . '</strong>';
            echo '</div>';
            ?>
        </div>
    </div>
</section>
>>>>>>> origin/Suraj-K.Sah
