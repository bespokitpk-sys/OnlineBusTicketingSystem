<?php
require_once __DIR__ . '/config/db.php';

echo "<h2>All Schedules (as shown on Operator Dashboard, sorted by departure_time DESC)</h2>";
echo "<table style='border-collapse: collapse; width: 100%;'>";
echo "<tr style='background: #f5f7fa;'>";
echo "<th style='border: 1px solid #ddd; padding: 10px;'>ID</th>";
echo "<th style='border: 1px solid #ddd; padding: 10px;'>Route</th>";
echo "<th style='border: 1px solid #ddd; padding: 10px;'>Departure Time</th>";
echo "<th style='border: 1px solid #ddd; padding: 10px;'>Bus</th>";
echo "<th style='border: 1px solid #ddd; padding: 10px;'>Operator ID</th>";
echo "</tr>";

$result = $conn->query("
    SELECT s.id, s.source, s.destination, s.departure_time, b.bus_name, s.operator_id
    FROM schedules s
    LEFT JOIN buses b ON s.bus_id = b.id
    ORDER BY s.departure_time DESC
");

if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td style='border: 1px solid #ddd; padding: 10px;'>#" . $row['id'] . "</td>";
        echo "<td style='border: 1px solid #ddd; padding: 10px;'>" . htmlspecialchars($row['source'] . " → " . $row['destination']) . "</td>";
        echo "<td style='border: 1px solid #ddd; padding: 10px;'>" . date('M d, Y H:i', strtotime($row['departure_time'])) . "</td>";
        echo "<td style='border: 1px solid #ddd; padding: 10px;'>" . htmlspecialchars($row['bus_name']) . "</td>";
        echo "<td style='border: 1px solid #ddd; padding: 10px;'>" . ($row['operator_id'] ?? 'NULL') . "</td>";
        echo "</tr>";
    }
}

echo "</table>";
?>
