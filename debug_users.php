<?php
require_once __DIR__ . '/config/db.php';

echo "<h2>Check User Accounts</h2>";

// Check if Fatima Malik exists
$result = $conn->query("SELECT id, email, name, role, is_verified, password_hash FROM users WHERE email = 'fatima.malik@gmail.com' LIMIT 1");

if ($result && $result->num_rows > 0) {
    $user = $result->fetch_assoc();
    echo "<h3>Fatima Malik Account:</h3>";
    echo "<table style='border-collapse: collapse;'>";
    echo "<tr style='background: #f5f7fa;'>";
    echo "<td style='border: 1px solid #ddd; padding: 10px; font-weight: bold;'>Field</td>";
    echo "<td style='border: 1px solid #ddd; padding: 10px; font-weight: bold;'>Value</td>";
    echo "</tr>";
    
    foreach ($user as $key => $value) {
        echo "<tr>";
        echo "<td style='border: 1px solid #ddd; padding: 10px;'>" . htmlspecialchars($key) . "</td>";
        if ($key === 'password_hash') {
            echo "<td style='border: 1px solid #ddd; padding: 10px;'>" . (strlen($value) > 20 ? substr($value, 0, 20) . '...' : $value) . "</td>";
        } else {
            echo "<td style='border: 1px solid #ddd; padding: 10px;'>" . htmlspecialchars($value) . "</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
    
    // Test password verification
    echo "<h3>Password Test:</h3>";
    $testPassword = "Fatima@2024";
    if (password_verify($testPassword, $user['password_hash'])) {
        echo "<p style='background: #d4edda; padding: 10px; border-radius: 5px;'>✅ Password verification: CORRECT</p>";
    } else {
        echo "<p style='background: #f8d7da; padding: 10px; border-radius: 5px;'>❌ Password verification: INCORRECT</p>";
    }
} else {
    echo "<p style='background: #f8d7da; padding: 10px; border-radius: 5px;'>❌ User not found</p>";
}

// Check all passengers
echo "<h3>All Passengers:</h3>";
$result = $conn->query("SELECT id, email, name, role, is_verified FROM users WHERE role = 'passenger' ORDER BY id DESC LIMIT 10");

if ($result) {
    echo "<p>Total passengers: " . $result->num_rows . "</p>";
    echo "<table style='border-collapse: collapse;'>";
    echo "<tr style='background: #f5f7fa;'>";
    echo "<td style='border: 1px solid #ddd; padding: 10px; font-weight: bold;'>ID</td>";
    echo "<td style='border: 1px solid #ddd; padding: 10px; font-weight: bold;'>Email</td>";
    echo "<td style='border: 1px solid #ddd; padding: 10px; font-weight: bold;'>Name</td>";
    echo "<td style='border: 1px solid #ddd; padding: 10px; font-weight: bold;'>Verified</td>";
    echo "</tr>";
    
    while ($user = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td style='border: 1px solid #ddd; padding: 10px;'>" . $user['id'] . "</td>";
        echo "<td style='border: 1px solid #ddd; padding: 10px;'>" . htmlspecialchars($user['email']) . "</td>";
        echo "<td style='border: 1px solid #ddd; padding: 10px;'>" . htmlspecialchars($user['name']) . "</td>";
        echo "<td style='border: 1px solid #ddd; padding: 10px;'>" . ($user['is_verified'] ? '✅ Yes' : '❌ No') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

?>
