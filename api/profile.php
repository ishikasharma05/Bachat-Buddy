<?php
require_once __DIR__ . "/config/db.php";
require_once __DIR__ . "/auth/session.php";

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $sql = "SELECT * FROM users WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    $result = $stmt->get_result();
    echo json_encode($result->fetch_assoc());
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name   = $_POST['name'];
    $email  = $_POST['email'];
    $phone  = $_POST['phone'];
    $budget = $_POST['budget'];
    $lang   = $_POST['language'];

    $imgPath = null;

    if (!empty($_FILES['profile_image']['name'])) {
        $targetDir = "../uploads/profile/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

        $fileName = time() . "_" . basename($_FILES['profile_image']['name']);
        $targetFile = $targetDir . $fileName;

        move_uploaded_file($_FILES['profile_image']['tmp_name'], $targetFile);
        $imgPath = "uploads/profile/" . $fileName;
    }

    if ($imgPath) {
        $sql = "UPDATE users SET name=?, email=?, phone=?, monthly_budget=?, language=?, profile_image=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssissi", $name, $email, $phone, $budget, $lang, $imgPath, $user_id);
    } else {
        $sql = "UPDATE users SET name=?, email=?, phone=?, monthly_budget=?, language=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssisi", $name, $email, $phone, $budget, $lang, $user_id);
    }

    $stmt->execute();
    echo json_encode(["status" => "success"]);
}
?>