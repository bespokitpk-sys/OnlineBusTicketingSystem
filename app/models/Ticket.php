<?php
require_once APP_ROOT . '/config/db.php';

class Ticket {
    public static function create(int $user_id, int $schedule_id, int $seats, string $status = 'pending') {
        global $conn;
        $user_id = intval($user_id);
        $schedule_id = intval($schedule_id);
        $seats = intval($seats);
        $status = $conn->real_escape_string($status);
        $conn->query("INSERT INTO tickets (user_id, schedule_id, seats, status, created_at) VALUES ($user_id, $schedule_id, $seats, '$status', NOW())");
        return $conn->insert_id;
    }

    public static function findById(int $id) {
        global $conn;
        $result = $conn->query("SELECT tickets.*, schedules.source, schedules.destination, schedules.departure_time, buses.bus_name FROM tickets JOIN schedules ON tickets.schedule_id = schedules.id JOIN buses ON schedules.bus_id = buses.id WHERE tickets.id = $id LIMIT 1");
        return $result ? $result->fetch_assoc() : null;
    }

    public static function findByUser(int $user_id) {
        global $conn;
        return $conn->query("SELECT tickets.*, schedules.source, schedules.destination, schedules.departure_time, buses.bus_name FROM tickets JOIN schedules ON tickets.schedule_id = schedules.id JOIN buses ON schedules.bus_id = buses.id WHERE tickets.user_id = $user_id ORDER BY tickets.created_at DESC");
    }

    public static function approve(int $id) {
        global $conn;
        $conn->query("UPDATE tickets SET status = 'approved' WHERE id = $id");
    }
}