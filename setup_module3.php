<?php
require_once __DIR__ . '/config/db.php';

echo "<h2>Module 3 Database Setup</h2>";

// Add trip_status column to schedules table if it doesn't exist
$result = $conn->query("SHOW COLUMNS FROM schedules LIKE 'trip_status'");
if ($result->num_rows == 0) {
    $conn->query("ALTER TABLE schedules ADD COLUMN trip_status ENUM('scheduled', 'ongoing', 'completed', 'cancelled') DEFAULT 'scheduled' AFTER departure_time");
    echo "✅ Added trip_status column to schedules table<br>";
} else {
    echo "✅ trip_status column already exists<br>";
}

// Add boarded_at column to tickets if it doesn't exist
$result = $conn->query("SHOW COLUMNS FROM tickets LIKE 'boarded_at'");
if ($result->num_rows == 0) {
    $conn->query("ALTER TABLE tickets ADD COLUMN boarded_at DATETIME NULL AFTER status");
    echo "✅ Added boarded_at column to tickets table<br>";
} else {
    echo "✅ boarded_at column already exists<br>";
}

// Ensure ticket status supports boarded state
$statusColumn = $conn->query("SHOW COLUMNS FROM tickets LIKE 'status'");
$statusDefinition = $statusColumn && $statusColumn->num_rows > 0 ? strtolower((string) ($statusColumn->fetch_assoc()['Type'] ?? '')) : '';
if (strpos($statusDefinition, "'boarded'") === false) {
    $conn->query("ALTER TABLE tickets MODIFY COLUMN status ENUM('pending','approved','boarded','cancelled') NOT NULL DEFAULT 'pending'");
    echo "✅ Updated ticket status enum to include boarded<br>";
} else {
    echo "✅ ticket status enum already supports boarded<br>";
}

// Add operator_id to schedules if it doesn't exist
$result = $conn->query("SHOW COLUMNS FROM schedules LIKE 'operator_id'");
if ($result->num_rows == 0) {
    $conn->query("ALTER TABLE schedules ADD COLUMN operator_id INT NULL AFTER bus_id");
    $conn->query("ALTER TABLE schedules ADD FOREIGN KEY (operator_id) REFERENCES users(id) ON DELETE SET NULL");
    echo "✅ Added operator_id column to schedules table with foreign key<br>";
} else {
    echo "✅ operator_id column already exists<br>";
}

echo "<h3 style='color: #28a745;'>✅ Module 3 Database Setup Completed!</h3>";
echo "<a href='index.php'>← Back to Home</a>";
?>
