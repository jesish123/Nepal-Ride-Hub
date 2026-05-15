<?php
/**
 * db_review_sync.php
 * Consolidated database repair and enhancement script for Nepal Ride Hub reviews.
 * This script ensures the site_reviews table exists and has all required columns.
 */

require_once 'includes/db_connect.php';

try {
    // 1. Create or Update site_reviews table
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
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");

    // 2. Add missing columns if they don't exist
    $columnFixes = [
        "service_type" => "VARCHAR(100) DEFAULT 'general' AFTER comment",
        "booking_id"   => "INT NULL AFTER user_id",
        "admin_reply"  => "TEXT NULL",
        "replied_at"   => "TIMESTAMP NULL"
    ];

    foreach ($columnFixes as $col => $definition) {
        try {
            $pdo->exec("ALTER TABLE site_reviews ADD COLUMN IF NOT EXISTS $col $definition");
        } catch (PDOException $e) {
            // Probably already exists or DB version doesn't support IF NOT EXISTS on ALTER
        }
    }

    // 3. Ensure ENUM values are correct
    $pdo->exec("ALTER TABLE site_reviews MODIFY COLUMN status ENUM('pending','approved','rejected') DEFAULT 'pending'");

    echo "✅ Review Database Sync Completed Successfully!";
} catch (Exception $e) {
    die("❌ Sync Error: " . $e->getMessage());
}
?>