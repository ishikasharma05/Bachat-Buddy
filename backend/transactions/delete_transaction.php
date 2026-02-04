<?php
/**
 * Backend: Delete Transaction
 * Handles deleting transactions
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

if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit();
}

require_once __DIR__ . '/../../config/db.php';

try {
    // Get transaction ID
    $transaction_id = 0;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $jsonData = file_get_contents('php://input');
        
        if (!empty($jsonData)) {
            $data = json_decode($jsonData, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $transaction_id = intval($data['id'] ?? 0);
            }
        }
        
        // Fallback to POST data if JSON parsing failed
        if ($transaction_id === 0) {
            $transaction_id = intval($_POST['id'] ?? 0);
        }
    } else {
        // For DELETE requests, get ID from query string
        $transaction_id = intval($_GET['id'] ?? 0);
    }
    
    if ($transaction_id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid transaction ID']);
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
    
    // Delete the transaction
    $delete_stmt = $conn->prepare("DELETE FROM transactions WHERE id = ? AND user_id = ?");
    
    if (!$delete_stmt) {
        echo json_encode(['status' => 'error', 'message' => 'Prepare failed: ' . $conn->error]);
        exit();
    }
    
    $delete_stmt->bind_param("ii", $transaction_id, $user_id);
    
    if ($delete_stmt->execute()) {
        if ($delete_stmt->affected_rows > 0) {
            echo json_encode([
                'status' => 'success', 
                'message' => 'Transaction deleted successfully!'
            ]);
        } else {
            echo json_encode([
                'status' => 'error', 
                'message' => 'Transaction could not be deleted.'
            ]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Execute failed: ' . $delete_stmt->error]);
    }
    
    $delete_stmt->close();
    $conn->close();
    
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Exception: ' . $e->getMessage()]);
}

ob_end_flush();
?>