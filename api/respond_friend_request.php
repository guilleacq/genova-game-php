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

$current_user_id = $_SESSION['user_id'];
$request_id = isset($_POST['request_id']) ? (int)$_POST['request_id'] : null;
$action = isset($_POST['action']) ? $_POST['action'] : null;

if (!$request_id || !$action) {
    echo json_encode(['error' => 'Request ID and action required']);
    exit();
}

if ($action !== 'accept' && $action !== 'reject') {
    echo json_encode(['error' => 'Invalid action']);
    exit();
}

// Get the friend request and verify it's for the current user
$stmt = $conn->prepare("SELECT sender_id, receiver_id, status FROM friend_requests WHERE id = ?");
$stmt->bind_param("i", $request_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['error' => 'Friend request not found']);
    $stmt->close();
    $conn->close();
    exit();
}

$request = $result->fetch_assoc();
$stmt->close();

// Verify the current user is the receiver
if ($request['receiver_id'] != $current_user_id) {
    echo json_encode(['error' => 'Unauthorized']);
    $conn->close();
    exit();
}

if ($request['status'] !== 'pending') {
    echo json_encode(['error' => 'Request already processed']);
    $conn->close();
    exit();
}

if ($action === 'accept') {
    // Update request status
    $stmt = $conn->prepare("UPDATE friend_requests SET status = 'accepted' WHERE id = ?");
    $stmt->bind_param("i", $request_id);
    $stmt->execute();
    $stmt->close();

    // Create bidirectional friendship
    $sender_id = $request['sender_id'];
    $receiver_id = $request['receiver_id'];

    $stmt = $conn->prepare("INSERT INTO friendships (user_id, friend_id, created_at) VALUES (?, ?, NOW()), (?, ?, NOW())");
    $stmt->bind_param("iiii", $sender_id, $receiver_id, $receiver_id, $sender_id);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Friend request accepted'
        ]);
    } else {
        echo json_encode(['error' => 'Failed to create friendship']);
    }
    $stmt->close();
} else {
    // Reject
    $stmt = $conn->prepare("UPDATE friend_requests SET status = 'rejected' WHERE id = ?");
    $stmt->bind_param("i", $request_id);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Friend request rejected'
        ]);
    } else {
        echo json_encode(['error' => 'Failed to reject request']);
    }
    $stmt->close();
}

$conn->close();

