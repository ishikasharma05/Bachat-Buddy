<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bachat-Buddy | Add Expense</title>
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
    </style>
    <style>
        body {
            background: linear-gradient(to right, #f8f9fa, #e9ecef);
            font-family: 'Segoe UI', sans-serif;
        }

        .transaction-card {
            max-width: 700px;
            margin: 60px auto;
            border: none;
            border-radius: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            background: white;
        }

        .form-section-title {
            font-weight: 600;
            font-size: 1.2rem;
        }

        .toggle-btns .btn {
            border-radius: 30px;
        }

        .toggle-btns .btn.active {
            color: #fff;
        }
    </style>
    <style>
        .footer {
            background: linear-gradient(135deg, #2a9d8f, #4cafef);
            border-radius: 20px 20px 0 0;
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
                <h4 class="mb-4 text-center text-black">
                    <i class="bi bi-wallet2 me-2"></i>New Transaction
                </h4>

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

            <div class="main-body">
                <div class="container">
                    <div class="card transaction-card">
                        <div class="card-body p-4">

                            <!-- Toggle Buttons -->
                            <div class="d-flex justify-content-center mb-4 toggle-btns">
                                <button type="button" class="btn btn-outline-success me-2 active" id="incomeBtn">
                                    <i class="bi bi-arrow-down-circle me-1"></i>Income
                                </button>
                                <button type="button" class="btn btn-outline-danger" id="expenseBtn">
                                    <i class="bi bi-arrow-up-circle me-1"></i>Expense
                                </button>
                            </div>

                            <!-- Form -->
                            <form id="transactionForm">
                                <input type="hidden" id="transactionType" value="income">

                                <div class="mb-3">
                                    <label class="form-label form-section-title"><i class="bi bi-cash-coin me-1"></i>Amount</label>
                                    <input type="number" class="form-control form-control-lg" placeholder="Enter amount" id="amount" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label form-section-title"><i class="bi bi-tags me-1"></i>Category</label>
                                    <select class="form-select form-select-lg" id="category" required>
                                        <option disabled selected>Select category</option>
                                        <option value="Salary">Salary</option>
                                        <option value="Groceries">Groceries</option>
                                        <option value="Rent">Rent</option>
                                        <option value="Bills">Bills</option>
                                        <option value="Investment">Investment</option>
                                        <option value="Miscellaneous">Miscellaneous</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label form-section-title"><i class="bi bi-calendar-date me-1"></i>Date</label>
                                    <input type="date" class="form-control form-control-lg" id="date" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label form-section-title"><i class="bi bi-card-text me-1"></i>Description</label>
                                    <textarea class="form-control form-control-lg" id="description" rows="2" placeholder="Description (optional)"></textarea>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label form-section-title"><i class="bi bi-hash me-1"></i>Tags</label>
                                    <input type="text" class="form-control form-control-lg" id="tags" placeholder="e.g., food, petrol, bonus">
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="bi bi-check2-circle me-1"></i>Submit Transaction
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- Footer -->
                <footer class="footer mt-5 py-3 text-center text-white">
                    <div class="container">
                        <p class="mb-1 fw-bold"><i class="bi bi-piggy-bank-fill me-2"></i>Bachat Buddy</p>
                        <p class="small mb-0">© 2025 Bachat Buddy | Smart Budget Tracker with AI Guide</p>
                    </div>
                </footer>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const incomeBtn = document.getElementById('incomeBtn');
        const expenseBtn = document.getElementById('expenseBtn');
        const transactionType = document.getElementById('transactionType');

        incomeBtn.addEventListener('click', () => {
            incomeBtn.classList.add('active');
            expenseBtn.classList.remove('active');
            transactionType.value = 'income';
            incomeBtn.classList.replace('btn-outline-success', 'btn-success');
            expenseBtn.classList.replace('btn-danger', 'btn-outline-danger');
        });

        expenseBtn.addEventListener('click', () => {
            expenseBtn.classList.add('active');
            incomeBtn.classList.remove('active');
            transactionType.value = 'expense';
            expenseBtn.classList.replace('btn-outline-danger', 'btn-danger');
            incomeBtn.classList.replace('btn-success', 'btn-outline-success');
        });

        document.getElementById('transactionForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const data = {
                type: transactionType.value,
                amount: document.getElementById('amount').value,
                category: document.getElementById('category').value,
                date: document.getElementById('date').value,
                description: document.getElementById('description').value,
                tags: document.getElementById('tags').value
            };
            console.log("Transaction submitted:", data);
            alert("Transaction added successfully!");
            this.reset();
            incomeBtn.click();
        });
    </script>
</body>

</html>