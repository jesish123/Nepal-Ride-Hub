<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = isset($_POST['name']) ? trim(strip_tags(htmlspecialchars($_POST['name']))) : '';
    $email = isset($_POST['email']) ? trim(strip_tags(htmlspecialchars($_POST['email']))) : '';
    $subject = isset($_POST['subject']) ? trim(strip_tags(htmlspecialchars($_POST['subject']))) : '';
    $message = isset($_POST['message']) ? trim(strip_tags(htmlspecialchars($_POST['message']))) : '';

    $_SESSION['nrh_form_name'] = $name;
    $_SESSION['nrh_form_email'] = $email;
    $_SESSION['nrh_form_subject'] = $subject;
    $_SESSION['nrh_form_message'] = $message;

    $error = '';
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email address.";
    } elseif (strlen($name) < 2 || strlen($name) > 100) {
        $error = "Name must be between 2 and 100 characters.";
    } elseif (strlen($message) < 10 || strlen($message) > 2000) {
        $error = "Message must be between 10 and 2000 characters.";
    }

    if (empty($error)) {
        if (isset($_SESSION['nrh_last_contact'])) {
            $time_passed = time() - $_SESSION['nrh_last_contact'];
            if ($time_passed < 60) {
                $wait = 60 - $time_passed;
                $error = "Please wait {$wait} seconds before submitting again.";
            }
        }
    }

    if (!empty($error)) {
        $_SESSION['nrh_msg'] = $error;
        $_SESSION['nrh_msg_type'] = 'error';
        header("Location: contact.php");
        exit;
    }

    require 'vendor/phpmailer/Exception.php';
    require 'vendor/phpmailer/PHPMailer.php';
    require 'vendor/phpmailer/SMTP.php';

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'support.nepalridehub@gmail.com';
        $mail->Password   = 'krnacwetzvfqbgik';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('support.nepalridehub@gmail.com', 'Nepal Ride Hub Contact');
        $mail->addAddress('support.nepalridehub@gmail.com', 'Nepal Ride Hub Support');
        $mail->addReplyTo($email, $name);

        $mail->isHTML(true);
        $mail->Subject = "Nepal Ride Hub — New Message: " . $subject;
        $mail->CharSet = 'UTF-8';
        
        $email_body = "
        <div style='background: #ffffff; padding: 20px; font-family: sans-serif;'>
            <div style='background: #0f172a; border-radius: 10px; overflow: hidden; max-width: 600px; margin: 0 auto; color: #ffffff;'>
                <div style='background: #f97316; padding: 20px; text-align: center;'>
                    <h2 style='color: #ffffff; margin: 0; font-weight: bold;'>Nepal Ride Hub</h2>
                    <p style='margin: 5px 0 0 0; color: #ffffff;'>New Contact Form Message</p>
                </div>
                <div style='padding: 20px;'>
                    <table style='width: 100%; border-collapse: collapse; margin-bottom: 20px;'>
                        <tr>
                            <td style='padding: 8px 0; border-bottom: 1px solid #334155; width: 100px; color: #94a3b8;'>👤 Full Name</td>
                            <td style='padding: 8px 0; border-bottom: 1px solid #334155;'>{$name}</td>
                        </tr>
                        <tr>
                            <td style='padding: 8px 0; border-bottom: 1px solid #334155; color: #94a3b8;'>📧 Email</td>
                            <td style='padding: 8px 0; border-bottom: 1px solid #334155;'><a href='mailto:{$email}' style='color: #38bdf8; text-decoration: none;'>{$email}</a></td>
                        </tr>
                        <tr>
                            <td style='padding: 8px 0; border-bottom: 1px solid #334155; color: #94a3b8;'>📌 Subject</td>
                            <td style='padding: 8px 0; border-bottom: 1px solid #334155;'>{$subject}</td>
                        </tr>
                    </table>
                    <div style='height: 2px; background: #f97316; margin: 20px 0;'></div>
                    <h3 style='color: #f97316; margin-top: 0;'>💬 Message</h3>
                    <div style='background: #1e293b; padding: 15px; border-radius: 8px; color: #e2e8f0; line-height: 1.6;'>
                        " . nl2br($message) . "
                    </div>
                </div>
                <div style='text-align: center; padding: 20px; color: #94a3b8; font-size: 12px; border-top: 1px solid #334155;'>
                    <p style='margin: 0;'>This message was sent via Nepal Ride Hub contact form</p>
                    <p style='margin: 5px 0 0 0;'>Reply to this email to respond directly to the visitor</p>
                </div>
            </div>
        </div>";

        $mail->Body    = $email_body;
        $mail->AltBody = "Nepal Ride Hub — New Contact Message\nName: {$name}\nEmail: {$email}\nSubject: {$subject}\nMessage: {$message}";

        $mail->send();

        $_SESSION['nrh_last_contact'] = time();
        $_SESSION['nrh_msg'] = "Message sent successfully! We will get back to you soon.";
        $_SESSION['nrh_msg_type'] = 'success';
        
        unset($_SESSION['nrh_form_name']);
        unset($_SESSION['nrh_form_email']);
        unset($_SESSION['nrh_form_subject']);
        unset($_SESSION['nrh_form_message']);
        
        header("Location: contact.php");
        exit;
    } catch (Exception $e) {
        $_SESSION['nrh_msg'] = "There was an error sending your message. Please try again later.";
        $_SESSION['nrh_msg_type'] = 'error';
        header("Location: contact.php");
        exit;
    }
}
?>
<?php include 'includes/header.php'; ?>

<style>
    .contact-container {
        padding: 5rem 0;
    }
    .contact-wrapper {
        display: grid;
        grid-template-columns: 1fr 1.5fr;
        gap: 4rem;
        background: #fff;
        padding: 4rem;
        border-radius: 30px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.05);
    }
    .info-card {
        background: #f8fbff;
        padding: 2.5rem;
        border-radius: 20px;
        height: 100%;
    }
    .info-item {
        display: flex;
        gap: 1.5rem;
        margin-bottom: 2.5rem;
    }
    .info-icon {
        width: 50px;
        height: 50px;
        background: #fff;
        color: var(--new-blue);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
    }
    .contact-form input, .contact-form textarea {
        width: 100%;
        padding: 1rem 1.5rem;
        border: 1.5px solid #eee;
        border-radius: 12px;
        font-family: 'Inter', sans-serif;
        margin-bottom: 1.5rem;
        transition: border-color 0.3s;
    }
    .contact-form input:focus, .contact-form textarea:focus {
        outline: none;
        border-color: var(--new-blue);
    }
</style>

<div class="contact-container">
    <div class="container">
        <div style="text-align: center; margin-bottom: 4rem;">
            <h1 style="font-size: 3rem; font-weight: 800; color: #111; margin-bottom: 1rem;">Contact Us</h1>
            <p style="color: #666; max-width: 600px; margin: 0 auto;">Have questions? Our team is here to help you plan your perfect ride across Nepal.</p>
        </div>

        <div class="contact-wrapper">
            <!-- Left: Info -->
            <div class="info-card">
                <h2 style="font-size: 1.8rem; font-weight: 800; margin-bottom: 3rem;">Support Channels</h2>
                
                <div class="info-item">
                    <div class="info-icon"><i class="fa-solid fa-phone-volume"></i></div>
                    <div>
                        <h4 style="margin-bottom: 0.3rem;">Call Support</h4>
                        <p style="color: #555; font-size: 0.95rem;">+977 1-4000000</p>
                        <p style="color: #555; font-size: 0.95rem;">+977 1-4000001</p>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-icon"><i class="fa-solid fa-envelope-open-text"></i></div>
                    <div>
                        <h4 style="margin-bottom: 0.3rem;">Email Queries</h4>
                        <p style="color: #555; font-size: 0.95rem;">support@nepalridehub.com</p>
                        <p style="color: #555; font-size: 0.95rem;">info@nepalridehub.org</p>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-icon"><i class="fa-solid fa-location-dot"></i></div>
                    <div>
                        <h4 style="margin-bottom: 0.3rem;">Visit Office</h4>
                        <p style="color: #555; font-size: 0.95rem;">Durbar Marg, Kathmandu</p>
                        <p style="color: #555; font-size: 0.95rem;">Near Narayanhiti Palace</p>
                    </div>
                </div>

                <div style="margin-top: 4rem;">
                    <h4 style="margin-bottom: 1rem;">Find Us On</h4>
                    <div style="display: flex; gap: 1rem;">
                        <a href="#" style="background: #fff; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 50%; color: #3b5998; box-shadow: 0 4px 10px rgba(0,0,0,0.05);"><i class="fa-brands fa-facebook-f"></i></a>
                        <a href="#" style="background: #fff; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 50%; color: #e1306c; box-shadow: 0 4px 10px rgba(0,0,0,0.05);"><i class="fa-brands fa-instagram"></i></a>
                        <a href="#" style="background: #fff; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 50%; color: #1da1f2; box-shadow: 0 4px 10px rgba(0,0,0,0.05);"><i class="fa-brands fa-twitter"></i></a>
                    </div>
                </div>
            </div>

            <!-- Right: Form -->
            <div class="contact-form">
                <?php if (isset($_SESSION['nrh_msg'])): ?>
                    <?php 
                        $msgType = $_SESSION['nrh_msg_type'] === 'success' ? 'success' : 'error'; 
                        $bgColor = $msgType === 'success' ? '#dcfce7' : '#fee2e2';
                        $borderColor = $msgType === 'success' ? '#22c55e' : '#ef4444';
                        $textColor = $msgType === 'success' ? '#166534' : '#991b1b';
                    ?>
                    <div id="nrhAlert" style="background: <?php echo $bgColor; ?>; border-left: 4px solid <?php echo $borderColor; ?>; color: <?php echo $textColor; ?>; padding: 1rem 1.5rem; border-radius: 12px; margin-bottom: 2rem; position: relative; font-family: 'Inter', sans-serif;">
                        <span style="display: block; padding-right: 20px;"><?php echo htmlspecialchars($_SESSION['nrh_msg']); ?></span>
                        <button type="button" onclick="document.getElementById('nrhAlert').style.display='none';" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); background: transparent; border: none; font-size: 1.2rem; color: <?php echo $textColor; ?>; cursor: pointer; padding: 0;">&times;</button>
                    </div>
                    <script>
                        setTimeout(function() {
                            var alertMsg = document.getElementById('nrhAlert');
                            if(alertMsg) { alertMsg.style.display = 'none'; }
                        }, 5000);
                    </script>
                    <?php 
                        unset($_SESSION['nrh_msg']);
                        unset($_SESSION['nrh_msg_type']);
                    ?>
                <?php endif; ?>

                <h2 style="font-size: 1.8rem; font-weight: 800; margin-bottom: 2.5rem;">Send Message</h2>
                
                <form action="contact.php" method="POST">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                        <input type="text" name="name" placeholder="Full Name" value="<?php echo isset($_SESSION['nrh_form_name']) ? htmlspecialchars($_SESSION['nrh_form_name']) : ''; ?>" required>
                        <input type="email" name="email" placeholder="Email Address" value="<?php echo isset($_SESSION['nrh_form_email']) ? htmlspecialchars($_SESSION['nrh_form_email']) : ''; ?>" required>
                    </div>
                    <input type="text" name="subject" placeholder="Subject" value="<?php echo isset($_SESSION['nrh_form_subject']) ? htmlspecialchars($_SESSION['nrh_form_subject']) : ''; ?>" required>
                    <textarea name="message" placeholder="How can we help you?" style="height: 15rem; resize: none;" required><?php echo isset($_SESSION['nrh_form_message']) ? htmlspecialchars($_SESSION['nrh_form_message']) : ''; ?></textarea>
                    
                    <button type="submit" class="btn-blue-solid" style="width: 100%; padding: 1.2rem; border-radius: 12px; font-weight: 700; box-shadow: 0 8px 20px rgba(53,97,255,0.25);">Send Your Message</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
