<?php
session_start();
require_once __DIR__ . '/config/db.php';

// Set background color
$pageStyle = <<<HTML
<style>
body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f5f5; margin: 0; padding: 20px; }
.container { max-width: 900px; margin: 0 auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
h1, h2 { color: #0f1c33; border-bottom: 3px solid #0072ff; padding-bottom: 10px; }
table { width: 100%; border-collapse: collapse; margin: 20px 0; }
td { padding: 12px; border-bottom: 1px solid #ddd; }
td:first-child { font-weight: bold; color: #0072ff; width: 200px; }
.success { background: #d4edda; color: #155724; padding: 15px; border-radius: 6px; margin: 15px 0; border-left: 4px solid #28a745; }
.error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 6px; margin: 15px 0; border-left: 4px solid #f5c6cb; }
.info { background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 6px; margin: 15px 0; border-left: 4px solid #bee5eb; }
.code { background: #f5f5f5; padding: 10px; border-radius: 4px; font-family: 'Courier New', monospace; font-size: 12px; overflow-x: auto; }
a { color: #0072ff; text-decoration: none; font-weight: 600; }
a:hover { text-decoration: underline; }
button { background: #0072ff; color: white; border: none; padding: 12px 24px; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 14px; margin-top: 20px; }
button:hover { background: #0058cc; }
.credential { background: #f9f9f9; padding: 12px; border-radius: 6px; margin: 8px 0; border-left: 3px solid #0072ff; font-family: monospace; }
</style>
HTML;

echo $pageStyle;
echo '<div class="container">';
echo '<h1>🔍 Debug Login Information</h1>';

// Test database connection
echo '<h2>1. Database Connection</h2>';
if ($conn->connect_error) {
    echo '<div class="error">❌ Connection Failed: ' . $conn->connect_error . '</div>';
} else {
    echo '<div class="success">✅ Connected to: ' . $conn->server_info . '</div>';
    
    $dbSelect = $conn->select_db('bus_db');
    if (!$dbSelect) {
        echo '<div class="error">❌ Could not select database: ' . $conn->error . '</div>';
    } else {
        echo '<div class="success">✅ Selected Database: bus_db</div>';
    }
}

// Check users table structure
echo '<h2>2. Users Table Structure</h2>';
$checkTable = $conn->query("DESCRIBE users");
if (!$checkTable) {
    echo '<div class="error">❌ Users table not found: ' . $conn->error . '</div>';
} else {
    echo '<div class="success">✅ Users table exists</div>';
    echo '<table>';
    echo '<tr style="background: #f5f5f5;"><td>Field</td><td>Type</td><td>Null</td><td>Key</td></tr>';
    while ($col = $checkTable->fetch_assoc()) {
        echo '<tr>';
        echo '<td>' . $col['Field'] . '</td>';
        echo '<td>' . $col['Type'] . '</td>';
        echo '<td>' . ($col['Null'] === 'YES' ? 'Yes' : 'No') . '</td>';
        echo '<td>' . ($col['Key'] ?: '-') . '</td>';
        echo '</tr>';
    }
    echo '</table>';
}

// Count all users
echo '<h2>3. Users in Database</h2>';
$countResult = $conn->query("SELECT COUNT(*) as total FROM users");
$countRow = $countResult->fetch_assoc();
$totalUsers = $countRow['total'];
echo '<div class="info">Total users: <strong>' . $totalUsers . '</strong></div>';

// Check for admin user
echo '<h2>4. Check Admin Credentials</h2>';
$adminCheck = $conn->query("SELECT id, email, role, is_verified, password_hash FROM users WHERE email = 'admin123@gmail.com'");
if ($adminCheck->num_rows > 0) {
    echo '<div class="success">✅ Admin account FOUND</div>';
    $admin = $adminCheck->fetch_assoc();
    echo '<table>';
    echo '<tr><td>Email:</td><td>' . htmlspecialchars($admin['email']) . '</td></tr>';
    echo '<tr><td>Role:</td><td>' . $admin['role'] . '</td></tr>';
    echo '<tr><td>Verified:</td><td>' . ($admin['is_verified'] ? '✅ Yes' : '❌ No') . '</td></tr>';
    echo '<tr><td>ID:</td><td>' . $admin['id'] . '</td></tr>';
    echo '<tr><td>Password Hash (first 30 chars):</td><td><code>' . substr($admin['password_hash'], 0, 30) . '...</code></td></tr>';
    echo '</table>';
    
    // Test password verification
    echo '<h3>Password Verification Test:</h3>';
    $testPassword = 'Password@123';
    $isValid = password_verify($testPassword, $admin['password_hash']);
    echo '<div class="credential">Test Password: <strong>' . htmlspecialchars($testPassword) . '</strong></div>';
    echo ($isValid ? '<div class="success">✅ Password matches!</div>' : '<div class="error">❌ Password does NOT match!</div>');
    
} else {
    echo '<div class="error">❌ Admin account NOT FOUND - Email: admin123@gmail.com</div>';
}

// Check for operator user
echo '<h2>5. Check Operator Credentials</h2>';
$operatorCheck = $conn->query("SELECT id, email, role, is_verified, password_hash FROM users WHERE email = 'operator123@gmail.com'");
if ($operatorCheck->num_rows > 0) {
    echo '<div class="success">✅ Operator account FOUND</div>';
    $operator = $operatorCheck->fetch_assoc();
    echo '<table>';
    echo '<tr><td>Email:</td><td>' . htmlspecialchars($operator['email']) . '</td></tr>';
    echo '<tr><td>Role:</td><td>' . $operator['role'] . '</td></tr>';
    echo '<tr><td>Verified:</td><td>' . ($operator['is_verified'] ? '✅ Yes' : '❌ No') . '</td></tr>';
    echo '<tr><td>ID:</td><td>' . $operator['id'] . '</td></tr>';
    echo '<tr><td>Password Hash (first 30 chars):</td><td><code>' . substr($operator['password_hash'], 0, 30) . '...</code></td></tr>';
    echo '</table>';
    
    // Test password verification
    echo '<h3>Password Verification Test:</h3>';
    $testPassword = 'Password1@23';
    $isValid = password_verify($testPassword, $operator['password_hash']);
    echo '<div class="credential">Test Password: <strong>' . htmlspecialchars($testPassword) . '</strong></div>';
    echo ($isValid ? '<div class="success">✅ Password matches!</div>' : '<div class="error">❌ Password does NOT match!</div>');
    
} else {
    echo '<div class="error">❌ Operator account NOT FOUND - Email: operator123@gmail.com</div>';
}

// List all users with role 'admin' or 'operator'
echo '<h2>6. All Admin/Operator Users in Database</h2>';
$allAdminOp = $conn->query("SELECT id, email, role, is_verified FROM users WHERE role IN ('admin', 'operator') ORDER BY role DESC");
if ($allAdminOp->num_rows > 0) {
    echo '<table>';
    echo '<tr style="background: #f5f5f5;"><td>ID</td><td>Email</td><td>Role</td><td>Verified</td></tr>';
    while ($row = $allAdminOp->fetch_assoc()) {
        echo '<tr>';
        echo '<td>' . $row['id'] . '</td>';
        echo '<td>' . htmlspecialchars($row['email']) . '</td>';
        echo '<td><strong>' . ucfirst($row['role']) . '</strong></td>';
        echo '<td>' . ($row['is_verified'] ? '✅ Yes' : '❌ No') . '</td>';
        echo '</tr>';
    }
    echo '</table>';
} else {
    echo '<div class="error">⚠️ No admin or operator users found</div>';
}

// Action buttons
echo '<h2>7. Actions</h2>';
echo '<div style="margin: 20px 0;">';
echo '<p><strong>If credentials are NOT showing above:</strong></p>';
echo '<form action="create_credentials.php" style="display: inline;">';
echo '<button type="submit" onclick="return confirm(\'Create permanent credentials?\')">Create Admin & Operator Credentials</button>';
echo '</form>';
echo '</div>';

echo '<div style="margin: 20px 0;">';
echo '<p><strong>If credentials ARE showing and verified:</strong></p>';
echo '<form action="public/auth_router.php?action=login" style="display: inline; method="GET">';
echo '<button type="submit">Go to Login Page</button>';
echo '</form>';
echo '</div>';

echo '<p style="margin-top: 30px; font-size: 12px; color: #999;">';
echo '<a href="index.php">← Back to Home</a>';
echo '</p>';
echo '</div>';
?>
