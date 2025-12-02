<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['logged_user'])) {
    echo json_encode(['error' => 'Not authenticated']);
    exit();
}

require '../db.php';

$user_id = $_SESSION['user_id'];

// Update current user's last_activity (heartbeat)
$stmt = $conn->prepare("UPDATE users SET last_activity = NOW() WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->close();

// Get all active users (activity within last 30 seconds)
$stmt = $conn->prepare("
    SELECT id, username, pos_x, pos_y, avatar_color, profile_picture_url 
    FROM users 
    WHERE last_activity >= DATE_SUB(NOW(), INTERVAL 30 SECOND)
");
$stmt->execute();
$result = $stmt->get_result();

$players = [];
while ($row = $result->fetch_assoc()) {
    $players[] = [
        'id' => (int)$row['id'],
        'username' => $row['username'],
        'pos_x' => (int)$row['pos_x'],
        'pos_y' => (int)$row['pos_y'],
        'avatar_color' => $row['avatar_color'],
        'profile_picture_url' => $row['profile_picture_url']
    ];
}

$stmt->close();
$conn->close();

echo json_encode([
    'success' => true,
    'players' => $players,
    'count' => count($players)
]);

