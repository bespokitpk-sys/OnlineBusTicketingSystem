<?php
/**
 * Database migration to add operator_id to schedules table
 * This allows tracking which operator created each schedule
 */

require_once __DIR__ . '/config/db.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

try {
    // Check if operator_id column already exists
    $result = $conn->query("SHOW COLUMNS FROM schedules LIKE 'operator_id'");
    
    if ($result->num_rows === 0) {
        // Add operator_id column
        $sql = "ALTER TABLE schedules ADD COLUMN operator_id INT NULL AFTER bus_id";
        if ($conn->query($sql)) {
            echo "✓ Added operator_id column to schedules table\n";
        } else {
            echo "✗ Error adding operator_id: " . $conn->error . "\n";
        }
        
        // Add foreign key constraint
        $sql = "ALTER TABLE schedules ADD CONSTRAINT fk_schedules_operator 
                FOREIGN KEY (operator_id) REFERENCES users(id) ON DELETE SET NULL";
        if ($conn->query($sql)) {
            echo "✓ Added foreign key constraint for operator_id\n";
        } else {
            echo "✗ Error adding foreign key: " . $conn->error . "\n";
        }
    } else {
        echo "✓ operator_id column already exists\n";
    }
    
    // Check if trip_status column exists (for future use)
    $result = $conn->query("SHOW COLUMNS FROM schedules LIKE 'trip_status'");
    if ($result->num_rows === 0) {
        $sql = "ALTER TABLE schedules ADD COLUMN trip_status ENUM('scheduled','in_progress','completed','cancelled') DEFAULT 'scheduled' AFTER departure_time";
        if ($conn->query($sql)) {
            echo "✓ Added trip_status column to schedules table\n";
        } else {
            echo "✗ Error adding trip_status: " . $conn->error . "\n";
        }
    } else {
        echo "✓ trip_status column already exists\n";
    }
    
    echo "\n✓ Database migration completed successfully!\n";
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

$conn->close();
?>
