<?php
session_start();
require_once '../../config/db.php';

header('Content-Type: application/json');

echo json_encode([
    'db_connection' => $conn->connect_error ? 'FAILED: ' . $conn->connect_error : 'SUCCESS',
    'session_user_id' => $_SESSION['user_id'] ?? 'NOT SET',
    'php_version' => phpversion(),
    'post_method' => $_SERVER['REQUEST_METHOD']
]);
?>