<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../controllers/AuthController.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Forward to AuthController
    AuthController::forgotPassword();
} else {
    // If not POST, redirect to forgot password form
    header('Location: auth_router.php?action=forgotPassword');
    exit;
}
?>
