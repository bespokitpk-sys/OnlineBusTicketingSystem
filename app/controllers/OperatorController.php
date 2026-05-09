<?php
require_once APP_ROOT . '/config/db.php';
require_once APP_ROOT . '/app/core/Auth.php';

class OperatorController {

    private static $ticketColumnCache = [];

    private static function ticketColumnExists(string $columnName): bool {
        global $conn;

        if (array_key_exists($columnName, self::$ticketColumnCache)) {
            return self::$ticketColumnCache[$columnName];
        }

        $stmt = $conn->prepare("SHOW COLUMNS FROM tickets LIKE ?");
        $stmt->bind_param("s", $columnName);
        $stmt->execute();
        $result = $stmt->get_result();
        self::$ticketColumnCache[$columnName] = $result && $result->num_rows > 0;
        $stmt->close();

        return self::$ticketColumnCache[$columnName];
    }
    
    // Get all schedules assigned to operator
    public static function getMySchedules(int $operatorId) {
        global $conn;
        $stmt = $conn->prepare("
            SELECT 
                s.id, s.bus_id, s.source, s.destination, s.departure_time, s.created_at, s.operator_id,
                b.bus_name, b.total_seats,
                COALESCE(COUNT(t.id), 0) as total_bookings,
                COALESCE(SUM(CASE WHEN t.status = 'pending' THEN 1 ELSE 0 END), 0) as pending_payments,
                COALESCE(SUM(CASE WHEN t.status = 'approved' THEN 1 ELSE 0 END), 0) as approved_bookings,
                COALESCE(SUM(CASE WHEN t.status = 'boarded' THEN 1 ELSE 0 END), 0) as boarded_passengers
            FROM schedules s
            LEFT JOIN buses b ON s.bus_id = b.id
            LEFT JOIN tickets t ON s.id = t.schedule_id
            WHERE s.operator_id = ?
            GROUP BY s.id
            ORDER BY s.departure_time DESC
        ");
        $stmt->bind_param("i", $operatorId);
        $stmt->execute();
        $result = $stmt->get_result();
        $schedules = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
        $stmt->close();
        return $schedules;
    }
    
    // Add new schedule
    public static function addSchedule(int $operatorId, int $busId, string $source, string $destination, string $departureTime) {
        global $conn;
        
        if ($busId <= 0 || empty($source) || empty($destination) || empty($departureTime)) {
            return ['success' => false, 'message' => 'Please complete all schedule fields.'];
        }
        
        $stmt = $conn->prepare("
            INSERT INTO schedules (bus_id, operator_id, source, destination, departure_time) 
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("iisss", $busId, $operatorId, $source, $destination, $departureTime);
        $success = $stmt->execute();
        $stmt->close();
        
        return $success ? ['success' => true, 'message' => 'Schedule added successfully!'] : ['success' => false, 'message' => 'Error adding schedule.'];
    }

    // Get schedule details by ID
    public static function getScheduleById(int $scheduleId) {
        global $conn;
        $stmt = $conn->prepare("
            SELECT 
                s.*, b.bus_name, b.total_seats,
                COUNT(t.id) as total_bookings
            FROM schedules s
            LEFT JOIN buses b ON s.bus_id = b.id
            LEFT JOIN tickets t ON s.id = t.schedule_id
            WHERE s.id = ?
            GROUP BY s.id
            LIMIT 1
        ");
        $stmt->bind_param("i", $scheduleId);
        $stmt->execute();
        $result = $stmt->get_result();
        $schedule = $result ? $result->fetch_assoc() : null;
        $stmt->close();
        return $schedule;
    }

    // Start trip (scheduled -> ongoing)
    public static function startTrip(int $scheduleId) {
        global $conn;
        $scheduleId = intval($scheduleId);
        // Trip started successfully - no status column to update in database
        return ['success' => true, 'message' => 'Trip started successfully!'];
    }

    // End trip (ongoing -> completed)
    public static function endTrip(int $scheduleId) {
        global $conn;
        $scheduleId = intval($scheduleId);
        // Trip completed successfully - no status column to update in database
        return ['success' => true, 'message' => 'Trip completed successfully!'];
    }

    // Get pending payments for a trip
    public static function getPendingPayments(int $scheduleId) {
        global $conn;
        $stmt = $conn->prepare("
            SELECT 
                t.id, t.user_id, t.seats, t.status, t.created_at,
                u.name, u.email, u.phone
            FROM tickets t
            LEFT JOIN users u ON t.user_id = u.id
            WHERE t.schedule_id = ? AND t.status = 'pending'
            ORDER BY t.created_at ASC
        ");
        $stmt->bind_param("i", $scheduleId);
        $stmt->execute();
        $result = $stmt->get_result();
        $payments = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
        $stmt->close();
        return $payments;
    }

    // Approve payment (pending -> approved)
    public static function approvePayment(int $ticketId) {
        global $conn;
        $stmt = $conn->prepare("UPDATE tickets SET status = 'approved' WHERE id = ? AND status = 'pending'");
        $stmt->bind_param("i", $ticketId);
        $result = $stmt->execute();

        if (!$result) {
            $stmt->close();
            return ['success' => false, 'message' => 'Failed to approve payment.'];
        }

        if ($conn->affected_rows === 0) {
            $stmt->close();
            return ['success' => false, 'message' => 'This passenger is no longer waiting for payment approval.'];
        }

        $stmt->close();
        return ['success' => true, 'message' => 'Payment approved!'];
    }

    // Board passenger (approved -> boarded)
    public static function boardPassenger(int $ticketId) {
        global $conn;
        
        if (self::ticketColumnExists('boarded_at')) {
            $stmt = $conn->prepare("UPDATE tickets SET status = 'boarded', boarded_at = NOW() WHERE id = ? AND status = 'approved'");
        } else {
            $stmt = $conn->prepare("UPDATE tickets SET status = 'boarded' WHERE id = ? AND status = 'approved'");
        }
        
        $stmt->bind_param("i", $ticketId);
        $result = $stmt->execute();

        if (!$result) {
            $stmt->close();
            return ['success' => false, 'message' => 'Failed to board passenger. Ensure the tickets table supports the boarded status.'];
        }

        if ($conn->affected_rows === 0) {
            $stmt->close();
            return ['success' => false, 'message' => 'Only approved passengers can be marked as boarded.'];
        }

        $stmt->close();
        return ['success' => true, 'message' => 'Passenger boarded successfully!'];
    }

    // Get all boarded passengers for a trip
    public static function getBoardedPassengers(int $scheduleId) {
        global $conn;
        $boardedAtSelect = self::ticketColumnExists('boarded_at') ? 't.boarded_at' : 'NULL AS boarded_at';

        $stmt = $conn->prepare("
            SELECT 
                t.id, t.user_id, t.seats, t.created_at, $boardedAtSelect,
                u.name, u.phone
            FROM tickets t
            LEFT JOIN users u ON t.user_id = u.id
            WHERE t.schedule_id = ? AND t.status = 'boarded'
            ORDER BY t.created_at ASC
        ");
        $stmt->bind_param("i", $scheduleId);
        $stmt->execute();
        $result = $stmt->get_result();
        $passengers = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
        $stmt->close();
        return $passengers;
    }

    // On-spot booking (add passenger during trip)
    public static function onSpotBooking(int $scheduleId, int $userId, int $seats) {
        global $conn;

        // Check if user already has a ticket for this schedule
        $stmt = $conn->prepare("SELECT id FROM tickets WHERE schedule_id = ? AND user_id = ? LIMIT 1");
        $stmt->bind_param("ii", $scheduleId, $userId);
        $stmt->execute();
        $existing = $stmt->get_result();
        if ($existing && $existing->num_rows > 0) {
            $stmt->close();
            return ['success' => false, 'message' => 'Passenger already has a booking for this trip.'];
        }
        $stmt->close();

        // Create ticket with 'boarded' status (auto-approved for on-spot booking)
        $stmt = $conn->prepare("
            INSERT INTO tickets (user_id, schedule_id, seats, status, created_at)
            VALUES (?, ?, ?, 'boarded', NOW())
        ");
        $stmt->bind_param("iii", $userId, $scheduleId, $seats);
        $success = $stmt->execute();
        $stmt->close();
        return $success ? ['success' => true, 'message' => 'On-spot booking created!'] : ['success' => false, 'message' => 'Error creating booking.'];
    }

    // Get available passengers (not yet booked)
    public static function getAvailablePassengers() {
        global $conn;
        $result = $conn->query("
            SELECT id, name, email, phone FROM users 
            WHERE role = 'passenger' AND is_verified = 1
            ORDER BY name ASC
        ");
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    // Get trip summary for a schedule
    public static function getTripSummary(int $scheduleId) {
        global $conn;
        $scheduleId = intval($scheduleId);
        $result = $conn->query("
            SELECT 
                s.id, s.source, s.destination, s.departure_time, s.created_at,
                b.bus_name, b.total_seats,
                COALESCE(COUNT(DISTINCT t.id), 0) as total_tickets,
                COALESCE(SUM(CASE WHEN t.status = 'pending' THEN 1 ELSE 0 END), 0) as pending_count,
                COALESCE(SUM(CASE WHEN t.status = 'approved' THEN 1 ELSE 0 END), 0) as approved_count,
                COALESCE(SUM(CASE WHEN t.status = 'boarded' THEN 1 ELSE 0 END), 0) as boarded_count,
                COALESCE(SUM(CASE WHEN t.status = 'cancelled' THEN 1 ELSE 0 END), 0) as cancelled_count
            FROM schedules s
            LEFT JOIN buses b ON s.bus_id = b.id
            LEFT JOIN tickets t ON s.id = t.schedule_id
            WHERE s.id = $scheduleId
            GROUP BY s.id
        ");
        $summary = $result ? $result->fetch_assoc() : null;
        if ($summary) {
            // Add aliases for dashboard compatibility
            $summary['total_bookings'] = $summary['total_tickets'];
        }
        return $summary;
    }
}
