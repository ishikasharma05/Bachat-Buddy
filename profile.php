<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bachat-Buddy | Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background-color: #f2f6f9;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .layout {
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        /* Sidebar Styling */
        .sidebar {
            width: 250px;
            background-color: #fff;
            border-right: 1px solid #eee;
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
            transition: all 0.3s ease;
        }

        .notification {
            background: #f5f5f5;
            padding: 8px;
            border-radius: 10px;
            transition: background-color 0.3s ease;
        }

        .main-body {
            padding: 2rem;
            overflow-y: auto;
            background-color: #f2f6f9;
            transition: background-color 0.3s ease;
        }

        /* Profile Specific Styling */
        .cover {
            background: linear-gradient(135deg, #a8edea, #fed6e3);
            height: 200px;
            border-radius: 20px;
            position: relative;
            transition: all 0.3s ease;
        }

        .profile-card {
            max-width: 850px;
            margin: -80px auto 50px;
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.05);
            padding: 30px;
            position: relative;
            z-index: 2;
            transition: all 0.3s ease;
        }

        .profile-pic {
            width: 140px;
            height: 140px;
            object-fit: cover;
            border-radius: 50%;
            border: 5px solid #fff;
            margin-top: -100px;
            background: #eee;
            transition: border-color 0.3s ease;
        }

        .info-box {
            background: #f9fafc;
            border-radius: 15px;
            padding: 15px 20px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            transition: background-color 0.3s ease;
        }

        .stat-box {
            background: #eef6ff;
            border-radius: 15px;
            padding: 20px;
            width: 45%;
            text-align: center;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.02);
            transition: background-color 0.3s ease;
        }

        .footer {
            background: linear-gradient(135deg, #2a9d8f, #4cafef);
            border-radius: 20px 20px 0 0;
            transition: background 0.3s ease;
        }

        /* ===== DARK MODE REFINEMENTS ===== */
        .dark body, .dark .main-body {
            background-color: #0f172a !important;
            color: #f8fafc;
        }

        .dark .header, .dark .sidebar {
            background-color: #1e293b !important;
            border-color: #334155 !important;
            color: #f8fafc;
        }

        .dark .profile-card {
            background-color: #1e293b !important;
            border: 1px solid #334155 !important;
            color: #f8fafc !important;
        }

        .dark .nav-link { color: #cbd5e1 !important; }
        .dark .nav-link:hover { background-color: #334155 !important; color: #fff !important; }

        .dark .info-box { background-color: #334155 !important; color: #f8fafc; }
        .dark .stat-box { background-color: #0f172a !important; border: 1px solid #334155; }
        
        .dark .cover { background: linear-gradient(135deg, #1e3a8a, #4c1d95); opacity: 0.8; }
        .dark .profile-pic { border-color: #1e293b; }
        .dark .notification, .dark #theme-toggle { background-color: #334155 !important; color: #f8fafc; }

        .dark .modal-content {
            background-color: #1e293b;
            color: #f8fafc;
            border: 1px solid #334155;
        }
        .dark .form-control, .dark .form-select {
            background-color: #0f172a;
            border-color: #475569;
            color: #fff;
        }

        #theme-toggle {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
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
                    <li><a class="nav-link active" href="profile.php" style="background: #000; color: #fff;"><i class="bi bi-person-circle"></i> Profile</a></li>
                    <li><a class="nav-link" href="#"><i class="bi bi-arrow-left-right"></i> Transactions</a></li>
                    <li><a class="nav-link" href="#"><i class="bi bi-journal-plus"></i> Add Entry</a></li>
                    <li><a class="nav-link" href="#"><i class="bi bi-bar-chart"></i> Reports</a></li>
                    <li><a class="nav-link" href="#"><i class="bi bi-wallet2"></i> Budgets</a></li>
                    <li><a class="nav-link" href="#"><i class="bi bi-bullseye"></i> Goals</a></li>
                </ul>
            </div>
            <div class="logout mb-3">
                <a class="nav-link" href="#"><i class="bi bi-box-arrow-left"></i> Log out</a>
            </div>
        </div>

        <div class="main-content">
            <div class="header">
                <h5 class="mb-0 fw-bold">User Profile</h5>
                <div class="d-flex align-items-center gap-3">
                    <div class="notification">
                        <i class="bi bi-bell"></i>
                    </div>
                    <button id="theme-toggle" class="notification border-0 rounded-full">
                        <i class="fas fa-moon"></i>
                    </button>
                </div>
            </div>

            <div class="main-body">
                <div class="cover"></div>

                <div class="profile-card text-center">
                    <div class="mb-3">
                        <img id="profileImg" src="https://via.placeholder.com/140" alt="Profile Picture" class="profile-pic mb-3">
                        <input type="file" id="uploadImg" accept="image/*" style="display: none;" onchange="previewImage(event)">
                        <br>
                        <button class="btn btn-sm btn-outline-primary rounded-pill px-3" onclick="document.getElementById('uploadImg').click();">
                            Change Picture
                        </button>
                    </div>

                    <h4 id="userName" class="fw-bold mb-1">Ian Somerhalder</h4>
                    <p class="text-muted small mb-4">Regular User</p>

                    <button class="btn btn-primary rounded-pill px-4 mb-4" data-bs-toggle="modal" data-bs-target="#editModal">
                        <i class="bi bi-pencil-square me-1"></i> Edit Profile
                    </button>

                    <div class="d-flex justify-content-around mb-4">
                        <div class="stat-box">
                            <h4 class="fw-bold text-danger mb-0">₹12,500</h4>
                            <p class="text-muted small mb-0">Total Expenses</p>
                        </div>
                        <div class="stat-box">
                            <h4 class="fw-bold text-success mb-0">50%</h4>
                            <p class="text-muted small mb-0">Budget Used</p>
                        </div>
                    </div>

                    <div class="text-start mx-auto" style="max-width: 600px;">
                        <div class="info-box">
                            <i class="bi bi-envelope-fill me-3 text-primary"></i> 
                            <span id="userEmail">iansomerhalder@example.com</span>
                        </div>
                        <div class="info-box">
                            <i class="bi bi-telephone-fill me-3 text-primary"></i> 
                            <span id="userPhone">+91 9876543210</span>
                        </div>
                        <div class="info-box">
                            <i class="bi bi-wallet2 me-3 text-primary"></i> 
                            <span id="userBudget">Monthly Budget: ₹25,000</span>
                        </div>
                        <div class="info-box">
                            <i class="bi bi-translate me-3 text-primary"></i> 
                            <span id="userLang">Preferred Language: English</span>
                        </div>
                    </div>
                </div>

                <footer class="footer mt-5 py-4 text-center text-white">
                    <div class="container">
                        <p class="mb-1 fw-bold"><i class="bi bi-piggy-bank-fill me-2"></i>Bachat Buddy</p>
                        <p class="small mb-0 opacity-75">© 2025 Bachat Buddy | Smart Budget Tracker with AI Guide</p>
                    </div>
                </footer>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Edit Profile Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editForm">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Name</label>
                            <input type="text" id="editName" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Email</label>
                            <input type="email" id="editEmail" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Phone</label>
                            <input type="text" id="editPhone" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Monthly Budget</label>
                            <input type="text" id="editBudget" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Language</label>
                            <select id="editLang" class="form-select">
                                <option>English</option>
                                <option>Hindi</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary rounded-pill px-4" onclick="saveProfile()">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Profile Data Object
        let profile = {
            name: "Ian Somerhalder",
            email: "iansomerhalder@example.com",
            phone: "+91 9876543210",
            budget: "₹25,000",
            language: "English"
        };

        // Theme Toggle Logic
        const themeToggleBtn = document.getElementById('theme-toggle');
        const setTheme = (isDark) => {
            if (isDark) {
                document.documentElement.classList.add('dark');
                themeToggleBtn.innerHTML = '<i class="fas fa-sun"></i>';
            } else {
                document.documentElement.classList.remove('dark');
                themeToggleBtn.innerHTML = '<i class="fas fa-moon"></i>';
            }
        };

        const savedTheme = localStorage.getItem('theme') || 'light';
        setTheme(savedTheme === 'dark');

        themeToggleBtn.addEventListener('click', () => {
            const isNowDark = !document.documentElement.classList.contains('dark');
            localStorage.setItem('theme', isNowDark ? 'dark' : 'light');
            setTheme(isNowDark);
        });

        // Modal pre-fill
        const editModalEl = document.getElementById("editModal");
        editModalEl.addEventListener("show.bs.modal", () => {
            document.getElementById("editName").value = profile.name;
            document.getElementById("editEmail").value = profile.email;
            document.getElementById("editPhone").value = profile.phone;
            document.getElementById("editBudget").value = profile.budget;
            document.getElementById("editLang").value = profile.language;
        });

        // Save Profile Function
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

            bootstrap.Modal.getInstance(editModalEl).hide();
        }

        // Image Preview
        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function(){
                document.getElementById('profileImg').src = reader.result;
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
</body>
</html>