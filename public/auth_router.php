<?php
require_once __DIR__ . '/../config/db.php';
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
include_once __DIR__ . '/../controllers/AuthController.php';

$action = $_GET['action'] ?? $_POST['action'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

// GET requests: Show form pages
if ($method === 'GET') {
    switch ($action) {
        case 'register':
            include_once __DIR__ . '/../views/register_form.php';
            exit;
        case 'login':
            include_once __DIR__ . '/../views/login_form.php';
            exit;
        case 'verifyOTP':
            if (!isset($_SESSION['temp_user_id'])) {
                $_SESSION['error'] = 'Please complete registration first.';
                header('Location: auth_router.php?action=register');
                exit;
            }
            include_once __DIR__ . '/../views/verify_otp_form.php';
            exit;
        case 'resendOTP':
            AuthController::resendOTP();
            exit;
        case 'forgotPassword':
            include_once __DIR__ . '/../views/forgot_password_form.php';
            exit;
        case 'resetPassword':
            $token = $_GET['token'] ?? '';
            if (empty($token)) {
                $_SESSION['error'] = 'Invalid reset token.';
                header('Location: auth_router.php?action=login');
                exit;
            }
            include_once __DIR__ . '/../views/reset_password_form.php';
            exit;
        default:
            header('Location: auth_router.php?action=login');
            exit;
    }
}

// POST requests: Process forms
if ($method === 'POST') {
    switch ($action) {
        case 'register':
            AuthController::register();
            exit;
        case 'verifyOTP':
            AuthController::verifyOTP();
            exit;
        case 'login':
            AuthController::login();
            exit;
        case 'forgotPassword':
            AuthController::forgotPassword();
            exit;
        case 'resetPassword':
            AuthController::resetPassword();
            exit;
        default:
            $_SESSION['error'] = 'Invalid action.';
            header('Location: auth_router.php?action=login');
            exit;
    }
}

// No GET or POST - redirect to login
header('Location: auth_router.php?action=login');
exit;
?>