<?php
session_start();
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/models/Ticket.php';

echo "<h1>Download Ticket Debug</h1>";

echo "<h2>Current Session</h2>";
echo "<p><strong>User ID:</strong> " . ($_SESSION['user_id'] ?? 'Not set') . "</p>";
echo "<p><strong>User Name:</strong> " . ($_SESSION['user_name'] ?? 'Not set') . "</p>";
echo "<p><strong>User Role:</strong> " . ($_SESSION['user_role'] ?? 'Not set') . "</p>";

echo "<h2>Ticket #7 Info</h2>";
$ticket = Ticket::findById(7);
if ($ticket) {
    echo "<table style='border-collapse: collapse;'>";
    echo "<tr style='background: #f5f7fa;'>";
    echo "<td style='border: 1px solid #ddd; padding: 10px; font-weight: bold;'>Field</td>";
    echo "<td style='border: 1px solid #ddd; padding: 10px; font-weight: bold;'>Value</td>";
    echo "</tr>";
    foreach ($ticket as $key => $value) {
        echo "<tr>";
        echo "<td style='border: 1px solid #ddd; padding: 10px;'>" . $key . "</td>";
        echo "<td style='border: 1px solid #ddd; padding: 10px;'>" . htmlspecialchars($value) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    if ($_SESSION['user_id'] == $ticket['user_id']) {
        echo "<p style='background: #d4edda; padding: 10px; border-radius: 5px; margin-top: 20px;'>✅ Current user owns this ticket</p>";
    } else {
        echo "<p style='background: #f8d7da; padding: 10px; border-radius: 5px; margin-top: 20px;'>❌ Current user does NOT own this ticket</p>";
        echo "<p><strong>Current user ID:</strong> " . $_SESSION['user_id'] . "</p>";
        echo "<p><strong>Ticket owner ID:</strong> " . $ticket['user_id'] . "</p>";
    }
} else {
    echo "<p style='background: #f8d7da; padding: 10px; border-radius: 5px;'>❌ Ticket not found</p>";
}

echo "<h2>Download Link Test</h2>";
$downloadUrl = "http://localhost/BusTicketingSystem/public/download_ticket.php?ticket_id=7&format=pdf";
echo "<p><a href='" . $downloadUrl . "' style='display: inline-block; padding: 12px 24px; background: #0072ff; color: white; text-decoration: none; border-radius: 6px; font-weight: bold;'>📥 Test Download PDF</a></p>";
?>
