<?php
require_once __DIR__ . '/config/db.php';

// Get all buses
$buses_result = $conn->query("SELECT id FROM buses");
$buses = [];
while ($row = $buses_result->fetch_assoc()) {
    $buses[] = $row['id'];
}

// Routes to cycle through
$routes = [
    ['source' => 'Karachi', 'destination' => 'Lahore'],
    ['source' => 'Lahore', 'destination' => 'Karachi'],
    ['source' => 'Lahore', 'destination' => 'Islamabad'],
    ['source' => 'Islamabad', 'destination' => 'Lahore'],
    ['source' => 'Lahore', 'destination' => 'Kharian'],
    ['source' => 'Kharian', 'destination' => 'Lahore'],
    ['source' => 'Karachi', 'destination' => 'Islamabad'],
    ['source' => 'Islamabad', 'destination' => 'Karachi'],
];

// Departure times
$times = ['08:00', '10:00', '12:00', '14:00', '16:00', '18:00', '20:00'];

$today = new DateTime('2026-05-05'); // Start from today
$count = 0;

// Add schedules for next 20 days
for ($day = 0; $day < 20; $day++) {
    $current_date = clone $today;
    $current_date->modify("+{$day} days");
    $date_str = $current_date->format('Y-m-d');
    
    // Add 2-3 schedules per day
    for ($i = 0; $i < 3; $i++) {
        $route = $routes[($day * 3 + $i) % count($routes)];
        $time = $times[($day * 3 + $i) % count($times)];
        $bus_id = $buses[($day * 3 + $i) % count($buses)];
        
        $departure_time = $date_str . ' ' . $time . ':00';
        
        $stmt = $conn->prepare("
            INSERT INTO schedules (bus_id, source, destination, departure_time) 
            VALUES (?, ?, ?, ?)
        ");
        $stmt->bind_param('isss', $bus_id, $route['source'], $route['destination'], $departure_time);
        
        if ($stmt->execute()) {
            $count++;
        }
        $stmt->close();
    }
}

echo "✅ Successfully added $count schedules for the next 20 days!";
?>
