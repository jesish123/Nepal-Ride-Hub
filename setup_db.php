<?php
// setup_db.php
require_once 'includes/db_connect.php';

echo "<h2>Setting up Nepal Ride Hub Master Database...</h2>";

try {
    // 1. Users Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        phone VARCHAR(20) NOT NULL,
        location VARCHAR(100) NULL,
        country VARCHAR(100) NULL,
        role ENUM('customer', 'admin') DEFAULT 'customer',
        facebook_id VARCHAR(100) NULL,
        auth_provider ENUM('local', 'facebook', 'google') DEFAULT 'local',
        profile_photo VARCHAR(255) NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "<p>✅ Users table ready (including profile & social fields).</p>";

    // 2. User Documents Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS user_documents (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        document_type ENUM('citizenship', 'license', 'passport') NOT NULL,
        file_path VARCHAR(255) NOT NULL,
        status ENUM('pending', 'verified', 'rejected') DEFAULT 'pending',
        expiry_date DATE NULL,
        uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");
    echo "<p>✅ User Documents table ready.</p>";

    // 3. Vehicles Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS vehicles (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        type ENUM('car', 'bike', 'bus', 'taxi') NOT NULL,
        condition_type ENUM('city', 'offroad', 'highway', 'all-terrain') DEFAULT 'city',
        brand VARCHAR(50) NOT NULL,
        model_year INT NOT NULL,
        price_per_day DECIMAL(10, 2) NOT NULL,
        status ENUM('available', 'maintenance', 'booked') DEFAULT 'available',
        description TEXT,
        image_path VARCHAR(255),
        gps_lat DECIMAL(10, 8) NULL,
        gps_lng DECIMAL(11, 8) NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "<p>✅ Vehicles table ready (v2 with Taxi & Condition types).</p>";

    // 4. Bookings Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS bookings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        vehicle_id INT NOT NULL,
        start_date DATE NOT NULL,
        end_date DATE NOT NULL,
        purpose ENUM('travel', 'function', 'pick_and_drop') DEFAULT 'travel',
        pickup_location VARCHAR(255) NULL,
        dropoff_location VARCHAR(255) NULL,
        total_amount DECIMAL(10, 2) NOT NULL,
        status ENUM('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending',
        with_driver TINYINT(1) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE CASCADE
    )");
    echo "<p>✅ Bookings table ready (v2 with Routing & Driver options).</p>";

    // 5. Site Reviews Table (Modern)
    $pdo->exec("CREATE TABLE IF NOT EXISTS site_reviews (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        booking_id INT NULL,
        service_id INT NULL,
        rating INT NOT NULL CHECK(rating >= 1 AND rating <= 5),
        comment TEXT NOT NULL,
        service_type VARCHAR(100) DEFAULT 'general',
        status ENUM('pending','approved','rejected') DEFAULT 'pending',
        admin_reply TEXT NULL,
        replied_at TIMESTAMP NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE SET NULL
    )");
    echo "<p>✅ Site Reviews table ready.</p>";

    // 6. Emergency Contacts Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS emergency_contacts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        service_name VARCHAR(100) NOT NULL,
        phone_number VARCHAR(30) NOT NULL,
        description TEXT NULL,
        icon VARCHAR(50) DEFAULT 'fa-phone',
        display_order INT DEFAULT 0,
        is_active TINYINT(1) DEFAULT 1
    )");
    echo "<p>✅ Emergency Contacts table ready.</p>";

    // Seed Emergency Contacts if empty
    $checkEmerg = $pdo->query("SELECT COUNT(*) FROM emergency_contacts")->fetchColumn();
    if ($checkEmerg == 0) {
        $pdo->exec("INSERT INTO emergency_contacts (service_name, phone_number, description, icon, display_order) VALUES
        ('Nepal Police', '100', 'National police emergency line available 24/7 across Nepal.', 'fa-shield-halved', 1),
        ('Nepal Ambulance', '102', 'Metropolitan Ambulance Service for immediate medical emergencies.', 'fa-truck-medical', 2),
        ('Fire Brigade', '101', 'Fire and rescue services for emergency fire situations.', 'fa-fire-extinguisher', 3),
        ('Tourist Police', '1144', 'Dedicated helpline for tourists facing issues during their Nepal visit.', 'fa-star-of-life', 4),
        ('Nepal Ride Hub 24/7 Support', '+977-01-4000000', 'Our round-the-clock customer support for all rental-related emergencies.', 'fa-headset', 5),
        ('Roadside Assistance', '+977-9800000001', 'Vehicle breakdown and roadside assistance for all active bookings.', 'fa-car-burst', 6)
        ");
        echo "<p>✨ Emergency contacts seeded successfully.</p>";
    }

    // 7. Emergency Incidents Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS emergency_incidents (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NULL,
        booking_id INT NULL,
        incident_type VARCHAR(100) NOT NULL,
        description TEXT NOT NULL,
        location_text VARCHAR(255) NULL,
        gps_lat DECIMAL(10,8) NULL,
        gps_lng DECIMAL(11,8) NULL,
        status ENUM('open','in_progress','resolved') DEFAULT 'open',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
        FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE SET NULL
    )");
    echo "<p>✅ Emergency Incidents table ready.</p>";

    // 8. Maintenance Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS maintenance (
        id INT AUTO_INCREMENT PRIMARY KEY,
        vehicle_id INT NOT NULL,
        service_date DATE NOT NULL,
        description TEXT NOT NULL,
        cost DECIMAL(10, 2) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE CASCADE
    )");
    echo "<p>✅ Maintenance table ready.</p>";

    // 9. Feedback Table (Legacy/General Compatibility)
    $pdo->exec("CREATE TABLE IF NOT EXISTS feedback (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        vehicle_id INT NOT NULL,
        rating INT NOT NULL CHECK(rating >= 1 AND rating <= 5),
        comments TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE CASCADE
    )");
    echo "<p>✅ Legacy Feedback table ready.</p>";

    // 10. Default Admin Account
    $adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password_hash, phone, role) VALUES (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE id=id");
    $stmt->execute(['Admin', 'admin@nepalridehub.com', $adminPassword, '9800000000', 'admin']);
    echo "<p>👑 Default Admin account created (admin@nepalridehub.com / admin123).</p>";

    echo "<hr><h3 style='color: green;'>🚀 Nepal Ride Hub Database setup completed successfully!</h3>";
    echo "<a href='index.php' style='display:inline-block; padding:10px 20px; background:#3561ff; color:#fff; text-decoration:none; border-radius:5px;'>Go to Homepage</a>";

} catch (PDOException $e) {
    echo "<div style='padding:1rem; background:#fee; border:1px solid #fcc; color:#c00; border-radius:8px;'>";
    echo "<strong>Database Error:</strong> " . $e->getMessage();
    echo "</div>";
}
?>