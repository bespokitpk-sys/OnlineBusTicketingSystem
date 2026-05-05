<?php
session_start();
require_once __DIR__ . '/config/db.php';

echo "<h1>Session & Ticket Debug</h1>";

echo "<h2>Current Session</h2>";
echo "<pre>";
echo "User ID: " . ($_SESSION['user_id'] ?? 'Not set') . "\n";
echo "User Name: " . ($_SESSION['user_name'] ?? 'Not set') . "\n";
echo "User Role: " . ($_SESSION['user_role'] ?? 'Not set') . "\n";
echo "</pre>";

echo "<h2>Ticket #7 Info</h2>";
$result = $conn->query("SELECT id, user_id, bus_name, source, destination FROM tickets WHERE id = 7 LIMIT 1");
if ($result && $result->num_rows > 0) {
    $ticket = $result->fetch_assoc();
    echo "<pre>";
    echo "Ticket ID: " . $ticket['id'] . "\n";
    echo "Owner (User ID): " . $ticket['user_id'] . "\n";
    echo "Bus: " . $ticket['bus_name'] . "\n";
    echo "Route: " . $ticket['source'] . " → " . $ticket['destination'] . "\n";
    echo "</pre>";
    
    echo "<h2>Ticket Owner Details</h2>";
    $userResult = $conn->query("SELECT id, name, email, role FROM users WHERE id = " . $ticket['user_id']);
    if ($userResult && $userResult->num_rows > 0) {
        $owner = $userResult->fetch_assoc();
        echo "<pre>";
        echo "Owner ID: " . $owner['id'] . "\n";
        echo "Owner Name: " . $owner['name'] . "\n";
        echo "Owner Email: " . $owner['email'] . "\n";
        echo "Owner Role: " . $owner['role'] . "\n";
        echo "</pre>";
        
        if ($_SESSION['user_id'] == $ticket['user_id']) {
            echo "<p style='background: #d4edda; padding: 10px; border-radius: 5px;'>✅ User owns this ticket</p>";
        } else {
            echo "<p style='background: #f8d7da; padding: 10px; border-radius: 5px;'>❌ User does NOT own this ticket</p>";
            echo "<p>Current user ID: " . $_SESSION['user_id'] . "</p>";
            echo "<p>Ticket owner ID: " . $ticket['user_id'] . "</p>";
        }
    }
} else {
    echo "<p>Ticket not found</p>";
}

echo "<h2>All Users</h2>";
$result = $conn->query("SELECT id, name, email, role FROM users ORDER BY id DESC LIMIT 5");
echo "<table style='border-collapse: collapse;'>";
echo "<tr style='background: #f5f7fa;'>";
echo "<td style='border: 1px solid #ddd; padding: 10px; font-weight: bold;'>ID</td>";
echo "<td style='border: 1px solid #ddd; padding: 10px; font-weight: bold;'>Name</td>";
echo "<td style='border: 1px solid #ddd; padding: 10px; font-weight: bold;'>Email</td>";
echo "<td style='border: 1px solid #ddd; padding: 10px; font-weight: bold;'>Role</td>";
echo "</tr>";

while ($user = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td style='border: 1px solid #ddd; padding: 10px;'>" . $user['id'] . "</td>";
    echo "<td style='border: 1px solid #ddd; padding: 10px;'>" . $user['name'] . "</td>";
    echo "<td style='border: 1px solid #ddd; padding: 10px;'>" . $user['email'] . "</td>";
    echo "<td style='border: 1px solid #ddd; padding: 10px;'>" . $user['role'] . "</td>";
    echo "</tr>";
}
echo "</table>";
?>
