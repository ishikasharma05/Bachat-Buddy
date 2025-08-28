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
        /* ==== General Styling ==== */
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #74ebd5 0%, #ACB6E5 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .profile-card {
            background: #fff;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0px 6px 20px rgba(0, 0, 0, 0.2);
            width: 380px;
            text-align: center;
            animation: fadeIn 0.8s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .profile-card h2 {
            margin-bottom: 20px;
            color: #333;
        }

        .avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #6c63ff;
            margin-bottom: 15px;
        }

        .form-group {
            margin-bottom: 15px;
            text-align: left;
        }

        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
            color: #444;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 10px;
            outline: none;
            transition: 0.3s;
        }

        .form-group input:focus,
        .form-group select:focus {
            border-color: #6c63ff;
            box-shadow: 0 0 5px rgba(108, 99, 255, 0.4);
        }

        .btn {
            background: #6c63ff;
            color: #fff;
            font-weight: bold;
            border: none;
            padding: 12px;
            width: 100%;
            border-radius: 10px;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn:hover {
            background: #5146d9;
        }

        .message {
            margin-top: 15px;
            font-weight: bold;
            color: green;
            display: none;
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
                <div class="profile-card">
                    <img src="https://i.pravatar.cc/100" alt="Avatar" class="avatar">
                    <h2>My Profile</h2>

                    <form id="profileForm">
                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" id="name" value="Ishika">
                        </div>

                        <div class="form-group">
                            <label>Email (read-only)</label>
                            <input type="email" id="email" value="ishika@example.com" readonly>
                        </div>

                        <div class="form-group">
                            <label>Phone</label>
                            <input type="text" id="phone" value="+91 9876543210">
                        </div>

                        <div class="form-group">
                            <label>Language Preference</label>
                            <select id="lang">
                                <option value="EN" selected>English</option>
                                <option value="HI">हिन्दी</option>
                            </select>
                        </div>

                        <button type="button" class="btn" onclick="updateProfile()">Update Profile</button>
                        <div class="message" id="msg">✅ Profile Updated Successfully!</div>
                    </form>
                </div>

                <script>
                    function updateProfile() {
                        // simple frontend simulation
                        const name = document.getElementById("name").value;
                        const phone = document.getElementById("phone").value;
                        const lang = document.getElementById("lang").value;

                        if (name === "" || phone === "") {
                            alert("⚠️ Please fill all required fields.");
                            return;
                        }

                        // pretend update success
                        document.getElementById("msg").style.display = "block";
                        setTimeout(() => {
                            document.getElementById("msg").style.display = "none";
                        }, 3000);
                    }
                </script>
            </div>
        </div>
    </div>


</body>

</html>