<?php
/**
 * Backend: Fetch Transactions - UNIFIED VERSION
 * Handles both dashboard data AND transaction list
 */

session_start();
require_once '../../config/db.php';

header('Content-Type: application/json');

// Check authentication
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Not authenticated']);
    exit();
}

$user_id = $_SESSION['user_id'];
$currentYear = date('Y');
$currentMonth = date('m');

// Get parameters
$action = isset($_GET['action']) ? $_GET['action'] : 'list'; // Default to 'list' for transaction page
$selectedMonth = isset($_GET['month']) ? $_GET['month'] : $currentMonth;

// Validate month if provided
if (isset($_GET['month']) && !preg_match('/^(0[1-9]|1[0-2])$/', $selectedMonth)) {
    echo json_encode(['error' => 'Invalid month']);
    exit();
}

// Initialize response
$response = [];

try {
    // ============================================
    // ACTION: Get Transaction List (NEW - for transaction.php page)
    // ============================================
    if ($action === 'list') {
        // Fetch all transactions for the user
        $stmt = $conn->prepare("
            SELECT id, type, amount, category, description, tags, date, created_at
            FROM transactions 
            WHERE user_id = ? 
            ORDER BY date DESC, created_at DESC
        ");
        
        if (!$stmt) {
            echo json_encode(['error' => 'Query preparation failed: ' . $conn->error]);
            exit();
        }
        
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $transactions = [];
        
        while ($row = $result->fetch_assoc()) {
            $transactions[] = [
                'id' => intval($row['id']),
                'type' => $row['type'],
                'amount' => floatval($row['amount']),
                'category' => $row['category'] ?? 'Uncategorized',
                'description' => $row['description'] ?? '',
                'tags' => $row['tags'] ?? '',
                'date' => $row['date'],
                'created_at' => $row['created_at']
            ];
        }
        
        $stmt->close();
        
        // Return the transactions array directly
        echo json_encode($transactions);
    }
    
    // ============================================
    // ACTION: Get Month Expenses (for dropdown)
    // ============================================
    else if ($action === 'month_expenses') {
        $response = [
            'donutData' => [0, 0, 0, 0, 0, 0],
            'categoryLabels' => ['Shopping', 'Fun', 'Kids', 'Vehicle', 'House', 'Insure'],
            'categoryBreakdown' => []
        ];

        // Category mapping
        $categoryMapping = [
            'Shopping' => 0,
            'Groceries' => 0,
            'Clothing' => 0,
            'Fun' => 1,
            'Entertainment' => 1,
            'Kids' => 2,
            'Education' => 2,
            'Vehicle' => 3,
            'Transport' => 3,
            'House' => 4,
            'Rent' => 4,
            'Utilities' => 4,
            'Insure' => 5,
            'Insurance' => 5
        ];

        // Get category-wise expenses for selected month
        $stmt = $conn->prepare("
            SELECT category, SUM(amount) as total 
            FROM transactions 
            WHERE user_id = ? AND type = 'expense' AND YEAR(date) = ? AND MONTH(date) = ?
            GROUP BY category
        ");
        $stmt->bind_param("iii", $user_id, $currentYear, $selectedMonth);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            $category = $row['category'];
            if (isset($categoryMapping[$category])) {
                $response['donutData'][$categoryMapping[$category]] += floatval($row['total']);
            }
        }
        $stmt->close();

        // Get category breakdown with percentages
        $stmt = $conn->prepare("
            SELECT category, SUM(amount) as total 
            FROM transactions 
            WHERE user_id = ? AND type = 'expense' AND YEAR(date) = ? AND MONTH(date) = ?
            GROUP BY category
            ORDER BY total DESC
        ");
        $stmt->bind_param("iii", $user_id, $currentYear, $selectedMonth);
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
        
        echo json_encode($response);
    }
    
    // ============================================
    // ACTION: Get Full Dashboard Data (for dashboard/index.php)
    // ============================================
    else if ($action === 'dashboard') {
        $response = [
            'totalIncome' => 0,
            'totalExpense' => 0,
            'totalSavings' => 0,
            'balance' => 0,
            'incomeData' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            'expenseData' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            'donutData' => [0, 0, 0, 0, 0, 0],
            'categoryLabels' => ['Shopping', 'Fun', 'Kids', 'Vehicle', 'House', 'Insure'],
            'recentTransactions' => [],
            'categoryBreakdown' => [],
            'insights' => []
        ];

        // 1. Total Income
        $stmt = $conn->prepare("SELECT COALESCE(SUM(amount), 0) as total FROM transactions WHERE user_id = ? AND type = 'income'");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $response['totalIncome'] = floatval($result->fetch_assoc()['total']);
        $stmt->close();

        // 2. Total Expense
        $stmt = $conn->prepare("SELECT COALESCE(SUM(amount), 0) as total FROM transactions WHERE user_id = ? AND type = 'expense'");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $response['totalExpense'] = floatval($result->fetch_assoc()['total']);
        $stmt->close();

        // 3. Total Savings
        $stmt = $conn->prepare("SELECT COALESCE(SUM(amount), 0) as total FROM transactions WHERE user_id = ? AND type = 'savings'");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $response['totalSavings'] = floatval($result->fetch_assoc()['total']);
        $stmt->close();

        // Calculate Balance
        $response['balance'] = $response['totalIncome'] - $response['totalExpense'];

        // 4. Monthly Income Data
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

        // 5. Monthly Expense Data
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

        // 6. Category Breakdown for Donut (Current Month)
        $categoryMapping = [
            'Shopping' => 0,
            'Groceries' => 0,
            'Clothing' => 0,
            'Fun' => 1,
            'Entertainment' => 1,
            'Kids' => 2,
            'Education' => 2,
            'Vehicle' => 3,
            'Transport' => 3,
            'House' => 4,
            'Rent' => 4,
            'Utilities' => 4,
            'Insure' => 5,
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
                $response['donutData'][$categoryMapping[$category]] += floatval($row['total']);
            }
        }
        $stmt->close();

        // 7. Recent Transactions
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

        // 8. Category Breakdown with Percentages
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

        // 9. Generate Insights
        if ($response['totalExpense'] === 0 && $response['totalIncome'] === 0) {
            $response['insights'][] = '📊 Start tracking your expenses to see personalized insights!';
        } else {
            if ($response['totalIncome'] > 0) {
                $savingsRate = (($response['totalIncome'] - $response['totalExpense']) / $response['totalIncome'] * 100);
                if ($savingsRate > 20) {
                    $response['insights'][] = sprintf('🎉 Great job! You\'re saving %.1f%% of your income.', $savingsRate);
                } elseif ($savingsRate > 0) {
                    $response['insights'][] = sprintf('💡 You\'re saving %.1f%% - try to increase it to 20%%+.', $savingsRate);
                } else {
                    $response['insights'][] = '⚠️ Your expenses exceed your income. Review your spending.';
                }
            }
            
            $maxExpense = max($response['donutData']);
            if ($maxExpense > 0 && $response['totalExpense'] > 0) {
                $maxIndex = array_search($maxExpense, $response['donutData']);
                $categoryName = $response['categoryLabels'][$maxIndex];
                $percentage = ($maxExpense / $response['totalExpense'] * 100);
                $response['insights'][] = sprintf('📈 %s is your highest expense category at %.1f%%.', $categoryName, $percentage);
            }
            
            // Calculate average monthly expense (only months with expenses)
            $monthsWithExpenses = count(array_filter($response['expenseData'], function($val) { return $val > 0; }));
            if ($monthsWithExpenses > 0) {
                $monthlyAvg = $response['totalExpense'] / $monthsWithExpenses;
                $response['insights'][] = sprintf('📅 Your average monthly expense is ₹%s.', number_format($monthlyAvg, 0));
            }
        }
        
        echo json_encode($response);
    }
    
    // ============================================
    // DEFAULT: If no action specified or invalid action
    // ============================================
    else {
        echo json_encode(['error' => 'Invalid action parameter. Use: list, dashboard, or month_expenses']);
    }

} catch (Exception $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}

$conn->close();
?>