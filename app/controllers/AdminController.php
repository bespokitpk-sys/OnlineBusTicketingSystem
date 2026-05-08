<?php
require_once APP_ROOT . '/config/db.php';
require_once APP_ROOT . '/app/core/Auth.php';
require_once APP_ROOT . '/app/models/User.php';

requireRole('admin');

class AdminController {
    
    // ==================== DASHBOARD STATISTICS ====================
    
    public static function getDashboardStats() {
        global $conn;
        
        $stats = [
            'total_buses' => 0,
            'total_operators' => 0,
            'total_passengers' => 0,
            'total_schedules' => 0,
            'total_tickets' => 0,
            'revenue_today' => 0,
            'active_bookings' => 0
        ];
        
        // Total buses
        $busResult = $conn->query("SELECT COUNT(*) as count FROM buses");
        if ($busResult) {
            $stats['total_buses'] = $busResult->fetch_assoc()['count'] ?? 0;
        }
        
        // Total operators
        $opResult = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'operator'");
        if ($opResult) {
            $stats['total_operators'] = $opResult->fetch_assoc()['count'] ?? 0;
        }
        
        // Total passengers
        $passResult = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'passenger'");
        if ($passResult) {
            $stats['total_passengers'] = $passResult->fetch_assoc()['count'] ?? 0;
        }
        
        // Total schedules
        $schedResult = $conn->query("SELECT COUNT(*) as count FROM schedules");
        if ($schedResult) {
            $stats['total_schedules'] = $schedResult->fetch_assoc()['count'] ?? 0;
        }
        
        // Total tickets
        $ticketResult = $conn->query("SELECT COUNT(*) as count FROM tickets");
        if ($ticketResult) {
            $stats['total_tickets'] = $ticketResult->fetch_assoc()['count'] ?? 0;
        }
        
        // Active bookings (not cancelled, not completed)
        $activeResult = $conn->query("SELECT COUNT(*) as count FROM tickets WHERE status IN ('booked', 'confirmed')");
        if ($activeResult) {
            $stats['active_bookings'] = $activeResult->fetch_assoc()['count'] ?? 0;
        }
        
        return $stats;
    }
    
    // ==================== BUS MANAGEMENT ====================
    
    public static function addBus(string $name, int $seats) {
        global $conn;
        
        // Validation
        if (empty($name) || $seats <= 0) {
            return ['success' => false, 'message' => 'Invalid input data.'];
        }
        
        $stmt = $conn->prepare("INSERT INTO buses (bus_name, total_seats, created_at) VALUES (?, ?, NOW())");
        $stmt->bind_param("si", $name, $seats);
        
        if ($stmt->execute()) {
            $busId = $conn->insert_id;
            $stmt->close();
            return ['success' => true, 'message' => 'Bus added successfully!', 'bus_id' => $busId];
        } else {
            $stmt->close();
            return ['success' => false, 'message' => 'Error adding bus.'];
        }
    }
    
    public static function getAllBuses() {
        global $conn;
        $result = $conn->query("
            SELECT * FROM buses ORDER BY bus_name ASC
        ");
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
    
    public static function getBusById(int $id) {
        global $conn;
        $stmt = $conn->prepare("SELECT * FROM buses WHERE id = ? LIMIT 1");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $bus = $result ? $result->fetch_assoc() : null;
        $stmt->close();
        return $bus;
    }
    
    public static function updateBus(int $id, string $name, int $seats) {
        global $conn;
        
        if (empty($name) || $seats <= 0) {
            return ['success' => false, 'message' => 'Invalid input data.'];
        }
        
        $stmt = $conn->prepare("UPDATE buses SET bus_name = ?, total_seats = ? WHERE id = ?");
        $stmt->bind_param("sii", $name, $seats, $id);
        
        if ($stmt->execute()) {
            $stmt->close();
            return ['success' => true, 'message' => 'Bus updated successfully!'];
        } else {
            $stmt->close();
            return ['success' => false, 'message' => 'Error updating bus.'];
        }
    }
    
    public static function deleteBus(int $id) {
        global $conn;
        $stmt = $conn->prepare("DELETE FROM buses WHERE id = ?");
        $stmt->bind_param("i", $id);
        $success = $stmt->execute();
        $stmt->close();
        return $success ? ['success' => true, 'message' => 'Bus deleted successfully!'] : ['success' => false, 'message' => 'Error deleting bus.'];
    }
    
    // ==================== SCHEDULE MANAGEMENT ====================
    
    public static function getAllSchedules() {
        global $conn;
        $result = $conn->query("
            SELECT s.*, b.bus_name, b.total_seats, u.name as operator_name, u.email as operator_email, u.phone as operator_phone
            FROM schedules s
            LEFT JOIN buses b ON s.bus_id = b.id
            LEFT JOIN users u ON s.operator_id = u.id
            ORDER BY s.departure_time DESC
        ");
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
    
    public static function getScheduleById(int $id) {
        global $conn;
        $stmt = $conn->prepare("
            SELECT s.*, b.bus_name, b.total_seats
            FROM schedules s
            LEFT JOIN buses b ON s.bus_id = b.id
            WHERE s.id = ? LIMIT 1
        ");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $schedule = $result ? $result->fetch_assoc() : null;
        $stmt->close();
        return $schedule;
    }
    
    public static function deleteSchedule(int $id) {
        global $conn;
        $stmt = $conn->prepare("DELETE FROM schedules WHERE id = ?");
        $stmt->bind_param("i", $id);
        $success = $stmt->execute();
        $stmt->close();
        return $success ? ['success' => true, 'message' => 'Schedule deleted successfully!'] : ['success' => false, 'message' => 'Error deleting schedule.'];
    }
    
    // ==================== TICKET MANAGEMENT ====================
    
    public static function getAllTickets() {
        global $conn;
        $result = $conn->query("
            SELECT 
                t.*, 
                u.name as passenger_name, 
                u.email as passenger_email,
                s.source,
                s.destination,
                s.departure_time,
                b.bus_name
            FROM tickets t
            LEFT JOIN users u ON t.user_id = u.id
            LEFT JOIN schedules s ON t.schedule_id = s.id
            LEFT JOIN buses b ON s.bus_id = b.id
            ORDER BY t.created_at DESC
        ");
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
    
    public static function getTicketById(int $id) {
        global $conn;
        $stmt = $conn->prepare("
            SELECT 
                t.*, 
                u.name as passenger_name,
                s.source,
                s.destination,
                s.departure_time,
                b.bus_name
            FROM tickets t
            LEFT JOIN users u ON t.user_id = u.id
            LEFT JOIN schedules s ON t.schedule_id = s.id
            LEFT JOIN buses b ON s.bus_id = b.id
            WHERE t.id = ? LIMIT 1
        ");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $ticket = $result ? $result->fetch_assoc() : null;
        $stmt->close();
        return $ticket;
    }
    
    public static function updateTicketStatus(int $id, string $status) {
        global $conn;
        
        $allowed_statuses = ['pending', 'approved', 'cancelled'];
        if (!in_array($status, $allowed_statuses)) {
            return ['success' => false, 'message' => 'Invalid status.'];
        }
        
        $stmt = $conn->prepare("UPDATE tickets SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $id);
        
        if ($stmt->execute()) {
            $stmt->close();
            return ['success' => true, 'message' => 'Ticket status updated successfully!'];
        } else {
            $stmt->close();
            return ['success' => false, 'message' => 'Error updating ticket.'];
        }
    }
    
    public static function cancelTicket(int $id) {
        global $conn;
        $stmt = $conn->prepare("UPDATE tickets SET status = 'cancelled' WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $stmt->close();
            return ['success' => true, 'message' => 'Ticket cancelled successfully!'];
        } else {
            $stmt->close();
            return ['success' => false, 'message' => 'Error cancelling ticket.'];
        }
    }
    
    // ==================== OPERATOR MANAGEMENT ====================
    
    public static function createOperator(string $name, string $email, string $phone, string $password) {
        // Validation
        if (empty($name) || empty($email) || empty($phone) || empty($password)) {
            return ['success' => false, 'message' => 'All fields are required.'];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Invalid email format.'];
        }

        if (strlen($password) < 6) {
            return ['success' => false, 'message' => 'Password must be at least 6 characters.'];
        }

        // Check if email already exists
        if (User::findByEmail($email)) {
            return ['success' => false, 'message' => 'Email already registered.'];
        }

        // Create operator (pre-verified by admin)
        $operatorId = User::create($name, $email, $phone, $password, 'operator', true);
        
        if ($operatorId) {
            return ['success' => true, 'message' => 'Operator created successfully!', 'operator_id' => $operatorId];
        } else {
            return ['success' => false, 'message' => 'Error creating operator. Please try again.'];
        }
    }

    public static function getOperators() {
        global $conn;
        $result = $conn->query("
            SELECT u.id, u.name, u.email, u.phone, u.created_at, u.is_verified
            FROM users u 
            WHERE u.role = 'operator' 
            ORDER BY u.name ASC
        ");
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public static function getOperatorById(int $id) {
        global $conn;
        $stmt = $conn->prepare("SELECT * FROM users WHERE id = ? AND role = 'operator' LIMIT 1");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $operator = $result ? $result->fetch_assoc() : null;
        $stmt->close();
        return $operator;
    }

    public static function updateOperator(int $id, string $name, string $phone) {
        global $conn;
        
        if (empty($name) || empty($phone)) {
            return ['success' => false, 'message' => 'Name and phone are required.'];
        }
        
        $stmt = $conn->prepare("UPDATE users SET name = ?, phone = ? WHERE id = ? AND role = 'operator'");
        $stmt->bind_param("ssi", $name, $phone, $id);
        
        if ($stmt->execute()) {
            $stmt->close();
            return ['success' => true, 'message' => 'Operator updated successfully!'];
        } else {
            $stmt->close();
            return ['success' => false, 'message' => 'Error updating operator.'];
        }
    }

    public static function deleteOperator(int $id) {
        global $conn;
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND role = 'operator'");
        $stmt->bind_param("i", $id);
        $success = $stmt->execute();
        $stmt->close();
        return $success ? ['success' => true, 'message' => 'Operator deleted successfully!'] : ['success' => false, 'message' => 'Error deleting operator.'];
    }
    
    // ==================== USER MANAGEMENT ====================
    
    public static function getAllUsers() {
        global $conn;
        $result = $conn->query("
            SELECT id, name, email, phone, role, is_verified, created_at 
            FROM users 
            ORDER BY created_at DESC
        ");
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
    
    public static function getUsersByRole(string $role) {
        global $conn;
        $stmt = $conn->prepare("
            SELECT id, name, email, phone, role, is_verified, created_at 
            FROM users 
            WHERE role = ? 
            ORDER BY created_at DESC
        ");
        $stmt->bind_param("s", $role);
        $stmt->execute();
        $result = $stmt->get_result();
        $users = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
        $stmt->close();
        return $users;
    }
    
    public static function verifyUser(int $id) {
        global $conn;
        $stmt = $conn->prepare("UPDATE users SET is_verified = 1 WHERE id = ?");
        $stmt->bind_param("i", $id);
        $success = $stmt->execute();
        $stmt->close();
        return $success ? ['success' => true, 'message' => 'User verified!'] : ['success' => false, 'message' => 'Error verifying user.'];
    }
    
    public static function deleteUser(int $id) {
        global $conn;
        // Prevent deleting admin accounts
        $user = User::findById($id);
        if ($user && $user['role'] === 'admin') {
            return ['success' => false, 'message' => 'Cannot delete admin accounts.'];
        }
        
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $success = $stmt->execute();
        $stmt->close();
        return $success ? ['success' => true, 'message' => 'User deleted successfully!'] : ['success' => false, 'message' => 'Error deleting user.'];
    }
    
    // ==================== REPORTS ====================
    
    public static function getRevenueReport() {
        global $conn;
        $result = $conn->query("
            SELECT 
                DATE(t.created_at) as date,
                COUNT(t.id) as total_tickets,
                COUNT(t.id) * 50 as total_revenue
            FROM tickets t
            WHERE t.status != 'cancelled'
            GROUP BY DATE(t.created_at)
            ORDER BY DATE(t.created_at) DESC
            LIMIT 30
        ");
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
    
    public static function getCancelledTickets() {
        global $conn;
        $result = $conn->query("
            SELECT id, user_id, schedule_id, created_at, status
            FROM tickets
            WHERE status = 'cancelled'
            ORDER BY created_at DESC
            LIMIT 50
        ");
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
}
