<?php
// Start session and check authentication
require_once 'components/auth_check.php';
require_once 'config/db.php';

// Fetch all transactions directly in PHP
$user_id = $_SESSION['user_id'];
$transactions = [];
$error = null;

try {
    $stmt = $conn->prepare("
        SELECT id, type, amount, category, description, tags, date, created_at
        FROM transactions 
        WHERE user_id = ? 
        ORDER BY date DESC, created_at DESC
    ");
    
    if ($stmt) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            $transactions[] = $row;
        }
        
        $stmt->close();
    } else {
        $error = "Failed to prepare query";
    }
} catch (Exception $e) {
    $error = "Database error: " . $e->getMessage();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bachat Buddy | Transactions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        }

        @media (max-width: 767px) {
            .transaction-card .input-group {
                flex-direction: column;
            }

            .transaction-card .input-group .form-control,
            .transaction-card .input-group .form-select {
                width: 100%;
                margin-bottom: 0.5rem;
            }
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
                        <h3>Transactions</h3>
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
                                        <option value="income">Income</option>
                                        <option value="expense">Expense</option>
                                        <option value="savings">Savings</option>
                                        <option value="withdraw_savings">Withdraw Savings</option>
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
                            <tbody id="transactionTableBody">
                                <?php if ($error): ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-danger">
                                            <i class="bi bi-exclamation-triangle me-2"></i><?php echo htmlspecialchars($error); ?>
                                        </td>
                                    </tr>
                                <?php elseif (empty($transactions)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">
                                            <i class="bi bi-inbox me-2"></i>No transactions yet. Add your first transaction!
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
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

    <!-- Embed transactions data directly in JavaScript -->
    <script>
        // Data loaded directly from PHP - NO API CALLS!
        const transactions = <?php echo json_encode($transactions); ?>;
        console.log('Loaded transactions:', transactions.length);
    </script>

    <script>
        function toggleMenu() {
            document.getElementById("mobileSidebar").classList.toggle("active");
            document.getElementById("sidebarOverlay").classList.toggle("active");
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

        // Pagination and filtering settings
        const itemsPerPage = 10;
        let currentPage = 1;

        // Initial render
        renderTransactions();

        function renderTransactions() {
            const tbody = document.getElementById('transactionTableBody');
            tbody.innerHTML = '';

            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const typeFilter = document.getElementById('typeFilter').value;
            const sortFilter = document.getElementById('sortFilter').value;

            // Filter transactions
            let filtered = transactions.filter(tx => {
                const matchesType = typeFilter === 'all' || tx.type.toLowerCase().replace(' ', '_') === typeFilter;
                const matchesSearch = 
                    (tx.description && tx.description.toLowerCase().includes(searchTerm)) || 
                    (tx.category && tx.category.toLowerCase().includes(searchTerm)) ||
                    (tx.tags && tx.tags.toLowerCase().includes(searchTerm));
                return matchesType && matchesSearch;
            });

            // Sort transactions
            filtered.sort((a, b) => {
                if (sortFilter === 'date-desc') return new Date(b.date) - new Date(a.date);
                if (sortFilter === 'date-asc') return new Date(a.date) - new Date(b.date);
                if (sortFilter === 'amount-desc') return Math.abs(b.amount) - Math.abs(a.amount);
                if (sortFilter === 'amount-asc') return Math.abs(a.amount) - Math.abs(b.amount);
                return 0;
            });

            // Paginate
            const start = (currentPage - 1) * itemsPerPage;
            const paginated = filtered.slice(start, start + itemsPerPage);

            if (paginated.length === 0 && filtered.length === 0) {
                tbody.innerHTML = `<tr><td colspan="6" class="text-center text-muted">
                    <i class="bi bi-search me-2"></i>No transactions match your filters
                </td></tr>`;
                document.getElementById('pagination').innerHTML = '';
                return;
            }

            // Render transaction rows
            for (const tx of paginated) {
                const tr = document.createElement('tr');
                
                // Format date
                const dateObj = new Date(tx.date);
                const formattedDate = dateObj.toLocaleDateString('en-US', { 
                    year: 'numeric', 
                    month: 'short', 
                    day: 'numeric' 
                });
                
                // Badge colors
                let badgeClass = 'bg-secondary';
                if (tx.type === 'income') badgeClass = 'bg-success';
                else if (tx.type === 'expense') badgeClass = 'bg-danger';
                else if (tx.type === 'savings') badgeClass = 'bg-primary';
                else if (tx.type === 'withdraw_savings') badgeClass = 'bg-warning';
                
                const typeDisplay = tx.type.replace('_', ' ').split(' ').map(word => 
                    word.charAt(0).toUpperCase() + word.slice(1)
                ).join(' ');
                
                tr.innerHTML = `
                  <td data-label="Date">${formattedDate}</td>
                  <td data-label="Description">${tx.description || '<em class="text-muted">No description</em>'}</td>
                  <td data-label="Category">
                    <span class="badge bg-light text-dark">${tx.category || 'Uncategorized'}</span>
                  </td>
                  <td data-label="Type">
                    <span class="badge ${badgeClass}">${typeDisplay}</span>
                  </td>
                  <td data-label="Amount" class="${tx.type === 'expense' ? 'text-danger' : 'text-success'} fw-bold">
                    ${tx.type === 'expense' ? '-' : '+'}$${Math.abs(parseFloat(tx.amount)).toFixed(2)}
                  </td>
                  <td class="text-end">
                    <div class="dropdown">
                        <button class="btn btn-sm btn-link text-decoration-none" type="button" data-bs-toggle="dropdown" style="color: var(--text-main);">
                            <i class="bi bi-three-dots-vertical fs-5"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow">
                            <li><a class="dropdown-item" href="edit-entry.php?id=${tx.id}">
                                <i class="bi bi-pencil-square me-2"></i>Edit
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><button class="dropdown-item text-danger" onclick="deleteTransaction(${tx.id}, '${(tx.description || 'this transaction').replace(/'/g, "\\'")}')">
                                <i class="bi bi-trash me-2"></i>Delete
                            </button></li>
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

            if (pageCount <= 1) return;

            const prevLi = document.createElement('li');
            prevLi.className = 'page-item ' + (currentPage === 1 ? 'disabled' : '');
            prevLi.innerHTML = `<button class="page-link" onclick="goToPage(${currentPage - 1})">
                <i class="bi bi-chevron-left"></i>
            </button>`;
            pagination.appendChild(prevLi);

            let startPage = Math.max(1, currentPage - 2);
            let endPage = Math.min(pageCount, startPage + 4);
            
            if (endPage - startPage < 4) {
                startPage = Math.max(1, endPage - 4);
            }

            for (let i = startPage; i <= endPage; i++) {
                const li = document.createElement('li');
                li.className = 'page-item ' + (i === currentPage ? 'active' : '');
                li.innerHTML = `<button class="page-link" onclick="goToPage(${i})">${i}</button>`;
                pagination.appendChild(li);
            }

            const nextLi = document.createElement('li');
            nextLi.className = 'page-item ' + (currentPage === pageCount ? 'disabled' : '');
            nextLi.innerHTML = `<button class="page-link" onclick="goToPage(${currentPage + 1})">
                <i class="bi bi-chevron-right"></i>
            </button>`;
            pagination.appendChild(nextLi);
        }

        function goToPage(page) {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const typeFilter = document.getElementById('typeFilter').value;
            
            let filtered = transactions.filter(tx => {
                const matchesType = typeFilter === 'all' || tx.type.toLowerCase().replace(' ', '_') === typeFilter;
                const matchesSearch = 
                    (tx.description && tx.description.toLowerCase().includes(searchTerm)) || 
                    (tx.category && tx.category.toLowerCase().includes(searchTerm)) ||
                    (tx.tags && tx.tags.toLowerCase().includes(searchTerm));
                return matchesType && matchesSearch;
            });
            
            const totalPages = Math.ceil(filtered.length / itemsPerPage);
            if (page < 1 || page > totalPages) return;
            
            currentPage = page;
            renderTransactions();
            
            document.querySelector('.table-responsive').scrollIntoView({ behavior: 'smooth', block: 'start' });
        }

        function deleteTransaction(id, description) {
            if (!confirm(`Are you sure you want to delete "${description}"?`)) return;
            
            fetch('backend/transactions/delete_transaction.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ id: id })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    // Reload page to refresh data
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to delete transaction. Please try again.');
            });
        }

        // Event listeners for filters
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
    </script>
</body>

</html>