<?php
require_once APP_ROOT . '/config/db.php';
require_once APP_ROOT . '/app/core/Auth.php';
require_once APP_ROOT . '/app/controllers/OperatorController.php';

requireRole('operator');

$user = currentUser();
$operator_id = $user['id'];

$message = '';
$message_type = '';
$buses = $conn->query("SELECT * FROM buses ORDER BY bus_name ASC");

if (isset($_POST['add'])) {
    $bus_id = intval($_POST['bus_id']);
    $source = $_POST['source'] ?? '';
    $destination = $_POST['destination'] ?? '';
    $time = $_POST['time'] ?? '';
    
    $result = OperatorController::addSchedule($operator_id, $bus_id, $source, $destination, $time);
    
    if ($result['success']) {
        $message = $result['message'];
        $message_type = 'success';
        // Clear form
        $_POST = [];
    } else {
        $message = $result['message'];
        $message_type = 'error';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Add Schedule - Operator Panel</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background: #f5f7fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        
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
            gap: 20px;
            flex-wrap: nowrap;
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
            position: relative;
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
        
        .container { max-width: 600px; margin: 0 auto; padding: 30px 20px; }
        
        .page-header {
            background: linear-gradient(135deg, #0072ff 0%, #0056cc 100%);
            color: white;
            padding: 40px;
            border-radius: 12px;
            margin-bottom: 30px;
            box-shadow: 0 4px 12px rgba(0, 114, 255, 0.15);
        }
        
        .page-header h2 {
            font-size: 1.6rem;
            font-weight: 700;
            margin-bottom: 8px;
        }
        
        .page-header p {
            font-size: 0.95rem;
            opacity: 0.95;
            margin: 0;
        }
        
        .back-btn {
            display: inline-block; 
            padding: 10px 20px; 
            background: rgba(255, 255, 255, 0.25); 
            color: white; 
            border-radius: 6px; 
            text-decoration: none; 
            font-weight: 600; 
            transition: all 0.3s ease; 
            border: 2px solid rgba(255, 255, 255, 0.5);
            margin-bottom: 15px;
        }
        
        .back-btn:hover {
            background: rgba(255, 255, 255, 0.35);
        }
        
        .form-container {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
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
        
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 0.95rem;
        }
        
        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #0072ff;
            box-shadow: 0 0 0 3px rgba(0, 114, 255, 0.1);
        }
        
        button {
            width: 100%;
            padding: 12px;
            background: #0072ff;
            color: white;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        button:hover {
            background: #0056cc;
            transform: translateY(-2px);
        }
        
        .message {
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 20px;
            border-left: 4px solid #0072ff;
            background: #d1e7ff;
            color: #003d99;
        }
    </style>
</head>
<body>
<nav>
    <h2><span style="font-size: 2.5rem; display: inline-block;">🚌</span> Book Smarter, Travel Better</h2>
    <div>
        <a href="<?php echo BASE_URL; ?>operator/dashboard">Dashboard</a>
        <a href="<?php echo BASE_URL; ?>operator/schedules">My Schedules</a>
        <a href="<?php echo BASE_URL; ?>">Home</a>
        <a href="<?php echo BASE_URL; ?>logout">Logout</a>
    </div>
</nav>

<div class="container">
    <div class="page-header">
        <a href="javascript:history.back()" class="back-btn">← Back</a>
        <h2>📅 Add Schedule</h2>
        <p>Create a new trip schedule for your bus</p>
    </div>
    
    <div class="form-container">
        <?php if ($message): ?>
            <div class="message"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="bus_id">Select Bus *</label>
                <select id="bus_id" name="bus_id" required>
                    <option value="">-- Choose a Bus --</option>
                    <?php while ($bus = $buses->fetch_assoc()): ?>
                        <option value="<?php echo intval($bus['id']); ?>"><?php echo htmlspecialchars($bus['bus_name'] . ' (' . $bus['total_seats'] . ' seats)'); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="source">From (Source) *</label>
                <input type="text" id="source" name="source" placeholder="e.g., Karachi" required>
            </div>
            
            <div class="form-group">
                <label for="destination">To (Destination) *</label>
                <input type="text" id="destination" name="destination" placeholder="e.g., Lahore" required>
            </div>
            
            <div class="form-group">
                <label for="time">Departure Time *</label>
                <input type="datetime-local" id="time" name="time" required>
            </div>
            
            <button type="submit" name="add">➕ Add Schedule</button>
        </form>
    </div>
</div>
</body>
</html>