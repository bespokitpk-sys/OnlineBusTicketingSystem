<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/User.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$error = '';
$success = '';

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        $token = User::generateResetToken($email);
        if ($token) {
            // Here you would send the reset email
            // For now, just show success message
            $success = 'Password reset link has been sent to your email address.';
        } else {
            $error = 'Email address not found in our system.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Book smarter, travel better</title>
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

        .page-banner {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 60px 40px;
            text-align: center;
        }
        
        .page-banner-content h2 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            color: white;
        }
        
        .page-banner-content p {
            font-size: 1.1rem;
            opacity: 0.9;
            color: white;
        }

        .form-container {
            max-width: 500px;
            margin: 40px auto;
            padding: 40px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .form-container h2 {
            color: #0f1c33;
            margin-bottom: 20px;
            font-size: 1.8rem;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #0f1c33;
            font-weight: 600;
            font-size: 0.95rem;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        .form-group input:focus {
            outline: none;
            border-color: #0072ff;
            box-shadow: 0 0 0 3px rgba(0, 114, 255, 0.1);
        }

        .btn-primary {
            width: 100%;
            padding: 12px;
            background: linear-gradient(90deg, #0072ff 0%, #0056cc 100%);
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            box-shadow: 0 4px 12px rgba(0, 114, 255, 0.4);
            transform: translateY(-2px);
        }

        .back-button {
            display: inline-block;
            color: #0072ff;
            text-decoration: none;
            font-weight: 600;
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }

        .back-button:hover {
            color: #0056cc;
        }

        .error-message {
            background: #f8d7da;
            color: #842029;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            border: 1px solid #f5c2c7;
        }

        .success-message {
            background: #d1e7dd;
            color: #0f5132;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            border: 1px solid #badbcc;
        }

        .small-text {
            text-align: center;
            color: #666;
            margin-top: 15px;
            font-size: 0.95rem;
        }

        .small-text a {
            color: #0072ff;
            text-decoration: none;
            font-weight: 600;
        }

        .small-text a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<script src="<?php echo BASE_URL; ?>assets/js/script.js"></script>
<nav>
    <h2><span style="font-size: 2.5rem; display: inline-block;">🚌</span> Book Smarter, Travel Better</h2>
    <div>
        <a href="<?php echo BASE_URL; ?>index.php">Home</a>
        <a href="<?php echo BASE_URL; ?>public/passenger_login.php">Login</a>
        <a href="<?php echo BASE_URL; ?>public/passenger_register.php">Register</a>
    </div>
</nav>

<section class="page-banner">
    <div class="page-banner-content">
        <h2>Password Recovery</h2>
        <p>Enter your email address and we'll send you a link to reset your password.</p>
    </div>
</section>

<div class="form-container">
    <a href="<?php echo BASE_URL; ?>public/passenger_login.php" class="back-button">← Back to Login</a>
    <h2>Forgot Your Password?</h2>

    <?php if ($error): ?>
        <div class="error-message">
            <p><?php echo htmlspecialchars($error); ?></p>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="success-message">
            <p><?php echo htmlspecialchars($success); ?></p>
            <p style="margin-top: 10px; font-size: 0.9rem;">You can now <a href="<?php echo BASE_URL; ?>public/passenger_login.php" style="color: inherit; text-decoration: underline;">return to login</a> or check your email for the reset link.</p>
        </div>
    <?php endif; ?>

    <form action="forgot_password.php" method="POST">
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

        <button type="submit" class="btn-primary">Send Reset Link</button>
    </form>

    <p class="small-text">
        Remember your password? <a href="<?php echo BASE_URL; ?>public/passenger_login.php">Back to Login</a>
    </p>
</div>

</body>
</html>