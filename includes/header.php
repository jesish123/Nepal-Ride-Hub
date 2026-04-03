<?php
// includes/header.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nepal Ride Hub - Premium Vehicle Rental</title>
    <!-- Modern Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,400;0,500;0,600;0,700;1,600&family=Outfit:ital,wght@0,400;0,600;0,800;1,800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="top-bar">
        <div class="container top-bar-container">
            <div>
                <i class="fa-solid fa-phone"></i> 01-234567
            </div>
            <div>
                <i class="fa-regular fa-envelope"></i> info@nepalridehub.org
            </div>
        </div>
    </div>
    
    <nav class="navbar-redesigned" style="background-color: #fff; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
        <div class="container" style="display: flex; justify-content: space-between; align-items: center;">
            <a href="index.php" class="logo-custom">
                <div class="logo-icon">
                    <i class="fa-solid fa-car-side"></i>
                </div>
                <div class="logo-text-wrapper">
                    <span class="logo-title">Nepal Ride Hub</span>
                    <span class="logo-subtitle">PREMIUM MOBILITY</span>
                </div>
            </a>
            
            <ul class="nav-links-right" style="margin: 0; padding: 0; display: flex; align-items: center; gap: 2rem;">
                <li><a href="index.php" class="nav-item">Home</a></li>
                <li><a href="vehicles.php" class="nav-item">Rent a car</a></li>
<<<<<<< HEAD
                <li><a href="#" class="nav-item">Blog</a></li>
                <li><a href="#" class="nav-item">About us</a></li>
                <li><a href="#" class="nav-item">Contact us</a></li>
=======
                <li><a href="blog.php" class="nav-item">Blog</a></li>
                <li><a href="about.php" class="nav-item">About us</a></li>
                <li><a href="contact.php" class="nav-item">Contact us</a></li>
>>>>>>> origin/seraj
                
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="user-profile-dropdown" style="position: relative; list-style: none;">
                        <div style="display: flex; align-items: center; gap: 0.75rem; cursor: pointer; padding: 0.5rem 1rem; background: #f8f9fa; border-radius: 50px;">
                            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['name']); ?>&background=3561ff&color=fff" alt="User" style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover;">
                            <span style="font-weight: 700; color: #111; font-size: 0.95rem;"><?php echo htmlspecialchars($_SESSION['name']); ?></span>
                            <i class="fa-solid fa-chevron-down" style="font-size: 0.7rem; color: #888;"></i>
                        </div>
                        <!-- Dropdown menu -->
                        <div class="dropdown-menu" style="display: none; position: absolute; top: 100%; right: 0; background: #fff; box-shadow: 0 10px 30px rgba(0,0,0,0.1); border-radius: 12px; min-width: 180px; z-index: 100; margin-top: 0.5rem; border: 1px solid #eee;">
<<<<<<< HEAD
                            <a href="profile.php" style="display: block; padding: 0.8rem 1.2rem; color: #333; text-decoration: none; border-bottom: 1px solid #eee; font-weight: 600;">My Profile</a>
=======
                            <a href="user_details.php" style="display: block; padding: 0.8rem 1.2rem; color: #333; text-decoration: none; border-bottom: 1px solid #eee; font-weight: 600;">User Details</a>
                            <a href="profile.php" style="display: block; padding: 0.8rem 1.2rem; color: #333; text-decoration: none; border-bottom: 1px solid #eee; font-weight: 600;">Edit Profile</a>
>>>>>>> origin/seraj
                            <a href="<?php echo ($_SESSION['role']==='admin'?'admin_dashboard.php':'customer_dashboard.php'); ?>" style="display: block; padding: 0.8rem 1.2rem; color: #333; text-decoration: none; border-bottom: 1px solid #eee; font-weight: 600;">Dashboard</a>
                            <a href="api/auth.php?action=logout" style="display: block; padding: 0.8rem 1.2rem; color: #da291c; text-decoration: none; font-weight: 600;">Logout</a>
                        </div>
                    </li>
                <?php else: ?>
                    <li style="list-style: none;">
                        <a href="login.php" class="btn-blue-solid" style="padding: 0.6rem 1.8rem; border-radius: 8px;">Login</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>
    <main>