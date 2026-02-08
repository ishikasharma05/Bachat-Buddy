<?php
/**
 * ============================================
 * BACHAT-BUDDY GOALS BACKEND (UPDATED)
 * ============================================
 * Updated to match your existing database structure:
 * - Uses 'current_amount' instead of 'saved_amount'
 * - Calculates progress on-the-fly (not stored in DB)
 * - Uses enum status: 'active', 'completed', 'cancelled'
 * ============================================
 */

// Start PHP session to track logged-in user
session_start();

// Set response type to JSON
header('Content-Type: application/json');

// ============================================
// SECURITY CHECK: Verify user is logged in
// ============================================
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit();
}

// ============================================
// DATABASE CONFIGURATION
// ============================================
// IMPORTANT: Change these to match your MySQL setup
$host = 'localhost';        // Usually 'localhost'
$dbname = 'bachat_buddy';   // Your database name
$username = 'root';         // Your MySQL username
$password = '';             // Your MySQL password (empty for XAMPP default)

try {
    // ============================================
    // CREATE DATABASE CONNECTION
    // ============================================
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    
    // Set error mode to throw exceptions
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get logged-in user ID from session
    $userId = $_SESSION['user_id'];
    
    // Get action from URL parameter (e.g., ?action=fetch)
    $action = $_GET['action'] ?? '';
    
    // ============================================
    // ACTION: FETCH ALL GOALS
    // ============================================
    if ($action === 'fetch') {
        // Prepare SQL query to get all goals for this user
        $stmt = $conn->prepare("
            SELECT * FROM goals 
            WHERE user_id = :user_id 
            ORDER BY created_at DESC
        ");
        
        // Bind user ID parameter
        $stmt->bindParam(':user_id', $userId);
        
        // Execute query
        $stmt->execute();
        
        // Convert results to array of goals
        $goals = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $goal) {
            // Calculate progress on-the-fly
            $currentAmount = (float)$goal['current_amount'];
            $targetAmount = (float)$goal['target_amount'];
            $progress = $targetAmount > 0 ? ($currentAmount / $targetAmount) * 100 : 0;
            
            // Determine display status
            $displayStatus = $goal['status'] === 'completed' ? 'Completed' : 
                           ($goal['status'] === 'cancelled' ? 'Cancelled' : 'In Progress');
            
            $goals[] = [
                'id' => (int)$goal['id'],
                'goalName' => $goal['goal_name'],
                'targetAmount' => $targetAmount,
                'savedAmount' => $currentAmount,  // Map current_amount to savedAmount for frontend
                'progress' => $progress,
                'status' => $displayStatus,
                'createdAt' => $goal['created_at']
            ];
        }
        
        // Return success response with goals
        echo json_encode(['success' => true, 'goals' => $goals]);
    }
    
    // ============================================
    // ACTION: ADD NEW GOAL
    // ============================================
    elseif ($action === 'add') {
        // Get data from POST request
        $name = trim($_POST['goalName'] ?? '');
        $target = floatval($_POST['targetAmount'] ?? 0);
        $saved = floatval($_POST['savedAmount'] ?? 0);
        
        // Validation: Check goal name
        if (empty($name)) {
            echo json_encode(['success' => false, 'message' => 'Goal name is required']);
            exit();
        }
        
        // Validation: Check target amount
        if ($target <= 0) {
            echo json_encode(['success' => false, 'message' => 'Target must be greater than 0']);
            exit();
        }
        
        // Ensure saved amount is within valid range
        if ($saved < 0) $saved = 0;
        if ($saved > $target) $saved = $target;
        
        // Calculate progress to determine status
        $progress = ($saved / $target) * 100;
        
        // Determine status based on progress
        $status = $progress >= 100 ? 'completed' : 'active';
        
        // Prepare INSERT query (using current_amount as per your DB structure)
        $stmt = $conn->prepare("
            INSERT INTO goals (user_id, goal_name, target_amount, current_amount, status, created_at) 
            VALUES (:uid, :name, :target, :saved, :status, NOW())
        ");
        
        // Execute with parameters
        $stmt->execute([
            ':uid' => $userId,
            ':name' => $name,
            ':target' => $target,
            ':saved' => $saved,
            ':status' => $status
        ]);
        
        // Return success with new goal ID
        echo json_encode([
            'success' => true, 
            'message' => 'Goal added successfully!', 
            'goalId' => (int)$conn->lastInsertId()
        ]);
    }
    
    // ============================================
    // ACTION: ADD FUNDS TO EXISTING GOAL
    // ============================================
    elseif ($action === 'add_funds') {
        // Get data from POST request
        $goalId = intval($_POST['goalId'] ?? 0);
        $amount = floatval($_POST['additionalAmount'] ?? 0);
        
        // Validation: Check goal ID and amount
        if ($goalId <= 0 || $amount <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid data']);
            exit();
        }
        
        // Fetch current goal data
        $stmt = $conn->prepare("SELECT * FROM goals WHERE id = :id AND user_id = :uid");
        $stmt->execute([':id' => $goalId, ':uid' => $userId]);
        $goal = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Check if goal exists
        if (!$goal) {
            echo json_encode(['success' => false, 'message' => 'Goal not found']);
            exit();
        }
        
        // Calculate new current amount (don't exceed target)
        $newAmount = min((float)$goal['current_amount'] + $amount, (float)$goal['target_amount']);
        
        // Calculate new progress
        $newProgress = ($newAmount / (float)$goal['target_amount']) * 100;
        
        // Update status if goal is completed
        $newStatus = $newProgress >= 100 ? 'completed' : 'active';
        
        // Update goal in database
        $stmt = $conn->prepare("
            UPDATE goals 
            SET current_amount = :amount, status = :status, updated_at = NOW() 
            WHERE id = :id AND user_id = :uid
        ");
        
        $stmt->execute([
            ':amount' => $newAmount,
            ':status' => $newStatus,
            ':id' => $goalId,
            ':uid' => $userId
        ]);
        
        // Return success
        echo json_encode(['success' => true, 'message' => 'Funds added successfully!']);
    }
    
    // ============================================
    // ACTION: EDIT GOAL
    // ============================================
    elseif ($action === 'edit') {
        // Get data from POST request
        $goalId = intval($_POST['goalId'] ?? 0);
        $name = trim($_POST['goalName'] ?? '');
        $target = floatval($_POST['targetAmount'] ?? 0);
        $saved = floatval($_POST['savedAmount'] ?? 0);
        
        // Validation
        if ($goalId <= 0 || empty($name) || $target <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid data']);
            exit();
        }
        
        // Ensure saved amount is within valid range
        if ($saved < 0) $saved = 0;
        if ($saved > $target) $saved = $target;
        
        // Calculate progress
        $progress = ($saved / $target) * 100;
        
        // Determine status
        $status = $progress >= 100 ? 'completed' : 'active';
        
        // Update goal in database
        $stmt = $conn->prepare("
            UPDATE goals 
            SET goal_name = :name, target_amount = :target, current_amount = :saved, 
                status = :status, updated_at = NOW() 
            WHERE id = :id AND user_id = :uid
        ");
        
        $stmt->execute([
            ':name' => $name,
            ':target' => $target,
            ':saved' => $saved,
            ':status' => $status,
            ':id' => $goalId,
            ':uid' => $userId
        ]);
        
        // Return success
        echo json_encode(['success' => true, 'message' => 'Goal updated successfully!']);
    }
    
    // ============================================
    // ACTION: DELETE GOAL
    // ============================================
    elseif ($action === 'delete') {
        // Get goal ID from POST request
        $goalId = intval($_POST['goalId'] ?? 0);
        
        // Validation
        if ($goalId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid ID']);
            exit();
        }
        
        // Delete goal from database
        $stmt = $conn->prepare("DELETE FROM goals WHERE id = :id AND user_id = :uid");
        $stmt->execute([':id' => $goalId, ':uid' => $userId]);
        
        // Return success
        echo json_encode(['success' => true, 'message' => 'Goal deleted successfully!']);
    }
    
    // ============================================
    // UNKNOWN ACTION
    // ============================================
    else {
        echo json_encode(['success' => false, 'message' => 'Unknown action']);
    }
    
} catch (PDOException $e) {
    // ============================================
    // DATABASE ERROR HANDLING
    // ============================================
    echo json_encode([
        'success' => false, 
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>