<?php
// Direct test of auth_router
echo "1. Checking includes...<br>";

echo "2. DB config: ";
if (file_exists(__DIR__ . '/../config/db.php')) {
    echo "✓ EXISTS<br>";
    require_once __DIR__ . '/../config/db.php';
    echo "BASE_URL = " . BASE_URL . "<br>";
} else {
    echo "✗ NOT FOUND<br>";
}

echo "3. AuthController: ";
if (file_exists(__DIR__ . '/../controllers/AuthController.php')) {
    echo "✓ EXISTS<br>";
    include_once __DIR__ . '/../controllers/AuthController.php';
} else {
    echo "✗ NOT FOUND<br>";
}

echo "4. Views folder: ";
if (is_dir(__DIR__ . '/../views')) {
    echo "✓ EXISTS<br>";
    $files = scandir(__DIR__ . '/../views');
    echo "Files: " . implode(', ', array_diff($files, ['.', '..'])) . "<br>";
} else {
    echo "✗ NOT FOUND<br>";
}

echo "5. Testing action=register...<br>";
$_GET['action'] = 'register';
$_SERVER['REQUEST_METHOD'] = 'GET';
echo "View file check:<br>";
$viewFile = __DIR__ . '/../views/register_form.php';
echo "Path: $viewFile<br>";
echo "Exists: " . (file_exists($viewFile) ? "✓ YES" : "✗ NO") . "<br>";

if (file_exists($viewFile)) {
    echo "<strong>✓ EVERYTHING LOOKS GOOD - Router should work!</strong>";
} else {
    echo "<strong>✗ View file missing!</strong>";
}
?>
