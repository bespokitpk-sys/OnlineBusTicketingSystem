<?php
require_once APP_ROOT . '/config/db.php';
require_once APP_ROOT . '/app/core/Auth.php';
require_once APP_ROOT . '/app/controllers/AdminController.php';

requireRole('admin');

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $name = trim($_POST['bus_name'] ?? '');
    $seats = intval($_POST['seats'] ?? 0);
    
    if (!empty($name) && $seats > 0) {
        $result = AdminController::addBus($name, $seats);
        if ($result['success']) {
            $messageType = 'success';
            $message = $result['message'];
            $name = '';
            $seats = '';
        } else {
            $messageType = 'error';
            $message = $result['message'];
        }
    } else {
        $messageType = 'error';
        $message = 'Please enter a valid bus name and seat count.';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Add Bus - Admin Panel</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
    <style>
        .form-container {
            max-width: 600px;
            margin: 40px auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        
        .form-container h2 {
            color: #0f1c33;
            margin-bottom: 24px;
            font-size: 1.5rem;
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
            font-size: 0.95rem;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #0072ff;
            box-shadow: 0 0 0 3px rgba(0, 114, 255, 0.1);
        }
        
        .btn-group {
            display: flex;
            gap: 12px;
            margin-top: 24px;
        }
        
        button {
            flex: 1;
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        button[type="submit"] {
            background: #0072ff;
            color: white;
        }
        
        button[type="submit"]:hover {
            background: #0056cc;
        }
        
        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 20px;
            border-left: 4px solid #28a745;
        }
        
        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 20px;
            border-left: 4px solid #dc3545;
        }
        
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
    <h2><span style="font-size: 2.5rem; display: inline-block;">??</span> Book Smarter, Travel Better</h2>
    <div>
        <a href="<?php echo BASE_URL; ?>admin/dashboard">Dashboard</a>
        <a href="<?php echo BASE_URL; ?>admin/buses">Manage Buses</a>
        <a href="<?php echo BASE_URL; ?>admin/users">Manage Users</a>
        <a href="<?php echo BASE_URL; ?>">Home</a>
        <a href="<?php echo BASE_URL; ?>logout">Logout</a>
    </div>
</nav>

<div style="max-width: 600px; margin: 20px auto 0; padding: 0 20px;">
    <a href="javascript:history.back()" style="display: inline-block; padding: 10px 20px; background: #0072ff; color: white; border-radius: 6px; text-decoration: none; font-weight: 600; transition: all 0.3s ease;" onmouseover="this.style.background='#0056cc'" onmouseout="this.style.background='#0072ff'">? Back</a>
</div>

<div class="form-container">
    <h2>? Add New Bus</h2>
    
    <?php if ($message): ?>
        <div class="<?php echo $messageType === 'success' ? 'success-message' : 'error-message'; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>
    
    <form method="POST">
        <div class="form-group">
            <label for="bus_name">Bus Name *</label>
            <input type="text" id="bus_name" name="bus_name" placeholder="e.g., Karachi Express" value="<?php echo htmlspecialchars($name ?? ''); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="seats">Total Seats *</label>
            <input type="number" id="seats" name="seats" placeholder="e.g., 42" min="1" max="100" value="<?php echo intval($seats ?? ''); ?>" required>
        </div>
        
        <div class="btn-group">
            <button type="submit" name="add">Add Bus</button>
            <a href="<?php echo BASE_URL; ?>admin/buses" style="padding: 12px 24px; background: #6c757d; color: white; text-decoration: none; border-radius: 6px; text-align: center; transition: all 0.3s; display: flex; align-items: center; justify-content: center;">Cancel</a>
        </div>
    </form>
</div>

<script src="<?php echo BASE_URL; ?>assets/js/script.js"></script>
</body>
</html>