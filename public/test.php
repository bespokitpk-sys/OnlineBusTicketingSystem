<?php
// Test file - simple check
echo "Router is accessible!<br>";
echo "Current file: " . __FILE__ . "<br>";
echo "GET params: " . print_r($_GET, true) . "<br>";
echo "REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD'] . "<br>";
?>
