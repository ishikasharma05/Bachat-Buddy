<?php
session_start();
require_once __DIR__ . '/../../config/db.php';

header('Content-Type: application/json');

// Check authentication
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

$user_id = $_SESSION['user_id'];

// For now, this just returns success since we're calculating notifications in real-time
// In the future, you could mark them as "read" in a database table

echo json_encode([
    'success' => true,
    'message' => 'Notifications cleared'
]);

$conn->close();
?>
