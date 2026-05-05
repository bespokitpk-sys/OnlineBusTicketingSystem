<?php
require_once 'config/db.php';

// Read the SQL file
$sql_content = file_get_contents('add_schedules.sql');

// Remove comments
$lines = explode("\n", $sql_content);
$clean_sql = "";
foreach ($lines as $line) {
    $line = trim($line);
    if (empty($line) || strpos($line, '--') === 0) {
        continue;
    }
    $clean_sql .= $line . " ";
}

// Split by semicolon
$statements = array_filter(array_map('trim', explode(";", $clean_sql)));

$count = 0;
$errors = [];

foreach ($statements as $statement) {
    if (empty($statement)) {
        continue;
    }
    
    if ($conn->query($statement)) {
        // Count rows affected
        $count += $conn->affected_rows;
    } else {
        $errors[] = "Error: " . $conn->error . " (Statement: " . substr($statement, 0, 100) . "...)";
    }
}

echo "<!DOCTYPE html>";
echo "<html>";
echo "<head><title>Schedule Population</title>";
echo "<style>body { font-family: Arial; text-align: center; padding: 40px; background: #f5f5f5; } ";
echo ".container { background: white; max-width: 600px; margin: 0 auto; padding: 30px; border-radius: 10px; }</style>";
echo "</head>";
echo "<body>";
echo "<div class='container'>";

if (empty($errors)) {
    echo "<h2 style='color: green; font-size: 2rem;'>✅ Success!</h2>";
    echo "<p style='font-size: 1.2rem; color: #333;'>Added <strong>" . $count . " schedules</strong> to database.</p>";
    echo "<p style='margin-top: 20px;'>";
    echo "<a href='" . BASE_URL . "public/search.php' style='color: white; background: #0072ff; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: bold; display: inline-block;'>→ View Schedules on Search Page</a>";
    echo "</p>";
} else {
    echo "<h2 style='color: red; font-size: 2rem;'>⚠️ Issues Occurred</h2>";
    echo "<p>Added " . $count . " schedules.</p>";
    echo "<details style='text-align: left; margin-top: 20px;'>";
    echo "<summary style='cursor: pointer; font-weight: bold;'>Show Errors (" . count($errors) . ")</summary>";
    foreach ($errors as $error) {
        echo "<p style='color: #c00; margin: 10px 0;'>" . htmlspecialchars($error) . "</p>";
    }
    echo "</details>";
}

echo "</div>";
echo "</body></html>";
?>
