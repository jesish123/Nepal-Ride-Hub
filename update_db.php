<?php
require_once 'includes/db_connect.php';

echo "<h2>Updating Database Schema...</h2>";

try {
    // Add condition_type to vehicles table safely
    $pdo->exec("ALTER TABLE vehicles ADD COLUMN condition_type ENUM('city', 'offroad', 'highway', 'all-terrain') DEFAULT 'city' AFTER type");
    
    // Auto-migrate existing seed vehicles to sensible defaults
    $pdo->exec("UPDATE vehicles SET condition_type = 'city' WHERE type = 'car'");
    $pdo->exec("UPDATE vehicles SET condition_type = 'offroad' WHERE brand = 'Royal Enfield'");
    $pdo->exec("UPDATE vehicles SET condition_type = 'highway' WHERE type = 'bus'");
    
    echo "<h3 style='color: green;'>Successfully added 'condition_type' to vehicles! existing records migrated.</h3>";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column') !== false) {
        echo "<h3 style='color: orange;'>Column already exists. Database is up to date.</h3>";
    } else {
        echo "<p style='color: red;'>Database error: " . $e->getMessage() . "</p>";
    }
}
?>
