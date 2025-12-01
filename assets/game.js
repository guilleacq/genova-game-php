// Genova Mini-MMO Game JavaScript

// State management
let players = {};
let lastChatMessageId = 0;
let isMoving = false;

// DOM Elements
const lobbyArea = document.getElementById('lobbyArea');
const chatMessages = document.getElementById('chatMessages');
const chatInput = document.getElementById('chatInput');
const sendMessageBtn = document.getElementById('sendMessageBtn');
const onlineCount = document.getElementById('onlineCount');

// Modals
const profileModal = document.getElementById('profileModal');
const friendRequestsModal = document.getElementById('friendRequestsModal');
const friendsListModal = document.getElementById('friendsListModal');

// Buttons
const myProfileBtn = document.getElementById('myProfileBtn');
const friendRequestsBtn = document.getElementById('friendRequestsBtn');
const friendsListBtn = document.getElementById('friendsListBtn');
const requestCount = document.getElementById('requestCount');

// Initialize game
document.addEventListener('DOMContentLoaded', function() {
    initializeGame();
});

function initializeGame() {
    // Set up event listeners
    lobbyArea.addEventListener('click', handleLobbyClick);
    sendMessageBtn.addEventListener('click', sendMessage);
    chatInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            sendMessage();
        }
    });

    // Modal buttons
    myProfileBtn.addEventListener('click', () => showUserProfile(currentUserId));
    friendRequestsBtn.addEventListener('click', showFriendRequests);
    friendsListBtn.addEventListener('click', showFriendsList);

    // Close modals when clicking X or outside
    setupModalClosing();

    // Start polling
    updateLobbyState();
    updateChatMessages();
    updateFriendRequestCount();
    
    setInterval(updateLobbyState, 2000); // Every 2 seconds
    setInterval(updateChatMessages, 3000); // Every 3 seconds
    setInterval(updateFriendRequestCount, 5000); // Every 5 seconds
}

// ===== LOBBY & PLAYER MOVEMENT =====

function handleLobbyClick(e) {
    if (e.target.classList.contains('player') || e.target.classList.contains('player-label')) {
        return; // Don't move when clicking on a player
    }

    const rect = lobbyArea.getBoundingClientRect();
    const x = e.clientX - rect.left;
    const y = e.clientY - rect.top;

    // Clamp to bounds
    const clampedX = Math.max(25, Math.min(775, x));
    const clampedY = Math.max(25, Math.min(575, y));

    movePlayer(clampedX, clampedY);
}

function movePlayer(x, y) {
    if (isMoving) return;
    isMoving = true;

    // Send position to server
    fetch('api/update_position.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `x=${Math.round(x)}&y=${Math.round(y)}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Animate current player locally for immediate feedback
            const currentPlayerElement = document.querySelector(`.player[data-user-id="${currentUserId}"]`);
            if (currentPlayerElement) {
                animatePlayerMovement(currentPlayerElement, x - 25, y - 25);
            }
        }
        isMoving = false;
    })
    .catch(error => {
        console.error('Error updating position:', error);
        isMoving = false;
    });
}

function animatePlayerMovement(element, targetX, targetY) {
    element.style.transition = 'left 0.5s ease-out, top 0.5s ease-out';
    element.style.left = targetX + 'px';
    element.style.top = targetY + 'px';
}

function updateLobbyState() {
    fetch('api/get_lobby_state.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updatePlayers(data.players);
                onlineCount.textContent = data.count;
            }
        })
        .catch(error => console.error('Error fetching lobby state:', error));
}

function updatePlayers(newPlayers) {
    const currentPlayerIds = new Set();

    newPlayers.forEach(player => {
        currentPlayerIds.add(player.id);
        
        let playerElement = document.querySelector(`.player[data-user-id="${player.id}"]`);
        
        if (!playerElement) {
            // Create new player element
            playerElement = createPlayerElement(player);
            lobbyArea.appendChild(playerElement);
        } else {
            // Update existing player position (if not current user or not moving)
            if (player.id !== currentUserId || !isMoving) {
                playerElement.style.left = (player.pos_x - 25) + 'px';
                playerElement.style.top = (player.pos_y - 25) + 'px';
            }
        }
        
        players[player.id] = player;
    });

    // Remove players that are no longer in the lobby
    Object.keys(players).forEach(playerId => {
        if (!currentPlayerIds.has(parseInt(playerId))) {
            const elementToRemove = document.querySelector(`.player[data-user-id="${playerId}"]`);
            if (elementToRemove) {
                elementToRemove.remove();
            }
            delete players[playerId];
        }
    });
}

function createPlayerElement(player) {
    const playerDiv = document.createElement('div');
    playerDiv.className = 'player';
    playerDiv.dataset.userId = player.id;
    playerDiv.style.backgroundColor = player.avatar_color;
    playerDiv.style.left = (player.pos_x - 25) + 'px';
    playerDiv.style.top = (player.pos_y - 25) + 'px';
    
    if (player.id === currentUserId) {
        playerDiv.classList.add('current-user');
    }

    const label = document.createElement('div');
    label.className = 'player-label';
    label.textContent = player.nickname;
    playerDiv.appendChild(label);

    // Click to view profile
    playerDiv.addEventListener('click', (e) => {
        e.stopPropagation();
        showUserProfile(player.id);
    });

    return playerDiv;
}

// ===== CHAT SYSTEM =====

function sendMessage() {
    const message = chatInput.value.trim();
    
    if (!message) return;

    fetch('api/send_message.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `message=${encodeURIComponent(message)}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            chatInput.value = '';
            // Immediately fetch new messages
            updateChatMessages();
        } else {
            alert(data.error || 'Failed to send message');
        }
    })
    .catch(error => console.error('Error sending message:', error));
}

function updateChatMessages() {
    fetch('api/get_messages.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayMessages(data.messages);
            }
        })
        .catch(error => console.error('Error fetching messages:', error));
}

function displayMessages(messages) {
    // Check if we need to update
    if (messages.length === 0 && chatMessages.children.length === 0) return;

    const shouldScroll = chatMessages.scrollHeight - chatMessages.scrollTop <= chatMessages.clientHeight + 50;

    chatMessages.innerHTML = '';

    messages.forEach(msg => {
        const messageDiv = document.createElement('div');
        messageDiv.className = 'chat-message';
        messageDiv.style.borderLeftColor = msg.avatar_color;

        const headerDiv = document.createElement('div');
        headerDiv.className = 'chat-message-header';

        const avatar = document.createElement('span');
        avatar.className = 'chat-avatar';
        avatar.style.backgroundColor = msg.avatar_color;

        const nickname = document.createElement('span');
        nickname.className = 'chat-nickname';
        nickname.textContent = msg.nickname;

        const timestamp = document.createElement('span');
        timestamp.className = 'chat-timestamp';
        timestamp.textContent = formatTime(msg.timestamp);

        headerDiv.appendChild(avatar);
        headerDiv.appendChild(nickname);
        headerDiv.appendChild(timestamp);

        const textDiv = document.createElement('div');
        textDiv.className = 'chat-text';
        textDiv.textContent = msg.message;

        messageDiv.appendChild(headerDiv);
        messageDiv.appendChild(textDiv);
        chatMessages.appendChild(messageDiv);
    });

    if (shouldScroll) {
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
}

function formatTime(timestamp) {
    const date = new Date(timestamp);
    const hours = date.getHours().toString().padStart(2, '0');
    const minutes = date.getMinutes().toString().padStart(2, '0');
    return `${hours}:${minutes}`;
}

// ===== PROFILE MODAL =====

function showUserProfile(userId) {
    fetch(`api/get_user_info.php?user_id=${userId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayProfile(data);
                profileModal.style.display = 'block';
            } else {
                alert(data.error || 'Failed to load profile');
            }
        })
        .catch(error => console.error('Error fetching user info:', error));
}

function displayProfile(data) {
    const user = data.user;
    const profileContent = document.getElementById('profileContent');

    let actionsHTML = '';
    
    if (data.is_current_user) {
        actionsHTML = `
            <div class="profile-actions">
                <a href="edit_profile.php" class="btn btn-primary">Edit My Profile</a>
            </div>
        `;
    } else {
        if (data.is_friend) {
            actionsHTML = `
                <div class="profile-actions">
                    <button class="btn btn-success" disabled>Already Friends ✓</button>
                </div>
            `;
        } else if (data.friend_request_status === 'sent') {
            actionsHTML = `
                <div class="profile-actions">
                    <button class="btn btn-primary" disabled>Friend Request Sent</button>
                </div>
            `;
        } else if (data.friend_request_status === 'received') {
            actionsHTML = `
                <div class="profile-actions">
                    <button class="btn btn-success" onclick="respondToRequest(${data.request_id}, 'accept')">Accept Friend Request</button>
                    <button class="btn btn-danger" onclick="respondToRequest(${data.request_id}, 'reject')">Decline</button>
                </div>
            `;
        } else {
            actionsHTML = `
                <div class="profile-actions">
                    <button class="btn btn-primary" onclick="sendFriendRequest(${user.id})">Add Friend</button>
                </div>
            `;
        }
    }

    profileContent.innerHTML = `
        <div class="profile-header">
            <div class="profile-avatar" style="background-color: ${user.avatar_color};"></div>
            <div class="profile-name">
                <h2>${escapeHtml(user.nickname)}</h2>
                <p class="profile-username">@${escapeHtml(user.username)}</p>
            </div>
        </div>
        <div class="profile-info">
            <div class="profile-field">
                <span class="profile-label">Country:</span>
                <span class="profile-value">${escapeHtml(user.country)}</span>
            </div>
            <div class="profile-field">
                <span class="profile-label">Studying:</span>
                <span class="profile-value">${escapeHtml(user.major)}</span>
            </div>
            ${user.instagram_handle ? `
                <div class="profile-field">
                    <span class="profile-label">Instagram:</span>
                    <span class="profile-value">@${escapeHtml(user.instagram_handle)}</span>
                </div>
            ` : ''}
            ${user.bio ? `
                <div class="profile-field">
                    <span class="profile-label">Bio:</span>
                    <span class="profile-value">${escapeHtml(user.bio)}</span>
                </div>
            ` : ''}
        </div>
        ${actionsHTML}
    `;
}

// ===== FRIEND SYSTEM =====

function sendFriendRequest(userId) {
    fetch('api/send_friend_request.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `user_id=${userId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Friend request sent!');
            profileModal.style.display = 'none';
        } else {
            alert(data.error || 'Failed to send friend request');
        }
    })
    .catch(error => console.error('Error sending friend request:', error));
}

function updateFriendRequestCount() {
    fetch('api/get_friend_requests.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                requestCount.textContent = data.count;
                if (data.count > 0) {
                    requestCount.style.display = 'inline-block';
                } else {
                    requestCount.style.display = 'none';
                }
            }
        })
        .catch(error => console.error('Error fetching friend requests:', error));
}

function showFriendRequests() {
    fetch('api/get_friend_requests.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayFriendRequests(data.requests);
                friendRequestsModal.style.display = 'block';
            }
        })
        .catch(error => console.error('Error fetching friend requests:', error));
}

function displayFriendRequests(requests) {
    const listContainer = document.getElementById('friendRequestsList');
    
    if (requests.length === 0) {
        listContainer.innerHTML = `
            <div class="empty-state">
                <p>No pending friend requests</p>
            </div>
        `;
        return;
    }

    listContainer.innerHTML = requests.map(req => `
        <div class="request-item">
            <div class="request-avatar" style="background-color: ${req.avatar_color};"></div>
            <div class="request-info">
                <div class="request-name">${escapeHtml(req.nickname)}</div>
                <div class="request-username">@${escapeHtml(req.username)}</div>
            </div>
            <div class="request-actions">
                <button class="btn btn-success btn-sm" onclick="respondToRequest(${req.id}, 'accept')">Accept</button>
                <button class="btn btn-danger btn-sm" onclick="respondToRequest(${req.id}, 'reject')">Decline</button>
            </div>
        </div>
    `).join('');
}

function respondToRequest(requestId, action) {
    fetch('api/respond_friend_request.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `request_id=${requestId}&action=${action}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Refresh the requests list
            showFriendRequests();
            updateFriendRequestCount();
            
            // Close profile modal if open
            profileModal.style.display = 'none';
            
            if (action === 'accept') {
                alert('Friend request accepted!');
            }
        } else {
            alert(data.error || 'Failed to process request');
        }
    })
    .catch(error => console.error('Error responding to friend request:', error));
}

function showFriendsList() {
    fetch('api/get_friends.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayFriendsList(data.friends);
                friendsListModal.style.display = 'block';
            }
        })
        .catch(error => console.error('Error fetching friends:', error));
}

function displayFriendsList(friends) {
    const listContainer = document.getElementById('friendsList');
    
    if (friends.length === 0) {
        listContainer.innerHTML = `
            <div class="empty-state">
                <p>No friends yet</p>
                <p>Start adding friends by clicking on players in the lobby!</p>
            </div>
        `;
        return;
    }

    listContainer.innerHTML = friends.map(friend => `
        <div class="friend-item" onclick="showUserProfile(${friend.id}); friendsListModal.style.display='none';" style="cursor: pointer;">
            <div class="friend-avatar" style="background-color: ${friend.avatar_color};"></div>
            <div class="friend-info">
                <div class="friend-name">
                    ${escapeHtml(friend.nickname)}
                    <span class="online-status ${friend.is_online ? 'online' : 'offline'}"></span>
                </div>
                <div class="friend-username">@${escapeHtml(friend.username)} • ${escapeHtml(friend.country)}</div>
            </div>
        </div>
    `).join('');
}

// ===== MODAL MANAGEMENT =====

function setupModalClosing() {
    // Close buttons
    const closeButtons = document.querySelectorAll('.modal .close');
    closeButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            this.closest('.modal').style.display = 'none';
        });
    });

    // Click outside modal
    window.addEventListener('click', function(e) {
        if (e.target.classList.contains('modal')) {
            e.target.style.display = 'none';
        }
    });

    // ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal').forEach(modal => {
                modal.style.display = 'none';
            });
        }
    });
}

// ===== UTILITY FUNCTIONS =====

function escapeHtml(text) {
    if (!text) return '';
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, m => map[m]);
}

