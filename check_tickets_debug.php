<?php
require_once __DIR__ . '/config/db.php';

echo "<h2>📋 All Tickets in Database</h2>";
echo "<table style='border-collapse: collapse; width: 100%; margin: 20px;'>";
echo "<thead><tr style='background: #f5f7fa;'>";
echo "<th style='border: 1px solid #ddd; padding: 10px;'>Ticket ID</th>";
echo "<th style='border: 1px solid #ddd; padding: 10px;'>Passenger</th>";
echo "<th style='border: 1px solid #ddd; padding: 10px;'>Schedule</th>";
echo "<th style='border: 1px solid #ddd; padding: 10px;'>Seats</th>";
echo "<th style='border: 1px solid #ddd; padding: 10px;'>Status</th>";
echo "</tr></thead>";
echo "<tbody>";

$result = $conn->query("
    SELECT 
        t.id as ticket_id,
        u.name,
        s.source,
        s.destination,
        t.seats,
        t.status,
        t.schedule_id,
        s.operator_id
    FROM tickets t
    LEFT JOIN users u ON t.user_id = u.id
    LEFT JOIN schedules s ON t.schedule_id = s.id
    ORDER BY t.id DESC
");

if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td style='border: 1px solid #ddd; padding: 10px;'>#" . $row['ticket_id'] . "</td>";
        echo "<td style='border: 1px solid #ddd; padding: 10px;'>" . htmlspecialchars($row['name']) . "</td>";
        echo "<td style='border: 1px solid #ddd; padding: 10px;'>" . htmlspecialchars($row['source'] . " → " . $row['destination']) . " (Schedule: " . $row['schedule_id'] . ", Operator: " . ($row['operator_id'] ?? 'NULL') . ")</td>";
        echo "<td style='border: 1px solid #ddd; padding: 10px;'>" . $row['seats'] . "</td>";
        echo "<td style='border: 1px solid #ddd; padding: 10px;'><strong>" . $row['status'] . "</strong></td>";
        echo "</tr>";
    }
}

echo "</tbody>";
echo "</table>";

echo "<h2>✅ Summary</h2>";
$countResult = $conn->query("SELECT COUNT(*) as count FROM tickets");
$countRow = $countResult->fetch_assoc();
echo "<p>Total Tickets: <strong>" . $countRow['count'] . "</strong></p>";

// Check if Ali Khan's ticket exists
$aliResult = $conn->query("SELECT t.*, s.source, s.destination FROM tickets t LEFT JOIN schedules s ON t.schedule_id = s.id WHERE t.user_id = (SELECT id FROM users WHERE email = 'alikhan@test.com')");
if ($aliResult && $aliRow = $aliResult->fetch_assoc()) {
    echo "<p style='background: #d4edda; padding: 15px; border-radius: 6px;'>";
    echo "✅ Found Ali Khan's ticket (#" . $aliRow['id'] . ") on schedule: " . $aliRow['source'] . " → " . $aliRow['destination'];
    echo "</p>";
} else {
    echo "<p style='background: #f8d7da; padding: 15px; border-radius: 6px;'>";
    echo "❌ Ali Khan's ticket not found!";
    echo "</p>";
}

// Check if Sarah's ticket exists
$sarahResult = $conn->query("SELECT t.*, s.source, s.destination FROM tickets t LEFT JOIN schedules s ON t.schedule_id = s.id WHERE t.user_id = (SELECT id FROM users WHERE email = 'sarah@test.com')");
if ($sarahResult && $sarahRow = $sarahResult->fetch_assoc()) {
    echo "<p style='background: #d4edda; padding: 15px; border-radius: 6px;'>";
    echo "✅ Found Sarah Ahmed's ticket (#" . $sarahRow['id'] . ") on schedule: " . $sarahRow['source'] . " → " . $sarahRow['destination'];
    echo "</p>";
} else {
    echo "<p style='background: #f8d7da; padding: 15px; border-radius: 6px;'>";
    echo "❌ Sarah Ahmed's ticket not found!";
    echo "</p>";
}
?>
