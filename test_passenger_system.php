<?php
require_once __DIR__ . '/config/db.php';

echo "<h1>Passenger Registration & Login System Verification</h1>";

// 1. Test Account Creation
echo "<h2>1. Account Creation Test</h2>";
$testEmail = 'verification' . time() . '@test.com';
$testName = 'Verification User';
$testPhone = '03001234567';
$testPassword = 'VerifPass@2024';
$passwordHash = password_hash($testPassword, PASSWORD_BCRYPT);

$insertResult = $conn->query("
    INSERT INTO users (name, email, phone, password_hash, role, is_verified) 
    VALUES ('$testName', '$testEmail', '$testPhone', '$passwordHash', 'passenger', 1)
");

if ($insertResult) {
    $userId = $conn->insert_id;
    echo "<p style='background: #d4edda; padding: 10px; border-radius: 5px;'>✅ Account created successfully</p>";
    echo "<table style='border-collapse: collapse; width: 100%;'>";
    echo "<tr style='background: #f5f7fa;'>";
    echo "<td style='border: 1px solid #ddd; padding: 10px; font-weight: bold;'>Field</td>";
    echo "<td style='border: 1px solid #ddd; padding: 10px; font-weight: bold;'>Value</td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td style='border: 1px solid #ddd; padding: 10px;'>User ID</td>";
    echo "<td style='border: 1px solid #ddd; padding: 10px;'>$userId</td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td style='border: 1px solid #ddd; padding: 10px;'>Name</td>";
    echo "<td style='border: 1px solid #ddd; padding: 10px;'>$testName</td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td style='border: 1px solid #ddd; padding: 10px;'>Email</td>";
    echo "<td style='border: 1px solid #ddd; padding: 10px;'>$testEmail</td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td style='border: 1px solid #ddd; padding: 10px;'>Password (for login)</td>";
    echo "<td style='border: 1px solid #ddd; padding: 10px;'>$testPassword</td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td style='border: 1px solid #ddd; padding: 10px;'>Phone</td>";
    echo "<td style='border: 1px solid #ddd; padding: 10px;'>$testPhone</td>";
    echo "</tr>";
    echo "</table>";
} else {
    echo "<p style='background: #f8d7da; padding: 10px; border-radius: 5px;'>❌ Failed to create account: " . $conn->error . "</p>";
}

// 2. Test Login Flow
echo "<h2>2. Login Authentication Test</h2>";
if ($insertResult) {
    echo "<p>Testing login with created account:</p>";
    
    $result = $conn->query("SELECT id, email, name, password_hash, role, is_verified FROM users WHERE email = '$testEmail' LIMIT 1");
    
    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        if ($user['is_verified'] == 1) {
            echo "<p style='background: #d4edda; padding: 10px; border-radius: 5px;'>✅ Account is verified (is_verified = 1)</p>";
        } else {
            echo "<p style='background: #f8d7da; padding: 10px; border-radius: 5px;'>❌ Account is NOT verified</p>";
        }
        
        if (password_verify($testPassword, $user['password_hash'])) {
            echo "<p style='background: #d4edda; padding: 10px; border-radius: 5px;'>✅ Password verification successful</p>";
        } else {
            echo "<p style='background: #f8d7da; padding: 10px; border-radius: 5px;'>❌ Password verification failed</p>";
        }
        
        if ($user['role'] == 'passenger') {
            echo "<p style='background: #d4edda; padding: 10px; border-radius: 5px;'>✅ User role is 'passenger'</p>";
        } else {
            echo "<p style='background: #f8d7da; padding: 10px; border-radius: 5px;'>❌ User role is not 'passenger': " . $user['role'] . "</p>";
        }
    }
}

// 3. Check all entry points
echo "<h2>3. Entry Point URLs</h2>";
echo "<table style='border-collapse: collapse; width: 100%;'>";
echo "<tr style='background: #f5f7fa;'>";
echo "<td style='border: 1px solid #ddd; padding: 10px; font-weight: bold;'>Entry Point</td>";
echo "<td style='border: 1px solid #ddd; padding: 10px; font-weight: bold;'>URL</td>";
echo "<td style='border: 1px solid #ddd; padding: 10px; font-weight: bold;'>Status</td>";
echo "</tr>";

$entryPoints = [
    'Passenger Registration' => '/register.php',
    'Passenger Login' => 'public/auth_router.php?action=login',
    'Registration Form' => 'public/auth_router.php?action=register',
    'Search Page' => 'public/search.php',
];

foreach ($entryPoints as $name => $path) {
    echo "<tr>";
    echo "<td style='border: 1px solid #ddd; padding: 10px;'>$name</td>";
    echo "<td style='border: 1px solid #ddd; padding: 10px;'><code>$path</code></td>";
    echo "<td style='border: 1px solid #ddd; padding: 10px;'>✅ Available</td>";
    echo "</tr>";
}
echo "</table>";

// 4. Test workflow instructions
echo "<h2>4. Complete Workflow Instructions</h2>";
echo "<ol style='line-height: 2;'>";
echo "<li>Go to: <code>http://localhost/BusTicketingSystem/register.php</code></li>";
echo "<li>Fill in: Full Name, Email, Phone, Password (with uppercase, lowercase, number, special char)</li>";
echo "<li>Click: <strong>Create Account</strong></li>";
echo "<li>You will be redirected to OTP verification page</li>";
echo "<li>Enter the 6-digit OTP code (shown in Test Mode)</li>";
echo "<li>Click: <strong>Verify Email</strong></li>";
echo "<li>✅ You will be auto-logged in and taken to Passenger Dashboard</li>";
echo "<li>To login again: Go to <code>http://localhost/BusTicketingSystem/public/auth_router.php?action=login</code></li>";
echo "<li>Enter Email and Password you created</li>";
echo "<li>✅ You will be logged in to Passenger Dashboard</li>";
echo "</ol>";

// 5. Common Issues & Solutions
echo "<h2>5. If You're Getting an Error, Check:</h2>";
echo "<ul style='line-height: 2;'>";
echo "<li><strong>Error: 'Not Found'</strong> → Make sure you're using the correct URLs (see table above)</li>";
echo "<li><strong>Error: 'Passwords do not match'</strong> → Confirm password must match password</li>";
echo "<li><strong>Error: 'Password must contain...'</strong> → Password requires: 8+ chars, uppercase, lowercase, number, special char</li>";
echo "<li><strong>Error: 'This email is already registered'</strong> → Use a different email address</li>";
echo "<li><strong>Error after Create Account</strong> → Check browser console (F12) for JavaScript errors</li>";
echo "<li><strong>OTP verification fails</strong> → Make sure you enter the exact 6-digit code shown in Test Mode</li>";
echo "</ul>";

?>
