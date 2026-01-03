<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bachat-Buddy | Goals</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="components/styles.css">
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
            --label-color: #495057;
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
            --label-color: #ffffff;
            /* High contrast for labels like 'Goal Name' */
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
            text-decoration: none;
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
            cursor: pointer;
            border: none;
            color: var(--text-main);
        }

        .profile-info {
            display: flex;
            align-items: center;
            gap: 10px;
            background: var(--input-bg);
            padding: 6px 12px;
            border-radius: 20px;
        }

        .main-body {
            padding: 2rem;
            overflow-y: auto;
            background-color: var(--bg-main);
            transition: all 0.3s ease;
        }

        .page-header {
            background: linear-gradient(135deg, #a8edea, #fed6e3);
            padding: 40px 20px;
            border-radius: 0 0 30px 30px;
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }

        [data-theme="dark"] .page-header {
            background: linear-gradient(135deg, #1e3a8a, #4c1d95);
            color: #fff;
        }

        /* --- VISIBILITY FIX FOR LABELS --- */
        .form-label,
        .text-muted,
        small.d-block {
            color: var(--label-color) !important;
            opacity: 1;
        }

        .goal-card {
            background: var(--bg-card);
            border-radius: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
            transition: transform 0.2s, background 0.3s;
            border: 1px solid var(--border-color);
        }

        .goal-card:hover {
            transform: translateY(-5px);
        }

        .modal-content {
            background-color: var(--bg-card);
            color: var(--text-main);
            border: 1px solid var(--border-color);
            border-radius: 20px;
        }

        /* --- FIXING THE CROSS (CLOSE BUTTON) VISIBILITY --- */
        [data-theme="dark"] .btn-close {
            filter: invert(1) grayscale(100%) brightness(200%);
        }

        .form-control {
            background-color: var(--input-bg);
            border-color: var(--border-color);
            color: var(--text-main);
        }

        .form-control:focus {
            background-color: var(--input-bg);
            color: var(--text-main);
            border-color: #3b82f6;
            box-shadow: 0 0 0 0.25rem rgba(59, 130, 246, 0.25);
        }

        .form-control::placeholder {
            color: var(--text-muted);
            opacity: 0.7;
        }

        .footer {
            background: linear-gradient(135deg, #2a9d8f, #4cafef);
            border-radius: 20px 20px 0 0;
        }

        .badge.bg-success {
            background-color: #059669 !important;
            color: #ecfdf5;
        }
    </style>
</head>

<body>
    <div class="layout">
        <div class="sidebar">
            <div>
                <div class="brand d-flex align-items-center mb-4">
                    <i class="bi bi-piggy-bank me-2 text-success"></i> Bachat-Buddy
                </div>
                <ul class="nav flex-column gap-2">
                    <li><a class="nav-link" href="index.php"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
                    <li><a class="nav-link" href="profile.php"><i class="bi bi-person-circle"></i> Profile</a></li>
                    <li><a class="nav-link" href="transaction.php"><i class="bi bi-arrow-left-right"></i> Transactions</a></li>
                    <li><a class="nav-link" href="add-entry.php"><i class="bi bi-journal-plus"></i> Add Entry</a></li>
                    <li><a class="nav-link" href="goals.php" style="background: #3b82f6; color: #fff;"><i class="bi bi-bullseye"></i> Goals</a></li>
                </ul>
            </div>
        </div>


        <div class="main-content">
            <?php include 'components/header.php'; ?>

            <div class="main-body">
                <div class="page-header">
                    <h2>🎯 My Savings Goals</h2>
                    <p class="opacity-75">Track your progress and achieve your financial dreams</p>
                    <button class="btn btn-primary rounded-pill px-4 mt-2" data-bs-toggle="modal" data-bs-target="#goalModal" onclick="prepareAddGoal()">
                        <i class="bi bi-plus-circle me-1"></i> Add New Goal
                    </button>
                </div>

                <div class="container" id="goalList">
                    <div class="goal-card">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="fw-bold">car</h5>
                            <span class="badge bg-success rounded-pill px-3">In Progress</span>
                        </div>
                        <p class="text-muted mb-2">Target: ₹500,000 | Saved: ₹100,000</p>
                        <div class="progress mb-2" style="height: 10px; background-color: var(--input-bg);">
                            <div class="progress-bar bg-info" style="width: 20%"></div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <button class="btn btn-sm btn-outline-primary rounded-pill px-3" onclick="updateGoal(this)">Add Funds</button>
                            <div>
                                <i class="bi bi-pencil text-muted me-2" style="cursor:pointer" onclick="editGoal(this)"></i>
                                <i class="bi bi-trash text-danger" style="cursor:pointer" onclick="deleteGoal(this)"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="goalModal" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content shadow-lg">
                            <div class="modal-header border-0 pb-0">
                                <h5 class="modal-title fw-bold" id="modalTitle">Add New Goal</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body p-4">
                                <form id="goalForm">
                                    <input type="hidden" id="editGoalId">
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold">Goal Name</label>
                                        <input type="text" id="goalName" class="form-control rounded-3" placeholder="e.g. Dream Vacation">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold">Target Amount (₹)</label>
                                        <input type="number" id="goalTarget" class="form-control rounded-3" placeholder="0.00">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold">Initial Saved Amount (₹)</label>
                                        <input type="number" id="goalSaved" class="form-control rounded-3" placeholder="0.00">
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer border-0 pt-0 d-flex justify-content-center gap-3">
                                <button class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                                <button class="btn btn-primary rounded-pill px-4" id="saveGoalBtn" onclick="addGoal()">Save Goal</button>
                            </div>
                        </div>
                    </div>
                </div>

                <?php include 'components/footer.php'; ?>
            </div>
        </div>
    </div>

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

        // Initialize Theme
        setTheme(localStorage.getItem('theme') || 'dark');

        let currentEditCard = null;

        function prepareAddGoal() {
            currentEditCard = null;
            document.getElementById("modalTitle").innerText = "Add New Goal";
            document.getElementById("goalForm").reset();
            document.getElementById("saveGoalBtn").innerText = "Save Goal";
        }

        function addGoal() {
            const name = document.getElementById("goalName").value;
            const target = document.getElementById("goalTarget").value;
            const saved = document.getElementById("goalSaved").value || 0;
            
            if (!name || !target) {
                alert("Please fill in all fields");
                return;
            }

            const progress = Math.min((saved / target) * 100, 100);
            
            const cardHTML = `
                <div class="d-flex justify-content-between align-items-center">
                  <h5 class="fw-bold">${name}</h5>
                  <span class="badge rounded-pill px-3 ${progress >= 100 ? "bg-primary" : "bg-success"}">${progress >= 100 ? "Completed" : "In Progress"}</span>
                </div>
                <p class="text-muted mb-2">Target: ₹${target} | Saved: ₹${saved}</p>
                <div class="progress mb-2" style="height: 10px; background-color: var(--input-bg);"><div class="progress-bar ${progress >= 100 ? "bg-primary" : "bg-info"}" style="width: ${progress}%"></div></div>
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <button class="btn btn-sm btn-outline-primary rounded-pill px-3" onclick="updateGoal(this)">Add Funds</button>
                    <div>
                        <i class="bi bi-pencil text-muted me-2" style="cursor:pointer" onclick="editGoal(this)"></i>
                        <i class="bi bi-trash text-danger" style="cursor:pointer" onclick="deleteGoal(this)"></i>
                    </div>
                </div>`;

            if (currentEditCard) {
                // Updating existing card
                currentEditCard.innerHTML = cardHTML;
            } else {
                // Adding new card
                const goalCard = document.createElement("div");
                goalCard.className = "goal-card";
                goalCard.innerHTML = cardHTML;
                document.getElementById("goalList").appendChild(goalCard);
            }

            document.getElementById("goalForm").reset();
            bootstrap.Modal.getInstance(document.getElementById("goalModal")).hide();
        }

        function updateGoal(button) {
            const card = button.closest(".goal-card");
            const details = card.querySelector("p");
            let [target, saved] = details.innerText.match(/\d+/g).map(Number);
            const addMore = prompt("Enter amount to add to savings:");
            if (!addMore || isNaN(addMore)) return;
            saved += parseInt(addMore);
            const progress = Math.min((saved / target) * 100, 100);
            details.innerText = `Target: ₹${target} | Saved: ₹${saved}`;
            card.querySelector(".progress-bar").style.width = `${progress}%`;
            card.querySelector(".progress-bar").className = `progress-bar ${progress >= 100 ? "bg-primary" : "bg-info"}`;
            card.querySelector(".badge").className = `badge rounded-pill px-3 ${progress >= 100 ? "bg-primary" : "bg-success"}`;
            card.querySelector(".badge").innerText = progress >= 100 ? "Completed" : "In Progress";
        }

        function deleteGoal(icon) {
            if (confirm("Are you sure you want to delete this goal?")) {
                const card = icon.closest(".goal-card");
                card.remove();
            }
        }

        function editGoal(icon) {
            currentEditCard = icon.closest(".goal-card");
            const name = currentEditCard.querySelector("h5").innerText;
            const details = currentEditCard.querySelector("p").innerText;
            const [target, saved] = details.match(/\d+/g).map(Number);

            // Populate Modal
            document.getElementById("goalName").value = name;
            document.getElementById("goalTarget").value = target;
            document.getElementById("goalSaved").value = saved;
            
            document.getElementById("modalTitle").innerText = "Edit Goal";
            document.getElementById("saveGoalBtn").innerText = "Update Goal";

            // Show Modal
            const modal = new bootstrap.Modal(document.getElementById('goalModal'));
            modal.show();
        }
    </script>
</body>

</html>