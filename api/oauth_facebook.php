<?php
session_start();
require_once '../includes/db_connect.php';

// Facebook OAuth Configuration
$clientID = 'YOUR_FACEBOOK_APP_ID';
$clientSecret = 'YOUR_FACEBOOK_APP_SECRET';
$redirectUri = 'http://localhost/Nepal_Ride-Hub/api/oauth_facebook.php';

// Detect if real keys have been configured
$usingPlaceholders = ($clientID === 'YOUR_FACEBOOK_APP_ID' || empty($clientID));

// 1. Initial Redirect to Facebook
if (isset($_GET['action']) && $_GET['action'] == 'login') {
    if ($usingPlaceholders) {
        showSetupHelp('Facebook');
        exit;
    }
    $authUrl = "https://www.facebook.com/v12.0/dialog/oauth?client_id={$clientID}&redirect_uri={$redirectUri}&scope=email,public_profile";
    header("Location: $authUrl");
    exit;
}

// 2. Handling the callback and user setup
if (isset($_GET['code']) || isset($_GET['simulate'])) {
    
    // Simulated Facebook API data
    $email = 'facebook_user@example.com';
    $name = 'Facebook User';
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];
        } else {
            $hashed = password_hash(bin2hex(random_bytes(12)), PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password_hash, phone, role) VALUES (?, ?, ?, 'Social Login', 'customer')");
            $stmt->execute([$name, $email, $hashed]);
            
            $_SESSION['user_id'] = $pdo->lastInsertId();
            $_SESSION['name'] = $name;
            $_SESSION['role'] = 'customer';
        }
        
        $redirectUrl = ($_SESSION['role'] === 'admin') ? '../admin_dashboard.php' : '../customer_dashboard.php';
        header("Location: $redirectUrl");
        exit;
    } catch (PDOException $e) {
        die("Facebook Login Error: " . $e->getMessage());
    }

} else {
    showSetupHelp('Facebook');
    exit;
}

function showSetupHelp($provider) {
    echo "
    <div style='text-align:center; margin-top:100px; font-family:\"Inter\", sans-serif;'>
        <h1 style='color:#3561ff; font-weight:800;'>Nepal Ride Hub - Social Auth</h1>
        <div style='max-width:500px; margin: 0 auto; background:#f9f9f9; padding:2rem; border-radius:12px; box-shadow:0 10px 40px rgba(0,0,0,0.1);'>
            <h3 style='margin-top:0;'>Setup Required for $provider</h3>
            <p style='color:#666;'>Your <code>api/oauth_$provider.php</code> file is currently using placeholder Client IDs.</p>
            <p style='color:#666;'>To use the real login, please replace the <code>YOUR_...</code> variables in that file with actual credentials from the developer console.</p>
            
            <a href='oauth_".strtolower($provider).".php?simulate=1' style='display:block; padding:12px; background:#4267B2; color:#fff; text-decoration:none; border-radius:8px; font-weight:700; margin-top:1.5rem;'>Proceed with Simulated Login</a>
            <a href='../login.php' style='display:block; color:#aaa; margin-top:1rem; text-decoration:none; font-size:0.9rem;'>Cancel</a>
        </div>
    </div>";
}
?>