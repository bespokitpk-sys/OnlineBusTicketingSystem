<?php
require_once APP_ROOT . '/config/db.php';
require_once APP_ROOT . '/app/core/Auth.php';
require_once APP_ROOT . '/app/controllers/OperatorController.php';

requireRole('operator');
$operator = currentUser();

$result = $conn->query("SELECT schedules.*, buses.bus_name FROM schedules JOIN buses ON schedules.bus_id = buses.id ORDER BY schedules.departure_time DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Schedules - Book Smarter, Travel Better</title>
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
        
        .dashboard { max-width: 1200px; margin: 0 auto; padding: 30px 20px; }
        
        .page-header {
            background: linear-gradient(135deg, #0072ff 0%, #0056cc 100%);
            color: white;
            padding: 40px;
            border-radius: 12px;
            margin-bottom: 30px;
            box-shadow: 0 4px 12px rgba(0, 114, 255, 0.15);
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
        
        .schedules-container {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 24px;
            margin: 30px 0;
        }
        .schedule-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
        }
        .schedule-card:hover {
            box-shadow: 0 4px 16px rgba(0, 114, 255, 0.15);
            transform: translateY(-2px);
        }
        .schedule-header {
            background: linear-gradient(135deg, #0072ff 0%, #0056cc 100%);
            color: white;
            padding: 20px;
            flex-grow: 1;
        }
        .schedule-route {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 8px;
        }
        .schedule-bus {
            font-size: 0.9rem;
            opacity: 0.9;
            margin-bottom: 12px;
        }
        .trip-status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.4);
        }
        .trip-status-badge.scheduled {
            background: #fff3cd;
            color: #856404;
            border-color: #ffc107;
        }
        .trip-status-badge.ongoing {
            background: #d1ecf1;
            color: #0c5460;
            border-color: #17a2b8;
        }
        .trip-status-badge.completed {
            background: #d4edda;
            color: #155724;
            border-color: #28a745;
        }
        .schedule-body {
            padding: 20px;
            flex-grow: 1;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            font-size: 0.9rem;
        }
        .detail-label {
            color: #666;
            font-weight: 600;
        }
        .detail-value {
            color: #0f1c33;
            font-weight: 500;
        }
        .schedule-footer {
            padding: 16px 20px;
            border-top: 1px solid #e0e0e0;
            display: flex;
            gap: 10px;
        }
        .schedule-footer a {
            flex: 1;
            text-align: center;
            padding: 10px 12px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.85rem;
            transition: all 0.3s ease;
        }
        .btn-view {
            background: #0072ff;
            color: white;
        }
        .btn-view:hover {
            background: #0056cc;
        }
        .empty-state {
            grid-column: 1 / -1;
            text-align: center;
            padding: 60px 40px;
            background: white;
            border-radius: 12px;
            color: #999;
        }
        @media (max-width: 1400px) {
            .schedules-container {
                grid-template-columns: repeat(3, 1fr);
            }
        }
        
        @media (max-width: 1024px) {
            .schedules-container {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        @media (max-width: 768px) {
            .schedules-container {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
<nav>
    <h2><span style="font-size: 2.5rem; display: inline-block;">🚌</span> Book Smarter, Travel Better</h2>
    <div>
        <a href="<?php echo BASE_URL; ?>operator/dashboard">Dashboard</a>
        <a href="<?php echo BASE_URL; ?>operator/add-schedule">Add Schedule</a>
        <a href="<?php echo BASE_URL; ?>operator/schedules">My Schedules</a>
        <a href="<?php echo BASE_URL; ?>">Home</a>
        <a href="<?php echo BASE_URL; ?>logout">Logout</a>
    </div>
</nav>
<div class="dashboard">
    <div class="page-header">
        <a href="javascript:history.back()" class="back-btn">← Back</a>
        <h2>📅 Manage Schedules</h2>
        <p>View and manage all your scheduled trips.</p>
    </div>

    <?php if ($result && $result->num_rows > 0): ?>
        <div class="schedules-container">
            <?php while ($schedule = $result->fetch_assoc()): 
                $tripStatus = $schedule['trip_status'] ?? 'scheduled';
                $summary = OperatorController::getTripSummary($schedule['id']);
            ?>
                <div class="schedule-card">
                    <div class="schedule-header">
                        <div class="schedule-route">
                            <?php echo htmlspecialchars($schedule['source']); ?> → <?php echo htmlspecialchars($schedule['destination']); ?>
                        </div>
                        <div class="schedule-bus">
                            🚌 <?php echo htmlspecialchars($schedule['bus_name']); ?>
                        </div>
                        <span class="trip-status-badge <?php echo $tripStatus; ?>">
                            <?php echo ucfirst($tripStatus); ?>
                        </span>
                    </div>
                    <div class="schedule-body">
                        <div class="detail-row">
                            <span class="detail-label">Departure</span>
                            <span class="detail-value"><?php echo date('M d, H:i', strtotime($schedule['departure_time'])); ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Bookings</span>
                            <span class="detail-value"><?php echo $summary['total_tickets'] ?? 0; ?> / <?php echo $summary['total_seats'] ?? 0; ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Pending</span>
                            <span class="detail-value" style="color: #ffc107;"><?php echo $summary['pending_count'] ?? 0; ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Boarded</span>
                            <span class="detail-value" style="color: #28a745;"><?php echo $summary['boarded_count'] ?? 0; ?></span>
                        </div>
                    </div>
                    <div class="schedule-footer">
                        <a href="<?php echo BASE_URL; ?>operator/dashboard#schedule-<?php echo $schedule['id']; ?>" class="btn-view">View Details</a>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <h3>No Schedules Found</h3>
            <p>You don't have any scheduled trips yet.</p>
            <a href="<?php echo BASE_URL; ?>operator/add-schedule" class="btn">Create New Schedule</a>
        </div>
    <?php endif; ?>
</div>

</body>
</html>