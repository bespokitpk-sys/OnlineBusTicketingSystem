<?php
include_once __DIR__ . '/config/db.php';
include_once __DIR__ . '/models/User.php';

function createUserIfMissing($name, $email, $phone, $password, $role, $verified = false) {
    global $conn;
    $email = $conn->real_escape_string($email);
    $existing = $conn->query("SELECT id FROM users WHERE email = '$email' LIMIT 1");
    if ($existing && $existing->num_rows > 0) {
        return false;
    }

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $phone = $conn->real_escape_string($phone);
    $name = $conn->real_escape_string($name);
    $role = $conn->real_escape_string($role);
    $isVerified = $verified ? 1 : 0;

    $query = "INSERT INTO users (name, email, phone, password_hash, role, is_verified) VALUES ('$name', '$email', '$phone', '$passwordHash', '$role', $isVerified)";
    return $conn->query($query);
}

$seeds = [
    ['name' => 'Admin User', 'email' => 'admin@busticketing.local', 'phone' => '03001234567', 'password' => 'Admin@123', 'role' => 'admin', 'verified' => true],
    ['name' => 'Operator User', 'email' => 'operator@busticketing.local', 'phone' => '03007654321', 'password' => 'Operator@123', 'role' => 'operator', 'verified' => true],
    ['name' => 'Demo Passenger', 'email' => 'demo@busticketing.local', 'phone' => '03001112233', 'password' => 'Passenger@123', 'role' => 'passenger', 'verified' => true],
];

$messages = [];
foreach ($seeds as $seed) {
    if (createUserIfMissing($seed['name'], $seed['email'], $seed['phone'], $seed['password'], $seed['role'], $seed['verified'])) {
        $messages[] = "Created {$seed['role']} account: {$seed['email']}";
    } else {
        $messages[] = "Skipped existing account: {$seed['email']}";
    }
}

$sampleData = [
    "INSERT IGNORE INTO buses (id, bus_name, total_seats) VALUES (1, 'Karachi Express', 42), (2, 'Lahore Flyer', 36), (3, 'Islamabad Premium', 30), (4, 'Kharian Shuttle', 28)",
    "INSERT IGNORE INTO schedules (id, bus_id, source, destination, departure_time) VALUES
        (1, 1, 'Karachi', 'Lahore', '2026-05-01 08:00:00'),
        (2, 2, 'Lahore', 'Karachi', '2026-05-01 14:00:00'),
        (3, 3, 'Lahore', 'Islamabad', '2026-05-02 09:00:00'),
        (4, 3, 'Islamabad', 'Lahore', '2026-05-02 17:00:00'),
        (5, 4, 'Lahore', 'Kharian', '2026-05-01 12:30:00'),
        (6, 4, 'Kharian', 'Lahore', '2026-05-01 18:00:00')"
];

foreach ($sampleData as $sql) {
    if ($conn->query($sql) === TRUE) {
        $messages[] = "Seeded sample data.";
    } else {
        $messages[] = "Seed error: " . $conn->error;
    }
}

header('Content-Type: text/plain');
echo implode("\n", $messages);
?>