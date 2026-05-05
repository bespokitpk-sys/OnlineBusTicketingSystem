<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (!defined('BASE_URL')) {
    $projectRoot = realpath(__DIR__ . '/..');
    $documentRoot = isset($_SERVER['DOCUMENT_ROOT']) ? realpath($_SERVER['DOCUMENT_ROOT']) : false;

    $normalizedProjectRoot = $projectRoot ? str_replace('\\', '/', $projectRoot) : '';
    $normalizedDocumentRoot = $documentRoot ? str_replace('\\', '/', $documentRoot) : '';

    $basePath = '/';

    if ($normalizedProjectRoot !== '' && $normalizedDocumentRoot !== '') {
        $projectForCompare = PHP_OS_FAMILY === 'Windows' ? strtolower($normalizedProjectRoot) : $normalizedProjectRoot;
        $documentForCompare = PHP_OS_FAMILY === 'Windows' ? strtolower($normalizedDocumentRoot) : $normalizedDocumentRoot;

        if (strpos($projectForCompare, $documentForCompare) === 0) {
            $relativePath = trim(substr($normalizedProjectRoot, strlen($normalizedDocumentRoot)), '/');
            $basePath = $relativePath === '' ? '/' : '/' . $relativePath . '/';
        }
    }

    define('BASE_URL', $basePath);
}

$host = "localhost";
$user = "root";
$password = "";
$database = "bus_db";

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>
