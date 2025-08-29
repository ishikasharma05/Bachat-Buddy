<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BayChat Buddy | Transactions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background-color: #f2f6f9;
        }

        .layout {
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        .sidebar {
            width: 250px;
            background-color: #fff;
            border-right: 1px solid #eee;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 2rem 1rem;
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

        .sidebar .logout {
            padding-left: 15px;
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
        }

        .search-box {
            background: #f5f5f5;
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
        }

        .notification {
            background: #f5f5f5;
            padding: 8px;
            border-radius: 10px;
        }

        .profile-info {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #f5f5f5;
            padding: 6px 12px;
            border-radius: 20px;
        }

        .profile-info img {
            width: 36px;
            height: 36px;
            border-radius: 50%;
        }

        .main-body {
            padding: 2rem;
            overflow-y: auto;
            background-color: #f2f6f9;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: 2fr 1.2fr;
            gap: 2rem;
        }

        .grid-2 {
            display: grid;
            grid-template-columns: 1.1fr 1fr;
            gap: 2rem;
            margin-top: 2rem;
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
        }

        .tabs .active {
            color: #000;
        }

        .donut-container {
            position: relative;
            width: 160px;
            height: 160px;
            margin-right: 2rem;
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

        .legend-label {
            font-size: 0.9rem;
            color: #555;
        }

        .legend-value {
            font-weight: bold;
        }

        .main-content {
            height: 100vh;
            overflow-y: auto;
        }
    </style>
    <style>
        body {
            background-color: #f8f9fa;
        }

        .transaction-card {
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            border-radius: 8px;
        }

        .table td,
        .table th {
            vertical-align: middle;
            white-space: nowrap;
        }

        .btn-edit {
            background-color: #198754;
            color: white;
        }

        .btn-delete {
            background-color: #dc3545;
            color: white;
        }
    </style>
</head>

<body>
    <div class="layout">
        <div class="sidebar">
            <div>
                <div class="brand d-flex align-items-center mb-4">
                   <i class="bi bi-piggy-bank me-2"></i> Bachat-Buddy
                </div>
                <ul class="nav flex-column gap-2">
                    <li><a class="nav-link active" href="index.php"><i class="bi bi-speedometer2"></i> Dashboard</a></li>

                    <li><a class="nav-link" href="profile.php"><i class="bi bi-person-circle"></i> Profile</a></li>

                    <li><a class="nav-link" href="transaction.php"><i class="bi bi-arrow-left-right"></i> Transactions</a></li>

                    <li><a class="nav-link" href="add-entry.php"><i class="bi bi-journal-plus"></i> Add Entry</a></li>

                    <li><a class="nav-link" href="reports.php"><i class="bi bi-bar-chart"></i> Reports</a></li>

                    <li><a class="nav-link" href="budgets.php"><i class="bi bi-wallet2"></i> Budgets</a></li>

                    <li><a class="nav-link" href="goals.php"><i class="bi bi-bullseye"></i> Goals</a></li>

                </ul>
            </div>
            <div class="logout mb-3">
                <a class="nav-link" href="#"><i class="bi bi-box-arrow-left"></i>Log out</a>
            </div>
        </div>

        <div class="main-content">
            <div class="header">
                <h2 class="d-flex align-items-center mb-1">
                    <i class="bi bi-currency-dollar me-2 text-primary"></i> Transactions
                </h2>
                <div class="d-flex align-items-center gap-3">
                    <div class="search-box">
                        <i class="bi bi-search me-2"></i>
                        <input type="text" placeholder="Search..." />
                    </div>
                    <div class="notification">
                        <i class="bi bi-bell"></i>
                    </div>
                    <div class="profile-info">
                        <div class="bg-secondary text-white rounded-circle d-flex justify-content-center align-items-center" style="width: 36px; height: 36px;">
                            <i class="bi bi-person-circle fs-5"></i>
                        </div>
                        <div class="profile-text">
                            <div class="fw-bold">Sajib Das Supriyo</div>
                            <small>supriyoosajib@gmail.com</small>
                        </div>
                    </div>

                </div>
            </div>

            <div class="main-content">
                <div class="container py-5">
                    <div class="mb-4">
                        <p class="text-muted ms-4">Manage and track your income and expenses easily</p>
                    </div>


                    <!-- Filters -->
                    <div class="card mb-4 p-3 transaction-card">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                                    <input type="search" class="form-control" id="searchInput" placeholder="Search transactions..." />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="bi bi-filter"></i></span>
                                    <select class="form-select" id="typeFilter">
                                        <option value="all">All Types</option>
                                        <option value="Income">Income</option>
                                        <option value="Expense">Expense</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="bi bi-sort-down"></i></span>
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

                    <!-- Transactions Table -->
                    <div class="table-responsive transaction-card p-3 bg-white">
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

                    <!-- Pagination -->
                    <nav class="mt-4 d-flex justify-content-end">
                        <ul class="pagination" id="pagination"></ul>
                    </nav>
                </div>
            </div>
        </div>


        <!-- Bootstrap JS and Transaction Logic -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            const transactions = [{
                    date: '2025-08-01',
                    description: 'Salary',
                    category: 'Income',
                    type: 'Income',
                    amount: 3000
                },
                {
                    date: '2025-08-02',
                    description: 'Groceries',
                    category: 'Food',
                    type: 'Expense',
                    amount: -150
                },
                {
                    date: '2025-08-03',
                    description: 'Freelance',
                    category: 'Income',
                    type: 'Income',
                    amount: 800
                },
                {
                    date: '2025-08-04',
                    description: 'Transport',
                    category: 'Travel',
                    type: 'Expense',
                    amount: -60
                },
                {
                    date: '2025-08-05',
                    description: 'Rent',
                    category: 'Housing',
                    type: 'Expense',
                    amount: -1000
                },
                {
                    date: '2025-08-06',
                    description: 'Investment Return',
                    category: 'Income',
                    type: 'Income',
                    amount: 200
                },
                {
                    date: '2025-08-07',
                    description: 'Dinner Out',
                    category: 'Food',
                    type: 'Expense',
                    amount: -80
                }
            ];

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
          <td class="${tx.amount < 0 ? 'text-danger' : 'text-success'}">${tx.amount < 0 ? '-' : '+'}$${Math.abs(tx.amount)}</td>
         <td class="text-end align-middle">
    <div class="d-flex justify-content-end align-items-center">
      <div class="dropdown">
        <button class="btn btn-link text-dark" type="button" data-bs-toggle="dropdown" aria-expanded="false">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-three-dots" viewBox="0 0 16 16">
            <path d="M3 9.5a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3zm5 0a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3zm5 0a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3z"/>
          </svg>
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
          <li><button class="dropdown-item" onclick="alert('Edit ${tx.description}')"><i class="bi bi-pencil-square me-2"></i>Edit</button></li>
          <li><button class="dropdown-item text-danger" onclick="alert('Delete ${tx.description}')"><i class="bi bi-trash me-2"></i>Delete</button></li>
        </ul>
      </div>
    </div>
  </td>
  </tr>
        `;
                    tbody.appendChild(tr);
                }

                renderPagination(filtered.length);
            }

            function renderPagination(totalItems) {
                const pageCount = Math.ceil(totalItems / itemsPerPage);
                const pagination = document.getElementById('pagination');
                pagination.innerHTML = '';

                // Previous Button
                const prevLi = document.createElement('li');
                prevLi.className = 'page-item ' + (currentPage === 1 ? 'disabled' : '');
                prevLi.innerHTML = `
    <button class="page-link" onclick="goToPage(${currentPage - 1})" ${currentPage === 1 ? 'disabled' : ''}>
      Previous
    </button>`;
                pagination.appendChild(prevLi);

                // Page Number Buttons
                for (let i = 1; i <= pageCount; i++) {
                    const li = document.createElement('li');
                    li.className = 'page-item ' + (i === currentPage ? 'active' : '');
                    li.innerHTML = `<button class="page-link" onclick="goToPage(${i})">${i}</button>`;
                    pagination.appendChild(li);
                }

                // Next Button
                const nextLi = document.createElement('li');
                nextLi.className = 'page-item ' + (currentPage === pageCount ? 'disabled' : '');
                nextLi.innerHTML = `
    <button class="page-link" onclick="goToPage(${currentPage + 1})" ${currentPage === pageCount ? 'disabled' : ''}>
      Next
    </button>`;
                pagination.appendChild(nextLi);
            }

            function goToPage(page) {
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

            // Initial render
            renderTransactions();
        </script>


</body>

</html>