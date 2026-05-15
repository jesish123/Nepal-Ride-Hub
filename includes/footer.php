</main>
<footer class="footer">
    <div class="container footer-content">
        <div class="footer-brand">
            <h3><i class="fa-solid fa-mountain-sun"></i> Nepal Ride Hub</h3>
            <p>Explore the beauty of Nepal with our premium, reliable, and verified vehicle rentals.</p>
        </div>
        <div class="footer-links">
            <h4>Quick Links</h4>
            <a href="index.php">Home</a>
            <a href="vehicles.php">Browse Vehicles</a>
            <a href="about.php">About Us</a>
            <a href="blog.php">Latest Blog</a>
            <a href="contact.php">Contact Us</a>
        </div>
        <div class="footer-contact">
            <h4>Contact Support</h4>
            <p><i class="fas fa-phone"></i> <a href="tel:+97714000000"
                    style="color: inherit; text-decoration: none;">+977 1-4000000</a></p>
            <p><i class="fas fa-envelope"></i> <a href="mailto:support@nepalridehub.com"
                    style="color: inherit; text-decoration: none;">support@nepalridehub.com</a></p>
            <p><i class="fas fa-location-dot"></i> <a
                    href="https://www.google.com/maps/search/?api=1&query=Kathmandu,Nepal" target="_blank"
                    style="color: inherit; text-decoration: none;">Kathmandu, Nepal</a></p>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; <?php echo date('Y'); ?> Nepal Ride Hub. All rights reserved.</p>
    </div>
</footer>
<script src="js/ai_chatbot.js?v=<?php echo time(); ?>"></script>
<script src="js/script.js?v=<?php echo time(); ?>"></script>

<!-- Background Live Tracking for Customers -->
<?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'customer'): ?>
    <script>
        function updateMyLocation() {
            if ("geolocation" in navigator) {
                navigator.geolocation.getCurrentPosition(function (position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;

                    const formData = new FormData();
                    formData.append('lat', lat);
                    formData.append('lng', lng);

                    fetch('api/manage_vehicles.php?action=update_current_location', {
                        method: 'POST',
                        body: formData
                    })
                        .then(res => res.json())
                        .then(data => {
                            console.log("GPS Sync:", data.message);
                        })
                        .catch(err => console.error("GPS Sync Error:", err));
                }, function (error) {
                    console.warn("Location access denied or unavailable.");
                }, {
                    enableHighAccuracy: true,
                    timeout: 5000,
                    maximumAge: 0
                });
            }
        }

        // Run immediately then every 45 seconds
        updateMyLocation();
        setInterval(updateMyLocation, 45000);
    </script>
<?php endif; ?>
</body>

</html>