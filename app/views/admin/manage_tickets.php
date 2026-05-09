<?php
require_once APP_ROOT . '/config/db.php';
require_once APP_ROOT . '/app/core/Auth.php';
require_once APP_ROOT . '/app/controllers/AdminController.php';

requireRole('admin');

$user = currentUser();

// Handle ticket cancellation FIRST (before any HTML output)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $ticket_id = intval($_POST['ticket_id'] ?? 0);
    $action = $_POST['action'] ?? '';
    
    if ($ticket_id > 0 && $action === 'cancel') {
        $result = AdminController::cancelTicket($ticket_id);
        if ($result['success']) {
            header('Location: manage_tickets.php?success=' . urlencode($result['message']));
            exit;
        } else {
            header('Location: manage_tickets.php?error=' . urlencode($result['message']));
            exit;
        }
    }
}

// Get all tickets with booking information
$tickets = AdminController::getAllTickets();

$filter = $_GET['status'] ?? '';
if ($filter) {
    $tickets = array_filter($tickets, function($ticket) use ($filter) {
        return $ticket['status'] === $filter;
    });
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Tickets - Admin</title>
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
        
        .container { max-width: 1200px; margin: 0 auto; padding: 30px 20px; }
        
        .header { background: white; padding: 25px; border-radius: 12px; margin-bottom: 30px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08); }
        .header h1 { color: #0f1c33; margin-bottom: 8px; }
        .header p { color: #666; }
        
        .filter-section { background: white; padding: 20px; border-radius: 12px; margin-bottom: 20px; display: flex; gap: 15px; flex-wrap: wrap; align-items: center; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08); }
        
        .filter-section a {
            padding: 10px 20px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            border: 1px solid #0072ff;
            color: #0072ff;
        }
        
        .filter-section a.active {
            background: #0072ff;
            color: white;
        }
        
        .filter-section a:hover {
            background: #0072ff;
            color: white;
        }
        
        .table-container { background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08); overflow: hidden; }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        thead {
            background: #f5f7fa;
            border-bottom: 2px solid #e0e0e0;
        }
        
        th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
            color: #0f1c33;
        }
        
        td {
            padding: 15px;
            border-bottom: 1px solid #e0e0e0;
            color: #666;
        }
        
        tr:hover { background: #f9f9f9; }
        
        .badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        
        .badge-booked { background: #cfe2ff; color: #084298; }
        .badge-pending { background: #fff3cd; color: #664d03; }
        .badge-confirmed { background: #d1e7dd; color: #0f5132; }
        .badge-approved { background: #d1e7dd; color: #0f5132; }
        .badge-completed { background: #d1e7dd; color: #0f5132; }
        .badge-cancelled { background: #f8d7da; color: #842029; }
        
        .btn {
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.85rem;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
        }
        
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        
        .btn-danger:hover {
            background: #c82333;
        }
        
        .no-data { text-align: center; padding: 40px; color: #999; }
        .no-data p { font-size: 1.1rem; }
        
        @media (max-width: 768px) {
            table { font-size: 0.9rem; }
            th, td { padding: 10px; }
        }
        
        /* Toast Notification Styles */
        .toast-container {
            position: fixed;
            top: 90px;
            right: 20px;
            z-index: 9999;
            max-width: 400px;
        }
        
        .toast {
            background: white;
            border-radius: 8px;
            padding: 18px 24px;
            margin-bottom: 12px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
            display: flex;
            align-items: center;
            gap: 12px;
            animation: slideIn 0.3s ease-out;
            border-left: 5px solid #0072ff;
        }
        
        .toast.success {
            background: #d4edda;
            color: #155724;
            border-left-color: #28a745;
        }
        
        .toast.error {
            background: #f8d7da;
            color: #842029;
            border-left-color: #dc3545;
        }
        
        .toast.info {
            background: #d1ecf1;
            color: #0c5460;
            border-left-color: #0072ff;
        }
        
        .toast-icon {
            font-size: 1.4rem;
            min-width: 24px;
            text-align: center;
        }
        
        .toast-message {
            flex: 1;
            font-weight: 600;
            font-size: 0.95rem;
        }
        
        .toast-close {
            background: none;
            border: none;
            color: inherit;
            cursor: pointer;
            font-size: 1.2rem;
            opacity: 0.6;
            transition: opacity 0.2s;
            padding: 0;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .toast-close:hover {
            opacity: 1;
        }
        
        @keyframes slideIn {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(400px);
                opacity: 0;
            }
        }
        
        .toast.hide {
            animation: slideOut 0.3s ease-out forwards;
        }
    </style>
</head>
<body>

<!-- Toast Container -->
<div id="toastContainer" class="toast-container"></div>

<nav>
    <h2><span style="font-size: 2rem; margin-right: 10px;">??</span>Book Smarter, Travel Better</h2>
    <div>
        <a href="<?php echo BASE_URL; ?>admin/dashboard">Dashboard</a>
        <a href="<?php echo BASE_URL; ?>admin/buses">Buses</a>
        <a href="<?php echo BASE_URL; ?>admin/tickets">Tickets</a>
        <a href="<?php echo BASE_URL; ?>logout">Logout</a>
    </div>
</nav>

<div class="container">
    <div style="margin-bottom: 20px;">
        <a href="javascript:history.back()" style="display: inline-block; padding: 10px 20px; background: #0072ff; color: white; border-radius: 6px; text-decoration: none; font-weight: 600; transition: all 0.3s ease;" onmouseover="this.style.background='#0056cc'" onmouseout="this.style.background='#0072ff'">? Back</a>
    </div>
    <div class="header">
        <h1>?? Manage Tickets</h1>
        <p>View and manage all ticket bookings in the system</p>
    </div>
    
    <div class="filter-section">
        <strong>Filter by Status:</strong>
        <a href="<?php echo BASE_URL; ?>admin/tickets" class="<?php echo !$filter ? 'active' : ''; ?>">All Tickets</a>
        <a href="?status=pending" class="<?php echo $filter === 'pending' ? 'active' : ''; ?>">Pending</a>
        <a href="?status=approved" class="<?php echo $filter === 'approved' ? 'active' : ''; ?>">Approved</a>
        <a href="?status=cancelled" class="<?php echo $filter === 'cancelled' ? 'active' : ''; ?>">Cancelled</a>
    </div>
    
    <div class="table-container">
        <?php if (count($tickets) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Passenger</th>
                        <th>Bus Name</th>
                        <th>Route</th>
                        <th>Seats</th>
                        <th>Departure</th>
                        <th>Status</th>
                        <th>Booked On</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tickets as $ticket): ?>
                        <tr>
                            <td>#<?php echo $ticket['id']; ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($ticket['passenger_name'] ?? 'N/A'); ?></strong><br>
                                <small><?php echo htmlspecialchars($ticket['passenger_email'] ?? ''); ?></small>
                            </td>
                            <td><?php echo htmlspecialchars($ticket['bus_name'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($ticket['source'] ?? 'N/A'); ?> ? <?php echo htmlspecialchars($ticket['destination'] ?? 'N/A'); ?></td>
                            <td><?php echo intval($ticket['seats']); ?></td>
                            <td><?php echo $ticket['departure_time'] ? date('M d, h:i A', strtotime($ticket['departure_time'])) : 'N/A'; ?></td>
                            <td>
                                <span class="badge badge-<?php echo strtolower($ticket['status']); ?>">
                                    <?php echo ucfirst($ticket['status']); ?>
                                </span>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($ticket['created_at'])); ?></td>
                            <td>
                                <?php if ($ticket['status'] !== 'cancelled'): ?>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="ticket_id" value="<?php echo $ticket['id']; ?>">
                                        <input type="hidden" name="action" value="cancel">
                                        <button type="submit" class="btn btn-danger" onclick="return confirm('Cancel this ticket?')">Cancel</button>
                                    </form>
                                <?php else: ?>
                                    <span style="color: #999;">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="no-data">
                <p>?? No tickets found</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    // Toast notification function
    function showToast(message, type = 'info', duration = 4000) {
        const toastContainer = document.getElementById('toastContainer');
        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        
        let icon = '??';
        if (type === 'success') icon = '?';
        if (type === 'error') icon = '?';
        
        toast.innerHTML = `
            <div class="toast-icon">${icon}</div>
            <div class="toast-message">${message}</div>
            <button class="toast-close" onclick="this.parentElement.classList.add('hide'); setTimeout(() => this.parentElement.remove(), 300);">×</button>
        `;
        
        toastContainer.appendChild(toast);
        
        // Auto-hide after duration
        setTimeout(() => {
            if (toast.parentElement) {
                toast.classList.add('hide');
                setTimeout(() => toast.remove(), 300);
            }
        }, duration);
    }
    
    // Check for success or error messages in URL
    function checkAndShowNotification() {
        const urlParams = new URLSearchParams(window.location.search);
        const successMessage = urlParams.get('success');
        const errorMessage = urlParams.get('error');
        
        if (successMessage) {
            showToast(decodeURIComponent(successMessage), 'success', 4000);
            // Clean up URL
            window.history.replaceState({}, document.title, window.location.pathname);
        }
        
        if (errorMessage) {
            showToast(decodeURIComponent(errorMessage), 'error', 4000);
            // Clean up URL
            window.history.replaceState({}, document.title, window.location.pathname);
        }
    }
    
    // Check immediately (in case DOM is already loaded)
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', checkAndShowNotification);
    } else {
        checkAndShowNotification();
    }
</script>

<script src="<?php echo BASE_URL; ?>assets/js/script.js"></script>
</body>
</html>
