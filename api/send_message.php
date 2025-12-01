<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['logged_user'])) {
    echo json_encode(['error' => 'Not authenticated']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Method not allowed']);
    exit();
}

require '../db.php';

$user_id = $_SESSION['user_id'];
$message = isset($_POST['message']) ? trim($_POST['message']) : '';

// Validate message
if (empty($message)) {
    echo json_encode(['error' => 'Message cannot be empty']);
    exit();
}

// Limit message length to 200 characters
if (strlen($message) > 200) {
    echo json_encode(['error' => 'Message too long (max 200 characters)']);
    exit();
}

// Sanitize message
$message = stripslashes($message);
$message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');

// Insert message into database
$stmt = $conn->prepare("INSERT INTO chat_messages (user_id, message, created_at) VALUES (?, ?, NOW())");
$stmt->bind_param("is", $user_id, $message);

if ($stmt->execute()) {
    echo json_encode([
        'success' => true,
        'message' => 'Message sent'
    ]);
} else {
    echo json_encode(['error' => 'Failed to send message']);
}

$stmt->close();
$conn->close();

