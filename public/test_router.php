<?php
require_once __DIR__ . '/../config/db.php';
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
?>
<!DOCTYPE html>
<html>
<head><title>Router Test</title></head>
<body>
<h1>Bus Ticketing System - Router Test</h1>
<p>✅ Router is working!</p>
<p>📁 Base URL: <?php echo BASE_URL; ?></p>
<p>📁 Current Directory: <?php echo __DIR__; ?></p>
<p>🔗 <a href="<?php echo BASE_URL; ?>public/auth_router.php?action=register">Test Register Link</a></p>
<p>🔗 <a href="<?php echo BASE_URL; ?>public/auth_router.php?action=login">Test Login Link</a></p>
<p>🔗 <a href="<?php echo BASE_URL; ?>index.php">Back to Home</a></p>
</body>
</html>
