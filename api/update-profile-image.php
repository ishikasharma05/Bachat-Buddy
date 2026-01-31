<?php
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../auth/session.php";

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false]);
    exit;
}

if (!isset($_FILES['profile_image'])) {
    echo json_encode(["success" => false]);
    exit;
}

$user_id = $_SESSION['user_id'];
$file = $_FILES['profile_image'];

$allowed = ['jpg', 'jpeg', 'png', 'webp'];
$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

if (!in_array($ext, $allowed)) {
    echo json_encode(["success" => false]);
    exit;
}

$folder = "../uploads/";
if (!is_dir($folder)) {
    mkdir($folder, 0777, true);
}

$filename = "user_" . $user_id . "_" . time() . "." . $ext;
$path = $folder . $filename;
$dbPath = "uploads/" . $filename;

if (move_uploaded_file($file['tmp_name'], $path)) {

    $stmt = $conn->prepare(
        "UPDATE users SET profile_image = ? WHERE id = ?"
    );
    $stmt->bind_param("si", $dbPath, $user_id);
    $stmt->execute();

    echo json_encode([
        "success" => true,
        "image" => $dbPath
    ]);
} else {
    echo json_encode(["success" => false]);
}
?>