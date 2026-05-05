<?php
/**
 * Test Data Generator - Creates sample passengers and tickets
 * for testing the operator dashboard
 */

require_once __DIR__ . '/config/db.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

try {
    echo "🔧 Test Data Generator<br><br>";
    
    // Get first schedule
    $scheduleResult = $conn->query("SELECT id FROM schedules LIMIT 1");
    if (!$scheduleResult || $scheduleResult->num_rows === 0) {
        die("❌ No schedules found. Please create schedules first.");
    }
    
    $schedule = $scheduleResult->fetch_assoc();
    $schedule_id = $schedule['id'];
    echo "✓ Using Schedule ID: $schedule_id<br>";
    
    // Get first operator
    $operatorResult = $conn->query("SELECT id FROM users WHERE role = 'operator' LIMIT 1");
    if (!$operatorResult || $operatorResult->num_rows === 0) {
        die("❌ No operators found. Please create an operator account first.");
    }
    
    $operator = $operatorResult->fetch_assoc();
    $operator_id = $operator['id'];
    echo "✓ Using Operator ID: $operator_id<br><br>";
    
    // Update schedule to set operator_id if not already set
    $conn->query("UPDATE schedules SET operator_id = $operator_id WHERE id = $schedule_id AND operator_id IS NULL");
    echo "✓ Schedule assigned to operator<br>";
    
    // Create 3 test passengers and their tickets
    $test_passengers = [
        ['Ahmed Khan', 'ahmed@test.com', '03001234567', 2, 'pending'],
        ['Fatima Ali', 'fatima@test.com', '03002234567', 1, 'approved'],
        ['Hassan Raza', 'hassan@test.com', '03003234567', 3, 'approved'],
    ];
    
    echo "<br>Creating test passengers:<br>";
    
    foreach ($test_passengers as $idx => $passenger) {
        list($name, $email, $phone, $seats, $status) = $passenger;
        
        // Check if passenger exists
        $checkResult = $conn->query("SELECT id FROM users WHERE email = '$email'");
        
        if ($checkResult->num_rows > 0) {
            $user = $checkResult->fetch_assoc();
            $user_id = $user['id'];
            echo "✓ Using existing passenger: $name ($email)<br>";
        } else {
            // Create new passenger
            $password_hash = password_hash('Test@123', PASSWORD_BCRYPT);
            $insertUser = $conn->query("
                INSERT INTO users (name, email, phone, password_hash, role, is_verified, created_at)
                VALUES ('$name', '$email', '$phone', '$password_hash', 'passenger', 1, NOW())
            ");
            
            if ($insertUser) {
                $user_id = $conn->insert_id;
                echo "✓ Created passenger: $name ($email)<br>";
            } else {
                echo "✗ Error creating passenger: " . $conn->error . "<br>";
                continue;
            }
        }
        
        // Check if ticket already exists
        $checkTicket = $conn->query("SELECT id FROM tickets WHERE schedule_id = $schedule_id AND user_id = $user_id");
        
        if ($checkTicket->num_rows === 0) {
            // Create ticket
            $insertTicket = $conn->query("
                INSERT INTO tickets (user_id, schedule_id, seats, status, created_at)
                VALUES ($user_id, $schedule_id, $seats, '$status', NOW())
            ");
            
            if ($insertTicket) {
                echo "  → Ticket created: $seats seats, Status: $status<br>";
            } else {
                echo "  → Error creating ticket: " . $conn->error . "<br>";
            }
        } else {
            echo "  → Ticket already exists<br>";
        }
    }
    
    echo "<br>✅ Test data ready!<br>";
    echo "<br><a href='admin/dashboard.php'>Admin Dashboard</a> | <a href='operator/dashboard.php'>Operator Dashboard</a><br>";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}

$conn->close();
?>
