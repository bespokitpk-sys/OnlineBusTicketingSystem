<?php
$host = 'localhost';
$user = 'root';
$password = '';
$file = __DIR__ . '/database.sql';

if (!file_exists($file)) {
    die('database.sql file not found. Please make sure it exists in the project root.');
}

$sql = file_get_contents($file);
if ($sql === false) {
    die('Unable to read database.sql.');
}

$mysqli = new mysqli($host, $user, $password);
if ($mysqli->connect_error) {
    die('MySQL connection failed: ' . $mysqli->connect_error);
}

$commands = array_filter(array_map('trim', preg_split('/;\s*\n/', $sql)));
foreach ($commands as $command) {
    if ($command === '') {
        continue;
    }
    if (!$mysqli->query($command)) {
        echo 'Error executing command: ' . $mysqli->error . "\n";
        echo 'Command: ' . $command . "\n";
        exit;
    }
}

echo "Database setup complete.\n";
