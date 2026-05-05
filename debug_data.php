<?php
require_once __DIR__ . '/config/db.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

echo "<h2>Debug Data Check</h2>";

// Check schedules
$schedules = $conn->query("SELECT id, bus_id, operator_id, source, destination FROM schedules LIMIT 5");
echo "<h3>Schedules:</h3>";
if ($schedules && $schedules->num_rows > 0) {
    while ($s = $schedules->fetch_assoc()) {
        echo "ID: {$s['id']}, Bus: {$s['bus_id']}, Operator: {$s['operator_id']}, Route: {$s['source']} → {$s['destination']}<br>";
    }
} else {
    echo "No schedules found<br>";
}

// Check tickets
echo "<h3>Tickets:</h3>";
$tickets = $conn->query("SELECT t.id, t.schedule_id, t.user_id, t.seats, t.status, u.name FROM tickets t LEFT JOIN users u ON t.user_id = u.id LIMIT 10");
if ($tickets && $tickets->num_rows > 0) {
    while ($t = $tickets->fetch_assoc()) {
        echo "ID: {$t['id']}, Schedule: {$t['schedule_id']}, User: {$t['user_id']} ({$t['name']}), Seats: {$t['seats']}, Status: {$t['status']}<br>";
    }
} else {
    echo "No tickets found<br>";
}

// Check first schedule stats
echo "<h3>First Schedule Stats:</h3>";
$result = $conn->query("
    SELECT 
        COUNT(DISTINCT t.id) as total_tickets,
        SUM(CASE WHEN t.status = 'pending' THEN 1 ELSE 0 END) as pending_count,
        SUM(CASE WHEN t.status = 'approved' THEN 1 ELSE 0 END) as approved_count,
        SUM(CASE WHEN t.status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_count
    FROM schedules s
    LEFT JOIN tickets t ON s.id = t.schedule_id
    WHERE s.id = 1
");

if ($result && $result->num_rows > 0) {
    $stats = $result->fetch_assoc();
    echo "Total: {$stats['total_tickets']}, Pending: {$stats['pending_count']}, Approved: {$stats['approved_count']}, Cancelled: {$stats['cancelled_count']}";
} else {
    echo "No stats found";
}

$conn->close();
?>
