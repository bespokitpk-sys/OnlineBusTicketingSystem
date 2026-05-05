<?php
// Ensure session is started
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/Ticket.php';

// Prevent caching
header('Cache-Control: no-cache, no-store, must-revalidate, private');
header('Pragma: no-cache');
header('Expires: 0');

// Check if user is logged in
if (empty($_SESSION['user_id']) || $_SESSION['user_role'] !== 'passenger') {
    header('HTTP/1.0 403 Forbidden');
    die('Access denied - Please login first');
}

$ticket_id = intval($_GET['ticket_id'] ?? 0);
$action = $_GET['action'] ?? 'view'; // view or download

if (!$ticket_id) {
    header('HTTP/1.0 404 Not Found');
    die('Ticket not found');
}

$ticket = Ticket::findById($ticket_id);
if (!$ticket) {
    header('HTTP/1.0 404 Not Found');
    die('Ticket not found in database');
}

// Security check: ensure ticket belongs to current user
if (intval($ticket['user_id']) !== intval($_SESSION['user_id'])) {
    header('HTTP/1.0 403 Forbidden');
    die('Access denied - This ticket does not belong to you');
}

// Extract ticket data with defaults
$ticket_code = 'TICKET-' . $ticket['id'];
$ticket_id_display = $ticket['id'] ?? 'N/A';
$bus_name = $ticket['bus_name'] ?? 'N/A';
$seats = $ticket['seats'] ?? 'N/A';
$departure_time = $ticket['departure_time'] ?? 'N/A';
$source = $ticket['source'] ?? 'N/A';
$destination = $ticket['destination'] ?? 'N/A';
$status_raw = $ticket['status'] ?? 'pending';
$status = strtoupper($status_raw);
$status_lower = strtolower($status_raw);
$created_at = $ticket['created_at'] ?? 'N/A';

// Get QR URL
$hostname = gethostname();
$server_ip = $hostname . '.local';
$qr_url = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode($server_ip . '/public/verify_ticket.php?ticket_id=' . $ticket_id_display . '&code=' . $ticket_code);

// Handle PDF download
if ($action === 'download') {
    // Try to use TCPDF if available
    if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
        try {
            require_once __DIR__ . '/../vendor/autoload.php';
            if (class_exists('TCPDF')) {
                $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
                $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
                $pdf->SetMargins(15, 15, 15);
                $pdf->AddPage();
                
                $pdf_content = generateTicketHTML($ticket_id_display, $bus_name, $seats, $departure_time, $source, $destination, $status, $status_lower, $created_at, $ticket_code, $qr_url, true);
                $pdf->writeHTML($pdf_content, true, false, true, false, '');
                
                header('Content-Type: application/pdf');
                header('Content-Disposition: attachment; filename="Ticket-' . $ticket_id_display . '.pdf"');
                header('Content-Transfer-Encoding: binary');
                $pdf->Output('Ticket-' . $ticket_id_display . '.pdf', 'D');
                exit;
            }
        } catch (Exception $e) {
            // Fall through to HTML
        }
    }
    
    // Fallback: Return HTML that user can print as PDF
    header('Content-Type: text/html; charset=utf-8');
    echo generateTicketHTML($ticket_id_display, $bus_name, $seats, $departure_time, $source, $destination, $status, $status_lower, $created_at, $ticket_code, $qr_url, true);
    exit;
}

// Default view with buttons
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket #<?php echo $ticket_id_display; ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { width: 100%; margin: 0; padding: 0; }
        body { 
            font-family: Arial, sans-serif; 
            background: white; 
            padding: 5px;
            line-height: 1.2;
            font-size: 12px;
        }
        .action-buttons { 
            max-width: 900px; 
            margin: 0 auto 15px; 
            display: flex; 
            gap: 10px; 
            justify-content: center; 
            flex-wrap: wrap; 
        }
        .action-buttons button, .action-buttons a { 
            padding: 10px 20px; 
            border: none; 
            border-radius: 4px; 
            cursor: pointer; 
            font-size: 14px; 
            font-weight: bold; 
            text-decoration: none; 
            display: inline-block; 
        }
        .btn-print { background: #4CAF50; color: white; }
        .btn-print:hover { background: #45a049; }
        .btn-download { background: #2196F3; color: white; }
        .btn-download:hover { background: #0b7dda; }
        .ticket-container { 
            max-width: 750px; 
            margin: 0 auto; 
            background: white; 
            border: 2px solid #0f1c33; 
            padding: 15px;
            page-break-after: always;
            page-break-inside: avoid;
        }
        .header { 
            text-align: center; 
            border-bottom: 2px solid #0072ff; 
            padding-bottom: 8px; 
            margin-bottom: 12px; 
        }
        .header h1 { 
            color: #0f1c33; 
            font-size: 18px; 
            font-weight: bold; 
            margin: 0;
        }
        .header p { color: #666; font-size: 10px; margin: 2px 0 0 0; }
        .ticket-content { 
            display: block;
            margin-bottom: 12px; 
        }
        .ticket-details { 
            display: block;
            margin-bottom: 12px; 
        }
        .detail-row { 
            border-bottom: 1px solid #ddd; 
            padding: 5px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            page-break-inside: avoid;
        }
        .detail-label { 
            font-size: 8px; 
            color: #999; 
            text-transform: uppercase; 
            font-weight: bold; 
            letter-spacing: 0.5px;
            width: 35%;
        }
        .detail-value { 
            font-size: 12px; 
            color: #0f1c33; 
            font-weight: bold; 
            width: 65%;
            text-align: right;
        }
        .route-section { 
            display: flex; 
            flex-direction: column; 
            align-items: center; 
            justify-content: center;
            background: #e8f4f8; 
            padding: 12px; 
            border-radius: 4px; 
            margin: 12px 0;
            min-height: 80px;
            page-break-inside: avoid;
        }
        .route-from, .route-to { 
            font-size: 14px; 
            color: #0f1c33; 
            font-weight: bold; 
            text-align: center; 
        }
        .route-arrow { 
            font-size: 18px; 
            color: #0072ff; 
            margin: 5px 0; 
        }
        .qr-section { 
            display: flex; 
            flex-direction: column; 
            align-items: center; 
            justify-content: center;
            background: white; 
            padding: 12px; 
            border-radius: 4px; 
            border: 2px dashed #0072ff;
            margin: 12px 0;
            page-break-inside: avoid;
        }
        .qr-section img { 
            max-width: 130px; 
            width: 130px;
            height: 130px;
            border: 2px solid #0072ff; 
            border-radius: 3px; 
            padding: 3px; 
            background: white; 
            margin-bottom: 8px; 
        }
        .qr-label { 
            font-size: 9px; 
            color: #333; 
            text-align: center; 
            font-weight: bold; 
            text-transform: uppercase; 
        }
        .status-badge { 
            display: inline-block; 
            padding: 3px 8px; 
            border-radius: 12px; 
            font-weight: bold; 
            font-size: 10px; 
            text-transform: uppercase; 
        }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-approved { background: #d1e7dd; color: #0f5132; }
        .status-cancelled { background: #f8d7da; color: #842029; }
        .status-boarded { background: #cfe2ff; color: #084298; }
        .footer { 
            border-top: 2px solid #0072ff; 
            padding-top: 10px; 
            text-align: center; 
            color: #666; 
            font-size: 8px; 
            page-break-inside: avoid;
        }
        .footer strong { 
            display: block; 
            font-size: 10px; 
            color: #0f1c33; 
            margin-bottom: 3px; 
        }
        .footer p { margin: 2px 0; }
        
        @media print { 
            * { margin: 0 !important; padding: 0 !important; }
            html, body { background: white !important; margin: 0 !important; padding: 0 !important; width: 100%; }
            body { padding: 3px; }
            .action-buttons { display: none !important; }
            .ticket-container { 
                box-shadow: none !important; 
                max-width: 100% !important;
                margin: 0 !important;
                padding: 10px !important;
                border: 1px solid #ccc !important;
                page-break-after: avoid !important;
            }
            .header { page-break-after: avoid !important; }
            .ticket-content { page-break-after: avoid !important; }
            .detail-row { page-break-inside: avoid !important; }
            .route-section { page-break-inside: avoid !important; }
            .qr-section { page-break-inside: avoid !important; }
            .footer { page-break-inside: avoid !important; }
        }
        @media (max-width: 768px) { 
            .ticket-content { display: block; }
            .action-buttons { flex-direction: column; } 
        }
    </style>
</head>
<body>
    <div class="action-buttons">
        <button class="btn-print" onclick="window.print()">🖨️ Print Ticket</button>
        <a class="btn-download" href="?ticket_id=<?php echo $ticket_id_display; ?>&action=download">⬇️ Download PDF</a>
    </div>

    <div class="ticket-container">
        <div class="header">
            <h1>🚌 Bus Ticket - Official Receipt</h1>
            <p>Download and save this ticket for your journey</p>
        </div>
        
        <div class="ticket-content">
            <div class="ticket-details">
                <div class="detail-row">
                    <div class="detail-label">Ticket ID</div>
                    <div class="detail-value">#<?php echo $ticket_id_display; ?></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Bus Name</div>
                    <div class="detail-value"><?php echo $bus_name; ?></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Number of Seats</div>
                    <div class="detail-value"><?php echo $seats; ?></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Departure</div>
                    <div class="detail-value"><?php echo $departure_time; ?></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Status</div>
                    <div class="detail-value"><span class="status-badge status-<?php echo $status_lower; ?>">✓ <?php echo $status; ?></span></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Booking Date</div>
                    <div class="detail-value"><?php echo $created_at; ?></div>
                </div>
            </div>
            
            <div style="margin-top: 20px;">
                <div class="route-section">
                    <strong><?php echo $source; ?></strong>
                    <div class="route-arrow">↓</div>
                    <strong><?php echo $destination; ?></strong>
                </div>
                
                <div class="qr-section">
                    <img src="<?php echo $qr_url; ?>" alt="QR Code" style="page-break-inside: avoid;">
                    <div class="qr-label">Scan with phone to verify ticket</div>
                </div>
            </div>
        </div>
        
        <div class="footer">
            <strong>Bus Ticketing System</strong>
            <p>Official Receipt - Ticket Code: <?php echo $ticket_code; ?></p>
            <p>Keep safe until journey completion</p>
        </div>
    </div>
</body>
</html>

<?php

function generateTicketHTML($id, $bus, $seats, $departure, $from, $to, $status, $status_lower, $date, $code, $qr_url, $for_pdf = false) {
    $html = '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Ticket #' . $id . '</title><style>';
    $html .= '* { margin: 0; padding: 0; box-sizing: border-box; }';
    $html .= 'html, body { width: 100%; margin: 0; padding: 0; }';
    $html .= 'body { font-family: Arial, sans-serif; background: white; padding: 5px; line-height: 1.2; font-size: 12px; }';
    $html .= '.ticket-container { max-width: 750px; margin: 0 auto; background: white; border: 2px solid #0f1c33; padding: 15px; page-break-inside: avoid; }';
    $html .= '.header { text-align: center; border-bottom: 2px solid #0072ff; padding-bottom: 8px; margin-bottom: 12px; }';
    $html .= '.header h1 { color: #0f1c33; font-size: 18px; font-weight: bold; margin: 0; }';
    $html .= '.header p { color: #666; font-size: 10px; margin: 2px 0 0 0; }';
    $html .= '.ticket-content { display: block; margin-bottom: 12px; }';
    $html .= '.ticket-details { display: block; margin-bottom: 12px; }';
    $html .= '.detail-row { border-bottom: 1px solid #ddd; padding: 5px 0; display: flex; justify-content: space-between; align-items: center; page-break-inside: avoid; }';
    $html .= '.detail-label { font-size: 8px; color: #999; text-transform: uppercase; font-weight: bold; letter-spacing: 0.5px; width: 35%; }';
    $html .= '.detail-value { font-size: 12px; color: #0f1c33; font-weight: bold; width: 65%; text-align: right; }';
    $html .= '.route-section { display: flex; flex-direction: column; align-items: center; justify-content: center; background: #e8f4f8; padding: 12px; border-radius: 4px; margin: 12px 0; min-height: 80px; page-break-inside: avoid; }';
    $html .= '.route-from, .route-to { font-size: 14px; color: #0f1c33; font-weight: bold; text-align: center; }';
    $html .= '.route-arrow { font-size: 18px; color: #0072ff; margin: 5px 0; }';
    $html .= '.qr-section { display: flex; flex-direction: column; align-items: center; justify-content: center; background: white; padding: 12px; border-radius: 4px; border: 2px dashed #0072ff; margin: 12px 0; page-break-inside: avoid; }';
    $html .= '.qr-section img { max-width: 130px; width: 130px; height: 130px; border: 2px solid #0072ff; border-radius: 3px; padding: 3px; background: white; margin-bottom: 8px; }';
    $html .= '.qr-label { font-size: 9px; color: #333; text-align: center; font-weight: bold; text-transform: uppercase; }';
    $html .= '.status-badge { display: inline-block; padding: 3px 8px; border-radius: 12px; font-weight: bold; font-size: 10px; text-transform: uppercase; }';
    $html .= '.status-pending { background: #fff3cd; color: #856404; }';
    $html .= '.status-approved { background: #d1e7dd; color: #0f5132; }';
    $html .= '.status-cancelled { background: #f8d7da; color: #842029; }';
    $html .= '.status-boarded { background: #cfe2ff; color: #084298; }';
    $html .= '.footer { border-top: 2px solid #0072ff; padding-top: 10px; text-align: center; color: #666; font-size: 8px; page-break-inside: avoid; }';
    $html .= '.footer strong { display: block; font-size: 10px; color: #0f1c33; margin-bottom: 3px; }';
    $html .= '.footer p { margin: 2px 0; }';
    $html .= '</style></head><body>';
    
    $html .= '<div class="ticket-container">';
    $html .= '<div class="header"><h1>🚌 Bus Ticket - Official Receipt</h1><p>Download and save this ticket for your journey</p></div>';
    
    $html .= '<div class="ticket-content">';
    $html .= '<div class="ticket-details">';
    $html .= '<div class="detail-row"><div class="detail-label">Ticket ID</div><div class="detail-value">#' . $id . '</div></div>';
    $html .= '<div class="detail-row"><div class="detail-label">Bus Name</div><div class="detail-value">' . $bus . '</div></div>';
    $html .= '<div class="detail-row"><div class="detail-label">Seats</div><div class="detail-value">' . $seats . '</div></div>';
    $html .= '<div class="detail-row"><div class="detail-label">Departure</div><div class="detail-value">' . $departure . '</div></div>';
    $html .= '<div class="detail-row"><div class="detail-label">Status</div><div class="detail-value"><span class="status-badge status-' . $status_lower . '">✓ ' . $status . '</span></div></div>';
    $html .= '<div class="detail-row"><div class="detail-label">Booking Date</div><div class="detail-value">' . $date . '</div></div>';
    $html .= '</div>';
    
    $html .= '<div style="margin-top: 12px;">';
    $html .= '<div class="route-section"><strong>' . $from . '</strong><div class="route-arrow">↓</div><strong>' . $to . '</strong></div>';
    $html .= '<div class="qr-section"><img src="' . $qr_url . '" alt="QR Code" style="page-break-inside: avoid;"><div class="qr-label">Scan with phone to verify ticket</div></div>';
    $html .= '</div>';
    $html .= '</div>';
    
    $html .= '<div class="footer"><strong>Bus Ticketing System</strong><p>Official Receipt - Ticket Code: ' . $code . '</p><p>Keep safe until journey completion</p></div>';
    $html .= '</div></body></html>';
    
    return $html;
}
?>
