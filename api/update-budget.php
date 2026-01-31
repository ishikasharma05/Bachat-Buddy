<?php
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../auth/session.php";

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false]);
    exit;
}

$user_id = $_SESSION['user_id'];
$budget = intval($_POST['monthly_budget']);

if ($budget <= 0) {
    echo json_encode(["success" => false]);
    exit;
}

$stmt = $conn->prepare(
    "UPDATE users SET monthly_budget = ? WHERE id = ?"
);
$stmt->bind_param("ii", $budget, $user_id);

echo json_encode([
    "success" => $stmt->execute()
]);
?>