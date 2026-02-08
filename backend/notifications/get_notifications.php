<?php
session_start();
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
    // 2. GET CURRENT MONTH'S TOTAL EXPENSE
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
    
    // =====================================================
    // ALERT A: Monthly Budget Cross Alert
    // =====================================================
    if ($monthlyBudget > 0 && $monthlyExpense > $monthlyBudget) {
        $overBy = $monthlyExpense - $monthlyBudget;
        $notifications[] = [
            'icon' => '👀',
            'title' => 'Budget Check',
            'message' => "Hey! This month's spending (₹" . number_format($monthlyExpense, 0) . ") crossed your planned budget of ₹" . number_format($monthlyBudget, 0) . ". Want to review it together?",
            'time_ago' => 'Now',
            'type' => 'budget_cross'
        ];
    }
    
    // =====================================================
    // 3. GET TODAY'S EXPENSES
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
    // 4. GET AVERAGE DAILY EXPENSE (last 30 days, excluding today)
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
    // ALERT B: Impulsive Spending Alert
    // =====================================================
    
    // Rule 1: More than 5 transactions in one day
    if ($todayTxnCount >= 5) {
        $notifications[] = [
            'icon' => '🙂',
            'title' => 'Quick Check',
            'message' => "You've made {$todayTxnCount} purchases today. Just making sure everything's intentional! Want to look at them together?",
            'time_ago' => 'Today',
            'type' => 'impulsive_count'
        ];
    }
    
    // Rule 2: Today's spending is 2x higher than daily average
    if ($avgDaily > 0 && $todayExpenses > ($avgDaily * 2)) {
        $notifications[] = [
            'icon' => '🙂',
            'title' => 'Spending Pattern',
            'message' => "Today's spending (₹" . number_format($todayExpenses, 0) . ") is higher than your usual daily average. Everything okay?",
            'time_ago' => 'Today',
            'type' => 'impulsive_amount'
        ];
    }
    
    // Rule 3: Check for rapid transactions (3+ within 2 hours)
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
            'message' => "You've made {$rapidCount} purchases in the last 2 hours. Quick check - was this planned shopping or impulse buying?",
            'time_ago' => 'Just now',
            'type' => 'impulsive_rapid'
        ];
    }
    
    // =====================================================
    // 5. GET SAVINGS DATA
    // =====================================================
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
    
    // =====================================================
    // ALERT C1: Low Savings Alert
    // =====================================================
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
    
    if ($monthlyIncome > 0) {
        $savingsRate = ($monthlySavings / $monthlyIncome) * 100;
        
        // If savings rate is less than 5% and we're past mid-month
        if ($savingsRate < 5 && date('d') > 15) {
            $notifications[] = [
                'icon' => '💙',
                'title' => 'Savings Reminder',
                'message' => "You've saved " . number_format($savingsRate, 1) . "% this month. Even small amounts add up! Want to set a savings goal?",
                'time_ago' => 'This month',
                'type' => 'low_savings'
            ];
        }
    }
    
    // =====================================================
    // ALERT C2: Balance Running Low
    // =====================================================
    $balance = $monthlyIncome - $monthlyExpense;
    if ($balance > 0 && $balance < ($monthlyBudget * 0.15) && $monthlyBudget > 0) {
        $notifications[] = [
            'icon' => '💛',
            'title' => 'Balance Check',
            'message' => "Your remaining budget for this month is getting low (₹" . number_format($balance, 0) . "). Let's plan the rest of the month wisely!",
            'time_ago' => 'Now',
            'type' => 'low_balance'
        ];
    }
    
    // =====================================================
    // ALERT C3: Spending Velocity Alert
    // =====================================================
    $dayOfMonth = intval(date('d'));
    if ($dayOfMonth >= 10 && $monthlyBudget > 0) {
        $daysInMonth = intval(date('t'));
        $percentMonthPassed = ($dayOfMonth / $daysInMonth) * 100;
        $percentBudgetUsed = ($monthlyExpense / $monthlyBudget) * 100;
        
        // If we've used more budget % than month % passed (e.g., 60% budget used but only 40% month passed)
        if ($percentBudgetUsed > ($percentMonthPassed + 20)) {
            $notifications[] = [
                'icon' => '📊',
                'title' => 'Spending Pace',
                'message' => "You've used " . number_format($percentBudgetUsed, 0) . "% of your monthly budget, but we're only " . number_format($percentMonthPassed, 0) . "% through the month. Might want to slow down a bit!",
                'time_ago' => 'This month',
                'type' => 'spending_velocity'
            ];
        }
    }
    
    // =====================================================
    // Remove duplicates by type (keep only the first of each type)
    // =====================================================
    $uniqueNotifications = [];
    $seenTypes = [];
    foreach ($notifications as $notif) {
        if (!in_array($notif['type'], $seenTypes)) {
            $uniqueNotifications[] = $notif;
            $seenTypes[] = $notif['type'];
        }
    }
    
    // Limit to 5 most important notifications
    $uniqueNotifications = array_slice($uniqueNotifications, 0, 5);
    
    echo json_encode([
        'success' => true,
        'notifications' => $uniqueNotifications,
        'count' => count($uniqueNotifications)
    ]);
    
} catch (Exception $e) {
    error_log("Notification Error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error loading notifications',
        'notifications' => [],
        'count' => 0
    ]);
}

$conn->close();
?>