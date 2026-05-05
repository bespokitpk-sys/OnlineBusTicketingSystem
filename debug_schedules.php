<?php
require_once 'config/db.php';

echo "<h2>Schedules and Operators</h2>";
echo "<table border='1'>";
echo "<tr><th>Schedule ID</th><th>Route</th><th>Bus</th><th>Operator ID</th><th>Operator Name</th><th>Tickets</th></tr>";

$result = $conn->query("
    SELECT 
        s.id,
        s.source,
        s.destination,
        s.operator_id,
        COALESCE(u.name, 'UNASSIGNED') as operator_name,
        COUNT(t.id) as ticket_count
    FROM schedules s
    LEFT JOIN users u ON s.operator_id = u.id
    LEFT JOIN tickets t ON s.id = t.schedule_id
    GROUP BY s.id
    ORDER BY s.id
");

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row['id'] . "</td>";
    echo "<td>" . $row['source'] . " → " . $row['destination'] . "</td>";
    echo "<td>" . $row['bus_id'] ?? 'N/A' . "</td>";
    echo "<td>" . ($row['operator_id'] ? $row['operator_id'] : 'NULL') . "</td>";
    echo "<td>" . $row['operator_name'] . "</td>";
    echo "<td>" . $row['ticket_count'] . "</td>";
    echo "</tr>";
}
echo "</table>";

echo "<h2>Current Logged-In User</h2>";
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
echo "User ID: " . ($_SESSION['user_id'] ?? 'Not logged in') . "<br>";
echo "User Name: " . ($_SESSION['user_name'] ?? 'N/A') . "<br>";
echo "User Role: " . ($_SESSION['user_role'] ?? 'N/A') . "<br>";

// Get operator info
if (isset($_SESSION['user_id'])) {
    $user = $conn->query("SELECT * FROM users WHERE id = " . intval($_SESSION['user_id']))->fetch_assoc();
    echo "<hr>";
    echo "<h2>Operator Details</h2>";
    echo "Email: " . $user['email'] . "<br>";
    echo "Phone: " . $user['phone'] . "<br>";
}
?>
