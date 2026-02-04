<?php
/**
 * Backend: Edit Transaction
 * Handles updating existing transactions
 */

ob_start();
session_start();
header('Content-Type: application/json');
ob_clean();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not authenticated']);
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit();
}

require_once __DIR__ . '/../../config/db.php';

try {
    $jsonData = file_get_contents('php://input');
    
    if (empty($jsonData)) {
        echo json_encode(['status' => 'error', 'message' => 'No data received']);
        exit();
    }
    
    $data = json_decode($jsonData, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid JSON']);
        exit();
    }
    
    $transaction_id = intval($data['id'] ?? 0);
    $type = trim($data['type'] ?? '');
    $amount = floatval($data['amount'] ?? 0);
    $category = trim($data['category'] ?? '');
    $description = trim($data['description'] ?? '');
    $tags = trim($data['tags'] ?? '');
    $date = trim($data['date'] ?? '');
    
    if ($transaction_id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid transaction ID']);
        exit();
    }
    
    if (empty($type) || $amount <= 0 || empty($date) || empty($category)) {
        echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
        exit();
    }
    
    $validTypes = ['income', 'expense', 'savings', 'withdraw_savings'];
    if (!in_array($type, $validTypes)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid type']);
        exit();
    }
    
    if ($conn->connect_error) {
        echo json_encode(['status' => 'error', 'message' => 'Database error']);
        exit();
    }
    
    // First, verify that the transaction belongs to the user
    $check_stmt = $conn->prepare("SELECT id FROM transactions WHERE id = ? AND user_id = ?");
    
    if (!$check_stmt) {
        echo json_encode(['status' => 'error', 'message' => 'Prepare failed: ' . $conn->error]);
        exit();
    }
    
    $check_stmt->bind_param("ii", $transaction_id, $user_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows === 0) {
        echo json_encode(['status' => 'error', 'message' => 'Transaction not found or access denied']);
        exit();
    }
    
    $check_stmt->close();
    
    // Update the transaction
    $stmt = $conn->prepare("UPDATE transactions SET type = ?, amount = ?, category = ?, description = ?, tags = ?, date = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ? AND user_id = ?");
    
    if (!$stmt) {
        echo json_encode(['status' => 'error', 'message' => 'Prepare failed: ' . $conn->error]);
        exit();
    }
    
    $stmt->bind_param("sdsssiii", $type, $amount, $category, $description, $tags, $date, $transaction_id, $user_id);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode([
                'status' => 'success', 
                'message' => 'Transaction updated successfully!'
            ]);
        } else {
            echo json_encode([
                'status' => 'success', 
                'message' => 'No changes were made to the transaction.'
            ]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Execute failed: ' . $stmt->error]);
    }
    
    $stmt->close();
    $conn->close();
    
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Exception: ' . $e->getMessage()]);
}

ob_end_flush();
?>