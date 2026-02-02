<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "bachat_buddy";

$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Database connection failed"
    ]);
    exit;
}
?>
