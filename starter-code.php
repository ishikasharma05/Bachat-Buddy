<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prime Bank Dashboard</title>
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

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
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
</head>

<body>
    <div class="layout">
        <div class="sidebar">
            <div>
                <div class="brand d-flex align-items-center mb-4">
                    <i class="bi bi-bank2 me-2"></i> Prime Bank
                </div>
                <ul class="nav flex-column gap-2">
                    <li><a class="nav-link active" href="#"><i class="bi bi-bar-chart-line"></i>Dashboard</a></li>
                    <li><a class="nav-link" href="#"><i class="bi bi-graph-up"></i>Statistics</a></li>
                    <li><a class="nav-link" href="#"><i class="bi bi-credit-card"></i>Cards</a></li>
                    <li><a class="nav-link" href="#"><i class="bi bi-person"></i>Profile</a></li>
                    <li><a class="nav-link" href="#"><i class="bi bi-currency-dollar"></i>Payments</a></li>
                    <li><a class="nav-link" href="#"><i class="bi bi-headset"></i>Support</a></li>
                    <li><a class="nav-link" href="#"><i class="bi bi-grid"></i>Settings</a></li>
                </ul>
            </div>
            <div class="logout mb-3">
                <a class="nav-link" href="#"><i class="bi bi-box-arrow-left"></i>Log out</a>
            </div>
        </div>

        <div class="main-content">
            <div class="header">
                <h5 class="mb-0 fw-bold">Dashboard</h5>
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
                <div class="dashboard-grid">
                    <div class="card card-custom">
                        <div class="tab-header">
                            <h6 class="fw-bold">Monthly Details</h6>
                            <div class="tabs">
                                <span class="active">Income</span>
                                <span>Expenses</span>
                            </div>
                        </div>
                        <canvas id="monthlyChart"></canvas>
                    </div>

                    <div class="card card-custom">
                        <h6 class="fw-bold">Your Card</h6>
                        <!-- Placeholder for card and payments info -->
                        <p class="text-muted">(Mockup this section as per your image)</p>
                    </div>
                </div>

                <div class="grid-2">
                    <div class="card card-custom">
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
                                    <small>Total</small>
                                    <div class="fw-bold">$8900</div>
                                </div>
                            </div>
                            <div class="d-flex flex-wrap gap-3">
                                <div class="d-flex flex-column gap-2">
                                    <div><span class="legend-dot" style="background:#A7C7FF"></span> Various shopping<br><span class="legend-value">$2,650.00</span></div>
                                    <div><span class="legend-dot" style="background:#C6E2FF"></span> Entertainments<br><span class="legend-value">$1,350.00</span></div>
                                    <div><span class="legend-dot" style="background:#F9D5E5"></span> Kids Education<br><span class="legend-value">$1,950.00</span></div>
                                </div>
                                <div class="d-flex flex-column gap-2">
                                    <div><span class="legend-dot" style="background:#EAC8F2"></span> Vehicle cost<br><span class="legend-value">$1,850.00</span></div>
                                    <div><span class="legend-dot" style="background:#FDD9C1"></span> Households<br><span class="legend-value">$850.00</span></div>
                                    <div><span class="legend-dot" style="background:#C6F8D5"></span> Insurance<br><span class="legend-value">$250.00</span></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card card-custom">
                        <h6 class="fw-bold">Need more stats?</h6>
                        <p>Upgrade to pro max for added benefits</p>
                        <button class="btn btn-primary">Get now</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        new Chart(document.getElementById('monthlyChart'), {
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
                            label: ctx => `+ $${ctx.raw.toLocaleString()}`
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: val => val === 0 ? '0k' : val / 1000 + 'k'
                        },
                        grid: {
                            color: '#eee',
                            borderDash: [5, 5]
                        }
                    },
                    x: {
                        ticks: {
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

        new Chart(document.getElementById('expenseDonut'), {
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
                            label: ctx => `$${ctx.raw.toLocaleString()}`
                        }
                    }
                }
            }
        });
    </script>
</body>

</html>