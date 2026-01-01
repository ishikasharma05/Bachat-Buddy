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
<style>        .main-body {
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
        }</style>

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
                            <select class="form-select form-select-sm w-auto">
                                <option selected>April</option>
                                <option>March</option>
                                <option>May</option>
                            </select>
                        </div>
                        <div class="d-flex">
                            <div class="donut-container">
                                <canvas id="expenseDonut"></canvas>
                                <div class="donut-center-text">
                                    <small class="text-muted">Total</small>
                                    <div class="fw-bold">₹8900</div>
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

        const initCharts = () => {
            const isDark = document.documentElement.classList.contains('dark');
            const gridColor = isDark ? 'rgba(255, 255, 255, 0.1)' : '#eee';
            const textColor = isDark ? '#94a3b8' : '#666';

            mChart = new Chart(document.getElementById('monthlyChart'), {
                type: 'bar',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
                    datasets: [{
                        data: [12000, 8700, 9300, 12861, 11000, 6700, 8900],
                        backgroundColor: ['#eac8f2', '#fdd9c1', '#c6defc', '#a8c6ff', '#c6f8d5', '#c0f0fc', '#f9d5e5'],
                        borderRadius: 10,
                        borderSkipped: false
                    }]
                },
                options: {
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: ctx => `+ ₹${ctx.raw.toLocaleString()}`
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
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });

            eDonut = new Chart(document.getElementById('expenseDonut'), {
                type: 'doughnut',
                data: {
                    labels: ['Shopping', 'Entertainment', 'Education', 'Vehicle', 'Household', 'Insurance'],
                    datasets: [{
                        data: [2650, 1350, 1950, 1850, 850, 250],
                        backgroundColor: ['#A7C7FF', '#C6E2FF', '#F9D5E5', '#EAC8F2', '#FDD9C1', '#C6F8D5'],
                        borderWidth: 0,
                        cutout: '70%'
                    }]
                },
                options: {
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: ctx => `₹${ctx.raw.toLocaleString()}`
                            }
                        }
                    }
                }
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

        // Function to set theme
        const setTheme = (isDark) => {
            if (isDark) {
                document.documentElement.classList.add('dark');
                themeToggleBtn.innerHTML = '<i class="fas fa-sun"></i>';
            } else {
                document.documentElement.classList.remove('dark');
                themeToggleBtn.innerHTML = '<i class="fas fa-moon"></i>';
            }
            updateCharts();
        };

        // Load saved theme
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme === 'dark') {
            setTheme(true);
        } else if (savedTheme === 'light') {
            setTheme(false);
        } else {
            // Default to dark as per your original script preference
            setTheme(true);
        }

        // Toggle theme listener
        themeToggleBtn.addEventListener('click', () => {
            const isNowDark = !document.documentElement.classList.contains('dark');
            localStorage.setItem('theme', isNowDark ? 'dark' : 'light');
            setTheme(isNowDark);
        });
    </script>
</body>

</html>