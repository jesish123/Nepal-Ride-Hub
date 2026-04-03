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
                <h2 style="font-size: 1.8rem; font-weight: 800; margin-bottom: 2.5rem;">Send Message</h2>
                
                <form action="#" method="POST">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                        <input type="text" placeholder="Full Name" required>
                        <input type="email" placeholder="Email Address" required>
                    </div>
                    <input type="text" placeholder="Subject" required>
                    <textarea placeholder="How can we help you?" style="height: 15rem; resize: none;" required></textarea>
                    
                    <button type="submit" class="btn-blue-solid" style="width: 100%; padding: 1.2rem; border-radius: 12px; font-weight: 700; box-shadow: 0 8px 20px rgba(53,97,255,0.25);">Send Your Message</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>