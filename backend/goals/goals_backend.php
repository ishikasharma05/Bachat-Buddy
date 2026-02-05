<?php
// Bachat-Buddy Goals Backend
// Make sure there are NO SPACES OR TEXT before this <?php tag

session_start();
header('Content-Type: application/json');

// Test endpoint - visit: yoursite.com/goals_backend.php?action=test
if (isset($_GET['action']) && $_GET['action'] === 'test') {
    echo json_encode([
        'success' => true,
        'message' => 'Backend is working!',
        'logged_in' => isset($_SESSION['user_id']),
        'user_id' => $_SESSION['user_id'] ?? 'Not set'
    ]);
    exit();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit();
}

// Database credentials - CHANGE THESE TO MATCH YOUR DATABASE
$host = 'localhost';
$dbname = 'bachat_buddy';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $userId = $_SESSION['user_id'];
    $action = $_GET['action'] ?? '';
    
    // FETCH GOALS
    if ($action === 'fetch') {
        $stmt = $conn->prepare("SELECT * FROM goals WHERE user_id = :user_id ORDER BY created_at DESC");
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        
        $goals = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $goal) {
            $goals[] = [
                'id' => (int)$goal['id'],
                'goalName' => $goal['goal_name'],
                'targetAmount' => (float)$goal['target_amount'],
                'savedAmount' => (float)$goal['saved_amount'],
                'progress' => (float)$goal['progress'],
                'status' => $goal['status'],
                'createdAt' => $goal['created_at']
            ];
        }
        
        echo json_encode(['success' => true, 'goals' => $goals]);
    }
    
    // ADD GOAL
    elseif ($action === 'add') {
        $name = trim($_POST['goalName'] ?? '');
        $target = floatval($_POST['targetAmount'] ?? 0);
        $saved = floatval($_POST['savedAmount'] ?? 0);
        
        if (empty($name)) {
            echo json_encode(['success' => false, 'message' => 'Goal name required']);
            exit();
        }
        if ($target <= 0) {
            echo json_encode(['success' => false, 'message' => 'Target must be greater than 0']);
            exit();
        }
        if ($saved < 0) $saved = 0;
        if ($saved > $target) $saved = $target;
        
        $progress = ($saved / $target) * 100;
        $status = $progress >= 100 ? 'Completed' : 'In Progress';
        
        $stmt = $conn->prepare("INSERT INTO goals (user_id, goal_name, target_amount, saved_amount, progress, status, created_at) 
                                VALUES (:uid, :name, :target, :saved, :progress, :status, NOW())");
        $stmt->execute([
            ':uid' => $userId,
            ':name' => $name,
            ':target' => $target,
            ':saved' => $saved,
            ':progress' => $progress,
            ':status' => $status
        ]);
        
        echo json_encode(['success' => true, 'message' => 'Goal added!', 'goalId' => (int)$conn->lastInsertId()]);
    }
    
    // ADD FUNDS
    elseif ($action === 'add_funds') {
        $id = intval($_POST['goalId'] ?? 0);
        $amount = floatval($_POST['additionalAmount'] ?? 0);
        
        if ($id <= 0 || $amount <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid data']);
            exit();
        }
        
        $stmt = $conn->prepare("SELECT * FROM goals WHERE id = :id AND user_id = :uid");
        $stmt->execute([':id' => $id, ':uid' => $userId]);
        $goal = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$goal) {
            echo json_encode(['success' => false, 'message' => 'Goal not found']);
            exit();
        }
        
        $newSaved = min((float)$goal['saved_amount'] + $amount, (float)$goal['target_amount']);
        $newProgress = ($newSaved / (float)$goal['target_amount']) * 100;
        $newStatus = $newProgress >= 100 ? 'Completed' : 'In Progress';
        
        $stmt = $conn->prepare("UPDATE goals SET saved_amount = :saved, progress = :progress, status = :status, updated_at = NOW() 
                                WHERE id = :id AND user_id = :uid");
        $stmt->execute([
            ':saved' => $newSaved,
            ':progress' => $newProgress,
            ':status' => $newStatus,
            ':id' => $id,
            ':uid' => $userId
        ]);
        
        echo json_encode(['success' => true, 'message' => 'Funds added!']);
    }
    
    // EDIT GOAL
    elseif ($action === 'edit') {
        $id = intval($_POST['goalId'] ?? 0);
        $name = trim($_POST['goalName'] ?? '');
        $target = floatval($_POST['targetAmount'] ?? 0);
        $saved = floatval($_POST['savedAmount'] ?? 0);
        
        if ($id <= 0 || empty($name) || $target <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid data']);
            exit();
        }
        
        if ($saved < 0) $saved = 0;
        if ($saved > $target) $saved = $target;
        
        $progress = ($saved / $target) * 100;
        $status = $progress >= 100 ? 'Completed' : 'In Progress';
        
        $stmt = $conn->prepare("UPDATE goals SET goal_name = :name, target_amount = :target, saved_amount = :saved, 
                                progress = :progress, status = :status, updated_at = NOW() 
                                WHERE id = :id AND user_id = :uid");
        $stmt->execute([
            ':name' => $name,
            ':target' => $target,
            ':saved' => $saved,
            ':progress' => $progress,
            ':status' => $status,
            ':id' => $id,
            ':uid' => $userId
        ]);
        
        echo json_encode(['success' => true, 'message' => 'Goal updated!']);
    }
    
    // DELETE GOAL
    elseif ($action === 'delete') {
        $id = intval($_POST['goalId'] ?? 0);
        
        if ($id <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid ID']);
            exit();
        }
        
        $stmt = $conn->prepare("DELETE FROM goals WHERE id = :id AND user_id = :uid");
        $stmt->execute([':id' => $id, ':uid' => $userId]);
        
        echo json_encode(['success' => true, 'message' => 'Goal deleted!']);
    }
    
    else {
        echo json_encode(['success' => false, 'message' => 'Unknown action']);
    }
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>