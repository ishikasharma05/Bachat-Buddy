<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include '../../config/db.php';
include '../../components/auth_check.php';

header('Content-Type: application/json');

$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit;
}
file_put_contents('log.txt', print_r($data, true), FILE_APPEND);

$data = json_decode(file_get_contents('php://input'), true);

$type = $data['type'] ?? '';
$amount = $data['amount'] ?? 0;
$date = $data['date'] ?? '';
$category = $data['category'] ?? '';
$description = $data['description'] ?? '';
$tags = $data['tags'] ?? '';

if (!$type || !$amount || !$date || !$category) {
    echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
    exit;
}

$stmt = $conn->prepare("INSERT INTO transactions (user_id, type, amount, date, category, description, tags) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("isdssss", $user_id, $type, $amount, $date, $category, $description, $tags);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Transaction added']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Database error']);
}

$stmt->close();
$conn->close();
?>