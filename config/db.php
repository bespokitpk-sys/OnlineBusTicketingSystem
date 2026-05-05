<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// APP_ROOT is defined by public/index.php before this file is included.
// Fallback for CLI / direct setup scripts.
if (!defined('APP_ROOT')) {
    define('APP_ROOT', realpath(__DIR__ . '/..'));
}

// BASE_URL: auto-detected so it works on local Laragon and cPanel alike.
if (!defined('BASE_URL')) {
    $protocol  = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
    $host      = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $scriptDir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/index.php')), '/');
    define('BASE_URL', $protocol . $host . ($scriptDir === '' ? '/' : $scriptDir . '/'));
}

// ── Database credentials ─────────────────────────────────────────────────────
// For cPanel: update these with values from cPanel → MySQL Databases
$dbHost = 'localhost';
$dbUser = 'root';      // cPanel: your_cpanel_username_dbuser
$dbPass = '';          // cPanel: your database password
$dbName = 'bus_db';    // cPanel: your_cpanel_username_bus_db

$conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}
$conn->set_charset('utf8mb4');
?>
