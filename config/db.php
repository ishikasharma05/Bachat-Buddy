<?php
$conn = new mysqli("localhost", "root", "", "bachat_buddy");

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>
