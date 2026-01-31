<?php
session_start();
header("Content-Type: application/json");
require_once "../config/db.php";

// Check login
if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit;
}

$user_id = $_SESSION['user_id'];

$query = "
    SELECT 
        id,
        DATE_FORMAT(transaction_date, '%Y-%m-%d') as date,
        description,
        category,
        type,
        amount
    FROM transactions
    WHERE user_id = ?
    ORDER BY transaction_date DESC
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();
$transactions = [];

while ($row = $result->fetch_assoc()) {
    // Make expense negative for frontend logic
    if ($row['type'] === 'Expense') {
        $row['amount'] = -abs($row['amount']);
    }
    $transactions[] = $row;
}

echo json_encode($transactions);
$stmt->close();
$conn->close();     
?>