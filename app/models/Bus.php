<?php
require_once APP_ROOT . '/config/db.php';

class Bus {
    public static function all() {
        global $conn;
        return $conn->query("SELECT * FROM buses ORDER BY bus_name ASC");
    }

    public static function findById(int $id) {
        global $conn;
        $result = $conn->query("SELECT * FROM buses WHERE id = $id LIMIT 1");
        return $result ? $result->fetch_assoc() : null;
    }
}
