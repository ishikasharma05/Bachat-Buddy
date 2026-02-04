<?php
session_start();
include '../../config/db.php';
include '../../components/auth_check.php';

header('Content-Type: application/json');

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) { echo json_encode([]); exit; }

// Total Income
$result = $conn->query("SELECT SUM(amount) as totalIncome FROM transactions WHERE user_id=$user_id AND type='income'");
$totalIncome = $result->fetch_assoc()['totalIncome'] ?? 0;

// Total Expense
$result = $conn->query("SELECT SUM(amount) as totalExpense FROM transactions WHERE user_id=$user_id AND type='expense'");
$totalExpense = $result->fetch_assoc()['totalExpense'] ?? 0;

// Total Savings
$result = $conn->query("SELECT SUM(amount) as totalSavings FROM transactions WHERE user_id=$user_id AND type='savings'");
$totalSavings = $result->fetch_assoc()['totalSavings'] ?? 0;

// Monthly Data
$months = ['Jan'=>1,'Feb'=>2,'Mar'=>3,'Apr'=>4,'May'=>5,'Jun'=>6,'Jul'=>7,'Aug'=>8,'Sep'=>9,'Oct'=>10,'Nov'=>11,'Dec'=>12];
$incomeData = []; $expenseData = []; $donutData = [];

foreach ($months as $m => $num) {
    $res = $conn->query("SELECT SUM(amount) as sumAmt FROM transactions WHERE user_id=$user_id AND type='income' AND MONTH(date)=$num");
    $incomeData[] = $res->fetch_assoc()['sumAmt'] ?? 0;

    $res = $conn->query("SELECT SUM(amount) as sumAmt FROM transactions WHERE user_id=$user_id AND type='expense' AND MONTH(date)=$num");
    $expenseData[] = $res->fetch_assoc()['sumAmt'] ?? 0;
}

// Donut data: last selected month (use current month)
$currMonth = date('n');
$categories = ['Shopping','Entertainment','Education','Vehicle','Household','Insurance'];
foreach ($categories as $cat) {
    $res = $conn->query("SELECT SUM(amount) as sumAmt FROM transactions WHERE user_id=$user_id AND type='expense' AND category='$cat' AND MONTH(date)=$currMonth");
    $donutData[] = $res->fetch_assoc()['sumAmt'] ?? 0;
}

echo json_encode([
    'totalIncome'=>$totalIncome,
    'totalExpense'=>$totalExpense,
    'totalSavings'=>$totalSavings,
    'incomeData'=>$incomeData,
    'expenseData'=>$expenseData,
    'donutData'=>$donutData
]);
?>