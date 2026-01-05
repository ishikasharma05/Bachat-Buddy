<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bachat Buddy | Forgot Password</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
      background: #f9fafb; /* light background */
      font-family: "Segoe UI", sans-serif;
      color: #111827;
    }
    .forgot-box {
      max-width: 420px;
      margin: 70px auto;
      background: #ffffff;
      padding: 35px;
      border-radius: 16px;
      box-shadow: 0 6px 15px rgba(0,0,0,0.08);
    }
    .forgot-box h2 {
      text-align: center;
      margin-bottom: 15px;
      color: #2563eb; /* blue like login */
      font-weight: bold;
    }
    .form-control {
      background: #f3f4f6;
      border: 1px solid #e5e7eb;
    }
    .form-control:focus {
      background: #fff;
      border-color: #2563eb;
      box-shadow: 0 0 0 0.2rem rgba(37,99,235,0.2);
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
      font-size: 0.85rem;
      display: none;
    }
    /* New Success Message Style */
    #successBox {
      display: none;
      background: #dcfce7;
      color: #166534;
      border: 1px solid #bbf7d0;
      padding: 12px;
      border-radius: 8px;
      text-align: center;
      margin-bottom: 20px;
      font-size: 0.9rem;
    }
    .extra-links {
      text-align: center;
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
  <div class="forgot-box">
    <i class="bi bi-shield-lock brand-icon"></i>
    <h2>Forgot Password</h2>
    <p class="text-center text-muted mb-4">Enter your email to receive reset instructions</p>

    <div id="successBox">
      <i class="bi bi-check-circle-fill me-2"></i>
      Email has been sent! Redirecting to login...
    </div>

    <form id="forgotForm" novalidate>
      <div class="mb-3">
        <label class="form-label">Email Address</label>
        <input type="email" class="form-control" id="email" required>
        <div class="error" id="emailError">Please enter a valid email</div>
      </div>
      <button type="submit" class="btn btn-primary w-100">Send Reset Link</button>

      <div class="extra-links mt-3">
        <a href="login.php">Back to Login</a> | 
        <a href="sign-up.php">Create Account</a>
      </div>
    </form>
  </div>

  <script>
    document.getElementById("forgotForm").addEventListener("submit", function(e) {
      e.preventDefault();
      let valid = true;

      const email = document.getElementById("email").value.trim();
      const emailPattern = /^[^ ]+@[^ ]+\.[a-z]{2,3}$/;

      if (!email.match(emailPattern)) {
        document.getElementById("emailError").style.display = "block";
        valid = false;
      } else {
        document.getElementById("emailError").style.display = "none";
      }

      if (valid) {
        // Show success message instead of alert
        document.getElementById("successBox").style.display = "block";
        
        // Reset form
        document.getElementById("forgotForm").reset();

        // Redirect to login.php after 2 seconds
        setTimeout(() => {
          window.location.href = "login.php";
        }, 2000);
      }
    });
  </script>
</body>
</html>