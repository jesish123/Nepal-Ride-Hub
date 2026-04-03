<?php
require_once 'includes/db_connect.php';

echo "<h2>Executing V2 Database Updates...</h2>";

try {
    // 1. Add taxi to vehicle type Enum
    $pdo->exec("ALTER TABLE vehicles MODIFY COLUMN type ENUM('car', 'bike', 'bus', 'taxi') NOT NULL");

    // 2. Add purpose and routing fields to Bookings
    $pdo->exec("ALTER TABLE bookings ADD COLUMN purpose ENUM('travel', 'function', 'pick_and_drop') DEFAULT 'travel' AFTER end_date");
    $pdo->exec("ALTER TABLE bookings ADD COLUMN pickup_location VARCHAR(255) NULL AFTER purpose");
    $pdo->exec("ALTER TABLE bookings ADD COLUMN dropoff_location VARCHAR(255) NULL AFTER pickup_location");
    
    echo "<h3 style='color:green;'>Database successfully expanded with Taxi and specialized Function routing capabilities.</h3>";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column') !== false) {
        echo "<h3 style='color:orange;'>Columns already exist. Schema up to date!</h3>";
    } else {
        echo "<p style='color:red;'>Database Error: " . $e->getMessage() . "</p>";
    }
}
?>