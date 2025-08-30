<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bachat-Buddy | </title>
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
            background: #f5f7fa;
            font-family: "Poppins", sans-serif;
        }

        .navbar-brand,
        .navbar-nav .nav-link {
            color: #fff !important;
        }

        /* Page Header */
        .page-header {
            background: linear-gradient(135deg, #a8edea, #fed6e3);
            padding: 40px 20px;
            border-radius: 0 0 30px 30px;
            text-align: center;
            margin-bottom: 30px;
        }

        .page-header h2 {
            font-weight: bold;
        }

        /* Goal Card */
        .goal-card {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
            transition: transform 0.2s;
        }

        .goal-card:hover {
            transform: translateY(-5px);
        }

        .progress {
            height: 14px;
            border-radius: 10px;
        }

        .btn-add {
            border-radius: 30px;
            padding: 8px 20px;
        }

        .btn-update {
            font-size: 0.8rem;
            padding: 4px 12px;
            border-radius: 20px;
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
                <!-- please enter the code here -->

                <!-- Page Header -->
                <div class="page-header">
                    <h2>🎯 My Savings Goals</h2>
                    <p class="text-muted">Track your progress and achieve your financial dreams</p>
                    <button class="btn btn-primary btn-add mt-2" data-bs-toggle="modal" data-bs-target="#goalModal">
                        <i class="bi bi-plus-circle me-1"></i> Add New Goal
                    </button>
                </div>

                <!-- Goals List -->
                <div class="container" id="goalList">
                    <!-- Default Goal -->
                    <div class="goal-card">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5>Buy a Laptop 💻</h5>
                            <span class="badge bg-success">In Progress</span>
                        </div>
                        <p class="text-muted mb-2">Target: ₹50,000 | Saved: ₹20,000</p>
                        <div class="progress mb-2">
                            <div class="progress-bar bg-info" style="width: 40%"></div>
                        </div>
                        <button class="btn btn-sm btn-outline-primary btn-update" onclick="updateGoal(this)">Update Saved</button>
                    </div>
                </div>

                <!-- Add Goal Modal -->
                <div class="modal fade" id="goalModal" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Add New Goal</h5>
                                <button class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <form id="goalForm">
                                    <div class="mb-3">
                                        <label class="form-label">Goal Name</label>
                                        <input type="text" id="goalName" class="form-control" placeholder="e.g. Buy a Laptop">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Target Amount (₹)</label>
                                        <input type="number" id="goalTarget" class="form-control" placeholder="e.g. 50000">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Initial Saved Amount (₹)</label>
                                        <input type="number" id="goalSaved" class="form-control" placeholder="e.g. 10000">
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button class="btn btn-primary" onclick="addGoal()">Save Goal</button>
                            </div>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Add Goal
        function addGoal() {
            const name = document.getElementById("goalName").value;
            const target = document.getElementById("goalTarget").value;
            const saved = document.getElementById("goalSaved").value || 0;

            if (!name || !target) {
                alert("Please fill in all fields");
                return;
            }

            const progress = Math.min((saved / target) * 100, 100);

            const goalCard = document.createElement("div");
            goalCard.className = "goal-card";
            goalCard.innerHTML = `
        <div class="d-flex justify-content-between align-items-center">
          <h5>${name}</h5>
          <span class="badge ${progress >= 100 ? "bg-primary" : "bg-success"}">
            ${progress >= 100 ? "Completed" : "In Progress"}
          </span>
        </div>
        <p class="text-muted mb-2">Target: ₹${target} | Saved: ₹${saved}</p>
        <div class="progress mb-2">
          <div class="progress-bar ${progress >= 100 ? "bg-primary" : "bg-info"}" style="width: ${progress}%"></div>
        </div>
        <button class="btn btn-sm btn-outline-primary btn-update" onclick="updateGoal(this)">Update Saved</button>
      `;

            document.getElementById("goalList").appendChild(goalCard);

            // Reset form & close modal
            document.getElementById("goalForm").reset();
            const modal = bootstrap.Modal.getInstance(document.getElementById("goalModal"));
            modal.hide();
        }

        // Update Saved Amount
        function updateGoal(button) {
            const card = button.closest(".goal-card");
            const details = card.querySelector("p");
            let [target, saved] = details.innerText.match(/\d+/g).map(Number);

            const addMore = prompt("Enter amount to add to savings:");
            if (!addMore || isNaN(addMore)) return;

            saved += parseInt(addMore);
            const progress = Math.min((saved / target) * 100, 100);

            // Update UI
            details.innerText = `Target: ₹${target} | Saved: ₹${saved}`;
            card.querySelector(".progress-bar").style.width = `${progress}%`;
            card.querySelector(".progress-bar").className = `progress-bar ${progress >= 100 ? "bg-primary" : "bg-info"}`;
            card.querySelector(".badge").className = `badge ${progress >= 100 ? "bg-primary" : "bg-success"}`;
            card.querySelector(".badge").innerText = progress >= 100 ? "Completed" : "In Progress";
        }
    </script>

</body>

</html>