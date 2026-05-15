<?php
require_once 'includes/db_connect.php';
try {
    $pdo->exec("ALTER TABLE users ADD COLUMN location VARCHAR(255) NULL");
    echo "Added location\n";
} catch (Exception $e) {
}

try {
    $pdo->exec("ALTER TABLE users ADD COLUMN country VARCHAR(255) NULL");
    echo "Added country\n";
} catch (Exception $e) {
}
echo "Done";
?>