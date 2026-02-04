<?php
// backend/transactions/add_transaction.php

// Start output buffering to catch any accidental output
ob_start();

// Start session
session_start();

// Set headers FIRST (before ANY output)
header('Content-Type: application/json');

// Clear any output that might have leaked
ob_clean();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not authenticated. Please log in.']);
    exit();
}

$user_id = $_SESSION['user_id'];

// Check request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit();
}

// Include database connection
require_once __DIR__ . '/../../config/db.php';

try {
    // Get JSON data
    $jsonData = file_get_contents('php://input');
    
    if (empty($jsonData)) {
        echo json_encode(['status' => 'error', 'message' => 'No data received']);
        exit();
    }
    
    $data = json_decode($jsonData, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid JSON: ' . json_last_error_msg()]);
        exit();
    }
    
    // Extract and validate data
    $type = isset($data['type']) ? trim($data['type']) : '';
    $amount = isset($data['amount']) ? floatval($data['amount']) : 0;
    $category = isset($data['category']) ? trim($data['category']) : '';
    $description = isset($data['description']) ? trim($data['description']) : '';
    $tags = isset($data['tags']) ? trim($data['tags']) : '';
    $date = isset($data['date']) ? trim($data['date']) : '';
    
    // Validate required fields
    if (empty($type)) {
        echo json_encode(['status' => 'error', 'message' => 'Transaction type is required!']);
        exit();
    }
    
    if ($amount <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Amount must be greater than zero!']);
        exit();
    }
    
    if (empty($date)) {
        echo json_encode(['status' => 'error', 'message' => 'Date is required!']);
        exit();
    }
    
    if (empty($category)) {
        echo json_encode(['status' => 'error', 'message' => 'Category is required!']);
        exit();
    }
    
    // Validate transaction type
    $validTypes = ['income', 'expense', 'savings', 'withdraw_savings'];
    if (!in_array($type, $validTypes)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid transaction type: ' . $type]);
        exit();
    }
    
    // Check database connection
    if ($conn->connect_error) {
        echo json_encode(['status' => 'error', 'message' => 'Database connection failed']);
        exit();
    }
    
    // Prepare SQL statement
    $stmt = $conn->prepare("INSERT INTO transactions (user_id, type, amount, category, description, tags, date) VALUES (?, ?, ?, ?, ?, ?, ?)");
    
    if (!$stmt) {
        echo json_encode(['status' => 'error', 'message' => 'Failed to prepare statement: ' . $conn->error]);
        exit();
    }
    
    $stmt->bind_param("isdssss", $user_id, $type, $amount, $category, $description, $tags, $date);
    
    if ($stmt->execute()) {
        echo json_encode([
            'status' => 'success', 
            'message' => 'Transaction added successfully!',
            'transaction_id' => $conn->insert_id
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $stmt->error]);
    }
    
    $stmt->close();
    $conn->close();
    
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Exception: ' . $e->getMessage()]);
}

// End output buffering and flush
ob_end_flush();
?>