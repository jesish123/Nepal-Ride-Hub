<?php
// update_db_v3.php — Full Migration v3: All new tables + extensions
require_once 'includes/db_connect.php';

echo "<style>body{font-family:sans-serif; max-width:700px; margin:40px auto; line-height:1.7}
.ok{color:#155724; background:#d4edda; padding:8px 12px; border-radius:6px; margin:6px 0; display:block}
.er{color:#721c24; background:#f8d7da; padding:8px 12px; border-radius:6px; margin:6px 0; display:block}
h2{color:#003893} h3{color:#28a745}
</style>";
echo "<h2>🚀 Nepal Ride Hub — Database Migration v3+ (Emergency Support)</h2>";

$errors = 0;

function runSQL(PDO $pdo, string $sql, string $label): void
{
    global $errors;
    try {
        $pdo->exec($sql);
        echo "<span class='ok'>✅ $label</span>";
    } catch (PDOException $e) {
        // Ignore "Duplicate column" errors (idempotent re-runs)
        if (str_contains($e->getMessage(), 'Duplicate column')) {
            echo "<span class='ok'>⏭️ $label (already exists)</span>";
        } else {
            echo "<span class='er'>❌ $label: " . htmlspecialchars($e->getMessage()) . "</span>";
            $errors++;
        }
    }
}

// 1. Extend users table
runSQL($pdo, "ALTER TABLE users ADD COLUMN IF NOT EXISTS facebook_id VARCHAR(100) NULL", "users.facebook_id column");
runSQL($pdo, "ALTER TABLE users ADD COLUMN IF NOT EXISTS auth_provider ENUM('local','facebook','google') DEFAULT 'local'", "users.auth_provider column");
runSQL($pdo, "ALTER TABLE users ADD COLUMN IF NOT EXISTS profile_photo VARCHAR(255) NULL", "users.profile_photo column");

// 2. Extend bookings table
runSQL($pdo, "ALTER TABLE bookings ADD COLUMN IF NOT EXISTS with_driver TINYINT(1) DEFAULT 0", "bookings.with_driver column");

// 4. Emergency Contacts
runSQL($pdo, "CREATE TABLE IF NOT EXISTS emergency_contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    service_name VARCHAR(100) NOT NULL,
    phone_number VARCHAR(30) NOT NULL,
    description TEXT NULL,
    icon VARCHAR(50) DEFAULT 'fa-phone',
    display_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1
)", "emergency_contacts table");

try {
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
        echo "<span class='ok'>✅ Emergency contacts seeded</span>";
    } else {
        echo "<span class='ok'>⏭️ Emergency contacts already seeded</span>";
    }
} catch (PDOException $e) {
    echo "<span class='er'>❌ Seeding emergency contacts: " . htmlspecialchars($e->getMessage()) . "</span>";
}

// 5. Site Reviews (with service_id)
runSQL($pdo, "CREATE TABLE IF NOT EXISTS site_reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    booking_id INT NULL,
    service_id INT NULL,
    rating INT NOT NULL CHECK(rating >= 1 AND rating <= 5),
    comment TEXT NOT NULL,
    service_type VARCHAR(100) DEFAULT 'general',
    status ENUM('pending','approved','rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE SET NULL
)", "site_reviews table");

// Add service_id to existing table if missing
runSQL($pdo, "ALTER TABLE site_reviews ADD COLUMN IF NOT EXISTS service_id INT NULL", "site_reviews.service_id column");

// 6. Emergency Incidents
runSQL($pdo, "CREATE TABLE IF NOT EXISTS emergency_incidents (
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
)", "emergency_incidents table");

// Ensure status enum is synced
runSQL($pdo, "ALTER TABLE emergency_incidents MODIFY COLUMN status ENUM('open','in_progress','resolved') DEFAULT 'open'", "Syncing status enum in emergency_incidents");


// 7. Upload dirs
$dirs = [
    'uploads/driver_docs',
    'uploads/profile_photos',
];
foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
        echo "<span class='ok'>✅ Directory created: $dir</span>";
    } else {
        echo "<span class='ok'>⏭️ Directory exists: $dir</span>";
    }
}

echo "<br>";
if ($errors === 0) {
    echo "<h3>✅ Migration v3 completed successfully with no errors!</h3>";
} else {
    echo "<h3 style='color:#dc3545;'>⚠️ Migration completed with $errors error(s). Review above.</h3>";
}

echo "<div style='margin-top:1.5rem; display:flex; gap:1rem;'>
    <a href='index.php' style='padding:0.6rem 1.5rem; background:#003893; color:#fff; border-radius:8px; text-decoration:none; font-weight:700;'>← Homepage</a>
    <a href='admin_dashboard.php' style='padding:0.6rem 1.5rem; background:#28a745; color:#fff; border-radius:8px; text-decoration:none; font-weight:700;'>Admin Dashboard →</a>
</div>";
?>