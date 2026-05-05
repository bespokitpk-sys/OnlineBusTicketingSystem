<?php
require_once APP_ROOT . '/config/db.php';
$token = $_GET['token'] ?? '';
if (empty($token)) {
    header('Location: ../public/auth_router.php?action=login');
    exit;
}

$error = $_SESSION['error'] ?? '';
unset($_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Book smarter, travel better</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
    <script src="<?php echo BASE_URL; ?>assets/js/script.js"></script>
</head>
<body>
<nav>
    <h2><span style="font-size: 2.5rem; display: inline-block;">🚌</span> Book Smarter, Travel Better</h2>
    <div>
        <a href="<?php echo BASE_URL; ?>">Home</a>
        <a href="<?php echo BASE_URL; ?>login">Login</a>
        <a href="<?php echo BASE_URL; ?>register">Register</a>
    </div>
</nav>
</nav>

<section class="page-banner">
    <div class="page-banner-content">
        <h2>Reset Your Password</h2>
        <p>Enter a new password for your account.</p>
    </div>
</section>

<div class="form-container">
    <a href="<?php echo BASE_URL; ?>login" class="back-button">← Back to Login</a>
    <h2>Create New Password</h2>

    <?php if ($error): ?>
        <div class="error-message">
            <p><?php echo htmlspecialchars($error); ?></p>
        </div>
    <?php endif; ?>

    <form action="<?php echo BASE_URL; ?>public/auth_router.php" method="POST">
        <input type="hidden" name="action" value="resetPassword">
        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
        <div class="form-group">
            <label for="password">New Password *</label>
            <div style="position: relative; display: flex; align-items: center;">
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    placeholder="Enter new password" 
                    required
                    style="flex: 1; padding-right: 45px;">
                <button 
                    type="button" 
                    id="togglePassword" 
                    style="position: absolute; right: 12px; background: none; border: none; cursor: pointer; color: #666; font-size: 18px; padding: 0;"
                    onclick="togglePasswordVisibility('password', 'togglePassword')">
                    👁️
                </button>
            </div>
            <small>Minimum 8 characters with uppercase, lowercase, numbers, and special characters.</small>
        </div>

        <div class="form-group">
            <label for="confirm_password">Confirm New Password *</label>
            <div style="position: relative; display: flex; align-items: center;">
                <input 
                    type="password" 
                    id="confirm_password" 
                    name="confirm_password" 
                    placeholder="Confirm new password" 
                    required
                    style="flex: 1; padding-right: 45px;">
                <button 
                    type="button" 
                    id="toggleConfirmPassword" 
                    style="position: absolute; right: 12px; background: none; border: none; cursor: pointer; color: #666; font-size: 18px; padding: 0;"
                    onclick="togglePasswordVisibility('confirm_password', 'toggleConfirmPassword')">
                    👁️
                </button>
            </div>
        </div>

        <button type="submit" class="btn-primary">Reset Password</button>
    </form>

    <p class="small-text">
        <a href="<?php echo BASE_URL; ?>login">Back to Login</a>
    </p>
</div>

<script src="<?php echo BASE_URL; ?>assets/js/script.js"></script>
<script>
    document.querySelector('form').addEventListener('submit', function(e) {
        const password = document.getElementById('password').value;
        const confirm = document.getElementById('confirm_password').value;
        
        if (password !== confirm) {
            e.preventDefault();
            showToast('Passwords do not match.', 'error');
            return false;
        }

        const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
        if (!passwordRegex.test(password)) {
            e.preventDefault();
            showToast('Password must be at least 8 characters and contain uppercase, lowercase, numbers, and special characters.', 'error');
            return false;
        }
    });

    // Toggle password visibility
    function togglePasswordVisibility(inputId, buttonId) {
        const input = document.getElementById(inputId);
        const button = document.getElementById(buttonId);
        
        if (input.type === 'password') {
            input.type = 'text';
            button.innerHTML = '🙈'; // Hide icon
        } else {
            input.type = 'password';
            button.innerHTML = '👁️'; // Show icon
        }
    }
</script>

</body>
</html>
