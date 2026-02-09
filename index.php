<?php
// index.php - Dashboard with Enhanced UI/UX
require_once 'components/auth_check.php';
require_once 'config/db.php';

// Get current year and month
$currentYear = date('Y');
$currentMonth = date('m');

// Get selected month from query parameter (for AJAX updates)
$selectedMonth = isset($_GET['month']) ? $_GET['month'] : $currentMonth;

// Initialize all data arrays
$totalIncome = 0;
$totalExpense = 0;
$totalSavings = 0;
$incomeData = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
$expenseData = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
$donutData = [0, 0, 0, 0, 0, 0];
$categoryLabels = ['Shopping', 'Fun', 'Kids', 'Vehicle', 'House', 'Insure'];
$categoryExpenses = [];
$recentTransactions = [];

try {
    // 1. Total Income (Current Year Only)
    $stmt = $conn->prepare("SELECT COALESCE(SUM(amount), 0) as total FROM transactions WHERE user_id = ? AND type = 'income' AND YEAR(date) = ?");
    $stmt->bind_param("ii", $user_id, $currentYear);
    $stmt->execute();
    $result = $stmt->get_result();
    $totalIncome = floatval($result->fetch_assoc()['total']);
    $stmt->close();

    // 2. Total Expense (Current Year Only)
    $stmt = $conn->prepare("SELECT COALESCE(SUM(amount), 0) as total FROM transactions WHERE user_id = ? AND type = 'expense' AND YEAR(date) = ?");
    $stmt->bind_param("ii", $user_id, $currentYear);
    $stmt->execute();
    $result = $stmt->get_result();
    $totalExpense = floatval($result->fetch_assoc()['total']);
    $stmt->close();

    // 3. Total Savings (Current Year Only)
    $stmt = $conn->prepare("SELECT COALESCE(SUM(amount), 0) as total FROM transactions WHERE user_id = ? AND type = 'savings' AND YEAR(date) = ?");
    $stmt->bind_param("ii", $user_id, $currentYear);
    $stmt->execute();
    $result = $stmt->get_result();
    $totalSavings = floatval($result->fetch_assoc()['total']);
    $stmt->close();

    // 4. Monthly Income Data (Current Year Only)
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
        $incomeData[$row['month'] - 1] = floatval($row['total']);
    }
    $stmt->close();

    // 5. Monthly Expense Data (Current Year Only)
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
        $expenseData[$row['month'] - 1] = floatval($row['total']);
    }
    $stmt->close();

    // 6. Category Breakdown for Donut (Selected Month) - ALL EXPENSES
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

    // Get ALL expense transactions for selected month
    $stmt = $conn->prepare("
        SELECT category, description, amount 
        FROM transactions 
        WHERE user_id = ? AND type = 'expense' AND YEAR(date) = ? AND MONTH(date) = ?
    ");
    $stmt->bind_param("iii", $user_id, $currentYear, $selectedMonth);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $category = $row['category'];
        $amount = floatval($row['amount']);

        // Add to donut data based on category mapping
        if (isset($categoryMapping[$category])) {
            $donutData[$categoryMapping[$category]] += $amount;
        }
    }
    $stmt->close();

    // 7. Category Breakdown with Percentages (Selected Month) - ALL EXPENSES
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

    $totalExpenseThisMonth = array_sum($donutData);
    while ($row = $result->fetch_assoc()) {
        $amount = floatval($row['total']);
        $percentage = $totalExpenseThisMonth > 0 ? ($amount / $totalExpenseThisMonth) * 100 : 0;
        $categoryExpenses[] = [
            'category' => $row['category'],
            'amount' => $amount,
            'percentage' => round($percentage, 1)
        ];
    }
    $stmt->close();

    // 8. Recent Transactions (Last 5)
    $stmt = $conn->prepare("
        SELECT type, category, description, amount, date 
        FROM transactions 
        WHERE user_id = ? 
        ORDER BY date DESC, created_at DESC 
        LIMIT 5
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $recentTransactions[] = $row;
    }
    $stmt->close();
} catch (Exception $e) {
    error_log("Dashboard Error: " . $e->getMessage());
}

$conn->close();

$balance = $totalIncome - $totalExpense;
$donutTotal = array_sum($donutData);

// Calculate average monthly expense
$monthsWithExpenses = count(array_filter($expenseData, function ($val) {
    return $val > 0;
}));
$monthlyAvg = $monthsWithExpenses > 0 ? $totalExpense / $monthsWithExpenses : 0;

// Helper function to format amount (remove .00 if whole number)
function formatAmount($amount)
{
    if (floor($amount) == $amount) {
        return number_format($amount, 0);
    }
    return number_format($amount, 2);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bachat-Buddy - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="components/style.css">
    <link rel="stylesheet" href="assets/css/chatbot-style.css">
    <style>
        /* ===================================================================
           ENHANCED DESIGN SYSTEM - MATCHING TRANSACTION.PHP
           ================================================================ */
        * {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            scrollbar-width: auto;
            scrollbar-color: auto;
        }

        ::-webkit-scrollbar {
            width: 10px;
            height: 10px;
        }

        ::-webkit-scrollbar-track {
            background: var(--bg-main);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--border-medium);
            border-radius: 5px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--text-muted);
        }

        :root {
            /* Primary Colors */
            --color-primary-500: #00bcd4;
            --color-primary-600: #00acc1;
            --color-primary-700: #0097a7;
            
            /* Semantic Colors */
            --color-success-500: #4caf50;
            --color-success-600: #43a047;
            --color-danger-500: #f44336;
            --color-danger-600: #e53935;
            
            /* Neutral Colors */
            --bg-main: #f8fafb;
            --bg-card: #ffffff;
            --bg-elevated: #ffffff;
            --text-primary: #1a202c;
            --text-secondary: #4a5568;
            --text-muted: #718096;
            --border-light: #e2e8f0;
            --border-medium: #cbd5e0;
            --sidebar-bg: #ffffff;
            --header-bg: #ffffff;
            --input-bg: #f7fafc;
            
            /* Shadows */
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        [data-theme="dark"] {
            --bg-main: #0f172a;
            --bg-card: #1e293b;
            --bg-elevated: #334155;
            --text-primary: #f1f5f9;
            --text-secondary: #cbd5e1;
            --text-muted: #94a3b8;
            --border-light: #334155;
            --border-medium: #475569;
            --sidebar-bg: #1e293b;
            --header-bg: #1e293b;
            --input-bg: #334155;
        }

        body {
            margin: 0;
            background-color: var(--bg-main);
            color: var(--text-primary);
            transition: background-color 0.3s ease, color 0.3s ease;
            font-size: 15px;
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        .layout {
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        /* ===================================================================
           ENHANCED SIDEBAR - MATCHING TRANSACTION.PHP
           ================================================================ */
        .sidebar {
            width: 260px;
            background-color: var(--sidebar-bg);
            border-right: 1px solid var(--border-light);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 1.5rem 1rem;
            transition: all 0.3s ease;
        }

        .sidebar .nav-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            border-radius: 12px;
            color: var(--text-secondary);
            font-weight: 500;
            font-size: 0.9375rem;
            transition: all 0.2s ease;
            text-decoration: none;
            margin-bottom: 0.25rem;
        }

        .sidebar .nav-link:hover {
            background: linear-gradient(135deg, var(--color-primary-500), var(--color-primary-600));
            color: white;
            transform: translateX(4px);
        }

        .sidebar .nav-link.active {
            background: linear-gradient(135deg, var(--color-primary-500), var(--color-primary-600));
            color: white;
            box-shadow: 0 4px 12px rgba(0, 188, 212, 0.25);
        }

        .sidebar .nav-link i {
            margin-right: 0.875rem;
            font-size: 1.25rem;
            width: 24px;
            text-align: center;
        }

        .brand {
            font-weight: 800;
            font-size: 1.375rem;
            padding: 0.75rem 1rem;
            margin-bottom: 2rem;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 0.625rem;
        }

        .brand i {
            color: var(--color-success-500);
            font-size: 1.75rem;
        }

        /* ===================================================================
           ENHANCED HEADER - MATCHING TRANSACTION.PHP
           ================================================================ */
        .header {
            background-color: var(--header-bg);
            padding: 1rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid var(--border-light);
            transition: all 0.3s ease;
            box-shadow: var(--shadow-sm);
        }

        .notification,
        #theme-toggle {
            background: var(--input-bg);
            padding: 0.625rem;
            border-radius: 12px;
            border: 2px solid var(--border-light);
            cursor: pointer;
            color: var(--text-secondary);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
            width: 42px;
            height: 42px;
        }

        .notification:hover,
        #theme-toggle:hover {
            background: var(--bg-elevated);
            border-color: var(--color-primary-400);
            color: var(--color-primary-600);
            transform: scale(1.05);
        }

        .profile-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            background: var(--input-bg);
            padding: 0.5rem 1rem;
            border-radius: 50px;
            border: 2px solid var(--border-light);
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .profile-info:hover {
            background: var(--bg-elevated);
            border-color: var(--color-primary-400);
        }

        .profile-info img {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            object-fit: cover;
        }

        .profile-text {
            color: var(--text-primary);
            font-weight: 600;
            font-size: 0.9375rem;
        }

        /* Mobile Sidebar */
        .mobile-sidebar {
            position: fixed;
            top: 0;
            left: -300px;
            width: 260px;
            height: 100vh;
            background-color: var(--sidebar-bg);
            z-index: 1050;
            transition: left 0.3s ease;
            overflow-y: auto;
            box-shadow: var(--shadow-xl);
        }

        .mobile-sidebar.active {
            left: 0;
        }

        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1040;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease;
            backdrop-filter: blur(4px);
        }

        .sidebar-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            height: 100vh;
            overflow: hidden;
        }

        .main-body {
            padding: 2rem;
            overflow-y: auto;
            background-color: var(--bg-main);
        }

        /* ===================================================================
           DASHBOARD SPECIFIC STYLES
           ================================================================ */
        .welcome-banner {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
            color: white;
            box-shadow: 0 10px 30px rgba(59, 130, 246, 0.3);
            animation: slideDown 0.6s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .welcome-banner h2 {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .welcome-banner p {
            font-size: 1.1rem;
            opacity: 0.95;
        }

        [data-theme="dark"] .welcome-banner {
            background: linear-gradient(135deg, #1e40af 0%, #1e3a8a 100%);
        }

        .summary-row {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .summary-card {
            position: relative;
            border-radius: 16px;
            background: var(--bg-card);
            box-shadow: var(--shadow-sm);
            padding: 1.25rem 1.5rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            animation: scaleIn 0.5s ease-out backwards;
            border: 1px solid var(--border-light);
        }

        .summary-card:nth-child(1) { animation-delay: 0.1s; }
        .summary-card:nth-child(2) { animation-delay: 0.2s; }
        .summary-card:nth-child(3) { animation-delay: 0.3s; }
        .summary-card:nth-child(4) { animation-delay: 0.4s; }

        @keyframes scaleIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .summary-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
        }

        .summary-card-accent {
            position: absolute;
            top: 0;
            left: 0;
            width: 6px;
            height: 100%;
            border-radius: 16px 0 0 16px;
        }

        .summary-card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            flex-shrink: 0;
        }

        .income-icon {
            background: #dcfce7;
            color: #16a34a;
        }

        .expense-icon {
            background: #fee2e2;
            color: #dc2626;
        }

        .savings-icon {
            background: #dbeafe;
            color: #2563eb;
        }

        .balance-icon {
            background: #f3e8ff;
            color: #8b5cf6;
        }

        [data-theme="dark"] .income-icon {
            background: #14532d;
            color: #86efac;
        }

        [data-theme="dark"] .expense-icon {
            background: #450a0a;
            color: #fca5a5;
        }

        [data-theme="dark"] .savings-icon {
            background: #1e3a8a;
            color: #93c5fd;
        }

        [data-theme="dark"] .balance-icon {
            background: #581c87;
            color: #d8b4fe;
        }

        .dashboard-two-cols {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 2rem;
        }

        .card-custom {
            border-radius: 16px;
            padding: 1.5rem;
            background: var(--bg-card);
            box-shadow: var(--shadow-sm);
            animation: slideUp 0.6s ease-out backwards;
            animation-delay: 0.5s;
            border: 1px solid var(--border-light);
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .tab-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .tabs span {
            margin-left: 1rem;
            font-weight: 500;
            cursor: pointer;
            color: var(--text-muted);
            transition: color 0.3s;
            padding: 0.5rem 1rem;
            border-radius: 8px;
        }

        .tabs .active {
            color: var(--text-primary);
            background: #f0f9ff;
        }

        [data-theme="dark"] .tabs .active {
            color: var(--text-primary);
            background: var(--bg-elevated);
        }

        .donut-container {
            position: relative;
            width: 200px;
            height: 200px;
            margin: 0 auto 1rem;
        }

        .donut-center-text {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
        }

        .legend-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 8px;
        }

        .chart-insight-box {
            background: #f0f9ff;
            border-left: 4px solid #3b82f6;
            padding: 12px 16px;
            border-radius: 8px;
            margin-top: 16px;
        }

        [data-theme="dark"] .chart-insight-box {
            background: #1e3a5f;
            border-left-color: #60a5fa;
        }

        .chart-insight-box small {
            display: block;
            margin-bottom: 6px;
            color: #64748b;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
        }

        [data-theme="dark"] .chart-insight-box small {
            color: #94a3b8;
        }

        .chart-insight-text {
            font-weight: 500;
            font-size: 0.9rem;
            color: #1e293b;
            line-height: 1.6;
        }

        [data-theme="dark"] .chart-insight-text {
            color: #e2e8f0;
        }

        .insight-pill {
            background: #fef3c7 !important;
            border-left: 4px solid #fbbf24 !important;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 10px;
            font-size: 0.9rem;
            color: #92400e !important;
        }

        [data-theme="dark"] .insight-pill {
            background-color: #451a03 !important;
            border-left-color: #fbbf24 !important;
            color: #fde68a !important;
        }

        @media (max-width: 991px) {
            .sidebar.d-lg-block {
                display: none !important;
            }

            .main-body {
                padding: 1.5rem 1rem;
            }
        }

        @media (max-width: 768px) {
            .summary-row {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .dashboard-two-cols {
                grid-template-columns: 1fr;
            }

            .welcome-banner h2 {
                font-size: 1.5rem;
            }

            .welcome-banner p {
                font-size: 1rem;
            }
        }

        /* Dark mode card fixes */
        [data-theme="dark"] .card,
        [data-theme="dark"] .card-body,
        [data-theme="dark"] .card-custom {
            background-color: var(--bg-card) !important;
            color: var(--text-primary) !important;
        }

        [data-theme="dark"] .text-muted {
            color: var(--text-muted) !important;
        }
    </style>
</head>

<body>
    <div class="layout">
        <div class="sidebar d-none d-lg-block">
            <div>
                <div class="brand d-flex align-items-center mb-4">
                    <i class="bi bi-piggy-bank-fill"></i>
                    <span>Bachat Buddy</span>
                </div>
                <ul class="nav flex-column gap-2">
                    <li><a class="nav-link active" href="index.php"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
                    <li><a class="nav-link" href="profile.php"><i class="bi bi-person-circle"></i> Profile</a></li>
                    <li><a class="nav-link" href="transaction.php"><i class="bi bi-arrow-left-right"></i> Transactions</a></li>
                    <li><a class="nav-link" href="add-entry.php"><i class="bi bi-journal-plus"></i> Add Entry</a></li>
                    <li><a class="nav-link" href="goals.php"><i class="bi bi-bullseye"></i> Goals</a></li>
                </ul>
            </div>
        </div>

        <div id="mobileSidebar" class="mobile-sidebar d-lg-none">
            <div class="p-4">
                <div class="brand d-flex align-items-center justify-content-between mb-4">
                    <span><i class="bi bi-piggy-bank-fill"></i> Bachat Buddy</span>
                    <button onclick="toggleMenu()" class="btn-close"></button>
                </div>
                <ul class="nav flex-column gap-2">
                    <li><a class="nav-link active" href="index.php"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
                    <li><a class="nav-link" href="profile.php"><i class="bi bi-person-circle"></i> Profile</a></li>
                    <li><a class="nav-link" href="transaction.php"><i class="bi bi-arrow-left-right"></i> Transactions</a></li>
                    <li><a class="nav-link" href="add-entry.php"><i class="bi bi-journal-plus"></i> Add Entry</a></li>
                    <li><a class="nav-link" href="goals.php"><i class="bi bi-bullseye"></i> Goals</a></li>
                </ul>
            </div>
        </div>
        <div id="sidebarOverlay" class="sidebar-overlay d-lg-none" onclick="toggleMenu()"></div>

        <div class="main-content">
            <?php include 'components/header.php'; ?>
            <div class="main-body">
                <!-- Welcome Banner -->
                <div class="welcome-banner">
                    <h2>👋 Welcome back, <?= htmlspecialchars($_SESSION['user_name'] ?? 'User') ?>!</h2>
                    <p class="mb-0">Here's your financial overview for <?= date('F Y') ?></p>
                </div>

                <div class="summary-row">
                    <div class="summary-card">
                        <div class="summary-card-accent" style="background:#c6f8d5;"></div>
                        <div class="summary-card-header">
                            <div>
                                <p class="mb-1 text-muted small text-uppercase" style="font-weight: 600; letter-spacing: 0.5px;">Total Income</p>
                                <h4 style="color:#16a34a; font-weight: 700;">₹<?= formatAmount($totalIncome) ?></h4>
                            </div>
                            <div class="stat-icon income-icon">
                                <i class="bi bi-arrow-down-circle-fill"></i>
                            </div>
                        </div>
                    </div>
                    <div class="summary-card">
                        <div class="summary-card-accent" style="background:#f8d7da;"></div>
                        <div class="summary-card-header">
                            <div>
                                <p class="mb-1 text-muted small text-uppercase" style="font-weight: 600; letter-spacing: 0.5px;">Total Expenses</p>
                                <h4 style="color:#dc2626; font-weight: 700;">₹<?= formatAmount($totalExpense) ?></h4>
                            </div>
                            <div class="stat-icon expense-icon">
                                <i class="bi bi-arrow-up-circle-fill"></i>
                            </div>
                        </div>
                    </div>
                    <div class="summary-card">
                        <div class="summary-card-accent" style="background:#a8c6ff;"></div>
                        <div class="summary-card-header">
                            <div>
                                <p class="mb-1 text-muted small text-uppercase" style="font-weight: 600; letter-spacing: 0.5px;">Total Savings</p>
                                <h4 style="color:#2563eb; font-weight: 700;">₹<?= formatAmount($totalSavings) ?></h4>
                            </div>
                            <div class="stat-icon savings-icon">
                                <i class="bi bi-piggy-bank-fill"></i>
                            </div>
                        </div>
                    </div>
                    <div class="summary-card">
                        <div class="summary-card-accent" style="background:#f4ab6a;"></div>
                        <div class="summary-card-header">
                            <div>
                                <p class="mb-1 text-muted small text-uppercase" style="font-weight: 600; letter-spacing: 0.5px;">Balance</p>
                                <h4 style="color:#8b5cf6; font-weight: 700;">₹<?= formatAmount($balance) ?></h4>
                            </div>
                            <div class="stat-icon balance-icon">
                                <i class="bi bi-wallet2"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="dashboard-two-cols">
                    <div class="card-custom">
                        <div class="tab-header">
                            <h6 class="fw-bold">Monthly Details</h6>
                            <div class="tabs">
                                <span class="active" onclick="switchToIncome()">Income</span>
                                <span onclick="switchToExpense()">Expenses</span>
                            </div>
                        </div>
                        <div style="height: 300px;">
                            <canvas id="monthlyChart"></canvas>
                        </div>
                        <div id="monthlyInsights" class="chart-insight-box">
                            <small><i class="bi bi-lightbulb-fill"></i> Chart Insights</small>
                            <div id="monthlyInsightText" class="chart-insight-text"></div>
                        </div>
                    </div>

                    <div class="card-custom">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <h6 class="fw-bold">Expense Summary</h6>
                            <select id="monthSelect" class="form-select form-select-sm w-auto" onchange="updateDonutChart()">
                                <?php
                                $months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
                                for ($i = 1; $i <= 12; $i++) {
                                    $monthVal = str_pad($i, 2, '0', STR_PAD_LEFT);
                                    $disabled = ($i > intval($currentMonth)) ? 'disabled' : '';
                                    $selected = ($monthVal == $selectedMonth) ? 'selected' : '';
                                    echo "<option value='$monthVal' $selected $disabled>{$months[$i - 1]}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="donut-container">
                            <canvas id="expenseDonut"></canvas>
                            <div class="donut-center-text">
                                <small class="text-muted">Total</small>
                                <div class="fw-bold">₹<span id="donutTotal"><?= formatAmount($donutTotal) ?></span></div>
                            </div>
                        </div>
                        <div class="row mt-3" id="legendContainer">
                            <div class="col-6">
                                <div class="mb-2"><span class="legend-dot" style="background:#A7C7FF"></span>Shopping: <strong>₹<span class="legend-value"><?= formatAmount($donutData[0]) ?></span></strong></div>
                                <div class="mb-2"><span class="legend-dot" style="background:#C6E2FF"></span>Fun: <strong>₹<span class="legend-value"><?= formatAmount($donutData[1]) ?></span></strong></div>
                                <div class="mb-2"><span class="legend-dot" style="background:#F9D5E5"></span>Kids: <strong>₹<span class="legend-value"><?= formatAmount($donutData[2]) ?></span></strong></div>
                            </div>
                            <div class="col-6">
                                <div class="mb-2"><span class="legend-dot" style="background:#EAC8F2"></span>Vehicle: <strong>₹<span class="legend-value"><?= formatAmount($donutData[3]) ?></span></strong></div>
                                <div class="mb-2"><span class="legend-dot" style="background:#FDD9C1"></span>House: <strong>₹<span class="legend-value"><?= formatAmount($donutData[4]) ?></span></strong></div>
                                <div class="mb-2"><span class="legend-dot" style="background:#C6F8D5"></span>Insure: <strong>₹<span class="legend-value"><?= formatAmount($donutData[5]) ?></span></strong></div>
                            </div>
                        </div>
                        <div id="donutInsights" class="chart-insight-box" style="background: #fef3c7; border-left-color: #f59e0b;">
                            <small><i class="bi bi-pie-chart-fill"></i> Expense Breakdown Insights</small>
                            <div id="donutInsightText" class="chart-insight-text" style="color: #92400e;"></div>
                        </div>
                    </div>
                </div>

                <div class="container-fluid px-0 my-4">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body py-4 px-4">
                            <div class="row">
                                <div class="col-md-7 border-end">
                                    <h5 class="fw-semibold mb-4">Monthly Overview</h5>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <p class="text-muted mb-2">Expense Categories</p>
                                            <div id="categoryBreakdown">
                                                <?php if (count($categoryExpenses) > 0): ?>
                                                    <?php foreach ($categoryExpenses as $cat): ?>
                                                        <div class="d-flex justify-content-between mb-2">
                                                            <span><?= htmlspecialchars($cat['category']) ?></span>
                                                            <span><strong>₹<?= formatAmount($cat['amount']) ?></strong> <small class="text-muted">(<?= $cat['percentage'] ?>%)</small></span>
                                                        </div>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <p class="text-muted">No expenses this month</p>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="text-muted mb-2">Recent Transactions</p>
                                            <?php if (count($recentTransactions) > 0): ?>
                                                <?php foreach ($recentTransactions as $txn): ?>
                                                    <div class="d-flex justify-content-between mb-2 px-3 py-2 rounded-3 recent-txn-box" style="background:var(--input-bg);">
                                                        <span><?= htmlspecialchars($txn['category'] ?: $txn['description']) ?></span>
                                                        <span class="<?= $txn['type'] == 'income' ? 'text-success' : 'text-danger' ?> fw-semibold">
                                                            <?= $txn['type'] == 'income' ? '+' : '-' ?>₹<?= formatAmount($txn['amount']) ?>
                                                        </span>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <p class="text-muted">No recent transactions</p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-5 ps-md-4">
                                    <h5 class="fw-semibold mb-3"><i class="fa-solid fa-lightbulb text-warning me-2"></i>Bachat Insights</h5>
                                    <?php
                                    $insights = [];
                                    if ($totalExpense === 0 && $totalIncome === 0) {
                                        $insights[] = '📊 Start tracking your expenses to see personalized insights!';
                                    } else {
                                        if ($totalIncome > 0) {
                                            $savingsRate = (($totalIncome - $totalExpense) / $totalIncome * 100);
                                            if ($savingsRate > 20) {
                                                $insights[] = sprintf('🎉 Great job! You\'re saving %.1f%% of your income.', $savingsRate);
                                            } elseif ($savingsRate > 0) {
                                                $insights[] = sprintf('💡 You\'re saving %.1f%% - try to increase it to 20%%+.', $savingsRate);
                                            } else {
                                                $insights[] = '⚠️ Your expenses exceed your income. Review your spending.';
                                            }
                                        }
                                        $maxExpense = max($donutData);
                                        if ($maxExpense > 0 && $totalExpense > 0) {
                                            $maxIndex = array_search($maxExpense, $donutData);
                                            $categoryName = $categoryLabels[$maxIndex];
                                            $percentage = ($maxExpense / $totalExpense * 100);
                                            $insights[] = sprintf(
                                                '📈 %s is your highest expense category at %.1f%%.',
                                                $categoryName,
                                                $percentage
                                            );
                                        }
                                        if ($monthlyAvg > 0) {
                                            $insights[] = sprintf('📅 Your average monthly expense is ₹%s.', formatAmount($monthlyAvg));
                                        }
                                    }
                                    foreach ($insights as $insight) {
                                        echo '<div class="insight-pill">' . $insight . '</div>';
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php include 'components/footer.php'; ?>
            </div>
        </div>
    </div>

    <button id="bbChatToggle" class="btn btn-primary bb-chat-btn" aria-label="Open Bachat Buddy Chat">
        💬
    </button>

    <div id="bbChatBox" class="card bb-chat-box d-none">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>💛 Bachat Buddy</span>
            <button class="btn btn-sm" onclick="toggleChat()" aria-label="Close chat">✖</button>
        </div>

        <div class="card-body bb-chat-body" id="bbChatBody">
            <div class="bb-bot-msg">
                <strong>Buddy:</strong> Hey there! 😄 I'm Bachat Buddy, your friendly money helper. Ask me about your spending, savings, or if you should make a purchase!
            </div>
        </div>

        <div class="card-footer p-2">
            <div class="input-group">
                <input type="text" id="bbChatInput" class="form-control" placeholder="Type your message..." aria-label="Chat message">
                <button class="btn btn-success" onclick="sendMessage()">Send</button>
            </div>
        </div>
    </div>

    <div id="bbSoftPopup" class="alert bb-soft-popup d-none"></div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="components/js/chatbot.js"></script>

    <script>
        const incomeData = <?= json_encode($incomeData) ?>;
        const expenseData = <?= json_encode($expenseData) ?>;
        let donutData = <?= json_encode($donutData) ?>;
        const categoryLabels = <?= json_encode($categoryLabels) ?>;

        let monthlyChart = null;
        let expenseDonut = null;
        let currentChartType = 'income';

        function formatAmount(amount) {
            if (Math.floor(amount) === amount) {
                return amount.toLocaleString('en-IN', { maximumFractionDigits: 0 });
            }
            return amount.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        function generateMonthlyInsights() {
            const data = currentChartType === 'income' ? incomeData : expenseData;
            const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
            const currentMonthIndex = new Date().getMonth();

            let insights = [];

            const max = Math.max(...data);
            const maxIndex = data.indexOf(max);
            const nonZeroData = data.filter(v => v > 0);
            const min = nonZeroData.length > 0 ? Math.min(...nonZeroData) : 0;
            const minIndex = data.indexOf(min);

            if (max > 0) {
                insights.push(`📊 Highest ${currentChartType}: ₹${formatAmount(max)} in ${monthNames[maxIndex]}`);
            }

            if (currentMonthIndex > 0 && data[currentMonthIndex] > 0) {
                const current = data[currentMonthIndex];
                const previous = data[currentMonthIndex - 1];
                if (previous > 0) {
                    const change = ((current - previous) / previous * 100).toFixed(1);
                    const direction = current > previous ? 'increased' : 'decreased';
                    const emoji = currentChartType === 'income' ?
                        (current > previous ? '📈' : '📉') :
                        (current > previous ? '⚠️' : '✅');
                    insights.push(`${emoji} ${currentChartType.charAt(0).toUpperCase() + currentChartType.slice(1)} ${direction} by ${Math.abs(change)}% from last month`);
                }
            }

            const avg = nonZeroData.length > 0 ? nonZeroData.reduce((a, b) => a + b, 0) / nonZeroData.length : 0;
            if (avg > 0) {
                insights.push(`💰 Average monthly ${currentChartType}: ₹${formatAmount(avg)}`);
            }

            const ytdTotal = data.slice(0, currentMonthIndex + 1).reduce((a, b) => a + b, 0);
            if (ytdTotal > 0) {
                insights.push(`📅 Year-to-date total: ₹${formatAmount(ytdTotal)}`);
            }

            return insights.join(' • ');
        }

        function generateDonutInsights() {
            const total = donutData.reduce((a, b) => a + b, 0);
            if (total === 0) return 'No expenses recorded for this month';

            let insights = [];
            const maxExpense = Math.max(...donutData);
            const maxIndex = donutData.indexOf(maxExpense);
            const percentage = ((maxExpense / total) * 100).toFixed(1);

            insights.push(`📌 ${categoryLabels[maxIndex]} dominates at ${percentage}% (₹${formatAmount(maxExpense)})`);

            const activeCategories = donutData.filter(v => v > 0).length;
            insights.push(`${activeCategories} out of ${categoryLabels.length} categories active`);

            const sorted = [...donutData].sort((a, b) => b - a);
            if (sorted[1] > 0) {
                const secondIndex = donutData.indexOf(sorted[1]);
                const secondPercentage = ((sorted[1] / total) * 100).toFixed(1);
                insights.push(`${categoryLabels[secondIndex]} follows at ${secondPercentage}%`);
            }

            return insights.join(' • ');
        }

        function updateChartInsights() {
            document.getElementById('monthlyInsightText').innerHTML = generateMonthlyInsights();
            document.getElementById('donutInsightText').innerHTML = generateDonutInsights();
        }

        function toggleMenu() {
            document.getElementById("mobileSidebar").classList.toggle("active");
            document.getElementById("sidebarOverlay").classList.toggle("active");
            document.body.classList.toggle("sidebar-open");
        }

        function switchToIncome() {
            if (currentChartType === 'income') return;
            currentChartType = 'income';
            document.querySelectorAll('.tabs span').forEach(span => span.classList.remove('active'));
            document.querySelectorAll('.tabs span')[0].classList.add('active');
            if (monthlyChart) {
                monthlyChart.data.datasets[0].data = incomeData;
                monthlyChart.data.datasets[0].label = 'Income';
                monthlyChart.data.datasets[0].backgroundColor = '#a8c6ff';
                monthlyChart.update();
                updateChartInsights();
            }
        }

        function switchToExpense() {
            if (currentChartType === 'expense') return;
            currentChartType = 'expense';
            document.querySelectorAll('.tabs span').forEach(span => span.classList.remove('active'));
            document.querySelectorAll('.tabs span')[1].classList.add('active');
            if (monthlyChart) {
                monthlyChart.data.datasets[0].data = expenseData;
                monthlyChart.data.datasets[0].label = 'Expenses';
                monthlyChart.data.datasets[0].backgroundColor = '#f8d7da';
                monthlyChart.update();
                updateChartInsights();
            }
        }

        function updateDonutChart() {
            const selectedMonth = document.getElementById('monthSelect').value;

            fetch(`backend/transactions/fetch_transactions.php?action=month_expenses&month=${selectedMonth}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        console.error('Error:', data.error);
                        return;
                    }

                    donutData = data.donutData;
                    expenseDonut.data.datasets[0].data = donutData;
                    expenseDonut.update();

                    const total = donutData.reduce((a, b) => a + b, 0);
                    document.getElementById('donutTotal').textContent = formatAmount(total);

                    const legendValues = document.querySelectorAll('.legend-value');
                    donutData.forEach((val, idx) => {
                        if (legendValues[idx]) {
                            legendValues[idx].textContent = formatAmount(val);
                        }
                    });

                    if (data.categoryBreakdown && data.categoryBreakdown.length > 0) {
                        let html = '';
                        data.categoryBreakdown.forEach(cat => {
                            html += `
                                <div class="d-flex justify-content-between mb-2">
                                    <span>${cat.category}</span>
                                    <span><strong>₹${formatAmount(cat.amount)}</strong> 
                                    <small class="text-muted">(${cat.percentage}%)</small></span>
                                </div>
                            `;
                        });
                        document.getElementById('categoryBreakdown').innerHTML = html;
                    } else {
                        document.getElementById('categoryBreakdown').innerHTML = '<p class="text-muted">No expenses this month</p>';
                    }

                    updateChartInsights();
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                });
        }

        // Theme toggle
        const themeToggle = document.getElementById('theme-toggle');
        const setTheme = (theme) => {
            document.documentElement.setAttribute('data-theme', theme);
            themeToggle.innerHTML = theme === 'dark' 
                ? '<i class="fas fa-sun"></i>' 
                : '<i class="fas fa-moon"></i>';
            localStorage.setItem('theme', theme);
        };

        if (themeToggle) {
            themeToggle.addEventListener('click', () => {
                const currentTheme = document.documentElement.getAttribute('data-theme');
                setTheme(currentTheme === 'dark' ? 'light' : 'dark');
            });

            setTheme(localStorage.getItem('theme') || 'light');
        }

        // Logout
        document.addEventListener('DOMContentLoaded', function() {
            const logoutBtn = document.getElementById('logout-btn');
            if (logoutBtn) {
                logoutBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (confirm('Are you sure you want to logout?')) {
                        window.location.href = 'backend/auth/logout.php';
                    }
                });
            }
        });

        window.addEventListener('load', function() {
            try {
                const ctxMonthly = document.getElementById('monthlyChart').getContext('2d');
                monthlyChart = new Chart(ctxMonthly, {
                    type: 'bar',
                    data: {
                        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                        datasets: [{
                            label: 'Income',
                            data: incomeData,
                            backgroundColor: '#a8c6ff',
                            borderRadius: 8,
                            barThickness: 25
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: ctx => ctx.dataset.label + ': ₹' + formatAmount(ctx.parsed.y)
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: value => '₹' + formatAmount(value),
                                    color: document.documentElement.getAttribute('data-theme') === 'dark' ? '#e2e8f0' : '#666'
                                },
                                grid: {
                                    color: document.documentElement.getAttribute('data-theme') === 'dark' ? '#334155' : '#e5e7eb'
                                }
                            },
                            x: {
                                ticks: {
                                    color: document.documentElement.getAttribute('data-theme') === 'dark' ? '#e2e8f0' : '#666'
                                },
                                grid: {
                                    color: document.documentElement.getAttribute('data-theme') === 'dark' ? '#334155' : '#e5e7eb'
                                }
                            }
                        }
                    }
                });

                const ctxDonut = document.getElementById('expenseDonut').getContext('2d');
                expenseDonut = new Chart(ctxDonut, {
                    type: 'doughnut',
                    data: {
                        labels: categoryLabels,
                        datasets: [{
                            data: donutData,
                            backgroundColor: ['#A7C7FF', '#C6E2FF', '#F9D5E5', '#EAC8F2', '#FDD9C1', '#C6F8D5'],
                            borderWidth: 0,
                            cutout: '70%'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: ctx => ctx.label + ': ₹' + formatAmount(ctx.parsed)
                                }
                            }
                        }
                    }
                });

                updateChartInsights();
            } catch (error) {
                console.error('Chart error:', error);
            }
        });
    </script>
</body>

</html>