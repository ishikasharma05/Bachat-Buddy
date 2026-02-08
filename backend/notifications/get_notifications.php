<?php
session_start();

// Fix the path - go up two levels from backend/notifications to root
require_once __DIR__ . '/../../config/db.php';

header('Content-Type: application/json');

// Check authentication
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

$user_id = $_SESSION['user_id'];
$notifications = [];

try {
    // Get current month's data
    $currentYear = date('Y');
    $currentMonth = date('m');
    $today = date('Y-m-d');
    
    // =====================================================
    // 1. GET MONTHLY BUDGET FROM USER
    // =====================================================
    $stmt = $conn->prepare("SELECT monthly_budget FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $userData = $result->fetch_assoc();
    $monthlyBudget = floatval($userData['monthly_budget'] ?? 0);
    $stmt->close();
    
    // =====================================================
    // 2. GET TOTAL INCOME (ALL TIME)
    // =====================================================
    $stmt = $conn->prepare("
        SELECT COALESCE(SUM(amount), 0) as total 
        FROM transactions 
        WHERE user_id = ? AND type = 'income'
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $totalIncome = floatval($result->fetch_assoc()['total']);
    $stmt->close();
    
    // =====================================================
    // 3. GET TOTAL EXPENSES (ALL TIME)
    // =====================================================
    $stmt = $conn->prepare("
        SELECT COALESCE(SUM(amount), 0) as total 
        FROM transactions 
        WHERE user_id = ? AND type = 'expense'
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $totalExpenses = floatval($result->fetch_assoc()['total']);
    $stmt->close();
    
    // =====================================================
    // 4. GET TOTAL SAVINGS (ALL TIME)
    // =====================================================
    $stmt = $conn->prepare("
        SELECT COALESCE(SUM(amount), 0) as total 
        FROM transactions 
        WHERE user_id = ? AND type = 'savings'
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $totalSavings = floatval($result->fetch_assoc()['total']);
    $stmt->close();
    
    // =====================================================
    // 5. CALCULATE ACTUAL BALANCE
    // Balance = Income - Expenses - Savings
    // =====================================================
    $actualBalance = $totalIncome - $totalExpenses - $totalSavings;
    
    // =====================================================
    // 6. GET CURRENT MONTH'S DATA
    // =====================================================
    $stmt = $conn->prepare("
        SELECT COALESCE(SUM(amount), 0) as total 
        FROM transactions 
        WHERE user_id = ? AND type = 'expense' 
        AND YEAR(date) = ? AND MONTH(date) = ?
    ");
    $stmt->bind_param("iii", $user_id, $currentYear, $currentMonth);
    $stmt->execute();
    $result = $stmt->get_result();
    $monthlyExpense = floatval($result->fetch_assoc()['total']);
    $stmt->close();
    
    $stmt = $conn->prepare("
        SELECT COALESCE(SUM(amount), 0) as total 
        FROM transactions 
        WHERE user_id = ? AND type = 'income'
        AND YEAR(date) = ? AND MONTH(date) = ?
    ");
    $stmt->bind_param("iii", $user_id, $currentYear, $currentMonth);
    $stmt->execute();
    $result = $stmt->get_result();
    $monthlyIncome = floatval($result->fetch_assoc()['total']);
    $stmt->close();
    
    $stmt = $conn->prepare("
        SELECT COALESCE(SUM(amount), 0) as total 
        FROM transactions 
        WHERE user_id = ? AND type = 'savings'
        AND YEAR(date) = ? AND MONTH(date) = ?
    ");
    $stmt->bind_param("iii", $user_id, $currentYear, $currentMonth);
    $stmt->execute();
    $result = $stmt->get_result();
    $monthlySavings = floatval($result->fetch_assoc()['total']);
    $stmt->close();
    
    // Calculate budget remaining for THIS MONTH
    $budgetRemaining = $monthlyBudget - $monthlyExpense;
    
    // =====================================================
    // ALERT A: Monthly Budget Cross Alert
    // =====================================================
    if ($monthlyBudget > 0 && $monthlyExpense > $monthlyBudget) {
        $overBy = $monthlyExpense - $monthlyBudget;
        $notifications[] = [
            'icon' => '⚠️',
            'title' => 'Budget Alert',
            'message' => "This month's spending (₹" . number_format($monthlyExpense, 0) . ") crossed your budget of ₹" . number_format($monthlyBudget, 0) . " by ₹" . number_format($overBy, 0) . ". Want to review together?",
            'time_ago' => 'This month',
            'type' => 'budget_cross',
            'priority' => 1
        ];
    }
    
    // =====================================================
    // 7. GET TODAY'S EXPENSES
    // =====================================================
    $stmt = $conn->prepare("
        SELECT COUNT(*) as txn_count, COALESCE(SUM(amount), 0) as today_total
        FROM transactions 
        WHERE user_id = ? AND type = 'expense' AND date = ?
    ");
    $stmt->bind_param("is", $user_id, $today);
    $stmt->execute();
    $result = $stmt->get_result();
    $todayData = $result->fetch_assoc();
    $todayExpenses = floatval($todayData['today_total']);
    $todayTxnCount = intval($todayData['txn_count']);
    $stmt->close();
    
    // =====================================================
    // 8. GET AVERAGE DAILY EXPENSE
    // =====================================================
    $stmt = $conn->prepare("
        SELECT AVG(daily_total) as avg_daily
        FROM (
            SELECT DATE(date) as expense_date, SUM(amount) as daily_total
            FROM transactions
            WHERE user_id = ? AND type = 'expense'
            AND date >= DATE_SUB(?, INTERVAL 30 DAY)
            AND date < ?
            GROUP BY DATE(date)
        ) as daily_expenses
    ");
    $stmt->bind_param("iss", $user_id, $today, $today);
    $stmt->execute();
    $result = $stmt->get_result();
    $avgDaily = floatval($result->fetch_assoc()['avg_daily'] ?? 0);
    $stmt->close();
    
    // =====================================================
    // ALERT B: Impulsive Spending Alerts
    // =====================================================
    
    // Rule 1: More than 5 transactions in one day
    if ($todayTxnCount >= 5) {
        $notifications[] = [
            'icon' => '🛒',
            'title' => 'Shopping Spree',
            'message' => "You've made {$todayTxnCount} purchases today! Just checking - was this planned shopping?",
            'time_ago' => 'Today',
            'type' => 'impulsive_count',
            'priority' => 3
        ];
    }
    
    // Rule 2: Today's spending 2x higher than average
    if ($avgDaily > 0 && $todayExpenses > ($avgDaily * 2)) {
        $notifications[] = [
            'icon' => '📈',
            'title' => 'High Spending Day',
            'message' => "Today's spending (₹" . number_format($todayExpenses, 0) . ") is much higher than your usual ₹" . number_format($avgDaily, 0) . " daily average. Everything okay?",
            'time_ago' => 'Today',
            'type' => 'impulsive_amount',
            'priority' => 3
        ];
    }
    
    // Rule 3: Rapid transactions (3+ within 2 hours)
    $stmt = $conn->prepare("
        SELECT COUNT(*) as rapid_count
        FROM transactions
        WHERE user_id = ? 
        AND type = 'expense'
        AND created_at >= DATE_SUB(NOW(), INTERVAL 2 HOUR)
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $rapidCount = intval($result->fetch_assoc()['rapid_count']);
    $stmt->close();
    
    if ($rapidCount >= 3) {
        $notifications[] = [
            'icon' => '⚡',
            'title' => 'Quick Spending',
            'message' => "{$rapidCount} purchases in 2 hours! Take a breath - was this impulse buying or planned?",
            'time_ago' => 'Just now',
            'type' => 'impulsive_rapid',
            'priority' => 2
        ];
    }
    
    // =====================================================
    // ALERT C: Low Balance Alert (Focus on saving)
    // =====================================================
    if ($actualBalance > 0 && $actualBalance < 1000) {
        $notifications[] = [
            'icon' => '💛',
            'title' => 'Balance Check',
            'message' => "Your balance is running low. Time to focus on saving and reducing spending!",
            'time_ago' => 'Now',
            'type' => 'low_balance',
            'priority' => 2
        ];
    }
    
    // =====================================================
    // ALERT D: Low Savings Alert
    // =====================================================
    if ($monthlyIncome > 0) {
        $savingsRate = ($monthlySavings / $monthlyIncome) * 100;
        
        if ($savingsRate < 10 && date('d') > 15 && $monthlyIncome > 1000) {
            $notifications[] = [
                'icon' => '💙',
                'title' => 'Savings Reminder',
                'message' => "You've saved only " . number_format($savingsRate, 1) . "% this month (₹" . number_format($monthlySavings, 0) . "). Try to save at least 10-20%!",
                'time_ago' => 'This month',
                'type' => 'low_savings',
                'priority' => 4
            ];
        }
    }
    
    // =====================================================
    // ALERT E: Spending Velocity
    // =====================================================
    $dayOfMonth = intval(date('d'));
    if ($dayOfMonth >= 10 && $monthlyBudget > 0 && $monthlyExpense > 0) {
        $daysInMonth = intval(date('t'));
        $percentMonthPassed = ($dayOfMonth / $daysInMonth) * 100;
        $percentBudgetUsed = ($monthlyExpense / $monthlyBudget) * 100;
        
        if ($percentBudgetUsed > ($percentMonthPassed + 25)) {
            $notifications[] = [
                'icon' => '📊',
                'title' => 'Spending Too Fast',
                'message' => "You've used " . number_format($percentBudgetUsed, 0) . "% of your budget but only " . number_format($percentMonthPassed, 0) . "% of the month is over. Slow down!",
                'time_ago' => 'This month',
                'type' => 'spending_velocity',
                'priority' => 3
            ];
        }
    }
    
    // =====================================================
    // ALERT F: Negative Balance
    // =====================================================
    if ($actualBalance < 0) {
        $deficit = abs($actualBalance);
        $notifications[] = [
            'icon' => '🚨',
            'title' => 'Deficit Alert',
            'message' => "You're in deficit by ₹" . number_format($deficit, 0) . "! Your expenses exceed your income. Need help planning?",
            'time_ago' => 'Now',
            'type' => 'negative_balance',
            'priority' => 1
        ];
    }
    
    // =====================================================
    // Remove duplicates and sort
    // =====================================================
    $uniqueNotifications = [];
    $seenTypes = [];
    foreach ($notifications as $notif) {
        if (!in_array($notif['type'], $seenTypes)) {
            $uniqueNotifications[] = $notif;
            $seenTypes[] = $notif['type'];
        }
    }
    
    // Sort by priority
    usort($uniqueNotifications, function($a, $b) {
        return ($a['priority'] ?? 99) - ($b['priority'] ?? 99);
    });
    
    // Limit to top 5
    $uniqueNotifications = array_slice($uniqueNotifications, 0, 5);
    
    echo json_encode([
        'success' => true,
        'notifications' => $uniqueNotifications,
        'count' => count($uniqueNotifications),
        'debug' => [
            'total_income' => $totalIncome,
            'total_expenses' => $totalExpenses,
            'total_savings' => $totalSavings,
            'actual_balance' => $actualBalance,
            'monthly_income' => $monthlyIncome,
            'monthly_expense' => $monthlyExpense,
            'monthly_budget' => $monthlyBudget,
            'budget_remaining' => $budgetRemaining
        ]
    ]);
    
} catch (Exception $e) {
    error_log("Notification Error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage(),
        'notifications' => [],
        'count' => 0
    ]);
}

$conn->close();
?>