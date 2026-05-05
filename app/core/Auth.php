<?php
require_once APP_ROOT . '/config/db.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
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
