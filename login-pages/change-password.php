<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Change Password</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
  <div class="card p-4 mx-auto" style="max-width: 400px;">
    <h4 class="text-center mb-3">Change Password</h4>

    <form action="send-otp.php" method="POST">
      <div class="mb-3">
        <label>Email</label>
        <input type="email" name="email" required class="form-control">
      </div>

      <div class="mb-3">
        <label>New Password</label>
        <input type="password" name="new_password" required class="form-control">
      </div>

      <button class="btn btn-primary w-100">Send OTP</button>
    </form>
  </div>
</div>

</body>
</html>
