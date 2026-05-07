<?php
require_once APP_ROOT . '/config/db.php';
require_once APP_ROOT . '/app/core/Auth.php';
require_once APP_ROOT . '/app/controllers/OperatorController.php';

requireRole('operator');

$user = currentUser();
$operator_id = $user['id'];

$schedules = OperatorController::getMySchedules($operator_id);
$selected_schedule_id = $_GET['schedule_id'] ?? null;
$schedule_details = null;
$boarding_passengers = [];
$pending_payments = [];

if ($selected_schedule_id) {
    $selected_schedule_id = intval($selected_schedule_id);
    $schedule_details = OperatorController::getScheduleById($selected_schedule_id);
    $boarding_passengers = OperatorController::getBoardedPassengers($selected_schedule_id);
    $pending_payments = OperatorController::getPendingPayments($selected_schedule_id);
}

// Handle payment approval
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $ticket_id = intval($_POST['ticket_id'] ?? 0);
    
    if ($action === 'approve_payment' && $ticket_id > 0) {
        $result = OperatorController::approvePayment($ticket_id);
        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    } elseif ($action === 'board_passenger' && $ticket_id > 0) {
        $result = OperatorController::boardPassenger($ticket_id);
        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Boarding & Payment - Operator</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f7fa;
            min-height: 100vh;
            color: #1f2937;
        }
        
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
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 30px 20px 40px;
        }
        
        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            justify-content: center;
            padding: 10px 16px;
            background: white;
            color: #0f1c33;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            margin-bottom: 18px;
            box-shadow: 0 2px 8px rgba(15, 28, 51, 0.06);
            font-size: 0.92rem;
            border: 1px solid #d7e3f0;
            cursor: pointer;
        }
        
        .back-btn:hover {
            background: #f8fbff;
            border-color: #b8d6ff;
            transform: translateY(-1px);
        }
        
        .page-header {
            background: white;
            color: #0f1c33;
            padding: 32px;
            border-radius: 14px;
            margin-bottom: 22px;
            box-shadow: 0 2px 10px rgba(15, 28, 51, 0.08);
            border: 1px solid #e7edf5;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 24px;
        }

        .page-header-copy {
            max-width: 760px;
        }

        .eyebrow {
            display: inline-block;
            margin-bottom: 12px;
            padding: 8px 14px;
            border-radius: 999px;
            background: linear-gradient(90deg, #e8f4f8 0%, #d4e9f7 100%);
            color: #0f1c33;
            font-size: 0.82rem;
            font-weight: 700;
            letter-spacing: 0.4px;
            text-transform: uppercase;
        }
        
        .page-header h1 {
            font-size: 1.95rem;
            margin-bottom: 10px;
            font-weight: 700;
            letter-spacing: -0.3px;
            color: #0f1c33;
        }
        
        .page-header p {
            font-size: 1rem;
            color: #5f6b7a;
            font-weight: 500;
            line-height: 1.6;
        }

        .page-header-side {
            min-width: 240px;
            text-align: right;
            color: #667085;
            font-size: 0.92rem;
            line-height: 1.6;
        }

        .summary-strip {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .summary-card {
            background: white;
            border: 1px solid #e7edf5;
            border-radius: 12px;
            padding: 20px 22px;
            box-shadow: 0 2px 8px rgba(15, 28, 51, 0.06);
        }

        .summary-label {
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            color: #7a8696;
            margin-bottom: 8px;
        }

        .summary-value {
            font-size: 1.45rem;
            font-weight: 700;
            color: #0f1c33;
            margin-bottom: 6px;
            letter-spacing: -0.2px;
        }

        .summary-subtext {
            font-size: 0.92rem;
            color: #627083;
            line-height: 1.5;
        }
        
        .schedule-selector {
            background: white;
            padding: 25px;
            border-radius: 14px;
            margin-bottom: 24px;
            box-shadow: 0 2px 10px rgba(15, 28, 51, 0.08);
            border: 1px solid #e7edf5;
        }
        
        .schedule-selector label {
            display: block;
            margin-bottom: 12px;
            font-weight: 700;
            color: #0f1c33;
            font-size: 0.95rem;
        }
        
        .schedule-selector select {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #e0e8f1;
            border-radius: 8px;
            font-size: 1rem;
            cursor: pointer;
            background: white;
            transition: all 0.3s ease;
            font-weight: 500;
        }
        
        .schedule-selector select:focus {
            outline: none;
            border-color: #0072ff;
            box-shadow: 0 0 0 4px rgba(0, 114, 255, 0.1);
        }
        
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 25px; }
        
        .panel {
            background: white;
            padding: 30px;
            border-radius: 14px;
            box-shadow: 0 2px 10px rgba(15, 28, 51, 0.08);
            border: 1px solid #e7edf5;
        }
        
        .panel h2 {
            color: #0f1c33;
            margin-bottom: 24px;
            font-size: 1.1rem;
            border-bottom: 1px solid #dbe6f2;
            padding-bottom: 12px;
            font-weight: 700;
            letter-spacing: -0.2px;
        }
        
        .qr-container {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .scanner-shell {
            background: linear-gradient(180deg, #f8fbff 0%, #f2f7fc 100%);
            border: 1px solid #dce8f3;
            border-radius: 18px;
            padding: 18px;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.85);
        }

        .scanner-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            margin-bottom: 14px;
        }

        .scanner-copy strong {
            display: block;
            font-size: 1rem;
            color: #0f1c33;
            margin-bottom: 4px;
        }

        .scanner-copy span {
            color: #667085;
            font-size: 0.92rem;
            line-height: 1.5;
        }

        .camera-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 14px;
            border-radius: 999px;
            background: #eef4fb;
            border: 1px solid #d8e3ef;
            color: #425466;
            font-size: 0.82rem;
            font-weight: 700;
            letter-spacing: 0.3px;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .camera-pill::before {
            content: '';
            width: 10px;
            height: 10px;
            border-radius: 999px;
            background: #94a3b8;
            box-shadow: 0 0 0 4px rgba(148, 163, 184, 0.18);
        }

        .camera-pill.live {
            background: #effcf6;
            border-color: #b7ebc8;
            color: #166534;
        }

        .camera-pill.live::before {
            background: #16a34a;
            box-shadow: 0 0 0 4px rgba(22, 163, 74, 0.15);
        }

        .scanner-frame {
            position: relative;
            padding: 12px;
            border-radius: 22px;
            background: radial-gradient(circle at top, rgba(18, 93, 169, 0.12), transparent 45%), #0f1720;
            box-shadow: 0 16px 40px rgba(15, 23, 32, 0.18);
            overflow: hidden;
        }

        .scanner-frame::before,
        .scanner-frame::after {
            content: '';
            position: absolute;
            width: 64px;
            height: 64px;
            border: 3px solid rgba(125, 211, 252, 0.92);
            z-index: 2;
            pointer-events: none;
        }

        .scanner-frame::before {
            top: 18px;
            left: 18px;
            border-right: none;
            border-bottom: none;
            border-radius: 16px 0 0 0;
        }

        .scanner-frame::after {
            right: 18px;
            bottom: 18px;
            border-left: none;
            border-top: none;
            border-radius: 0 0 16px 0;
        }
        
        #video {
            width: 100%;
            height: auto;
            min-height: 360px;
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            background: #000;
            box-shadow: inset 0 0 0 1px rgba(255,255,255,0.04);
            display: block;
            max-width: 100%;
            aspect-ratio: 16 / 9;
            object-fit: cover;
        }

        .scanner-guide {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 10px;
            margin-top: 14px;
        }

        .guide-chip {
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid #dbe6f2;
            border-radius: 12px;
            padding: 12px 14px;
            color: #425466;
            font-size: 0.84rem;
            line-height: 1.45;
        }

        .guide-chip strong {
            display: block;
            color: #0f1c33;
            margin-bottom: 4px;
        }
        
        .controls {
            display: flex;
            gap: 12px;
        }
        
        button {
            flex: 1;
            padding: 14px 20px;
            border: 1px solid transparent;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.95rem;
            box-shadow: 0 2px 6px rgba(15, 28, 51, 0.06);
            letter-spacing: 0.2px;
        }
        
        .btn-primary {
            background: #0072ff;
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 18px rgba(0, 114, 255, 0.22);
            background: #005fd6;
        }
        
        .btn-danger {
            background: #f8fafc;
            border-color: #d8e3ef;
            color: #344054;
            box-shadow: none;
        }

        .btn-danger:hover {
            background: #eef3f8;
            transform: translateY(-1px);
        }

        .btn-success {
            background: #0f9f6e;
            color: white;
        }
        
        .btn-success:hover {
            background: #0a845b;
            transform: translateY(-2px);
        }
        
        .scanned-result {
            background: #ecfdf3;
            border: 1px solid #b7ebc8;
            border-radius: 8px;
            padding: 16px 18px;
            margin-top: 12px;
            display: none;
            box-shadow: none;
            font-weight: 600;
            color: #166534;
        }
        
        .scanned-result.show { display: block; }

        .passenger-shell {
            min-height: 100%;
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .passenger-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 14px;
            padding: 18px;
            background: linear-gradient(180deg, #f8fbff 0%, #f3f8fc 100%);
            border: 1px solid #dce8f3;
            border-radius: 16px;
        }

        .passenger-header h3 {
            font-size: 1.08rem;
            color: #0f1c33;
            margin-bottom: 6px;
        }

        .passenger-header p {
            font-size: 0.92rem;
            color: #667085;
            line-height: 1.5;
        }

        .passenger-ticket-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 10px 14px;
            border-radius: 12px;
            background: #0f1c33;
            color: #fff;
            font-size: 0.82rem;
            font-weight: 700;
            letter-spacing: 0.45px;
            text-transform: uppercase;
            min-width: 120px;
        }

        .passenger-highlights {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .highlight-card {
            border: 1px solid #e3ecf4;
            border-radius: 14px;
            padding: 16px;
            background: #fbfdff;
        }

        .highlight-card label {
            display: block;
            font-size: 0.76rem;
            text-transform: uppercase;
            letter-spacing: 0.35px;
            color: #7a8696;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .highlight-card strong {
            display: block;
            color: #0f1c33;
            font-size: 1rem;
            line-height: 1.45;
        }

        .detail-list {
            display: grid;
            gap: 10px;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            padding: 13px 0;
            border-bottom: 1px solid #ebf1f6;
        }

        .detail-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .detail-row label {
            color: #667085;
            font-size: 0.88rem;
            font-weight: 600;
        }

        .detail-row span {
            color: #0f1c33;
            font-size: 0.94rem;
            font-weight: 700;
            text-align: right;
        }

        .detail-row span.muted {
            color: #667085;
            font-weight: 600;
        }
        
        .passenger-card {
            background: #fbfcfe;
            padding: 20px;
            margin-bottom: 14px;
            border-radius: 12px;
            transition: all 0.3s ease;
            border: 1px solid #e5edf5;
        }
        
        .passenger-card:hover {
            box-shadow: 0 6px 18px rgba(15, 28, 51, 0.08);
            transform: translateY(-1px);
        }

        .passenger-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px 18px;
        }
        
        .passenger-info {
            margin-bottom: 0;
        }
        
        .passenger-info label {
            font-weight: 700;
            color: #7a8696;
            display: block;
            font-size: 0.78rem;
            margin-bottom: 4px;
            letter-spacing: 0.35px;
            text-transform: uppercase;
        }
        
        .passenger-info span {
            color: #1f2937;
            font-weight: 500;
        }
        
        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 8px 14px;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.3px;
            text-transform: uppercase;
        }
        
        .status-pending {
            background: #fff7e6;
            color: #9a6700;
            border: 1px solid #f3d58b;
        }
        
        .status-approved {
            background: #eaf2ff;
            color: #0b57d0;
            border: 1px solid #bfd5ff;
        }
        
        .status-boarded {
            background: #ecfdf3;
            color: #166534;
            border: 1px solid #b7ebc8;
        }
        
        .action-btns {
            display: flex;
            gap: 12px;
            margin-top: 16px;
            padding-top: 16px;
            border-top: 1px solid #e9eff5;
        }
        
        .action-btns button {
            flex: 1;
            padding: 12px 16px;
            font-size: 0.92rem;
            font-weight: 600;
            min-height: 44px;
        }
        
        .no-data {
            text-align: center;
            padding: 50px 40px;
            color: #7a8696;
            font-size: 1rem;
            background: white;
            border: 1px dashed #dbe6f2;
            border-radius: 12px;
        }

        .loading-card {
            padding: 28px 24px;
            border-radius: 14px;
            border: 1px solid #dce8f3;
            background: linear-gradient(180deg, #fbfdff 0%, #f3f8fc 100%);
            color: #486173;
            font-weight: 600;
            text-align: center;
        }
        
        .panel-stack {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        @media (max-width: 768px) {
            .page-header {
                flex-direction: column;
                padding: 24px 20px;
            }
            .page-header-side {
                text-align: left;
                min-width: 0;
            }
            .summary-strip {
                grid-template-columns: 1fr;
            }
            .grid { grid-template-columns: 1fr; }
            .passenger-grid { grid-template-columns: 1fr; }
            .passenger-highlights { grid-template-columns: 1fr; }
            .detail-row { flex-direction: column; align-items: flex-start; }
            .detail-row span { text-align: left; }
            .page-header h1 { font-size: 1.55rem; }
            nav {
                padding: 15px 20px;
            }
            nav h2 {
                font-size: 1.2rem;
            }
            nav a {
                padding: 8px 12px;
                font-size: 0.8rem;
            }
            .page-header {
                padding: 24px 20px;
            }
            .controls {
                flex-direction: column;
            }
            .scanner-toolbar,
            .passenger-header {
                flex-direction: column;
                align-items: flex-start;
            }
            .scanner-guide {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

<nav>
    <h2><span style="font-size: 2rem; margin-right: 10px;">🚌</span>Book Smarter, Travel Better</h2>
    <div>
        <a href="<?php echo BASE_URL; ?>operator/dashboard">Dashboard</a>
        <a href="<?php echo BASE_URL; ?>operator/schedules">Schedules</a>
        <a href="<?php echo BASE_URL; ?>logout">Logout</a>
    </div>
</nav>

<div class="container">
    <a href="<?php echo BASE_URL; ?>operator/dashboard" class="back-btn">← Back to Dashboard</a>
    
    <div class="page-header">
        <div class="page-header-copy">
            <span class="eyebrow">Boarding Operations</span>
            <h1>Boarding and Payment Control</h1>
            <p>Use this screen to verify passengers, approve offline payments, and move approved travelers through boarding with a clear operational view.</p>
        </div>
        <div class="page-header-side">
            Select a schedule first, then use the scanner to pull passenger records directly into the review panel.
        </div>
    </div>
    
    <div class="schedule-selector">
        <label>Select a Schedule</label>
        <select onchange="window.location.href='?schedule_id=' + this.value">
            <option value="">-- Choose a schedule --</option>
            <?php foreach ($schedules as $sch): ?>
                <option value="<?php echo $sch['id']; ?>" <?php echo ($selected_schedule_id == $sch['id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($sch['source'] ?? 'N/A'); ?> → <?php echo htmlspecialchars($sch['destination'] ?? 'N/A'); ?>
                    (<?php echo date('M d, h:i A', strtotime($sch['departure_time'])); ?>)
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    
    <?php if ($schedule_details): ?>
        <div class="summary-strip">
            <div class="summary-card">
                <div class="summary-label">Route</div>
                <div class="summary-value"><?php echo htmlspecialchars($schedule_details['source'] ?? 'N/A'); ?> → <?php echo htmlspecialchars($schedule_details['destination'] ?? 'N/A'); ?></div>
                <div class="summary-subtext">Active trip currently selected for scanning and manual processing.</div>
            </div>
            <div class="summary-card">
                <div class="summary-label">Departure</div>
                <div class="summary-value"><?php echo !empty($schedule_details['departure_time']) ? date('M d, h:i A', strtotime($schedule_details['departure_time'])) : 'Not set'; ?></div>
                <div class="summary-subtext">Verify arrivals against this scheduled departure window.</div>
            </div>
            <div class="summary-card">
                <div class="summary-label">Pending Payments</div>
                <div class="summary-value"><?php echo count($pending_payments); ?></div>
                <div class="summary-subtext">Passengers still waiting for manual payment confirmation.</div>
            </div>
            <div class="summary-card">
                <div class="summary-label">Boarded Passengers</div>
                <div class="summary-value"><?php echo count($boarding_passengers); ?></div>
                <div class="summary-subtext">Passengers already processed and marked as boarded.</div>
            </div>
        </div>

        <div class="grid">
            <div class="panel">
                <h2>QR Code Scanner</h2>
                <div class="qr-container">
                    <div class="scanner-shell">
                        <div class="scanner-toolbar">
                            <div class="scanner-copy">
                                <strong>Live boarding scanner</strong>
                                <span>Start the camera, point it at the passenger ticket QR code, and the boarding panel will load the booking automatically.</span>
                            </div>
                            <div id="cameraStatus" class="camera-pill">Camera Offline</div>
                        </div>
                        <div class="scanner-frame">
                            <video id="video" autoplay playsinline muted></video>
                        </div>
                        <div class="scanner-guide">
                            <div class="guide-chip"><strong>1. Select trip</strong> Keep the active route selected before scanning.</div>
                            <div class="guide-chip"><strong>2. Align QR</strong> Place the ticket code fully inside the camera frame.</div>
                            <div class="guide-chip"><strong>3. Review & act</strong> Approve payment or mark boarding from the panel on the right.</div>
                        </div>
                    </div>
                    <div class="controls">
                        <button id="startCameraBtn" type="button" class="btn-primary" onclick="startCamera()">Start Camera</button>
                        <button id="stopCameraBtn" type="button" class="btn-danger" onclick="stopCamera()">Stop Camera</button>
                    </div>
                    <div id="scannedResult" class="scanned-result">
                        <strong id="resultText">Passenger code scanned successfully.</strong>
                    </div>
                </div>
            </div>
            
            <div class="panel">
                <h2>Passenger Details</h2>
                <div id="passengerDetails" class="no-data">
                    <p>Scan a passenger QR code to load booking details and available actions.</p>
                </div>
            </div>
        </div>
        
        <div class="panel-stack" style="margin-top: 25px;">
            <div class="panel">
                <h2>Pending Payments (<?php echo count($pending_payments); ?>)</h2>
                <?php if (count($pending_payments) > 0): ?>
                    <?php foreach ($pending_payments as $payment): ?>
                        <div class="passenger-card">
                            <div class="passenger-grid">
                                <div class="passenger-info">
                                    <label>Name</label>
                                    <span><?php echo htmlspecialchars($payment['name']); ?></span>
                                </div>
                                <div class="passenger-info">
                                    <label>Phone</label>
                                    <span><?php echo htmlspecialchars($payment['phone']); ?></span>
                                </div>
                                <div class="passenger-info">
                                    <label>Seats</label>
                                    <span><?php echo htmlspecialchars($payment['seats']); ?> seat(s)</span>
                                </div>
                                <div class="passenger-info">
                                    <label>Status</label>
                                    <span class="status-badge status-pending">Payment Pending</span>
                                </div>
                            </div>
                            <div class="action-btns">
                                <button class="btn-success" onclick="approvePayment(<?php echo $payment['id']; ?>)">Approve Payment</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-data"><p>All payments for this trip are already approved.</p></div>
                <?php endif; ?>
            </div>

            <div class="panel">
                <h2>Boarded Passengers (<?php echo count($boarding_passengers); ?>)</h2>
                <?php if (count($boarding_passengers) > 0): ?>
                    <?php foreach ($boarding_passengers as $passenger): ?>
                        <div class="passenger-card">
                            <div class="passenger-grid">
                                <div class="passenger-info">
                                    <label>Name</label>
                                    <span><?php echo htmlspecialchars($passenger['name']); ?></span>
                                </div>
                                <div class="passenger-info">
                                    <label>Phone</label>
                                    <span><?php echo htmlspecialchars($passenger['phone']); ?></span>
                                </div>
                                <div class="passenger-info">
                                    <label>Seats</label>
                                    <span><?php echo htmlspecialchars($passenger['seats']); ?> seat(s)</span>
                                </div>
                                <div class="passenger-info">
                                    <label>Status</label>
                                    <span class="status-badge status-boarded">Boarded</span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-data"><p>No passengers have been boarded for this schedule yet.</p></div>
                <?php endif; ?>
            </div>
        </div>
    <?php else: ?>
        <div class="panel">
            <div class="no-data">
                <p>Select a schedule to open the boarding scanner and passenger processing tools.</p>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
    let cameraStream = null;
    let scannerActive = false;
    let selectedScheduleId = <?php echo json_encode($selected_schedule_id); ?>;
    let lastScannedValue = null;
    let lastScannedAt = 0;

    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function formatDateTime(value) {
        if (!value) {
            return 'Not available';
        }

        const parsed = new Date(value.replace(' ', 'T'));
        if (Number.isNaN(parsed.getTime())) {
            return value;
        }

        return parsed.toLocaleString([], {
            month: 'short',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    function getStatusMeta(status) {
        const normalized = String(status || '').toLowerCase();

        if (normalized === 'pending') {
            return { label: 'Payment Pending', className: 'status-pending' };
        }

        if (normalized === 'approved') {
            return { label: 'Ready to Board', className: 'status-approved' };
        }

        if (normalized === 'boarded') {
            return { label: 'Boarded', className: 'status-boarded' };
        }

        return { label: normalized ? normalized.toUpperCase() : 'Unknown', className: 'status-pending' };
    }

    function setCameraStatus(label, isLive = false) {
        const cameraStatus = document.getElementById('cameraStatus');
        if (!cameraStatus) {
            return;
        }

        cameraStatus.textContent = label;
        cameraStatus.classList.toggle('live', isLive);
    }

    function setPassengerPlaceholder(message) {
        const passengerDetails = document.getElementById('passengerDetails');
        if (!passengerDetails) {
            return;
        }

        passengerDetails.className = 'no-data';
        passengerDetails.innerHTML = '<p>' + escapeHtml(message) + '</p>';
    }

    function setPassengerLoading(message) {
        const passengerDetails = document.getElementById('passengerDetails');
        if (!passengerDetails) {
            return;
        }

        passengerDetails.className = 'loading-card';
        passengerDetails.textContent = message;
    }

    function updateCameraButtons() {
        const startButton = document.getElementById('startCameraBtn');
        const stopButton = document.getElementById('stopCameraBtn');

        if (startButton) {
            startButton.disabled = scannerActive;
            startButton.style.opacity = scannerActive ? '0.65' : '1';
            startButton.style.cursor = scannerActive ? 'not-allowed' : 'pointer';
        }

        if (stopButton) {
            stopButton.disabled = !scannerActive;
            stopButton.style.opacity = !scannerActive ? '0.65' : '1';
            stopButton.style.cursor = !scannerActive ? 'not-allowed' : 'pointer';
        }
    }
    
    // Get video element safely
    function getVideoElement() {
        let video = document.getElementById('video');
        if (!video) {
            console.error('Video element not found');
            return null;
        }
        return video;
    }

    function extractTicketIdFromQRCode(rawValue) {
        if (!rawValue) {
            return null;
        }

        const trimmedValue = String(rawValue).trim();

        if (/^\d+$/.test(trimmedValue)) {
            return parseInt(trimmedValue, 10);
        }

        const ticketIdMatch = trimmedValue.match(/[?&]ticket_id=(\d+)/i);
        if (ticketIdMatch) {
            return parseInt(ticketIdMatch[1], 10);
        }

        const directIdMatch = trimmedValue.match(/ticket[_-]?id[=:/\s]+(\d+)/i);
        if (directIdMatch) {
            return parseInt(directIdMatch[1], 10);
        }

        try {
            const parsedUrl = new URL(trimmedValue, window.location.origin);
            const ticketId = parsedUrl.searchParams.get('ticket_id');
            if (ticketId && /^\d+$/.test(ticketId)) {
                return parseInt(ticketId, 10);
            }
        } catch (error) {
            console.warn('QR payload is not a standard URL:', trimmedValue);
        }

        return null;
    }

    function showScannedMessage(message, isError = false) {
        const scannedResult = document.getElementById('scannedResult');
        const resultText = document.getElementById('resultText');

        if (!scannedResult || !resultText) {
            return;
        }

        resultText.textContent = message;
        scannedResult.classList.add('show');
        scannedResult.style.background = isError ? '#fff4f4' : '#ecfdf3';
        scannedResult.style.borderColor = isError ? '#f3c2c2' : '#b7ebc8';
        scannedResult.style.color = isError ? '#b42318' : '#166534';
    }
    
    function startCamera() {
        const video = getVideoElement();
        if (!video) {
            showScannedMessage('Video element was not found. Refresh the page and try again.', true);
            return;
        }

        if (!selectedScheduleId) {
            showScannedMessage('Select a schedule before starting the boarding camera.', true);
            return;
        }
        
        if (scannerActive) {
            return;
        }

        if (!navigator.mediaDevices || typeof navigator.mediaDevices.getUserMedia !== 'function') {
            showScannedMessage('Camera access is not available in this browser. Open this page in Chrome, Edge, or another browser that supports camera scanning.', true);
            updateCameraButtons();
            return;
        }

        updateCameraButtons();
        setCameraStatus('Starting Camera');
        setPassengerPlaceholder('Scanner is ready. Start the camera and scan a passenger ticket.');
        
        video.setAttribute('autoplay', 'true');
        video.setAttribute('playsinline', 'true');
        video.setAttribute('muted', 'true');

        const streamAttempts = [
            { video: { facingMode: { ideal: 'environment' }, width: { ideal: 1280 }, height: { ideal: 720 } }, audio: false },
            { video: { width: { ideal: 1280 }, height: { ideal: 720 } }, audio: false }
        ];

        const openCamera = (attemptIndex = 0) => {
            if (attemptIndex >= streamAttempts.length) {
                return Promise.reject(new Error('No camera stream could be opened.'));
            }

            return navigator.mediaDevices.getUserMedia(streamAttempts[attemptIndex]).catch(error => {
                if (attemptIndex < streamAttempts.length - 1 && (error.name === 'OverconstrainedError' || error.name === 'NotFoundError')) {
                    return openCamera(attemptIndex + 1);
                }
                throw error;
            });
        };

        openCamera().then(stream => {
            cameraStream = stream;
            video.srcObject = stream;
            lastScannedValue = null;
            lastScannedAt = 0;

            video.addEventListener('loadedmetadata', () => {
                video.play().catch(err => {
                    console.error('Play error:', err);
                    showScannedMessage('Camera opened but the preview could not start: ' + err.message, true);
                });
            }, { once: true });

            scannerActive = true;
            updateCameraButtons();
            setCameraStatus('Camera Live', true);
            showScannedMessage('Camera started. Hold the passenger ticket QR code steady inside the frame.');
            scanQRCode();
        }).catch(err => {
            console.error('Camera error:', err);
            let errorMsg = 'Error accessing camera: ' + err.message;
            if (err.name === 'NotAllowedError') {
                errorMsg = '❌ Camera permission denied.\n\nPlease:\n1. Go to your browser settings\n2. Allow camera access for this site\n3. Try again';
            } else if (err.name === 'NotFoundError') {
                errorMsg = '❌ No camera found on this device.\n\nPlease check if your device has a camera or try a different browser.';
            } else if (err.name === 'NotReadableError') {
                errorMsg = '❌ Camera is already in use by another app.\n\nPlease close other apps using the camera and try again.';
            }
            scannerActive = false;
            updateCameraButtons();
            setCameraStatus('Camera Offline');
            showScannedMessage(errorMsg.replace(/\n+/g, ' '), true);
        });
    }
    
    function stopCamera() {
        if (!cameraStream) {
            scannerActive = false;
            updateCameraButtons();
            setCameraStatus('Camera Offline');
            return;
        }

        if (cameraStream) {
            cameraStream.getTracks().forEach(track => track.stop());
            cameraStream = null;
            const video = getVideoElement();
            if (video) {
                video.srcObject = null;
            }
            scannerActive = false;
            updateCameraButtons();
            setCameraStatus('Camera Offline');
            showScannedMessage('Camera stopped. You can start it again whenever you are ready.');
        }
    }
    
    function scanQRCode() {
        if (!scannerActive) return;
        
        const video = getVideoElement();
        if (!video || video.videoWidth === 0 || video.videoHeight === 0) {
            // Video not yet ready, try again
            setTimeout(scanQRCode, 100);
            return;
        }
        
        const canvas = document.createElement('canvas');
        const context = canvas.getContext('2d');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        
        context.drawImage(video, 0, 0, canvas.width, canvas.height);
        
        const imageData = context.getImageData(0, 0, canvas.width, canvas.height);
        const code = jsQR(imageData.data, imageData.width, imageData.height);
        
        if (code) {
            const scannedValue = String(code.data).trim();
            const now = Date.now();

            if (scannedValue === lastScannedValue && now - lastScannedAt < 2000) {
                requestAnimationFrame(scanQRCode);
                return;
            }

            lastScannedValue = scannedValue;
            lastScannedAt = now;

            const ticketId = extractTicketIdFromQRCode(scannedValue);
            if (!isNaN(ticketId) && ticketId > 0) {
                showScannedMessage('Passenger ticket detected. Loading details for ticket #' + ticketId + '.');
                fetchPassengerDetails(ticketId);
                stopCamera();
            } else {
                showScannedMessage('Scanned QR code is not a valid passenger ticket for boarding.', true);
            }
        }
        
        requestAnimationFrame(scanQRCode);
    }
    
    function fetchPassengerDetails(ticketId) {
        setPassengerLoading('Loading passenger booking details...');

        fetch(BASE_URL + 'operator/get-passenger', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                ticket_id: String(ticketId),
                schedule_id: String(selectedScheduleId)
            }).toString()
        })
        .then(r => {
            if (!r.ok) {
                throw new Error('Unable to load passenger details right now.');
            }
            return r.json();
        })
        .then(data => {
            if (data.success) {
                displayPassengerDetails(data.passenger, ticketId);
                showScannedMessage(data.message || 'Passenger details loaded successfully.');
            } else {
                setPassengerPlaceholder(data.message || 'Passenger not found for this schedule.');
                showScannedMessage(data.message || 'Passenger not found', true);
            }
        })
        .catch(e => {
            console.error(e);
            setPassengerPlaceholder('Passenger details could not be loaded. Please scan again.');
            showScannedMessage(e.message || 'Passenger details could not be loaded.', true);
        });
    }
    
    function displayPassengerDetails(passenger, ticketId) {
        const statusMeta = getStatusMeta(passenger.status);
        const routeText = escapeHtml((passenger.source || 'N/A') + ' → ' + (passenger.destination || 'N/A'));
        const html = `
            <div class="passenger-shell">
                <div class="passenger-header">
                    <div>
                        <h3>${escapeHtml(passenger.name)}</h3>
                        <p>Passenger record loaded from the scanned ticket. Review the route, seating, and payment state before processing boarding.</p>
                    </div>
                    <div class="passenger-ticket-badge">${escapeHtml(passenger.ticket_code || ('TICKET-' + ticketId))}</div>
                </div>
                <div class="passenger-highlights">
                    <div class="highlight-card">
                        <label>Route</label>
                        <strong>${routeText}</strong>
                    </div>
                    <div class="highlight-card">
                        <label>Assigned Bus</label>
                        <strong>${escapeHtml(passenger.bus_name || 'N/A')}</strong>
                    </div>
                </div>
                <div class="passenger-card">
                    <div class="detail-list">
                        <div class="detail-row">
                            <label>Status</label>
                            <span><span class="status-badge ${statusMeta.className}">${statusMeta.label}</span></span>
                        </div>
                        <div class="detail-row">
                            <label>Seat Count</label>
                            <span>${escapeHtml(passenger.seats)} seat(s)</span>
                        </div>
                        <div class="detail-row">
                            <label>Departure</label>
                            <span>${escapeHtml(formatDateTime(passenger.departure_time))}</span>
                        </div>
                        <div class="detail-row">
                            <label>Phone</label>
                            <span>${escapeHtml(passenger.phone || 'N/A')}</span>
                        </div>
                        <div class="detail-row">
                            <label>Email</label>
                            <span class="muted">${escapeHtml(passenger.email || 'N/A')}</span>
                        </div>
                        <div class="detail-row">
                            <label>Booked At</label>
                            <span class="muted">${escapeHtml(formatDateTime(passenger.booked_at))}</span>
                        </div>
                        ${passenger.boarded_at ? `
                        <div class="detail-row">
                            <label>Boarded At</label>
                            <span class="muted">${escapeHtml(formatDateTime(passenger.boarded_at))}</span>
                        </div>` : ''}
                    </div>
                    <div class="action-btns">
                    ${passenger.actions && passenger.actions.can_approve_payment ? 
                        `<button class="btn-success" onclick="approvePayment(${ticketId})">Approve Payment</button>` : 
                        ''}
                    ${passenger.actions && passenger.actions.can_board ? 
                        `<button class="btn-primary" onclick="boardPassenger(${ticketId})">Mark as Boarded</button>` : 
                        ''}
                    ${passenger.status === 'boarded' ? 
                        `<button class="btn-danger" type="button" disabled>Already Boarded</button>` : ''}
                    </div>
                </div>
            </div>
        `;

        const passengerDetails = document.getElementById('passengerDetails');
        passengerDetails.className = '';
        passengerDetails.innerHTML = html;
    }
    
    function approvePayment(ticketId) {
        if (!confirm('Did you receive payment from this passenger? Approve payment?')) return;
        
        fetch('<?php echo BASE_URL; ?>operator/boarding', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'action=approve_payment&ticket_id=' + ticketId
        })
        .then(r => {
            if (!r.ok) {
                throw new Error('Payment approval request failed.');
            }
            return r.json();
        })
        .then(data => {
            if (data.success) {
                showScannedMessage(data.message || 'Payment approved successfully.');
                location.reload();
            } else {
                showScannedMessage(data.message || 'Payment approval failed.', true);
            }
        })
        .catch(e => showScannedMessage(e.message || 'Payment approval failed.', true));
    }
    
    function boardPassenger(ticketId) {
        if (!confirm('Board this passenger?')) return;
        
        fetch('<?php echo BASE_URL; ?>operator/boarding', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'action=board_passenger&ticket_id=' + ticketId
        })
        .then(r => {
            if (!r.ok) {
                throw new Error('Boarding request failed.');
            }
            return r.json();
        })
        .then(data => {
            if (data.success) {
                showScannedMessage(data.message || 'Passenger boarded successfully.');
                location.reload();
            } else {
                showScannedMessage(data.message || 'Passenger boarding failed.', true);
            }
        })
        .catch(e => showScannedMessage(e.message || 'Passenger boarding failed.', true));
    }

    setCameraStatus('Camera Offline');
    updateCameraButtons();
</script>

</body>
</html>
