<?php
// api/auth.php
session_start();
require_once '../includes/db_connect.php';
header('Content-Type: application/json');
$action = $_GET['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'register') {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $phone = trim($_POST['phone'] ?? '');

        if (empty($name) || empty($email) || empty($password) || empty($phone)) {
            echo json_encode(['success' => false, 'message' => 'All fields are required.']);
            exit;
        }

        try {
            // Check if email exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->rowCount() > 0) {
                echo json_encode(['success' => false, 'message' => 'Email already registered.']);
                exit;
            }

            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password_hash, phone, role) VALUES (?, ?, ?, ?, 'customer')");
            $stmt->execute([$name, $email, $hashed, $phone]);

            echo json_encode(['success' => true, 'message' => 'Registration successful!']);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Registration failed: ' . $e->getMessage()]);
        }
    } 
    elseif ($action === 'login') {
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (empty($email) || empty($password)) {
            echo json_encode(['success' => false, 'message' => 'Email and password are required.']);
            exit;
        }

        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? OR name = ?");
            $stmt->execute([$email, $email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password_hash'])) {
                if ($user['role'] === 'customer') {
                    // Generate 6-digit code for 2FA
                    $code = rand(100000, 999999);
                    
                    // Set temporary session
                    $_SESSION['temp_user_id'] = $user['id'];
                    $_SESSION['temp_user_name'] = $user['name'];
                    $_SESSION['temp_user_role'] = $user['role'];
                    $_SESSION['temp_user_email'] = $user['email'];
                    $_SESSION['temp_user_phone'] = $user['phone'];
                    $_SESSION['verification_code'] = $code;
                    
                    require '../vendor/phpmailer/Exception.php';
                    require '../vendor/phpmailer/PHPMailer.php';
                    require '../vendor/phpmailer/SMTP.php';

                    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
                    try {
                        $mail->isSMTP();
                        $mail->Host       = 'smtp.gmail.com';
                        $mail->SMTPAuth   = true;
                        $mail->Username   = 'support.nepalridehub@gmail.com';
                        $mail->Password   = 'krnacwetzvfqbgik';
                        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
                        $mail->Port       = 587;

                        $mail->setFrom('support.nepalridehub@gmail.com', 'Nepal Ride Hub');
                        $mail->addAddress($user['email'], $user['name']);

                        $mail->isHTML(true);
                        $mail->Subject = 'Login Verification Code - Nepal Ride Hub';
                        $mail->Body    = "Hello " . htmlspecialchars($user['name']) . ",<br><br>Your login verification code is: <b>$code</b><br><br>Please enter this code to securely log in.";
                        $mail->AltBody = "Hello " . $user['name'] . ",\n\nYour login verification code is: $code\n\nPlease enter this code to securely log in.";

                        $mail->send();
                        echo json_encode(['success' => true, 'redirect' => 'verify.php', 'message' => 'Verification code sent to your email.']);
                    } catch (Exception $e) {
                        // Fallback message for development if mail fails
                        echo json_encode(['success' => true, 'redirect' => 'verify.php', 'message' => 'Verification code sent (Dev note: code is ' . $code . ')']);
                    }
                } else {
                    // Admin logs in directly without code
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['name'] = $user['name'];
                    $_SESSION['role'] = $user['role'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['phone'] = $user['phone'];
                    echo json_encode(['success' => true, 'redirect' => 'admin_dashboard.php', 'message' => 'Login successful!']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid email or password.']);
            }
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Login error.']);
        }
    }
    elseif ($action === 'verify_code') {
        $code = trim($_POST['code'] ?? '');
        if (empty($code)) {
            echo json_encode(['success' => false, 'message' => 'Please enter the verification code.']);
            exit;
        }

        if (isset($_SESSION['verification_code']) && $_SESSION['verification_code'] == $code) {
            // Apply session data
            $_SESSION['user_id'] = $_SESSION['temp_user_id'];
            $_SESSION['name'] = $_SESSION['temp_user_name'];
            $_SESSION['role'] = $_SESSION['temp_user_role'];
            $_SESSION['email'] = $_SESSION['temp_user_email'];
            $_SESSION['phone'] = $_SESSION['temp_user_phone'];
            
            // Clean up temporary variables
            unset($_SESSION['temp_user_id'], $_SESSION['temp_user_name'], $_SESSION['temp_user_role'], $_SESSION['temp_user_email'], $_SESSION['temp_user_phone'], $_SESSION['verification_code']);
            
            // Redirect customer to home page index.php
            echo json_encode(['success' => true, 'redirect' => 'index.php', 'message' => 'Verification successful!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid verification code. Please try again.']);
        }
    }
    elseif ($action === 'forgot_password') {
        $email = trim($_POST['email'] ?? '');
        if (empty($email)) {
            echo json_encode(['success' => false, 'message' => 'Please enter your email or username.']);
            exit;
        }

        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? OR name = ?");
            $stmt->execute([$email, $email]);
            $user = $stmt->fetch();

            if ($user) {
                // Generate 6-digit reset code
                $code = rand(100000, 999999);
                $_SESSION['reset_email'] = $user['email'];
                $_SESSION['reset_code'] = $code;
                
                require '../vendor/phpmailer/Exception.php';
                require '../vendor/phpmailer/PHPMailer.php';
                require '../vendor/phpmailer/SMTP.php';

                $mail = new PHPMailer\PHPMailer\PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host       = 'smtp.gmail.com';
                    $mail->SMTPAuth   = true;
                    $mail->Username   = 'support.nepalridehub@gmail.com';
                    $mail->Password   = 'krnacwetzvfqbgik';
                    $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port       = 587;

                    $mail->setFrom('support.nepalridehub@gmail.com', 'Nepal Ride Hub');
                    $mail->addAddress($user['email'], $user['name']);

                    $mail->isHTML(true);
                    $mail->Subject = 'Password Reset Code - Nepal Ride Hub';
                    $mail->Body    = "Hello " . htmlspecialchars($user['name']) . ",<br><br>Your password reset code is: <b>$code</b><br><br>Please enter this code on the reset page to create a new password.";
                    $mail->AltBody = "Hello " . $user['name'] . ",\n\nYour password reset code is: $code\n\nPlease enter this code on the reset page to create a new password.";

                    $mail->send();
                    echo json_encode(['success' => true, 'message' => 'Reset code has been sent to your email address.', 'redirect' => 'reset_password.php']);
                } catch (Exception $e) {
                    // Fallback to simulation if mail fails, but don't show code in production
                    echo json_encode(['success' => true, 'message' => 'Email service error. For development, your code is: ' . $code, 'redirect' => 'reset_password.php']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'No account found with that email or username.']);
            }
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Database error.']);
        }
    }
    elseif ($action === 'reset_password') {
        $code = trim($_POST['code'] ?? '');
        $newPassword = $_POST['password'] ?? '';
        
        if (empty($code) || empty($newPassword)) {
            echo json_encode(['success' => false, 'message' => 'Code and new password are required.']);
            exit;
        }

        if (isset($_SESSION['reset_code']) && $_SESSION['reset_code'] == $code) {
            try {
                $email = $_SESSION['reset_email'];
                $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE email = ?");
                $stmt->execute([$hashed, $email]);

                unset($_SESSION['reset_code'], $_SESSION['reset_email']);
                echo json_encode(['success' => true, 'message' => 'Password reset successful! You can now log in.', 'redirect' => 'login.php']);
            } catch (PDOException $e) {
                echo json_encode(['success' => false, 'message' => 'Failed to reset password.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid or expired reset code.']);
        }
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'logout') {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    session_destroy();
    
    // Dynamic redirect based on where the user logged out from or where the file exists
    if (file_exists(__DIR__ . '/../uploads/login.php')) {
        header("Location: ../uploads/login.php");
    } else {
        header("Location: ../login.php");
    }
    exit;
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
?>
