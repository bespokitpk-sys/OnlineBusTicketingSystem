<?php
include_once __DIR__ . '/../config/db.php';
include_once __DIR__ . '/../includes/auth.php';

requireRole('passenger');

class PassengerController {
    public static function getMyTickets(int $userId) {
        global $conn;
        return $conn->query("SELECT tickets.*, schedules.source, schedules.destination, schedules.departure_time, buses.bus_name FROM tickets JOIN schedules ON tickets.schedule_id = schedules.id JOIN buses ON schedules.bus_id = buses.id WHERE tickets.user_id = $userId ORDER BY tickets.created_at DESC");
    }
}
