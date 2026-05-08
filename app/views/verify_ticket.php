<?php
require_once APP_ROOT . '/config/db.php';

// Get ticket ID and verification code from URL
$ticket_id = intval($_GET['ticket_id'] ?? 0);
$code = $_GET['code'] ?? '';

// Verify the ticket exists and code matches using prepared statement
$stmt = $conn->prepare("SELECT t.*, u.name as passenger_name, u.email as passenger_email, u.phone as passenger_phone, 
                        b.bus_name, s.source, s.destination, s.departure_time 
                FROM tickets t 
                LEFT JOIN users u ON t.user_id = u.id
                LEFT JOIN schedules s ON t.schedule_id = s.id
                LEFT JOIN buses b ON s.bus_id = b.id
                WHERE t.id = ?");
$stmt->bind_param("i", $ticket_id);
$stmt->execute();
$result = $stmt->get_result();

if (!$result || $result->num_rows === 0) {
    $ticket = null;
    $valid = false;
} else {
    $ticket = $result->fetch_assoc();
    $expected_code = 'TICKET-' . $ticket['id'];
    $valid = ($code === $expected_code);
}
$stmt->close();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Verification - Scan Result</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .scan-container {
            max-width: 600px;
            width: 100%;
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }
        
        .scan-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        
        .scan-header h1 {
            font-size: 1.8rem;
            margin-bottom: 10px;
            font-weight: 700;
        }
        
        .status-badge {
            display: inline-block;
            padding: 6px 14px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
            margin-top: 10px;
        }
        
        .status-badge.valid {
            background: #4caf50;
            color: white;
        }
        
        .status-badge.invalid {
            background: #f44336;
            color: white;
        }
        
        .scan-content {
            padding: 30px;
        }
        
        .alert {
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 600;
            text-align: center;
        }
        
        .alert.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .info-section {
            margin-bottom: 25px;
        }
        
        .section-title {
            font-size: 0.9rem;
            font-weight: 700;
            color: #667eea;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        
        .info-item {
            background: #f8f9fa;
            padding: 12px;
            border-radius: 6px;
            border-left: 3px solid #667eea;
        }
        
        .info-item.full {
            grid-column: 1 / -1;
        }
        
        .info-label {
            font-size: 0.8rem;
            color: #666;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }
        
        .info-value {
            font-size: 1.1rem;
            color: #0f1c33;
            font-weight: 700;
        }
        
        .status-section {
            background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%);
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            margin: 20px 0;
        }
        
        .status-label {
            font-size: 0.85rem;
            color: #666;
            margin-bottom: 8px;
        }
        
        .status-value {
            font-size: 1.3rem;
            font-weight: 700;
            color: #667eea;
            text-transform: uppercase;
        }
        
        .status-value.pending {
            color: #ff9800;
        }
        
        .status-value.approved {
            color: #4caf50;
        }
        
        .status-value.boarded {
            color: #2196f3;
        }
        
        .status-value.cancelled {
            color: #f44336;
        }
        
        .action-buttons {
            display: flex;
            gap: 10px;
            margin-top: 20px;
            flex-wrap: wrap;
        }
        
        .btn {
            flex: 1;
            min-width: 140px;
            padding: 12px 20px;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            text-align: center;
            display: inline-block;
        }
        
        .btn-primary {
            background: #667eea;
            color: white;
        }
        
        .btn-primary:hover {
            background: #5568d3;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
            box-shadow: 0 4px 12px rgba(108, 117, 125, 0.4);
            transform: translateY(-2px);
        }
        
        .verify-button {
            background: #4caf50;
            color: white;
            margin-top: 15px;
            width: 100%;
            padding: 15px;
            font-size: 1.1rem;
        }
        
        .verify-button:hover {
            background: #45a049;
        }
        
        .verify-button:disabled {
            background: #cccccc;
            cursor: not-allowed;
        }
        
        @media (max-width: 768px) {
            body {
                padding: 10px;
            }
            
            .scan-container {
                border-radius: 12px;
            }
            
            .scan-header {
                padding: 25px 15px;
            }
            
            .scan-header h1 {
                font-size: 1.5rem;
                margin-bottom: 10px;
            }
            
            .status-badge {
                padding: 6px 12px;
                font-size: 0.8rem;
            }
            
            .scan-content {
                padding: 20px;
            }
            
            .info-grid {
                grid-template-columns: 1fr !important;
                gap: 12px;
            }
            
            .info-item {
                grid-column: 1 / -1 !important;
                padding: 10px;
            }
            
            .info-label {
                font-size: 0.75rem;
                margin-bottom: 5px;
            }
            
            .info-value {
                font-size: 1rem;
            }
            
            .section-title {
                font-size: 0.85rem;
                margin-bottom: 10px;
            }
            
            .status-section {
                padding: 12px;
                margin: 15px 0;
            }
            
            .status-value {
                font-size: 1.2rem;
            }
            
            .action-buttons {
                flex-direction: column;
                gap: 8px;
                margin-top: 15px;
            }
            
            .btn {
                width: 100% !important;
                padding: 12px 15px;
                font-size: 0.95rem;
                min-width: auto;
            }
            
            .alert {
                padding: 15px;
                font-size: 0.9rem;
                border-radius: 6px;
            }
        }
        
        @media (max-width: 480px) {
            .scan-header {
                padding: 20px 12px;
            }
            
            .scan-header h1 {
                font-size: 1.3rem;
            }
            
            .scan-content {
                padding: 15px;
            }
            
            .info-section {
                margin-bottom: 18px;
            }
            
            .info-item {
                padding: 8px !important;
            }
            
            .info-label {
                font-size: 0.7rem;
            }
            
            .info-value {
                font-size: 0.95rem;
            }
            
            .btn {
                padding: 10px 12px !important;
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
<div class="scan-container">
    <div class="scan-header">
        <h1>🎫 Ticket Verification</h1>
        <?php if ($valid): ?>
            <span class="status-badge valid">✓ VALID TICKET</span>
        <?php else: ?>
            <span class="status-badge invalid">✗ INVALID TICKET</span>
        <?php endif; ?>
    </div>
    
    <div class="scan-content">
        <?php if ($valid && $ticket): ?>
            <!-- Valid Ticket - Show Details -->
            <div class="alert success">
                ✓ Ticket verified successfully! Passenger details below.
            </div>
            
            <!-- Passenger Information -->
            <div class="info-section">
                <div class="section-title">👤 Passenger Information</div>
                <div class="info-grid">
                    <div class="info-item full">
                        <div class="info-label">Passenger Name</div>
                        <div class="info-value"><?php echo htmlspecialchars($ticket['passenger_name'] ?? 'N/A'); ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Email</div>
                        <div class="info-value" style="font-size: 0.95rem;"><?php echo htmlspecialchars($ticket['passenger_email'] ?? 'N/A'); ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Phone</div>
                        <div class="info-value"><?php echo htmlspecialchars($ticket['passenger_phone'] ?? 'N/A'); ?></div>
                    </div>
                </div>
            </div>
            
            <!-- Ticket Information -->
            <div class="info-section">
                <div class="section-title">🎫 Ticket Information</div>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Ticket ID</div>
                        <div class="info-value">#<?php echo intval($ticket['id']); ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Number of Seats</div>
                        <div class="info-value"><?php echo intval($ticket['seats']); ?></div>
                    </div>
                </div>
            </div>
            
            <!-- Bus Information -->
            <div class="info-section">
                <div class="section-title">🚌 Bus Information</div>
                <div class="info-grid">
                    <div class="info-item full">
                        <div class="info-label">Bus Name</div>
                        <div class="info-value"><?php echo htmlspecialchars($ticket['bus_name'] ?? 'N/A'); ?></div>
                    </div>
                    <div class="info-item full">
                        <div class="info-label">Route</div>
                        <div class="info-value"><?php echo htmlspecialchars($ticket['source'] ?? 'N/A'); ?> → <?php echo htmlspecialchars($ticket['destination'] ?? 'N/A'); ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Date</div>
                        <div class="info-value"><?php echo date('M d, Y', strtotime($ticket['departure_time'])); ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Time</div>
                        <div class="info-value"><?php echo date('H:i', strtotime($ticket['departure_time'])); ?></div>
                    </div>
                </div>
            </div>
            
            <!-- Booking Status -->
            <div class="status-section">
                <div class="status-label">Current Status</div>
                <div class="status-value <?php echo strtolower($ticket['status']); ?>">
                    <?php echo htmlspecialchars(ucfirst($ticket['status'])); ?>
                </div>
            </div>
            
            <!-- Booking Date -->
            <div class="info-section">
                <div class="section-title">📅 Booking Details</div>
                <div class="info-grid">
                    <div class="info-item full">
                        <div class="info-label">Booked On</div>
                        <div class="info-value"><?php echo date('M d, Y H:i:s', strtotime($ticket['created_at'])); ?></div>
                    </div>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="action-buttons">
                <button class="btn btn-primary" onclick="window.print()">🖨️ Print Details</button>
                <button class="btn btn-secondary" onclick="location.reload()">🔄 Scan Another</button>
            </div>
        
        <?php else: ?>
            <!-- Invalid or Not Found Ticket -->
            <div class="alert error">
                ✗ Ticket not found or invalid verification code!
            </div>
            
            <?php if ($_GET['ticket_id'] ?? false): ?>
                <div class="info-section">
                    <div class="section-title">⚠️ Error Details</div>
                    <div class="info-grid">
                        <div class="info-item full">
                            <div class="info-label">Ticket ID Scanned</div>
                            <div class="info-value">#<?php echo htmlspecialchars($_GET['ticket_id']); ?></div>
                        </div>
                        <div class="info-item full">
                            <div class="info-label">Reason</div>
                            <div class="info-value" style="font-size: 1rem; color: #f44336;">
                                <?php 
                                if ($ticket === null) {
                                    echo "Ticket record not found in system.";
                                } else {
                                    echo "Verification code mismatch. This ticket may be fraudulent.";
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="info-section">
                    <p style="color: #666; text-align: center; padding: 20px;">
                        Scan a valid QR code from a bus ticket to verify passenger details.
                    </p>
                </div>
            <?php endif; ?>
            
            <div class="action-buttons">
                <button class="btn btn-secondary" onclick="window.history.back()">← Go Back</button>
                <button class="btn btn-primary" onclick="location.reload()">🔄 Try Again</button>
            </div>
        
        <?php endif; ?>
    </div>
</div>
</body>
</html>