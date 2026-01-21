<?php
session_start();
require_once "../config/db.php";

/* 🔐 LOGIN CHECK */
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];

/* 💰 TOTAL INCOME */
$incomeStmt = $conn->prepare(
    "SELECT COALESCE(SUM(amount),0) 
     FROM transactions 
     WHERE user_id = ? AND category = 'income'"
);
$incomeStmt->bind_param("i", $userId);
$incomeStmt->execute();
$incomeStmt->bind_result($totalIncome);
$incomeStmt->fetch();
$incomeStmt->close();

/* 💸 TOTAL EXPENSE */
$expenseStmt = $conn->prepare(
    "SELECT COALESCE(SUM(amount),0) 
     FROM transactions 
     WHERE user_id = ? AND category = 'expense'"
);
$expenseStmt->bind_param("i", $userId);
$expenseStmt->execute();
$expenseStmt->bind_result($totalExpense);
$expenseStmt->fetch();
$expenseStmt->close();

/* 💾 SAVINGS / BALANCE */
$totalSavings = $totalIncome - $totalExpense;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bachat-Buddy </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="components/style.css">
    <script src="components/java.js" defer></script>
</head>
<style>
    .main-body {
        padding: 2rem;
        overflow-y: auto;
        background-color: #f2f6f9;
        transition: background-color 0.3s ease;
    }

    /* 4 summary boxes row */
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
        transition: background-color 0.3s ease, transform 0.2s;
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
        transition: background-color 0.3s ease;
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

    /* Styles for the Expense Tips Section */
    .insight-pill {
        background: #fff3cd;
        border-left: 4px solid #ffc107;
        padding: 12px;
        border-radius: 8px;
        margin-bottom: 10px;
        font-size: 0.9rem;
    }

    .dark .insight-pill {
        background: #2d2d1a;
        color: #ffe08a;
        border-left-color: #ffc107;
    }
</style>

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
                    <button onclick="toggleMenu()" class="btn-close border-0 bg-transparent text-muted fs-4">
                        <i class="bi bi-x-lg"></i>
                    </button>
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
                        <h4 style="color:#16a34a;">
                            ₹<?= number_format($totalIncome, 2) ?>
                        </h4>

                    </div>

                    <div class="summary-card">
                        <div class="summary-card-accent" style="background:#f8d7da;"></div>
                        <p class="mb-1 text-muted">Expenses</p>
                        <h4 style="color:#dc2626;">
                            ₹<?= number_format($totalExpense, 2) ?>
                        </h4>

                    </div>

                    <div class="summary-card">
                        <div class="summary-card-accent" style="background:#a8c6ff;"></div>
                        <p class="mb-1 text-muted">Savings</p>
                        <h4 style="color:#2563eb;">
                            ₹<?= number_format($totalSavings, 2) ?>
                        </h4>

                    </div>

                    <div class="summary-card">
                        <div class="summary-card-accent" style="background:#f4ab6a;"></div>
                        <p class="mb-1 text-muted">Balance</p>
                        <h4 style="color:#2563eb;">
                            ₹<?= number_format($totalSavings, 2) ?>
                        </h4>

                    </div>
                </div>

                <div class="dashboard-two-cols">
                    <div class="card-custom">
                        <div class="tab-header">
                            <h6 class="fw-bold">Monthly Details</h6>
                            <div class="tabs">
                                <span class="active">Income</span>
                                <span>Expenses</span>
                            </div>
                        </div>
                        <canvas id="monthlyChart"></canvas>
                    </div>

                    <div class="card-custom">
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <h6 class="fw-bold">Expense Summary</h6>
                            <select id="monthSelect" class="form-select form-select-sm w-auto">
                                <option value="Jan">January</option>
                                <option value="Feb">February</option>
                                <option value="Mar">March</option>
                                <option value="Apr" selected>April</option>
                                <option value="May">May</option>
                                <option value="Jun">June</option>
                                <option value="Jul">July</option>
                                <option value="Aug">August</option>
                                <option value="Sep">September</option>
                                <option value="Oct">October</option>
                                <option value="Nov">November</option>
                                <option value="Dec">December</option>
                            </select>
                        </div>
                        <div class="d-flex">
                            <div class="donut-container">
                                <canvas id="expenseDonut"></canvas>
                                <div class="donut-center-text">
                                    <small class="text-muted">Total</small>
                                    <div id="donutTotalAmount" class="fw-bold">₹8,900</div>
                                </div>
                            </div>
                            <div class="d-flex flex-wrap gap-3">
                                <div class="d-flex flex-column gap-2">
                                    <div><span class="legend-dot" style="background:#A7C7FF"></span> Shopping<br><span
                                            class="legend-value small">₹2,650</span></div>
                                    <div><span class="legend-dot" style="background:#C6E2FF"></span> Fun<br><span
                                            class="legend-value small">₹1,350</span></div>
                                    <div><span class="legend-dot" style="background:#F9D5E5"></span> Kids<br><span
                                            class="legend-value small">₹1,950</span></div>
                                </div>
                                <div class="d-flex flex-column gap-2">
                                    <div><span class="legend-dot" style="background:#EAC8F2"></span> Vehicle<br><span
                                            class="legend-value small">₹1,850</span></div>
                                    <div><span class="legend-dot" style="background:#FDD9C1"></span> House<br><span
                                            class="legend-value small">₹850</span></div>
                                    <div><span class="legend-dot" style="background:#C6F8D5"></span> Insure<br><span
                                            class="legend-value small">₹250</span></div>
                                </div>
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
                                        <div class="col-md-6 mb-3 mb-md-0">
                                            <p class="text-muted mb-2">Expense Categories</p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <p class="mb-1">Food</p>
                                                </div>
                                                <div class="text-end">
                                                    <span class="fw-semibold">₹2,000</span>
                                                    <span class="text-muted ms-1">(100.0%)</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="text-muted mb-2">Recent Transactions</p>
                                            <div class="d-flex justify-content-between align-items-center mb-2 px-3 py-2 rounded-3" style="background:#f8fafc;">
                                                <span>food</span>
                                                <span class="text-danger fw-semibold">-₹2,000</span>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center px-3 py-2 rounded-3" style="background:#f8fafc;">
                                                <span>salary</span>
                                                <span class="text-success fw-semibold">+₹20,000</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-5 ps-md-4">
                                    <h5 class="fw-semibold mb-3"><i class="fa-solid fa-lightbulb text-warning me-2"></i>Bachat Insights</h5>
                                    <div id="expenseTipsContainer">
                                        <div class="insight-pill">
                                            Calculating your personalized tips...
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php include 'components/footer.php'; ?>
            </div>
        </div>
    </div>


    <script>
        const sidebar = document.querySelector('.sidebar');
        const hamburgerBtn = document.getElementById('hamburgerBtn');
        const overlay = document.getElementById('sidebarOverlay');

        if (hamburgerBtn) {
            hamburgerBtn.addEventListener('click', () => {
                sidebar.classList.toggle('active');
                overlay.classList.toggle('show');
            });
        }

        if (overlay) {
            overlay.addEventListener('click', () => {
                sidebar.classList.remove('active');
                overlay.classList.remove('show');
            });
        }

        /* Auto close sidebar on resize to desktop */
        window.addEventListener('resize', () => {
            if (window.innerWidth > 991) {
                sidebar.classList.remove('active');
                overlay.classList.remove('show');
            }
        });
    </script>
    <script>
        function toggleMenu() {
            document.getElementById("mobileSidebar").classList.toggle("active");
            document.getElementById("sidebarOverlay").classList.toggle("active");
            document.body.classList.toggle("sidebar-open");
        }
    </script>


    <script>
        // --- NEW: Dynamic Expense Tips Logic ---
        const generateTips = (dataArray) => {
            const categories = ['Shopping', 'Entertainment', 'Education', 'Vehicle', 'Household', 'Insurance'];
            const tipsContainer = document.getElementById('expenseTipsContainer');
            tipsContainer.innerHTML = ''; // Clear existing

            // Logic: Find the highest spending category
            let maxVal = Math.max(...dataArray);
            let maxIndex = dataArray.indexOf(maxVal);
            let topCategory = categories[maxIndex];

            let tips = [];

            // Tip 1: Top Spending Alert
            tips.push(`<div class="insight-pill"><strong>High Spend:</strong> Your biggest expense is <b>${topCategory}</b>. Consider setting a 10% lower limit here next month.</div>`);

            // Tip 2: Category Specific Advice
            if (topCategory === 'Shopping') tips.push(`<div class="insight-pill"><strong>Tip:</strong> Wait 24 hours before any "Shopping" purchase to avoid impulse buying.</div>`);
            if (topCategory === 'Entertainment') tips.push(`<div class="insight-pill"><strong>Tip:</strong> Look for group discounts or free weekend events to lower fun costs.</div>`);

            // Tip 3: General Savings Advice
            const total = dataArray.reduce((a, b) => a + b, 0);
            if (total > 5000) {
                tips.push(`<div class="insight-pill"><strong>Savings:</strong> You spent over ₹5,000. Putting ₹500 into a SIP now could grow to ₹10,000 in a few years!</div>`);
            }

            tipsContainer.innerHTML = tips.join('');
        };

        // Global chart variables
        let mChart, eDonut;

        const chartData = {
            income: [12000, 8700, 9300, 12861, 11000, 6700, 8900],
            expenses: [7500, 6200, 8100, 5400, 9200, 4300, 6100],
            incomeColors: ['#eac8f2', '#fdd9c1', '#c6defc', '#a8c6ff', '#c6f8d5', '#c0f0fc', '#f9d5e5'],
            expenseColors: ['#fecaca', '#fed7aa', '#fef08a', '#bbf7d0', '#99f6e4', '#bae6fd', '#e9d5ff']
        };

        const donutMonthlyData = {
            'Jan': [1000, 500, 800, 700, 400, 100],
            'Feb': [1200, 600, 700, 900, 300, 200],
            'Mar': [2000, 1000, 1500, 1500, 600, 150],
            'Apr': [2650, 1350, 1950, 1850, 850, 250],
            'May': [2100, 1100, 1400, 1200, 700, 200],
            'Jun': [1500, 800, 1100, 900, 500, 150],
            'Jul': [2200, 1200, 1800, 1600, 800, 250],
            'Aug': [1800, 900, 1300, 1100, 600, 200],
            'Sep': [1900, 1000, 1400, 1300, 700, 210],
            'Oct': [2300, 1150, 1650, 1450, 750, 230],
            'Nov': [2500, 1300, 1900, 1700, 800, 240],
            'Dec': [3000, 1500, 2000, 2000, 1000, 300]
        };

        const initCharts = () => {
            const isDark = document.documentElement.classList.contains('dark');
            const gridColor = isDark ? 'rgba(255, 255, 255, 0.1)' : '#eee';
            const textColor = isDark ? '#94a3b8' : '#666';

            const ctxMonthly = document.getElementById('monthlyChart').getContext('2d');
            const ctxDonut = document.getElementById('expenseDonut').getContext('2d');

            mChart = new Chart(ctxMonthly, {
                type: 'bar',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
                    datasets: [{
                        label: 'Income',
                        data: chartData.income,
                        backgroundColor: chartData.incomeColors,
                        borderRadius: 10,
                        borderSkipped: false
                    }]
                },
                options: {
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                color: textColor
                            }
                        },
                        x: {
                            ticks: {
                                color: textColor
                            }
                        }
                    }
                }
            });

            const currentMonth = document.getElementById('monthSelect').value;
            // Generate Initial Tips
            generateTips(donutMonthlyData[currentMonth]);

            eDonut = new Chart(ctxDonut, {
                type: 'doughnut',
                data: {
                    labels: ['Shopping', 'Entertainment', 'Education', 'Vehicle', 'Household', 'Insurance'],
                    datasets: [{
                        data: donutMonthlyData[currentMonth],
                        backgroundColor: ['#A7C7FF', '#C6E2FF', '#F9D5E5', '#EAC8F2', '#FDD9C1', '#C6F8D5'],
                        borderWidth: 0,
                        cutout: '70%'
                    }]
                },
                options: {
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });

            // Tab Switching Logic
            const tabs = document.querySelectorAll('.tabs span');
            tabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    tabs.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                    const selectedTab = this.innerText.trim();
                    if (selectedTab === 'Income') {
                        mChart.data.datasets[0].data = chartData.income;
                        mChart.data.datasets[0].label = 'Income';
                    } else {
                        mChart.data.datasets[0].data = chartData.expenses;
                        mChart.data.datasets[0].label = 'Expenses';
                    }
                    mChart.update();
                });
            });

            // Month Select Logic
            document.getElementById('monthSelect').addEventListener('change', function() {
                const selectedMonth = this.value;
                const newData = donutMonthlyData[selectedMonth];
                eDonut.data.datasets[0].data = newData;
                eDonut.update();

                // Update Total and Generate NEW TIPS for the selected month
                const total = newData.reduce((a, b) => a + b, 0);
                document.getElementById('donutTotalAmount').innerText = `₹${total.toLocaleString()}`;
                generateTips(newData);
            });
        };

        const updateCharts = () => {
            if (mChart) mChart.destroy();
            if (eDonut) eDonut.destroy();
            initCharts();
        };

        initCharts();

        // Theme Toggle Logic
        const themeToggleBtn = document.getElementById('theme-toggle');
        const setTheme = (isDark) => {
            if (isDark) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
            updateCharts();
        };

        const savedTheme = localStorage.getItem('theme') || 'light';
        setTheme(savedTheme === 'dark');

        if (themeToggleBtn) {
            themeToggleBtn.addEventListener('click', () => {
                const isNowDark = !document.documentElement.classList.contains('dark');
                localStorage.setItem('theme', isNowDark ? 'dark' : 'light');
                setTheme(isNowDark);
            });
        }

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


        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        const fetchDashboardData = (monthNum = new Date().getMonth() + 1) => {
            return fetch(`get-dashboard-data.php?month=${monthNum}`)
                .then(res => res.json());
        };

        const initChartsAjax = async () => {
            const currentMonth = document.getElementById('monthSelect').selectedIndex + 1;
            const data = await fetchDashboardData(currentMonth);

            // Update summary cards
            document.querySelector('.summary-row .summary-card:nth-child(1) h4').innerText = `₹${data.totalIncome.toLocaleString('en-IN', {minimumFractionDigits:2})}`;
            document.querySelector('.summary-row .summary-card:nth-child(2) h4').innerText = `₹${data.totalExpense.toLocaleString('en-IN', {minimumFractionDigits:2})}`;
            document.querySelector('.summary-row .summary-card:nth-child(3) h4').innerText = `₹${data.totalSavings.toLocaleString('en-IN', {minimumFractionDigits:2})}`;
            document.querySelector('.summary-row .summary-card:nth-child(4) h4').innerText = `₹${data.totalSavings.toLocaleString('en-IN', {minimumFractionDigits:2})}`;

            // Destroy previous charts if they exist
            if (window.mChart) window.mChart.destroy();
            if (window.eDonut) window.eDonut.destroy();

            // Monthly Bar Chart
            const ctxMonthly = document.getElementById('monthlyChart').getContext('2d');
            window.mChart = new Chart(ctxMonthly, {
                type: 'bar',
                data: {
                    labels: months.slice(0, 7), // show Jan-Jul
                    datasets: [{
                        label: 'Income',
                        data: data.incomeData.slice(0, 7),
                        backgroundColor: '#a8c6ff',
                        borderRadius: 10,
                        borderSkipped: false
                    }]
                },
                options: {
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        },
                        x: {}
                    }
                }
            });

            // Donut chart
            const ctxDonut = document.getElementById('expenseDonut').getContext('2d');
            window.eDonut = new Chart(ctxDonut, {
                type: 'doughnut',
                data: {
                    labels: ['Shopping', 'Entertainment', 'Education', 'Vehicle', 'Household', 'Insurance'],
                    datasets: [{
                        data: data.donutData,
                        backgroundColor: ['#A7C7FF', '#C6E2FF', '#F9D5E5', '#EAC8F2', '#FDD9C1', '#C6F8D5'],
                        cutout: '70%'
                    }]
                },
                options: {
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });

            document.getElementById('donutTotalAmount').innerText = `₹${data.donutData.reduce((a,b)=>a+b,0).toLocaleString('en-IN')}`;

            // Generate expense tips dynamically
            generateTips(data.donutData);
        };

        // Month change event
        document.getElementById('monthSelect').addEventListener('change', initChartsAjax);

        // Initial load
        initChartsAjax();
    </script>
</body>

</html>