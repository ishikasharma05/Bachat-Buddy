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

        /* Header */
        .navbar {
            background: linear-gradient(135deg, #4cafef, #2a9d8f);
        }

        .navbar-brand {
            font-weight: bold;
            color: #fff !important;
        }

        .navbar-nav .nav-link {
            color: #fff !important;
            margin-left: 15px;
        }

        /* Profile Card */
        .cover {
            background: linear-gradient(135deg, #a8edea, #fed6e3);
            height: 200px;
            border-radius: 0 0 30px 30px;
            position: relative;
        }

        .profile-card {
            max-width: 850px;
            margin: -80px auto 50px;
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            padding: 30px;
            position: relative;
            z-index: 2;
        }

        .profile-pic {
            width: 140px;
            height: 140px;
            object-fit: cover;
            border-radius: 50%;
            border: 5px solid #fff;
            margin-top: -100px;
            background: #eee;
        }

        .btn-edit {
            border-radius: 30px;
            padding: 8px 20px;
        }

        /* Info Boxes */
        .info-box {
            background: #f9fafc;
            border-radius: 15px;
            padding: 15px 20px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }

        .info-box i {
            color: #4cafef;
            margin-right: 10px;
            font-size: 1.3rem;
        }

        /* Stats */
        .stats {
            display: flex;
            justify-content: space-around;
            margin: 20px 0;
        }

        .stat-box {
            background: #eef6ff;
            border-radius: 15px;
            padding: 20px;
            width: 45%;
            text-align: center;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.05);
        }

        .stat-box h4 {
            margin: 0;
            color: #2a9d8f;
        }

        .stat-box p {
            margin: 5px 0 0;
            color: #555;
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
                <h5 class="mb-0 fw-bold">Profile</h5>
                <div class="d-flex align-items-center gap-3">

                    <div class="notification">
                        <i class="bi bi-bell"></i>
                    </div>


                </div>
            </div>

            <div class="main-body">
                <!-- please enter the code here -->

                <!-- Cover -->
                <div class="cover"></div>

                <!-- Profile Card -->
                <div class="profile-card text-center">
                    <!-- Profile Picture -->
                    <!-- Profile Picture -->
                    <div class="mb-3">
                        <img id="profileImg" src="../Bachat-Buddy/assets/images/ian.jpg"
                            alt="Profile Picture" class="profile-pic mb-3">

                        <!-- Hidden file input -->
                        <input type="file" id="uploadImg" accept="image/*" style="display: none;" onchange="previewImage(event)">

                        <!-- Change button -->
                        <br>
                        <button class="btn btn-sm btn-outline-primary" onclick="document.getElementById('uploadImg').click();">
                            Change Picture
                        </button>
                    </div>


                    <!-- User Info -->
                    <h4 id="userName">Ian Somerhalder</h4>
                    <p class="text-muted">Regular User</p>

                    <!-- Edit Button -->
                    <button class="btn btn-primary btn-edit mb-3" data-bs-toggle="modal" data-bs-target="#editModal">
                        <i class="bi bi-pencil-square me-1"></i> Edit Profile
                    </button>

                    <!-- Stats -->
                    <div class="stats">
                        <div class="stat-box">
                            <h4>₹12,500</h4>
                            <p>Total Expenses</p>
                        </div>
                        <div class="stat-box">
                            <h4>50%</h4>
                            <p>Budget Used</p>
                        </div>
                    </div>

                    <!-- Profile Info -->
                    <div class="text-start">
                        <div class="info-box">
                            <i class="bi bi-envelope-fill"></i> <span id="userEmail">iansomerhalder@example.com</span>
                        </div>
                        <div class="info-box">
                            <i class="bi bi-telephone-fill"></i> <span id="userPhone">+91 9876543210</span>
                        </div>
                        <div class="info-box">
                            <i class="bi bi-wallet2"></i> <span id="userBudget">Monthly Budget: ₹25,000</span>
                        </div>
                        <div class="info-box">
                            <i class="bi bi-translate"></i> <span id="userLang">Preferred Language: English</span>
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

                <!-- Edit Profile Modal -->
                <div class="modal fade" id="editModal" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Edit Profile</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <form id="editForm">
                                    <div class="mb-3">
                                        <label class="form-label">Name</label>
                                        <input type="text" id="editName" class="form-control">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" id="editEmail" class="form-control">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Phone</label>
                                        <input type="text" id="editPhone" class="form-control">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Monthly Budget</label>
                                        <input type="text" id="editBudget" class="form-control">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Preferred Language</label>
                                        <select id="editLang" class="form-select">
                                            <option>English</option>
                                            <option>Hindi</option>
                                        </select>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button class="btn btn-primary" onclick="saveProfile()">Save</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let profile = {
            name: "Ian Somerhalder",
            email: "iansomerhalder@example.com",
            phone: "+91 9876543210",
            budget: "₹25,000",
            language: "English"
        };

        // Pre-fill form when modal opens
        const editModal = document.getElementById("editModal");
        editModal.addEventListener("show.bs.modal", () => {
            document.getElementById("editName").value = profile.name;
            document.getElementById("editEmail").value = profile.email;
            document.getElementById("editPhone").value = profile.phone;
            document.getElementById("editBudget").value = profile.budget;
            document.getElementById("editLang").value = profile.language;
        });

        // Save profile
        function saveProfile() {
            profile.name = document.getElementById("editName").value;
            profile.email = document.getElementById("editEmail").value;
            profile.phone = document.getElementById("editPhone").value;
            profile.budget = document.getElementById("editBudget").value;
            profile.language = document.getElementById("editLang").value;

            document.getElementById("userName").innerText = profile.name;
            document.getElementById("userEmail").innerText = profile.email;
            document.getElementById("userPhone").innerText = profile.phone;
            document.getElementById("userBudget").innerText = "Monthly Budget: " + profile.budget;
            document.getElementById("userLang").innerText = "Preferred Language: " + profile.language;

            const modal = bootstrap.Modal.getInstance(editModal);
            modal.hide();
        }
    </script>


</body>

</html>