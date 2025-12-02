<?php
session_start();

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['logged_user']) || !isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Not authenticated']);
    exit();
}

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit();
}

require '../db.php';

$user_id = $_SESSION['user_id'];

// Delete the user from database
$stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);

if ($stmt->execute()) {
    $stmt->close();
    $conn->close();
    
    // Destroy the session (log out)
    session_destroy();
    
    echo json_encode(['success' => true, 'message' => 'Account deleted successfully']);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to delete account']);
}
?>