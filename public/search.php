<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Search Buses - Book smarter, travel better</title>
    <?php 
    require_once __DIR__ . '/../config/db.php';
    require_once __DIR__ . '/../includes/auth.php';
    
    // Get all upcoming schedules
    $schedules_query = "SELECT schedules.*, buses.bus_name, buses.total_seats 
                        FROM schedules 
                        JOIN buses ON schedules.bus_id = buses.id 
                        WHERE departure_time >= DATE_SUB(NOW(), INTERVAL 1 DAY)
                        ORDER BY departure_time ASC 
                        LIMIT 30";
    $all_schedules = $conn->query($schedules_query);
    $userIsPassenger = isLoggedIn() && ($_SESSION['user_role'] ?? '') === 'passenger';
    ?>
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
        
        .schedules-section {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 40px;
        }
        
        .schedules-section h2 {
            color: #0f1c33;
            margin-bottom: 20px;
            font-size: 1.8rem;
            font-weight: 700;
        }
        
        .schedules-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-top: 20px;
        }
        
        @media (max-width: 1024px) {
            .schedules-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        @media (max-width: 640px) {
            .schedules-grid {
                grid-template-columns: 1fr;
            }
        }
        
        .schedule-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            border-left: 5px solid #0072ff;
        }
        
        .schedule-card:hover {
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12);
            transform: translateY(-3px);
        }
        
        .schedule-card h3 {
            color: #0f1c33;
            font-size: 1.2rem;
            margin-bottom: 10px;
            font-weight: 700;
        }
        
        .schedule-card p {
            color: #666;
            margin: 8px 0;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .schedule-card .route {
            color: #0f1c33;
            font-weight: 600;
            font-size: 1rem;
            margin-bottom: 12px;
            padding: 8px;
            background: #f0f7ff;
            border-radius: 4px;
        }
        
        .schedule-card .btn {
            width: 100%;
            margin-top: 15px;
            padding: 10px 15px;
            background: linear-gradient(90deg, #0072ff 0%, #0056cc 100%);
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
            border: none;
            font-size: 0.95rem;
            display: block;
        }
        
        .schedule-card .btn:hover {
            box-shadow: 0 4px 12px rgba(0, 114, 255, 0.4);
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
<nav>
    <h2><span style="font-size: 2.5rem; display: inline-block;">🚌</span> Book Smarter, Travel Better</h2>
    <div>
        <a href="<?php echo BASE_URL; ?>index.php">Home</a>
        <a href="<?php echo BASE_URL; ?>public/passenger_login.php">Passenger Login</a>
        <a href="<?php echo BASE_URL; ?>public/passenger_register.php">Register</a>
    </div>
</nav>
<section class="page-banner">
    <div class="page-banner-content">
        <h2>Find Your Next Bus Trip</h2>
        <p>Search destinations, compare schedules, and confirm your reservation with our official bus ticketing system.</p>
        <a href="<?php echo BASE_URL; ?>public/passenger_login.php" class="btn">Passenger Login</a>
    </div>
</section>
<div class="search-box">
    <a href="<?php echo BASE_URL; ?>index.php" class="back-button">← Back to Home</a>
    <h2>Find Your Bus</h2>
    <form action="results.php" method="GET">
        <input type="text" name="source" placeholder="From" list="city-list" required>
        <input type="text" name="destination" placeholder="To" list="city-list" required>
        <input type="date" name="date" required>
        <button type="submit">Search</button>
    </form>
    <datalist id="city-list">
        <option value="Karachi">
        <option value="Lahore">
        <option value="Islamabad">
        <option value="Kharian">
    </datalist>
</div>

<!-- All Available Schedules Section -->
<div class="schedules-section">
    <h2>📅 Available Bus Schedules</h2>
    <p style="color: #666; margin-bottom: 20px;">Browse all upcoming buses or use the search above to filter by route and date.</p>
    
    <?php if ($all_schedules && $all_schedules->num_rows > 0): ?>
        <div class="schedules-grid">
            <?php while ($schedule = $all_schedules->fetch_assoc()): ?>
                <div class="schedule-card">
                    <h3><?php echo htmlspecialchars($schedule['bus_name']); ?></h3>
                    
                    <div class="route">
                        📍 <?php echo htmlspecialchars($schedule['source']); ?> → <?php echo htmlspecialchars($schedule['destination']); ?>
                    </div>
                    
                    <p><strong>📅 Date:</strong> <?php echo date('M d, Y', strtotime($schedule['departure_time'])); ?></p>
                    <p><strong>🕐 Time:</strong> <?php echo date('H:i', strtotime($schedule['departure_time'])); ?></p>
                    <p><strong>💺 Seats:</strong> <?php echo intval($schedule['total_seats']); ?></p>
                    
                    <?php if ($userIsPassenger): ?>
                        <a href="<?php echo BASE_URL; ?>passenger/book_ticket.php?schedule_id=<?php echo intval($schedule['id']); ?>" class="btn">Book Now</a>
                    <?php else: ?>
                        <a href="<?php echo BASE_URL; ?>public/passenger_login.php" class="btn">Login to Book</a>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p style="color: #999; text-align: center; padding: 40px;">No schedules available at the moment.</p>
    <?php endif; ?>
</div>

</body>
</html>