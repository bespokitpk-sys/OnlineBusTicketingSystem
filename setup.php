<?php
/**
 * Bus Ticketing System - One-Time Setup Script
 * 
 * This script creates default admin and operator accounts
 * and verifies database connection.
 * 
 * ⚠️ IMPORTANT: Delete this file after running setup for security!
 * 
 * Default Credentials Created:
 * - Admin: admin@busticketing.com / Admin@123
 * - Operator: operator@busticketing.com / Operator@123
 * 
 * Note: Passengers register themselves via the registration page.
 */

// Prevent running if already set up
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (file_exists(__DIR__ . '/.setup_complete')) {
    die('<h1>Setup Already Completed</h1><p>This script has already been run. Delete .setup_complete file to run again.</p><p><strong>Security Warning:</strong> Please delete setup.php file!</p>');
}

if (!defined('APP_ROOT')) { define('APP_ROOT', __DIR__); }
if (!isset($conn)) { require_once APP_ROOT . '/config/db.php'; }

// Check if database connection is successful
if ($conn->connect_error) {
    die('<h1>Database Connection Failed</h1><p>Error: ' . $conn->connect_error . '</p><p>Please check your database credentials in <code>config/db.php</code></p>');
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bus Ticketing System - Setup</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .setup-container {
            background: white;
            max-width: 700px;
            width: 100%;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }
        .setup-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .setup-header h1 {
            font-size: 2rem;
            margin-bottom: 10px;
        }
        .setup-header p {
            opacity: 0.9;
            font-size: 1rem;
        }
        .setup-content {
            padding: 40px;
        }
        .status-box {
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .status-box.success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        .status-box.error {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        .status-box.info {
            background: #d1ecf1;
            border: 1px solid #bee5eb;
            color: #0c5460;
        }
        .status-box.warning {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
        }
        .icon {
            font-size: 2rem;
        }
        .credentials {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #667eea;
        }
        .credential-item {
            margin: 15px 0;
            padding: 10px;
            background: white;
            border-radius: 6px;
        }
        .credential-item h3 {
            color: #667eea;
            font-size: 1.1rem;
            margin-bottom: 8px;
        }
        .credential-item p {
            margin: 5px 0;
            font-family: 'Courier New', monospace;
            color: #333;
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            font-size: 1rem;
            margin-top: 10px;
        }
        .btn:hover {
            background: #5568d3;
        }
        .btn-danger {
            background: #dc3545;
        }
        .btn-danger:hover {
            background: #c82333;
        }
        .warning-box {
            background: #fff3cd;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #ffc107;
            margin: 20px 0;
        }
        .warning-box h3 {
            color: #856404;
            margin-bottom: 10px;
        }
        .warning-box ul {
            margin-left: 20px;
            color: #856404;
        }
        code {
            background: #f4f4f4;
            padding: 2px 6px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
        }
        .checklist {
            list-style: none;
            margin: 20px 0;
        }
        .checklist li {
            padding: 10px;
            margin: 8px 0;
            background: #f8f9fa;
            border-radius: 6px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .checklist li:before {
            content: '✓';
            display: inline-block;
            width: 24px;
            height: 24px;
            background: #28a745;
            color: white;
            border-radius: 50%;
            text-align: center;
            line-height: 24px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="setup-container">
        <div class="setup-header">
            <h1>🚌 Bus Ticketing System</h1>
            <p>One-Time Setup & Installation</p>
        </div>
        
        <div class="setup-content">
            <?php
            $setupSuccess = false;
            $errors = [];
            $accounts = [];

            // Verify database tables exist
            $requiredTables = ['users', 'buses', 'schedules', 'tickets'];
            foreach ($requiredTables as $table) {
                $result = $conn->query("SHOW TABLES LIKE '$table'");
                if (!$result || $result->num_rows === 0) {
                    $errors[] = "Table '$table' not found. Please import database.sql first.";
                }
            }

            if (empty($errors)) {
                // Check if admin already exists
                $adminCheck = $conn->query("SELECT id FROM users WHERE role = 'admin' LIMIT 1");
                
                if ($adminCheck && $adminCheck->num_rows > 0) {
                    echo '<div class="status-box warning">';
                    echo '<span class="icon">⚠️</span>';
                    echo '<div><strong>Setup Previously Completed</strong><br>Admin account already exists in database.</div>';
                    echo '</div>';
                } else {
                    // Create default accounts (Admin and Operator only - Passengers self-register)
                    $defaultAccounts = [
                        [
                            'name' => 'System Administrator',
                            'email' => 'admin@busticketing.com',
                            'phone' => '03001234567',
                            'password' => 'Admin@123',
                            'role' => 'admin'
                        ],
                        [
                            'name' => 'Test Operator',
                            'email' => 'operator@busticketing.com',
                            'phone' => '03001234568',
                            'password' => 'Operator@123',
                            'role' => 'operator'
                        ]
                    ];

                    foreach ($defaultAccounts as $account) {
                        // Check if account exists
                        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
                        $stmt->bind_param("s", $account['email']);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        
                        if ($result->num_rows === 0) {
                            // Create account
                            $passwordHash = password_hash($account['password'], PASSWORD_DEFAULT);
                            $stmt = $conn->prepare("INSERT INTO users (name, email, phone, password_hash, role, is_verified, created_at) VALUES (?, ?, ?, ?, ?, 1, NOW())");
                            $stmt->bind_param("sssss", $account['name'], $account['email'], $account['phone'], $passwordHash, $account['role']);
                            
                            if ($stmt->execute()) {
                                $accounts[] = $account;
                            } else {
                                $errors[] = "Failed to create {$account['role']} account: " . $stmt->error;
                            }
                            $stmt->close();
                        }
                    }

                    if (empty($errors) && !empty($accounts)) {
                        $setupSuccess = true;
                        
                        // Create .setup_complete marker file
                        file_put_contents(__DIR__ . '/.setup_complete', date('Y-m-d H:i:s'));
                    }
                }
            }

            // Display results
            if (!empty($errors)) {
                echo '<div class="status-box error">';
                echo '<span class="icon">❌</span>';
                echo '<div><strong>Setup Failed</strong><br>';
                foreach ($errors as $error) {
                    echo '• ' . htmlspecialchars($error) . '<br>';
                }
                echo '</div></div>';

                echo '<div class="warning-box">';
                echo '<h3>Troubleshooting Steps:</h3>';
                echo '<ol>';
                echo '<li>Ensure MySQL is running</li>';
                echo '<li>Import <code>database.sql</code> via phpMyAdmin</li>';
                echo '<li>Verify credentials in <code>config/db.php</code></li>';
                echo '<li>Refresh this page after fixing issues</li>';
                echo '</ol>';
                echo '</div>';
            } elseif ($setupSuccess) {
                echo '<div class="status-box success">';
                echo '<span class="icon">✅</span>';
                echo '<div><strong>Setup Completed Successfully!</strong><br>Default accounts have been created.</div>';
                echo '</div>';

                echo '<div class="credentials">';
                echo '<h2 style="margin-bottom: 20px; color: #667eea;">🔑 Default Login Credentials</h2>';
                
                foreach ($accounts as $account) {
                    echo '<div class="credential-item">';
                    echo '<h3>' . ucfirst($account['role']) . ' Account</h3>';
                    echo '<p><strong>Email:</strong> ' . htmlspecialchars($account['email']) . '</p>';
                    echo '<p><strong>Password:</strong> ' . htmlspecialchars($account['password']) . '</p>';
                    echo '</div>';
                }
                echo '</div>';

                echo '<div class="warning-box">';
                echo '<h3>⚠️ Important Security Steps:</h3>';
                echo '<ul class="checklist">';
                echo '<li>Login and change all default passwords immediately</li>';
                echo '<li>Delete this <code>setup.php</code> file from your server</li>';
                echo '<li>Delete <code>diagnose.php</code> and <code>clear_cache.php</code> in production</li>';
                echo '<li>Enable HTTPS for production deployment</li>';
                echo '<li>Configure email settings in AuthController.php</li>';
                echo '</ul>';
                echo '</div>';

                echo '<div style="text-align: center; margin-top: 30px;">';
                echo '<a href="' . BASE_URL . 'login" class="btn">Go to Login Page</a>';
                echo '</div>';

            } else {
                echo '<div class="status-box info">';
                echo '<span class="icon">ℹ️</span>';
                echo '<div><strong>Database Connected</strong><br>All required tables exist. Admin account already configured.</div>';
                echo '</div>';

                echo '<div style="text-align: center; margin-top: 30px;">';
                echo '<a href="' . BASE_URL . 'login" class="btn">Go to Login Page</a>';
                echo '</div>';
            }
            ?>

            <div style="margin-top: 30px; padding-top: 20px; border-top: 2px solid #eee; text-align: center; color: #666;">
                <p><strong>Need Help?</strong> Check <code>SETUP.md</code> for detailed instructions</p>
                <p style="margin-top: 10px; font-size: 0.9rem;">Database: <?php echo $dbName; ?> | Host: <?php echo $dbHost; ?></p>
            </div>
        </div>
    </div>
</body>
</html>
