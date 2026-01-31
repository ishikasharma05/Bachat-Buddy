<?php
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../auth/session.php";

header("Content-Type: application/json");

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit;
}

$user_id = $_SESSION['user_id'];

// Validate inputs
$type = $_POST['type'] ?? '';
$amount = $_POST['amount'] ?? '';
$category = $_POST['category'] ?? '';
$date = $_POST['date'] ?? '';
$description = $_POST['description'] ?? '';
$tags = $_POST['tags'] ?? '';

if (!$type || !$amount || !$category || !$date) {
    echo json_encode(["success" => false, "message" => "Missing fields"]);
    exit;
}

// Insert transaction
$stmt = $conn->prepare("
    INSERT INTO transactions
    (user_id, type, amount, category, description, tags, transaction_date)
    VALUES (?, ?, ?, ?, ?, ?, ?)
");

$stmt->bind_param(
    "isdssss",
    $user_id,
    $type,
    $amount,
    $category,
    $description,
    $tags,
    $date
);

$stmt->execute();

/* ===============================
   OPTIONAL BUSINESS LOGIC
================================ */

// Savings logic
if ($type === "savings") {
    $conn->query("
        UPDATE users
        SET savings_balance = savings_balance + $amount
        WHERE id = $user_id
    ");
}

if ($type === "withdraw-savings") {
    $conn->query("
        UPDATE users
        SET savings_balance = savings_balance - $amount
        WHERE id = $user_id
    ");
}

echo json_encode(["success" => true]);

$stmt->close();
$conn->close();     
?>