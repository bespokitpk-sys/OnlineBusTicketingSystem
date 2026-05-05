<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../controllers/AdminController.php';

requireRole('admin');

$buses = AdminController::getAllBuses();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Buses - Admin Panel</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        
        .header {
            background: white;
            padding: 24px;
            border-radius: 12px;
            margin-bottom: 30px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }
        
        .header h1 {
            color: #0f1c33;
            margin-bottom: 8px;
            font-size: 1.8rem;
        }
        
        .header p {
            color: #666;
            margin-bottom: 16px;
        }
        
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #0072ff;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn:hover {
            background: #0056cc;
            transform: translateY(-2px);
        }
        
        .buses-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 24px;
        }
        
        .bus-card {
            background: white;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            border-left: 4px solid #0072ff;
        }
        
        .bus-card:hover {
            box-shadow: 0 6px 16px rgba(0, 114, 255, 0.15);
            transform: translateY(-4px);
        }
        
        .bus-card h3 {
            color: #0f1c33;
            margin-bottom: 12px;
            font-size: 1.2rem;
        }
        
        .bus-info {
            color: #666;
            margin-bottom: 8px;
            font-size: 0.95rem;
        }
        
        .bus-info strong {
            color: #0f1c33;
        }
        
        .bus-actions {
            display: flex;
            gap: 10px;
            margin-top: 16px;
            padding-top: 16px;
            border-top: 1px solid #eee;
        }
        
        .btn-small {
            padding: 8px 16px;
            font-size: 0.85rem;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
            font-weight: 600;
            transition: all 0.3s;
            flex: 1;
        }
        
        .btn-edit {
            background: #0072ff;
            color: white;
        }
        
        .btn-edit:hover {
            background: #0056cc;
        }
        
        .btn-delete {
            background: #dc3545;
            color: white;
        }
        
        .btn-delete:hover {
            background: #c82333;
        }
        
        .empty-state {
            background: white;
            border-radius: 12px;
            padding: 60px 40px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }
        
        .empty-state p {
            color: #666;
            margin-bottom: 20px;
            font-size: 1.1rem;
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
        
        @media (max-width: 1400px) {
            .buses-grid { grid-template-columns: repeat(3, 1fr); }
        }
        
        @media (max-width: 1024px) {
            .buses-grid { grid-template-columns: repeat(2, 1fr); }
        }
        
        @media (max-width: 768px) {
            .buses-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<nav>
    <h2><span style="font-size: 2.5rem; display: inline-block;">🚌</span> Book Smarter, Travel Better</h2>
    <div>
        <a href="dashboard.php">Dashboard</a>
        <a href="add_bus.php">Add Bus</a>
        <a href="manage_users.php">Manage Users</a>
        <a href="../index.php">Home</a>
        <a href="../auth/logout.php">Logout</a>
    </div>
</nav>

<div class="container">
    <div style="margin-bottom: 20px;">
        <a href="javascript:history.back()" style="display: inline-block; padding: 10px 20px; background: #0072ff; color: white; border-radius: 6px; text-decoration: none; font-weight: 600; transition: all 0.3s ease;" onmouseover="this.style.background='#0056cc'" onmouseout="this.style.background='#0072ff'">← Back</a>
    </div>
    <div class="header">
        <h1>🚐 Manage Buses</h1>
        <p>View all buses in your fleet and manage bus information.</p>
        <a href="add_bus.php" class="btn">➕ Add New Bus</a>
    </div>
    
    <?php if (count($buses) > 0): ?>
        <div class="buses-grid">
            <?php foreach ($buses as $bus): ?>
                <div class="bus-card">
                    <h3><?php echo htmlspecialchars($bus['bus_name']); ?></h3>
                    <div class="bus-info">
                        <strong>Total Seats:</strong> <?php echo intval($bus['total_seats']); ?>
                    </div>
                    <div class="bus-info">
                        <strong>Added:</strong> <?php echo date('M d, Y', strtotime($bus['created_at'])); ?>
                    </div>
                    <div class="bus-actions">
                        <form method="POST" style="display: inline; flex: 1;">
                            <input type="hidden" name="delete_bus_id" value="<?php echo intval($bus['id']); ?>">
                            <button type="submit" class="btn-small btn-delete" onclick="return confirm('Delete this bus?')">Delete</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <p>📭 No buses added yet.</p>
            <a href="add_bus.php" class="btn">➕ Add First Bus</a>
        </div>
    <?php endif; ?>
</div>

<?php
// Handle bus deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_bus_id'])) {
    $bus_id = intval($_POST['delete_bus_id']);
    $result = AdminController::deleteBus($bus_id);
    if ($result['success']) {
        header('Location: manage_buses.php?success=' . urlencode($result['message']));
        exit;
    } else {
        header('Location: manage_buses.php?error=' . urlencode($result['message']));
        exit;
    }
}
?>

</body>
</html>