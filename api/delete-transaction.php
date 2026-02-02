<?php
// Show errors (for learning)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Response will be JSON
header("Content-Type: application/json");

// Database connection
require_once __DIR__ . "/../config/db.php";

// Only allow POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        "success" => false,
        "message" => "Invalid request method"
    ]);
    exit;
}

// Get transaction id
$id = $_POST['id'] ?? '';

// Validate id
if (!$id) {
    echo json_encode([
        "success" => false,
        "message" => "Transaction ID not received"
    ]);
    exit;
}

// Prepare delete query
$stmt = $conn->prepare(
    "DELETE FROM transactions WHERE id = ?"
);

// If prepare fails
if (!$stmt) {
    echo json_encode([
        "success" => false,
        "message" => $conn->error
    ]);
    exit;
}

// Bind id (integer)
$stmt->bind_param("i", $id);

// Execute query
if ($stmt->execute()) {
    echo json_encode([
        "success" => true,
        "message" => "Transaction deleted successfully"
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => $stmt->error
    ]);
}

// Close
$stmt->close();
$conn->close();
?>