<?php
require_once 'config/db.php';
require_once 'includes/auth.php';
require_once 'controllers/OperatorController.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Get operator info (assuming operator 12 is logged in)
$operatorId = 12;

echo "<h1>Debug: getMySchedules for Operator 12</h1>";
echo "<h2>Query Results:</h2>";

$result = $conn->query("
    SELECT 
        s.id, s.bus_id, s.source, s.destination, s.departure_time, s.created_at, s.operator_id,
        b.bus_name, b.total_seats,
        COUNT(t.id) as total_bookings
    FROM schedules s
    LEFT JOIN buses b ON s.bus_id = b.id
    LEFT JOIN tickets t ON s.id = t.schedule_id
    WHERE s.operator_id = 12
    GROUP BY s.id
    ORDER BY s.departure_time DESC
");

if ($result) {
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>ID</th><th>Route</th><th>Bus</th><th>Departure</th><th>Operator</th><th>Bookings</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['source'] . " → " . $row['destination'] . "</td>";
        echo "<td>" . $row['bus_name'] . "</td>";
        echo "<td>" . $row['departure_time'] . "</td>";
        echo "<td>" . $row['operator_id'] . "</td>";
        echo "<td>" . $row['total_bookings'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "<p>Total rows: " . $result->num_rows . "</p>";
} else {
    echo "Query failed: " . $conn->error;
}
?>
