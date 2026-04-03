<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'includes/header.php'; 
?>
<style>
/* Hide the default navbar and footer */
.top-bar, .navbar-redesigned, .footer { display: none !important; }

body {
    margin: 0;
    font-family: 'Inter', sans-serif;
    height: 100vh;
    overflow: hidden;
    background: url('uploads/premium_bg.png') no-repeat center center/cover;
    position: relative;
}

/* Dark overlay for contrast if needed */
body::before {
    content: '';
    position: absolute;
    top:0; left:0; width:100%; height:100%;
    background: linear-gradient(90deg, rgba(0,0,0,0.5) 0%, rgba(0,0,0,0.1) 50%, rgba(0,0,0,0.4) 100%);
    z-index: 1;
}

/* Floating overlay layout */
.auth-premium-wrapper {
    position: relative;
    z-index: 2;
    display: flex;
    justify-content: space-between;
    align-items: center;
    min-height: 100vh;
    padding: 0 8%;
}

/* Left Content */
.auth-premium-left {
    flex: 1;
    color: #fff;
    max-width: 500px;
}
.auth-top-logo {
    position: absolute;
    top: 30px;
    left: 5%;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    text-decoration: none;
    z-index: 10;
}
.auth-top-logo .icon {
    width: 40px;
    height: 40px;
    background-color: #3561ff;
    color: #fff;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
}
.auth-top-logo .titles { display: flex; flex-direction: column; }
.auth-top-logo .titles .main { font-weight: 700; color: #fff; font-size: 1.1rem; line-height: 1.1; font-family: 'Inter', sans-serif; }
.auth-top-logo .titles .sub { color: #ccc; font-size: 0.6rem; font-weight: 600; letter-spacing: 1.5px; }

.auth-premium-left h1 {
    font-size: 3.8rem;
    font-family: 'Outfit', sans-serif;
    font-weight: 800;
    line-height: 1.1;
    margin: 0;
    color: #ffffff;
    text-shadow: 0 4px 15px rgba(0,0,0,0.4);
}

/* Right Card */
.auth-premium-card {
    background: rgba(255, 255, 255, 0.98);
    backdrop-filter: blur(20px);
    width: 100%;
    max-width: 380px;
    border-radius: 16px;
    padding: 1.8rem;
    box-shadow: 0 15px 40px rgba(0,0,0,0.4);
    display: flex;
    flex-direction: column;
    margin-top: 1rem;
}

/* Card Logo */
.card-logo {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    margin-bottom: 1.2rem;
}
.card-logo .icon {
    width: 28px;
    height: 28px;
    background-color: #3561ff;
    color: #fff;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.85rem;
}
.card-logo .titles { display: flex; flex-direction: column; }
.card-logo .titles .main { font-weight: 800; color: #111; font-size: 0.9rem; line-height: 1.1; font-family: 'Inter', sans-serif; }
.card-logo .titles .sub { color: #777; font-size: 0.5rem; font-weight: 600; letter-spacing: 1px; }

.auth-premium-card h2 {
    font-family: 'Outfit', sans-serif;
    font-size: 1.6rem;
    font-weight: 800;
    color: #111;
    text-align: center;
    margin-bottom: 1rem;
    margin-top: 0;
}

.auth-form .form-group { margin-bottom: 1rem; }
.auth-form label {
    display: block;
    font-weight: 700;
    color: #333;
    margin-bottom: 0.4rem;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.input-with-icon {
    position: relative;
    display: flex;
    align-items: center;
}
.input-with-icon > i:first-child {
    position: absolute;
    left: 0.8rem;
    color: #888;
    font-size: 0.9rem;
}
.toggle-password {
    position: absolute;
    right: 0.8rem;
    color: #888;
    font-size: 0.9rem;
    cursor: pointer;
    z-index: 10;
    pointer-events: auto;
}
.toggle-password:hover {
    color: #3561ff;
}
.input-with-icon input {
    width: 100%;
    padding: 0.7rem 1rem 0.7rem 2.4rem;
    border: 1.5px solid #eee;
    border-radius: 8px;
    font-size: 0.85rem;
    font-family: 'Inter', sans-serif;
    background: #fcfcfc;
    transition: all 0.3s ease;
}
.input-with-icon input:focus { 
    outline: none; 
    border-color: #3561ff;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(53,97,255,0.1); 
}

.btn-auth-submit {
    background-color: #3561ff;
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: 0.8rem;
    width: 100%;
    margin: 1rem 0;
    display: block;
    font-size: 0.95rem;
    font-weight: 700;
    cursor: pointer;
    font-family: 'Inter', sans-serif;
    transition: transform 0.2s, background-color 0.2s;
}
.btn-auth-submit:hover { 
    background-color: #264ac9;
    transform: translateY(-2px);
}
.btn-auth-submit:active { transform: translateY(0); }

.auth-links-group {
    text-align: center;
    font-size: 0.85rem;
    color: #666;
}
.auth-links-group a {
    color: #3561ff;
    text-decoration: none;
    font-weight: 700;
}
.auth-links-group a:hover { text-decoration: underline; }

@media (max-width: 900px) {
    body { height: auto; overflow: auto; min-width: 100vh; }
    .auth-premium-wrapper {
        flex-direction: column;
        justify-content: center;
        padding: 6rem 2rem 2rem;
        gap: 3rem;
    }
    .auth-premium-left h1 { margin-bottom: 0; font-size: 3rem; text-align: center; }
    .auth-top-logo { 
        top: 20px;
        left: 50%;
        transform: translateX(-50%);
    }
}
</style>

<div class="auth-premium-wrapper">
    <div class="auth-premium-left">
        <a href="index.php" class="auth-top-logo">
            <div class="icon"><i class="fa-solid fa-car-side"></i></div>
            <div class="titles">
                <span class="main">Nepal Ride Hub</span>
                <span class="sub" style="color:#e0e0e0;">PREMIUM MOBILITY</span>
            </div>
        </a>
        <h1>Premium rides<br>across Nepal</h1>
    </div>

    <div class="auth-premium-card">
        <div class="card-logo">
            <div class="icon"><i class="fa-solid fa-car-side"></i></div>
            <div class="titles">
                <span class="main">Nepal Ride Hub</span>
                <span class="sub">PREMIUM MOBILITY</span>
            </div>
        </div>
        
        <h2>Sign up</h2>
        <div id="registerAlert" class="alert" style="display: none; font-size: 0.85rem; margin-bottom: 1rem; padding: 0.8rem;"></div>
        
        <form id="registerForm" class="auth-form">
            <!-- Hidden field to satisfy validation transparently -->
            <input type="hidden" name="phone" id="phone" value="0000000000">
            
            <div class="form-group">
                <label for="name">Username:</label>
                <div class="input-with-icon">
                    <i class="fa-regular fa-user"></i>
                    <input type="text" id="name" name="name" placeholder="Username" required>
                </div>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <div class="input-with-icon">
                    <i class="fa-regular fa-envelope"></i>
                    <input type="email" id="email" name="email" placeholder="Email" required>
                </div>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <div class="input-with-icon">
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" id="password" name="password" placeholder="Password" required>
                    <i class="fa-regular fa-eye toggle-password"></i>
                </div>
            </div>
            <div class="form-group">
                <label for="password_confirm">Confirm Password:</label>
                <div class="input-with-icon">
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" id="password_confirm" name="password_confirm" placeholder="Password" required>
                    <i class="fa-regular fa-eye toggle-password"></i>
                </div>
            </div>
            <button type="submit" class="btn-auth-submit" id="registerBtn">Sign up</button>
        </form>
        
        <div class="auth-links-group" style="margin-top: 0.5rem;">
            <p>Already have an account? <a href="login.php">Log in</a></p>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>