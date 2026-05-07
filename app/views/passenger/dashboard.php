<?php
require_once APP_ROOT . '/config/db.php';
require_once APP_ROOT . '/app/core/Auth.php';
requireRole('passenger');
$user = currentUser();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Passenger Dashboard - Bus Ticketing System</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
    <script src="<?php echo BASE_URL; ?>assets/js/script.js"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
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
        
        .dashboard h2 {
            color: #0f1c33;
            font-size: 2rem;
            margin-bottom: 15px;
            font-weight: 700;
        }
        
        .dashboard p {
            color: #666;
            font-size: 1.1rem;
            margin-bottom: 30px;
            line-height: 1.6;
        }
        
        .page-banner {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 60px 40px;
            text-align: center;
        }
        
        .page-banner-content h2 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            color: white;
        }
        
        .page-banner-content p {
            font-size: 1.1rem;
            opacity: 0.9;
            color: white;
        }
        
        .dashboard {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 40px;
        }
        
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(90deg, #0072ff 0%, #0056cc 100%);
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin-right: 10px;
            transition: all 0.3s ease;
            cursor: pointer;
            border: none;
            font-size: 1rem;
        }
        
        .btn:hover {
            box-shadow: 0 4px 12px rgba(0, 114, 255, 0.4);
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
<script src="<?php echo BASE_URL; ?>assets/js/script.js"></script>
<nav>
    <h2><span style="font-size: 2.5rem; display: inline-block;">🚌</span> Book Smarter, Travel Better</h2>
    <div>
        <a href="<?php echo BASE_URL; ?>">Home</a>
        <a href="<?php echo BASE_URL; ?>search">Search</a>
        <a href="<?php echo BASE_URL; ?>passenger/my-tickets">My Tickets</a>
        <a href="<?php echo BASE_URL; ?>logout">Logout</a>
    </div>
</nav>
<section class="page-banner">
    <div class="page-banner-content">
        <h2>Passenger Dashboard</h2>
        <p>Manage your bus bookings with confidence. Official ticket receipts and operator-approved payments are handled in one place.</p>
        <a href="<?php echo BASE_URL; ?>search" class="btn">Book a Bus</a>
    </div>
</section>
<div class="dashboard">
    <h2>Welcome, <?php echo htmlspecialchars($user['name']); ?></h2>
    <p>Use this dashboard to search buses, book tickets, and view your booking history.</p>
    <a href="<?php echo BASE_URL; ?>search" class="btn">Search Buses</a>
    <a href="<?php echo BASE_URL; ?>passenger/my-tickets" class="btn">My Tickets</a>
</div>
</body>
</html>