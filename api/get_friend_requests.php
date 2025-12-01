<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['logged_user'])) {
    echo json_encode(['error' => 'Not authenticated']);
    exit();
}

require '../db.php';

$user_id = $_SESSION['user_id'];

// Get pending friend requests received by the current user
$stmt = $conn->prepare("
    SELECT fr.id, fr.sender_id, fr.created_at, u.username, u.avatar_color
    FROM friend_requests fr
    JOIN users u ON fr.sender_id = u.id
    WHERE fr.receiver_id = ? AND fr.status = 'pending'
    ORDER BY fr.created_at DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$requests = [];
while ($row = $result->fetch_assoc()) {
    $requests[] = [
        'id' => (int)$row['id'],
        'sender_id' => (int)$row['sender_id'],
        'username' => $row['username'],
        'avatar_color' => $row['avatar_color'],
        'created_at' => $row['created_at']
    ];
}

$stmt->close();
$conn->close();

echo json_encode([
    'success' => true,
    'requests' => $requests,
    'count' => count($requests)
]);

