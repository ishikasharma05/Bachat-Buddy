<?php
// backend/user/update_profile.php
session_start();
header('Content-Type: application/json');

require_once '../../config/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not authenticated']);
    exit();
}

$user_id = $_SESSION['user_id'];

// Determine update type based on request
if (isset($_FILES['profile_image'])) {
    // ===== PROFILE IMAGE UPDATE =====
    $file = $_FILES['profile_image'];
    
    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['status' => 'error', 'message' => 'File upload error']);
        exit();
    }
    
    // Validate file type
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    if (!in_array($file['type'], $allowedTypes)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid file type. Only JPG, PNG, GIF, and WEBP allowed']);
        exit();
    }
    
    // Validate file size (max 5MB)
    if ($file['size'] > 5 * 1024 * 1024) {
        echo json_encode(['status' => 'error', 'message' => 'File size must be less than 5MB']);
        exit();
    }
    
    try {
        // Create uploads directory if it doesn't exist
        $uploadDir = '../../uploads/profiles/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'profile_' . $user_id . '_' . time() . '.' . $extension;
        $targetPath = $uploadDir . $filename;
        $dbPath = 'uploads/profiles/' . $filename;
        
        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            
            // Check if profile_image column exists
            $checkColumn = $conn->query("SHOW COLUMNS FROM users LIKE 'profile_image'");
            if ($checkColumn->num_rows == 0) {
                $conn->query("ALTER TABLE users ADD COLUMN profile_image VARCHAR(255) DEFAULT 'uploads/default.png' AFTER password");
            }
            
            // Get old image to delete
            $stmt = $conn->prepare("SELECT profile_image FROM users WHERE id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $oldImage = $result->fetch_assoc()['profile_image'] ?? null;
            $stmt->close();
            
            // Update database
            $stmt = $conn->prepare("UPDATE users SET profile_image = ? WHERE id = ?");
            $stmt->bind_param("si", $dbPath, $user_id);
            
            if ($stmt->execute()) {
                // Delete old image if it exists and is not default
                if ($oldImage && $oldImage !== 'uploads/default.png' && file_exists('../../' . $oldImage)) {
                    unlink('../../' . $oldImage);
                }
                
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Profile image updated successfully!',
                    'image_url' => $dbPath
                ]);
            } else {
                // Delete uploaded file if database update fails
                unlink($targetPath);
                echo json_encode(['status' => 'error', 'message' => 'Failed to update database']);
            }
            
            $stmt->close();
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to move uploaded file']);
        }
        
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
    }
    
} else {
    // ===== OTHER PROFILE UPDATES (Budget, Name, Email, Phone) =====
    $jsonData = file_get_contents('php://input');
    $data = json_decode($jsonData, true);
    
    if (!$data) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid request data']);
        exit();
    }
    
    try {
        // Check which field to update
        if (isset($data['monthly_budget'])) {
            // ===== UPDATE MONTHLY BUDGET =====
            $monthly_budget = floatval($data['monthly_budget']);
            
            if ($monthly_budget < 0) {
                echo json_encode(['status' => 'error', 'message' => 'Invalid budget amount']);
                exit();
            }
            
            // Check if column exists
            $checkColumn = $conn->query("SHOW COLUMNS FROM users LIKE 'monthly_budget'");
            if ($checkColumn->num_rows == 0) {
                $conn->query("ALTER TABLE users ADD COLUMN monthly_budget DECIMAL(10,2) DEFAULT 0.00 AFTER mobile");
            }
            
            $stmt = $conn->prepare("UPDATE users SET monthly_budget = ? WHERE id = ?");
            $stmt->bind_param("di", $monthly_budget, $user_id);
            
            if ($stmt->execute()) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Monthly budget updated successfully!',
                    'monthly_budget' => $monthly_budget
                ]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to update budget']);
            }
            
            $stmt->close();
            
        } elseif (isset($data['full_name']) || isset($data['email']) || isset($data['mobile'])) {
            // ===== UPDATE BASIC INFO =====
            $full_name = isset($data['full_name']) ? trim($data['full_name']) : null;
            $email = isset($data['email']) ? trim($data['email']) : null;
            $mobile = isset($data['mobile']) ? trim($data['mobile']) : null;
            
            $updates = [];
            $types = "";
            $values = [];
            
            if ($full_name) {
                $updates[] = "full_name = ?";
                $types .= "s";
                $values[] = $full_name;
            }
            
            if ($email) {
                // Check if email already exists for another user
                $checkEmail = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
                $checkEmail->bind_param("si", $email, $user_id);
                $checkEmail->execute();
                $result = $checkEmail->get_result();
                if ($result->num_rows > 0) {
                    echo json_encode(['status' => 'error', 'message' => 'Email already exists']);
                    exit();
                }
                $checkEmail->close();
                
                $updates[] = "email = ?";
                $types .= "s";
                $values[] = $email;
            }
            
            if ($mobile) {
                $updates[] = "mobile = ?";
                $types .= "s";
                $values[] = $mobile;
            }
            
            if (empty($updates)) {
                echo json_encode(['status' => 'error', 'message' => 'No data to update']);
                exit();
            }
            
            $types .= "i";
            $values[] = $user_id;
            
            $sql = "UPDATE users SET " . implode(", ", $updates) . " WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param($types, ...$values);
            
            if ($stmt->execute()) {
                // Update session if name or email changed
                if ($full_name) $_SESSION['user_name'] = $full_name;
                if ($email) $_SESSION['user_email'] = $email;
                
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Profile updated successfully!'
                ]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to update profile']);
            }
            
            $stmt->close();
            
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No valid update data provided']);
        }
        
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
    }
}

$conn->close();
?>