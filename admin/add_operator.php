<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../controllers/AdminController.php';

requireRole('admin');

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';

    $result = AdminController::createOperator($name, $email, $phone, $password);
    
    if ($result['success']) {
        $messageType = 'success';
        $message = $result['message'];
        // Clear form after success
        $name = $email = $phone = $password = '';
    } else {
        $messageType = 'error';
        $message = $result['message'];
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Operator - Book Smarter, Travel Better</title>
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
    <h2><span style="font-size: 2.5rem; display: inline-block;">🚌</span> Book Smarter, Travel Better</h2>
    <div>
        <a href="dashboard.php">Dashboard</a>
        <a href="manage_buses.php">Manage Buses</a>
        <a href="manage_users.php">Manage Users</a>
        <a href="../index.php">Home</a>
        <a href="../auth/logout.php">Logout</a>
    </div>
</nav>

<section class="page-banner">
    <div style="margin-bottom: 20px; padding: 0 40px;">
        <a href="javascript:history.back()" style="display: inline-block; padding: 10px 20px; background: white; color: #0072ff; border-radius: 6px; text-decoration: none; font-weight: 600; transition: all 0.3s ease; border: 2px solid white;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='white'">← Back</a>
    </div>
    <div class="page-banner-content">
        <h2>Create Operator Account</h2>
        <p>Add a new bus operator to the system. Operators will have access to manage schedules and trips.</p>
    </div>
</section>

<div class="form-container">
    <h2>New Operator Registration</h2>

    <?php if ($message): ?>
        <div class="<?php echo $messageType === 'success' ? 'success-message' : 'error-message'; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label for="name">Operator Name *</label>
            <input type="text" id="name" name="name" placeholder="Full name" value="<?php echo htmlspecialchars($name ?? ''); ?>" required>
        </div>

        <div class="form-group">
            <label for="email">Email Address *</label>
            <input type="email" id="email" name="email" placeholder="operator@example.com" value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
        </div>

        <div class="form-group">
            <label for="phone">Phone Number *</label>
            <input type="tel" id="phone" name="phone" placeholder="Your phone number" value="<?php echo htmlspecialchars($phone ?? ''); ?>" required>
        </div>

        <div class="form-group">
            <label for="password">Password *</label>
            <input type="password" id="password" name="password" placeholder="Minimum 6 characters" required>
            <small>Password must be at least 6 characters long</small>
        </div>

        <div style="display: flex; gap: 12px; margin-top: 24px;">
            <button type="submit" class="btn-primary">Create Operator</button>
            <a href="manage_users.php" class="btn" style="text-decoration: none; background: #6c757d; text-align: center;">Cancel</a>
        </div>
    </form>

    <div style="margin-top: 30px; padding: 20px; background: #f0f4f8; border-radius: 8px; border-left: 4px solid #0072ff;">
        <h3>📋 Operator Details</h3>
        <ul style="margin: 10px 0; padding-left: 20px;">
            <li>Operators are <strong>pre-verified</strong> and can log in immediately</li>
            <li>They can manage bus schedules and view trip details</li>
            <li>Admins can delete operators from the Manage Users page</li>
        </ul>
    </div>
</div>

</body>
</html>
