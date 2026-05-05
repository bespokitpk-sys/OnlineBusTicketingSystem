<?php
require_once __DIR__ . '/config/db.php';

echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    .container { max-width: 700px; margin: 0 auto; }
    .success { background: #d4edda; color: #155724; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #28a745; }
    .error { background: #f8d7da; color: #721c24; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #dc3545; }
    .info { background: #d1ecf1; color: #0c5460; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #17a2b8; }
    table { width: 100%; border-collapse: collapse; margin: 20px 0; }
    td { padding: 12px; border: 1px solid #ddd; }
    td:first-child { font-weight: bold; color: #0072ff; width: 40%; }
    a { color: #0072ff; text-decoration: none; }
    a:hover { text-decoration: underline; }
</style>";

echo "<div class='container'>";
echo "<h1>🔐 System Setup - Create Permanent Credentials</h1>";

// Admin credentials
$adminEmail = 'admin123@gmail.com';
$adminPassword = password_hash('Password@123', PASSWORD_DEFAULT);
$adminName = 'Admin User';
$adminPhone = '03001111111';

// Operator credentials
$operatorEmail = 'operator123@gmail.com';
$operatorPassword = password_hash('Password1@23', PASSWORD_DEFAULT);
$operatorName = 'Operator User';
$operatorPhone = '03002222222';

$errors = [];
$success = [];

// Check if admin already exists
$checkAdmin = $conn->query("SELECT id, email FROM users WHERE email = '$adminEmail'");
if ($checkAdmin && $checkAdmin->num_rows > 0) {
    echo "<div class='info'>";
    echo "<h3>ℹ️ Admin Account Already Exists</h3>";
    echo "<p>Email: <strong>$adminEmail</strong> is already in the system.</p>";
    echo "</div>";
} else {
    // Create admin account
    $resultAdmin = $conn->query("
        INSERT INTO users (name, email, phone, password_hash, is_verified, role, created_at)
        VALUES ('$adminName', '$adminEmail', '$adminPhone', '$adminPassword', 1, 'admin', NOW())
    ");
    
    if ($resultAdmin) {
        $success[] = "✅ Admin account created successfully!";
    } else {
        $errors[] = "❌ Error creating admin account: " . $conn->error;
    }
}

// Check if operator already exists
$checkOperator = $conn->query("SELECT id, email FROM users WHERE email = '$operatorEmail'");
if ($checkOperator && $checkOperator->num_rows > 0) {
    echo "<div class='info'>";
    echo "<h3>ℹ️ Operator Account Already Exists</h3>";
    echo "<p>Email: <strong>$operatorEmail</strong> is already in the system.</p>";
    echo "</div>";
} else {
    // Create operator account
    $resultOperator = $conn->query("
        INSERT INTO users (name, email, phone, password_hash, is_verified, role, created_at)
        VALUES ('$operatorName', '$operatorEmail', '$operatorPhone', '$operatorPassword', 1, 'operator', NOW())
    ");
    
    if ($resultOperator) {
        $success[] = "✅ Operator account created successfully!";
    } else {
        $errors[] = "❌ Error creating operator account: " . $conn->error;
    }
}

// Display errors
if (count($errors) > 0) {
    echo "<div class='error'>";
    echo "<h3>⚠️ Issues Encountered:</h3>";
    foreach ($errors as $err) {
        echo "<p>$err</p>";
    }
    echo "</div>";
}

// Display success
if (count($success) > 0) {
    echo "<div class='success'>";
    echo "<h3>✅ Setup Complete!</h3>";
    foreach ($success as $msg) {
        echo "<p>$msg</p>";
    }
    echo "</div>";
}

// Display permanent credentials
echo "<div class='info'>";
echo "<h3>📋 Permanent Login Credentials</h3>";
echo "<table>";
echo "<tr><td>Admin Email:</td><td><strong>admin123@gmail.com</strong></td></tr>";
echo "<tr><td>Admin Password:</td><td><strong>Password@123</strong></td></tr>";
echo "<tr><td></td><td></td></tr>";
echo "<tr><td>Operator Email:</td><td><strong>operator123@gmail.com</strong></td></tr>";
echo "<tr><td>Operator Password:</td><td><strong>Password1@23</strong></td></tr>";
echo "</table>";
echo "</div>";

// Verify accounts in database
echo "<div style='margin-top: 30px;'>";
echo "<h3>🔍 Verification</h3>";
$verify = $conn->query("SELECT name, email, role, is_verified FROM users WHERE role IN ('admin', 'operator') ORDER BY role DESC");
if ($verify && $verify->num_rows > 0) {
    echo "<table>";
    echo "<tr><td><strong>Name</strong></td><td><strong>Email</strong></td><td><strong>Role</strong></td><td><strong>Verified</strong></td></tr>";
    while ($row = $verify->fetch_assoc()) {
        $verified = $row['is_verified'] ? '✅ Yes' : '❌ No';
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
        echo "<td>" . ucfirst($row['role']) . "</td>";
        echo "<td>$verified</td>";
        echo "</tr>";
    }
    echo "</table>";
}
echo "</div>";

// Next steps
echo "<div style='background: #f0f4f8; padding: 20px; border-radius: 8px; margin-top: 30px; border-left: 4px solid #0072ff;'>";
echo "<h3>📍 Next Steps:</h3>";
echo "<ol>";
echo "<li><a href='index.php'>Go to Home Page</a></li>";
echo "<li>Click <strong>'Admin Login'</strong> to login as admin</li>";
echo "<li>Use credentials: <strong>admin123@gmail.com / Password@123</strong></li>";
echo "<li>Or click <strong>'Operator Login'</strong> to login as operator</li>";
echo "<li>Use credentials: <strong>operator123@gmail.com / Password1@23</strong></li>";
echo "</ol>";
echo "</div>";

echo "</div>";
?>
