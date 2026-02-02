<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bachat-Buddy | Add Entry</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <link src="components/styles.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background-color: #f2f6f9;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .layout {
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        /* Sidebar Styling */
        .sidebar {
            width: 250px;
            background-color: #fff;
            border-right: 1px solid #eee;
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
            color: #333;
            font-weight: 500;
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
            background-color: #fff;
            padding: 1rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid #eee;
            transition: all 0.3s ease;
        }

        .notification {
            background: #f5f5f5;
            padding: 8px;
            border-radius: 10px;
            transition: background-color 0.3s ease;
        }

        .profile-info {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #f5f5f5;
            padding: 6px 12px;
            border-radius: 20px;
            transition: background-color 0.3s ease;
        }

        .main-body {
            padding: 2rem;
            overflow-y: auto;
            background-color: #f2f6f9;
            transition: background-color 0.3s ease;
        }

        /* Transaction Card Styling */
        .transaction-card {
            max-width: 700px;
            margin: 20px auto;
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            background: white;
            transition: all 0.3s ease;
        }

        .form-section-title {
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: 10px;
            display: block;
        }

        .toggle-btns .btn {
            border-radius: 30px;
            padding: 8px 20px;
            font-weight: 500;
            transition: 0.3s;
        }

        .footer {
            background: linear-gradient(135deg, #2a9d8f, #4cafef);
            border-radius: 20px 20px 0 0;
        }

        /* ===== DARK MODE REFINEMENTS ===== */
        .dark body,
        .dark .main-body {
            background-color: #0f172a !important;
            color: #f8fafc;
        }

        .dark .header,
        .dark .sidebar {
            background-color: #1e293b !important;
            border-color: #334155 !important;
            color: #f8fafc;
        }

        .dark .transaction-card {
            background-color: #1e293b !important;
            border: 1px solid #334155 !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            color: #f8fafc;
        }

        .dark .nav-link {
            color: #cbd5e1 !important;
        }

        .dark .nav-link:hover {
            background-color: #334155 !important;
            color: #fff !important;
        }

        .dark .nav-link.active {
            background-color: #3b82f6 !important;
            color: white !important;
        }

        .dark .form-control,
        .dark .form-select {
            background-color: #0f172a !important;
            border-color: #334155 !important;
            color: #fff !important;
        }

        .dark .form-control::placeholder {
            color: #64748b;
        }

        .dark .notification,
        .dark .profile-info,
        .dark #theme-toggle {
            background-color: #334155 !important;
            color: #f8fafc;
        }

        .dark .text-black {
            color: #f8fafc !important;
        }

        .dark .form-section-title {
            color: #e2e8f0;
        }

        #theme-toggle {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        /* ---------- RESPONSIVE ADJUSTMENTS ---------- */

        /* MOBILE SIDEBAR */
        .mobile-sidebar {
            position: fixed;
            top: 0;
            left: -300px;
            width: 250px;
            height: 100vh;
            background-color: var(--sidebar-bg, #fff);
            z-index: 1050;
            transition: left 0.3s ease;
            overflow-y: auto;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.3);
        }

        .mobile-sidebar.active {
            left: 0;
        }

        /* OVERLAY */
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.4);
            z-index: 1040;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease;
        }

        .sidebar-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        /* HAMBURGER ICON IN HEADER */
        .header .btn.d-lg-none {
            background: transparent;
            border: none;
            padding: 0;
        }

        .header .btn.d-lg-none i {
            font-size: 1.8rem;
            color: var(--text-main, #333);
            transition: color 0.3s ease;
        }

        /* DARK MODE FIX FOR HAMBURGER */
        .dark .header .btn.d-lg-none i {
            color: var(--text-main, #f8fafc);
        }

        /* CLOSE BUTTON IN MOBILE SIDEBAR */
        .mobile-sidebar .btn-close i {
            color: var(--text-main, #333);
        }

        .dark .mobile-sidebar .btn-close i {
            color: var(--text-main, #f8fafc);
        }

        /* RESPONSIVE SIDEBAR + MAIN CONTENT */
        @media (max-width: 991px) {
            .sidebar.d-lg-block {
                display: none;
            }

            .main-content {
                flex: 1;
                width: 100%;
                overflow: hidden;
            }

            .main-body {
                padding: 1rem;
            }

            .transaction-card {
                margin: 10px;
            }
        }

        /* FORM ELEMENTS ON SMALLER SCREENS */
        @media (max-width: 767px) {
            .toggle-btns .btn {
                flex: 1 1 45%;
                margin-bottom: 5px;
            }

            .form-label.form-section-title {
                font-size: 0.95rem;
            }

            .form-control.form-control-lg {
                font-size: 0.95rem;
                padding: 10px;
            }
        }

        /* PAGE HEADER ADJUSTMENTS FOR MOBILE */
        .page-header {
            padding: 20px 15px;
        }

        .page-header h2 {
            font-size: 1.4rem;
        }

        .page-header p {
            font-size: 0.9rem;
        }

        /* BUTTONS FULL WIDTH ON MOBILE */
        @media (max-width: 575px) {
            .d-grid .btn {
                width: 100%;
                font-size: 0.95rem;
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
                    <li><a class="nav-link" href="index.php"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
                    <li><a class="nav-link" href="profile.php"><i class="bi bi-person-circle"></i> Profile</a></li>
                    <li><a class="nav-link" href="transaction.php"><i class="bi bi-arrow-left-right"></i> Transactions</a></li>
                    <li><a class="nav-link" href="add-entry.php" style="background: #3b82f6; color: #fff;"><i class="bi bi-journal-plus"></i> Add Entry</a></li>
                    <li><a class="nav-link" href="goals.php"><i class="bi bi-bullseye"></i> Goals</a></li>
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
                    <li><a class="nav-link" href="add-entry.php" style="background: #3b82f6; color: #fff;"><i class="bi bi-journal-plus"></i> Add Entry</a></li>
                    <li><a class="nav-link" href="goals.php"><i class="bi bi-bullseye"></i> Goals</a></li>
                </ul>
            </div>
        </div>
        <div id="sidebarOverlay" class="sidebar-overlay d-lg-none" onclick="toggleMenu()"></div>
        <div class="main-content">
            <?php include 'components/header.php'; ?>

            <div class="main-body">
                <div class="container">
                    <div class="card transaction-card">
                        <div class="card-body p-4 p-md-5">

                            <div class="d-flex justify-content-center mb-5 toggle-btns flex-wrap gap-2">
                                <button type="button" class="btn btn-outline-success active" id="incomeBtn">
                                    <i class="bi bi-arrow-down-circle me-1"></i>Income
                                </button>
                                <button type="button" class="btn btn-outline-danger" id="expenseBtn">
                                    <i class="bi bi-arrow-up-circle me-1"></i>Expense
                                </button>
                                <button type="button" class="btn btn-outline-primary" id="savingsBtn">
                                    <i class="bi bi-piggy-bank me-1"></i>Savings
                                </button>
                                <button type="button" class="btn btn-outline-warning" id="withdrawBtn">
                                    <i class="bi bi-arrow-counterclockwise me-1"></i>Withdraw
                                </button>
                            </div>

                            <form id="transactionForm">
                                <input type="hidden" id="transactionType" value="income">

                                <div class="row">
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label form-section-title"><i class="bi bi-cash-coin me-2"></i>Amount</label>
                                        <input type="number" class="form-control form-control-lg rounded-3" placeholder="0.00" id="amount" required>
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label form-section-title"><i class="bi bi-calendar-date me-2"></i>Date</label>
                                        <input type="date" class="form-control form-control-lg rounded-3" id="date" required>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label form-section-title"><i class="bi bi-tags me-2"></i>Category</label>
                                    <select class="form-select form-select-lg rounded-3" id="category" required>
                                        <option disabled selected>Select category</option>
                                        <option value="Salary">Salary</option>
                                        <option value="Groceries">Groceries</option>
                                        <option value="Rent">Rent</option>
                                        <option value="Bills">Bills</option>
                                        <option value="Investment">Investment</option>
                                        <option value="Miscellaneous">Miscellaneous</option>
                                    </select>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label form-section-title"><i class="bi bi-card-text me-2"></i>Description</label>
                                    <textarea class="form-control form-control-lg rounded-3" id="description" rows="2" placeholder="What was this for?"></textarea>
                                </div>

                                <div class="mb-5">
                                    <label class="form-label form-section-title"><i class="bi bi-hash me-2"></i>Tags</label>
                                    <input type="text" class="form-control form-control-lg rounded-3" id="tags" placeholder="e.g., food, travel, office">
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-lg rounded-pill shadow-sm">
                                        <i class="bi bi-check2-circle me-2"></i>Save Transaction
                                    </button>
                                </div>
                            </form>
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
        // Theme Toggle Logic
        const themeToggleBtn = document.getElementById('theme-toggle');
        const setTheme = (isDark) => {
            if (isDark) {
                document.documentElement.classList.add('dark');
                themeToggleBtn.innerHTML = '<i class="fas fa-sun"></i>';
            } else {
                document.documentElement.classList.remove('dark');
                themeToggleBtn.innerHTML = '<i class="fas fa-moon"></i>';
            }
        };

        // Load saved preference
        const savedTheme = localStorage.getItem('theme') || 'light';
        setTheme(savedTheme === 'dark');

        themeToggleBtn.addEventListener('click', () => {
            const isNowDark = !document.documentElement.classList.contains('dark');
            localStorage.setItem('theme', isNowDark ? 'dark' : 'light');
            setTheme(isNowDark);
        });

        // Transaction Type Selection Logic
        const incomeBtn = document.getElementById('incomeBtn');
        const expenseBtn = document.getElementById('expenseBtn');
        const savingsBtn = document.getElementById('savingsBtn');
        const withdrawBtn = document.getElementById('withdrawBtn');
        const transactionType = document.getElementById('transactionType');
        const allBtns = [incomeBtn, expenseBtn, savingsBtn, withdrawBtn];

        function resetButtons() {
            allBtns.forEach(btn => {
                btn.classList.remove('active', 'btn-success', 'btn-danger', 'btn-primary', 'btn-warning');
                if (btn === incomeBtn) btn.classList.add('btn-outline-success');
                if (btn === expenseBtn) btn.classList.add('btn-outline-danger');
                if (btn === savingsBtn) btn.classList.add('btn-outline-primary');
                if (btn === withdrawBtn) btn.classList.add('btn-outline-warning');
            });
        }

        incomeBtn.addEventListener('click', () => {
            resetButtons();
            incomeBtn.classList.add('active', 'btn-success');
            transactionType.value = 'income';
        });

        expenseBtn.addEventListener('click', () => {
            resetButtons();
            expenseBtn.classList.add('active', 'btn-danger');
            transactionType.value = 'expense';
        });

        savingsBtn.addEventListener('click', () => {
            resetButtons();
            savingsBtn.classList.add('active', 'btn-primary');
            transactionType.value = 'savings';
        });

        withdrawBtn.addEventListener('click', () => {
            resetButtons();
            withdrawBtn.classList.add('active', 'btn-warning');
            transactionType.value = 'withdraw-savings';
        });

        // Form Handling

        // Set default date to today on load
        document.getElementById('date').valueAsDate = new Date();

        // --- Notification Applet Logic ---
        const notificationBtn = document.getElementById('notificationBtn');
        const notificationDropdown = document.getElementById('notificationDropdown');
        const notificationBadge = document.getElementById('notificationBadge');

        // 1. Toggle visibility when clicking the bell
        notificationBtn.addEventListener('click', (e) => {
            e.stopPropagation(); // Prevents immediate closing
            notificationDropdown.classList.toggle('hidden');
        });

        // 2. Close the applet if the user clicks anywhere else on the page
        window.addEventListener('click', (e) => {
            if (!notificationBtn.contains(e.target) && !notificationDropdown.contains(e.target)) {
                notificationDropdown.classList.add('hidden');
            }
        });

        // 3. Clear Notifications Function
        function clearNotifications() {
            const list = document.getElementById('notificationList');
            list.innerHTML = `
        <div class="p-4 text-center text-sm text-gray-500">
            <i class="bi bi-check2-all text-success d-block fs-4 mb-2"></i>
            All caught up!
        </div>
    `;
            // Hide the badge count
            notificationBadge.style.display = 'none';
        }

        // 4. (Optional) Function to update the number dynamically from other parts of your app
        function updateNotificationCount(count) {
            if (count > 0) {
                notificationBadge.innerText = count;
                notificationBadge.style.display = 'inline-flex';
            } else {
                notificationBadge.style.display = 'none';
            }
        }

        // --- Logout Confirmation Logic ---
        document.getElementById("logout-btn").addEventListener("click", function() {
            // Asks for user permission
            const confirmLogout = confirm("Are you sure you want to logout?");

            // If the user clicks 'OK', it redirects
            if (confirmLogout) {
                window.location.href = "login-pages/login.php";
            }
            // If they click 'Cancel', nothing happens and they stay on the page
        });
    </script>

    <script>
        document.getElementById('transactionForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData();
            formData.append("type", transactionType.value);
            formData.append("amount", document.getElementById('amount').value);
            formData.append("category", document.getElementById('category').value);
            formData.append("date", document.getElementById('date').value);
            formData.append("description", document.getElementById('description').value);
            formData.append("tags", document.getElementById('tags').value);

            fetch("api/add-transaction.php", {
                    method: "POST",
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        alert("Transaction added successfully!");
                        this.reset();
                        incomeBtn.click();
                        document.getElementById('date').valueAsDate = new Date();
                    } else {
                        alert(data.message || "Something went wrong");
                    }
                })
                .catch(() => alert("Server error"));
        });
    </script>

</body>

</html>