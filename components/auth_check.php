<?php
// components/auth_check.php
// DO NOT output anything from this file!

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // For AJAX requests, don't redirect
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Not authenticated']);
        exit();
    }
    
    // For normal page requests, redirect
    header("Location: login-pages/login.php");
    exit();
}

// Store user_id in a variable for easy access
$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'] ?? 'User';
$user_email = $_SESSION['user_email'] ?? '';
?>