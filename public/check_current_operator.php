<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';

if (isLoggedIn()) {
    $user = currentUser();
    echo "<h2>👤 Current Logged-In User</h2>";
    echo "<table style='border-collapse: collapse;'>";
    echo "<tr><td style='border: 1px solid #ddd; padding: 10px;'>User ID:</td><td style='border: 1px solid #ddd; padding: 10px;'><strong>" . $user['id'] . "</strong></td></tr>";
    echo "<tr><td style='border: 1px solid #ddd; padding: 10px;'>Name:</td><td style='border: 1px solid #ddd; padding: 10px;'>" . htmlspecialchars($user['name']) . "</td></tr>";
    echo "<tr><td style='border: 1px solid #ddd; padding: 10px;'>Email:</td><td style='border: 1px solid #ddd; padding: 10px;'>" . htmlspecialchars($user['email']) . "</td></tr>";
    echo "<tr><td style='border: 1px solid #ddd; padding: 10px;'>Role:</td><td style='border: 1px solid #ddd; padding: 10px;'><strong>" . $user['role'] . "</strong></td></tr>";
    echo "</table>";
    
    if ($user['role'] === 'operator') {
        // Get schedules for this operator
        $result = $conn->query("SELECT id, source, destination FROM schedules WHERE operator_id = {$user['id']}");
        echo "<h2>📅 Schedules for This Operator</h2>";
        echo "<ul>";
        if ($result) {
            while ($schedule = $result->fetch_assoc()) {
                echo "<li>Schedule #{$schedule['id']}: {$schedule['source']} → {$schedule['destination']}</li>";
            }
        }
        echo "</ul>";
    }
} else {
    echo "<p>Not logged in</p>";
}
?>
