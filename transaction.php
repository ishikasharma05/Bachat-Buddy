<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BayChat Buddy | Transactions</title>
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
            --table-header: #f8f9fa;
            --table-row-hover: #f1f5f9;
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
            --table-header: #0f172a;
            --table-row-hover: #2d3748;
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
            transition: 0.3s ease;
            text-decoration: none;
        }

        .sidebar .nav-link:hover {
            background-color: #000;
            color: #fff;
        }

        .sidebar .nav-link i {
            margin-right: 12px;
            font-size: 1.1rem;
        }

        .sidebar .logout {
            padding-left: 15px;
        }

        .brand {
            font-weight: bold;
            font-size: 1.2rem;
            padding-left: 15px;
            margin-bottom: 2rem;
            color: var(--text-main);
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
            border: none;
            cursor: pointer;
            color: var(--text-main);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .profile-info {
            display: flex;
            align-items: center;
            gap: 10px;
            background: var(--input-bg);
            padding: 6px 12px;
            border-radius: 20px;
        }

        .profile-info img {
            width: 36px;
            height: 36px;
            border-radius: 50%;
        }

        .profile-text {
            color: var(--text-main);
        }

        .main-body {
            padding: 2rem;
            overflow-y: auto;
            background-color: var(--bg-main);
            transition: all 0.3s ease;
        }

        .card-custom {
            border-radius: 16px;
            padding: 1.5rem;
            background: var(--bg-card);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.04);
            border: 1px solid var(--border-color);
        }

        .transaction-card {
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            border-radius: 8px;
            background-color: var(--bg-card) !important;
            border: 1px solid var(--border-color);
        }

        .text-muted {
            color: var(--text-muted) !important;
        }

        .table {
            color: var(--text-main) !important;
        }

        .table td,
        .table th {
            vertical-align: middle;
            white-space: nowrap;
            color: var(--text-main) !important;
            border-bottom: 1px solid var(--border-color);
            background-color: transparent !important;
        }

        .table-light {
            background-color: var(--table-header) !important;
            color: var(--text-main) !important;
        }

        .table-hover tbody tr:hover {
            background-color: var(--table-row-hover) !important;
        }

        .footer {
            background: linear-gradient(135deg, #2a9d8f, #4cafef);
            border-radius: 20px 20px 0 0;
        }

        .form-control,
        .form-select {
            background-color: var(--input-bg) !important;
            border: 1px solid var(--border-color) !important;
            color: var(--text-main) !important;
        }

        .form-control::placeholder {
            color: var(--text-muted) !important;
            opacity: 0.8;
        }

        .input-group-text {
            background-color: var(--input-bg) !important;
            border: 1px solid var(--border-color) !important;
            color: var(--text-main) !important;
        }

        .dropdown-menu {
            background-color: var(--bg-card) !important;
            border-color: var(--border-color) !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .dropdown-item {
            color: var(--text-main) !important;
        }

        .dropdown-item:hover {
            background-color: var(--table-row-hover) !important;
        }

        .page-link {
            background-color: var(--bg-card) !important;
            border-color: var(--border-color) !important;
            color: var(--text-main) !important;
            transition: background-color 0.2s ease;
        }

        .page-link:hover {
            background-color: var(--table-row-hover) !important;
            color: var(--text-main) !important;
        }

        .page-item.active .page-link {
            background-color: #2a9d8f !important;
            border-color: #2a9d8f !important;
            color: #ffffff !important;
        }

        .page-item.disabled .page-link {
            background-color: var(--input-bg) !important;
            border-color: var(--border-color) !important;
            color: var(--text-muted) !important;
            cursor: not-allowed;
        }

        /* ================== MOBILE SIDEBAR & HEADER ================== */
        .mobile-sidebar {
            position: fixed;
            top: 0;
            left: -300px;
            width: 250px;
            height: 100vh;
            background-color: var(--sidebar-bg);
            z-index: 1050;
            transition: left 0.3s ease;
            overflow-y: auto;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.3);
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

        .header .btn.d-lg-none {
            background: transparent;
            border: none;
            padding: 0;
        }

        .header .btn.d-lg-none i {
            font-size: 1.8rem;
            color: var(--text-main);
            transition: color 0.3s ease;
        }

        /* Close button in mobile sidebar */
        .mobile-sidebar .btn-close i {
            color: var(--text-main);
            font-size: 1.3rem;
        }

        /* ================== RESPONSIVE SIDEBAR & CONTENT ================== */
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
                margin: 10px 0;
            }
        }

        /* ================== FORM & TABLES ON SMALL SCREENS ================== */
        @media (max-width: 767px) {

            .input-group input,
            .input-group select {
                font-size: 0.9rem;
            }

            .transaction-card .input-group {
                flex-direction: column;
            }

            .transaction-card .input-group .form-control,
            .transaction-card .input-group .form-select,
            .transaction-card .input-group .input-group-text {
                width: 100%;
                margin-bottom: 0.5rem;
            }

            .transaction-card .input-group .input-group-text {
                justify-content: flex-start;
            }
        }

        /* ================== TABLE RESPONSIVENESS ================== */
        .table-responsive {
            overflow-x: auto;
        }

        @media (max-width: 575px) {
            .table thead {
                display: none;
            }

            .table tbody tr {
                display: block;
                margin-bottom: 1rem;
                border: 1px solid var(--border-color);
                border-radius: 8px;
                padding: 0.5rem;
                background-color: var(--bg-card);
            }

            .table tbody td {
                display: flex;
                justify-content: space-between;
                padding: 0.5rem 0;
                white-space: normal;
            }

            .table tbody td::before {
                content: attr(data-label);
                font-weight: 500;
                color: var(--text-muted);
                flex-basis: 50%;
            }

            .table tbody td:last-child {
                justify-content: flex-end;
            }
        }

        /* ================== PAGINATION RESPONSIVENESS ================== */
        @media (max-width: 575px) {
            .pagination {
                flex-wrap: wrap;
            }

            .page-item {
                margin-bottom: 5px;
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
                    <li><a class="nav-link" href="transaction.php" style="background: #3b82f6; color: #fff;"><i class="bi bi-arrow-left-right"></i> Transactions</a></li>
                    <li><a class="nav-link" href="add-entry.php"><i class="bi bi-journal-plus"></i> Add Entry</a></li>
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
                    <li><a class="nav-link" href="transaction.php" style="background: #3b82f6; color: #fff;"><i class="bi bi-arrow-left-right"></i> Transactions</a></li>
                    <li><a class="nav-link" href="add-entry.php"><i class="bi bi-journal-plus"></i> Add Entry</a></li>
                    <li><a class="nav-link" href="goals.php"><i class="bi bi-bullseye"></i> Goals</a></li>
                </ul>
            </div>
        </div>
        <div id="sidebarOverlay" class="sidebar-overlay d-lg-none" onclick="toggleMenu()"></div>

        <div class="main-content">
            <?php include 'components/header.php'; ?>
            <div class="main-body">
                <div class="container py-2">
                    <div class="mb-4">
                        <p class="text-muted ms-1">Manage and track your income and expenses easily</p>
                    </div>

                    <div class="card mb-4 p-3 transaction-card">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                                    <input type="search" class="form-control" id="searchInput" placeholder="Search transactions..." />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-filter"></i></span>
                                    <select class="form-select" id="typeFilter">
                                        <option value="all">All Types</option>
                                        <option value="Income">Income</option>
                                        <option value="Expense">Expense</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-sort-down"></i></span>
                                    <select class="form-select" id="sortFilter">
                                        <option value="date-desc">Newest First</option>
                                        <option value="date-asc">Oldest First</option>
                                        <option value="amount-desc">Amount: High to Low</option>
                                        <option value="amount-asc">Amount: Low to High</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive transaction-card p-3">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Description</th>
                                    <th>Category</th>
                                    <th>Type</th>
                                    <th>Amount</th>
                                    <th style="width: 80px;" class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody id="transactionTableBody"></tbody>
                        </table>
                    </div>

                    <nav class="mt-4 d-flex justify-content-end">
                        <ul class="pagination" id="pagination"></ul>
                    </nav>

                    <?php include 'components/footer.php'; ?>
                </div>
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

        setTheme(localStorage.getItem('theme') || 'light');

        let transactions = [];

        fetch("api/get-transactions.php")
            .then(res => res.json())
            .then(data => {
                transactions = data;
                renderTransactions();
            })
            .catch(() => {
                alert("Failed to load transactions");
            });


        const itemsPerPage = 5;
        let currentPage = 1;

        function renderTransactions() {
            const tbody = document.getElementById('transactionTableBody');
            tbody.innerHTML = '';

            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const typeFilter = document.getElementById('typeFilter').value;
            const sortFilter = document.getElementById('sortFilter').value;

            let filtered = transactions.filter(tx =>
                (typeFilter === 'all' || tx.type === typeFilter) &&
                (tx.description.toLowerCase().includes(searchTerm) || tx.category.toLowerCase().includes(searchTerm))
            );

            filtered.sort((a, b) => {
                if (sortFilter === 'date-desc') return new Date(b.date) - new Date(a.date);
                if (sortFilter === 'date-asc') return new Date(a.date) - new Date(b.date);
                if (sortFilter === 'amount-desc') return b.amount - a.amount;
                if (sortFilter === 'amount-asc') return a.amount - b.amount;
            });

            const start = (currentPage - 1) * itemsPerPage;
            const paginated = filtered.slice(start, start + itemsPerPage);

            for (const tx of paginated) {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                  <td>${tx.date}</td>
                  <td>${tx.description}</td>
                  <td>${tx.category}</td>
                  <td>${tx.type}</td>
                  <td class="${tx.amount < 0 ? 'text-danger' : 'text-success'} fw-bold">${tx.amount < 0 ? '-' : '+'}$${Math.abs(tx.amount)}</td>
                  <td class="text-end">
                    <div class="dropdown">
                        <button class="btn btn-sm btn-link text-decoration-none" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="color: var(--text-main);">
                            <i class="bi bi-three-dots fs-5"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow">
                            <li><button class="dropdown-item" onclick="alert('Edit ${tx.description}')"><i class="bi bi-pencil-square me-2"></i>Edit</button></li>
                            <li><button class="dropdown-item text-danger" onclick="alert('Delete ${tx.description}')"><i class="bi bi-trash me-2"></i>Delete</button></li>
                        </ul>
                    </div>
                  </td>
                `;
                tbody.appendChild(tr);
            }
            renderPagination(filtered.length);
        }

        function renderPagination(totalItems) {
            const pageCount = Math.ceil(totalItems / itemsPerPage);
            const pagination = document.getElementById('pagination');
            pagination.innerHTML = '';

            const prevLi = document.createElement('li');
            prevLi.className = 'page-item ' + (currentPage === 1 ? 'disabled' : '');
            prevLi.innerHTML = `<button class="page-link" onclick="goToPage(${currentPage - 1})">Previous</button>`;
            pagination.appendChild(prevLi);

            for (let i = 1; i <= pageCount; i++) {
                const li = document.createElement('li');
                li.className = 'page-item ' + (i === currentPage ? 'active' : '');
                li.innerHTML = `<button class="page-link" onclick="goToPage(${i})">${i}</button>`;
                pagination.appendChild(li);
            }

            const nextLi = document.createElement('li');
            nextLi.className = 'page-item ' + (currentPage === pageCount ? 'disabled' : '');
            nextLi.innerHTML = `<button class="page-link" onclick="goToPage(${currentPage + 1})">Next</button>`;
            pagination.appendChild(nextLi);
        }

        function goToPage(page) {
            const totalPages = Math.ceil(transactions.length / itemsPerPage);
            if (page < 1 || page > totalPages) return;
            currentPage = page;
            renderTransactions();
        }

        document.getElementById('searchInput').addEventListener('input', () => {
            currentPage = 1;
            renderTransactions();
        });
        document.getElementById('typeFilter').addEventListener('change', () => {
            currentPage = 1;
            renderTransactions();
        });
        document.getElementById('sortFilter').addEventListener('change', () => {
            currentPage = 1;
            renderTransactions();
        });

        renderTransactions();

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
</body>

</html>