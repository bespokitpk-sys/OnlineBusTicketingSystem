<?php
require_once APP_ROOT . '/config/db.php';
require_once APP_ROOT . '/app/core/Auth.php';
require_once APP_ROOT . '/app/models/Ticket.php';

requireRole('passenger');
$user = currentUser();
$tickets = Ticket::findByUser($user['id']);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>My Tickets - Passenger</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/qrcode/build/qrcode.min.js"></script>
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
        
        .ticket-card {
            display: grid;
            grid-template-columns: 1fr 200px;
            gap: 20px;
            align-items: center;
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 15px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }
        
        .ticket-info p {
            margin: 8px 0;
            font-size: 0.95rem;
        }
        
        .ticket-info strong {
            color: #0f1c33;
        }
        
        .ticket-info p span {
            color: #666;
        }
        
        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-top: 8px;
        }
        
        .status-pending { background: #fff3cd; color: #856404; }
        .status-approved { background: #d1e7dd; color: #0f5132; }
        .status-cancelled { background: #f8d7da; color: #842029; }
        .status-boarded { background: #cfe2ff; color: #084298; }
        
        .qr-code {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: #f9f9f9;
            border-radius: 8px;
            padding: 15px;
        }
        
        .qr-code canvas {
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .qr-label {
            font-size: 0.8rem;
            color: #999;
            margin-top: 8px;
            text-align: center;
        }
        
        @media (max-width: 768px) {
            .ticket-card {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
<nav>
    <h2><span style="font-size: 2.5rem; display: inline-block;">🚌</span> Book Smarter, Travel Better</h2>
    <div>
        <a href="<?php echo BASE_URL; ?>passenger/dashboard.php">Dashboard</a>
        <a href="<?php echo BASE_URL; ?>public/search.php">Search</a>
        <a href="<?php echo BASE_URL; ?>index.php">Home</a>
        <a href="<?php echo BASE_URL; ?>auth/logout.php">Logout</a>
    </div>
</nav>
<section class="page-banner">
    <div class="page-banner-content">
        <h2>Your Ticket History</h2>
        <p>View all your confirmed bus bookings, ticket statuses, and payment approvals in one professional dashboard.</p>
    </div>
</section>
<div class="results">
    <a href="<?php echo BASE_URL; ?>passenger/dashboard.php" class="back-button">← Back to Dashboard</a>
    <h2>My Tickets</h2>
    <?php if ($tickets && $tickets->num_rows > 0): ?>
        <?php while ($ticket = $tickets->fetch_assoc()): ?>
            <div class="ticket-card">
                <div class="ticket-info">
                    <p><strong>🚌 Bus:</strong> <span><?php echo htmlspecialchars($ticket['bus_name']); ?></span></p>
                    <p><strong>📍 Route:</strong> <span><?php echo htmlspecialchars($ticket['source']); ?> → <?php echo htmlspecialchars($ticket['destination']); ?></span></p>
                    <p><strong>📅 Date:</strong> <span><?php echo date('Y-m-d H:i', strtotime($ticket['departure_time'])); ?></span></p>
                    <p><strong>💺 Seats:</strong> <span><?php echo intval($ticket['seats']); ?></span></p>
                    <p><strong>🎫 Ticket ID:</strong> <span>#<?php echo intval($ticket['id']); ?></span></p>
                    <p><strong>Status:</strong> <span class="status-badge status-<?php echo strtolower($ticket['status']); ?>"><?php echo htmlspecialchars(ucfirst($ticket['status'])); ?></span></p>
                </div>
                <div class="qr-code">
                    <canvas id="qr-<?php echo $ticket['id']; ?>" width="180" height="180"></canvas>
                    <div class="qr-label">Scan for boarding</div>
                    <div style="margin-top: 15px; display: flex; flex-direction: column; gap: 8px; width: 100%;">
                        <a href="<?php echo BASE_URL; ?>public/download_ticket.php?ticket_id=<?php echo $ticket['id']; ?>&format=pdf" 
                           style="display: block; padding: 10px; background: #0072ff; color: white; text-decoration: none; border-radius: 6px; font-weight: bold; text-align: center; transition: all 0.3s ease; font-size: 14px;"
                           onmouseover="this.style.background='#0056cc'" 
                           onmouseout="this.style.background='#0072ff'">
                            📥 Download PDF
                        </a>
                        <a href="<?php echo BASE_URL; ?>public/download_ticket.php?ticket_id=<?php echo $ticket['id']; ?>&format=print" 
                           style="display: block; padding: 10px; background: #28a745; color: white; text-decoration: none; border-radius: 6px; font-weight: bold; text-align: center; transition: all 0.3s ease; font-size: 14px;"
                           onmouseover="this.style.background='#218838'" 
                           onmouseout="this.style.background='#28a745'">
                            🖨️ Print Ticket
                        </a>
                    </div>
                </div>
            </div>
            <script>
                QRCode.toCanvas(document.getElementById('qr-<?php echo $ticket['id']; ?>'), '<?php echo $ticket['id']; ?>', {
                    width: 180,
                    margin: 1,
                    color: {
                        dark: '#0f1c33',
                        light: '#ffffff'
                    }
                });
            </script>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="ticket-card">
            <p>You have no booked tickets yet. Start by searching for a bus.</p>
        </div>
    <?php endif; ?>
</div>
</body>
</html>