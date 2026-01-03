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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="components/style.css">
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
</style>

<body>
    <div class="layout">
        <?php include 'components/sidebar.php'; ?>
        <div class="main-content">
            <?php include 'components/header.php'; ?>
            <div class="main-body">
                <div class="summary-row">
                    <div class="summary-card">
                        <div class="summary-card-accent" style="background:#c6f8d5;"></div>
                        <p class="mb-1 text-muted">Income</p>
                        <h4 class="mb-0" style="color:#16a34a;">₹0</h4>
                    </div>

                    <div class="summary-card">
                        <div class="summary-card-accent" style="background:#f8d7da;"></div>
                        <p class="mb-1 text-muted">Expenses</p>
                        <h4 class="mb-0" style="color:#dc2626;">₹0</h4>
                    </div>

                    <div class="summary-card">
                        <div class="summary-card-accent" style="background:#a8c6ff;"></div>
                        <p class="mb-1 text-muted">Savings</p>
                        <h4 class="mb-0" style="color:#2563eb;">₹0</h4>
                    </div>

                    <div class="summary-card">
                        <div class="summary-card-accent" style="background:#f4ab6a;"></div>
                        <p class="mb-1 text-muted">Balance</p>
                        <h4 class="mb-0" style="color:#e77d22;">₹0</h4>
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

                                    <div class="d-flex justify-content-between align-items-center mb-2 px-3 py-2 rounded-3"
                                        style="background:#f8fafc;">
                                        <span>food</span>
                                        <span class="text-danger fw-semibold">-₹2,000</span>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center px-3 py-2 rounded-3"
                                        style="background:#f8fafc;">
                                        <span>salary</span>
                                        <span class="text-success fw-semibold">+₹20,000</span>
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
        // Global chart variables for color updates
        let mChart, eDonut;

        // 1. Define your data for both categories
        const chartData = {
            income: [12000, 8700, 9300, 12861, 11000, 6700, 8900],
            expenses: [7500, 6200, 8100, 5400, 9200, 4300, 6100],
            incomeColors: ['#eac8f2', '#fdd9c1', '#c6defc', '#a8c6ff', '#c6f8d5', '#c0f0fc', '#f9d5e5'],
            expenseColors: ['#fecaca', '#fed7aa', '#fef08a', '#bbf7d0', '#99f6e4', '#bae6fd', '#e9d5ff']
        };

        // Data for monthly donut (Expense breakdown example)
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

            // 2. Initialize bar chart
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
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: ctx => {
                                    const isIncome = mChart.data.datasets[0].label === 'Income';
                                    const prefix = isIncome ? '+' : '-';
                                    return `${prefix} ₹${ctx.raw.toLocaleString()}`;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                color: textColor,
                                callback: val => val === 0 ? '0' : val / 1000 + 'k'
                            },
                            grid: {
                                color: gridColor,
                                borderDash: [5, 5]
                            }
                        },
                        x: {
                            ticks: {
                                color: textColor,
                                font: {
                                    weight: ctx => ctx.tick.label === 'Apr' ? 'bold' : ''
                                }
                            },
                            grid: { display: false }
                        }
                    }
                }
            });

            // 3. Initialize Donut chart
            const currentMonth = document.getElementById('monthSelect').value;
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
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: ctx => `₹${ctx.raw.toLocaleString()}`
                            }
                        }
                    }
                }
            });

            // Tab Switching Logic for Bar Chart
            const tabs = document.querySelectorAll('.tabs span');
            tabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    tabs.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                    const selectedTab = this.innerText.trim();
                    if (selectedTab === 'Income') {
                        mChart.data.datasets[0].data = chartData.income;
                        mChart.data.datasets[0].backgroundColor = chartData.incomeColors;
                        mChart.data.datasets[0].label = 'Income';
                    } else {
                        mChart.data.datasets[0].data = chartData.expenses;
                        mChart.data.datasets[0].backgroundColor = chartData.expenseColors;
                        mChart.data.datasets[0].label = 'Expenses';
                    }
                    mChart.update();
                });
            });

            // Month Select Logic for Donut Chart
            document.getElementById('monthSelect').addEventListener('change', function() {
                const selectedMonth = this.value;
                const newData = donutMonthlyData[selectedMonth];
                
                // Update Chart
                eDonut.data.datasets[0].data = newData;
                eDonut.update();

                // Update Total Text in Center
                const total = newData.reduce((a, b) => a + b, 0);
                document.getElementById('donutTotalAmount').innerText = `₹${total.toLocaleString()}`;
            });
        };

        const updateCharts = () => {
            if (mChart) mChart.destroy();
            if (eDonut) eDonut.destroy();
            initCharts();
        };

        initCharts();
    </script>
    <script>
        const themeToggleBtn = document.getElementById('theme-toggle');

        const setTheme = (isDark) => {
            if (isDark) {
                document.documentElement.classList.add('dark');
                if(themeToggleBtn) themeToggleBtn.innerHTML = '<i class="fas fa-sun"></i>';
            } else {
                document.documentElement.classList.remove('dark');
                if(themeToggleBtn) themeToggleBtn.innerHTML = '<i class="fas fa-moon"></i>';
            }
            updateCharts();
        };

        const savedTheme = localStorage.getItem('theme');
        if (savedTheme === 'dark') {
            setTheme(true);
        } else if (savedTheme === 'light') {
            setTheme(false);
        } else {
            setTheme(true);
        }

        if(themeToggleBtn) {
            themeToggleBtn.addEventListener('click', () => {
                const isNowDark = !document.documentElement.classList.contains('dark');
                localStorage.setItem('theme', isNowDark ? 'dark' : 'light');
                setTheme(isNowDark);
            });
        }
    </script>
</body>

</html>