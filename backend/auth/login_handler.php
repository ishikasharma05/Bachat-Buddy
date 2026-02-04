<?php
declare(strict_types=1);

session_start();
require_once __DIR__ . "/../../config/db.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo "INVALID_REQUEST";
    exit;
}

$email    = trim($_POST["email"] ?? "");
$password = $_POST["password"] ?? "";

if ($email === "" || $password === "") {
    echo "FAILED";
    exit;
}

$stmt = $conn->prepare(
    "SELECT id, full_name, password
     FROM users
     WHERE email = ?"
);

$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "NO_USER";
    exit;
}

$user = $result->fetch_assoc();

if (!password_verify($password, $user["password"])) {
    echo "INVALID_PASSWORD";
    exit;
}

// ✅ Login successful → start session
$_SESSION["user_id"]   = $user["id"];
$_SESSION["user_name"] = $user["full_name"];

echo "SUCCESS";
exit;
?>