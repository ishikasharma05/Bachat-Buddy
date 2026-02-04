<?php
// backend/transactions/fetch_dashboard.php
session_start();
require_once '../../config/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Not authenticated']);
    exit();
}

$user_id = $_SESSION['user_id'];

// Initialize response array
$response = [
    'totalIncome' => 0,
    'totalExpense' => 0,
    'totalSavings' => 0,
    'incomeData' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0], // 12 months
    'expenseData' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0], // 12 months
    'donutData' => [0, 0, 0, 0, 0, 0], // Shopping, Entertainment, Education, Vehicle, Household, Insurance
    'recentTransactions' => [],
    'categoryBreakdown' => []
];

try {
    // Get current year
    $currentYear = date('Y');

    // 1. Calculate Total Income (all time)
    $stmt = $conn->prepare("SELECT COALESCE(SUM(amount), 0) as total FROM transactions WHERE user_id = ? AND type = 'income'");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $response['totalIncome'] = $result->fetch_assoc()['total'];
    $stmt->close();

    // 2. Calculate Total Expense (all time)
    $stmt = $conn->prepare("SELECT COALESCE(SUM(amount), 0) as total FROM transactions WHERE user_id = ? AND type = 'expense'");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $response['totalExpense'] = $result->fetch_assoc()['total'];
    $stmt->close();

    // 3. Calculate Total Savings (all time)
    $stmt = $conn->prepare("SELECT COALESCE(SUM(amount), 0) as total FROM transactions WHERE user_id = ? AND type = 'savings'");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $response['totalSavings'] = $result->fetch_assoc()['total'];
    $stmt->close();

    // 4. Get Monthly Income Data (current year)
    $stmt = $conn->prepare("
        SELECT MONTH(date) as month, SUM(amount) as total 
        FROM transactions 
        WHERE user_id = ? AND type = 'income' AND YEAR(date) = ?
        GROUP BY MONTH(date)
    ");
    $stmt->bind_param("ii", $user_id, $currentYear);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $response['incomeData'][$row['month'] - 1] = floatval($row['total']);
    }
    $stmt->close();

    // 5. Get Monthly Expense Data (current year)
    $stmt = $conn->prepare("
        SELECT MONTH(date) as month, SUM(amount) as total 
        FROM transactions 
        WHERE user_id = ? AND type = 'expense' AND YEAR(date) = ?
        GROUP BY MONTH(date)
    ");
    $stmt->bind_param("ii", $user_id, $currentYear);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $response['expenseData'][$row['month'] - 1] = floatval($row['total']);
    }
    $stmt->close();

    // 6. Get Category-wise Expense Breakdown for Donut Chart (current month)
    $currentMonth = date('m');
    $categoryMapping = [
        'Shopping' => 0,
        'Entertainment' => 1,
        'Education' => 2,
        'Vehicle' => 3,
        'Household' => 4,
        'Insurance' => 5
    ];

    $stmt = $conn->prepare("
        SELECT category, SUM(amount) as total 
        FROM transactions 
        WHERE user_id = ? AND type = 'expense' AND YEAR(date) = ? AND MONTH(date) = ?
        GROUP BY category
    ");
    $stmt->bind_param("iii", $user_id, $currentYear, $currentMonth);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $category = $row['category'];
        if (isset($categoryMapping[$category])) {
            $response['donutData'][$categoryMapping[$category]] = floatval($row['total']);
        }
    }
    $stmt->close();

    // 7. Get Recent Transactions (last 5)
    $stmt = $conn->prepare("
        SELECT type, category, amount, description, date 
        FROM transactions 
        WHERE user_id = ? 
        ORDER BY date DESC, created_at DESC 
        LIMIT 5
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $response['recentTransactions'][] = [
            'type' => $row['type'],
            'category' => $row['category'] ?: $row['description'],
            'amount' => floatval($row['amount']),
            'date' => $row['date']
        ];
    }
    $stmt->close();

    // 8. Get Category Breakdown (current month)
    $stmt = $conn->prepare("
        SELECT category, SUM(amount) as total 
        FROM transactions 
        WHERE user_id = ? AND type = 'expense' AND YEAR(date) = ? AND MONTH(date) = ?
        GROUP BY category
        ORDER BY total DESC
    ");
    $stmt->bind_param("iii", $user_id, $currentYear, $currentMonth);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $totalExpenseThisMonth = array_sum($response['donutData']);
    while ($row = $result->fetch_assoc()) {
        $amount = floatval($row['total']);
        $percentage = $totalExpenseThisMonth > 0 ? ($amount / $totalExpenseThisMonth) * 100 : 0;
        $response['categoryBreakdown'][] = [
            'category' => $row['category'],
            'amount' => $amount,
            'percentage' => round($percentage, 1)
        ];
    }
    $stmt->close();

    // Send JSON response
    header('Content-Type: application/json');
    echo json_encode($response);

} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}

$conn->close();
?>