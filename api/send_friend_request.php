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

$sender_id = $_SESSION['user_id'];
$receiver_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : null;

if (!$receiver_id) {
    echo json_encode(['error' => 'User ID required']);
    exit();
}

if ($sender_id == $receiver_id) {
    echo json_encode(['error' => 'Cannot send friend request to yourself']);
    exit();
}

// Check if they are already friends
$stmt = $conn->prepare("
    SELECT COUNT(*) as count 
    FROM friendships 
    WHERE (user_id = ? AND friend_id = ?) OR (user_id = ? AND friend_id = ?)
");
$stmt->bind_param("iiii", $sender_id, $receiver_id, $receiver_id, $sender_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
if ($row['count'] > 0) {
    echo json_encode(['error' => 'You are already friends with this user']);
    $stmt->close();
    $conn->close();
    exit();
}
$stmt->close();

// Check if there's already a pending request
$stmt = $conn->prepare("
    SELECT COUNT(*) as count 
    FROM friend_requests 
    WHERE ((sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?))
    AND status = 'pending'
");
$stmt->bind_param("iiii", $sender_id, $receiver_id, $receiver_id, $sender_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
if ($row['count'] > 0) {
    echo json_encode(['error' => 'Friend request already pending']);
    $stmt->close();
    $conn->close();
    exit();
}
$stmt->close();

// Create friend request
$stmt = $conn->prepare("INSERT INTO friend_requests (sender_id, receiver_id, status, created_at) VALUES (?, ?, 'pending', NOW())");
$stmt->bind_param("ii", $sender_id, $receiver_id);

if ($stmt->execute()) {
    echo json_encode([
        'success' => true,
        'message' => 'Friend request sent'
    ]);
} else {
    echo json_encode(['error' => 'Failed to send friend request']);
}

$stmt->close();
$conn->close();

