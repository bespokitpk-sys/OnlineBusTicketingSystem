<?php
require_once APP_ROOT . '/config/db.php';
$error = $_SESSION['error'] ?? '';
$success = $_SESSION['success'] ?? '';
unset($_SESSION['error'], $_SESSION['success']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Book smarter, travel better</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
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
<script src="<?php echo BASE_URL; ?>assets/js/script.js"></script>
<nav>
    <h2><span style="font-size: 2.5rem; display: inline-block;">🚌</span> Book Smarter, Travel Better</h2>
    <div>
        <a href="<?php echo BASE_URL; ?>">Home</a>
        <a href="<?php echo BASE_URL; ?>register">Register</a>
        <a href="<?php echo BASE_URL; ?>search">Search</a>
    </div>
</nav>

<section class="page-banner">
    <div class="page-banner-content">
        <h2>Welcome Back</h2>
        <p>Sign in to your account to book tickets, manage reservations, and enjoy seamless travel experiences.</p>
    </div>
</section>

<div class="form-container">
    <a href="<?php echo BASE_URL; ?>" class="back-button">← Back to Home</a>
    <h2>Login to Your Account</h2>
    
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

    <form action="<?php echo BASE_URL; ?>login" method="POST">
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
            <label for="password">Password *</label>
            <div style="position: relative; display: flex; align-items: center;">
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    placeholder="Enter your password" 
                    required
                    style="flex: 1; padding-right: 40px;">
                <button 
                    type="button" 
                    id="togglePassword" 
                    style="position: absolute; right: 12px; background: none; border: none; cursor: pointer; color: #666; font-size: 18px; padding: 0;"
                    onclick="togglePasswordVisibility()">
                    👁️
                </button>
            </div>
        </div>

        <button type="submit" class="btn-primary">Sign In</button>
    </form>

    <p class="small-text">
        Don't have an account? <a href="<?php echo BASE_URL; ?>register">Create one here</a><br>
        <a href="<?php echo BASE_URL; ?>forgot-password">Forgot your password?</a>
    </p>
</div>

<script>
function togglePasswordVisibility() {
    const passwordInput = document.getElementById('password');
    const toggleBtn = document.getElementById('togglePassword');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleBtn.textContent = '🙈';
    } else {
        passwordInput.type = 'password';
        toggleBtn.textContent = '👁️';
    }
}
</script>

</body>
</html>
