<?php
require_once APP_ROOT . '/config/db.php';
$error = $_SESSION['error'] ?? '';
$success = $_SESSION['success'] ?? '';
$testOtp = $_SESSION['test_otp'] ?? '';
unset($_SESSION['error'], $_SESSION['success']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Account - Book smarter, travel better</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
    <script src="<?php echo BASE_URL; ?>assets/js/script.js"></script>
</head>
<body>
<nav>
    <h2><span style="font-size: 2.5rem; display: inline-block;">🚌</span> Book Smarter, Travel Better</h2>
    <div>
        <a href="<?php echo BASE_URL; ?>">Home</a>
        <a href="<?php echo BASE_URL; ?>login">Login</a>
        <a href="<?php echo BASE_URL; ?>search">Search</a>
    </div>
</nav>

<section class="page-banner">
    <div class="page-banner-content">
        <h2>Verify Your Email</h2>
        <p>Enter the verification code sent to your email address to complete registration.</p>
    </div>
</section>

<div class="form-container">
    <a href="<?php echo BASE_URL; ?>" class="back-button">← Back to Home</a>
    <h2>Email Verification</h2>

    <?php if ($error): ?>
        <div class="error-message">
            <p><?php echo htmlspecialchars($error); ?></p>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="success-message">
            <p><?php echo htmlspecialchars($success); ?></p>
        </div>
    <?php endif; ?>

    <?php if (!empty($testOtp)): ?>
        <div class="success-message">
            <p><strong>Test Mode:</strong> Your OTP is: <strong><?php echo htmlspecialchars($testOtp); ?></strong></p>
        </div>
    <?php endif; ?>

    <form action="<?php echo BASE_URL; ?>public/auth_router.php" method="POST">
        <input type="hidden" name="action" value="verifyOTP">
        <div class="form-group">
            <label for="otp">Verification Code *</label>
            <input 
                type="text" 
                id="otp" 
                name="otp" 
                placeholder="Enter 6-digit code" 
                maxlength="6" 
                required
                inputmode="numeric">
        </div>

        <button type="submit" class="btn-primary">Verify Email</button>
    </form>

    <p class="small-text">
        Didn't receive the code? <a href="auth_router.php?action=resendOTP">Resend OTP</a><br>
        <a href="<?php echo BASE_URL; ?>register">Back to Register</a>
    </p>
</div>

<script>
    // Only allow numbers
    document.getElementById('otp').addEventListener('input', function(e) {
        e.target.value = e.target.value.replace(/[^0-9]/g, '');
    });
</script>

</body>
</html>
