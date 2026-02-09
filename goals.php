<?php
// ============================================
// SESSION AND LOGOUT HANDLING
// ============================================
session_start();

// Handle logout action
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    header("Location: login-pages/login.php");
    exit();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login-pages/login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bachat Buddy | Goals</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/chatbot-style.css">
    <!-- Custom Styles -->
    <link rel="stylesheet" href="components/style.css">
    <script src="components/js/chatbot.js"></script>

    <style>
        /* ===================================================================
           COMPREHENSIVE DESIGN SYSTEM
           ================================================================ */

        :root {
            /* Primary Colors - Professional Teal */
            --color-primary-50: #e0f7fa;
            --color-primary-100: #b2ebf2;
            --color-primary-200: #80deea;
            --color-primary-300: #4dd0e1;
            --color-primary-400: #26c6da;
            --color-primary-500: #00bcd4;
            --color-primary-600: #00acc1;
            --color-primary-700: #0097a7;
            --color-primary-800: #00838f;
            --color-primary-900: #006064;

            /* Success Colors - Achievement Green */
            --color-success-50: #e8f5e9;
            --color-success-100: #c8e6c9;
            --color-success-200: #a5d6a7;
            --color-success-300: #81c784;
            --color-success-400: #66bb6a;
            --color-success-500: #4caf50;
            --color-success-600: #43a047;
            --color-success-700: #388e3c;
            --color-success-800: #2e7d32;
            --color-success-900: #1b5e20;

            /* Warning Colors - Progress Amber */
            --color-warning-50: #fff8e1;
            --color-warning-100: #ffecb3;
            --color-warning-200: #ffe082;
            --color-warning-300: #ffd54f;
            --color-warning-400: #ffca28;
            --color-warning-500: #ffc107;
            --color-warning-600: #ffb300;
            --color-warning-700: #ffa000;
            --color-warning-800: #ff8f00;
            --color-warning-900: #ff6f00;

            /* Danger Colors - Alert Red */
            --color-danger-50: #ffebee;
            --color-danger-100: #ffcdd2;
            --color-danger-200: #ef9a9a;
            --color-danger-300: #e57373;
            --color-danger-400: #ef5350;
            --color-danger-500: #f44336;
            --color-danger-600: #e53935;
            --color-danger-700: #d32f2f;
            --color-danger-800: #c62828;
            --color-danger-900: #b71c1c;

            /* Neutral Colors (Light Theme) */
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

            /* Shadows - Depth perception */
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            --shadow-2xl: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        /* Dark Theme Colors */
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
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.3);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.4), 0 2px 4px -1px rgba(0, 0, 0, 0.3);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.5), 0 4px 6px -2px rgba(0, 0, 0, 0.4);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.6), 0 10px 10px -5px rgba(0, 0, 0, 0.5);
        }

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

        /* Layout System */
        .layout {
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        /* Sidebar */
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

        /* Main Content */
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            height: 100vh;
            overflow: hidden;
        }

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

        .main-body {
            padding: 2rem;
            overflow-y: auto;
            background-color: var(--bg-main);
            transition: all 0.3s ease;
        }

        /* Page Header */
        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 2rem;
            border-radius: 16px;
            text-align: center;
            margin-bottom: 2rem;
            color: white;
            box-shadow: var(--shadow-lg);
        }

        .page-header h2 {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
        }

        .page-header p {
            font-size: 1rem;
            opacity: 0.95;
            margin-bottom: 1.5rem;
        }

        /* ===================================================================
           UPDATED: SMALLER GOAL CARDS
           Reduced padding, margins, and font sizes
           ================================================================ */
        .goal-card {
            background: var(--bg-card);
            border-radius: 16px;
            border: 1px solid var(--border-light);
            padding: 1.25rem;
            margin-bottom: 1rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: var(--shadow-sm);
            position: relative;
            overflow: hidden;
        }

        .goal-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
            border-color: var(--color-primary-300);
        }

        .goal-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .goal-title {
            font-size: 1.125rem;
            font-weight: 700;
            color: var(--text-primary);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .goal-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            background: linear-gradient(135deg, var(--color-primary-100), var(--color-primary-200));
            color: var(--color-primary-700);
            margin-right: 0.75rem;
        }

        /* Status Badges - Smaller */
        .status-badge {
            padding: 0.375rem 1rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
        }

        .status-badge.completed {
            background: var(--color-success-100);
            color: var(--color-success-800);
        }

        .status-badge.in-progress {
            background: var(--color-warning-100);
            color: var(--color-warning-800);
        }

        .status-badge.not-started {
            background: var(--border-light);
            color: var(--text-muted);
        }

        [data-theme="dark"] .status-badge.completed {
            background: var(--color-success-900);
            color: var(--color-success-100);
        }

        [data-theme="dark"] .status-badge.in-progress {
            background: var(--color-warning-900);
            color: var(--color-warning-100);
        }

        /* Goal Stats - Smaller */
        .goal-stats {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 0.75rem;
            margin-bottom: 1rem;
        }

        .stat-label {
            font-size: 0.75rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 600;
        }

        .stat-value {
            font-size: 1.125rem;
            font-weight: 700;
            color: var(--text-primary);
        }

        /* Progress Bar - Smaller */
        .progress-container {
            margin: 1rem 0;
        }

        .progress-bar-wrapper {
            height: 12px;
            background: var(--border-light);
            border-radius: 50px;
            overflow: hidden;
            margin-bottom: 0.5rem;
        }

        .progress-bar-fill {
            height: 100%;
            border-radius: 50px;
            transition: width 0.6s ease;
        }

        .progress-bar-fill.completed {
            background: linear-gradient(90deg, var(--color-success-500), var(--color-success-600));
        }

        .progress-bar-fill.in-progress {
            background: linear-gradient(90deg, var(--color-primary-500), var(--color-primary-600));
        }

        .progress-bar-fill.not-started {
            background: var(--border-medium);
        }

        .progress-info {
            display: flex;
            justify-content: space-between;
            font-size: 0.8125rem;
        }

        .progress-percentage {
            font-size: 1rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        .progress-amount {
            color: var(--text-muted);
        }

        /* Goal Actions - Smaller */
        .goal-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid var(--border-light);
        }

        .btn-modern {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.875rem;
            border: 2px solid transparent;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            text-decoration: none;
        }

        .btn-primary-modern {
            background: linear-gradient(135deg, var(--color-primary-500), var(--color-primary-600));
            color: white;
        }

        .btn-primary-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 188, 212, 0.3);
        }

        /* Button System */
        .btn-icon {
            width: 32px;
            height: 32px;
            padding: 0;
            border-radius: 6px;
            border: 1px solid var(--border-light);
            background: var(--input-bg);
            color: var(--text-secondary);
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-icon:hover {
            background: var(--bg-elevated);
            border-color: var(--color-primary-400);
            color: var(--color-primary-600);
            transform: scale(1.05);
        }

        .btn-icon.danger:hover {
            background: var(--color-danger-50);
            border-color: var(--color-danger-400);
            color: var(--color-danger-600);
        }

        /* Grid Layout for Goals */
        .goals-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1rem;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 3rem 2rem;
        }

        .empty-state-icon {
            font-size: 4rem;
            color: var(--text-muted);
            opacity: 0.3;
            margin-bottom: 1.5rem;
        }

        .empty-state-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.75rem;
        }

        .empty-state-text {
            color: var(--text-muted);
            margin-bottom: 1rem;
        }

        /* Dark mode card fixes */
        [data-theme="dark"] .card,
        [data-theme="dark"] .card-body,
        [data-theme="dark"] .modal-content {
            background-color: var(--bg-card) !important;
            color: var(--text-primary) !important;
        }

        [data-theme="dark"] .text-muted {
            color: var(--text-muted) !important;
        }

        [data-theme="dark"] .modal-header {
            border-bottom-color: var(--border-light);
        }

        [data-theme="dark"] .modal-footer {
            border-top-color: var(--border-light);
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
            .goals-grid {
                grid-template-columns: 1fr;
            }

            .goal-card {
                padding: 1rem;
            }

            .goal-stats {
                grid-template-columns: 1fr;
                gap: 0.5rem;
            }

            .page-header {
                padding: 1.5rem;
            }

            .page-header h2 {
                font-size: 1.75rem;
            }
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
                    <li><a class="nav-link" href="profile.php"><i class="bi bi-person-circle"></i> Profile</a></li>
                    <li><a class="nav-link" href="transaction.php"><i class="bi bi-arrow-left-right"></i> Transactions</a></li>
                    <li><a class="nav-link" href="add-entry.php"><i class="bi bi-journal-plus"></i> Add Entry</a></li>
                    <li><a class="nav-link active" href="goals.php"><i class="bi bi-bullseye"></i> Goals</a></li>
                </ul>
            </div>
        </div>

        <!-- Mobile Sidebar -->
        <div id="mobileSidebar" class="mobile-sidebar d-lg-none">
            <div class="p-4">
                <div class="brand d-flex align-items-center justify-content-between mb-4">
                    <span><i class="bi bi-piggy-bank-fill"></i> Bachat Buddy</span>
                    <button onclick="toggleMenu()" class="btn-close"></button>
                </div>
                <ul class="nav flex-column gap-2">
                    <li><a class="nav-link" href="index.php"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
                    <li><a class="nav-link" href="profile.php"><i class="bi bi-person-circle"></i> Profile</a></li>
                    <li><a class="nav-link" href="transaction.php"><i class="bi bi-arrow-left-right"></i> Transactions</a></li>
                    <li><a class="nav-link" href="add-entry.php"><i class="bi bi-journal-plus"></i> Add Entry</a></li>
                    <li><a class="nav-link active" href="goals.php"><i class="bi bi-bullseye"></i> Goals</a></li>
                </ul>
            </div>
        </div>
        <div id="sidebarOverlay" class="sidebar-overlay d-lg-none" onclick="toggleMenu()"></div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <?php include 'components/header.php'; ?>

            <!-- Main Body -->
            <div class="main-body">
                <!-- Motivational Header -->
                <div class="page-header">
                    <h2>🎯 My Savings Goals</h2>
                    <p>Every penny saved brings you closer to your dreams. Keep going!</p>
                    <button class="btn btn-light rounded-pill px-4 py-2 fw-bold mt-2" data-bs-toggle="modal" data-bs-target="#goalModal" onclick="resetGoalForm()">
                        <i class="bi bi-plus-circle me-2"></i> Create New Goal
                    </button>
                </div>

                <!-- Goals Container - Updated with grid layout -->
                <div class="goals-grid" id="goalList">
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="bi bi-hourglass-split"></i>
                        </div>
                        <div class="empty-state-title">Loading your goals...</div>
                        <div class="empty-state-text">Please wait while we fetch your financial aspirations</div>
                    </div>
                </div>

                <!-- Goal Modal -->
                <div class="modal fade" id="goalModal" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title fw-bold" id="modalTitle">
                                    <i class="bi bi-flag-fill me-2" style="color: var(--color-primary-500);"></i>
                                    Add New Goal
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <form id="goalForm">
                                    <input type="hidden" id="editGoalId">

                                    <div class="mb-3">
                                        <label class="form-label">Goal Name</label>
                                        <input type="text" id="goalName" class="form-control" placeholder="e.g. Dream Vacation, New Car" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Target Amount (₹)</label>
                                        <input type="number" id="goalTarget" class="form-control" placeholder="50000" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Initial Amount Saved (₹)</label>
                                        <input type="number" id="goalSaved" class="form-control" placeholder="0" value="0">
                                        <small class="text-muted">How much have you already saved towards this goal?</small>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-primary" id="saveGoalBtn" onclick="saveGoal()">
                                    <i class="bi bi-check-circle me-1"></i>
                                    Save Goal
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
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

    <!-- JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>

    <script>
        // ===================================================================
        // MOBILE MENU TOGGLE
        // ===================================================================
        function toggleMenu() {
            document.getElementById("mobileSidebar").classList.toggle("active");
            document.getElementById("sidebarOverlay").classList.toggle("active");
            document.body.classList.toggle("sidebar-open");
        }

        // ===================================================================
        // THEME TOGGLE
        // ===================================================================
        const themeToggle = document.getElementById('theme-toggle');
        const setTheme = (theme) => {
            document.documentElement.setAttribute('data-theme', theme);
            if (themeToggle) {
                themeToggle.innerHTML = theme === 'dark' ?
                    '<i class="fas fa-sun"></i>' :
                    '<i class="fas fa-moon"></i>';
            }
            localStorage.setItem('theme', theme);
        };

        if (themeToggle) {
            themeToggle.addEventListener('click', () => {
                const currentTheme = document.documentElement.getAttribute('data-theme');
                setTheme(currentTheme === 'dark' ? 'light' : 'dark');
            });

            setTheme(localStorage.getItem('theme') || 'light');
        }

        // ===================================================================
        // LOGOUT FUNCTIONALITY
        // ===================================================================
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

        // ===================================================================
        // GOALS MANAGEMENT
        // ===================================================================

        // Load goals on page load
        window.addEventListener('DOMContentLoaded', loadGoals);

        // Reset form for adding new goal
        function resetGoalForm() {
            document.getElementById('editGoalId').value = '';
            document.getElementById('goalForm').reset();
            document.getElementById('modalTitle').innerHTML =
                '<i class="bi bi-flag-fill me-2" style="color: var(--color-primary-500);"></i>Add New Goal';
            document.getElementById('saveGoalBtn').innerHTML =
                '<i class="bi bi-check-circle me-1"></i>Save Goal';
            document.getElementById('saveGoalBtn').setAttribute('onclick', 'saveGoal()');
        }

        function loadGoals() {
            fetch('backend/goals/goals_backend.php?action=fetch')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayGoals(data.goals);
                        animateGoalCards();
                    } else {
                        document.getElementById('goalList').innerHTML =
                            `<div class="empty-state">
                                <div class="empty-state-icon"><i class="bi bi-exclamation-triangle"></i></div>
                                <div class="empty-state-title">Error Loading Goals</div>
                                <div class="empty-state-text">${data.message}</div>
                            </div>`;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('goalList').innerHTML =
                        `<div class="empty-state">
                            <div class="empty-state-icon"><i class="bi bi-wifi-off"></i></div>
                            <div class="empty-state-title">Connection Error</div>
                            <div class="empty-state-text">Failed to load goals. Please refresh the page.</div>
                        </div>`;
                });
        }

        function displayGoals(goals) {
            const goalList = document.getElementById('goalList');

            if (!goalList) {
                console.error('Goal list container not found');
                return;
            }

            goalList.innerHTML = '';

            if (goals.length === 0) {
                goalList.innerHTML = `
                    <div class="empty-state">
                        <div class="empty-state-icon"><i class="bi bi-bullseye"></i></div>
                        <div class="empty-state-title">No Goals Yet</div>
                        <div class="empty-state-text">Start your savings journey by creating your first financial goal!</div>
                        <button class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#goalModal" onclick="resetGoalForm()">
                            <i class="bi bi-plus-circle me-2"></i>Create Your First Goal
                        </button>
                    </div>`;
                return;
            }

            goals.forEach(goal => {
                goalList.innerHTML += createGoalCard(goal);
            });
        }

        function createGoalCard(goal) {
            const progress = parseFloat(goal.progress);
            let statusClass, statusText, progressClass;

            if (progress >= 100) {
                statusClass = 'completed';
                statusText = 'Achieved';
                progressClass = 'completed';
            } else if (progress > 0) {
                statusClass = 'in-progress';
                statusText = 'In Progress';
                progressClass = 'in-progress';
            } else {
                statusClass = 'not-started';
                statusText = 'Not Started';
                progressClass = 'not-started';
            }

            return `
                <div class="goal-card" data-goal-id="${goal.id}">
                    <div class="goal-card-header">
                        <div class="d-flex align-items-center">
                            <div class="goal-icon">
                                <i class="bi bi-${progress >= 100 ? 'trophy-fill' : 'flag-fill'}"></i>
                            </div>
                            <h3 class="goal-title">${goal.goalName}</h3>
                        </div>
                        <span class="status-badge ${statusClass}">
                            <i class="bi bi-${progress >= 100 ? 'check-circle-fill' : 'clock-fill'}"></i>
                            ${statusText}
                        </span>
                    </div>

                    <div class="goal-stats">
                        <div class="stat-item">
                            <span class="stat-label">Target</span>
                            <span class="stat-value">₹${parseFloat(goal.targetAmount).toLocaleString('en-IN')}</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">Saved</span>
                            <span class="stat-value" style="color: var(--color-success-600);">₹${parseFloat(goal.savedAmount).toLocaleString('en-IN')}</span>
                        </div>
                    </div>

                    <div class="progress-container">
                        <div class="progress-bar-wrapper">
                            <div class="progress-bar-fill ${progressClass}" style="width: ${Math.min(progress, 100)}%"></div>
                        </div>
                        <div class="progress-info">
                            <span class="progress-percentage">${progress.toFixed(1)}% Complete</span>
                            <span class="progress-amount">₹${(parseFloat(goal.targetAmount) - parseFloat(goal.savedAmount)).toLocaleString('en-IN')} remaining</span>
                        </div>
                    </div>

                    <div class="goal-actions">
                        <button class="btn-modern btn-primary-modern" onclick="updateGoalFunds(${goal.id})">
                            <i class="bi bi-plus-circle"></i>
                            Add Funds
                        </button>
                        <div class="d-flex gap-2">
                            <button class="btn-icon" onclick="editGoal(${goal.id})" title="Edit Goal">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn-icon danger" onclick="deleteGoal(${goal.id})" title="Delete Goal">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
        }

        function saveGoal() {
            const goalName = document.getElementById("goalName").value.trim();
            const goalTarget = parseFloat(document.getElementById("goalTarget").value);
            const goalSaved = parseFloat(document.getElementById("goalSaved").value) || 0;
            const goalId = document.getElementById("editGoalId").value;

            if (!goalName || !goalTarget) {
                alert("Please fill in all required fields");
                return;
            }

            if (goalTarget <= 0) {
                alert("Target amount must be greater than 0");
                return;
            }

            if (goalSaved < 0) {
                alert("Saved amount cannot be negative");
                return;
            }

            const formData = new FormData();
            formData.append('goalName', goalName);
            formData.append('targetAmount', goalTarget);
            formData.append('savedAmount', goalSaved);

            let url = 'backend/goals/goals_backend.php?action=add';

            if (goalId) {
                url = 'backend/goals/goals_backend.php?action=edit';
                formData.append('goalId', goalId);
            }

            fetch(url, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Close modal
                        const modal = bootstrap.Modal.getInstance(document.getElementById('goalModal'));
                        modal.hide();

                        // Reload goals
                        loadGoals();

                        // Show success message
                        showToast('Goal saved successfully!', 'success');
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while saving the goal');
                });
        }

        function updateGoalFunds(goalId) {
            const addMore = prompt("Enter amount to add to your goal:");

            if (!addMore || isNaN(addMore) || parseFloat(addMore) <= 0) {
                alert("Please enter a valid amount");
                return;
            }

            const formData = new FormData();
            formData.append('goalId', goalId);
            formData.append('additionalAmount', parseFloat(addMore));

            fetch('backend/goals/goals_backend.php?action=add_funds', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        loadGoals();
                        showToast('Funds added successfully!', 'success');
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred');
                });
        }

        function editGoal(goalId) {
            fetch('backend/goals/goals_backend.php?action=fetch')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const goal = data.goals.find(g => g.id == goalId);

                        if (goal) {
                            document.getElementById('editGoalId').value = goalId;
                            document.getElementById('goalName').value = goal.goalName;
                            document.getElementById('goalTarget').value = goal.targetAmount;
                            document.getElementById('goalSaved').value = goal.savedAmount;

                            document.getElementById('modalTitle').innerHTML =
                                '<i class="bi bi-pencil-fill me-2" style="color: var(--color-primary-500);"></i>Edit Goal';

                            const modal = new bootstrap.Modal(document.getElementById('goalModal'));
                            modal.show();
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to load goal');
                });
        }

        function deleteGoal(goalId) {
            if (!confirm("Are you sure you want to delete this goal? This action cannot be undone.")) {
                return;
            }

            const formData = new FormData();
            formData.append('goalId', goalId);

            fetch('backend/goals/goals_backend.php?action=delete', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        loadGoals();
                        showToast('Goal deleted successfully!', 'success');
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred');
                });
        }

        function showToast(message, type = 'success') {
            // Create toast element
            const toast = document.createElement('div');
            toast.className = `toast align-items-center text-bg-${type} border-0`;
            toast.setAttribute('role', 'alert');
            toast.setAttribute('aria-live', 'assertive');
            toast.setAttribute('aria-atomic', 'true');

            toast.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            `;

            // Add to body
            document.body.appendChild(toast);

            // Initialize and show toast
            const bsToast = new bootstrap.Toast(toast);
            bsToast.show();

            // Remove after hide
            toast.addEventListener('hidden.bs.toast', function() {
                document.body.removeChild(toast);
            });
        }

        function animateGoalCards() {
            gsap.from('.goal-card', {
                duration: 0.6,
                y: 20,
                opacity: 0,
                stagger: 0.1,
                ease: 'power3.out'
            });
        }
    </script>
</body>

</html>