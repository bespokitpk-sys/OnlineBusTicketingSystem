<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../controllers/AuthController.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch($action) {
        case 'login':
            AuthController::login();
            break;
        case 'register':
            AuthController::register();
            break;
        case 'verifyOTP':
            AuthController::verifyOTP();
            break;
        case 'forgotPassword':
            AuthController::forgotPassword();
            break;
        case 'resetPassword':
            AuthController::resetPassword();
            break;
        default:
            header('Location: auth_router.php?action=login');
            exit;
    }
} else {
    header('Location: auth_router.php?action=login');
    exit;
}
?>
