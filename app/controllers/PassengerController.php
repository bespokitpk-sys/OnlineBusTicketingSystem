<?php
require_once APP_ROOT . '/config/db.php';
require_once APP_ROOT . '/app/core/Auth.php';

requireRole('passenger');

class PassengerController {
    public static function getMyTickets(int $userId) {
        global $conn;
        return $conn->query("SELECT tickets.*, schedules.source, schedules.destination, schedules.departure_time, buses.bus_name FROM tickets JOIN schedules ON tickets.schedule_id = schedules.id JOIN buses ON schedules.bus_id = buses.id WHERE tickets.user_id = $userId ORDER BY tickets.created_at DESC");
    }
}
