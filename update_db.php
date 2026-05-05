<?php
include 'config/db.php';

function columnExists($conn, $table, $column) {
    $table = $conn->real_escape_string($table);
    $column = $conn->real_escape_string($column);
    $result = $conn->query("SHOW COLUMNS FROM `$table` LIKE '$column'");
    return $result && $result->num_rows > 0;
}

$columns = [
    'phone' => 'VARCHAR(15) NOT NULL AFTER email',
    'otp_code' => 'VARCHAR(6) NULL AFTER phone',
    'otp_expiry' => 'DATETIME NULL AFTER otp_code',
    'is_verified' => 'TINYINT(1) DEFAULT 0 AFTER otp_expiry',
    'reset_token' => 'VARCHAR(64) NULL AFTER is_verified',
    'reset_expiry' => 'DATETIME NULL AFTER reset_token'
];

foreach ($columns as $column => $definition) {
    if (columnExists($conn, 'users', $column)) {
        echo "Skipped existing column: $column\n";
        continue;
    }

    $alter = "ALTER TABLE users ADD COLUMN `$column` $definition";
    if ($conn->query($alter) === TRUE) {
        echo "Added column: $column\n";
    } else {
        echo "Error adding $column: " . $conn->error . "\n";
    }
}

echo "Database schema update complete.\n";
?>