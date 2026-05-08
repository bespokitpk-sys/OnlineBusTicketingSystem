<?php
require_once APP_ROOT . '/config/db.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    // Set secure session parameters
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_samesite', 'Lax');
    
    // Use secure cookies if on HTTPS
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        ini_set('session.cookie_secure', 1);
    }
    
    session_start();
}

function isLoggedIn() {
    return !empty($_SESSION['user_id']);
}

function currentUser() {
    return [
        'id' => $_SESSION['user_id'] ?? null,
        'name' => $_SESSION['user_name'] ?? null,
        'email' => $_SESSION['user_email'] ?? null,
        'role' => $_SESSION['user_role'] ?? null,
    ];
}

function requireLogin() {
    if (!isLoggedIn()) {
        $_SESSION['after_login'] = $_SERVER['REQUEST_URI'];
        header('Location: ' . BASE_URL . 'login');
        exit;
    }
}

function requireRole($role) {
    requireLogin();
    if (($_SESSION['user_role'] ?? null) !== $role) {
        header('Location: ' . BASE_URL . 'login');
        exit;
    }
}

function requireAnyRole(array $roles) {
    requireLogin();
    if (!in_array($_SESSION['user_role'] ?? '', $roles, true)) {
        header('Location: ' . BASE_URL . 'login');
        exit;
    }
}
