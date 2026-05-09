<?php
require_once APP_ROOT . '/config/db.php';
require_once APP_ROOT . '/app/core/Auth.php';

class PassengerController {
    public static function getMyTickets(int $userId) {
        global $conn;
        $stmt = $conn->prepare("SELECT tickets.*, schedules.source, schedules.destination, schedules.departure_time, buses.bus_name FROM tickets JOIN schedules ON tickets.schedule_id = schedules.id JOIN buses ON schedules.bus_id = buses.id WHERE tickets.user_id = ? ORDER BY tickets.created_at DESC");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }
}
