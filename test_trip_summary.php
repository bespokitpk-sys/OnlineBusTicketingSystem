<?php
require_once __DIR__ . '/config/db.php';

echo "<h2>Testing getTripSummary() for Schedule #1</h2>";

// Test the exact query from getTripSummary()
$scheduleId = 1;
$result = $conn->query("
    SELECT 
        s.id, s.source, s.destination, s.departure_time, s.created_at,
        b.bus_name, b.total_seats,
        COALESCE(COUNT(DISTINCT t.id), 0) as total_tickets,
        COALESCE(SUM(CASE WHEN t.status = 'pending' THEN 1 ELSE 0 END), 0) as pending_count,
        COALESCE(SUM(CASE WHEN t.status = 'approved' THEN 1 ELSE 0 END), 0) as approved_count,
        COALESCE(SUM(CASE WHEN t.status = 'cancelled' THEN 1 ELSE 0 END), 0) as cancelled_count
    FROM schedules s
    LEFT JOIN buses b ON s.bus_id = b.id
    LEFT JOIN tickets t ON s.id = t.schedule_id
    WHERE s.id = $scheduleId
    GROUP BY s.id
");

if ($result && $summary = $result->fetch_assoc()) {
    echo "<table style='border-collapse: collapse;'>";
    echo "<tr style='background: #f5f7fa;'><td style='border: 1px solid #ddd; padding: 10px; font-weight: bold;'>Field</td><td style='border: 1px solid #ddd; padding: 10px; font-weight: bold;'>Value</td></tr>";
    foreach ($summary as $key => $value) {
        echo "<tr>";
        echo "<td style='border: 1px solid #ddd; padding: 10px;'>" . htmlspecialchars($key) . "</td>";
        echo "<td style='border: 1px solid #ddd; padding: 10px;'>" . htmlspecialchars($value) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    if ($summary['total_tickets'] == 0) {
        echo "<p style='background: #f8d7da; padding: 15px; border-radius: 6px;'>";
        echo "❌ Query returned 0 total_tickets for Schedule #1!";
        echo "</p>";
    } else {
        echo "<p style='background: #d4edda; padding: 15px; border-radius: 6px;'>";
        echo "✅ Query returned " . $summary['total_tickets'] . " total_tickets for Schedule #1";
        echo "</p>";
    }
} else {
    echo "<p style='background: #f8d7da; padding: 15px; border-radius: 6px;'>";
    echo "❌ Query failed or returned no results for Schedule #1!";
    echo "</p>";
}

// Also test if Schedule #1 has tickets
echo "<h2>Checking Tickets for Schedule #1</h2>";
$ticketResult = $conn->query("SELECT id, user_id, status FROM tickets WHERE schedule_id = 1");
echo "<p>Total tickets on Schedule #1: <strong>" . ($ticketResult ? $ticketResult->num_rows : 0) . "</strong></p>";
if ($ticketResult) {
    while ($ticket = $ticketResult->fetch_assoc()) {
        echo "- Ticket #" . $ticket['id'] . " (User #" . $ticket['user_id'] . ", Status: " . $ticket['status'] . ")<br>";
    }
}
?>
