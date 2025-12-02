<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['logged_user'])) {
    echo json_encode(['error' => 'Not authenticated']);
    exit();
}

require '../db.php';

// Get top 5 users with most friends
// Since friendships are stored bidirectionally, we count both directions
$stmt = $conn->prepare("
    SELECT
        u.id,
        u.username,
        u.avatar_color,
        u.profile_picture_url,
        COUNT(*) as friend_count
    FROM users u
    JOIN friendships f ON (u.id = f.user_id OR u.id = f.friend_id)
    GROUP BY u.id, u.username, u.avatar_color, u.profile_picture_url
    ORDER BY friend_count DESC, u.username ASC
    LIMIT 5
");

$stmt->execute();
$result = $stmt->get_result();

$leaderboard = [];
$rank = 1;
while ($row = $result->fetch_assoc()) {
    $leaderboard[] = [
        'rank' => $rank,
        'id' => (int)$row['id'],
        'username' => $row['username'],
        'avatar_color' => $row['avatar_color'],
        'profile_picture_url' => $row['profile_picture_url'],
        'friend_count' => (int)($row['friend_count'] / 2) // Divide by 2 since friendships are stored bidirectionally
    ];
    $rank++;
}

$stmt->close();
$conn->close();

echo json_encode([
    'success' => true,
    'leaderboard' => $leaderboard
]);


