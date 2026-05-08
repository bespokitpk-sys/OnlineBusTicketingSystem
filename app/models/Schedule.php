<?php
require_once APP_ROOT . '/config/db.php';

class Schedule {
    public static function search(string $source, string $destination, string $date) {
        global $conn;
        
        $sql = "SELECT schedules.*, buses.bus_name, buses.total_seats FROM schedules JOIN buses ON schedules.bus_id = buses.id WHERE 1=1";
        $types = "";
        $params = [];
        
        if ($source !== '') {
            $sql .= " AND schedules.source LIKE ?";
            $types .= "s";
            $params[] = "%$source%";
        }
        if ($destination !== '') {
            $sql .= " AND schedules.destination LIKE ?";
            $types .= "s";
            $params[] = "%$destination%";
        }
        if ($date !== '') {
            $sql .= " AND DATE(schedules.departure_time) = ?";
            $types .= "s";
            $params[] = $date;
        }
        
        $sql .= " ORDER BY schedules.departure_time ASC";
        
        if (empty($params)) {
            return $conn->query($sql);
        }
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }

    public static function findById(int $id) {
        global $conn;
        $stmt = $conn->prepare("SELECT schedules.*, buses.bus_name, buses.total_seats FROM schedules JOIN buses ON schedules.bus_id = buses.id WHERE schedules.id = ? LIMIT 1");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $schedule = $result ? $result->fetch_assoc() : null;
        $stmt->close();
        return $schedule;
    }
}
