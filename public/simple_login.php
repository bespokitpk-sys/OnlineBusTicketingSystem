<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/User.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// If POST, process login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $_SESSION['error'] = 'Please enter email and password.';
    } else {
        // Authenticate user
        $user = User::authenticate($email, $password);
        
        if ($user) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['success'] = 'Login successful!';
            
            // Redirect to appropriate dashboard based on role
            if ($user['role'] === 'admin') {
                header('Location: ../admin/dashboard.php');
                exit;
            } elseif ($user['role'] === 'operator') {
                header('Location: ../operator/dashboard.php');
                exit;
            } else {
                // Passenger
                header('Location: ../passenger/dashboard.php');
                exit;
            }
        } else {
            $_SESSION['error'] = 'Invalid email or password, or account not verified.';
        }
    }
}

// Get any error/success messages
$error = $_SESSION['error'] ?? '';
unset($_SESSION['error']);
$success = $_SESSION['success'] ?? '';
unset($_SESSION['success']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Bus Ticketing System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
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
        }
        
        nav h2 {
            font-size: 1.6rem;
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        
        nav div {
            display: flex;
            gap: 10px;
        }
        
        nav a {
            color: #0f1c33;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.85rem;
            padding: 10px 16px;
            border-radius: 6px;
            background: rgba(0, 114, 255, 0.1);
            border: 1px solid rgba(0, 114, 255, 0.4);
        }
        
        nav a:hover {
            background: #0072ff;
            color: white;
            border-color: #0072ff;
        }
        
        .page-banner {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 60px 40px;
            text-align: center;
        }
        
        .page-banner h2 {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
        
        .page-banner p {
            font-size: 1.1rem;
            opacity: 0.9;
        }
        
        .form-container {
            max-width: 500px;
            margin: 40px auto;
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .form-container h2 {
            margin-bottom: 30px;
            color: #0f1c33;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #0f1c33;
            font-weight: 600;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 1rem;
            font-family: inherit;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #0072ff;
            box-shadow: 0 0 0 3px rgba(0, 114, 255, 0.1);
        }
        
        .error-message {
            background: #fee;
            color: #c33;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            border: 1px solid #fcc;
        }
        
        .success-message {
            background: #efe;
            color: #3c3;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            border: 1px solid #cfc;
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
        
        .small-text {
            text-align: center;
            margin-top: 20px;
            color: #666;
            font-size: 0.9rem;
        }
        
        .small-text a {
            color: #0072ff;
            text-decoration: none;
        }
        
        .small-text a:hover {
            text-decoration: underline;
        }
        
        .back-button {
            display: inline-block;
            margin-bottom: 20px;
            color: #0072ff;
            text-decoration: none;
            font-weight: 600;
            padding: 8px 16px;
            border: 1px solid #0072ff;
            border-radius: 6px;
            transition: all 0.3s ease;
        }
        
        .back-button:hover {
            background: #0072ff;
            color: white;
        }
    </style>
</head>
<body>
<nav>
    <h2><span style="font-size: 2.5rem; display: inline-block;">🚌</span> Book Smarter, Travel Better</h2>
    <div>
        <a href="../index.php">Home</a>
        <a href="auth_router.php?action=register">Register</a>
        <a href="search.php">Search</a>
    </div>
</nav>

<section class="page-banner">
    <div class="page-banner-content">
        <h2>Welcome Back</h2>
        <p>Sign in to your account to book tickets, manage reservations, and enjoy seamless travel experiences.</p>
    </div>
</section>

<div class="form-container">
    <a href="../index.php" class="back-button">← Back to Home</a>
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

    <form method="POST" action="simple_login.php">
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
            <input 
                type="password" 
                id="password" 
                name="password" 
                placeholder="Enter your password" 
                required>
        </div>

        <button type="submit" class="btn-primary">Sign In</button>
    </form>

    <p class="small-text">
        Don't have an account? <a href="auth_router.php?action=register">Create one here</a><br>
        <a href="auth_router.php?action=forgotPassword">Forgot your password?</a>
    </p>
</div>

</body>
</html>
