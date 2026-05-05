<?php
require_once APP_ROOT . '/config/db.php';
require_once APP_ROOT . '/app/core/Auth.php';
require_once APP_ROOT . '/app/controllers/OperatorController.php';

requireRole('operator');
$operator = currentUser();
$schedules = OperatorController::getMySchedules($operator['id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Operator Dashboard - Bus Ticketing System</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            background: #f5f7fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            color: #1f2937;
        }
        
        /* Navigation */
        nav {
            background: white;
            border-bottom: 2px solid #e5e7eb;
            padding: 0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        
        .nav-container {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 24px;
        }
        
        .nav-brand {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1f2937;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .nav-links {
            display: flex;
            gap: 8px;
            align-items: center;
        }
        
        .nav-link {
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            color: #4b5563;
            font-weight: 500;
            font-size: 0.875rem;
            transition: all 0.2s;
            border: 1px solid transparent;
        }
        
        .nav-link:hover {
            background: #f3f4f6;
            color: #1f2937;
        }
        
        .nav-link-primary {
            background: #3b82f6;
            color: white;
            border-color: #3b82f6;
        }
        
        .nav-link-primary:hover {
            background: #2563eb;
            border-color: #2563eb;
        }
        
        /* Main Container */
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 24px;
        }
        
        /* Page Header */
        .page-header {
            margin-bottom: 24px;
        }
        
        .page-title {
            font-size: 1.875rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 4px;
        }
        
        .page-subtitle {
            color: #6b7280;
            font-size: 0.875rem;
        }
        
        /* QR Scanner Section */
        .qr-section {
            background: white;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            border: 1px solid #e5e7eb;
        }
        
        .qr-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }
        
        .qr-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: #1f2937;
        }
        
        .scan-btn {
            background: #10b981;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .scan-btn:hover {
            background: #059669;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(16,185,129,0.3);
        }
        
        .scan-btn:disabled {
            background: #9ca3af;
            cursor: not-allowed;
            transform: none;
        }
        
        #qr-reader {
            max-width: 500px;
            margin: 0 auto;
            display: none;
        }
        
        #qr-reader.active {
            display: block;
        }
        
        /* Schedule Cards */
        .schedules-grid {
            display: grid;
            gap: 20px;
        }
        
        .schedule-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            border: 1px solid #e5e7eb;
            overflow: hidden;
        }
        
        .schedule-header-new {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px 24px;
            color: white;
        }
        
        .schedule-route-new {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .route-arrow {
            font-size: 1.25rem;
        }
        
        .schedule-meta {
            display: flex;
            gap: 24px;
            font-size: 0.875rem;
            opacity: 0.95;
        }
        
        .schedule-body {
            padding: 0;
        }
        
        /* Stats Row */
        .stats-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            border-bottom: 1px solid #e5e7eb;
        }
        
        .stat-item {
            padding: 16px 20px;
            border-right: 1px solid #e5e7eb;
            text-align: center;
        }
        
        .stat-item:last-child {
            border-right: none;
        }
        
        .stat-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #6b7280;
            font-weight: 600;
            margin-bottom: 6px;
        }
        
        .stat-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1f2937;
        }
        
        .stat-value.pending {
            color: #f59e0b;
        }
        
        .stat-value.approved {
            color: #3b82f6;
        }
        
        .stat-value.boarded {
            color: #10b981;
        }
        
        /* Tables */
        .passengers-section {
            padding: 24px;
        }
        
        .section-title {
            font-size: 1rem;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .badge-pending {
            background: #fef3c7;
            color: #92400e;
        }
        
        .badge-approved {
            background: #dbeafe;
            color: #1e40af;
        }
        
        .badge-boarded {
            background: #d1fae5;
            color: #065f46;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.875rem;
        }
        
        thead {
            background: #f9fafb;
            border-bottom: 2px solid #e5e7eb;
        }
        
        th {
            text-align: left;
            padding: 12px;
            font-weight: 600;
            color: #6b7280;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
        }
        
        tbody tr {
            border-bottom: 1px solid #f3f4f6;
            transition: background 0.2s;
        }
        
        tbody tr:hover {
            background: #f9fafb;
        }
        
        td {
            padding: 12px;
            color: #374151;
        }
        
        .passenger-name {
            font-weight: 600;
            color: #1f2937;
        }
        
        .action-btn {
            padding: 6px 12px;
            border-radius: 6px;
            border: none;
            font-size: 0.75rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .btn-approve {
            background: #3b82f6;
            color: white;
        }
        
        .btn-approve:hover {
            background: #2563eb;
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(59,130,246,0.3);
        }
        
        .btn-board {
            background: #10b981;
            color: white;
        }
        
        .btn-board:hover {
            background: #059669;
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(16,185,129,0.3);
        }
        
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #9ca3af;
        }
        
        .empty-icon {
            font-size: 3rem;
            margin-bottom: 8px;
        }
        
        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 2000;
            align-items: center;
            justify-content: center;
        }
        
        .modal.active {
            display: flex;
        }
        
        .modal-content {
            background: white;
            border-radius: 12px;
            padding: 24px;
            max-width: 500px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .modal-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1f2937;
        }
        
        .close-modal {
            background: #f3f4f6;
            border: none;
            width: 32px;
            height: 32px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1.25rem;
            color: #6b7280;
        }
        
        .close-modal:hover {
            background: #e5e7eb;
        }
        
        .ticket-details {
            background: #f9fafb;
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 16px;
        }
        
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .detail-row:last-child {
            border-bottom: none;
        }
        
        .detail-label {
            font-weight: 600;
            color: #6b7280;
        }
        
        .detail-value {
            color: #1f2937;
            font-weight: 500;
        }
        
        @media (max-width: 768px) {
            .nav-container {
                flex-direction: column;
                gap: 12px;
            }
            
            .stats-row {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .schedule-meta {
                flex-direction: column;
                gap: 8px;
            }
            
            table {
                font-size: 0.75rem;
            }
            
            th, td {
                padding: 8px;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav>
        <div class="nav-container">
            <div class="nav-brand">
                🚌 Operator Dashboard
            </div>
            <div class="nav-links">
                <a href="<?php echo BASE_URL; ?>operator/schedules" class="nav-link">Manage Schedules</a>
                <a href="<?php echo BASE_URL; ?>" class="nav-link">Home</a>
                <a href="<?php echo BASE_URL; ?>logout" class="nav-link-primary">Logout</a>
            </div>
        </div>
    </nav>

    <!-- Main Container -->
    <div class="container">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-title">Welcome, <?php echo htmlspecialchars($operator['name']); ?></h1>
            <p class="page-subtitle">Manage your trips, scan tickets, and track passenger boarding</p>
        </div>

        <!-- QR Scanner Section -->
        <div class="qr-section">
            <div class="qr-header">
                <h2 class="qr-title">🎫 QR Code Scanner</h2>
                <button id="startScanBtn" class="scan-btn">
                    <span>📷</span> Start Scanning
                </button>
            </div>
            <div id="qr-reader"></div>
            <div id="scan-result" style="margin-top: 16px; display: none;"></div>
        </div>

        <!-- Schedules -->
        <div class="schedules-grid">
            <?php if (empty($schedules)): ?>
                <div class="empty-state">
                    <div class="empty-icon">📅</div>
                    <h3>No Schedules Assigned</h3>
                    <p>You don't have any trips assigned yet. Contact admin to assign trips.</p>
                </div>
            <?php else: ?>
                <?php foreach ($schedules as $schedule): 
                    $scheduleId = $schedule['id'];
                    $summary = OperatorController::getTripSummary($scheduleId);
                    $pendingPayments = OperatorController::getPendingPayments($scheduleId);
                    $readyToBoard = $conn->query("SELECT t.*, u.name, u.phone, u.email FROM tickets t LEFT JOIN users u ON t.user_id = u.id WHERE t.schedule_id = $scheduleId AND t.status = 'approved' ORDER BY t.created_at")->fetch_all(MYSQLI_ASSOC);
                    $boardedPassengers = OperatorController::getBoardedPassengers($scheduleId);
                ?>
                <div class="schedule-card">
                    <!-- Schedule Header -->
                    <div class="schedule-header-new">
                        <div class="schedule-route-new">
                            <span><?php echo htmlspecialchars($schedule['source']); ?></span>
                            <span class="route-arrow">→</span>
                            <span><?php echo htmlspecialchars($schedule['destination']); ?></span>
                        </div>
                        <div class="schedule-meta">
                            <span>🚌 <?php echo htmlspecialchars($schedule['bus_name']); ?></span>
                            <span>📅 <?php echo date('M j, Y', strtotime($schedule['departure_time'])); ?></span>
                            <span>🕐 <?php echo date('g:i A', strtotime($schedule['departure_time'])); ?></span>
                            <span>💺 <?php echo $schedule['total_seats']; ?> Seats</span>
                        </div>
                    </div>

                    <!-- Statistics Row -->
                    <div class="stats-row">
                        <div class="stat-item">
                            <div class="stat-label">Total Bookings</div>
                            <div class="stat-value"><?php echo $summary['total_tickets'] ?? 0; ?></div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-label">Pending Payment</div>
                            <div class="stat-value pending"><?php echo $summary['pending_count'] ?? 0; ?></div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-label">Ready to Board</div>
                            <div class="stat-value approved"><?php echo $summary['approved_count'] ?? 0; ?></div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-label">Boarded</div>
                            <div class="stat-value boarded"><?php echo $summary['boarded_count'] ?? 0; ?></div>
                        </div>
                    </div>

                    <!-- Passengers Section -->
                    <div class="passengers-section">
                        <!-- Pending Payments -->
                        <?php if (!empty($pendingPayments)): ?>
                        <h3 class="section-title">
                            ⏳ Pending Payments <span class="badge badge-pending"><?php echo count($pendingPayments); ?></span>
                        </h3>
                        <table>
                            <thead>
                                <tr>
                                    <th>Passenger</th>
                                    <th>Contact</th>
                                    <th>Seats</th>
                                    <th>Booked At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pendingPayments as $ticket): ?>
                                <tr>
                                    <td class="passenger-name"><?php echo htmlspecialchars($ticket['name']); ?></td>
                                    <td><?php echo htmlspecialchars($ticket['phone']); ?></td>
                                    <td><?php echo $ticket['seats']; ?></td>
                                    <td><?php echo date('M j, g:i A', strtotime($ticket['created_at'])); ?></td>
                                    <td>
                                        <form method="POST" action="process_trip.php" style="display: inline;">
                                            <input type="hidden" name="action" value="approve_payment">
                                            <input type="hidden" name="ticket_id" value="<?php echo $ticket['id']; ?>">
                                            <input type="hidden" name="schedule_id" value="<?php echo $scheduleId; ?>">
                                            <button type="submit" class="action-btn btn-approve">Approve Payment</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php endif; ?>

                        <!-- Ready to Board -->
                        <?php if (!empty($readyToBoard)): ?>
                        <h3 class="section-title" style="margin-top: 24px;">
                            ✅ Ready to Board <span class="badge badge-approved"><?php echo count($readyToBoard); ?></span>
                        </h3>
                        <table>
                            <thead>
                                <tr>
                                    <th>Passenger</th>
                                    <th>Contact</th>
                                    <th>Email</th>
                                    <th>Seats</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($readyToBoard as $ticket): ?>
                                <tr>
                                    <td class="passenger-name"><?php echo htmlspecialchars($ticket['name']); ?></td>
                                    <td><?php echo htmlspecialchars($ticket['phone']); ?></td>
                                    <td><?php echo htmlspecialchars($ticket['email']); ?></td>
                                    <td><?php echo $ticket['seats']; ?></td>
                                    <td>
                                        <form method="POST" action="process_trip.php" style="display: inline;">
                                            <input type="hidden" name="action" value="board_passenger">
                                            <input type="hidden" name="ticket_id" value="<?php echo $ticket['id']; ?>">
                                            <input type="hidden" name="schedule_id" value="<?php echo $scheduleId; ?>">
                                            <button type="submit" class="action-btn btn-board">Mark Boarded</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php endif; ?>

                        <!-- Boarded Passengers -->
                        <?php if (!empty($boardedPassengers)): ?>
                        <h3 class="section-title" style="margin-top: 24px;">
                            ✈️ Boarded Passengers <span class="badge badge-boarded"><?php echo count($boardedPassengers); ?></span>
                        </h3>
                        <table>
                            <thead>
                                <tr>
                                    <th>Passenger</th>
                                    <th>Contact</th>
                                    <th>Seats</th>
                                    <th>Boarded At</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($boardedPassengers as $ticket): ?>
                                <tr>
                                    <td class="passenger-name"><?php echo htmlspecialchars($ticket['name']); ?></td>
                                    <td><?php echo htmlspecialchars($ticket['phone']); ?></td>
                                    <td><?php echo $ticket['seats']; ?></td>
                                    <td><?php echo $ticket['boarded_at'] ? date('M j, g:i A', strtotime($ticket['boarded_at'])) : 'N/A'; ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php endif; ?>

                        <?php if (empty($pendingPayments) && empty($readyToBoard) && empty($boardedPassengers)): ?>
                        <div class="empty-state">
                            <div class="empty-icon">👥</div>
                            <p>No passengers yet for this trip</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Ticket Details Modal -->
    <div id="ticketModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Ticket Details</h2>
                <button class="close-modal" onclick="closeModal()">&times;</button>
            </div>
            <div id="modalBody"></div>
        </div>
    </div>

    <script>
        let html5QrCode;
        let isScanning = false;

        // QR Scanner
        document.getElementById('startScanBtn').addEventListener('click', function() {
            const qrReader = document.getElementById('qr-reader');
            const btn = this;
            
            if (!isScanning) {
                qrReader.classList.add('active');
                btn.textContent = '⏹️ Stop Scanning';
                btn.style.background = '#ef4444';
                
                html5QrCode = new Html5Qrcode("qr-reader");
                html5QrCode.start(
                    { facingMode: "environment" },
                    { fps: 10, qrbox: 250 },
                    onScanSuccess,
                    onScanError
                ).catch(err => {
                    alert('Unable to start camera: ' + err);
                    resetScanner();
                });
                
                isScanning = true;
            } else {
                resetScanner();
            }
        });

        function onScanSuccess(decodedText, decodedResult) {
            // Process QR code
            document.getElementById('scan-result').innerHTML = 
                `<div style="background: #d1fae5; color: #065f46; padding: 12px; border-radius: 8px; font-weight: 600;">
                    ✅ Scanned: ${decodedText}
                </div>`;
            document.getElementById('scan-result').style.display = 'block';
            
            // Fetch ticket details
            fetchTicketDetails(decodedText);
            
            resetScanner();
        }

        function onScanError(error) {
            // Ignore scan errors
        }

        function resetScanner() {
            if (html5QrCode) {
                html5QrCode.stop().then(() => {
                    document.getElementById('qr-reader').classList.remove('active');
                    document.getElementById('startScanBtn').innerHTML = '<span>📷</span> Start Scanning';
                    document.getElementById('startScanBtn').style.background = '#10b981';
                    isScanning = false;
                });
            }
        }

        function fetchTicketDetails(ticketId) {
            fetch(`get_passenger_details.php?ticket_id=${ticketId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showTicketModal(data.ticket);
                    } else {
                        alert(data.message || 'Ticket not found');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to fetch ticket details');
                });
        }

        function showTicketModal(ticket) {
            const modal = document.getElementById('ticketModal');
            const modalBody = document.getElementById('modalBody');
            
            const statusColors = {
                'pending': '#f59e0b',
                'approved': '#3b82f6',
                'boarded': '#10b981',
                'cancelled': '#ef4444'
            };
            
            modalBody.innerHTML = `
                <div class="ticket-details">
                    <div class="detail-row">
                        <span class="detail-label">Passenger:</span>
                        <span class="detail-value">${ticket.passenger_name}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Phone:</span>
                        <span class="detail-value">${ticket.phone}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Route:</span>
                        <span class="detail-value">${ticket.source} → ${ticket.destination}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Seats:</span>
                        <span class="detail-value">${ticket.seats}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Status:</span>
                        <span class="detail-value" style="color: ${statusColors[ticket.status]}; font-weight: 700; text-transform: uppercase;">
                            ${ticket.status}
                        </span>
                    </div>
                </div>
                ${ticket.status === 'approved' ? `
                    <form method="POST" action="process_trip.php">
                        <input type="hidden" name="action" value="board_passenger">
                        <input type="hidden" name="ticket_id" value="${ticket.id}">
                        <input type="hidden" name="schedule_id" value="${ticket.schedule_id}">
                        <button type="submit" class="action-btn btn-board" style="width: 100%; padding: 12px;">
                            Mark as Boarded
                        </button>
                    </form>
                ` : ''}
            `;
            
            modal.classList.add('active');
        }

        function closeModal() {
            document.getElementById('ticketModal').classList.remove('active');
        }

        // Close modal when clicking outside
        document.getElementById('ticketModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });
    </script>
</body>
</html>
