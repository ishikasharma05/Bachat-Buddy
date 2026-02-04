<?php
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
    
    $type = trim($data['type'] ?? '');
    $amount = floatval($data['amount'] ?? 0);
    $category = trim($data['category'] ?? '');
    $description = trim($data['description'] ?? '');
    $tags = trim($data['tags'] ?? '');
    $date = trim($data['date'] ?? '');
    
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
    
    $stmt = $conn->prepare("INSERT INTO transactions (user_id, type, amount, category, description, tags, date) VALUES (?, ?, ?, ?, ?, ?, ?)");
    
    if (!$stmt) {
        echo json_encode(['status' => 'error', 'message' => 'Prepare failed: ' . $conn->error]);
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
        echo json_encode(['status' => 'error', 'message' => 'Execute failed: ' . $stmt->error]);
    }
    
    $stmt->close();
    $conn->close();
    
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Exception: ' . $e->getMessage()]);
}

ob_end_flush();
?>