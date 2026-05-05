<?php
require_once __DIR__ . '/config/db.php';

// Read the SQL file
$sql = file_get_contents(__DIR__ . '/add_schedules.sql');

// Split by semicolon and execute each statement
$statements = array_filter(array_map('trim', explode(';', $sql)), function($stmt) {
    return !empty($stmt);
});

$success_count = 0;
$error_count = 0;

foreach ($statements as $statement) {
    if (!empty(trim($statement))) {
        if ($conn->query($statement) === TRUE) {
            $success_count++;
        } else {
            echo "Error: " . $conn->error . "<br>";
            $error_count++;
        }
    }
}

echo "<h2>✅ Schedules Added Successfully!</h2>";
echo "<p><strong>Total Schedules Added:</strong> " . ($success_count) . "</p>";
if ($error_count > 0) {
    echo "<p style='color: red;'><strong>Errors:</strong> " . $error_count . "</p>";
}
echo "<p><a href='/' style='color: blue; text-decoration: underline;'>Go Back to Home</a></p>";
?>
