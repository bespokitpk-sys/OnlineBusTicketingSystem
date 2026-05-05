<?php
require_once APP_ROOT . '/config/db.php';
require_once APP_ROOT . '/app/core/Auth.php';

$userIsPassenger = isLoggedIn() && ($_SESSION['user_role'] ?? '') === 'passenger';

function normalizeCity($name) {
    $name = strtolower(trim($name));
    $name = preg_replace('/[^a-z]/', '', $name);
    $aliases = [
        'karachi' => ['karachi', 'karach', 'khari', 'khrian'],
        'lahore' => ['lahore', 'lahor', 'lhr'],
        'islamabad' => ['islamabad', 'islamaabd', 'islambad', 'islambd', 'isb'],
        'kharian' => ['kharian', 'khrian', 'khriyan'],
    ];

    foreach ($aliases as $canonical => $values) {
        if ($name === $canonical) {
            return ucfirst($canonical);
        }
        foreach ($values as $alias) {
            if ($name === $alias) {
                return ucfirst($canonical);
            }
        }
    }

    return ucfirst($name);
}

$source = trim($_GET['source'] ?? '');
$destination = trim($_GET['destination'] ?? '');
$date = trim($_GET['date'] ?? '');

$where = [];
if ($source !== '') {
    $sourceNormalized = normalizeCity($source);
    $where[] = "LOWER(schedules.source) = '" . $conn->real_escape_string(strtolower($sourceNormalized)) . "'";
}
if ($destination !== '') {
    $destinationNormalized = normalizeCity($destination);
    $where[] = "LOWER(schedules.destination) = '" . $conn->real_escape_string(strtolower($destinationNormalized)) . "'";
}
if ($date !== '') {
    $where[] = "DATE(schedules.departure_time) = '" . $conn->real_escape_string($date) . "'";
}

$sql = "SELECT schedules.*, buses.bus_name, buses.total_seats FROM schedules JOIN buses ON schedules.bus_id = buses.id";
if (!empty($where)) {
    $sql .= ' WHERE ' . implode(' AND ', $where);
}
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Search Results - Book smarter, travel better</title>
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
        
        .bus-info {
            flex: 1;
        }
        
        .bus-info h3 {
            font-size: 1.4rem;
            color: #0f1c33;
            margin-bottom: 12px;
            font-weight: 700;
        }
        
        .route-info {
            margin-bottom: 15px;
            padding: 10px;
            background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%);
            border-left: 4px solid #0072ff;
            border-radius: 4px;
        }
        
        .route-info p {
            margin: 0;
            color: #0f1c33;
            font-size: 1.05rem;
            font-weight: 600;
        }
        
        .schedule-info {
            background: #f9f9f9;
            padding: 12px;
            border-radius: 6px;
            border: 1px solid #e0e0e0;
        }
        
        .schedule-info p {
            margin: 8px 0;
            color: #333;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .schedule-info strong {
            color: #0f1c33;
            min-width: 140px;
        }
        
        .results {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }
        
        .results h2 {
            color: #0f1c33;
            margin-bottom: 20px;
            font-size: 1.8rem;
        }
        
        .bus-card {
            background: white;
            padding: 20px;
            margin-bottom: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
            display: flex;
            align-items: center;
            gap: 20px;
            transition: all 0.3s ease;
        }
        
        .bus-card:hover {
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12);
            transform: translateY(-2px);
        }
        
        .bus-card-inner {
            flex: 1;
            display: flex;
            gap: 20px;
        }
        
        .bus-image {
            width: 150px;
            height: 150px;
            border-radius: 8px;
            overflow: hidden;
            flex-shrink: 0;
        }
        
        .bus-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .btn {
            padding: 12px 30px;
            background: linear-gradient(90deg, #0072ff 0%, #0056cc 100%);
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            white-space: nowrap;
            transition: all 0.3s ease;
            cursor: pointer;
            border: none;
            font-size: 1rem;
            display: inline-block;
        }
        
        .btn:hover {
            box-shadow: 0 4px 12px rgba(0, 114, 255, 0.4);
            transform: translateY(-2px);
        }
        
        .back-button {
            display: inline-block;
            color: #0072ff;
            text-decoration: none;
            font-weight: 600;
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }
        
        .back-button:hover {
            color: #0056cc;
            text-decoration: underline;
        }
    </style>
</head>
<body>
<script src="../assets/js/script.js"></script>
<nav>
    <h2><span style="font-size: 2.5rem; display: inline-block;">🚌</span> Book Smarter, Travel Better</h2>
    <div>
        <a href="<?php echo BASE_URL; ?>index.php">Home</a>
        <a href="search.php">Modify Search</a>
        <a href="<?php echo BASE_URL; ?>public/passenger_login.php">Login</a>
    </div>
</nav>
<div class="results">
    <a href="search.php" class="back-button">← Back to Search</a>
    <h2>Available Buses</h2>
    <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="bus-card">
                <div class="bus-card-inner">
                    <div class="bus-image">
                        <img src="https://images.unsplash.com/photo-1491553895911-0055eca6402d?auto=format&fit=crop&w=800&q=80" alt="Bus image">
                    </div>
                    <div class="bus-info">
                        <h3><?php echo htmlspecialchars($row['bus_name']); ?></h3>
                        <div class="route-info">
                            <p class="route"><strong>📍 Route:</strong> <?php echo htmlspecialchars($row['source']); ?> → <?php echo htmlspecialchars($row['destination']); ?></p>
                        </div>
                        <div class="schedule-info">
                            <p><strong>📅 Date:</strong> <?php echo date('l, M d, Y', strtotime($row['departure_time'])); ?></p>
                            <p><strong>🕐 Departure Time:</strong> <?php echo date('H:i', strtotime($row['departure_time'])); ?></p>
                            <p><strong>💺 Available Seats:</strong> <?php echo intval($row['total_seats']); ?></p>
                        </div>
                    </div>
                </div>
                <?php if ($userIsPassenger): ?>
                    <a class="btn" href="<?php echo BASE_URL; ?>passenger/book_ticket.php?schedule_id=<?php echo intval($row['id']); ?>">Book Now</a>
                <?php else: ?>
                    <a class="btn" href="<?php echo BASE_URL; ?>public/passenger_login.php">Login to Book</a>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="ticket">
            <p>No buses found. Please try a different route or date.</p>
        </div>
    <?php endif; ?>
</div>
</body>
</html>