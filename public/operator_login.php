<?php
require_once __DIR__ . '/../config/db.php';

header('Location: ' . BASE_URL . 'public/auth_router.php?action=login');
exit;
