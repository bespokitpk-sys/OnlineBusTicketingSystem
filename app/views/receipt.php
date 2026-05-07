<?php
require_once APP_ROOT . '/config/db.php';
require_once APP_ROOT . '/app/core/Auth.php';
require_once APP_ROOT . '/app/models/Ticket.php';

requireRole('passenger');
$ticket_id = intval($_GET['ticket_id'] ?? 0);
$ticket = Ticket::findById($ticket_id);
if (!$ticket || $ticket['user_id'] !== $_SESSION['user_id']) {
    header('Location: ' . BASE_URL . 'search');
    exit;
}
$ticket_code = 'TICKET-' . $ticket['id'];
// Generate QR code that contains a FULL ABSOLUTE URL - when scanned on mobile, it opens the verification page
// Use the ACTUAL network IP (WiFi) instead of VPN tunnel IP so phone can access it
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';

// Try multiple methods to get the real network IP (not VPN IPs)
$server_ip = null;

// Method 1: Check if accessing via domain/hostname
if (isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], '.test') !== false) {
    // If accessing via .test domain, resolve it properly
    $hostname = gethostname();
    $server_ip = gethostbyname($hostname);
}

// Method 2: Use SERVER_ADDR if it's not localhost/VPN
if (!$server_ip || $server_ip === '127.0.0.1' || (strpos($server_ip, '10.') === 0 && strpos($server_ip, '10.14') === 0)) {
    // 10.14.x.x is typically a VPN/tunnel, so skip it and try to get WiFi IP
    if (isset($_SERVER['SERVER_ADDR']) && $_SERVER['SERVER_ADDR'] !== '127.0.0.1') {
        $server_ip = $_SERVER['SERVER_ADDR'];
    }
}

// Method 3: Fallback to HTTP_HOST (usually the hostname.test domain)
if (!$server_ip) {
    $server_ip = $_SERVER['HTTP_HOST'] ?? '127.0.0.1';
}

// For testing: If still localhost-like, try to get REMOTE_ADDR context
// In most local setups, use the hostname which resolves correctly
if ($server_ip === '127.0.0.1' || strpos($server_ip, '10.14') === 0) {
    // Fall back to using the HTTP_HOST (busticketingsystem.test) which should work locally
    $server_ip = gethostname() . '.local';
}

$verify_url = $protocol . $server_ip . '/public/verify_ticket.php?ticket_id=' . $ticket['id'] . '&code=' . $ticket_code;
$qr_url = "https://api.qrserver.com/v1/create-qr-code/?size=280x280&data=" . urlencode($verify_url);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Receipt - Book smarter, travel better</title>
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
        
        .receipt-container {
            max-width: 1000px;
            margin: 30px auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .receipt-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            padding: 40px;
            align-items: center;
        }
        
        .receipt-details {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        
        .receipt-details h2 {
            font-size: 1.8rem;
            color: #0f1c33;
            margin-bottom: 15px;
            font-weight: 700;
            border-bottom: 3px solid #0072ff;
            padding-bottom: 10px;
        }
        
        .receipt-details p {
            font-size: 1rem;
            color: #333;
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        
        .receipt-details strong {
            color: #0f1c33;
            font-weight: 700;
            min-width: 150px;
        }
        
        .receipt-qr-section {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 15px;
            border: 2px dashed #0072ff;
            padding: 30px;
            border-radius: 8px;
            background: #f8f9fa;
        }
        
        .receipt-qr-section img {
            width: 280px;
            height: 280px;
            border: 3px solid #0072ff;
            border-radius: 8px;
            padding: 10px;
            background: white;
            box-shadow: 0 2px 8px rgba(0, 114, 255, 0.2);
        }
        
        .qr-label {
            font-weight: 700;
            color: #0f1c33;
            font-size: 1.1rem;
            text-align: center;
        }
        
        .qr-instruction {
            font-size: 0.9rem;
            color: #666;
            text-align: center;
            font-style: italic;
        }
        
        .receipt-actions {
            padding: 20px 40px 40px;
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .receipt-actions a {
            padding: 12px 24px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .receipt-actions a.pdf {
            background: #0072ff;
            color: white;
        }
        
        .receipt-actions a.pdf:hover {
            background: #0056cc;
            box-shadow: 0 4px 12px rgba(0, 114, 255, 0.3);
            transform: translateY(-2px);
        }
        
        .receipt-actions a.print {
            background: #28a745;
            color: white;
        }
        
        .receipt-actions a.print:hover {
            background: #218838;
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
            transform: translateY(-2px);
        }
        
        .receipt-actions a.back {
            background: #6c757d;
            color: white;
        }
        
        .receipt-actions a.back:hover {
            background: #5a6268;
            box-shadow: 0 4px 12px rgba(108, 117, 125, 0.3);
            transform: translateY(-2px);
        }
        
        @media (max-width: 768px) {
            nav {
                padding: 15px 20px;
                flex-wrap: wrap;
                min-height: auto;
            }
            
            nav h2 {
                font-size: 1.3rem;
                width: 100%;
                margin-bottom: 10px;
            }
            
            nav div {
                width: 100%;
                justify-content: flex-start;
                gap: 8px;
            }
            
            nav a {
                font-size: 0.75rem;
                padding: 8px 12px;
                height: 34px;
            }
            
            .receipt-container {
                margin: 20px 10px;
                border-radius: 8px;
            }
            
            .receipt-content {
                grid-template-columns: 1fr;
                gap: 20px;
                padding: 20px;
                align-items: stretch;
            }
            
            .receipt-details {
                gap: 10px;
            }
            
            .receipt-details h2 {
                font-size: 1.3rem;
                margin-bottom: 12px;
                padding-bottom: 8px;
            }
            
            .receipt-details p {
                font-size: 0.9rem;
                flex-direction: column;
                gap: 4px;
            }
            
            .receipt-details strong {
                min-width: auto;
                color: #0072ff;
            }
            
            .receipt-qr-section {
                gap: 12px;
                padding: 20px;
                border-width: 2px;
            }
            
            .receipt-qr-section img {
                width: 220px;
                height: 220px;
                border-width: 2px;
            }
            
            .qr-label {
                font-size: 1rem;
            }
            
            .qr-instruction {
                font-size: 0.85rem;
            }
            
            .receipt-actions {
                padding: 15px 20px 20px;
                gap: 10px;
                flex-direction: column;
            }
            
            .receipt-actions a {
                width: 100%;
                justify-content: center;
                padding: 12px 16px;
                font-size: 0.95rem;
            }
        }
        
        @media (max-width: 480px) {
            nav {
                padding: 12px 15px;
            }
            
            nav h2 {
                font-size: 1.1rem;
                gap: 5px;
            }
            
            nav h2 span {
                font-size: 1.8rem !important;
            }
            
            nav a {
                font-size: 0.7rem;
                padding: 6px 10px;
                height: 32px;
            }
            
            .receipt-container {
                margin: 15px 8px;
            }
            
            .receipt-content {
                gap: 15px;
                padding: 15px;
            }
            
            .receipt-details h2 {
                font-size: 1.1rem;
                margin-bottom: 10px;
            }
            
            .receipt-details p {
                font-size: 0.8rem;
                padding: 6px 0;
                gap: 3px;
            }
            
            .receipt-qr-section {
                gap: 10px;
                padding: 15px;
            }
            
            .receipt-qr-section img {
                width: 180px;
                height: 180px;
                border-width: 2px;
                padding: 8px;
            }
            
            .qr-label {
                font-size: 0.9rem;
            }
            
            .qr-instruction {
                font-size: 0.8rem;
                line-height: 1.3;
            }
            
            .receipt-actions {
                padding: 12px 15px 15px;
                gap: 8px;
            }
            
            .receipt-actions a {
                padding: 10px 12px;
                font-size: 0.85rem;
            }
        }
    </style>
</head>
<body>
<nav>
    <h2><span style="font-size: 2.5rem; display: inline-block;">🚌</span> Book Smarter, Travel Better</h2>
    <div>
        <a href="<?php echo BASE_URL; ?>index.php">Home</a>
        <a href="search.php">Search</a>
        <a href="<?php echo BASE_URL; ?>public/passenger_login.php">Login</a>
    </div>
</nav>
<div class="receipt-container">
    <div class="receipt-content">
        <!-- Left Side: Receipt Details -->
        <div class="receipt-details">
            <h2>📋 Booking Receipt</h2>
            <p><strong>Ticket ID:</strong> <span><?php echo intval($ticket['id']); ?></span></p>
            <p><strong>Bus:</strong> <span><?php echo htmlspecialchars($ticket['bus_name']); ?></span></p>
            <p><strong>Route:</strong> <span><?php echo htmlspecialchars($ticket['source']); ?> → <?php echo htmlspecialchars($ticket['destination']); ?></span></p>
            <p><strong>Date:</strong> <span><?php echo date('Y-m-d H:i', strtotime($ticket['departure_time'])); ?></span></p>
            <p><strong>Seats:</strong> <span><?php echo intval($ticket['seats']); ?></span></p>
            <p><strong>Status:</strong> <span><?php echo htmlspecialchars(ucfirst($ticket['status'])); ?></span></p>
            <p><strong>Payment Method:</strong> <span>Cash paid to operator</span></p>
        </div>
        
        <!-- Right Side: QR Code -->
        <div class="receipt-qr-section">
            <div class="qr-label">🔲 Ticket QR Code</div>
            <img src="<?php echo $qr_url; ?>" alt="Ticket QR Code" title="Show this QR code to operator for verification">
            <div class="qr-instruction">Show this QR code to the operator for payment confirmation.</div>
        </div>
    </div>
    
    <!-- Action Buttons -->
    <div class="receipt-actions">
        <a href="download_ticket.php?ticket_id=<?php echo $ticket['id']; ?>&format=pdf" class="pdf">
            📥 Download PDF
        </a>
        <a href="download_ticket.php?ticket_id=<?php echo $ticket['id']; ?>&format=print" class="print">
            🖨️ Print Ticket
        </a>
        <a href="<?php echo BASE_URL; ?>passenger/my_ticket.php" class="back">
            ← Back to My Tickets
        </a>
    </div>
</div>
</body>
</html>