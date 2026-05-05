<?php
// Handle registration requests from root URL
require_once __DIR__ . '/config/db.php';
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Forward POST data to auth_router for processing
    $_GET['action'] = 'register';
    include_once __DIR__ . '/public/auth_router.php';
} else {
    // For GET requests, show the registration form
    header('Location: ' . BASE_URL . 'public/auth_router.php?action=register');
    exit;
}
?>
