<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'includes/header.php';
?>
<style>
    /* Hide the default navbar and footer */
    .top-bar,
    .navbar-redesigned,
    .footer {
        display: none !important;
    }

    body {
        margin: 0;
        font-family: 'Inter', sans-serif;
        min-height: 100vh;
        background: url('https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&w=1920&q=80') no-repeat center center/cover;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    body::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1;
    }

    .auth-premium-card {
        position: relative;
        z-index: 2;
        background: rgba(255, 255, 255, 0.98);
        backdrop-filter: blur(20px);
        width: 100%;
        max-width: 440px;
        border-radius: 20px;
        padding: 3rem;
        box-shadow: 0 25px 60px rgba(0, 0, 0, 0.5);
    }

    .auth-premium-card h2 {
        font-family: 'Outfit', sans-serif;
        font-size: 2rem;
        font-weight: 800;
        color: #111;
        text-align: center;
        margin-bottom: 1.5rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-group label {
        display: block;
        font-weight: 700;
        color: #333;
        margin-bottom: 0.6rem;
        font-size: 0.85rem;
        text-transform: uppercase;
    }

    .input-with-icon {
        position: relative;
        display: flex;
        align-items: center;
    }

    .input-with-icon i {
        position: absolute;
        left: 1.2rem;
        color: #888;
    }

    .input-with-icon input {
        width: 100%;
        padding: 1rem 1rem 1rem 3.2rem;
        border: 1.5px solid #eee;
        border-radius: 12px;
        font-size: 1rem;
    }

    .btn-auth-submit {
        background: #3561ff;
        color: #fff;
        border: none;
        border-radius: 12px;
        padding: 1rem;
        width: 100%;
        font-size: 1.1rem;
        font-weight: 700;
        cursor: pointer;
        margin-top: 1rem;
    }
</style>

<div class="auth-premium-card">
    <h2>Reset Password</h2>
    <p style="text-align: center; color: #666; margin-bottom: 2rem; font-size: 0.9rem;">Enter your email or username to
        receive a reset code.</p>

    <div id="authAlert" class="alert" style="display: none; font-size: 0.85rem; margin-bottom: 1rem; padding: 0.8rem;">
    </div>

    <form id="forgotForm" class="auth-form">
        <div class="form-group">
            <label>Username / Email</label>
            <div class="input-with-icon">
                <i class="fa-regular fa-envelope"></i>
                <input type="text" id="email" name="email" placeholder="Enter username or email" required>
            </div>
        </div>
        <button type="submit" class="btn-auth-submit">Send Reset Code</button>
    </form>

    <div style="text-align: center; margin-top: 1.5rem;">
        <a href="login.php" style="color: #666; text-decoration: none; font-weight: 600; font-size: 0.9rem;">Back to
            Login</a>
    </div>
</div>

<script>
    document.getElementById('forgotForm').addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        fetch('api/auth.php?action=forgot_password', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                const authAlert = document.getElementById('authAlert');
                authAlert.style.display = 'block';
                if (data.success) {
                    authAlert.className = 'alert alert-success';
                    authAlert.textContent = data.message;
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 2000);
                } else {
                    authAlert.className = 'alert alert-danger';
                    authAlert.textContent = data.message;
                }
            });
    });
</script>
</body>

</html>