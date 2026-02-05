<?php
session_start();

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
    <title>Bachat-Buddy | Goals</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="components/styles.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
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

        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background-color: var(--bg-main);
            color: var(--text-main);
            transition: all 0.3s ease;
        }

        .layout {
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

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

        .form-label,
        .text-muted,
        small.d-block {
            color: var(--label-color) !important;
            opacity: 1;
        }

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

        .modal-content {
            background-color: var(--bg-card);
            color: var(--text-main);
            border: 1px solid var(--border-color);
            border-radius: 20px;
        }

        [data-theme="dark"] .btn-close {
            filter: invert(1) grayscale(100%) brightness(200%);
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

        .footer {
            background: linear-gradient(135deg, #2a9d8f, #4cafef);
            border-radius: 20px 20px 0 0;
        }

        .badge.bg-success {
            background-color: #059669 !important;
            color: #ecfdf5;
        }

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
                justify-content: space-between;
            }

            .header h5 {
                font-size: 1rem;
            }

            .search-box {
                width: 100%;
                max-width: 200px;
            }

            .hamburger {
                display: block;
                font-size: 1.75rem;
                cursor: pointer;
                color: var(--text-main);
            }

            .btn-close {
                font-size: 1.5rem;
                color: var(--text-main);
                opacity: 1;
            }

            [data-theme="dark"] .btn-close {
                filter: invert(1) grayscale(100%) brightness(200%);
            }
        }

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

            .search-box {
                max-width: 150px;
            }
        }

        @media (max-width: 576px) {
            .header {
                flex-wrap: wrap;
                gap: 8px;
            }

            .profile-info {
                flex: 1 1 100%;
                justify-content: flex-end;
            }

            .goal-card {
                padding: 12px;
                margin-bottom: 12px;
            }

            .search-box {
                width: 100%;
            }
        }

        .header .btn i.bi-list {
            color: var(--text-main);
            transition: color 0.3s ease;
        }

        [data-theme="dark"] .header .btn i.bi-list {
            color: var(--text-main);
            filter: invert(0);
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
                    <li><a class="nav-link" href="index.php"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
                    <li><a class="nav-link" href="profile.php"><i class="bi bi-person-circle"></i> Profile</a></li>
                    <li><a class="nav-link" href="transaction.php"><i class="bi bi-arrow-left-right"></i> Transactions</a></li>
                    <li><a class="nav-link" href="add-entry.php"><i class="bi bi-journal-plus"></i> Add Entry</a></li>
                    <li><a class="nav-link" href="goals.php" style="background: #3b82f6; color: #fff;"><i class="bi bi-bullseye"></i> Goals</a></li>
                </ul>
            </div>
        </div>

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
        <div id="sidebarOverlay" class="sidebar-overlay d-lg-none" onclick="toggleMenu()"></div>

        <div class="main-content">
            <?php include 'components/header.php'; ?>
            <div class="main-body">
                <div class="page-header">
                    <h2>🎯 My Savings Goals</h2>
                    <p class="opacity-75">Track your progress and achieve your financial dreams</p>
                    <button class="btn btn-primary rounded-pill px-4 mt-2" data-bs-toggle="modal" data-bs-target="#goalModal" onclick="prepareAddGoal()">
                        <i class="bi bi-plus-circle me-1"></i> Add New Goal
                    </button>
                </div>

                <div class="container" id="goalList">
                    <!-- Goals will be loaded here dynamically -->
                </div>

                <div class="modal fade" id="goalModal" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content shadow-lg">
                            <div class="modal-header border-0 pb-0">
                                <h5 class="modal-title fw-bold" id="modalTitle">Add New Goal</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body p-4">
                                <form id="goalForm">
                                    <input type="hidden" id="editGoalId">
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold">Goal Name</label>
                                        <input type="text" id="goalName" class="form-control rounded-3" placeholder="e.g. Dream Vacation">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold">Target Amount (₹)</label>
                                        <input type="number" id="goalTarget" class="form-control rounded-3" placeholder="0.00">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold">Initial Saved Amount (₹)</label>
                                        <input type="number" id="goalSaved" class="form-control rounded-3" placeholder="0.00">
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

                <?php include 'components/footer.php'; ?>
            </div>
        </div>
    </div>

    <script>
        function toggleMenu() {
            document.getElementById("mobileSidebar").classList.toggle("active");
            document.getElementById("sidebarOverlay").classList.toggle("active");
            document.body.classList.toggle("sidebar-open");
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const themeToggle = document.getElementById('theme-toggle');
        const setTheme = (theme) => {
            document.documentElement.setAttribute('data-theme', theme);
            themeToggle.innerHTML = theme === 'dark' ? '<i class="fas fa-sun text-warning"></i>' : '<i class="fas fa-moon"></i>';
            localStorage.setItem('theme', theme);
        };

        themeToggle.addEventListener('click', () => {
            const currentTheme = document.documentElement.getAttribute('data-theme');
            setTheme(currentTheme === 'dark' ? 'light' : 'dark');
        });

        setTheme(localStorage.getItem('theme') || 'dark');

        let currentEditGoalId = null;

        // Load goals on page load
        window.addEventListener('DOMContentLoaded', function() {
            loadGoals();
        });

        // Load all goals from database
        function loadGoals() {
            fetch('fetch_goals.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayGoals(data.goals);
                    } else {
                        console.error(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error loading goals:', error);
                });
        }

        // Display goals in the UI
        function displayGoals(goals) {
            const goalList = document.getElementById('goalList');
            goalList.innerHTML = '';

            if (goals.length === 0) {
                goalList.innerHTML = '<p class="text-center text-muted mt-4">No goals yet. Create your first goal!</p>';
                return;
            }

            goals.forEach(goal => {
                const goalCard = createGoalCard(goal);
                goalList.innerHTML += goalCard;
            });
        }

        // Create goal card HTML
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
                        <div class="progress-bar ${progressBarClass}" style="width: ${progress}%"></div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <button class="btn btn-sm btn-outline-primary rounded-pill px-3" onclick="updateGoalFunds(${goal.id})">Add Funds</button>
                        <div>
                            <i class="bi bi-pencil text-muted me-2" style="cursor:pointer" onclick="editGoal(${goal.id})"></i>
                            <i class="bi bi-trash text-danger" style="cursor:pointer" onclick="deleteGoal(${goal.id})"></i>
                        </div>
                    </div>
                </div>
            `;
        }

        // Prepare add goal modal
        function prepareAddGoal() {
            currentEditGoalId = null;
            document.getElementById("modalTitle").innerText = "Add New Goal";
            document.getElementById("goalForm").reset();
            document.getElementById("editGoalId").value = "";
            document.getElementById("saveGoalBtn").innerText = "Save Goal";
        }

        // Save goal (add or edit)
        function saveGoal() {
            const goalName = document.getElementById("goalName").value.trim();
            const goalTarget = parseFloat(document.getElementById("goalTarget").value);
            const goalSaved = parseFloat(document.getElementById("goalSaved").value) || 0;

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

            if (currentEditGoalId) {
                // Edit existing goal
                formData.append('action', 'edit_goal');
                formData.append('goalId', currentEditGoalId);
                formData.append('goalName', goalName);
                formData.append('targetAmount', goalTarget);
                formData.append('savedAmount', goalSaved);

                fetch('update_goals.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        bootstrap.Modal.getInstance(document.getElementById("goalModal")).hide();
                        loadGoals();
                        alert('Goal updated successfully!');
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred');
                });

            } else {
                // Add new goal
                formData.append('goalName', goalName);
                formData.append('targetAmount', goalTarget);
                formData.append('savedAmount', goalSaved);

                fetch('add_goals.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        bootstrap.Modal.getInstance(document.getElementById("goalModal")).hide();
                        loadGoals();
                        alert('Goal added successfully!');
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred');
                });
            }
        }

        // Update goal funds
        function updateGoalFunds(goalId) {
            const addMore = prompt("Enter amount to add to savings:");
            if (!addMore || isNaN(addMore) || parseFloat(addMore) <= 0) {
                alert("Please enter a valid amount");
                return;
            }

            const formData = new FormData();
            formData.append('action', 'add_funds');
            formData.append('goalId', goalId);
            formData.append('additionalAmount', parseFloat(addMore));

            fetch('update_goals.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadGoals();
                    alert('Funds added successfully!');
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred');
            });
        }

        // Edit goal
        function editGoal(goalId) {
            fetch('Bachat-Buddy/Bachat-Buddy/Bachat-Buddy/backend/goals/fetch_goals.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const goal = data.goals.find(g => g.id === goalId);
                        if (goal) {
                            currentEditGoalId = goalId;
                            document.getElementById("goalName").value = goal.goal_name;
                            document.getElementById("goalTarget").value = goal.target_amount;
                            document.getElementById("goalSaved").value = goal.saved_amount;
                            document.getElementById("modalTitle").innerText = "Edit Goal";
                            document.getElementById("saveGoalBtn").innerText = "Update Goal";
                            const modal = new bootstrap.Modal(document.getElementById('goalModal'));
                            modal.show();
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }

        // Delete goal
        function deleteGoal(goalId) {
            if (!confirm("Are you sure you want to delete this goal?")) {
                return;
            }

            const formData = new FormData();
            formData.append('action', 'delete_goal');
            formData.append('goalId', goalId);

            fetch('update_goals.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadGoals();
                    alert('Goal deleted successfully!');
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred');
            });
        }

        // Notification logic
        const notificationBtn = document.getElementById('notificationBtn');
        const notificationDropdown = document.getElementById('notificationDropdown');
        const notificationBadge = document.getElementById('notificationBadge');

        notificationBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            notificationDropdown.classList.toggle('hidden');
        });

        window.addEventListener('click', (e) => {
            if (!notificationBtn.contains(e.target) && !notificationDropdown.contains(e.target)) {
                notificationDropdown.classList.add('hidden');
            }
        });

        function clearNotifications() {
            const list = document.getElementById('notificationList');
            list.innerHTML = `
                <div class="p-4 text-center text-sm text-gray-500">
                    <i class="bi bi-check2-all text-success d-block fs-4 mb-2"></i>
                    All caught up!
                </div>
            `;
            notificationBadge.style.display = 'none';
        }

        function updateNotificationCount(count) {
            if (count > 0) {
                notificationBadge.innerText = count;
                notificationBadge.style.display = 'inline-flex';
            } else {
                notificationBadge.style.display = 'none';
            }
        }

        // Logout
        document.getElementById("logout-btn").addEventListener("click", function() {
            const confirmLogout = confirm("Are you sure you want to logout?");
            if (confirmLogout) {
                window.location.href = "login-pages/login.php";
            }
        });
    </script>
</body>

</html>