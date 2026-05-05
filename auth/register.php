<?php
// Legacy registration endpoint - forward to public auth_router for consistency
require_once __DIR__ . '/../config/db.php';
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Forward POST registration requests to the public auth_router
    $_GET['action'] = 'register';
    include_once __DIR__ . '/../public/auth_router.php';
} else {
    // Forward GET requests to show proper registration form
    header('Location: ' . BASE_URL . 'public/auth_router.php?action=register');
    exit;
}
?>
