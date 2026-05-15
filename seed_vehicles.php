<?php
// seed_vehicles.php
require_once __DIR__ . '/includes/db_connect.php';

$vehicles = [
    [
        'name' => 'Yamaha MT-15 V2',
        'type' => 'bike',
        'condition_type' => 'city',
        'brand' => 'Yamaha',
        'model_year' => 2024,
        'price_per_day' => 1500.00,
        'image_path' => 'uploads/vehicles/yamaha_mt15.png',
        'description' => 'A powerful 155cc hyper-naked bike with aggressive styling and superior handling, perfect for city rides and weekend getaways.'
    ],
    [
        'name' => 'Bajaj Pulsar NS200',
        'type' => 'bike',
        'condition_type' => 'highway',
        'brand' => 'Bajaj',
        'model_year' => 2023,
        'price_per_day' => 1200.00,
        'image_path' => 'uploads/vehicles/bajaj_pulsar_ns200.png',
        'description' => 'The ultimate naked sports bike with a perimeter frame and liquid-cooled engine, offering high performance and great fuel efficiency.'
    ],
    [
        'name' => 'BMW G 310 R',
        'type' => 'bike',
        'condition_type' => 'city',
        'brand' => 'BMW',
        'model_year' => 2024,
        'price_per_day' => 4500.00,
        'image_path' => 'uploads/vehicles/bmw_g310r.png',
        'description' => 'Premium compact roadster from BMW Motorrad. Agile, easy to handle, and optimized for pure riding pleasure.'
    ],
    [
        'name' => 'Royal Enfield Classic 350',
        'type' => 'bike',
        'condition_type' => 'highway',
        'brand' => 'Royal Enfield',
        'model_year' => 2023,
        'price_per_day' => 2500.00,
        'image_path' => 'uploads/vehicles/classic_350.png',
        'description' => 'The timeless classic. Heavy-duty construction with a smooth J-series engine, ideal for long-distance cruising across Nepal.'
    ],
    [
        'name' => 'Toyota Corolla',
        'type' => 'car',
        'condition_type' => 'highway',
        'brand' => 'Toyota',
        'model_year' => 2022,
        'price_per_day' => 6000.00,
        'image_path' => 'uploads/vehicles/toyota_corolla.png',
        'description' => 'The world’s best-selling car. Unmatched reliability, comfort, and safety features for your family trips.'
    ],
    [
        'name' => 'BMW 3 Series',
        'type' => 'car',
        'condition_type' => 'city',
        'brand' => 'BMW',
        'model_year' => 2023,
        'price_per_day' => 18000.00,
        'image_path' => 'uploads/vehicles/bmw_3series.png',
        'description' => 'A luxury sedan that defines sporty elegance. Experience ultimate comfort and cutting-edge technology.'
    ],
    [
        'name' => 'Mahindra XUV700',
        'type' => 'car',
        'condition_type' => 'all-terrain',
        'brand' => 'Mahindra',
        'model_year' => 2024,
        'price_per_day' => 8500.00,
        'image_path' => 'uploads/vehicles/mahindra_xuv700.png',
        'description' => 'A feature-packed SUV with 5-star safety rating and ADAS technology. Spacious and powerful for all terrains.'
    ],
    [
        'name' => 'Mahindra Thar 4x4',
        'type' => 'jeep',
        'condition_type' => 'offroad',
        'brand' => 'Mahindra',
        'model_year' => 2024,
        'price_per_day' => 10000.00,
        'image_path' => 'uploads/vehicles/mahindra_thar.png',
        'description' => 'The ultimate off-roader. Rugged, capable, and iconic. Perfect for exploring the high Himalayas of Nepal.'
    ],
    [
        'name' => 'Toyota Hiace',
        'type' => 'van',
        'condition_type' => 'highway',
        'brand' => 'Toyota',
        'model_year' => 2022,
        'price_per_day' => 12000.00,
        'image_path' => 'uploads/vehicles/toyata.png',
        'description' => 'A reliable 14-seater van for group tours and large families. Spacious, comfortable, and efficient.'
    ],
    [
        'name' => 'Ashok Leyland Tourist Bus',
        'type' => 'bus',
        'condition_type' => 'highway',
        'brand' => 'Ashok Leyland',
        'model_year' => 2023,
        'price_per_day' => 25000.00,
        'image_path' => 'uploads/vehicles/Tour.png',
        'description' => '35-seater luxury AC bus for large group travel, school trips, and corporate events across Nepal.'
    ],
    [
        'name' => 'KTM Duke 250',
        'type' => 'bike',
        'condition_type' => 'highway',
        'brand' => 'KTM',
        'model_year' => 2023,
        'price_per_day' => 3500.00,
        'image_path' => 'uploads/vehicles/yamahaR15.png', // Fallback for bike
        'description' => 'Light, powerful and packed with state-of-the-art technology, it guarantees a thrilling ride, whether you’re in the urban jungle or a forest of bends.'
    ],
    [
        'name' => 'Hyundai i20',
        'type' => 'car',
        'condition_type' => 'city',
        'brand' => 'Hyundai',
        'model_year' => 2024,
        'price_per_day' => 5000.00,
        'image_path' => 'uploads/vehicles/newcar.png', // Fallback for car
        'description' => 'A premium hatchback with a sophisticated look and feel. Perfect for city driving with its smooth handling and modern features.'
    ],
    [
        'name' => 'Suzuki Swift',
        'type' => 'car',
        'condition_type' => 'city',
        'brand' => 'Suzuki',
        'model_year' => 2024,
        'price_per_day' => 4500.00,
        'image_path' => 'uploads/vehicles/newcar.png',
        'description' => 'Fun to drive, easy to park, and extremely fuel-efficient. The Swift is the perfect urban companion for your daily needs.'
    ],
    [
        'name' => 'Force Traveller',
        'type' => 'van',
        'condition_type' => 'highway',
        'brand' => 'Force',
        'model_year' => 2023,
        'price_per_day' => 15000.00,
        'image_path' => 'uploads/vehicles/force_traveller.png',
        'description' => 'A versatile mini-bus with comfortable seating for up to 17 passengers. Ideal for long trips and group travel.'
    ],
    [
        'name' => 'Isuzu D-Max',
        'type' => 'jeep',
        'condition_type' => 'all-terrain',
        'brand' => 'Isuzu',
        'model_year' => 2023,
        'price_per_day' => 12000.00,
        'image_path' => 'uploads/vehicles/mahindra_xuv700.png', // Fallback for 4x4
        'description' => 'A powerful pickup truck that can handle anything you throw at it. Rugged, reliable, and built to last.'
    ]
];

echo "Cleaning up vehicles table...\n";

try {
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    $pdo->exec("TRUNCATE TABLE vehicles");
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
    echo "Vehicles table cleared.\n";

    echo "Starting vehicle seeding...\n";

    $stmt = $pdo->prepare("INSERT INTO vehicles (name, type, condition_type, brand, model_year, price_per_day, description, image_path, status) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'available')");

    $count = 0;
    foreach ($vehicles as $v) {
        $stmt->execute([
            $v['name'],
            $v['type'],
            $v['condition_type'],
            $v['brand'],
            $v['model_year'],
            $v['price_per_day'],
            $v['description'],
            $v['image_path']
        ]);
        $count++;
    }

    echo "Successfully seeded $count vehicles into the database!\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>