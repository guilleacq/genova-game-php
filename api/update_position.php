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
$pos_x = isset($_POST['x']) ? (int)$_POST['x'] : null;
$pos_y = isset($_POST['y']) ? (int)$_POST['y'] : null;

if ($pos_x === null || $pos_y === null) {
    echo json_encode(['error' => 'Missing coordinates']);
    exit();
}

// Update user position and last_activity
$stmt = $conn->prepare("UPDATE users SET pos_x = ?, pos_y = ?, last_activity = NOW() WHERE id = ?");
$stmt->bind_param("iii", $pos_x, $pos_y, $user_id);

if ($stmt->execute()) {
    echo json_encode([
        'success' => true,
        'pos_x' => $pos_x,
        'pos_y' => $pos_y
    ]);
} else {
    echo json_encode(['error' => 'Failed to update position']);
}

$stmt->close();
$conn->close();

