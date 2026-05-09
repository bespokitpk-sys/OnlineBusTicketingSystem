<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once APP_ROOT . '/config/db.php';
require_once APP_ROOT . '/app/core/Auth.php';
require_once APP_ROOT . '/app/controllers/AdminController.php';

requireRole('admin');

$user = currentUser();

// Get revenue data
$revenueData = AdminController::getRevenueReport();

// Get ticket statistics
$ticketStats = [];
$statResult = $conn->query("
    SELECT status, COUNT(*) as count FROM tickets GROUP BY status
");
if ($statResult) {
    while ($row = $statResult->fetch_assoc()) {
        $ticketStats[$row['status']] = $row['count'];
    }
}

// Get cancelled tickets
$cancelledTickets = AdminController::getCancelledTickets();

// Get total revenue
$totalRevenue = 0;
foreach ($revenueData as $data) {
    $totalRevenue += $data['total_revenue'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports & Analytics - Admin</title>
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
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-box {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            border-left: 4px solid #0072ff;
        }
        
        .stat-label { color: #666; font-size: 0.9rem; text-transform: uppercase; }
        .stat-value { color: #0f1c33; font-size: 2rem; font-weight: 700; margin-top: 8px; }
        
        .table-container { background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08); overflow: hidden; margin-bottom: 30px; }
        
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
        
        .section-title {
            font-size: 1.5rem;
            color: #0f1c33;
            margin-bottom: 20px;
            font-weight: 600;
        }
        
        .no-data { text-align: center; padding: 40px; color: #999; }
        
        @media (max-width: 768px) {
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
            table { font-size: 0.9rem; }
            th, td { padding: 10px; }
        }
    </style>
</head>
<body>

<nav>
    <h2><span style="font-size: 2rem; margin-right: 10px;">??</span>Book Smarter, Travel Better</h2>
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
        <h1>?? Reports & Analytics</h1>
        <p>System statistics and revenue analytics</p>
    </div>
    
    <!-- Summary Statistics -->
    <div class="stats-grid">
        <div class="stat-box">
            <div class="stat-label">Total Revenue</div>
            <div class="stat-value">Rs. <?php echo number_format($totalRevenue, 2); ?></div>
        </div>
        
        <div class="stat-box" style="border-left-color: #28a745;">
            <div class="stat-label">Booked Tickets</div>
            <div class="stat-value"><?php echo $ticketStats['booked'] ?? 0; ?></div>
        </div>
        
        <div class="stat-box" style="border-left-color: #17a2b8;">
            <div class="stat-label">Confirmed Tickets</div>
            <div class="stat-value"><?php echo $ticketStats['confirmed'] ?? 0; ?></div>
        </div>
        
        <div class="stat-box" style="border-left-color: #dc3545;">
            <div class="stat-label">Cancelled Tickets</div>
            <div class="stat-value"><?php echo $ticketStats['cancelled'] ?? 0; ?></div>
        </div>
    </div>
    
    <!-- Revenue Report -->
    <div style="margin-bottom: 50px;">
        <h2 class="section-title">?? Revenue Report (Last 30 Days)</h2>
        <div class="table-container">
            <?php if (count($revenueData) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Tickets Sold</th>
                            <th>Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($revenueData as $data): ?>
                            <tr>
                                <td><strong><?php echo date('M d, Y', strtotime($data['date'])); ?></strong></td>
                                <td><?php echo $data['total_tickets']; ?></td>
                                <td><strong>Rs. <?php echo number_format($data['total_revenue'], 2); ?></strong></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="no-data">
                    <p>?? No revenue data available</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Cancelled Tickets -->
    <div>
        <h2 class="section-title">? Recent Cancelled Tickets</h2>
        <div class="table-container">
            <?php if (count($cancelledTickets) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Ticket ID</th>
                            <th>User ID</th>
                            <th>Price</th>
                            <th>Cancelled On</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cancelledTickets as $ticket): ?>
                            <tr>
                                <td>#<?php echo $ticket['id']; ?></td>
                                <td><?php echo $ticket['user_id']; ?></td>
                                <td>Rs. <?php echo number_format($ticket['price'], 2); ?></td>
                                <td><?php echo date('M d, Y h:i A', strtotime($ticket['created_at'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="no-data">
                    <p>? No cancelled tickets</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="<?php echo BASE_URL; ?>assets/js/script.js"></script>
</body>
</html>
