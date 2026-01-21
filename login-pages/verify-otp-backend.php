<?php
session_start();
require_once __DIR__ . '/../config/db.php';

/* 1️⃣ Check OTP received */
if (!isset($_POST['otp'])) {
    die("OTP not received");
}

$otpEntered = trim($_POST['otp']);

/* 2️⃣ Get session values */
$email = $_SESSION['otp_email'] ?? null;
$newPassword = $_SESSION['new_password'] ?? null;

if (!$email || !$newPassword) {
    die("Session expired. Please restart the process.");
}

/* 3️⃣ Fetch OTP data */
$stmt = $conn->prepare(
    "SELECT otp_code, otp_expiry, otp_attempts FROM users WHERE email=?"
);
$stmt->bind_param("s", $email);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    die("User not found");
}

/* 4️⃣ Attempt limit */
if ($user['otp_attempts'] >= 3) {
    die("Too many attempts. Try again later.");
}

/* 5️⃣ Expiry check */
if (strtotime($user['otp_expiry']) < time()) {
    die("OTP expired");
}

/* 6️⃣ OTP match */
if ($otpEntered !== $user['otp_code']) {
    $inc = $conn->prepare(
        "UPDATE users SET otp_attempts = otp_attempts + 1 WHERE email=?"
    );
    $inc->bind_param("s", $email);
    $inc->execute();

    die("Invalid OTP");
}

/* 7️⃣ OTP valid → update password */
$update = $conn->prepare(
    "UPDATE users
     SET password=?, otp_code=NULL, otp_expiry=NULL, otp_attempts=0
     WHERE email=?"
);
$update->bind_param("ss", $newPassword, $email);
$update->execute();

/* 8️⃣ Clear session */
session_unset();
session_destroy();

echo "✅ Password changed successfully!";
exit;
?>