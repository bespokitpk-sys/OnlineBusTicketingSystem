<?php
require_once __DIR__ . '/../config/db.php';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Book smarter, travel better</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <script src="../assets/js/script.js"></script>
</head>
<body>
<nav>
    <h2><span style="font-size: 2.5rem; display: inline-block;">🚌</span> Book Smarter, Travel Better</h2>
    <div>
        <a href="../index.php">Home</a>
        <a href="auth_router.php?action=login">Login</a>
        <a href="search.php">Search</a>
    </div>
</nav>

<section class="page-banner">
    <div class="page-banner-content">
        <h2>Create Your Account</h2>
        <p>Join our professional bus ticketing platform. Register to book tickets securely, receive official receipts, and enjoy seamless travel.</p>
    </div>
</section>

<div class="form-container">
    <a href="../index.php" class="back-button">← Back to Home</a>
    <h2>Passenger Registration</h2>

    <?php if ($error): ?>
        <div class="error-message">
            <p><?php echo htmlspecialchars($error); ?></p>
        </div>
    <?php endif; ?>

    <form action="<?php echo BASE_URL; ?>public/auth_router.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="action" value="register">
        <div class="form-group">
            <label for="name">Full Name *</label>
            <input 
                type="text" 
                id="name" 
                name="name" 
                placeholder="John Doe" 
                required
                value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
        </div>

        <div class="form-group">
            <label for="email">Email Address *</label>
            <input 
                type="email" 
                id="email" 
                name="email" 
                placeholder="you@example.com" 
                required
                value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
        </div>

        <div class="form-group">
            <label for="phone">Phone Number *</label>
            <input 
                type="tel" 
                id="phone" 
                name="phone" 
                placeholder="03001234567" 
                required
                value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
        </div>

        <div class="form-group">
            <label for="cnic">CNIC (Computerized National Identity Card) *</label>
            <input 
                type="text" 
                id="cnic" 
                name="cnic" 
                placeholder="1234567890123" 
                pattern="[0-9]{13}"
                maxlength="13"
                required
                value="<?php echo htmlspecialchars($_POST['cnic'] ?? ''); ?>">
            <small>Enter 13-digit CNIC number without dashes</small>
        </div>

        <div class="form-group">
            <label for="profile_picture">Profile Picture (Optional)</label>
            <input 
                type="file" 
                id="profile_picture" 
                name="profile_picture" 
                accept="image/jpeg,image/jpg,image/png,image/gif">
            <small>Accepted formats: JPG, PNG, GIF (Max 2MB)</small>
        </div>

        <div class="form-group">
            <label for="password">Password *</label>
            <div style="position: relative; display: flex; align-items: center;">
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    placeholder="Create a strong password" 
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
            <label for="confirm_password">Confirm Password *</label>
            <div style="position: relative; display: flex; align-items: center;">
                <input 
                    type="password" 
                    id="confirm_password" 
                    name="confirm_password" 
                    placeholder="Confirm your password" 
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

        <div class="form-group">
            <label>
                <input type="checkbox" name="agree" required>
                I agree to the <a href="#" target="_blank">Terms & Conditions</a> and <a href="#" target="_blank">Privacy Policy</a>
            </label>
        </div>

        <div class="form-group">
            <div class="g-recaptcha" data-sitekey="6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI"></div>
        </div>

        <button type="submit" class="btn-primary">Create Account</button>
    </form>

    <p class="small-text">
        Already have an account? <a href="auth_router.php?action=login">Sign in here</a>
    </p>
</div>

<script src="../assets/js/script.js"></script>
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
