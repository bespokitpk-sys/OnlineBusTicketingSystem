<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';

requireRole('operator');

header('Content-Type: application/json');

$ticket_id = intval($_POST['ticket_id'] ?? 0);
$schedule_id = intval($_POST['schedule_id'] ?? 0);

if ($ticket_id <= 0 || $schedule_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid ticket or schedule ID']);
    exit;
}

// Fetch passenger and ticket details
$result = $conn->query("
    SELECT 
        t.id as ticket_id, t.status, t.seats, t.created_at, t.boarded_at,
        u.id as user_id, u.name, u.email, u.phone,
        s.source, s.destination, s.departure_time,
        b.bus_name
    FROM tickets t
    LEFT JOIN users u ON t.user_id = u.id
    LEFT JOIN schedules s ON t.schedule_id = s.id
    LEFT JOIN buses b ON s.bus_id = b.id
    WHERE t.id = $ticket_id AND t.schedule_id = $schedule_id
    LIMIT 1
");

if ($result && $result->num_rows > 0) {
    $passenger = $result->fetch_assoc();
    $status = strtolower((string) ($passenger['status'] ?? 'pending'));

    echo json_encode([
        'success' => true,
        'passenger' => [
            'ticket_id' => (int) $passenger['ticket_id'],
            'ticket_code' => 'TICKET-' . (int) $passenger['ticket_id'],
            'name' => $passenger['name'] ?? 'N/A',
            'email' => $passenger['email'] ?? 'N/A',
            'phone' => $passenger['phone'] ?? 'N/A',
            'seats' => (int) ($passenger['seats'] ?? 0),
            'status' => $status,
            'source' => $passenger['source'] ?? 'N/A',
            'destination' => $passenger['destination'] ?? 'N/A',
            'departure_time' => $passenger['departure_time'] ?? null,
            'bus_name' => $passenger['bus_name'] ?? 'N/A',
            'booked_at' => $passenger['created_at'] ?? null,
            'boarded_at' => $passenger['boarded_at'] ?? null,
            'actions' => [
                'can_approve_payment' => $status === 'pending',
                'can_board' => $status === 'approved'
            ]
        ],
        'message' => 'Passenger details loaded successfully.'
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Passenger not found for this schedule']);
}

$conn->close();
?>
