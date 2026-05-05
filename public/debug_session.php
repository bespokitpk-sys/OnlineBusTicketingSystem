<?php
require_once __DIR__ . '/../config/db.php';
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
?>
<!DOCTYPE html>
<html>
<head><title>Session Debug</title></head>
<body>
<h1>Session Debug</h1>
<pre>
<?php
echo "SESSION CONTENTS:\n";
print_r($_SESSION);
echo "\n\ntemp_user_id: " . ($_SESSION['temp_user_id'] ?? 'NOT SET') . "\n";
echo "temp_email: " . ($_SESSION['temp_email'] ?? 'NOT SET') . "\n";
echo "test_otp: " . ($_SESSION['test_otp'] ?? 'NOT SET') . "\n";
?>
</pre>
<p>
    <a href="auth_router.php?action=register">Go to Register</a><br>
    <a href="../index.php">Back to Home</a>
</p>
</body>
</html>
