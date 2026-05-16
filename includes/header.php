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
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,400;0,500;0,600;0,700;1,600&family=Outfit:ital,wght@0,400;0,600;0,800;1,800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>">
    <style>
        @media (max-width: 992px) {
            .mobile-menu-btn {
                display: block !important;
                background: none;
                border: none;
                color: #111;
                font-size: 1.8rem;
                cursor: pointer;
                z-index: 1002;
            }
            .nav-links-right {
                display: none !important;
                flex-direction: column;
                position: absolute;
                top: 100%;
                left: 0;
                width: 100%;
                background: #fff;
                padding: 2rem;
                box-shadow: 0 10px 20px rgba(0,0,0,0.1);
                z-index: 1001;
            }
            .nav-links-right.active {
                display: flex !important;
            }
            .top-bar-email {
                display: none !important;
            }
            .top-bar-container {
                justify-content: center !important;
            }
        }
    </style>
    <script>
        window.CHATBOT_USER = <?php
        if (!empty($_SESSION['role']) && $_SESSION['role'] === 'admin') {
            echo json_encode([
                'logged_in' => true,
                'role'      => 'admin',
                'name'      => $_SESSION['name'] ?? 'Admin',
                'email'     => $_SESSION['email'] ?? '',
                'id'        => $_SESSION['user_id'] ?? '',
            ]);
        } elseif (!empty($_SESSION['user_id'])) {
            echo json_encode([
                'logged_in' => true,
                'role'      => 'user',
                'name'      => $_SESSION['name'] ?? '',
                'email'     => $_SESSION['email'] ?? '',
                'phone'     => $_SESSION['phone'] ?? '',
                'id'        => $_SESSION['user_id'],
            ]);
        } else {
            echo json_encode(['logged_in' => false]);
        }
        ?>;
    </script>
</head>

<body>
    <div class="top-bar">
        <div class="container top-bar-container">
            <div>
                <a href="tel:01-234567" style="color: inherit; text-decoration: none;"><i class="fa-solid fa-phone-volume"></i>
                    01-234567</a>
            </div>
            <div class="top-bar-email">
                <a href="mailto:info@nepalridehub.org" style="color: inherit; text-decoration: none;"><i
                        class="fa-regular fa-envelope"></i> info@nepalridehub.org</a>
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

            <button class="mobile-menu-btn" id="mobileMenuBtn">
                <i class="fa-solid fa-bars"></i>
            </button>

            <ul class="nav-links-right" id="navLinks">
                <li><a href="index.php" class="nav-item">Home</a></li>
                <li><a href="vehicles.php" class="nav-item">Rent a car</a></li>

                <li><a href="reviews.php" class="nav-item">Reviews</a></li>
                <li><a href="about.php" class="nav-item">About us</a></li>
                <li><a href="contact.php" class="nav-item">Contact us</a></li>
                <li><a href="emergency.php" class="nav-item" style="color: #3561ff; font-weight: 800;"><i
                            class="fas fa-phone-volume"></i> Emergency</a></li>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="user-profile-dropdown" style="position: relative; list-style: none;">
                        <div
                            style="display: flex; align-items: center; gap: 0.75rem; cursor: pointer; padding: 0.5rem 1rem; background: #f8f9fa; border-radius: 50px;">
                            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['name']); ?>&background=3561ff&color=fff"
                                alt="User" style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover;">
                            <span
                                style="font-weight: 700; color: #111; font-size: 0.95rem;"><?php echo htmlspecialchars($_SESSION['name']); ?></span>
                                <i class="fa-solid fa-chevron-down" style="font-size: 0.7rem; color: #888;"></i>
                        </div>
                        <!-- Dropdown menu -->
                        <div class="dropdown-menu"
                            style="display: none; position: absolute; top: 100%; right: 0; background: #fff; box-shadow: 0 10px 30px rgba(0,0,0,0.1); border-radius: 12px; min-width: 180px; z-index: 100; margin-top: 0.5rem; border: 1px solid #eee;">
                            <a href="user_details.php"
                                style="display: block; padding: 0.8rem 1.2rem; color: #333; text-decoration: none; border-bottom: 1px solid #eee; font-weight: 600;">User
                                Details</a>
                            <a href="profile.php"
                                style="display: block; padding: 0.8rem 1.2rem; color: #333; text-decoration: none; border-bottom: 1px solid #eee; font-weight: 600;">Edit
                                Profile</a>
                            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                                <a href="track_vehicles.php"
                                    style="display: block; padding: 0.8rem 1.2rem; color: #333; text-decoration: none; border-bottom: 1px solid #eee; font-weight: 600;"><i
                                        class="fa-solid fa-location-dot" style="margin-right: 8px; color: #3561ff;"></i>Live
                                    Tracking</a>
                            <?php endif; ?>
                            <a href="<?php echo ($_SESSION['role'] === 'admin' ? 'admin_dashboard.php' : 'customer_dashboard.php'); ?>"
                                style="display: block; padding: 0.8rem 1.2rem; color: #333; text-decoration: none; border-bottom: 1px solid #eee; font-weight: 600;">Dashboard</a>
                            <a href="api/auth.php?action=logout"
                                style="display: block; padding: 0.8rem 1.2rem; color: #da291c; text-decoration: none; font-weight: 600;">Logout</a>
                        </div>
                    </li>
                <?php else: ?>
                    <li style="list-style: none;">
                        <a href="login.php" class="btn-blue-solid"
                            style="padding: 0.6rem 1.8rem; border-radius: 8px;">Login</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <script>
        document.getElementById('mobileMenuBtn').addEventListener('click', function() {
            const nav = document.getElementById('navLinks');
            const icon = this.querySelector('i');
            nav.classList.toggle('active');
            if (nav.classList.contains('active')) {
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-xmark');
            } else {
                icon.classList.remove('fa-xmark');
                icon.classList.add('fa-bars');
            }
        });

        // Toggle User Dropdown on Mobile
        const userDropdown = document.querySelector('.user-profile-dropdown');
        if (userDropdown) {
            userDropdown.addEventListener('click', function(e) {
                if (window.innerWidth <= 992) {
                    const menu = this.querySelector('.dropdown-menu');
                    const isVisible = menu.style.display === 'block';
                    menu.style.display = isVisible ? 'none' : 'block';
                }
            });
        }
    </script>
    <main>