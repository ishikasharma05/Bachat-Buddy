<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bachat Buddy | Sign Up</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
      background: #f9fafb;
      font-family: "Segoe UI", sans-serif;
      color: #111827;
    }
    .signup-box {
      max-width: 460px;
      margin: 70px auto;
      background: #ffffff;
      padding: 35px;
      border-radius: 16px;
      box-shadow: 0 6px 15px rgba(0,0,0,0.08);
    }
    .signup-box h2 {
      text-align: center;
      margin-bottom: 15px;
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
    #successMessage {
      display: none;
      background-color: #d1e7dd;
      color: #0f5132;
      border: 1px solid #badbcc;
      padding: 10px;
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
  <div class="signup-box">
    <i class="bi bi-wallet2 brand-icon"></i>
    <h2>Sign Up</h2>
    <p class="text-center text-muted mb-4">Create your Bachat Buddy account</p>

    <div id="successMessage">
      <i class="bi bi-check-circle-fill me-2"></i> 
      Sign up successful! Now you can login.
    </div>

    <form id="signupForm" novalidate>
      <div class="mb-3">
        <label class="form-label">Full Name</label>
        <input type="text" class="form-control" id="name" required>
        <div class="error" id="nameError">Please enter your name</div>
      </div>
      <div class="mb-3">
        <label class="form-label">Email Address</label>
        <input type="email" class="form-control" id="email" required>
        <div class="error" id="emailError">Please enter a valid email</div>
      </div>
      <div class="mb-3">
        <label class="form-label">Mobile Number</label>
        <input type="text" class="form-control" id="mobile" required>
        <div class="error" id="mobileError">Please enter a valid 10-digit mobile</div>
      </div>
      <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" class="form-control" id="password" required minlength="6">
        <div class="error" id="passwordError">Password must be at least 6 characters</div>
      </div>
      <button type="submit" class="btn btn-primary w-100">Sign Up</button>

      <div class="extra-links mt-3">
        Already have an account? <a href="login.php">Login here</a>
      </div>
    </form>
  </div>

  <script>
    document.getElementById("signupForm").addEventListener("submit", function(e) {
      e.preventDefault();
      let valid = true;

      const name = document.getElementById("name").value.trim();
      if (name.length < 2) {
        document.getElementById("nameError").style.display = "block";
        valid = false;
      } else {
        document.getElementById("nameError").style.display = "none";
      }

      const email = document.getElementById("email").value.trim();
      const emailPattern = /^[^ ]+@[^ ]+\.[a-z]{2,3}$/;
      if (!email.match(emailPattern)) {
        document.getElementById("emailError").style.display = "block";
        valid = false;
      } else {
        document.getElementById("emailError").style.display = "none";
      }

      const mobile = document.getElementById("mobile").value.trim();
      const mobilePattern = /^[0-9]{10}$/;
      if (!mobile.match(mobilePattern)) {
        document.getElementById("mobileError").style.display = "block";
        valid = false;
      } else {
        document.getElementById("mobileError").style.display = "none";
      }

      const password = document.getElementById("password").value;
      if (password.length < 6) {
        document.getElementById("passwordError").style.display = "block";
        valid = false;
      } else {
        document.getElementById("passwordError").style.display = "none";
      }

      if (valid) {
        // Show success message on screen
        const successBox = document.getElementById("successMessage");
        successBox.style.display = "block";
        
        // Reset the form fields
        document.getElementById("signupForm").reset();

        // Redirect to login.php after 2 seconds
        setTimeout(() => {
          window.location.href = "login.php";
        }, 2000);
      }
    });
  </script>
</body>
</html>