<?php
// includes/db_connect.php (PRODUCTION VERSION FOR INFINITYFREE)

/**
 * UPDATED WITH YOUR INFINITYFREE DETAILS:
 * Host: sql111.infinityfree.com
 * User: if0_41924868
 * Pass: SaHID786
 * 
 * IMPORTANT: You MUST create a database named 'nepal_ride_hub' in your 
 * InfinityFree Control Panel first. It will be named 'if0_41924868_nepal_ride_hub'.
 */

$host = 'sql111.infinityfree.com'; 
$dbname = 'if0_41924868_nepal_ride_hub'; 
$username = 'if0_41924868'; 
$password = 'SaHID786'; 

try {
    // Note: On InfinityFree, we don't use CREATE DATABASE IF NOT EXISTS 
    // as it is restricted. We connect directly to the database you created.
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
