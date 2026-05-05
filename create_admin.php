<?php
require_once __DIR__ . '/config/db.php';

// Check if admin already exists
$checkAdmin = $conn->query("SELECT id FROM users WHERE role = 'admin' LIMIT 1");

if ($checkAdmin && $checkAdmin->num_rows > 0) {
    echo "<div style='background: #d4edda; color: #155724; padding: 20px; border-radius: 8px; margin: 20px; border-left: 4px solid #28a745;'>";
    echo "<h2>✅ Admin Already Exists</h2>";
    echo "<p>An admin account is already in the system.</p>";
    echo "<p><strong>Email:</strong> admin@example.com</p>";
    echo "<p><strong>Password:</strong> admin123</p>";
    echo "<p><a href='index.php' style='color: #0072ff; text-decoration: none;'>← Back to Home</a></p>";
    echo "</div>";
} else {
    // Create admin account
    $adminName = 'Admin User';
    $adminEmail = 'admin@example.com';
    $adminPhone = '03001234567';
    $adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
    
    $result = $conn->query("
        INSERT INTO users (name, email, phone, password_hash, is_verified, role, created_at)
        VALUES ('$adminName', '$adminEmail', '$adminPhone', '$adminPassword', 1, 'admin', NOW())
    ");
    
    if ($result) {
        echo "<div style='background: #d4edda; color: #155724; padding: 20px; border-radius: 8px; margin: 20px; border-left: 4px solid #28a745;'>";
        echo "<h2>✅ Admin Account Created Successfully!</h2>";
        echo "<p><strong>Email:</strong> admin@example.com</p>";
        echo "<p><strong>Password:</strong> admin123</p>";
        echo "<p><strong>Role:</strong> Admin</p>";
        echo "<p style='margin-top: 20px; color: #0f1c33;'><strong>Next Steps:</strong></p>";
        echo "<ol>";
        echo "<li>Go to <a href='public/login.php' style='color: #0072ff; text-decoration: none;'>Login Page</a></li>";
        echo "<li>Enter admin email/password</li>";
        echo "<li>Login and create operators</li>";
        echo "</ol>";
        echo "<p><a href='index.php' style='color: #0072ff; text-decoration: none;'>← Back to Home</a></p>";
        echo "</div>";
    } else {
        echo "<div style='background: #f8d7da; color: #721c24; padding: 20px; border-radius: 8px; margin: 20px; border-left: 4px solid #dc3545;'>";
        echo "<h2>❌ Error Creating Admin</h2>";
        echo "<p>" . htmlspecialchars($conn->error) . "</p>";
        echo "<p><a href='index.php' style='color: #0072ff; text-decoration: none;'>← Back to Home</a></p>";
        echo "</div>";
    }
}
?>
