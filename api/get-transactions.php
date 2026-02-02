<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
header("Content-Type: application/json");

require_once __DIR__ . "/../config/db.php";

$sql = "
    SELECT 
        id,
        type,
        category,
        description,
        transaction_date AS date,
        amount
    FROM transactions
    ORDER BY transaction_date DESC
";

$result = mysqli_query($conn, $sql);

$transactions = [];

while ($row = mysqli_fetch_assoc($result)) {

    // Make amount negative for expense & withdraw (for frontend logic)
    if ($row['type'] === 'expense' || $row['type'] === 'withdraw-savings') {
        $row['amount'] = -abs($row['amount']);
    }

    // Capitalize type for filter match
    $row['type'] = ucfirst($row['type']);

    $transactions[] = $row;
}

echo json_encode($transactions);
?>