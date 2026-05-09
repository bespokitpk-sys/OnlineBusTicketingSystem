<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once APP_ROOT . '/config/db.php';
require_once APP_ROOT . '/app/core/Auth.php';
require_once APP_ROOT . '/app/controllers/AdminController.php';

requireRole('admin');

$user = currentUser();
$stats = AdminController::getDashboardStats();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Book Smarter, Travel Better</title>
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
        
        nav a:active {
            transform: translateY(0px);
            box-shadow: 0 2px 6px rgba(0, 114, 255, 0.4);
        }
        
        .dashboard { max-width: 1400px; margin: 0 auto; padding: 30px 20px; }
        
        .header-section {
            background: white;
            padding: 30px;
            border-radius: 12px;
            margin-bottom: 30px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }
        
        .header-section h1 { color: #0f1c33; margin-bottom: 8px; font-size: 2.2rem; }
        .header-section p { color: #666; font-size: 1.05rem; }
        
        .welcome-user { 
            display: inline-block; 
            background: linear-gradient(135deg, #e8f4f8 0%, #d4e9f7 100%);
            padding: 12px 20px;
            border-radius: 6px;
            color: #0f1c33;
            font-weight: 600;
            margin-bottom: 15px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            border-left: 4px solid #0072ff;
            transition: all 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 6px 16px rgba(0, 114, 255, 0.15);
        }
        
        .stat-icon { font-size: 2.5rem; margin-bottom: 12px; }
        .stat-label { color: #666; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px; }
        .stat-value { color: #0f1c33; font-size: 2rem; font-weight: 700; }
        
        .stat-card.buses { border-left-color: #ff9500; }
        .stat-card.operators { border-left-color: #28a745; }
        .stat-card.passengers { border-left-color: #17a2b8; }
        .stat-card.schedules { border-left-color: #ffc107; }
        .stat-card.tickets { border-left-color: #e83e8c; }
        .stat-card.bookings { border-left-color: #6f42c1; }
        
        .admin-section {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 25px;
            margin-bottom: 30px;
        }
        
        .admin-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }
        
        .admin-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 8px 20px rgba(0, 114, 255, 0.15);
        }
        
        .card-header {
            background: linear-gradient(135deg, #0072ff 0%, #0056cc 100%);
            color: white;
            padding: 25px;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .card-icon { font-size: 2.5rem; }
        
        .card-title {
            flex: 1;
        }
        
        .card-title h3 { font-size: 1.3rem; margin-bottom: 4px; }
        .card-title p { font-size: 0.85rem; opacity: 0.9; }
        
        .card-body {
            padding: 25px;
        }
        
        .card-body p {
            color: #666;
            margin-bottom: 20px;
            line-height: 1.6;
        }
        
        .btn-group {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
            gap: 12px;
        }
        
        .btn {
            display: inline-block;
            padding: 12px 20px;
            border-radius: 6px;
            text-decoration: none;
            text-align: center;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }
        
        .btn-primary {
            background: #0072ff;
            color: white;
        }
        
        .btn-primary:hover {
            background: #0056cc;
            transform: translateX(2px);
        }
        
        .btn-secondary {
            background: #e8f4f8;
            color: #0072ff;
            border: 1px solid #0072ff;
        }
        
        .btn-secondary:hover {
            background: #d4e9f7;
        }
        
        .quick-actions {
            background: linear-gradient(135deg, #e8f4f8 0%, #d4e9f7 100%);
            padding: 30px;
            border-radius: 12px;
            border-left: 4px solid #0072ff;
            margin-bottom: 30px;
        }
        
        .quick-actions h3 {
            color: #0f1c33;
            margin-bottom: 20px;
            font-size: 1.3rem;
        }
        
        .quick-actions ul {
            list-style: none;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
        }
        
        .quick-actions li {
            color: #0f1c33;
            font-size: 0.95rem;
            line-height: 1.6;
        }
        
        .quick-actions li strong {
            color: #0072ff;
        }
        
        @media (max-width: 1400px) {
            .stats-grid { grid-template-columns: repeat(3, 1fr); }
            .admin-section { grid-template-columns: repeat(3, 1fr); }
        }
        
        @media (max-width: 1024px) {
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
            .admin-section { grid-template-columns: repeat(2, 1fr); }
        }
        
        @media (max-width: 768px) {
            .stats-grid { grid-template-columns: 1fr; }
            .admin-section { grid-template-columns: 1fr; }
            nav div { gap: 10px; font-size: 0.9rem; }
        }
    </style>
</head>
<body>

<nav>
    <h2><span style="font-size: 2rem; margin-right: 10px;">&#128652;</span>Book Smarter, Travel Better</h2>
    <div>
        <a href="<?php echo BASE_URL; ?>">Home</a>
        <a href="<?php echo BASE_URL; ?>admin/buses">Buses</a>
        <a href="<?php echo BASE_URL; ?>admin/users">Users</a>
        <a href="<?php echo BASE_URL; ?>admin/add-operator">Add Operator</a>
        <a href="<?php echo BASE_URL; ?>logout">Logout</a>
    </div>
</nav>

<div class="dashboard">
    <!-- Header -->
    <div class="header-section">
        <div class="welcome-user">&#128075; Welcome, <?php echo htmlspecialchars($user['name']); ?> (Admin)</div>
        <h1>&#9881;&#65039; Admin Dashboard</h1>
        <p>Manage buses, operators, passengers, and view system statistics.</p>
    </div>
    
    <!-- Statistics -->
    <div class="stats-grid">
        <div class="stat-card buses">
            <div class="stat-icon">&#128652;</div>
            <div class="stat-label">Total Buses</div>
            <div class="stat-value"><?php echo $stats['total_buses']; ?></div>
        </div>
        
        <div class="stat-card operators">
            <div class="stat-icon">&#128119;</div>
            <div class="stat-label">Operators</div>
            <div class="stat-value"><?php echo $stats['total_operators']; ?></div>
        </div>
        
        <div class="stat-card passengers">
            <div class="stat-icon">&#128100;</div>
            <div class="stat-label">Passengers</div>
            <div class="stat-value"><?php echo $stats['total_passengers']; ?></div>
        </div>
        
        <div class="stat-card schedules">
            <div class="stat-icon">&#128197;</div>
            <div class="stat-label">Schedules</div>
            <div class="stat-value"><?php echo $stats['total_schedules']; ?></div>
        </div>
        
        <div class="stat-card tickets">
            <div class="stat-icon">&#127915;</div>
            <div class="stat-label">Total Tickets</div>
            <div class="stat-value"><?php echo $stats['total_tickets']; ?></div>
        </div>
        
        <div class="stat-card bookings">
            <div class="stat-icon">&#128200;</div>
            <div class="stat-label">Active Bookings</div>
            <div class="stat-value"><?php echo $stats['active_bookings']; ?></div>
        </div>
    </div>
    
    <!-- Admin Modules -->
    <div class="admin-section">
        <!-- Bus Management -->
        <div class="admin-card">
            <div class="card-header">
                <div class="card-icon">&#128652;</div>
                <div class="card-title">
                    <h3>Bus Management</h3>
                    <p>Add & manage bus fleet</p>
                </div>
            </div>
            <div class="card-body">
                <p>Create new buses, update bus information, capacity management, and remove buses from the system.</p>
                <div class="btn-group">
                    <a href="<?php echo BASE_URL; ?>admin/add-bus" class="btn btn-primary">Add Bus</a>
                    <a href="<?php echo BASE_URL; ?>admin/buses" class="btn btn-secondary">View All</a>
                </div>
            </div>
        </div>
        
        <!-- Operator Management -->
        <div class="admin-card">
            <div class="card-header">
                <div class="card-icon">&#128119;</div>
                <div class="card-title">
                    <h3>Operator Management</h3>
                    <p>Create & manage operators</p>
                </div>
            </div>
            <div class="card-body">
                <p>Create new operators without registration, manage operator accounts, view operator statistics, and manage operator buses.</p>
                <div class="btn-group">
                    <a href="<?php echo BASE_URL; ?>admin/add-operator" class="btn btn-primary">Create Operator</a>
                    <a href="manage_users.php?role=operator" class="btn btn-secondary">View All</a>
                </div>
            </div>
        </div>
        
        <!-- User Management -->
        <div class="admin-card">
            <div class="card-header">
                <div class="card-icon">&#128652;</div>
                <div class="card-title">
                    <h3>User Management</h3>
                    <p>Manage all system users</p>
                </div>
            </div>
            <div class="card-body">
                <p>View all passengers, operators, and admin accounts. Verify users, manage permissions, and handle user accounts.</p>
                <div class="btn-group">
                    <a href="<?php echo BASE_URL; ?>admin/users" class="btn btn-secondary">View All Users</a>
                </div>
            </div>
        </div>
        
        <!-- Schedule Management -->
        <div class="admin-card">
            <div class="card-header">
                <div class="card-icon">&#128652;</div>
                <div class="card-title">
                    <h3>Schedule Management</h3>
                    <p>Manage trip schedules</p>
                </div>
            </div>
            <div class="card-body">
                <p>View all schedules created by operators, manage trip timings, capacity allocation, and schedule modifications.</p>
                <div class="btn-group">
                    <a href="<?php echo BASE_URL; ?>admin/schedules" class="btn btn-secondary">View Schedules</a>
                </div>
            </div>
        </div>
        
        <!-- Ticket Management -->
        <div class="admin-card">
            <div class="card-header">
                <div class="card-icon">&#128652;</div>
                <div class="card-title">
                    <h3>Ticket Management</h3>
                    <p>Manage bookings & tickets</p>
                </div>
            </div>
            <div class="card-body">
                <p>Monitor all ticket bookings, handle cancellations, view ticket details, and manage passenger reservations system-wide.</p>
                <div class="btn-group">
                    <a href="<?php echo BASE_URL; ?>admin/tickets" class="btn btn-secondary">View Tickets</a>
                </div>
            </div>
        </div>
        
        <!-- Reports & Analytics -->
        <div class="admin-card">
            <div class="card-header">
                <div class="card-icon">&#128652;</div>
                <div class="card-title">
                    <h3>Reports & Analytics</h3>
                    <p>View system reports</p>
                </div>
            </div>
            <div class="card-body">
                <p>Revenue reports, booking analytics, cancellation statistics, and system performance insights for better decision making.</p>
                <div class="btn-group">
                    <a href="<?php echo BASE_URL; ?>admin/reports" class="btn btn-secondary">View Reports</a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="quick-actions">
        <h3>? Quick Actions & Features</h3>
        <ul>
            <li>? <strong>Add Buses:</strong> Quickly add new buses with capacity and type information</li>
            <li>? <strong>Create Operators:</strong> Generate operator accounts directly without registration process</li>
            <li>? <strong>View Analytics:</strong> Track revenue, bookings, and system performance in real-time</li>
            <li>? <strong>Manage Users:</strong> Verify accounts, manage permissions, and handle user issues</li>
            <li>? <strong>Monitor Schedules:</strong> Oversee all operator-created schedules and trip timings</li>
            <li>? <strong>Handle Tickets:</strong> Process cancellations and manage booking issues</li>
        </ul>
    </div>

    <!-- All Available Schedules Section -->
    <div style="margin-top: 50px; padding: 30px; background: white; border-radius: 12px; box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);">
        <h3 style="font-size: 1.5rem; color: #0f1c33; margin-bottom: 10px; display: flex; align-items: center; gap: 10px;">&#128197; All Scheduled Trips</h3>
        <p style="color: #666; margin-bottom: 20px;">View all upcoming bus schedules in the system.</p>
        
        <?php 
        // Get all schedules with bus and operator information
        $all_schedules_query = "SELECT s.*, b.bus_name, b.total_seats, u.name as operator_name, u.phone as operator_phone
                                FROM schedules s 
                                JOIN buses b ON s.bus_id = b.id 
                                LEFT JOIN users u ON s.operator_id = u.id
                                WHERE s.departure_time >= DATE_SUB(NOW(), INTERVAL 1 DAY)
                                ORDER BY s.departure_time ASC 
                                LIMIT 30";
        $all_schedules_result = $conn->query($all_schedules_query);
        ?>
        
        <?php if ($all_schedules_result && $all_schedules_result->num_rows > 0): ?>
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-top: 20px;">
                <?php while ($schedule = $all_schedules_result->fetch_assoc()): ?>
                    <div style="background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%); border-radius: 10px; padding: 20px; box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08); transition: all 0.3s ease; border-left: 5px solid #0072ff; position: relative;" onmouseover="this.style.boxShadow='0 4px 20px rgba(0, 0, 0, 0.15)'; this.style.transform='translateY(-3px)';" onmouseout="this.style.boxShadow='0 2px 12px rgba(0, 0, 0, 0.08)'; this.style.transform='translateY(0)';">
                        <!-- Bus Name -->
                        <h4 style="color: #0f1c33; font-size: 1.15rem; margin-bottom: 8px; font-weight: 700;">&#128652; <?php echo htmlspecialchars($schedule['bus_name']); ?></h4>
                        
                        <!-- Route -->
                        <div style="color: #0f1c33; font-weight: 600; font-size: 1rem; margin-bottom: 12px; padding: 10px; background: #e3f2fd; border-radius: 6px; border-left: 3px solid #0072ff;">
                            ?? <strong><?php echo htmlspecialchars($schedule['source']); ?></strong> ? <strong><?php echo htmlspecialchars($schedule['destination']); ?></strong>
                        </div>
                        
                        <!-- Schedule Details -->
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px;">
                            <div>
                                <p style="color: #999; font-size: 0.85rem; margin-bottom: 2px;"><strong>&#128197; Date</strong></p>
                                <p style="color: #0f1c33; font-weight: 600; font-size: 0.95rem;"><?php echo date('M d, Y', strtotime($schedule['departure_time'])); ?></p>
                            </div>
                            <div>
                                <p style="color: #999; font-size: 0.85rem; margin-bottom: 2px;"><strong>&#128336; Time</strong></p>
                                <p style="color: #0f1c33; font-weight: 600; font-size: 0.95rem;"><?php echo date('H:i', strtotime($schedule['departure_time'])); ?></p>
                            </div>
                        </div>
                        
                        <!-- Seats & Operator -->
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px;">
                            <div>
                                <p style="color: #999; font-size: 0.85rem; margin-bottom: 2px;"><strong>&#128186; Seats</strong></p>
                                <p style="color: #0f1c33; font-weight: 600; font-size: 0.95rem;"><?php echo intval($schedule['total_seats']); ?> Available</p>
                            </div>
                            <div>
                                <p style="color: #999; font-size: 0.85rem; margin-bottom: 2px;"><strong>&#128119; Operator</strong></p>
                                <p style="color: #0f1c33; font-weight: 600; font-size: 0.95rem;"><?php echo htmlspecialchars($schedule['operator_name'] ?? 'Unassigned'); ?></p>
                            </div>
                        </div>
                        
                        <!-- Action Button -->
                        <a href="manage_schedules.php?id=<?php echo intval($schedule['id']); ?>" style="display: block; text-align: center; background: #0072ff; color: white; padding: 8px 12px; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 0.9rem; transition: all 0.3s ease; margin-top: 12px;" onmouseover="this.style.background='#0056cc'; this.style.boxShadow='0 2px 8px rgba(0, 114, 255, 0.4)';" onmouseout="this.style.background='#0072ff'; this.style.boxShadow='none';">View Details</a>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div style="background: #f5f5f5; padding: 40px; border-radius: 8px; text-align: center; color: #999;">
                <p style="font-size: 1rem;">No schedules available at the moment.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="<?php echo BASE_URL; ?>assets/js/script.js"></script>
</body>
</html>