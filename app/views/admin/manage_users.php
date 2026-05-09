<?php
require_once APP_ROOT . '/config/db.php';
require_once APP_ROOT . '/app/core/Auth.php';
require_once APP_ROOT . '/app/controllers/AdminController.php';

requireRole('admin');

// Get operators and passengers
$operators = AdminController::getOperators();
$passengers = $conn->query("SELECT id, name, email, phone, created_at FROM users WHERE role = 'passenger' ORDER BY name ASC");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Book Smarter, Travel Better</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background: #f5f7fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        
        .page-header {
            background: linear-gradient(135deg, #0072ff 0%, #0056cc 100%);
            color: white;
            padding: 40px;
            border-radius: 12px;
            margin: 0 auto 30px auto;
            box-shadow: 0 4px 12px rgba(0, 114, 255, 0.15);
            max-width: 800px;
            width: 100%;
        }
        
        .page-header h2 {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .page-header p {
            font-size: 0.95rem;
            opacity: 0.95;
            margin: 0;
        }
        
        .users-container {
            display: grid;
            grid-template-columns: 1fr;
            gap: 30px;
        }
        .user-section {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        .user-section-header {
            background: linear-gradient(135deg, #0072ff 0%, #0056cc 100%);
            color: white;
            padding: 20px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .user-section-header h3 {
            margin: 0;
            font-size: 1.3rem;
        }
        .user-section-header .badge {
            background: rgba(255, 255, 255, 0.3);
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
        }
        .user-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        .user-table thead {
            background: #f5f5f5;
            border-bottom: 2px solid #e0e0e0;
        }
        .user-table th {
            padding: 16px;
            text-align: center;
            color: #0f1c33;
            font-weight: 600;
            border-right: 1px solid #e0e0e0;
        }
        .user-table th:last-child {
            border-right: none;
        }
        .user-table td {
            padding: 14px 16px;
            border-bottom: 1px solid #e0e0e0;
            border-right: 1px solid #f0f0f0;
            color: #666;
            word-wrap: break-word;
            text-align: center;
        }
        .user-table td:last-child {
            border-right: none;
        }
        .user-table th:nth-child(1),
        .user-table td:nth-child(1) {
            width: 20%;
        }
        .user-table th:nth-child(2),
        .user-table td:nth-child(2) {
            width: 30%;
        }
        .user-table th:nth-child(3),
        .user-table td:nth-child(3) {
            width: 25%;
        }
        .user-table th:nth-child(4),
        .user-table td:nth-child(4) {
            width: 15%;
        }
        .user-table th:nth-child(5),
        .user-table td:nth-child(5) {
            width: 10%;
            text-align: center;
        }
        .user-table tr:hover {
            background: #f9f9f9;
        }
        .user-table .action-btn {
            padding: 6px 12px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-block;
            border: none;
        }
        .action-btn.delete {
            background: #d32f2f;
            color: white;
        }
        .action-btn.delete:hover {
            background: #b71c1c;
        }
        .empty-state {
            padding: 40px;
            text-align: center;
            color: #999;
        }
        .empty-state p {
            margin: 10px 0;
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
    <h2><span style="font-size: 2.5rem; display: inline-block;">&#128652;</span> Book Smarter, Travel Better</h2>
    <div>
        <a href="<?php echo BASE_URL; ?>admin/dashboard">Dashboard</a>
        <a href="<?php echo BASE_URL; ?>admin/add-bus">Add Bus</a>
        <a href="<?php echo BASE_URL; ?>admin/buses">Manage Buses</a>
        <a href="<?php echo BASE_URL; ?>">Home</a>
        <a href="<?php echo BASE_URL; ?>logout">Logout</a>
    </div>
</nav>

<div class="dashboard">
    <div class="page-header">
        <div style="margin-bottom: 15px;">
            <a href="javascript:history.back()" style="display: inline-block; padding: 10px 20px; background: rgba(255, 255, 255, 0.25); color: white; border-radius: 6px; text-decoration: none; font-weight: 600; transition: all 0.3s ease; border: 2px solid rgba(255, 255, 255, 0.5);" onmouseover="this.style.background='rgba(255, 255, 255, 0.35)'" onmouseout="this.style.background='rgba(255, 255, 255, 0.25)'">? Back</a>
        </div>
        <h2>&#128100; Manage Users</h2>
        <p>View and manage all operators and passengers in the system.</p>
    </div>

    <div class="users-container">
        <!-- OPERATORS SECTION -->
        <div class="user-section">
            <div class="user-section-header">
                <h3>&#128119; Bus Operators</h3>
                <span class="badge"><?php echo count($operators); ?> Operator(s)</span>
            </div>
            <?php if (count($operators) > 0): ?>
                <table class="user-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Joined</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($operators as $op): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($op['name']); ?></strong></td>
                                <td><?php echo htmlspecialchars($op['email']); ?></td>
                                <td><?php echo htmlspecialchars($op['phone']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($op['created_at'])); ?></td>
                                <td>
                                    <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this operator?');">
                                        <input type="hidden" name="delete_operator_id" value="<?php echo intval($op['id']); ?>">
                                        <button type="submit" class="action-btn delete">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">
                    <p>No operators yet.</p>
                    <a href="<?php echo BASE_URL; ?>admin/add-operator" class="btn" style="display: inline-block; margin-top: 12px;">Create First Operator</a>
                </div>
            <?php endif; ?>
        </div>

        <!-- PASSENGERS SECTION -->
        <div class="user-section">
            <div class="user-section-header">
                <h3>&#128100; Passengers</h3>
                <span class="badge"><?php echo $passengers->num_rows; ?> Passenger(s)</span>
            </div>
            <?php if ($passengers->num_rows > 0): ?>
                <table class="user-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Joined</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($pass = $passengers->fetch_assoc()): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($pass['name']); ?></strong></td>
                                <td><?php echo htmlspecialchars($pass['email']); ?></td>
                                <td><?php echo htmlspecialchars($pass['phone']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($pass['created_at'])); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">
                    <p>No passengers registered yet.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
// Handle operator deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_operator_id'])) {
    $op_id = intval($_POST['delete_operator_id']);
    $result = AdminController::deleteOperator($op_id);
    if ($result['success']) {
        header('Location: manage_users.php?deleted=true');
        exit;
    }
}
?>
<script src="<?php echo BASE_URL; ?>assets/js/script.js"></script>
</body>
</html>