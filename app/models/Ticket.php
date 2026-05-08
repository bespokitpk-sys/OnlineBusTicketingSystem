<?php
require_once APP_ROOT . '/config/db.php';

class Ticket {
    public static function create(int $user_id, int $schedule_id, int $seats, string $status = 'pending') {
        global $conn;
        $stmt = $conn->prepare("INSERT INTO tickets (user_id, schedule_id, seats, status, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param("iiis", $user_id, $schedule_id, $seats, $status);
        $stmt->execute();
        $insertId = $conn->insert_id;
        $stmt->close();
        return $insertId;
    }

    public static function findById(int $id) {
        global $conn;
        $stmt = $conn->prepare("SELECT tickets.*, schedules.source, schedules.destination, schedules.departure_time, buses.bus_name FROM tickets JOIN schedules ON tickets.schedule_id = schedules.id JOIN buses ON schedules.bus_id = buses.id WHERE tickets.id = ? LIMIT 1");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $ticket = $result ? $result->fetch_assoc() : null;
        $stmt->close();
        return $ticket;
    }

    public static function findByUser(int $user_id) {
        global $conn;
        $stmt = $conn->prepare("SELECT tickets.*, schedules.source, schedules.destination, schedules.departure_time, buses.bus_name FROM tickets JOIN schedules ON tickets.schedule_id = schedules.id JOIN buses ON schedules.bus_id = buses.id WHERE tickets.user_id = ? ORDER BY tickets.created_at DESC");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }

    public static function approve(int $id) {
        global $conn;
        $stmt = $conn->prepare("UPDATE tickets SET status = 'approved' WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }
}