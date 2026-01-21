<?php
session_start();
if (!isset($_SESSION['otp_email'])) {
    die("Unauthorized access");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Verify OTP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card p-4 mx-auto" style="max-width: 400px;">
        <h4 class="text-center mb-3">Verify OTP</h4>

        <form action="verify-otp-backend.php" method="POST">
            <input
                type="text"
                name="otp"
                class="form-control mb-3"
                placeholder="Enter OTP"
                required
            >
            <button type="submit" class="btn btn-success w-100">
                Verify OTP
            </button>
        </form>
    </div>
</div>

</body>
</html>
