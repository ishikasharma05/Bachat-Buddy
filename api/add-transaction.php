<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
header("Content-Type: application/json");

require_once __DIR__ . "/../config/db.php";

$type        = $_POST['type'] ?? '';
$amount      = $_POST['amount'] ?? '';
$category    = $_POST['category'] ?? '';
$date        = $_POST['date'] ?? '';
$description = $_POST['description'] ?? '';
$tags        = $_POST['tags'] ?? '';

if (!$type || !$amount || !$category || !$date) {
    echo json_encode(["success" => false, "message" => "Missing fields"]);
    exit;
}

$sql = "INSERT INTO transactions 
        (type, amount, category, description, tags, transaction_date)
        VALUES (?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode([
        "success" => false,
        "message" => $conn->error   // 🔥 SHOWS REAL SQL ERROR
    ]);
    exit;
}

$stmt->bind_param("sdssss", $type, $amount, $category, $description, $tags, $date);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => $stmt->error]);
}

$stmt->close();
$conn->close();
?>