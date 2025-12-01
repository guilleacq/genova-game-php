<?php
session_start();

if (!isset($_SESSION['logged_user'])) {
    header('Location: index.php');
    exit();
}

require 'db.php';

// Get current user info
$user_id = $_SESSION['user_id'];
$username = $_SESSION['logged_user'];
$stmt = $conn->prepare("SELECT nickname, avatar_color FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($nickname, $avatar_color);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lobby - Genova Game</title>
    <link rel="stylesheet" href="assets/game.css">
</head>
<body>
    <div class="game-container">
        <!-- Top Navigation Bar -->
        <div class="top-nav">
            <div class="nav-left">
                <h1>🇮🇹 Genova Exchange Lobby</h1>
                <span class="current-user">Playing as: <strong><?php echo htmlspecialchars($nickname, ENT_QUOTES, 'UTF-8'); ?></strong></span>
            </div>
            <div class="nav-right">
                <button id="myProfileBtn" class="nav-btn">My Profile</button>
                <button id="friendRequestsBtn" class="nav-btn">
                    Friend Requests <span id="requestCount" class="badge">0</span>
                </button>
                <button id="friendsListBtn" class="nav-btn">Friends</button>
                <a href="logout.php" class="nav-btn logout-btn">Logout</a>
            </div>
        </div>

        <!-- Main Game Area -->
        <div class="main-content">
            <!-- Lobby Area (Left Side) -->
            <div class="lobby-container">
                <div id="lobbyArea" class="lobby-area">
                    <!-- Background will be set via CSS -->
                    <!-- Players will be dynamically added here -->
                </div>
                <div class="lobby-info">
                    <span id="onlineCount">0</span> players online
                </div>
            </div>

            <!-- Chat Panel (Right Side) -->
            <div class="chat-panel">
                <div class="chat-header">
                    <h3>Global Chat</h3>
                </div>
                <div id="chatMessages" class="chat-messages">
                    <!-- Messages will be dynamically added here -->
                </div>
                <div class="chat-input-container">
                    <input type="text" id="chatInput" placeholder="Type a message..." maxlength="200">
                    <button id="sendMessageBtn">Send</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Profile Modal -->
    <div id="profileModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div id="profileContent">
                <!-- Profile content will be loaded here -->
            </div>
        </div>
    </div>

    <!-- Friend Requests Modal -->
    <div id="friendRequestsModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Friend Requests</h2>
            <div id="friendRequestsList">
                <!-- Friend requests will be loaded here -->
            </div>
        </div>
    </div>

    <!-- Friends List Modal -->
    <div id="friendsListModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>My Friends</h2>
            <div id="friendsList">
                <!-- Friends list will be loaded here -->
            </div>
        </div>
    </div>

    <!-- Pass PHP variables to JavaScript -->
    <script>
        const currentUserId = <?php echo $user_id; ?>;
        const currentUsername = "<?php echo htmlspecialchars($username, ENT_QUOTES, 'UTF-8'); ?>";
        const currentNickname = "<?php echo htmlspecialchars($nickname, ENT_QUOTES, 'UTF-8'); ?>";
        const currentAvatarColor = "<?php echo htmlspecialchars($avatar_color, ENT_QUOTES, 'UTF-8'); ?>";
    </script>
    <script src="assets/game.js"></script>
</body>
</html>

