<?php
include_once __DIR__ . '/../config/db.php';
include_once __DIR__ . '/../models/User.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $_SESSION['error'] = 'Please enter both email and password.';
        header('Location: ' . BASE_URL . 'public/login.php');
        exit;
    }

    $user = User::authenticate($email, $password);
    if (!$user) {
        $_SESSION['error'] = 'Invalid login credentials.';
        header('Location: ' . BASE_URL . 'public/login.php');
        exit;
    }

    $_SESSION['user_id'] = intval($user['id']);
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_role'] = $user['role'];

    $redirect = $_POST['redirect'] ?? $_SESSION['after_login'] ?? '';
    unset($_SESSION['after_login']);
    if ($redirect !== '') {
        $allowedPrefixes = ['public/', 'passenger/', 'operator/', 'admin/'];
        $redirectPath = ltrim(trim($redirect), '/');
        foreach ($allowedPrefixes as $prefix) {
            if (strpos($redirectPath, $prefix) === 0) {
                header('Location: ' . BASE_URL . $redirectPath);
                exit;
            }
        }
    }

    if ($user['role'] === 'admin') {
        header('Location: ' . BASE_URL . 'admin/dashboard.php');
        exit;
    }
    if ($user['role'] === 'operator') {
        header('Location: ' . BASE_URL . 'operator/dashboard.php');
        exit;
    }

    header('Location: ' . BASE_URL . 'passenger/dashboard.php');
    exit;
}
header('Location: ' . BASE_URL . 'public/login.php');
exit;
