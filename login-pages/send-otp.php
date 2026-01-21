<?php
session_start();
require_once __DIR__ . '/../config/db.php';


$email = $_POST['email'];
$newPassword = $_POST['new_password'];

$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Email not found");
}

$otp = rand(100000, 999999);
$expiry = date("Y-m-d H:i:s", strtotime("+5 minutes"));

$update = $conn->prepare(
  "UPDATE users SET otp_code=?, otp_expiry=?, otp_attempts=0 WHERE email=?"
);
$update->bind_param("sss", $otp, $expiry, $email);
$update->execute();

$_SESSION['otp_email'] = $email;
$_SESSION['new_password'] = password_hash($newPassword, PASSWORD_DEFAULT);

/* SEND EMAIL (basic) */
mail($email, "Your OTP Code", "Your OTP is: $otp\nValid for 5 minutes.");

header("Location: verify-otp.php");
exit;
