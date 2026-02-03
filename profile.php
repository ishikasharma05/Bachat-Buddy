
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
    <link rel="stylesheet" href="components/styles.css">
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background-color: #f2f6f9;
            transition: background-color 0.4s cubic-bezier(0.4, 0, 0.2, 1), color 0.4s ease;
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
            cursor: pointer;
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
            height: 220px;
            border-radius: 24px;
            position: relative;
            transition: all 0.5s ease;
        }

        .profile-card {
            max-width: 850px;
            margin: -80px auto 50px;
            background: #fff;
            border-radius: 24px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08);
            padding: 40px;
            position: relative;
            z-index: 2;
            transition: all 0.4s ease;
        }

        .profile-pic {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
            border: 6px solid #fff;
            margin-top: -110px;
            background: #eee;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .info-box {
            background: #f9fafc;
            border-radius: 15px;
            padding: 15px 20px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
            border: 1px solid transparent;
        }

        .info-box:hover {
            border-color: #3b82f6;
            transform: translateX(5px);
        }

        .stat-box {
            background: #eef6ff;
            border-radius: 18px;
            padding: 20px;
            width: 46%;
            text-align: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
            transition: all 0.3s ease;
        }

        .footer {
            background: linear-gradient(135deg, #2a9d8f, #4cafef);
            border-radius: 20px 20px 0 0;
            transition: background 0.3s ease;
        }

        /* ===== DARK MODE REFINEMENTS ===== */
        .dark body,
        .dark .main-body {
            background-color: #0f172a !important;
            color: #f8fafc;
        }

        .dark .header,
        .dark .sidebar {
            background-color: #1e293b !important;
            border-color: #334155 !important;
            color: #f8fafc;
        }

        .dark .profile-card {
            background-color: #1e293b !important;
            border: 1px solid #334155 !important;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            color: #f8fafc !important;
        }

        .dark .nav-link {
            color: #cbd5e1 !important;
        }

        .dark .nav-link:hover {
            background-color: #334155 !important;
            color: #fff !important;
        }

        .dark .info-box {
            background-color: #334155 !important;
            color: #f8fafc;
        }

        /* Fix for tag labels in dark mode */
        .dark .info-box .text-muted {
            color: #94a3b8 !important;
        }

        .dark .stat-box {
            background-color: #0f172a !important;
            border: 1px solid #334155;
        }

        /* Fix for stat labels in dark mode */
        .dark .stat-box .text-muted {
            color: #94a3b8 !important;
        }

        .dark .cover {
            background: linear-gradient(135deg, #1e3a8a, #4c1d95);
        }

        .dark .profile-pic {
            border-color: #1e293b;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.5);
        }

        .dark .notification,
        .dark #theme-toggle {
            background-color: #334155 !important;
            color: #f8fafc;
        }

        .dark .modal-content {
            background-color: #1e293b;
            color: #f8fafc;
            border: 1px solid #334155;
        }

        .dark .form-control,
        .dark .form-select {
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

        /* Progress Bar for Goal Enhancement */
        .progress-custom {
            height: 8px;
            background-color: #e9ecef;
            border-radius: 10px;
            overflow: hidden;
            margin-top: 10px;
        }

        .dark .progress-custom {
            background-color: #334155;
        }

        /* ===== MOBILE SIDEBAR HARD FIX ===== */

        /* hidden by default */
        .mobile-sidebar {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 260px;
            height: 100vh;
            background: #ffffff;
            z-index: 1050;
            overflow-y: auto;
        }

        /* dark mode */
        .dark .mobile-sidebar {
            background: #1e293b;
        }

        /* show only when hamburger clicked */
        .mobile-sidebar.active {
            display: block;
        }

        /* overlay */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.45);
            z-index: 1040;
        }

        .sidebar-overlay.active {
            display: block;
        }

        /* lock scroll */
        body.sidebar-open {
            overflow: hidden;
        }

        /* ===============================
   PROFILE – TABLET RESPONSIVE
   ≤ 992px
================================ */

        @media (max-width: 992px) {

            .layout {
                flex-direction: column;
                height: auto;
            }

            .main-content {
                height: auto;
            }

            .main-body {
                padding: 1.5rem;
            }

            /* Profile card scaling */
            .profile-card {
                max-width: 100%;
                margin: -60px 1rem 40px;
                padding: 30px;
            }

            /* Cover height reduced */
            .cover {
                height: 200px;
            }

            /* Profile image resize */
            .profile-pic {
                width: 130px;
                height: 130px;
                margin-top: -90px;
            }

            /* Stat boxes spacing */
            .stat-box {
                width: 48%;
            }
        }

        /* ===============================
   PROFILE – MOBILE RESPONSIVE
   ≤ 576px
================================ */

        @media (max-width: 576px) {

            .main-body {
                padding: 1rem;
            }

            /* Cover smaller on mobile */
            .cover {
                height: 160px;
                border-radius: 18px;
            }

            /* Profile card full width */
            .profile-card {
                margin: -50px 0.75rem 30px;
                padding: 20px;
                border-radius: 20px;
            }

            /* Profile image centered & smaller */
            .profile-pic {
                width: 110px;
                height: 110px;
                margin-top: -75px;
            }

            /* Info boxes stack clean */
            .info-box {
                flex-direction: column;
                align-items: flex-start;
                gap: 6px;
                padding: 14px;
            }

            /* Stats go single column */
            .stat-box {
                width: 100%;
                margin-bottom: 12px;
            }

            /* Header spacing fix */
            .header {
                padding: 0.75rem 1rem;
            }

            /* Notification alignment */
            #notificationDropdown {
                width: calc(100vw - 2rem);
                right: -0.5rem;
            }
        }

        /* ===== HAMBURGER VISIBILITY FIX ===== */

        /* default (light mode) */
        .header .bi-list {
            color: #0f172a;
        }

        /* dark mode */
        .dark .header .bi-list {
            color: #f8fafc !important;
        }
    </style>
</head>

<body>
    <div class="layout">
        <div class="sidebar d-none d-lg-block">
            <div>
                <div class="brand d-flex align-items-center mb-4">
                    <i class="bi bi-piggy-bank me-2 text-success"></i> Bachat-Buddy
                </div>
                <ul class="nav flex-column gap-2">
                    <li><a class="nav-link" href="index.php"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
                    <li><a class="nav-link" href="profile.php" style="background: #3b82f6; color: #fff;"><i class="bi bi-person-circle"></i> Profile</a></li>
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
                    <li><a class="nav-link" href="index.php"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
                    <li><a class="nav-link" href="profile.php" style="background: #3b82f6; color: #fff;"><i class="bi bi-person-circle"></i> Profile</a></li>
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
                <div class="cover"></div>

                <div class="profile-card text-center">
                    <div class="mb-3">

                        <img
                            id="profileImg"
                            src="<?= !empty($user['profile_image']) ? $user['profile_image'] : 'uploads/default.png' ?>"
                            class="profile-pic">

                        <input
                            type="file"
                            id="uploadImg"
                            accept="image/*"
                            style="display:none"
                            onchange="uploadProfileImage()">

                        <button
                            class="btn btn-sm btn-outline-primary rounded-pill px-3"
                            onclick="document.getElementById('uploadImg').click()">
                            <i class="bi bi-camera me-1"></i> Update Photo
                        </button>

                    </div>

                    <h4><?= $user['full_name'] ?></h4>
                    <div class="d-flex justify-content-center gap-2 mb-4">
                        <span class="badge bg-success-subtle text-success border border-success rounded-pill px-3">Elite Member</span>
                        <span class="badge bg-primary-subtle text-primary border border-primary rounded-pill px-3">Active Saver</span>
                    </div>

                    <button class="btn btn-primary rounded-pill px-5 mb-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#editModal">
                        <i class="bi bi-pencil-square me-2"></i>Edit Profile
                    </button>

                    <div class="d-flex justify-content-between gap-3 mb-5">
                        <div class="stat-box">
                            <h4 class="fw-bold text-danger mb-0">₹12,500</h4>
                            <p class="text-muted small mb-0">Total Expenses</p>
                            <div class="progress-custom">
                                <div class="bg-danger h-100" style="width: 70%"></div>
                            </div>
                        </div>
                        <div class="stat-box">
                            <h4 class="fw-bold text-success mb-0">50%</h4>
                            <p class="text-muted small mb-0">Savings Reached</p>
                            <div class="progress-custom">
                                <div class="bg-success h-100" style="width: 50%"></div>
                            </div>
                        </div>
                    </div>

                    <div class="text-start mx-auto" style="max-width: 650px;">
                        <h6 class="fw-bold mb-3 px-2 text-uppercase small opacity-75">Personal Information</h6>
                        <div class="info-box">
                            <i class="bi bi-envelope-fill me-3 fs-5 text-primary"></i>
                            <div>
                                <small class="text-muted d-block">Email Address</small>
                                <span id="userEmail"><?= $user['email'] ?></span>
                            </div>
                        </div>
                        <div class="info-box">
                            <i class="bi bi-telephone-fill me-3 fs-5 text-primary"></i>
                            <div>
                                <small class="text-muted d-block">Phone Number</small>
                                <span id="userPhone"><?= $user['mobile'] ?></span>
                            </div>
                        </div>
                        <div class="info-box">
                            <i class="bi bi-wallet2 me-3 fs-5 text-primary"></i>
                            <div>
                                <small class="text-muted d-block">Financial Settings</small>
                                <span id="userBudget">Monthly Budget: ₹<?= $user['monthly_budget'] ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <?php include 'components/footer.php'; ?>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold">Update Monthly Budget</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body px-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Monthly Budget (₹)</label>
                        <input
                            type="number"
                            id="editBudget"
                            class="form-control rounded-3 p-2"
                            value="<?= $user['monthly_budget'] ?>">
                    </div>
                </div>

                <div class="modal-footer border-0">
                    <button class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary rounded-pill px-4" onclick="updateBudget()">
                        <i class="bi bi-check-lg me-2"></i>Update
                    </button>
                </div>
            </div>
        </div>
    </div>


    <script>
        function toggleMenu() {
            document.getElementById("mobileSidebar").classList.toggle("active");
            document.getElementById("sidebarOverlay").classList.toggle("active");
            document.body.classList.toggle("sidebar-open");
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Profile Data Object
        // Theme Toggle Logic with smooth transition
        const themeToggleBtn = document.getElementById('theme-toggle');
        const setTheme = (isDark) => {
            if (isDark) {
                document.documentElement.classList.add('dark');
                themeToggleBtn.innerHTML = '<i class="fas fa-sun text-warning"></i>';
            } else {
                document.documentElement.classList.remove('dark');
                themeToggleBtn.innerHTML = '<i class="fas fa-moon text-dark"></i>';
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
            document.getElementById("editBudget").value = profile.budget.replace('₹', '');
            document.getElementById("editLang").value = profile.language;
        });

        // Save Profile Function
        function saveProfile() {
            profile.name = document.getElementById("editName").value;
            profile.email = document.getElementById("editEmail").value;
            profile.phone = document.getElementById("editPhone").value;
            profile.budget = '₹' + document.getElementById("editBudget").value;
            profile.language = document.getElementById("editLang").value;

            document.getElementById("userName").innerText = profile.name;
            document.getElementById("userEmail").innerText = profile.email;
            document.getElementById("userPhone").innerText = profile.phone;
            document.getElementById("userBudget").innerText = "Monthly Budget: " + profile.budget;
            document.getElementById("userLang").innerText = "Language: " + profile.language;

            // Simple Success Feedback
            const btn = document.querySelector('.modal-footer .btn-primary');
            btn.innerHTML = '<i class="bi bi-check-lg"></i> Saved!';
            setTimeout(() => {
                btn.innerText = 'Apply Changes';
                bootstrap.Modal.getInstance(editModalEl).hide();
            }, 800);
        }

        // Image Preview with quality preservation
        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function() {
                const output = document.getElementById('profileImg');
                output.src = reader.result;
            };
            reader.readAsDataURL(event.target.files[0]);
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
    </script>
    <script>
        function saveProfile() {
            const formData = new FormData();
            formData.append("name", document.getElementById("editName").value);
            formData.append("email", document.getElementById("editEmail").value);
            formData.append("phone", document.getElementById("editPhone").value);
            formData.append("budget", document.getElementById("editBudget").value);
            formData.append("language", document.getElementById("editLang").value);

            const img = document.getElementById("uploadImg").files[0];
            if (img) formData.append("profile_image", img);

            fetch("api/profile.php", {
                    method: "POST",
                    body: formData
                })
                .then(res => res.json())
                .then(() => location.reload());
        }
    </script>
    <script>
        function updateBudget() {
            const budget = document.getElementById("editBudget").value;

            if (!budget || budget <= 0) {
                alert("Please enter a valid budget amount");
                return;
            }

            fetch("api/update-budget.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: "monthly_budget=" + budget
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert("Failed to update budget");
                    }
                })
                .catch(() => alert("Server error"));
        }
    </script>
    <script>
        function uploadProfileImage() {
            const fileInput = document.getElementById("uploadImg");
            const file = fileInput.files[0];

            if (!file) return;

            const formData = new FormData();
            formData.append("profile_image", file);

            fetch("api/update-profile-image.php", {
                    method: "POST",
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById("profileImg").src = data.image + "?t=" + new Date().getTime();
                    } else {
                        alert("Image upload failed");
                    }
                })
                .catch(() => alert("Server error"));
        }
    </script>

</body>

</html>