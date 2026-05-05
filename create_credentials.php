<?php
require_once __DIR__ . '/config/db.php';

// Check if admin exists
$adminCheck = $conn->query("SELECT id FROM users WHERE email = 'admin123@gmail.com' LIMIT 1");
$adminExists = $adminCheck && $adminCheck->num_rows > 0;

// Check if operator exists  
$operatorCheck = $conn->query("SELECT id FROM users WHERE email = 'operator123@gmail.com' LIMIT 1");
$operatorExists = $operatorCheck && $operatorCheck->num_rows > 0;

// If both exist, just show them
if ($adminExists && $operatorExists) {
    echo "<div style='background: #d4edda; color: #155724; padding: 20px; border-radius: 8px; margin: 20px; border-left: 4px solid #28a745;'>";
    echo "<h2>✅ Credentials Already Exist</h2>";
    echo "<p><strong>Admin:</strong> admin123@gmail.com / Password@123</p>";
    echo "<p><strong>Operator:</strong> operator123@gmail.com / Password1@23</p>";
    echo "<p><a href='index.php'>← Go to Home</a></p>";
    echo "</div>";
    exit;
}

// Create admin if doesn't exist
if (!$adminExists) {
    $adminEmail = 'admin123@gmail.com';
    $adminPassword = password_hash('Password@123', PASSWORD_DEFAULT);
    $adminName = 'Admin User';
    $adminPhone = '03001111111';
    
    $insertAdmin = $conn->query("
        INSERT INTO users (name, email, phone, password_hash, is_verified, role, created_at)
        VALUES ('$adminName', '$adminEmail', '$adminPhone', '$adminPassword', 1, 'admin', NOW())
    ");
    
    if (!$insertAdmin) {
        die("Error creating admin: " . $conn->error);
    }
}

// Create operator if doesn't exist
if (!$operatorExists) {
    $operatorEmail = 'operator123@gmail.com';
    $operatorPassword = password_hash('Password1@23', PASSWORD_DEFAULT);
    $operatorName = 'Operator User';
    $operatorPhone = '03002222222';
    
    $insertOperator = $conn->query("
        INSERT INTO users (name, email, phone, password_hash, is_verified, role, created_at)
        VALUES ('$operatorName', '$operatorEmail', '$operatorPhone', '$operatorPassword', 1, 'operator', NOW())
    ");
    
    if (!$insertOperator) {
        die("Error creating operator: " . $conn->error);
    }
}

// Verify they exist now
$verify = $conn->query("SELECT email, role, is_verified FROM users WHERE role IN ('admin', 'operator') ORDER BY role DESC");

echo "<style>
body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
.container { max-width: 600px; margin: 0 auto; background: white; padding: 40px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
h1 { color: #0f1c33; margin-bottom: 30px; }
.success { background: #d4edda; color: #155724; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #28a745; }
table { width: 100%; margin-top: 20px; border-collapse: collapse; }
td { padding: 12px; border-bottom: 1px solid #ddd; text-align: left; }
td:first-child { font-weight: bold; color: #0072ff; }
a { color: #0072ff; text-decoration: none; font-weight: 600; }
a:hover { text-decoration: underline; }
</style>";

echo "<div class='container'>";
echo "<h1>✅ Credentials Created Successfully!</h1>";

echo "<div class='success'>";
echo "<h3>Login Credentials:</h3>";
echo "<table>";
echo "<tr><td>Admin Email:</td><td>admin123@gmail.com</td></tr>";
echo "<tr><td>Admin Password:</td><td>Password@123</td></tr>";
echo "<tr><td></td><td></td></tr>";
echo "<tr><td>Operator Email:</td><td>operator123@gmail.com</td></tr>";
echo "<tr><td>Operator Password:</td><td>Password1@23</td></tr>";
echo "</table>";
echo "</div>";

echo "<h3 style='margin-top: 30px; color: #0f1c33;'>Database Verification:</h3>";
echo "<table>";
echo "<tr style='background: #f5f5f5;'>";
echo "<td>Email</td>";
echo "<td>Role</td>";
echo "<td>Verified</td>";
echo "</tr>";

if ($verify && $verify->num_rows > 0) {
    while ($row = $verify->fetch_assoc()) {
        $verified = $row['is_verified'] ? '✅ Yes' : '❌ No';
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
        echo "<td>" . ucfirst($row['role']) . "</td>";
        echo "<td>$verified</td>";
        echo "</tr>";
    }
}
echo "</table>";

echo "<p style='margin-top: 30px; text-align: center;'>";
echo "<a href='public/auth_router.php?action=login'>Go to Login Page →</a>";
echo "</p>";
echo "</div>";
?>
