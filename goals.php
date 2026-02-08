<?php
// ============================================
// SESSION AND LOGOUT HANDLING
// ============================================
session_start();

// Handle logout action
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    // Destroy all session data
    session_destroy();
    
    // Redirect to login page
    header("Location: login-pages/login.php");
    exit();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login if not logged in
    header("Location: login-pages/login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bachat-Buddy | Goals</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Custom Styles -->
    <link rel="stylesheet" href="components/style.css">
    
    <style>
        /* ============================================
           CSS VARIABLES FOR THEME SUPPORT
           ============================================ */
        :root {
            --bg-main: #f2f6f9;
            --bg-card: #ffffff;
            --text-main: #333333;
            --text-muted: #6c757d;
            --border-color: #eeeeee;
            --sidebar-bg: #ffffff;
            --header-bg: #ffffff;
            --input-bg: #f5f5f5;
            --label-color: #495057;
        }

        /* Dark theme colors */
        [data-theme="dark"] {
            --bg-main: #0f172a;
            --bg-card: #1e293b;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --border-color: #334155;
            --sidebar-bg: #1e293b;
            --header-bg: #1e293b;
            --input-bg: #334155;
            --label-color: #ffffff;
        }

        /* ============================================
           GENERAL STYLES
           ============================================ */
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background-color: var(--bg-main);
            color: var(--text-main);
            transition: all 0.3s ease;
        }

        /* Main layout container */
        .layout {
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        /* ============================================
           SIDEBAR STYLES
           ============================================ */
        .sidebar {
            width: 250px;
            background-color: var(--sidebar-bg);
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 2rem 1rem;
            transition: all 0.3s ease;
        }

        .sidebar .nav-link {
            display: flex;
            align-items: center;
            padding: 10px 15px;
            border-radius: 10px;
            color: var(--text-main);
            font-weight: 500;
            text-decoration: none;
            transition: 0.3s ease;
        }

        .sidebar .nav-link:hover {
            background-color: #000;
            color: #fff;
        }

        .sidebar .nav-link i {
            margin-right: 12px;
            font-size: 1.1rem;
        }

        .brand {
            font-weight: bold;
            font-size: 1.2rem;
            padding-left: 15px;
            margin-bottom: 2rem;
        }

        /* ============================================
           HEADER STYLES
           ============================================ */
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            height: 100vh;
        }

        .header {
            background-color: var(--header-bg);
            padding: 1rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid var(--border-color);
            transition: all 0.3s ease;
        }

        .search-box {
            background: var(--input-bg);
            border-radius: 10px;
            padding: 5px 15px;
            width: 300px;
            display: flex;
            align-items: center;
        }

        .search-box input {
            border: none;
            background: transparent;
            outline: none;
            width: 100%;
            color: var(--text-main);
        }

        .notification,
        #theme-toggle {
            background: var(--input-bg);
            padding: 8px;
            border-radius: 10px;
            cursor: pointer;
            border: none;
            color: var(--text-main);
        }

        .profile-info {
            display: flex;
            align-items: center;
            gap: 10px;
            background: var(--input-bg);
            padding: 6px 12px;
            border-radius: 20px;
        }

        /* ============================================
           MAIN BODY STYLES
           ============================================ */
        .main-body {
            padding: 2rem;
            overflow-y: auto;
            background-color: var(--bg-main);
            transition: all 0.3s ease;
        }

        .page-header {
            background: linear-gradient(135deg, #a8edea, #fed6e3);
            padding: 40px 20px;
            border-radius: 0 0 30px 30px;
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }

        [data-theme="dark"] .page-header {
            background: linear-gradient(135deg, #1e3a8a, #4c1d95);
            color: #fff;
        }

        /* ============================================
           FORM STYLES
           ============================================ */
        .form-label,
        .text-muted,
        small.d-block {
            color: var(--label-color) !important;
            opacity: 1;
        }

        .form-control {
            background-color: var(--input-bg);
            border-color: var(--border-color);
            color: var(--text-main);
        }

        .form-control:focus {
            background-color: var(--input-bg);
            color: var(--text-main);
            border-color: #3b82f6;
            box-shadow: 0 0 0 0.25rem rgba(59, 130, 246, 0.25);
        }

        .form-control::placeholder {
            color: var(--text-muted);
            opacity: 0.7;
        }

        /* ============================================
           GOAL CARD STYLES
           ============================================ */
        .goal-card {
            background: var(--bg-card);
            border-radius: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
            transition: transform 0.2s, background 0.3s;
            border: 1px solid var(--border-color);
        }

        .goal-card:hover {
            transform: translateY(-5px);
        }

        /* ============================================
           MODAL STYLES
           ============================================ */
        .modal-content {
            background-color: var(--bg-card);
            color: var(--text-main);
            border: 1px solid var(--border-color);
            border-radius: 20px;
        }

        [data-theme="dark"] .btn-close {
            filter: invert(1) grayscale(100%) brightness(200%);
        }

        /* ============================================
           MOBILE SIDEBAR STYLES
           ============================================ */
        @media (max-width: 992px) {
            .mobile-sidebar {
                position: fixed;
                top: 0;
                left: -300px;
                width: 250px;
                height: 100%;
                background-color: var(--sidebar-bg);
                z-index: 1050;
                transition: all 0.3s ease;
                box-shadow: 5px 0 15px rgba(0, 0, 0, 0.2);
                overflow-y: auto;
                padding: 2rem 1rem;
            }

            .mobile-sidebar.active {
                left: 0;
            }

            .sidebar-overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, 0.4);
                z-index: 1040;
                transition: all 0.3s ease;
            }

            .sidebar-overlay.active {
                display: block;
            }

            .sidebar.d-lg-block {
                display: none;
            }

            .header {
                padding: 0.75rem 1rem;
            }

            .search-box {
                width: 100%;
                max-width: 200px;
            }
        }

        /* ============================================
           RESPONSIVE STYLES
           ============================================ */
        @media (max-width: 768px) {
            .main-body {
                padding: 1rem;
            }

            .page-header {
                padding: 30px 15px;
                border-radius: 0 0 20px 20px;
                font-size: 0.95rem;
            }

            .goal-card {
                padding: 15px;
                margin-bottom: 15px;
            }
        }

        @media (max-width: 576px) {
            .header {
                flex-wrap: wrap;
                gap: 8px;
            }

            .goal-card {
                padding: 12px;
                margin-bottom: 12px;
            }
        }

        /* ============================================
           BADGE STYLES
           ============================================ */
        .badge.bg-success {
            background-color: #059669 !important;
            color: #ecfdf5;
        }
    </style>
</head>

<body>
    <div class="layout">
        <!-- ============================================
             DESKTOP SIDEBAR
             ============================================ -->
        <div class="sidebar d-none d-lg-block">
            <div>
                <div class="brand d-flex align-items-center mb-4">
                    <i class="bi bi-piggy-bank me-2 text-success"></i> Bachat-Buddy
                </div>
                <ul class="nav flex-column gap-2">
                    <li><a class="nav-link" href="index.php"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
                    <li><a class="nav-link" href="profile.php"><i class="bi bi-person-circle"></i> Profile</a></li>
                    <li><a class="nav-link" href="transaction.php"><i class="bi bi-arrow-left-right"></i> Transactions</a></li>
                    <li><a class="nav-link" href="add-entry.php"><i class="bi bi-journal-plus"></i> Add Entry</a></li>
                    <li><a class="nav-link" href="goals.php" style="background: #3b82f6; color: #fff;"><i class="bi bi-bullseye"></i> Goals</a></li>
                </ul>
            </div>
        </div>

        <!-- ============================================
             MOBILE SIDEBAR
             ============================================ -->
        <div id="mobileSidebar" class="mobile-sidebar d-lg-none">
            <div class="p-4">
                <div class="brand d-flex align-items-center justify-content-between mb-4">
                    <span><i class="bi bi-piggy-bank me-2 text-success"></i> Bachat-Buddy</span>
                    <button onclick="toggleMenu()" class="btn-close border-0 bg-transparent text-muted fs-4">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                <ul class="nav flex-column gap-2">
                    <li><a class="nav-link" href="index.php"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
                    <li><a class="nav-link" href="profile.php"><i class="bi bi-person-circle"></i> Profile</a></li>
                    <li><a class="nav-link" href="transaction.php"><i class="bi bi-arrow-left-right"></i> Transactions</a></li>
                    <li><a class="nav-link" href="add-entry.php"><i class="bi bi-journal-plus"></i> Add Entry</a></li>
                    <li><a class="nav-link" href="goals.php" style="background: #3b82f6; color: #fff;"><i class="bi bi-bullseye"></i> Goals</a></li>
                </ul>
            </div>
        </div>
        
        <!-- Sidebar overlay for mobile -->
        <div id="sidebarOverlay" class="sidebar-overlay d-lg-none" onclick="toggleMenu()"></div>

        <!-- ============================================
             MAIN CONTENT AREA
             ============================================ -->
        <div class="main-content">
            <!-- ============================================
                 HEADER
                 ============================================ -->
            <div class="header">
                <div class="d-flex align-items-center gap-2">
                    <!-- Mobile menu toggle button -->
                    <button class="btn d-lg-none border-0 p-0 me-2" onclick="toggleMenu()">
                        <i class="bi bi-list fs-2"></i>
                    </button>
                    <h5 class="mb-0 fw-bold">Goals</h5>
                </div>
                
                <div class="d-flex align-items-center gap-3">
                    <!-- Notification dropdown -->
                    <div class="relative inline-block text-left">
                        <div id="notificationBtn" class="notification p-2 rounded-full cursor-pointer hover:bg-gray-200 transition-colors relative">
                            <i class="bi bi-bell"></i>
                            <span id="notificationBadge" class="absolute top-0 right-0 inline-flex items-center justify-center px-1.5 py-0.5 text-[10px] font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-500 rounded-full">
                                3
                            </span>
                        </div>
                        <div id="notificationDropdown" class="hidden absolute right-0 mt-3 w-80 bg-white border border-gray-200 rounded-xl shadow-xl z-[1000] overflow-hidden">
                            <div class="p-3 border-b border-gray-100 flex justify-content-between align-items-center bg-white">
                                <span class="font-semibold text-gray-700">Notifications</span>
                                <button onclick="clearNotifications()" class="text-xs text-blue-600 hover:underline">Clear all</button>
                            </div>
                            <div class="max-h-64 overflow-y-auto bg-white" id="notificationList">
                                <div class="p-3 border-b border-gray-50 hover:bg-gray-50 cursor-pointer">
                                    <p class="text-sm text-gray-800 mb-0"><strong>Budget Alert:</strong> You've spent 80% of your Food budget.</p>
                                    <span class="text-[11px] text-gray-400">5 mins ago</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Theme toggle button -->
                    <button id="theme-toggle" class="p-2 rounded-full border-0">
                        <i class="fas fa-moon"></i>
                    </button>

                    <!-- Logout button -->
                    <button id="logout-btn" class="notification p-2 rounded-full border-0" title="Logout">
                        <i class="fas fa-sign-out-alt"></i>
                    </button>
                </div>
            </div>
            
            <!-- ============================================
                 MAIN BODY
                 ============================================ -->
            <div class="main-body">
                <!-- Page header -->
                <div class="page-header">
                    <h2>🎯 My Savings Goals</h2>
                    <p class="opacity-75">Track your progress and achieve your financial dreams</p>
                    <button class="btn btn-primary rounded-pill px-4 mt-2" data-bs-toggle="modal" data-bs-target="#goalModal" onclick="prepareAddGoal()">
                        <i class="bi bi-plus-circle me-1"></i> Add New Goal
                    </button>
                </div>

                <!-- Goals list container -->
                <div class="container" id="goalList">
                    <p class="text-center text-muted mt-4">Loading goals...</p>
                </div>

                <!-- ============================================
                     MODAL: ADD/EDIT GOAL
                     ============================================ -->
                <div class="modal fade" id="goalModal" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content shadow-lg">
                            <div class="modal-header border-0 pb-0">
                                <h5 class="modal-title fw-bold" id="modalTitle">Add New Goal</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body p-4">
                                <form id="goalForm">
                                    <!-- Hidden field for edit goal ID -->
                                    <input type="hidden" id="editGoalId">
                                    
                                    <!-- Goal Name -->
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold">Goal Name</label>
                                        <input type="text" id="goalName" class="form-control rounded-3" placeholder="e.g. Dream Vacation" required>
                                    </div>
                                    
                                    <!-- Target Amount -->
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold">Target Amount (₹)</label>
                                        <input type="number" id="goalTarget" class="form-control rounded-3" placeholder="0.00" required>
                                    </div>
                                    
                                    <!-- Initial Saved Amount -->
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold">Initial Saved Amount (₹)</label>
                                        <input type="number" id="goalSaved" class="form-control rounded-3" placeholder="0.00" value="0">
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer border-0 pt-0 d-flex justify-content-center gap-3">
                                <button class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                                <button class="btn btn-primary rounded-pill px-4" id="saveGoalBtn" onclick="saveGoal()">Save Goal</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <?php include 'components/footer.php'; ?>
            </div>
        </div>
    </div>

    <!-- ============================================
         CHATBOT
         ============================================ -->
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

    <!-- ============================================
         JAVASCRIPT LIBRARIES
         ============================================ -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="components/js/chatbot.js"></script>
    
    <script>
        // ============================================
        // MOBILE MENU TOGGLE
        // ============================================
        function toggleMenu() {
            document.getElementById("mobileSidebar").classList.toggle("active");
            document.getElementById("sidebarOverlay").classList.toggle("active");
            document.body.classList.toggle("sidebar-open");
        }

        // ============================================
        // THEME TOGGLE
        // ============================================
        const themeToggle = document.getElementById('theme-toggle');
        
        // Function to set theme
        const setTheme = (theme) => {
            document.documentElement.setAttribute('data-theme', theme);
            themeToggle.innerHTML = theme === 'dark' ? '<i class="fas fa-sun text-warning"></i>' : '<i class="fas fa-moon"></i>';
            localStorage.setItem('theme', theme);
        };

        // Toggle theme on button click
        themeToggle.addEventListener('click', () => {
            const currentTheme = document.documentElement.getAttribute('data-theme');
            setTheme(currentTheme === 'dark' ? 'light' : 'dark');
        });

        // Load saved theme on page load
        setTheme(localStorage.getItem('theme') || 'dark');

        // ============================================
        // GLOBAL VARIABLES
        // ============================================
        let currentEditGoalId = null;

        // ============================================
        // LOAD GOALS ON PAGE LOAD
        // ============================================
        window.addEventListener('DOMContentLoaded', function() {
            loadGoals();
        });

        // ============================================
        // FUNCTION: Load all goals from backend
        // ============================================
        function loadGoals() {
            fetch('backend/goals/goals_backend.php?action=fetch')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayGoals(data.goals);
                    } else {
                        document.getElementById('goalList').innerHTML = 
                            '<p class="text-center text-danger mt-4">Error: ' + data.message + '</p>';
                    }
                })
                .catch(error => {
                    console.error('Error loading goals:', error);
                    document.getElementById('goalList').innerHTML = 
                        '<p class="text-center text-danger mt-4">Failed to load goals. Please refresh.</p>';
                });
        }

        // ============================================
        // FUNCTION: Display goals on page
        // ============================================
        function displayGoals(goals) {
            const goalList = document.getElementById('goalList');
            goalList.innerHTML = '';

            // Check if there are any goals
            if (goals.length === 0) {
                goalList.innerHTML = '<p class="text-center text-muted mt-4">No goals yet. Create your first goal!</p>';
                return;
            }

            // Loop through each goal and create card
            goals.forEach(goal => {
                const goalCard = createGoalCard(goal);
                goalList.innerHTML += goalCard;
            });
        }

        // ============================================
        // FUNCTION: Create HTML for single goal card
        // ============================================
        function createGoalCard(goal) {
            const progress = goal.progress;
            const status = goal.status;
            const badgeClass = progress >= 100 ? 'bg-primary' : 'bg-success';
            const progressBarClass = progress >= 100 ? 'bg-primary' : 'bg-info';

            return `
                <div class="goal-card" data-goal-id="${goal.id}">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold">${goal.goalName}</h5>
                        <span class="badge ${badgeClass} rounded-pill px-3">${status}</span>
                    </div>
                    <p class="text-muted mb-2">Target: ₹${goal.targetAmount.toLocaleString()} | Saved: ₹${goal.savedAmount.toLocaleString()}</p>
                    <div class="progress mb-2" style="height: 10px; background-color: var(--input-bg);">
                        <div class="progress-bar ${progressBarClass}" style="width: ${Math.min(progress, 100)}%"></div>
                    </div>
                    <small class="text-muted">${progress.toFixed(1)}% Complete</small>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <button class="btn btn-sm btn-outline-primary rounded-pill px-3" onclick="updateGoalFunds(${goal.id})">
                            <i class="bi bi-plus-circle me-1"></i> Add Funds
                        </button>
                        <div>
                            <i class="bi bi-pencil text-muted me-3" style="cursor:pointer" onclick="editGoal(${goal.id})" title="Edit"></i>
                            <i class="bi bi-trash text-danger" style="cursor:pointer" onclick="deleteGoal(${goal.id})" title="Delete"></i>
                        </div>
                    </div>
                </div>
            `;
        }

        // ============================================
        // FUNCTION: Prepare modal for new goal
        // ============================================
        function prepareAddGoal() {
            currentEditGoalId = null;
            document.getElementById("modalTitle").innerText = "Add New Goal";
            document.getElementById("goalForm").reset();
            document.getElementById("saveGoalBtn").innerText = "Save Goal";
        }

        // ============================================
        // FUNCTION: Save goal (add or edit)
        // ============================================
        function saveGoal() {
            // Get form values
            const goalName = document.getElementById("goalName").value.trim();
            const goalTarget = parseFloat(document.getElementById("goalTarget").value);
            const goalSaved = parseFloat(document.getElementById("goalSaved").value) || 0;

            // Validation
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

            // Prepare form data
            const formData = new FormData();
            formData.append('goalName', goalName);
            formData.append('targetAmount', goalTarget);
            formData.append('savedAmount', goalSaved);

            // Determine if adding new or editing
            let url = 'backend/goals/goals_backend.php?action=add';
            
            if (currentEditGoalId) {
                url = 'backend/goals/goals_backend.php?action=edit';
                formData.append('goalId', currentEditGoalId);
            }

            // Send request to backend
            fetch(url, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Close modal
                    bootstrap.Modal.getInstance(document.getElementById("goalModal")).hide();
                    
                    // Reload goals
                    loadGoals();
                    
                    // Show success message
                    alert(data.message);
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred');
            });
        }

        // ============================================
        // FUNCTION: Add funds to goal
        // ============================================
        function updateGoalFunds(goalId) {
            // Prompt user for amount
            const addMore = prompt("Enter amount to add:");
            
            // Validation
            if (!addMore || isNaN(addMore) || parseFloat(addMore) <= 0) {
                alert("Please enter a valid amount");
                return;
            }

            // Prepare form data
            const formData = new FormData();
            formData.append('goalId', goalId);
            formData.append('additionalAmount', parseFloat(addMore));

            // Send request to backend
            fetch('backend/goals/goals_backend.php?action=add_funds', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadGoals();
                    alert(data.message);
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred');
            });
        }

        // ============================================
        // FUNCTION: Edit goal
        // ============================================
        function editGoal(goalId) {
            // Fetch goal data
            fetch('backend/goals/goals_backend.php?action=fetch')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Find the specific goal
                        const goal = data.goals.find(g => g.id === goalId);
                        
                        if (goal) {
                            // Set edit mode
                            currentEditGoalId = goalId;
                            
                            // Populate form
                            document.getElementById("goalName").value = goal.goalName;
                            document.getElementById("goalTarget").value = goal.targetAmount;
                            document.getElementById("goalSaved").value = goal.savedAmount;
                            
                            // Update modal title
                            document.getElementById("modalTitle").innerText = "Edit Goal";
                            document.getElementById("saveGoalBtn").innerText = "Update Goal";
                            
                            // Show modal
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

        // ============================================
        // FUNCTION: Delete goal
        // ============================================
        function deleteGoal(goalId) {
            // Confirm deletion
            if (!confirm("Are you sure you want to delete this goal?")) {
                return;
            }

            // Prepare form data
            const formData = new FormData();
            formData.append('goalId', goalId);

            // Send delete request
            fetch('backend/goals/goals_backend.php?action=delete', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadGoals();
                    alert(data.message);
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred');
            });
        }

        // ============================================
        // NOTIFICATIONS
        // ============================================
        const notificationBtn = document.getElementById('notificationBtn');
        const notificationDropdown = document.getElementById('notificationDropdown');

        if (notificationBtn) {
            notificationBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                notificationDropdown.classList.toggle('hidden');
            });
        }

        // Close dropdown when clicking outside
        window.addEventListener('click', (e) => {
            if (notificationBtn && !notificationBtn.contains(e.target) && !notificationDropdown.contains(e.target)) {
                notificationDropdown.classList.add('hidden');
            }
        });

        // Clear notifications function
        function clearNotifications() {
            document.getElementById('notificationList').innerHTML = `
                <div class="p-4 text-center text-sm text-gray-500">
                    <i class="bi bi-check2-all text-success d-block fs-4 mb-2"></i>
                    All caught up!
                </div>
            `;
            const badge = document.getElementById('notificationBadge');
            if (badge) badge.style.display = 'none';
        }

        // ============================================
        // LOGOUT BUTTON
        // ============================================
        document.getElementById("logout-btn").addEventListener("click", function() {
            // Confirm logout
            if (confirm("Are you sure you want to logout?")) {
                // Redirect to goals.php with logout action
                window.location.href = "goals.php?action=logout";
            }
        });
    </script>
</body>

</html>