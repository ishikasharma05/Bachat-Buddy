<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION['user_id'])) exit;

$id = $_POST['id'];
$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("DELETE FROM transactions WHERE id=? AND user_id=?");
$stmt->bind_param("ii", $id, $user_id);
$stmt->execute();

echo json_encode(["success" => true]);
?>