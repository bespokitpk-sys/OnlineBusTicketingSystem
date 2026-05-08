<?php
/**
 * Quick Error Diagnostic Tool
 * Upload this to your cPanel root and access it directly
 * 
 * ⚠️ DELETE after diagnosing!
 */

// Enable all error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

echo "<h1>🔍 Error Diagnostic Tool</h1>";
echo "<style>body{font-family:Arial;max-width:800px;margin:40px auto;padding:20px;background:#f5f5f5;} .panel{background:white;padding:20px;margin:15px 0;border-radius:8px;box-shadow:0 2px 4px rgba(0,0,0,0.1);} .success{color:green;} .error{color:red;} .warning{color:orange;} code{background:#f3f4f6;padding:2px 6px;border-radius:3px;}</style>";

echo "<div class='panel'>";
echo "<h2>1. PHP Version</h2>";
echo "<p class='success'>✅ PHP " . phpversion() . "</p>";
echo "</div>";

echo "<div class='panel'>";
echo "<h2>2. File Structure Check</h2>";
$requiredFiles = [
    'config/db.php',
    'public/index.php',
    'app/core/Auth.php',
    'app/models/User.php'
];

$allExist = true;
foreach ($requiredFiles as $file) {
    $exists = file_exists(__DIR__ . '/' . $file);
    $allExist = $allExist && $exists;
    $status = $exists ? "✅" : "❌";
    $class = $exists ? "success" : "error";
    echo "<p class='$class'>$status $file</p>";
}
echo "</div>";

echo "<div class='panel'>";
echo "<h2>3. Database Connection Test</h2>";
try {
    // Test with current config/db.php
    if (file_exists(__DIR__ . '/config/db.php')) {
        echo "<p>Loading config/db.php...</p>";
        require_once __DIR__ . '/config/db.php';
        
        if (isset($conn) && $conn instanceof mysqli) {
            if ($conn->connect_error) {
                echo "<p class='error'>❌ Connection Failed: " . htmlspecialchars($conn->connect_error) . "</p>";
                echo "<div class='warning' style='background:#fff3cd;padding:10px;margin:10px 0;border-left:4px solid #ffc107;'>";
                echo "<strong>⚠️ Database credentials are incorrect!</strong><br>";
                echo "Please update <code>config/db.php</code> with your cPanel database details:<br>";
                echo "- Host: usually 'localhost'<br>";
                echo "- User: your_cpanel_username_dbuser<br>";
                echo "- Password: your database password<br>";
                echo "- Database: your_cpanel_username_bus_db<br>";
                echo "</div>";
            } else {
                echo "<p class='success'>✅ Database Connected Successfully!</p>";
                echo "<p>Database: <code>" . htmlspecialchars($dbName ?? 'Unknown') . "</code></p>";
            }
        } else {
            echo "<p class='error'>❌ Database connection object not created</p>";
        }
    } else {
        echo "<p class='error'>❌ config/db.php file not found!</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>❌ Exception: " . htmlspecialchars($e->getMessage()) . "</p>";
}
echo "</div>";

echo "<div class='panel'>";
echo "<h2>4. Document Root & Paths</h2>";
echo "<table style='width:100%;border-collapse:collapse;'>";
echo "<tr><td style='padding:8px;border-bottom:1px solid #ddd;font-weight:bold;'>Document Root:</td><td style='padding:8px;border-bottom:1px solid #ddd;'><code>" . ($_SERVER['DOCUMENT_ROOT'] ?? 'Unknown') . "</code></td></tr>";
echo "<tr><td style='padding:8px;border-bottom:1px solid #ddd;font-weight:bold;'>Current Directory:</td><td style='padding:8px;border-bottom:1px solid #ddd;'><code>" . __DIR__ . "</code></td></tr>";
echo "<tr><td style='padding:8px;border-bottom:1px solid #ddd;font-weight:bold;'>Script Filename:</td><td style='padding:8px;border-bottom:1px solid #ddd;'><code>" . ($_SERVER['SCRIPT_FILENAME'] ?? 'Unknown') . "</code></td></tr>";
echo "</table>";

$expectedDocRoot = __DIR__ . '/public';
$actualDocRoot = $_SERVER['DOCUMENT_ROOT'] ?? '';
if ($actualDocRoot !== $expectedDocRoot && $actualDocRoot !== __DIR__) {
    echo "<div class='warning' style='background:#fff3cd;padding:10px;margin:10px 0;border-left:4px solid #ffc107;'>";
    echo "<strong>⚠️ Document Root Mismatch!</strong><br>";
    echo "Your cPanel Document Root should point to: <code>$expectedDocRoot</code><br>";
    echo "Current setting: <code>$actualDocRoot</code><br>";
    echo "<strong>Fix:</strong> In cPanel → Domains → Manage → Update Document Root to 'public' folder";
    echo "</div>";
}
echo "</div>";

echo "<div class='panel'>";
echo "<h2>5. .htaccess Files</h2>";
$htaccessFiles = [
    '.htaccess' => __DIR__ . '/.htaccess',
    'public/.htaccess' => __DIR__ . '/public/.htaccess'
];

foreach ($htaccessFiles as $name => $path) {
    if (file_exists($path)) {
        echo "<p class='success'>✅ $name exists</p>";
    } else {
        echo "<p class='error'>❌ $name missing</p>";
    }
}
echo "</div>";

echo "<div class='panel'>";
echo "<h2>6. Test Front Controller</h2>";
$indexPath = __DIR__ . '/public/index.php';
if (file_exists($indexPath)) {
    echo "<p class='success'>✅ public/index.php exists</p>";
    echo "<p>Try accessing: <a href='public/index.php'>public/index.php directly</a></p>";
} else {
    echo "<p class='error'>❌ public/index.php not found!</p>";
}
echo "</div>";

echo "<div class='panel' style='background:#ffebee;border-left:4px solid #f44336;'>";
echo "<h2 style='color:#c62828;'>⚠️ IMPORTANT: Delete This File!</h2>";
echo "<p>Once you've diagnosed the issue, <strong>DELETE error_check.php</strong> from your server for security!</p>";
echo "</div>";

echo "<div class='panel'>";
echo "<h2>📋 Common Solutions</h2>";
echo "<ol style='line-height:1.8;'>";
echo "<li><strong>Database Connection Failed?</strong> Update credentials in <code>config/db.php</code></li>";
echo "<li><strong>Document Root Wrong?</strong> Point to <code>public</code> folder in cPanel</li>";
echo "<li><strong>500 Error Persists?</strong> Check cPanel Error Logs (Metrics → Errors)</li>";
echo "<li><strong>OPcache Issue?</strong> Run <code>clear_cache.php</code> to clear PHP cache</li>";
echo "<li><strong>File Permissions?</strong> Ensure files are 644 and folders are 755</li>";
echo "</ol>";
echo "</div>";
?>
