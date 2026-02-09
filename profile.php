<?php
require_once 'components/auth_check.php';
require_once 'config/db.php';

// Handle logout action
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    header("Location: login-pages/login.php");
    exit();
}

// First, check and add missing columns if needed
try {
    $checkProfileImage = $conn->query("SHOW COLUMNS FROM users LIKE 'profile_image'");
    if ($checkProfileImage->num_rows == 0) {
        $conn->query("ALTER TABLE users ADD COLUMN profile_image VARCHAR(255) DEFAULT 'uploads/default.png' AFTER password");
    }

    $checkBudget = $conn->query("SHOW COLUMNS FROM users LIKE 'monthly_budget'");
    if ($checkBudget->num_rows == 0) {
        $conn->query("ALTER TABLE users ADD COLUMN monthly_budget DECIMAL(10,2) DEFAULT 0.00 AFTER mobile");
    }
} catch (Exception $e) {
    // Silently handle if columns already exist
}

// Fetch user data from database
$stmt = $conn->prepare("SELECT id, full_name, email, mobile, profile_image, monthly_budget, created_at FROM users WHERE id = ?");
if (!$stmt) {
    die("Database error: " . $conn->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Set defaults if fields are null
$user['mobile'] = $user['mobile'] ?? 'Not set';
$user['monthly_budget'] = $user['monthly_budget'] ?? '0';
$user['profile_image'] = $user['profile_image'] ?? 'uploads/default.png';

// Calculate total expenses and savings progress
$totalExpenses = 0;
$totalSavings = 0;
$savingsGoal = 0;
$monthlyExpenses = 0;

// Get total expenses
$stmt = $conn->prepare("SELECT COALESCE(SUM(amount), 0) as total FROM transactions WHERE user_id = ? AND type = 'expense'");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$totalExpenses = $result->fetch_assoc()['total'];
$stmt->close();

// Get current month expenses for budget tracking
$stmt = $conn->prepare("SELECT COALESCE(SUM(amount), 0) as total FROM transactions WHERE user_id = ? AND type = 'expense' AND MONTH(created_at) = MONTH(CURRENT_DATE()) AND YEAR(created_at) = YEAR(CURRENT_DATE())");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$monthlyExpenses = $result->fetch_assoc()['total'];
$stmt->close();

// Get total savings
$stmt = $conn->prepare("SELECT COALESCE(SUM(amount), 0) as total FROM transactions WHERE user_id = ? AND type = 'savings'");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$totalSavings = $result->fetch_assoc()['total'];
$stmt->close();

// Get savings goal (sum of all active goals)
$stmt = $conn->prepare("SELECT COALESCE(SUM(target_amount), 0) as total FROM goals WHERE user_id = ? AND status = 'active'");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$savingsGoal = $result->fetch_assoc()['total'];
$stmt->close();

// Calculate savings percentage
$savingsProgress = $savingsGoal > 0 ? min(100, ($totalSavings / $savingsGoal) * 100) : 0;

// Calculate budget usage percentage
$budgetUsage = $user['monthly_budget'] > 0 ? min(100, ($monthlyExpenses / $user['monthly_budget']) * 100) : 0;

// Calculate member status
$accountAge = strtotime('now') - strtotime($user['created_at']);
$daysActive = floor($accountAge / (60 * 60 * 24));

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bachat-Buddy | Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="components/style.css">
    <style>
        /* ===== GLOBAL DESIGN SYSTEM (Matching Transaction Page) ===== */
        * {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }

        :root {
            /* Primary Colors */
            --color-primary-500: #00bcd4;
            --color-primary-600: #00acc1;
            --color-primary-700: #0097a7;
            
            /* Success Colors */
            --color-success-500: #4caf50;
            --color-success-600: #43a047;
            
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
            --table-row-hover: #f7fafc;
            
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
            --table-row-hover: #334155;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.3);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.4), 0 2px 4px -1px rgba(0, 0, 0, 0.3);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.5), 0 4px 6px -2px rgba(0, 0, 0, 0.4);
        }

        body {
            margin: 0;
            background-color: var(--bg-main);
            color: var(--text-primary);
            transition: background-color 0.3s ease, color 0.3s ease;
            font-size: 15px;
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
        }

        .layout {
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        /* ===== SIDEBAR (Matching Transaction Page) ===== */
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

        .sidebar .nav-link.active,
        .sidebar .nav-link[style*="background"] {
            background: linear-gradient(135deg, var(--color-primary-500), var(--color-primary-600)) !important;
            color: white !important;
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

        /* ===== HEADER (Matching Transaction Page) ===== */
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

        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            height: 100vh;
        }

        .main-body {
            padding: 2rem;
            overflow-y: auto;
            background-color: var(--bg-main);
            transition: all 0.3s ease;
        }

        /* ===== PROFILE SPECIFIC STYLING ===== */
        .cover {
            background: linear-gradient(135deg, #a8edea, #fed6e3);
            height: 220px;
            border-radius: 24px;
            position: relative;
            transition: all 0.5s ease;
            overflow: hidden;
        }

        [data-theme="dark"] .cover {
            background: linear-gradient(135deg, #1e3a8a, #4c1d95);
        }

        .cover::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="grid" width="20" height="20" patternUnits="userSpaceOnUse"><path d="M 20 0 L 0 0 0 20" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="1"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
            opacity: 0.3;
        }

        .profile-card {
            max-width: 850px;
            margin: -80px auto 50px;
            background: var(--bg-card);
            border-radius: 24px;
            box-shadow: var(--shadow-lg);
            padding: 40px;
            position: relative;
            z-index: 2;
            transition: all 0.4s ease;
            animation: fadeIn 0.6s ease-out;
            border: 1px solid var(--border-light);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .profile-pic-wrapper {
            position: relative;
            display: inline-block;
            margin-top: -110px;
        }

        .profile-pic {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
            border: 6px solid var(--bg-card);
            background: #eee;
            transition: all 0.3s ease;
            box-shadow: var(--shadow-md);
            cursor: pointer;
        }

        .profile-pic:hover {
            transform: scale(1.05);
            box-shadow: var(--shadow-lg);
        }

        .status-indicator {
            position: absolute;
            bottom: 10px;
            right: 10px;
            width: 20px;
            height: 20px;
            background: #10b981;
            border: 4px solid var(--bg-card);
            border-radius: 50%;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% {
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
            }
            50% {
                box-shadow: 0 0 0 6px rgba(16, 185, 129, 0);
            }
        }

        .user-meta {
            font-size: 0.875rem;
            color: var(--text-muted);
            margin-top: 0.5rem;
            margin-bottom: 1.5rem;
        }

        /* Statistics Grid */
        .stats-container {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
            margin: 2rem 0;
        }

        .stat-box {
            background: var(--input-bg);
            border-radius: 18px;
            padding: 20px;
            text-align: center;
            box-shadow: var(--shadow-sm);
            transition: all 0.3s ease;
            border: 2px solid transparent;
            position: relative;
            overflow: hidden;
        }

        .stat-box::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #3b82f6, #8b5cf6);
        }

        .stat-box:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-md);
            border-color: var(--color-primary-500);
        }

        .stat-icon {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 0.75rem;
            font-size: 1.125rem;
        }

        .stat-box.expense .stat-icon {
            background: #fee2e2;
            color: #ef4444;
        }

        .stat-box.savings .stat-icon {
            background: #d1fae5;
            color: #10b981;
        }

        .stat-box.budget .stat-icon {
            background: #fef3c7;
            color: #f59e0b;
        }

        [data-theme="dark"] .stat-box.expense .stat-icon {
            background: #450a0a;
            color: #fca5a5;
        }

        [data-theme="dark"] .stat-box.savings .stat-icon {
            background: #14532d;
            color: #86efac;
        }

        [data-theme="dark"] .stat-box.budget .stat-icon {
            background: #451a03;
            color: #fde68a;
        }

        .stat-value {
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0.5rem 0;
            color: var(--text-primary);
        }

        .stat-label {
            font-size: 0.75rem;
            color: var(--text-muted);
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        /* Info Boxes */
        .info-box {
            background: var(--input-bg);
            border-radius: 15px;
            padding: 15px 20px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
            border: 1px solid var(--border-light);
        }

        .info-box:hover {
            border-color: var(--color-primary-500);
            transform: translateX(5px);
            box-shadow: var(--shadow-sm);
        }

        .info-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            font-size: 1.125rem;
            flex-shrink: 0;
        }

        .info-icon.email {
            background: #dbeafe;
            color: #3b82f6;
        }

        .info-icon.phone {
            background: #d1fae5;
            color: #10b981;
        }

        .info-icon.budget {
            background: #fef3c7;
            color: #f59e0b;
        }

        [data-theme="dark"] .info-icon.email {
            background: #1e3a8a;
            color: #93c5fd;
        }

        [data-theme="dark"] .info-icon.phone {
            background: #14532d;
            color: #86efac;
        }

        [data-theme="dark"] .info-icon.budget {
            background: #451a03;
            color: #fde68a;
        }

        .info-label {
            font-size: 0.75rem;
            color: var(--text-muted);
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            display: block;
            margin-bottom: 2px;
        }

        .info-value {
            font-size: 0.95rem;
            color: var(--text-primary);
            font-weight: 600;
        }

        /* Progress Bar */
        .progress-custom {
            height: 8px;
            background-color: var(--border-light);
            border-radius: 10px;
            overflow: hidden;
            margin-top: 10px;
            position: relative;
        }

        .progress-bar-animated {
            height: 100%;
            border-radius: 10px;
            transition: width 1s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .progress-bar-animated::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            right: 0;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            animation: shimmer 2s infinite;
        }

        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }

        .progress-text {
            font-size: 0.7rem;
            color: var(--text-muted);
            margin-top: 0.5rem;
        }

        /* Buttons (Matching Transaction Page) */
        .btn-modern {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.625rem 1.25rem;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.9375rem;
            border: 2px solid transparent;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            text-decoration: none;
        }

        .btn-primary-modern {
            background: linear-gradient(135deg, var(--color-primary-500), var(--color-primary-600));
            color: white;
            border-color: var(--color-primary-500);
        }

        .btn-primary-modern:hover {
            background: linear-gradient(135deg, var(--color-primary-600), var(--color-primary-700));
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(0, 188, 212, 0.3);
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
            box-shadow: var(--shadow-lg);
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

        /* Modal Styling */
        .modal-content {
            background: var(--bg-card);
            color: var(--text-primary);
            border: 1px solid var(--border-light);
            border-radius: 16px;
        }

        .form-control,
        .form-select {
            background-color: var(--input-bg) !important;
            border: 2px solid var(--border-light) !important;
            color: var(--text-primary) !important;
            border-radius: 10px;
            transition: all 0.2s ease;
        }

        .form-control:focus,
        .form-select:focus {
            background-color: var(--bg-card) !important;
            border-color: var(--color-primary-500) !important;
            box-shadow: 0 0 0 3px rgba(0, 188, 212, 0.1) !important;
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .layout {
                flex-direction: column;
                height: auto;
            }

            .sidebar.d-lg-block {
                display: none !important;
            }

            .main-content {
                height: auto;
                width: 100%;
            }

            .main-body {
                padding: 1.5rem;
            }

            .profile-card {
                max-width: 100%;
                margin: -60px 1rem 40px;
                padding: 30px;
            }

            .cover {
                height: 200px;
            }

            .profile-pic {
                width: 130px;
                height: 130px;
                margin-top: -90px;
            }

            .stats-container {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 576px) {
            .main-body {
                padding: 1rem;
            }

            .cover {
                height: 160px;
                border-radius: 18px;
            }

            .profile-card {
                margin: -50px 0.75rem 30px;
                padding: 20px;
                border-radius: 20px;
            }

            .profile-pic {
                width: 110px;
                height: 110px;
                margin-top: -75px;
            }

            .stats-container {
                grid-template-columns: 1fr;
                gap: 0.75rem;
            }

            .info-box {
                flex-direction: column;
                align-items: flex-start;
                gap: 6px;
                padding: 14px;
            }

            .info-icon {
                margin-right: 0;
                margin-bottom: 0.5rem;
            }
        }

        /* Smooth scroll behavior */
        html {
            scroll-behavior: smooth;
        }

        /* Custom scrollbar */
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
    </style>
</head>

<body>
    <div class="layout">
        <!-- Desktop Sidebar -->
        <div class="sidebar d-none d-lg-block">
            <div>
                <div class="brand">
                    <i class="bi bi-piggy-bank-fill"></i>
                    <span>Bachat Buddy</span>
                </div>
                <ul class="nav flex-column">
                    <li><a class="nav-link" href="index.php"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
                    <li><a class="nav-link active" href="profile.php"><i class="bi bi-person-circle"></i> Profile</a></li>
                    <li><a class="nav-link" href="transaction.php"><i class="bi bi-arrow-left-right"></i> Transactions</a></li>
                    <li><a class="nav-link" href="add-entry.php"><i class="bi bi-journal-plus"></i> Add Entry</a></li>
                    <li><a class="nav-link" href="goals.php"><i class="bi bi-bullseye"></i> Goals</a></li>
                </ul>
            </div>
        </div>

        <!-- Mobile Sidebar -->
        <div id="mobileSidebar" class="mobile-sidebar d-lg-none">
            <div class="p-4">
                <div class="brand d-flex align-items-center justify-content-between mb-4">
                    <span><i class="bi bi-piggy-bank-fill text-success"></i> Bachat Buddy</span>
                    <button onclick="toggleMenu()" class="btn-close border-0 bg-transparent text-muted fs-4" aria-label="Close menu">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                <ul class="nav flex-column">
                    <li><a class="nav-link" href="index.php"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
                    <li><a class="nav-link active" href="profile.php"><i class="bi bi-person-circle"></i> Profile</a></li>
                    <li><a class="nav-link" href="transaction.php"><i class="bi bi-arrow-left-right"></i> Transactions</a></li>
                    <li><a class="nav-link" href="add-entry.php"><i class="bi bi-journal-plus"></i> Add Entry</a></li>
                    <li><a class="nav-link" href="goals.php"><i class="bi bi-bullseye"></i> Goals</a></li>
                </ul>
            </div>
        </div>
        <div id="sidebarOverlay" class="sidebar-overlay d-lg-none" onclick="toggleMenu()"></div>

        <!-- Main Content -->
        <div class="main-content">
            <?php include 'components/header.php'; ?>
            <div class="main-body">
                <div class="cover"></div>

                <div class="profile-card text-center">
                    <!-- Profile Picture with Active Status -->
                    <div class="mb-3">
                        <div class="profile-pic-wrapper">
                            <img
                                id="profileImg"
                                src="<?= htmlspecialchars($user['profile_image']) ?>"
                                class="profile-pic"
                                alt="Profile Picture"
                                onclick="document.getElementById('uploadImg').click()">
                            <div class="status-indicator" title="Active"></div>
                        </div>

                        <input
                            type="file"
                            id="uploadImg"
                            accept="image/*"
                            style="display:none"
                            onchange="uploadProfileImage()">

                        <button
                            class="btn btn-sm btn-outline-primary rounded-pill px-3 mt-2"
                            onclick="document.getElementById('uploadImg').click()">
                            <i class="bi bi-camera me-1"></i> Update Photo
                        </button>
                    </div>

                    <!-- User Name and Meta Info -->
                    <h4><?= htmlspecialchars($user['full_name']) ?></h4>
                    <p class="user-meta">
                        <i class="bi bi-calendar-check"></i>
                        Member since <?= date('F Y', strtotime($user['created_at'])) ?> 
                        <span class="mx-1">•</span>
                        <i class="bi bi-clock"></i>
                        <?= $daysActive ?> days active
                    </p>

                    <div class="d-flex justify-content-center gap-2 mb-4">
                        <span class="badge bg-success-subtle text-success border border-success rounded-pill px-3">Elite Member</span>
                        <span class="badge bg-primary-subtle text-primary border border-primary rounded-pill px-3">Active Saver</span>
                    </div>

                    <button class="btn btn-primary-modern rounded-pill px-5 mb-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#editModal">
                        <i class="bi bi-pencil-square me-2"></i>Edit Profile
                    </button>

                    <!-- Enhanced 2x2 Statistics Grid -->
                    <div class="stats-container">
                        <!-- Total Expenses -->
                        <div class="stat-box expense">
                            <div class="stat-icon">
                                <i class="bi bi-wallet2"></i>
                            </div>
                            <h4 class="stat-value text-danger mb-0">₹<?= number_format($totalExpenses, 2) ?></h4>
                            <p class="stat-label mb-0">Total Expenses</p>
                        </div>

                        <!-- Savings Progress -->
                        <div class="stat-box savings">
                            <div class="stat-icon">
                                <i class="bi bi-piggy-bank"></i>
                            </div>
                            <h4 class="stat-value text-success mb-0">₹<?= number_format($totalSavings, 2) ?></h4>
                            <p class="stat-label mb-0">Total Savings</p>
                            <div class="progress-custom">
                                <div class="progress-bar-animated bg-success" style="width: <?= round($savingsProgress) ?>%"></div>
                            </div>
                            <small class="progress-text d-block mt-1"><?= round($savingsProgress) ?>% of ₹<?= number_format($savingsGoal, 2) ?> goal</small>
                        </div>

                        <!-- This Month Budget -->
                        <div class="stat-box budget" style="grid-column: span 2;">
                            <div class="stat-icon">
                                <i class="bi bi-graph-up"></i>
                            </div>
                            <h4 class="stat-value mb-0" style="color: <?= $budgetUsage > 90 ? '#ef4444' : ($budgetUsage > 70 ? '#f59e0b' : '#10b981') ?>;">
                                ₹<?= number_format($monthlyExpenses, 2) ?>
                            </h4>
                            <p class="stat-label mb-0">This Month's Expenses</p>
                            <div class="progress-custom">
                                <div class="progress-bar-animated" style="width: <?= round($budgetUsage) ?>%; background-color: <?= $budgetUsage > 90 ? '#ef4444' : ($budgetUsage > 70 ? '#f59e0b' : '#10b981') ?>;"></div>
                            </div>
                            <small class="progress-text d-block mt-1"><?= round($budgetUsage) ?>% of ₹<?= number_format($user['monthly_budget'], 2) ?> budget used</small>
                        </div>
                    </div>

                    <!-- Personal Information Section -->
                    <div class="text-start mx-auto" style="max-width: 650px;">
                        <h6 class="fw-bold mb-3 px-2 text-uppercase small opacity-75">Personal Information</h6>
                        
                        <div class="info-box">
                            <div class="info-icon email">
                                <i class="bi bi-envelope-fill"></i>
                            </div>
                            <div>
                                <small class="info-label d-block">Email Address</small>
                                <span class="info-value" id="userEmail"><?= htmlspecialchars($user['email']) ?></span>
                            </div>
                        </div>

                        <div class="info-box">
                            <div class="info-icon phone">
                                <i class="bi bi-telephone-fill"></i>
                            </div>
                            <div>
                                <small class="info-label d-block">Phone Number</small>
                                <span class="info-value" id="userPhone"><?= htmlspecialchars($user['mobile']) ?></span>
                            </div>
                        </div>

                        <div class="info-box">
                            <div class="info-icon budget">
                                <i class="bi bi-wallet2"></i>
                            </div>
                            <div>
                                <small class="info-label d-block">Monthly Budget</small>
                                <span class="info-value" id="userBudget">₹<?= number_format($user['monthly_budget'], 2) ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <?php include 'components/footer.php'; ?>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold"><i class="bi bi-gear me-2"></i>Update Monthly Budget</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body px-4">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        Set your monthly expense limit. You'll receive alerts when you approach or exceed this amount.
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Monthly Budget (₹)</label>
                        <input
                            type="number"
                            step="0.01"
                            id="editBudget"
                            class="form-control rounded-3 p-2"
                            value="<?= $user['monthly_budget'] ?>"
                            placeholder="Enter your monthly budget">
                    </div>
                </div>

                <div class="modal-footer border-0">
                    <button class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn-primary-modern rounded-pill px-4" onclick="updateBudget()">
                        <i class="bi bi-check-lg me-2"></i>Update
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Chatbot Integration -->
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

    <script src="components/js/chatbot.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function toggleMenu() {
            document.getElementById("mobileSidebar").classList.toggle("active");
            document.getElementById("sidebarOverlay").classList.toggle("active");
            document.body.classList.toggle("sidebar-open");
        }

        // Logout button functionality
        document.addEventListener('DOMContentLoaded', function() {
            const logoutBtn = document.getElementById('logout-btn');
            if (logoutBtn) {
                logoutBtn.addEventListener('click', function() {
                    if (confirm('Are you sure you want to logout?')) {
                        window.location.href = 'profile.php?action=logout';
                    }
                });
            }
        });

        // Theme Toggle
        const themeToggleBtn = document.getElementById('theme-toggle');
        if (themeToggleBtn) {
            const setTheme = (isDark) => {
                if (isDark) {
                    document.documentElement.setAttribute('data-theme', 'dark');
                    themeToggleBtn.innerHTML = '<i class="fas fa-sun"></i>';
                } else {
                    document.documentElement.setAttribute('data-theme', 'light');
                    themeToggleBtn.innerHTML = '<i class="fas fa-moon"></i>';
                }
            };

            const savedTheme = localStorage.getItem('theme') || 'light';
            setTheme(savedTheme === 'dark');

            themeToggleBtn.addEventListener('click', () => {
                const isNowDark = document.documentElement.getAttribute('data-theme') !== 'dark';
                localStorage.setItem('theme', isNowDark ? 'dark' : 'light');
                setTheme(isNowDark);
            });
        }

        // Budget Update
        function updateBudget() {
            const budget = document.getElementById("editBudget").value;

            if (!budget || budget < 0) {
                alert("Please enter a valid budget amount");
                return;
            }

            if (budget < 1000) {
                if (!confirm("Your budget is quite low. Are you sure you want to proceed?")) {
                    return;
                }
            }

            fetch("backend/user/update_profile.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        monthly_budget: budget
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert(data.message);
                        location.reload();
                    } else {
                        alert(data.message || "Failed to update budget");
                    }
                })
                .catch(() => alert("Server error"));
        }

        // Profile Image Upload
        function uploadProfileImage() {
            const fileInput = document.getElementById("uploadImg");
            const file = fileInput.files[0];

            if (!file) return;

            if (!file.type.startsWith('image/')) {
                alert("Please upload an image file");
                return;
            }

            if (file.size > 5 * 1024 * 1024) {
                alert("File size must be less than 5MB");
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById("profileImg").src = e.target.result;
            };
            reader.readAsDataURL(file);

            const formData = new FormData();
            formData.append("profile_image", file);

            fetch("backend/user/update_profile.php", {
                    method: "POST",
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        document.getElementById("profileImg").src = data.image_url + "?t=" + new Date().getTime();
                        alert(data.message);
                    } else {
                        alert(data.message || "Image upload failed");
                    }
                })
                .catch(() => alert("Server error"));
        }

        // Animate statistics on load
        window.addEventListener('load', () => {
            const statValues = document.querySelectorAll('.stat-value');
            statValues.forEach((stat, index) => {
                stat.style.opacity = '0';
                setTimeout(() => {
                    stat.style.transition = 'opacity 0.5s ease';
                    stat.style.opacity = '1';
                }, 100 * index);
            });
        });
    </script>

</body>

</html>