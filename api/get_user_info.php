<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['logged_user'])) {
    echo json_encode(['error' => 'Not authenticated']);
    exit();
}

require '../db.php';

$target_user_id = isset($_GET['user_id']) ? (int)$_GET['user_id'] : null;
$current_user_id = $_SESSION['user_id'];

if (!$target_user_id) {
    echo json_encode(['error' => 'User ID required']);
    exit();
}

// Get user profile information
$stmt = $conn->prepare("
    SELECT id, username, bio, country, major, instagram_handle, avatar_color, profile_picture_url 
    FROM users 
    WHERE id = ?
");
$stmt->bind_param("i", $target_user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['error' => 'User not found']);
    $stmt->close();
    $conn->close();
    exit();
}

$user = $result->fetch_assoc();
$stmt->close();

// Check if they are already friends
$stmt = $conn->prepare("
    SELECT COUNT(*) as is_friend 
    FROM friendships 
    WHERE (user_id = ? AND friend_id = ?) OR (user_id = ? AND friend_id = ?)
");
$stmt->bind_param("iiii", $current_user_id, $target_user_id, $target_user_id, $current_user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$is_friend = $row['is_friend'] > 0;
$stmt->close();

// Check if there's a pending friend request
$stmt = $conn->prepare("
    SELECT id, status, sender_id 
    FROM friend_requests 
    WHERE ((sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?))
    AND status = 'pending'
");
$stmt->bind_param("iiii", $current_user_id, $target_user_id, $target_user_id, $current_user_id);
$stmt->execute();
$result = $stmt->get_result();

$friend_request_status = 'none';
$request_id = null;
if ($result->num_rows > 0) {
    $request = $result->fetch_assoc();
    $request_id = $request['id'];
    if ($request['sender_id'] == $current_user_id) {
        $friend_request_status = 'sent';
    } else {
        $friend_request_status = 'received';
    }
}
$stmt->close();
$conn->close();

echo json_encode([
    'success' => true,
    'user' => [
        'id' => (int)$user['id'],
        'username' => $user['username'],
        'bio' => $user['bio'],
        'country' => $user['country'],
        'major' => $user['major'],
        'instagram_handle' => $user['instagram_handle'],
        'avatar_color' => $user['avatar_color'],
        'profile_picture_url' => $user['profile_picture_url']
    ],
    'is_friend' => $is_friend,
    'friend_request_status' => $friend_request_status,
    'request_id' => $request_id,
    'is_current_user' => ($target_user_id == $current_user_id)
]);

