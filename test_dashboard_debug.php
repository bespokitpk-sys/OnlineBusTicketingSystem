<?php
session_start();
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/controllers/OperatorController.php';

echo "<h2>All Schedules on Operator Dashboard</h2>";

// Get schedules like the dashboard does
$schedules = OperatorController::getMySchedules(12);

echo "<p>Total schedules: " . count($schedules) . "</p>";

echo "<table style='border-collapse: collapse; width: 100%; margin-top: 20px;'>";
echo "<tr style='background: #f5f7fa;'>";
echo "<td style='border: 1px solid #ddd; padding: 10px; font-weight: bold;'>Schedule ID</td>";
echo "<td style='border: 1px solid #ddd; padding: 10px; font-weight: bold;'>Route</td>";
echo "<td style='border: 1px solid #ddd; padding: 10px; font-weight: bold;'>Departure</td>";
echo "<td style='border: 1px solid #ddd; padding: 10px; font-weight: bold;'>Bus</td>";
echo "<td style='border: 1px solid #ddd; padding: 10px; font-weight: bold;'>total_bookings (from getMySchedules)</td>";
echo "<td style='border: 1px solid #ddd; padding: 10px; font-weight: bold;'>total_tickets (from getTripSummary)</td>";
echo "<td style='border: 1px solid #ddd; padding: 10px; font-weight: bold;'>pending_count (from getTripSummary)</td>";
echo "</tr>";

foreach ($schedules as $schedule) {
    $summary = OperatorController::getTripSummary($schedule['id']);
    
    $bgColor = '';
    if ($summary['total_tickets'] == 4 && $schedule['source'] === 'Karachi' && $schedule['destination'] === 'Lahore') {
        $bgColor = ' style="background: #d4edda;"'; // Highlight the target
    }
    
    echo "<tr$bgColor>";
    echo "<td style='border: 1px solid #ddd; padding: 10px;'>" . $schedule['id'] . "</td>";
    echo "<td style='border: 1px solid #ddd; padding: 10px;'>" . $schedule['source'] . " → " . $schedule['destination'] . "</td>";
    echo "<td style='border: 1px solid #ddd; padding: 10px;'>" . date('M d, H:i', strtotime($schedule['departure_time'])) . "</td>";
    echo "<td style='border: 1px solid #ddd; padding: 10px;'>" . $schedule['bus_name'] . "</td>";
    echo "<td style='border: 1px solid #ddd; padding: 10px;'>" . ($schedule['total_bookings'] ?? 'NULL') . "</td>";
    echo "<td style='border: 1px solid #ddd; padding: 10px;'>" . ($summary['total_tickets'] ?? 'NULL') . "</td>";
    echo "<td style='border: 1px solid #ddd; padding: 10px;'>" . ($summary['pending_count'] ?? 'NULL') . "</td>";
    echo "</tr>";
}
echo "</table>";

echo "<h3 style='margin-top: 20px; color: #d4edda;'>🟢 The highlighted row is Schedule #1 with 4 bookings and 1 pending payment</h3>";
?>
