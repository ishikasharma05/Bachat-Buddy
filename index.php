<?php
// index.php - FULLY FIXED VERSION
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
    // 1. Total Income
    $stmt = $conn->prepare("SELECT COALESCE(SUM(amount), 0) as total FROM transactions WHERE user_id = ? AND type = 'income'");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $totalIncome = floatval($result->fetch_assoc()['total']);
    $stmt->close();

    // 2. Total Expense
    $stmt = $conn->prepare("SELECT COALESCE(SUM(amount), 0) as total FROM transactions WHERE user_id = ? AND type = 'expense'");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $totalExpense = floatval($result->fetch_assoc()['total']);
    $stmt->close();

    // 3. Total Savings
    $stmt = $conn->prepare("SELECT COALESCE(SUM(amount), 0) as total FROM transactions WHERE user_id = ? AND type = 'savings'");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $totalSavings = floatval($result->fetch_assoc()['total']);
    $stmt->close();

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
        $incomeData[$row['month'] - 1] = floatval($row['total']);
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
        $expenseData[$row['month'] - 1] = floatval($row['total']);
    }
    $stmt->close();

    // 6. Category Breakdown for Donut (Selected Month)
    // Map ALL possible categories to the 6 main categories
    $categoryMapping = [
        'Shopping' => 0,
        'Groceries' => 0,  // Map Groceries to Shopping
        'Clothing' => 0,   // Map Clothing to Shopping
        'Fun' => 1,
        'Entertainment' => 1,  // Map Entertainment to Fun
        'Kids' => 2,
        'Education' => 2,  // Map Education to Kids
        'Vehicle' => 3,
        'Transport' => 3,  // Map Transport to Vehicle
        'House' => 4,
        'Rent' => 4,       // Map Rent to House
        'Utilities' => 4,  // Map Utilities to House
        'Insure' => 5,
        'Insurance' => 5   // Map Insurance to Insure
    ];

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
            $donutData[$categoryMapping[$category]] += floatval($row['total']);
        }
    }
    $stmt->close();

    // 7. Category Breakdown with Percentages (Selected Month)
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

// Calculate average monthly expense (only for months with expenses)
$monthsWithExpenses = count(array_filter($expenseData, function ($val) {
    return $val > 0;
}));
$monthlyAvg = $monthsWithExpenses > 0 ? $totalExpense / $monthsWithExpenses : 0;
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
    <link rel="stylesheet" href="components/style.css">
    <style>
        /* Remove custom scrollbar - use default */
        * {
            scrollbar-width: auto;
            scrollbar-color: auto;
        }

        ::-webkit-scrollbar {
            width: auto;
            height: auto;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: auto;
        }

        /* Hide hamburger menu button on desktop */
        @media (min-width: 992px) {
            .header .menu-btn,
            .header button.btn.d-lg-none {
                display: none !important;
            }
        }

        /* Make all header buttons circular and consistent */
        .header button,
        .header .notification,
        #theme-toggle {
            width: 40px !important;
            height: 40px !important;
            border-radius: 50% !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            border: none !important;
            background-color: #f3f4f6 !important;
            color: #374151 !important;
            cursor: pointer !important;
            transition: background-color 0.2s ease !important;
            padding: 0 !important;
            position: relative !important;
        }

        .header button:hover,
        .header .notification:hover,
        #theme-toggle:hover {
            background-color: #e5e7eb !important;
        }

        .header button i,
        .header .notification i,
        #theme-toggle i {
            font-size: 18px !important;
        }

        /* Fix notification badge position - MUST be on the bell icon button */
        #notificationBtn {
            position: relative !important;
        }

        .notification-badge,
        #notificationBadge,
        #notificationBtn .notification-badge,
        #notificationBtn span {
            position: absolute !important;
            top: -2px !important;
            right: -2px !important;
            background: #ef4444 !important;
            color: white !important;
            border-radius: 50% !important;
            width: 18px !important;
            height: 18px !important;
            font-size: 11px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-weight: 600 !important;
            z-index: 10 !important;
            line-height: 1 !important;
        }

        /* Force notification dropdown to be hidden by default */
        #notificationDropdown.hidden {
            display: none !important;
        }

        #notificationDropdown:not(.hidden) {
            display: block !important;
        }

        /* Dark Theme Styles - Match Profile Page */
        [data-theme="dark"] {
            background-color: #0f172a;
            color: #e2e8f0;
        }

        [data-theme="dark"] body {
            background-color: #0f172a;
            color: #e2e8f0;
        }

        [data-theme="dark"] .layout {
            background-color: #0f172a;
        }

        [data-theme="dark"] .main-body {
            background-color: #0f172a !important;
        }

        /* Sidebar - Always visible in dark mode */
        [data-theme="dark"] .sidebar {
            background-color: #1e293b !important;
            color: #e2e8f0 !important;
        }

        [data-theme="dark"] .sidebar .brand {
            color: #e2e8f0 !important;
        }

        [data-theme="dark"] .sidebar .nav-link {
            color: #94a3b8 !important;
        }

        [data-theme="dark"] .sidebar .nav-link:hover {
            background-color: #334155 !important;
            color: #e2e8f0 !important;
        }

        [data-theme="dark"] .sidebar .nav-link[style*="background"] {
            background: #3b82f6 !important;
            color: #fff !important;
        }

        /* Header in dark mode */
        [data-theme="dark"] .header {
            background-color: #1e293b !important;
            border-bottom-color: #334155 !important;
        }

        [data-theme="dark"] .header h5 {
            color: #e2e8f0 !important;
        }

        [data-theme="dark"] .header button,
        [data-theme="dark"] .header .notification,
        [data-theme="dark"] #theme-toggle {
            background-color: #334155 !important;
            color: #e2e8f0 !important;
        }

        [data-theme="dark"] .header button:hover,
        [data-theme="dark"] .header .notification:hover,
        [data-theme="dark"] #theme-toggle:hover {
            background-color: #475569 !important;
        }

        /* Cards in dark mode */
        [data-theme="dark"] .summary-card {
            background-color: #1e293b !important;
            color: #e2e8f0 !important;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3) !important;
        }

        [data-theme="dark"] .card-custom {
            background-color: #1e293b !important;
            color: #e2e8f0 !important;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3) !important;
        }

        [data-theme="dark"] .card {
            background-color: #1e293b !important;
            color: #e2e8f0 !important;
        }

        [data-theme="dark"] .card-body {
            background-color: #1e293b !important;
        }

        /* Text colors in dark mode */
        [data-theme="dark"] .text-muted {
            color: #94a3b8 !important;
        }

        [data-theme="dark"] h4,
        [data-theme="dark"] h5,
        [data-theme="dark"] h6 {
            color: #e2e8f0 !important;
        }

        /* Notification dropdown in dark mode */
        [data-theme="dark"] #notificationDropdown {
            background-color: #1e293b !important;
            border-color: #334155 !important;
            color: #e2e8f0 !important;
        }

        [data-theme="dark"] #notificationDropdown .p-3 {
            background-color: #1e293b !important;
            border-bottom-color: #334155 !important;
        }

        [data-theme="dark"] .notification-item,
        [data-theme="dark"] #notificationDropdown .p-3.border-b {
            border-bottom-color: #334155 !important;
            background-color: #1e293b !important;
        }

        [data-theme="dark"] #notificationDropdown .hover\:bg-gray-50:hover {
            background-color: #334155 !important;
        }

        /* Insight pills - YELLOW background like profile page */
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

        /* Form elements in dark mode */
        [data-theme="dark"] input,
        [data-theme="dark"] select,
        [data-theme="dark"] .form-select {
            background-color: #334155 !important;
            border-color: #475569 !important;
            color: #e2e8f0 !important;
        }

        [data-theme="dark"] .form-select option {
            background-color: #1e293b !important;
        }

        /* Tab styling in dark mode */
        [data-theme="dark"] .tabs span {
            color: #64748b !important;
        }

        [data-theme="dark"] .tabs span.active {
            color: #e2e8f0 !important;
        }

        .main-body {
            padding: 2rem;
            overflow-y: auto;
            background-color: #f2f6f9;
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
            background: #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.04);
            padding: 1.25rem 1.5rem;
            transition: transform 0.2s;
        }

        .summary-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        .summary-card-accent {
            position: absolute;
            top: 0;
            left: 0;
            width: 6px;
            height: 100%;
            border-radius: 16px 0 0 16px;
        }

        .dashboard-two-cols {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 2rem;
        }

        .card-custom {
            border-radius: 16px;
            padding: 1.5rem;
            background: #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.04);
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
            color: #999;
            transition: color 0.3s;
        }

        .tabs .active {
            color: #000;
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

        .insight-pill {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 10px;
            font-size: 0.9rem;
        }

        @media (max-width: 768px) {
            .summary-row {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .dashboard-two-cols {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <div class="layout">
        <div class="sidebar d-none d-lg-block">
            <div>
                <div class="brand d-flex align-items-center mb-4">
                    <i class="bi bi-piggy-bank me-2 text-success"></i> Bachat-Buddy
                </div>
                <ul class="nav flex-column gap-2">
                    <li><a class="nav-link" href="index.php" style="background: #3b82f6; color: #fff;"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
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
                    <span><i class="bi bi-piggy-bank me-2 text-success"></i> Bachat-Buddy</span>
                    <button onclick="toggleMenu()" class="btn-close"></button>
                </div>
                <ul class="nav flex-column gap-2">
                    <li><a class="nav-link" href="index.php" style="background: #3b82f6; color: #fff;"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
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
                <div class="summary-row">
                    <div class="summary-card">
                        <div class="summary-card-accent" style="background:#c6f8d5;"></div>
                        <p class="mb-1 text-muted">Income</p>
                        <h4 style="color:#16a34a;">₹<?= number_format($totalIncome, 2) ?></h4>
                    </div>
                    <div class="summary-card">
                        <div class="summary-card-accent" style="background:#f8d7da;"></div>
                        <p class="mb-1 text-muted">Expenses</p>
                        <h4 style="color:#dc2626;">₹<?= number_format($totalExpense, 2) ?></h4>
                    </div>
                    <div class="summary-card">
                        <div class="summary-card-accent" style="background:#a8c6ff;"></div>
                        <p class="mb-1 text-muted">Savings</p>
                        <h4 style="color:#2563eb;">₹<?= number_format($totalSavings, 2) ?></h4>
                    </div>
                    <div class="summary-card">
                        <div class="summary-card-accent" style="background:#f4ab6a;"></div>
                        <p class="mb-1 text-muted">Balance</p>
                        <h4 style="color:#2563eb;">₹<?= number_format($balance, 2) ?></h4>
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
                    </div>

                    <div class="card-custom">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <h6 class="fw-bold">Expense Summary</h6>
                            <select id="monthSelect" class="form-select form-select-sm w-auto" onchange="updateDonutChart()">
                                <?php
                                $months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
                                for ($i = 1; $i <= 12; $i++) {
                                    $monthVal = str_pad($i, 2, '0', STR_PAD_LEFT);
                                    $selected = ($monthVal == $selectedMonth) ? 'selected' : '';
                                    echo "<option value='$monthVal' $selected>{$months[$i - 1]}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="donut-container">
                            <canvas id="expenseDonut"></canvas>
                            <div class="donut-center-text">
                                <small class="text-muted">Total</small>
                                <div class="fw-bold">₹<span id="donutTotal"><?= number_format($donutTotal, 0) ?></span></div>
                            </div>
                        </div>
                        <div class="row mt-3" id="legendContainer">
                            <div class="col-6">
                                <div class="mb-2"><span class="legend-dot" style="background:#A7C7FF"></span>Shopping: <strong>₹<span class="legend-value"><?= number_format($donutData[0], 0) ?></span></strong></div>
                                <div class="mb-2"><span class="legend-dot" style="background:#C6E2FF"></span>Fun: <strong>₹<span class="legend-value"><?= number_format($donutData[1], 0) ?></span></strong></div>
                                <div class="mb-2"><span class="legend-dot" style="background:#F9D5E5"></span>Kids: <strong>₹<span class="legend-value"><?= number_format($donutData[2], 0) ?></span></strong></div>
                            </div>
                            <div class="col-6">
                                <div class="mb-2"><span class="legend-dot" style="background:#EAC8F2"></span>Vehicle: <strong>₹<span class="legend-value"><?= number_format($donutData[3], 0) ?></span></strong></div>
                                <div class="mb-2"><span class="legend-dot" style="background:#FDD9C1"></span>House: <strong>₹<span class="legend-value"><?= number_format($donutData[4], 0) ?></span></strong></div>
                                <div class="mb-2"><span class="legend-dot" style="background:#C6F8D5"></span>Insure: <strong>₹<span class="legend-value"><?= number_format($donutData[5], 0) ?></span></strong></div>
                            </div>
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
                                                            <span><strong>₹<?= number_format($cat['amount'], 0) ?></strong> <small class="text-muted">(<?= $cat['percentage'] ?>%)</small></span>
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
                                                    <div class="d-flex justify-content-between mb-2 px-3 py-2 rounded-3" style="background:#f8fafc;">
                                                        <span><?= htmlspecialchars($txn['category'] ?: $txn['description']) ?></span>
                                                        <span class="<?= $txn['type'] == 'income' ? 'text-success' : 'text-danger' ?> fw-semibold">
                                                            <?= $txn['type'] == 'income' ? '+' : '-' ?>₹<?= number_format($txn['amount'], 2) ?>
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
                                            $insights[] = sprintf('📅 Your average monthly expense is ₹%s.', number_format($monthlyAvg, 0));
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

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <script>
        console.log('🔄 Initializing dashboard...');

        const incomeData = <?= json_encode($incomeData) ?>;
        const expenseData = <?= json_encode($expenseData) ?>;
        let donutData = <?= json_encode($donutData) ?>;
        const categoryLabels = <?= json_encode($categoryLabels) ?>;

        console.log('📊 Data loaded:', {
            incomeData,
            expenseData,
            donutData,
            categoryLabels
        });

        let monthlyChart = null;
        let expenseDonut = null;
        let currentChartType = 'income';

        function toggleMenu() {
            document.getElementById("mobileSidebar").classList.toggle("active");
            document.getElementById("sidebarOverlay").classList.toggle("active");
            document.body.classList.toggle("sidebar-open");
        }

        // Theme Toggle Functionality - WORKS WITH ANY BUTTON STRUCTURE
        function initThemeToggle() {
            console.log('🎨 Initializing theme toggle...');
            
            // Try multiple selectors to find the theme button
            let themeToggle = document.getElementById('theme-toggle') 
                           || document.querySelector('[id*="theme"]')
                           || document.querySelector('button .fa-moon')?.parentElement
                           || document.querySelector('button .bi-moon')?.parentElement
                           || document.querySelector('button .bi-moon-fill')?.parentElement;
            
            console.log('Theme toggle element:', themeToggle);
            
            const htmlElement = document.documentElement;
            const bodyElement = document.body;
            
            if (!themeToggle) {
                console.error('❌ Theme toggle button not found! Trying all buttons...');
                const allButtons = document.querySelectorAll('button');
                console.log('All buttons found:', allButtons.length);
                allButtons.forEach((btn, idx) => {
                    console.log(`Button ${idx}:`, btn, 'ID:', btn.id, 'Classes:', btn.className);
                });
                return;
            }
            
            const themeIcon = themeToggle.querySelector('i');
            console.log('Theme icon:', themeIcon);
            
            // Load saved theme from localStorage
            const savedTheme = localStorage.getItem('theme') || 'light';
            console.log('Saved theme:', savedTheme);
            htmlElement.setAttribute('data-theme', savedTheme);
            if (bodyElement) {
                bodyElement.setAttribute('data-theme', savedTheme);
            }
            
            // Update icon based on current theme
            if (themeIcon) {
                if (savedTheme === 'dark') {
                    themeIcon.className = themeIcon.className.includes('fa-') ? 'fas fa-sun' : 'bi bi-sun-fill';
                } else {
                    themeIcon.className = themeIcon.className.includes('fa-') ? 'fas fa-moon' : 'bi bi-moon-fill';
                }
            }
            
            // Add click event
            themeToggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                console.log('🖱️ Theme toggle clicked!');
                
                const currentTheme = htmlElement.getAttribute('data-theme') || 'light';
                const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                
                console.log('Current theme:', currentTheme, '→ New theme:', newTheme);
                
                htmlElement.setAttribute('data-theme', newTheme);
                if (bodyElement) {
                    bodyElement.setAttribute('data-theme', newTheme);
                }
                localStorage.setItem('theme', newTheme);
                
                // Toggle icon
                if (themeIcon) {
                    if (newTheme === 'dark') {
                        themeIcon.className = themeIcon.className.includes('fa-') ? 'fas fa-sun' : 'bi bi-sun-fill';
                    } else {
                        themeIcon.className = themeIcon.className.includes('fa-') ? 'fas fa-moon' : 'bi bi-moon-fill';
                    }
                }
                
                console.log('✅ Theme toggled to:', newTheme);
            });
            
            console.log('✅ Theme toggle initialized successfully');
        }

        // Notification Functionality - FIXED FOR HEADER.PHP
        function initNotifications() {
            console.log('🔔 Initializing notifications...');
            
            const notificationBtn = document.getElementById("notificationBtn");
            const notificationDropdown = document.getElementById("notificationDropdown");
            const notificationBadge = document.getElementById("notificationBadge");

            console.log('Notification button:', notificationBtn);
            console.log('Notification dropdown:', notificationDropdown);
            console.log('Notification badge:', notificationBadge);

            if (!notificationBtn || !notificationDropdown) {
                console.error('❌ Notification elements not found!');
                console.log('Looking for: #notificationBtn and #notificationDropdown');
                return;
            }

            // Force hide dropdown on page load
            notificationDropdown.classList.add("hidden");
            notificationDropdown.style.display = "none";

            // Toggle dropdown
            notificationBtn.addEventListener("click", function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                console.log('🖱️ Notification button clicked!');
                
                const isHidden = notificationDropdown.classList.contains('hidden');
                
                if (isHidden) {
                    notificationDropdown.classList.remove("hidden");
                    notificationDropdown.style.display = "block";
                    console.log('Dropdown is now: visible');
                } else {
                    notificationDropdown.classList.add("hidden");
                    notificationDropdown.style.display = "none";
                    console.log('Dropdown is now: hidden');
                }
            });

            // Close dropdown when clicking outside
            document.addEventListener("click", function(e) {
                if (!notificationDropdown.classList.contains("hidden")) {
                    if (!notificationDropdown.contains(e.target) && e.target !== notificationBtn && !notificationBtn.contains(e.target)) {
                        notificationDropdown.classList.add("hidden");
                        notificationDropdown.style.display = "none";
                        console.log('🔔 Notification dropdown closed (clicked outside)');
                    }
                }
            });

            // Prevent dropdown from closing when clicking inside
            notificationDropdown.addEventListener("click", function(e) {
                e.stopPropagation();
            });
            
            console.log('✅ Notifications initialized successfully');
        }

        // Clear notifications
        function clearNotifications() {
            const notificationList = document.getElementById("notificationList");
            const notificationBadge = document.getElementById("notificationBadge");
            
            if (notificationList) {
                notificationList.innerHTML = `
                    <div class="p-3 text-center text-gray-500">
                        No notifications
                    </div>
                `;
            }
            
            if (notificationBadge) {
                notificationBadge.style.display = "none";
            }
        }

        // Logout button functionality
        function initLogout() {
            const logoutBtn = document.getElementById('logout-btn');
            if (logoutBtn) {
                logoutBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (confirm('Are you sure you want to logout?')) {
                        window.location.href = 'backend/auth/logout.php';
                    }
                });
            }
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

                    // Update donut chart
                    donutData = data.donutData;
                    expenseDonut.data.datasets[0].data = donutData;
                    expenseDonut.update();

                    // Update total
                    const total = donutData.reduce((a, b) => a + b, 0);
                    document.getElementById('donutTotal').textContent = total.toLocaleString('en-IN', {
                        maximumFractionDigits: 0
                    });

                    // Update legend values
                    const legendValues = document.querySelectorAll('.legend-value');
                    donutData.forEach((val, idx) => {
                        if (legendValues[idx]) {
                            legendValues[idx].textContent = val.toLocaleString('en-IN', {
                                maximumFractionDigits: 0
                            });
                        }
                    });

                    // Update category breakdown
                    if (data.categoryBreakdown && data.categoryBreakdown.length > 0) {
                        let html = '';
                        data.categoryBreakdown.forEach(cat => {
                            html += `
                                <div class="d-flex justify-content-between mb-2">
                                    <span>${cat.category}</span>
                                    <span><strong>₹${cat.amount.toLocaleString('en-IN', {maximumFractionDigits: 0})}</strong> 
                                    <small class="text-muted">(${cat.percentage}%)</small></span>
                                </div>
                            `;
                        });
                        document.getElementById('categoryBreakdown').innerHTML = html;
                    } else {
                        document.getElementById('categoryBreakdown').innerHTML = '<p class="text-muted">No expenses this month</p>';
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                });
        }

        // Initialize immediately when DOM is ready
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🔍 DOM Content Loaded - Initializing...');
            
            // Initialize theme toggle, notifications, and logout first
            setTimeout(function() {
                initThemeToggle();
                initNotifications();
                initLogout();
            }, 100);
        });

        window.addEventListener('load', function() {
            console.log('🎨 Creating charts...');

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
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: ctx => ctx.dataset.label + ': ₹' + ctx.parsed.y.toLocaleString('en-IN')
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: value => '₹' + value.toLocaleString('en-IN')
                                }
                            }
                        }
                    }
                });
                console.log('✅ Monthly chart created');

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
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: ctx => ctx.label + ': ₹' + ctx.parsed.toLocaleString('en-IN')
                                }
                            }
                        }
                    }
                });
                console.log('✅ Donut chart created');
                
                console.log('🎉 Dashboard fully loaded!');
            } catch (error) {
                console.error('❌ Chart error:', error);
            }
        });
    </script>
</body>

</html>