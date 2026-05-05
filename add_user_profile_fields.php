<?php
/**
 * Migration Script: Add CNIC and Profile Picture to Users Table
 * Run this file once to update the database schema
 */

require_once __DIR__ . '/config/db.php';

echo "<!DOCTYPE html><html><head><title>Database Migration</title>";
echo "<style>body{font-family:Arial,sans-serif;padding:40px;max-width:800px;margin:0 auto;}";
echo "h2{color:#0072ff;}p{margin:10px 0;padding:10px;border-radius:5px;}";
echo ".success{background:#d4edda;color:#155724;border:1px solid #c3e6cb;}";
echo ".error{background:#f8d7da;color:#721c24;border:1px solid #f5c6cb;}";
echo ".info{background:#d1ecf1;color:#0c5460;border:1px solid #bee5eb;}";
echo "a{color:#0072ff;text-decoration:none;font-weight:bold;}</style></head><body>";

echo "<h2>Adding CNIC and Profile Picture fields to Users table...</h2>";

// Check if CNIC column exists
$checkCnic = $conn->query("SHOW COLUMNS FROM users LIKE 'cnic'");
if ($checkCnic->num_rows == 0) {
    // Add CNIC column
    $sql1 = "ALTER TABLE users ADD COLUMN cnic VARCHAR(15) NULL AFTER phone";
    if ($conn->query($sql1)) {
        echo "<p class='success'>✅ CNIC column added successfully</p>";
    } else {
        echo "<p class='error'>❌ Error adding CNIC column: " . $conn->error . "</p>";
    }
} else {
    echo "<p class='info'>ℹ️ CNIC column already exists</p>";
}

// Check if profile_picture column exists
$checkProfile = $conn->query("SHOW COLUMNS FROM users LIKE 'profile_picture'");
if ($checkProfile->num_rows == 0) {
    // Add profile_picture column
    $sql2 = "ALTER TABLE users ADD COLUMN profile_picture VARCHAR(255) NULL AFTER cnic";
    if ($conn->query($sql2)) {
        echo "<p class='success'>✅ Profile Picture column added successfully</p>";
    } else {
        echo "<p class='error'>❌ Error adding Profile Picture column: " . $conn->error . "</p>";
    }
} else {
    echo "<p class='info'>ℹ️ Profile Picture column already exists</p>";
}

echo "<h3 style='color:#2e7d32;'>Migration completed!</h3>";
echo "<p><a href='index.php'>← Back to Home</a> | <a href='public/auth_router.php?action=register'>Go to Registration</a></p>";
echo "</body></html>";

$conn->close();
?>
