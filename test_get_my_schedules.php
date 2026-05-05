<?php
session_start();
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/controllers/OperatorController.php';

echo "<h2>Debug: getMySchedules() for Operator ID 12</h2>";

// Get schedules like the dashboard does
$schedules = OperatorController::getMySchedules(12);

echo "<p>Total schedules returned: <strong>" . count($schedules) . "</strong></p>";

if (count($schedules) > 0) {
    // Show the last 3 schedules (should include Schedule #1)
    $lastSchedules = array_slice($schedules, -3);
    
    echo "<h3>Last 3 Schedules (should include May 01):</h3>";
    echo "<table style='border-collapse: collapse; width: 100%;'>";
    echo "<tr style='background: #f5f7fa;'>";
    echo "<td style='border: 1px solid #ddd; padding: 10px; font-weight: bold;'>ID</td>";
    echo "<td style='border: 1px solid #ddd; padding: 10px; font-weight: bold;'>Route</td>";
    echo "<td style='border: 1px solid #ddd; padding: 10px; font-weight: bold;'>Departure</td>";
    echo "<td style='border: 1px solid #ddd; padding: 10px; font-weight: bold;'>Bus</td>";
    echo "<td style='border: 1px solid #ddd; padding: 10px; font-weight: bold;'>total_bookings</td>";
    echo "</tr>";
    
    foreach ($lastSchedules as $schedule) {
        echo "<tr>";
        echo "<td style='border: 1px solid #ddd; padding: 10px;'>" . $schedule['id'] . "</td>";
        echo "<td style='border: 1px solid #ddd; padding: 10px;'>" . $schedule['source'] . " → " . $schedule['destination'] . "</td>";
        echo "<td style='border: 1px solid #ddd; padding: 10px;'>" . date('M d, H:i', strtotime($schedule['departure_time'])) . "</td>";
        echo "<td style='border: 1px solid #ddd; padding: 10px;'>" . $schedule['bus_name'] . "</td>";
        echo "<td style='border: 1px solid #ddd; padding: 10px;'>" . ($schedule['total_bookings'] ?? 'NULL') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Now test getTripSummary() for each of these schedules
    echo "<h3>getTripSummary() Results for Last 3:</h3>";
    echo "<table style='border-collapse: collapse; width: 100%;'>";
    echo "<tr style='background: #f5f7fa;'>";
    echo "<td style='border: 1px solid #ddd; padding: 10px; font-weight: bold;'>Schedule ID</td>";
    echo "<td style='border: 1px solid #ddd; padding: 10px; font-weight: bold;'>total_tickets</td>";
    echo "<td style='border: 1px solid #ddd; padding: 10px; font-weight: bold;'>pending_count</td>";
    echo "<td style='border: 1px solid #ddd; padding: 10px; font-weight: bold;'>approved_count</td>";
    echo "</tr>";
    
    foreach ($lastSchedules as $schedule) {
        $summary = OperatorController::getTripSummary($schedule['id']);
        echo "<tr>";
        echo "<td style='border: 1px solid #ddd; padding: 10px;'>" . $schedule['id'] . "</td>";
        echo "<td style='border: 1px solid #ddd; padding: 10px;'>" . ($summary['total_tickets'] ?? 'NULL') . "</td>";
        echo "<td style='border: 1px solid #ddd; padding: 10px;'>" . ($summary['pending_count'] ?? 'NULL') . "</td>";
        echo "<td style='border: 1px solid #ddd; padding: 10px;'>" . ($summary['approved_count'] ?? 'NULL') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}
?>
