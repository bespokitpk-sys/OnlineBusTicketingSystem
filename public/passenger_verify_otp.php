<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/User.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$otp_error = '';
$otp_success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $otp = trim($_POST['otp'] ?? '');
    $user_id = $_SESSION['temp_user_id'] ?? null;
    
    if (!$user_id || empty($otp)) {
        $otp_error = 'Invalid request.';
    } elseif (User::verifyOTP($user_id, $otp)) {
        $user = User::findById($user_id);
        $_SESSION['user_id'] = $user_id;
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];
        unset($_SESSION['temp_user_id'], $_SESSION['temp_email'], $_SESSION['test_otp']);
        $otp_success = true;
    } else {
        $otp_error = 'Invalid or expired OTP.';
    }
}

// If OTP verified, redirect to passenger dashboard
if ($otp_success) {
    header('Location: ' . BASE_URL . 'passenger/dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email - Bus Ticketing System</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f7fa; }
        
        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: linear-gradient(90deg, #e8f4f8 0%, #d4e9f7 100%);
            color: #0f1c33;
            padding: 20px 40px;
        }
        
        nav h2 { font-size: 1.6rem; font-weight: 700; }
        nav div { display: flex; gap: 10px; }
        nav a { color: #0f1c33; text-decoration: none; font-weight: 600; padding: 10px 16px; border-radius: 6px; background: rgba(0, 114, 255, 0.1); }
        
        .form-container { max-width: 500px; margin: 40px auto; background: white; padding: 40px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); }
        .form-container h2 { margin-bottom: 30px; color: #0f1c33; }
        
        .error-message { background: #fee; color: #c33; padding: 12px; border-radius: 6px; margin-bottom: 20px; border: 1px solid #fcc; }
        .success-message { background: #efe; color: #3c3; padding: 12px; border-radius: 6px; margin-bottom: 20px; border: 1px solid #cfc; }
        
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; color: #0f1c33; font-weight: 600; }
        .form-group input { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px; }
        
        .btn-primary { width: 100%; padding: 12px; background: linear-gradient(90deg, #0072ff 0%, #0056cc 100%); color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; }
        
        .small-text { text-align: center; margin-top: 20px; color: #666; }
        .small-text a { color: #0072ff; text-decoration: none; }
    </style>
</head>
<body>
<nav>
    <h2><span style="font-size: 2.5rem;">🚌</span> Book Smarter, Travel Better</h2>
    <div>
        <a href="<?php echo BASE_URL; ?>index.php">Home</a>
        <a href="<?php echo BASE_URL; ?>public/passenger_login.php">Login</a>
    </div>
</nav>

<div class="form-container">
    <h2>Email Verification</h2>
    
    <?php if ($otp_error): ?>
        <div class="error-message">
            <p><?php echo htmlspecialchars($otp_error); ?></p>
        </div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label for="otp">Enter OTP (6 digits) *</label>
            <input type="text" id="otp" name="otp" maxlength="6" inputmode="numeric" required>
        </div>
        <button type="submit" class="btn-primary">Verify Email</button>
    </form>

    <p class="small-text">
        <a href="<?php echo BASE_URL; ?>public/passenger_register.php">Back to Register</a>
    </p>
</div>

</body>
</html>
