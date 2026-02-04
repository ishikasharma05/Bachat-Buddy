<?php
session_start();
require 'config/db.php'; // Update path if needed

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];
$month = isset($_GET['month']) ? intval($_GET['month']) : date('m');

// Initialize totals
$totalIncome = 0;
$totalExpense = 0;
$totalSavings = 0;

// 1️⃣ Fetch total Income, Expense, Savings
$sqlTotals = "SELECT type, SUM(amount) as total 
              FROM transactions 
              WHERE user_id = ? AND MONTH(date) = ? 
              GROUP BY type";

$stmt = $conn->prepare($sqlTotals);
$stmt->bind_param("ii", $user_id, $month);
$stmt->execute();
$result = $stmt->get_result();

$donutData = [0,0,0,0,0,0]; // For Shopping, Entertainment, Education, Vehicle, House, Insurance
$categoriesMap = ['Shopping'=>0,'Entertainment'=>1,'Education'=>2,'Vehicle'=>3,'Household'=>4,'Insurance'=>5];

while($row = $result->fetch_assoc()) {
    if($row['type'] === 'income') $totalIncome = floatval($row['total']);
    if($row['type'] === 'expense') $totalExpense = floatval($row['total']);
    if($row['type'] === 'savings') $totalSavings = floatval($row['total']);
}

// 2️⃣ Monthly Bar Chart Data (Income and Expenses per month)
$months = range(1,12);
$incomeData = [];
$expenseData = [];
foreach($months as $m) {
    $stmt = $conn->prepare("SELECT type, SUM(amount) as total FROM transactions WHERE user_id=? AND MONTH(date)=? GROUP BY type");
    $stmt->bind_param("ii", $user_id, $m);
    $stmt->execute();
    $res = $stmt->get_result();
    $inc = 0; $exp = 0;
    while($r = $res->fetch_assoc()){
        if($r['type']==='income') $inc = floatval($r['total']);
        if($r['type']==='expense') $exp = floatval($r['total']);
    }
    $incomeData[] = $inc;
    $expenseData[] = $exp;
}

// 3️⃣ Donut chart: expense categories breakdown
$stmt = $conn->prepare("SELECT category, SUM(amount) as total FROM transactions WHERE user_id=? AND MONTH(date)=? AND type='expense' GROUP BY category");
$stmt->bind_param("ii", $user_id, $month);
$stmt->execute();
$res = $stmt->get_result();

while($r = $res->fetch_assoc()){
    $cat = $r['category'];
    if(isset($categoriesMap[$cat])){
        $donutData[$categoriesMap[$cat]] = floatval($r['total']);
    }
}

// Response
echo json_encode([
    'success'=>true,
    'totalIncome'=>$totalIncome,
    'totalExpense'=>$totalExpense,
    'totalSavings'=>$totalSavings,
    'incomeData'=>$incomeData,
    'expenseData'=>$expenseData,
    'donutData'=>$donutData
]);
?>