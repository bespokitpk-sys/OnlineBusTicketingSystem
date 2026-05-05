<?php
/**
 * Utility Script: Assign Operator to All Schedules
 * This script assigns all schedules without operators to the first available operator
 */

require_once __DIR__ . '/config/db.php';

// Get the first operator (usually ID 12 or the operator user)
$operatorResult = $conn->query("SELECT id, name, email FROM users WHERE role='operator' LIMIT 1");
$operator = $operatorResult ? $operatorResult->fetch_assoc() : null;

if (!$operator) {
    echo "<div style='color: red; font-size: 16px; padding: 20px;'>❌ No operators found in the system!</div>";
    exit;
}

// Get count of unassigned schedules
$unassignedResult = $conn->query("SELECT COUNT(*) as count FROM schedules WHERE operator_id IS NULL");
$unassignedData = $unassignedResult->fetch_assoc();
$unassignedCount = $unassignedData['count'];

// Assign all unassigned schedules to the operator
if ($unassignedCount > 0) {
    $conn->query("UPDATE schedules SET operator_id = {$operator['id']} WHERE operator_id IS NULL");
    
    // Verify the update
    $verifyResult = $conn->query("SELECT COUNT(*) as count FROM schedules WHERE operator_id = {$operator['id']}");
    $verifyData = $verifyResult->fetch_assoc();
    $totalAssigned = $verifyData['count'];
    
    echo "<div style='background: linear-gradient(135deg, #0072ff 0%, #0056cc 100%); color: white; padding: 30px; border-radius: 12px; margin: 20px; font-size: 16px;'>";
    echo "<h2 style='margin-bottom: 15px;'>✅ Operator Assignment Successful!</h2>";
    echo "<p><strong>Operator:</strong> {$operator['name']} ({$operator['email']})</p>";
    echo "<p><strong>Schedules Assigned:</strong> {$unassignedCount} unassigned schedules</p>";
    echo "<p><strong>Total Schedules for this Operator:</strong> {$totalAssigned}</p>";
    echo "</div>";
} else {
    echo "<div style='background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); color: white; padding: 30px; border-radius: 12px; margin: 20px; font-size: 16px;'>";
    echo "<h2 style='margin-bottom: 15px;'>ℹ️ All schedules are already assigned!</h2>";
    echo "<p><strong>Operator:</strong> {$operator['name']}</p>";
    echo "<p>No unassigned schedules found.</p>";
    echo "</div>";
}

// Show all schedules for this operator
$schedulesResult = $conn->query("
    SELECT s.id, s.source, s.destination, s.departure_time, b.bus_name, u.name as operator_name
    FROM schedules s
    LEFT JOIN buses b ON s.bus_id = b.id
    LEFT JOIN users u ON s.operator_id = u.id
    WHERE s.operator_id = {$operator['id']}
    ORDER BY s.id DESC
");

echo "<div style='background: white; padding: 30px; border-radius: 12px; margin: 20px;'>";
echo "<h3 style='color: #0f1c33; margin-bottom: 15px;'>📅 Schedules for {$operator['name']}:</h3>";
echo "<table style='width: 100%; border-collapse: collapse;'>";
echo "<thead><tr style='background: #f5f7fa;'>";
echo "<th style='padding: 12px; text-align: left; border-bottom: 2px solid #ddd;'>ID</th>";
echo "<th style='padding: 12px; text-align: left; border-bottom: 2px solid #ddd;'>Bus</th>";
echo "<th style='padding: 12px; text-align: left; border-bottom: 2px solid #ddd;'>Route</th>";
echo "<th style='padding: 12px; text-align: left; border-bottom: 2px solid #ddd;'>Departure</th>";
echo "</tr></thead>";
echo "<tbody>";

if ($schedulesResult) {
    while ($schedule = $schedulesResult->fetch_assoc()) {
        echo "<tr style='border-bottom: 1px solid #eee;'>";
        echo "<td style='padding: 12px;'>#" . $schedule['id'] . "</td>";
        echo "<td style='padding: 12px;'>" . htmlspecialchars($schedule['bus_name']) . "</td>";
        echo "<td style='padding: 12px;'>" . htmlspecialchars($schedule['source']) . " → " . htmlspecialchars($schedule['destination']) . "</td>";
        echo "<td style='padding: 12px;'>" . date('M d, Y H:i', strtotime($schedule['departure_time'])) . "</td>";
        echo "</tr>";
    }
}

echo "</tbody>";
echo "</table>";
echo "</div>";

echo "<div style='background: #fff3cd; color: #856404; padding: 20px; border-radius: 12px; margin: 20px; border-left: 4px solid #ffc107;'>";
echo "<strong>ℹ️ Note:</strong> This script assigns all unassigned schedules to the first operator in the system. The operator can now see all these schedules on their dashboard.";
echo "</div>";

echo "<div style='text-align: center; margin: 30px;'>";
echo "<a href='admin/dashboard.php' style='background: #0072ff; color: white; padding: 12px 25px; border-radius: 6px; text-decoration: none; display: inline-block;'>← Back to Admin Dashboard</a>";
echo "</div>";
?>
