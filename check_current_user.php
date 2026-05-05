<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

echo "<h2>Current User Debug</h2>";

if (isset($_SESSION['user_id'])) {
    echo "✓ Logged In User ID: " . $_SESSION['user_id'] . "<br>";
    echo "✓ User Role: " . ($_SESSION['user_role'] ?? 'Not set') . "<br>";
    
    $user = currentUser();
    if ($user) {
        echo "<h3>User Details:</h3>";
        echo "ID: {$user['id']}<br>";
        echo "Name: {$user['name']}<br>";
        echo "Email: {$user['email']}<br>";
        echo "Role: {$user['role']}<br>";
        
        // Get schedules for this operator
        if ($user['role'] === 'operator') {
            echo "<h3>Assigned Schedules:</h3>";
            $schedules = $conn->query("
                SELECT s.id, s.source, s.destination, s.operator_id FROM schedules s WHERE s.operator_id = {$user['id']}
            ");
            if ($schedules && $schedules->num_rows > 0) {
                while ($sch = $schedules->fetch_assoc()) {
                    echo "ID: {$sch['id']}, Route: {$sch['source']} → {$sch['destination']}, Operator ID: {$sch['operator_id']}<br>";
                }
            } else {
                echo "No schedules assigned<br>";
            }
        }
    }
} else {
    echo "✗ Not logged in<br>";
}

$conn->close();
?>
