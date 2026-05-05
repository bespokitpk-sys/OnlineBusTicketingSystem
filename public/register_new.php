<?php
require_once __DIR__ . '/../config/db.php';
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
include_once __DIR__ . '/../controllers/AuthController.php';

$error = $_SESSION['error'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

// If POST request, process the form
if ($method === 'POST') {
    AuthController::register();
    exit;
}

// If GET request, show the form
unset($_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Bus Ticketing System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <script src="../assets/js/script.js"></script>
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
            box-shadow: 0 8px 24px rgba(15, 28, 51, 0.12);
            position: sticky;
            top: 0;
            z-index: 100;
            flex-wrap: nowrap;
            gap: 20px;
            min-height: 70px;
        }
        
        nav h2 {
            font-size: 1.6rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            gap: 10px;
            white-space: nowrap;
            margin: 0;
            flex-shrink: 0;
        }
        
        nav div {
            display: flex;
            gap: 10px;
            align-items: center;
            justify-content: flex-end;
            flex-wrap: nowrap;
        }
        
        nav a {
            color: #0f1c33;
            text-decoration: none;
            transition: all 0.3s ease;
            font-weight: 600;
            font-size: 0.85rem;
            padding: 10px 16px;
            border-radius: 6px;
            background: rgba(0, 114, 255, 0.1);
            border: 1px solid rgba(0, 114, 255, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: auto;
            height: 38px;
            white-space: nowrap;
            line-height: 1;
            box-sizing: border-box;
            cursor: pointer;
        }
        
        nav a:hover {
            color: #ffffff;
            background: #0072ff;
            border-color: #0072ff;
            box-shadow: 0 4px 12px rgba(0, 114, 255, 0.5);
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
<nav>
    <h2>🚌 Bus Ticketing System</h2>
    <div>
        <a href="../index.php">Home</a>
        <a href="login.php">Login</a>
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

    <form action="<?php echo BASE_URL; ?>public/auth_router.php?action=register" method="POST">
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
            <label for="password">Password *</label>
            <input 
                type="password" 
                id="password" 
                name="password" 
                placeholder="Create a strong password" 
                required>
            <small>Minimum 8 characters with uppercase, lowercase, numbers, and special characters.</small>
        </div>

        <div class="form-group">
            <label for="confirm_password">Confirm Password *</label>
            <input 
                type="password" 
                id="confirm_password" 
                name="confirm_password" 
                placeholder="Confirm your password" 
                required>
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
        Already have an account? <a href="login.php">Sign in here</a>
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
</script>

</body>
</html>
