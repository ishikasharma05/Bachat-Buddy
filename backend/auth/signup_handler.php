<?php
require_once __DIR__ . "/../../config/db.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo "INVALID_REQUEST";
    exit;
}

$full_name = trim($_POST["name"] ?? "");
$email     = trim($_POST["email"] ?? "");
$mobile    = trim($_POST["mobile"] ?? "");
$password  = $_POST["password"] ?? "";

if ($full_name === "" || $email === "" || $mobile === "" || $password === "") {
    echo "FAILED_EMPTY";
    exit;
}

$check = $conn->prepare("SELECT id FROM users WHERE email = ?");
$check->bind_param("s", $email);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    echo "EMAIL_EXISTS";
    exit;
}

$hash = password_hash($password, PASSWORD_BCRYPT);

$insert = $conn->prepare(
    "INSERT INTO users (full_name, email, mobile, password)
     VALUES (?, ?, ?, ?)"
);

if (!$insert) {
    echo "PREPARE_ERROR: " . $conn->error;
    exit;
}

$insert->bind_param("ssss", $full_name, $email, $mobile, $hash);

if (!$insert->execute()) {
    echo "EXECUTE_ERROR: " . $insert->error;
    exit;
}

echo "SUCCESS";
exit;
?>