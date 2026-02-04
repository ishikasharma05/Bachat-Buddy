<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bachat Buddy | Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
      background: #f9fafb;
      /* plain light background */
      font-family: "Segoe UI", sans-serif;
      color: #111827;
    }

    .login-box {
      max-width: 420px;
      margin: 70px auto;
      background: #ffffff;
      padding: 35px;
      border-radius: 16px;
      box-shadow: 0 6px 15px rgba(0, 0, 0, 0.08);
    }

    .login-box h2 {
      text-align: center;
      margin-bottom: 20px;
      color: #2563eb;
      font-weight: bold;
    }

    .form-control {
      background: #f3f4f6;
      border: 1px solid #e5e7eb;
    }

    .form-control:focus {
      background: #fff;
      border-color: #2563eb;
      box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.2);
    }

    .btn-primary {
      background: #2563eb;
      border: none;
      font-weight: 600;
    }

    .btn-primary:hover {
      background: #1d4ed8;
    }

    .error {
      color: #dc2626;
      font-size: 0.9rem;
      display: none;
    }

    .extra-links {
      display: flex;
      justify-content: space-between;
      margin-top: 15px;
    }

    .extra-links a {
      font-size: 0.9rem;
      color: #2563eb;
      text-decoration: none;
    }

    .extra-links a:hover {
      text-decoration: underline;
    }

    .brand-icon {
      font-size: 2.5rem;
      color: #2563eb;
      display: block;
      text-align: center;
      margin-bottom: 10px;
    }
  </style>
</head>

<body>
  <div class="login-box">
    <i class="bi bi-wallet2 brand-icon"></i>
    <h2>Login</h2>
    <p class="text-center text-muted mb-4">Welcome back! Please sign in</p>
    <form id="loginForm" novalidate>
      <div class="mb-3">
        <label class="form-label">Email Address</label>
        <input type="email" class="form-control" id="email" name="email" required>
        <div class="error" id="emailError">Please enter a valid email</div>
      </div>
      <div class="mb-3">
        <label class="form-label">Password</label>
        <div class="input-group">
          <input type="password" class="form-control" id="password" name="password" required minlength="6">
          <span class="input-group-text" id="togglePassword" style="cursor: pointer;">
            <i class="bi bi-eye"></i>
          </span>
        </div>
        <div class="error" id="passwordError">Password must be at least 6 characters</div>
      </div>
      <button type="submit" class="btn btn-primary w-100">Login</button>

      <div class="extra-links mt-3">
        <a href="forget-password.php">Forgot Password?</a>
        <a href="sign-up.php">Create Account</a>
      </div>
    </form>
  </div>

  <script>
    document.getElementById("loginForm").addEventListener("submit", function(e) {
      e.preventDefault();

      let formData = new FormData(this);

      fetch("login.php", {
          method: "POST",
          body: formData
        })
        .then(res => res.text())
        .then(data => {

          if (data === "SUCCESS") {
            window.location.href = "../index.php";
          } else if (data === "INVALID_PASSWORD") {
            alert("Incorrect password");
          } else if (data === "NO_USER") {
            alert("No account found with this email");
          } else {
            alert("Login failed");
          }

        });
    });
  </script>

</body>

</html>