<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['logged_user'])) {
    echo json_encode(['error' => 'Not authenticated']);
    exit();
}

require '../db.php';

// Get the last 50 messages
$stmt = $conn->prepare("
    SELECT cm.id, cm.user_id, cm.message, cm.created_at, u.username, u.avatar_color, u.profile_picture_url
    FROM chat_messages cm
    JOIN users u ON cm.user_id = u.id
    ORDER BY cm.created_at DESC
    LIMIT 50
");
$stmt->execute();
$result = $stmt->get_result();

$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = [
        'id' => (int)$row['id'],
        'user_id' => (int)$row['user_id'],
        'username' => $row['username'],
        'avatar_color' => $row['avatar_color'],
        'profile_picture_url' => $row['profile_picture_url'],
        'message' => $row['message'],
        'timestamp' => $row['created_at']
    ];
}

// Reverse to show oldest first
$messages = array_reverse($messages);

$stmt->close();
$conn->close();

echo json_encode([
    'success' => true,
    'messages' => $messages
]);

