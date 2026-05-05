<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../controllers/AuthController.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Forward to AuthController
    AuthController::login();
} else {
    // If not POST, redirect to login form
    header('Location: auth_router.php?action=login');
    exit;
}
?>
