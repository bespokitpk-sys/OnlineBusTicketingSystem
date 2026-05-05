<?php
require_once __DIR__ . '/../config/db.php';
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$userId = $_SESSION['temp_user_id'] ?? null;

if (!$userId) {
    echo "No temp_user_id in session";
    exit;
}

// Query the database to see the OTP info
$query = "SELECT id, email, otp_code, otp_expiry, DATE_ADD(NOW(), INTERVAL 0 SECOND) as server_time FROM users WHERE id = $userId";
$result = $conn->query($query);

if ($result) {
    $user = $result->fetch_assoc();
    echo "<h1>OTP Debug Info</h1>";
    echo "<pre>";
    echo "User ID: " . $user['id'] . "\n";
    echo "Email: " . $user['email'] . "\n";
    echo "OTP Code: " . $user['otp_code'] . "\n";
    echo "OTP Expiry: " . $user['otp_expiry'] . "\n";
    echo "Server Time: " . $user['server_time'] . "\n";
    echo "Is Expired?: " . (strtotime($user['otp_expiry']) > strtotime($user['server_time']) ? "NO" : "YES") . "\n";
    echo "</pre>";
    
    // Check raw comparison
    echo "<h2>Raw Comparison:</h2>";
    $expiry_time = strtotime($user['otp_expiry']);
    $now_time = time();
    echo "Expiry Timestamp: $expiry_time<br>";
    echo "Current Timestamp: $now_time<br>";
    echo "Difference (seconds): " . ($expiry_time - $now_time) . "<br>";
} else {
    echo "User not found";
}
?>
