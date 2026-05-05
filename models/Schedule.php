<?php
include_once __DIR__ . '/../config/db.php';

class Schedule {
    public static function search(string $source, string $destination, string $date) {
        global $conn;
        $filters = [];
        if ($source !== '') {
            $filters[] = "schedules.source LIKE '%" . $conn->real_escape_string($source) . "%'";
        }
        if ($destination !== '') {
            $filters[] = "schedules.destination LIKE '%" . $conn->real_escape_string($destination) . "%'";
        }
        if ($date !== '') {
            $filters[] = "DATE(schedules.departure_time) = '" . $conn->real_escape_string($date) . "'";
        }

        $sql = "SELECT schedules.*, buses.bus_name, buses.total_seats FROM schedules JOIN buses ON schedules.bus_id = buses.id";
        if (!empty($filters)) {
            $sql .= ' WHERE ' . implode(' AND ', $filters);
        }
        $sql .= " ORDER BY schedules.departure_time ASC";
        return $conn->query($sql);
    }

    public static function findById(int $id) {
        global $conn;
        $result = $conn->query("SELECT schedules.*, buses.bus_name, buses.total_seats FROM schedules JOIN buses ON schedules.bus_id = buses.id WHERE schedules.id = $id LIMIT 1");
        return $result ? $result->fetch_assoc() : null;
    }
}
