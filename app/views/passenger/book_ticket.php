<?php
require_once APP_ROOT . '/config/db.php';
require_once APP_ROOT . '/app/core/Auth.php';
requireRole('passenger');

$schedule_id = intval($_GET['schedule_id'] ?? 0);
$schedule = null;
if ($schedule_id > 0) {
    $result = $conn->query("SELECT schedules.*, buses.bus_name, buses.total_seats FROM schedules JOIN buses ON schedules.bus_id = buses.id WHERE schedules.id = $schedule_id");
    $schedule = $result ? $result->fetch_assoc() : null;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Book Ticket - Passenger</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
    <script src="<?php echo BASE_URL; ?>assets/js/script.js"></script>
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
    </style>
</head>
<body>
<nav>
    <h2><span style="font-size: 2.5rem; display: inline-block;">🚌</span> Book Smarter, Travel Better</h2>
    <div>
        <a href="<?php echo BASE_URL; ?>passenger/dashboard.php">Dashboard</a>
        <a href="<?php echo BASE_URL; ?>passenger/my_ticket.php">My Tickets</a>
        <a href="<?php echo BASE_URL; ?>index.php">Home</a>
        <a href="<?php echo BASE_URL; ?>auth/logout.php">Logout</a>
    </div>
</nav>
<section class="page-banner">
    <div class="page-banner-content">
        <h2>Book Your Bus Ticket</h2>
        <p>Choose your route, confirm your seats, and complete the on-site payment process with the operator.</p>
    </div>
</section>
<div class="form-container">
    <a href="<?php echo BASE_URL; ?>public/search.php" class="back-button">← Back to Search</a>
    <h2>Book Ticket</h2>
    <?php if ($schedule): ?>
        <p><strong>Bus:</strong> <?php echo htmlspecialchars($schedule['bus_name']); ?></p>
        <p><strong>Route:</strong> <?php echo htmlspecialchars($schedule['source']); ?> → <?php echo htmlspecialchars($schedule['destination']); ?></p>
        <p><strong>Departure:</strong> <?php echo date('Y-m-d H:i', strtotime($schedule['departure_time'])); ?></p>
        <form method="POST" action="<?php echo BASE_URL; ?>public/booking_handler.php">
            <input type="hidden" name="schedule_id" value="<?php echo intval($schedule_id); ?>">
            <input type="number" name="seats" placeholder="Number of seats" min="1" max="<?php echo intval($schedule['total_seats']); ?>" required>
            <button type="submit">Confirm Booking</button>
        </form>
    <?php else: ?>
        <p>No schedule selected. Go back to <a href="<?php echo BASE_URL; ?>public/search.php">search</a> and choose a bus.</p>
    <?php endif; ?>
</div>
</body>
</html>