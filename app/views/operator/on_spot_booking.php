<?php
require_once APP_ROOT . '/config/db.php';
require_once APP_ROOT . '/app/core/Auth.php';
require_once APP_ROOT . '/app/controllers/OperatorController.php';

requireRole('operator');
$operator = currentUser();

$schedule_id = intval($_GET['schedule_id'] ?? 0);
if ($schedule_id == 0) {
    header('Location: dashboard.php?error=invalid_schedule');
    exit;
}

$schedule = OperatorController::getScheduleById($schedule_id);
if (!$schedule) {
    header('Location: dashboard.php?error=schedule_not_found');
    exit;
}

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = intval($_POST['user_id'] ?? 0);
    $seats = intval($_POST['seats'] ?? 0);

    if ($user_id == 0 || $seats <= 0) {
        $messageType = 'error';
        $message = 'Please select a passenger and enter number of seats.';
    } else {
        $result = OperatorController::onSpotBooking($schedule_id, $user_id, $seats);
        $messageType = $result['success'] ? 'success' : 'error';
        $message = $result['message'];
        
        if ($result['success']) {
            // Clear form
            $user_id = 0;
            $seats = 0;
        }
    }
}

$availablePassengers = OperatorController::getAvailablePassengers();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>On-Spot Booking - Book Smarter, Travel Better</title>
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
        
        .dashboard { max-width: 700px; margin: 0 auto; padding: 30px 20px; }
        
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
        
        .booking-container {
            max-width: 600px;
            margin: 30px auto;
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        .booking-info {
            background: linear-gradient(135px, #e8f4f8 0%, #d4e9f7 100%);
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 24px;
            border-left: 4px solid #0072ff;
        }
        .booking-route {
            font-size: 1.2rem;
            font-weight: 600;
            color: #0f1c33;
            margin-bottom: 8px;
        }
        .booking-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-top: 12px;
        }
        .booking-detail {
            font-size: 0.9rem;
            color: #666;
        }
        .booking-label {
            font-weight: 600;
            color: #0072ff;
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
        <h2>🎫 On-Spot Booking</h2>
        <p>Add passengers to this trip during the journey.</p>
    </div>

    <div class="booking-container">
        <div class="booking-info">
            <div class="booking-route">
                <?php echo htmlspecialchars($schedule['source']); ?> → <?php echo htmlspecialchars($schedule['destination']); ?>
            </div>
            <div class="booking-details">
                <div class="booking-detail">
                    <span class="booking-label">Bus:</span> <?php echo htmlspecialchars($schedule['bus_name']); ?>
                </div>
                <div class="booking-detail">
                    <span class="booking-label">Departure:</span> <?php echo date('M d, H:i', strtotime($schedule['departure_time'])); ?>
                </div>
                <div class="booking-detail">
                    <span class="booking-label">Total Bookings:</span> <?php echo intval($schedule['total_bookings']); ?>
                </div>
                <div class="booking-detail">
                    <span class="booking-label">Capacity:</span> <?php echo intval($schedule['total_seats']); ?> seats
                </div>
            </div>
        </div>

        <?php if ($message): ?>
            <div class="<?php echo $messageType === 'success' ? 'success-message' : 'error-message'; ?>" style="margin-bottom: 20px;">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="form-container" style="width: 100%; margin: 0; padding: 0;">
            <div class="form-group">
                <label for="user_id">Select Passenger *</label>
                <select id="user_id" name="user_id" required>
                    <option value="">-- Choose Passenger --</option>
                    <?php foreach ($availablePassengers as $passenger): ?>
                        <option value="<?php echo $passenger['id']; ?>">
                            <?php echo htmlspecialchars($passenger['name']); ?> (<?php echo htmlspecialchars($passenger['phone']); ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (count($availablePassengers) == 0): ?>
                    <small style="color: #d32f2f;">No verified passengers available</small>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="seats">Number of Seats *</label>
                <input type="number" id="seats" name="seats" placeholder="1" min="1" value="1" required>
            </div>

            <div style="display: flex; gap: 12px; margin-top: 24px;">
                <button type="submit" class="btn-primary">Add Booking</button>
                <a href="<?php echo BASE_URL; ?>operator/dashboard" class="btn" style="background: #6c757d; text-decoration: none; text-align: center;">Back</a>
            </div>
        </form>

        <div style="background: #f0f4f8; padding: 20px; border-radius: 8px; margin-top: 30px; border-left: 4px solid #0072ff;">
            <h3>ℹ️ On-Spot Booking Info</h3>
            <ul style="margin: 10px 0; padding-left: 20px; color: #666;">
                <li>Select a verified passenger from the list</li>
                <li>Enter the number of seats to add</li>
                <li>The booking will be created with <strong>"Boarded"</strong> status</li>
                <li>Payment approval is skipped for on-spot bookings</li>
                <li>Passenger will be marked as boarded immediately</li>
            </ul>
        </div>
    </div>
</div>

</body>
</html>
