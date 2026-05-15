<?php
// seed_coordinates.php — Give sample vehicles some real locations in Nepal
require_once 'includes/db_connect.php';

echo "<h2>📍 Seeding GPS Coordinates for Vehicles...</h2>";

try {
    // Some coordinates in Nepal
    $locations = [
        ['name' => 'Kathmandu Center (Durbar Marg)', 'lat' => 27.7120, 'lng' => 85.3130],
        ['name' => 'Pokhara (Lakeside)', 'lat' => 28.2095, 'lng' => 83.9589],
        ['name' => 'Chitwan (Sauraha)', 'lat' => 27.5750, 'lng' => 84.4890],
        ['name' => 'Lumbini (Mayadevi Temple)', 'lat' => 27.4830, 'lng' => 83.2750],
        ['name' => 'Nagarkot (View Point)', 'lat' => 27.7170, 'lng' => 85.5210],
    ];

    // Fetch all vehicles
    $stmt = $pdo->query("SELECT id FROM vehicles");
    $vehicles = $stmt->fetchAll();

    $count = 0;
    foreach ($vehicles as $index => $v) {
        $loc = $locations[$index % count($locations)];
        $pdo->prepare("UPDATE vehicles SET gps_lat = ?, gps_lng = ? WHERE id = ?")
            ->execute([$loc['lat'], $loc['lng'], $v['id']]);
        $count++;
    }

    echo "<h3 style='color: green;'>Updated {$count} vehicles with GPS coordinates!</h3>";
    echo "<a href='admin_dashboard.php'>Go to Admin Dashboard</a>";

} catch (PDOException $e) {
    echo "<p style='color: red;'>Error seeding coordinates: " . $e->getMessage() . "</p>";
}
?>
