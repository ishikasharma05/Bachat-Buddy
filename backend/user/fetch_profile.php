<?php
// backend/user/fetch_profile.php
session_start();
header('Content-Type: application/json');

require_once '../../config/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not authenticated']);
    exit();
}

$user_id = $_SESSION['user_id'];

try {
    // First, check and add missing columns if needed
    $checkProfileImage = $conn->query("SHOW COLUMNS FROM users LIKE 'profile_image'");
    if ($checkProfileImage->num_rows == 0) {
        $conn->query("ALTER TABLE users ADD COLUMN profile_image VARCHAR(255) DEFAULT 'uploads/default.png' AFTER password");
    }
    
    $checkBudget = $conn->query("SHOW COLUMNS FROM users LIKE 'monthly_budget'");
    if ($checkBudget->num_rows == 0) {
        $conn->query("ALTER TABLE users ADD COLUMN monthly_budget DECIMAL(10,2) DEFAULT 0.00 AFTER mobile");
    }
    
    // Fetch user data
    $stmt = $conn->prepare("SELECT id, full_name, email, mobile, profile_image, monthly_budget, created_at FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    
    if (!$user) {
        echo json_encode(['status' => 'error', 'message' => 'User not found']);
        exit();
    }
    
    // Set defaults
    $user['mobile'] = $user['mobile'] ?? 'Not set';
    $user['monthly_budget'] = $user['monthly_budget'] ?? '0';
    $user['profile_image'] = $user['profile_image'] ?? 'uploads/default.png';
    
    // Get total expenses
    $stmt = $conn->prepare("SELECT COALESCE(SUM(amount), 0) as total FROM transactions WHERE user_id = ? AND type = 'expense'");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $totalExpenses = $result->fetch_assoc()['total'];
    $stmt->close();
    
    // Get total savings
    $stmt = $conn->prepare("SELECT COALESCE(SUM(amount), 0) as total FROM transactions WHERE user_id = ? AND type = 'savings'");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $totalSavings = $result->fetch_assoc()['total'];
    $stmt->close();
    
    // Get savings goal (sum of all active goals)
    $stmt = $conn->prepare("SELECT COALESCE(SUM(target_amount), 0) as total FROM goals WHERE user_id = ? AND status = 'active'");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $savingsGoal = $result->fetch_assoc()['total'];
    $stmt->close();
    
    // Calculate savings percentage
    $savingsProgress = $savingsGoal > 0 ? min(100, ($totalSavings / $savingsGoal) * 100) : 0;
    
    echo json_encode([
        'status' => 'success',
        'user' => $user,
        'stats' => [
            'totalExpenses' => floatval($totalExpenses),
            'totalSavings' => floatval($totalSavings),
            'savingsGoal' => floatval($savingsGoal),
            'savingsProgress' => round($savingsProgress, 2)
        ]
    ]);
    
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
}

$conn->close();
?>