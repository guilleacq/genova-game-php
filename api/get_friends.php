<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['logged_user'])) {
    echo json_encode(['error' => 'Not authenticated']);
    exit();
}

require '../db.php';

$user_id = $_SESSION['user_id'];

// Get all friends of the current user
$stmt = $conn->prepare("
    SELECT u.id, u.username, u.nickname, u.avatar_color, u.country, u.major,
           (u.last_activity >= DATE_SUB(NOW(), INTERVAL 30 SECOND)) as is_online
    FROM friendships f
    JOIN users u ON f.friend_id = u.id
    WHERE f.user_id = ?
    ORDER BY is_online DESC, u.nickname ASC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$friends = [];
while ($row = $result->fetch_assoc()) {
    $friends[] = [
        'id' => (int)$row['id'],
        'username' => $row['username'],
        'nickname' => $row['nickname'],
        'avatar_color' => $row['avatar_color'],
        'country' => $row['country'],
        'major' => $row['major'],
        'is_online' => (bool)$row['is_online']
    ];
}

$stmt->close();
$conn->close();

echo json_encode([
    'success' => true,
    'friends' => $friends,
    'count' => count($friends)
]);

