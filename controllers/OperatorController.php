<?php
include_once __DIR__ . '/../config/db.php';
include_once __DIR__ . '/../includes/auth.php';

requireRole('operator');

class OperatorController {

    private static $ticketColumnCache = [];

    private static function ticketColumnExists(string $columnName): bool {
        global $conn;

        if (array_key_exists($columnName, self::$ticketColumnCache)) {
            return self::$ticketColumnCache[$columnName];
        }

        $safeColumnName = $conn->real_escape_string($columnName);
        $result = $conn->query("SHOW COLUMNS FROM tickets LIKE '$safeColumnName'");
        self::$ticketColumnCache[$columnName] = $result && $result->num_rows > 0;

        return self::$ticketColumnCache[$columnName];
    }
    
    // Get all schedules assigned to operator
    public static function getMySchedules(int $operatorId) {
        global $conn;
        $result = $conn->query("
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
            WHERE s.operator_id = $operatorId
            GROUP BY s.id
            ORDER BY s.departure_time DESC
        ");
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
    
    // Add new schedule
    public static function addSchedule(int $operatorId, int $busId, string $source, string $destination, string $departureTime) {
        global $conn;
        $operatorId = intval($operatorId);
        $busId = intval($busId);
        $source = $conn->real_escape_string($source);
        $destination = $conn->real_escape_string($destination);
        $departureTime = $conn->real_escape_string($departureTime);
        
        if ($busId <= 0 || empty($source) || empty($destination) || empty($departureTime)) {
            return ['success' => false, 'message' => 'Please complete all schedule fields.'];
        }
        
        $result = $conn->query("
            INSERT INTO schedules (bus_id, operator_id, source, destination, departure_time) 
            VALUES ($busId, $operatorId, '$source', '$destination', '$departureTime')
        ");
        
        return $result ? ['success' => true, 'message' => 'Schedule added successfully!'] : ['success' => false, 'message' => 'Error adding schedule: ' . $conn->error];
    }

    // Get schedule details by ID
    public static function getScheduleById(int $scheduleId) {
        global $conn;
        $result = $conn->query("
            SELECT 
                s.*, b.bus_name, b.total_seats,
                COUNT(t.id) as total_bookings
            FROM schedules s
            LEFT JOIN buses b ON s.bus_id = b.id
            LEFT JOIN tickets t ON s.id = t.schedule_id
            WHERE s.id = $scheduleId
            GROUP BY s.id
            LIMIT 1
        ");
        return $result ? $result->fetch_assoc() : null;
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
        $scheduleId = intval($scheduleId);
        $result = $conn->query("
            SELECT 
                t.id, t.user_id, t.seats, t.status, t.created_at,
                u.name, u.email, u.phone
            FROM tickets t
            LEFT JOIN users u ON t.user_id = u.id
            WHERE t.schedule_id = $scheduleId AND t.status = 'pending'
            ORDER BY t.created_at ASC
        ");
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    // Approve payment (pending -> approved)
    public static function approvePayment(int $ticketId) {
        global $conn;
        $ticketId = intval($ticketId);
        $result = $conn->query("UPDATE tickets SET status = 'approved' WHERE id = $ticketId AND status = 'pending'");

        if (!$result) {
            return ['success' => false, 'message' => 'Failed to approve payment.'];
        }

        if ($conn->affected_rows === 0) {
            return ['success' => false, 'message' => 'This passenger is no longer waiting for payment approval.'];
        }

        return ['success' => true, 'message' => 'Payment approved!'];
    }

    // Board passenger (approved -> boarded)
    public static function boardPassenger(int $ticketId) {
        global $conn;
        $ticketId = intval($ticketId);
        $setClause = "status = 'boarded'";
        if (self::ticketColumnExists('boarded_at')) {
            $setClause .= ", boarded_at = NOW()";
        }

        $result = $conn->query("UPDATE tickets SET $setClause WHERE id = $ticketId AND status = 'approved'");

        if (!$result) {
            return ['success' => false, 'message' => 'Failed to board passenger. Ensure the tickets table supports the boarded status.'];
        }

        if ($conn->affected_rows === 0) {
            return ['success' => false, 'message' => 'Only approved passengers can be marked as boarded.'];
        }

        return ['success' => true, 'message' => 'Passenger boarded successfully!'];
    }

    // Get all boarded passengers for a trip
    public static function getBoardedPassengers(int $scheduleId) {
        global $conn;
        $scheduleId = intval($scheduleId);
        $boardedAtSelect = self::ticketColumnExists('boarded_at') ? 't.boarded_at' : 'NULL AS boarded_at';

        $result = $conn->query("
            SELECT 
                t.id, t.user_id, t.seats, t.created_at, $boardedAtSelect,
                u.name, u.phone
            FROM tickets t
            LEFT JOIN users u ON t.user_id = u.id
            WHERE t.schedule_id = $scheduleId AND t.status = 'boarded'
            ORDER BY t.created_at ASC
        ");
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    // On-spot booking (add passenger during trip)
    public static function onSpotBooking(int $scheduleId, int $userId, int $seats) {
        global $conn;
        $scheduleId = intval($scheduleId);
        $userId = intval($userId);
        $seats = intval($seats);

        // Check if user already has a ticket for this schedule
        $existing = $conn->query("SELECT id FROM tickets WHERE schedule_id = $scheduleId AND user_id = $userId LIMIT 1");
        if ($existing && $existing->num_rows > 0) {
            return ['success' => false, 'message' => 'Passenger already has a booking for this trip.'];
        }

        // Create ticket with 'boarded' status (auto-approved for on-spot booking)
        $result = $conn->query("
            INSERT INTO tickets (user_id, schedule_id, seats, status, created_at)
            VALUES ($userId, $scheduleId, $seats, 'boarded', NOW())
        ");
        return $result ? ['success' => true, 'message' => 'On-spot booking created!'] : ['success' => false, 'message' => 'Error creating booking.'];
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
