<?php
require_once APP_ROOT . '/config/db.php';

class Bus {
    public static function all() {
        global $conn;
        return $conn->query("SELECT * FROM buses ORDER BY bus_name ASC");
    }

    public static function findById(int $id) {
        global $conn;
        $stmt = $conn->prepare("SELECT * FROM buses WHERE id = ? LIMIT 1");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $bus = $result ? $result->fetch_assoc() : null;
        $stmt->close();
        return $bus;
    }
}
