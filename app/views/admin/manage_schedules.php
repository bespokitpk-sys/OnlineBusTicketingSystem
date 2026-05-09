<?php
require_once APP_ROOT . '/config/db.php';
require_once APP_ROOT . '/app/core/Auth.php';
require_once APP_ROOT . '/app/controllers/AdminController.php';

requireRole('admin');

$user = currentUser();
$schedules = AdminController::getAllSchedules();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Schedules - Admin</title>
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
        
        .container { max-width: 1200px; margin: 0 auto; padding: 30px 20px; }
        
        .header { background: white; padding: 25px; border-radius: 12px; margin-bottom: 30px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08); }
        .header h1 { color: #0f1c33; margin-bottom: 8px; }
        .header p { color: #666; }
        
        .table-container { background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08); overflow: hidden; }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        thead {
            background: #f5f7fa;
            border-bottom: 2px solid #e0e0e0;
        }
        
        th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
            color: #0f1c33;
        }
        
        td {
            padding: 15px;
            border-bottom: 1px solid #e0e0e0;
            color: #666;
        }
        
        tr:hover { background: #f9f9f9; }
        
        .badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            background: #d1e7dd;
            color: #0f5132;
        }
        
        .no-data { text-align: center; padding: 40px; color: #999; }
        .no-data p { font-size: 1.1rem; }
        
        @media (max-width: 768px) {
            table { font-size: 0.9rem; }
            th, td { padding: 10px; }
        }
    </style>
</head>
<body>

<nav>
    <h2><span style="font-size: 2rem; margin-right: 10px;">&#128652;</span>Book Smarter, Travel Better</h2>
    <div>
        <a href="<?php echo BASE_URL; ?>admin/dashboard">Dashboard</a>
        <a href="<?php echo BASE_URL; ?>admin/buses">Buses</a>
        <a href="<?php echo BASE_URL; ?>admin/tickets">Tickets</a>
        <a href="<?php echo BASE_URL; ?>logout">Logout</a>
    </div>
</nav>

<div class="container">
    <div style="margin-bottom: 20px;">
        <a href="javascript:history.back()" style="display: inline-block; padding: 10px 20px; background: #0072ff; color: white; border-radius: 6px; text-decoration: none; font-weight: 600; transition: all 0.3s ease;" onmouseover="this.style.background='#0056cc'" onmouseout="this.style.background='#0072ff'">? Back</a>
    </div>
    <div class="header">
        <h1>&#128197; Manage Schedules</h1>
        <p>View all trip schedules created by operators</p>
    </div>
    
    <div class="table-container">
        <?php if (count($schedules) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Schedule ID</th>
                        <th>Bus Name</th>
                        <th>Route</th>
                        <th>Operator</th>
                        <th>Departure</th>
                        <th>Created</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($schedules as $schedule): ?>
                        <tr>
                            <td><strong>#<?php echo $schedule['id']; ?></strong></td>
                            <td><?php echo htmlspecialchars($schedule['bus_name'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($schedule['source'] ?? 'N/A'); ?> ? <?php echo htmlspecialchars($schedule['destination'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($schedule['operator_name'] ?? 'N/A'); ?></td>
                            <td><?php echo date('M d, h:i A', strtotime($schedule['departure_time'])); ?></td>
                            <td><?php echo date('M d, Y', strtotime($schedule['created_at'])); ?></td>
                            <td>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="delete_schedule_id" value="<?php echo intval($schedule['id']); ?>">
                                    <button type="submit" style="padding: 6px 12px; background: #dc3545; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: 600; font-size: 0.85rem;" onclick="return confirm('Delete this schedule?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="no-data">
                <p>&#128197; No schedules found</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
// Handle schedule deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_schedule_id'])) {
    $schedule_id = intval($_POST['delete_schedule_id']);
    $result = AdminController::deleteSchedule($schedule_id);
    if ($result['success']) {
        header('Location: manage_schedules.php?success=' . urlencode($result['message']));
        exit;
    } else {
        header('Location: manage_schedules.php?error=' . urlencode($result['message']));
        exit;
    }
}
?>

<script src="<?php echo BASE_URL; ?>assets/js/script.js"></script>
</body>
</html>
