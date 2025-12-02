// Genova Mini-MMO Game JavaScript

// State management
let players = {};
let lastChatMessageId = 0;
let isFirstChatLoad = true; // Skip showing bubbles on initial page load
let isMoving = false;
let messageBubbleTimeouts = {}; // Track active bubble timeouts per player

// DOM Elements
const lobbyContainer = document.querySelector('.lobby-container');
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
    lobbyContainer.addEventListener('click', handleLobbyClick);
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

    const rect = lobbyContainer.getBoundingClientRect();
    const x = e.clientX - rect.left;
    const y = e.clientY - rect.top;

    movePlayer(x, y);
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
                if (onlineCount) {
                    onlineCount.textContent = data.count;
                }
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
            lobbyContainer.appendChild(playerElement);
        } else {
            // Update existing player position (if not current user or not moving)
            if (player.id !== currentUserId || !isMoving) {
                playerElement.style.left = (player.pos_x - 25) + 'px';
                playerElement.style.top = (player.pos_y - 25) + 'px';
            }
            
            // Check if avatar color or profile picture changed
            const oldPlayer = players[player.id];
            if (oldPlayer && (oldPlayer.avatar_color !== player.avatar_color || oldPlayer.profile_picture_url !== player.profile_picture_url)) {
                // Update background color
                playerElement.style.backgroundColor = player.avatar_color;
                
                // Update profile picture
                const existingImg = playerElement.querySelector('.player-avatar-img');
                if (player.profile_picture_url) {
                    if (existingImg) {
                        existingImg.src = player.profile_picture_url;
                    } else {
                        const avatarImg = document.createElement('img');
                        avatarImg.className = 'player-avatar-img';
                        avatarImg.src = player.profile_picture_url;
                        avatarImg.alt = player.username;
                        avatarImg.onerror = function() {
                            this.remove();
                        };
                        // Insert before the label
                        playerElement.insertBefore(avatarImg, playerElement.firstChild);
                    }
                } else if (existingImg) {
                    // Remove image if URL was cleared
                    existingImg.remove();
                }
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

    // Add profile picture if available
    const profilePictureUrl = player.profile_picture_url || null;
    
    if (profilePictureUrl) {
        const avatarImg = document.createElement('img');
        avatarImg.className = 'player-avatar-img';
        avatarImg.src = profilePictureUrl;
        avatarImg.alt = player.username;
        avatarImg.onerror = function() {
            // If image fails to load, remove it and show color fallback
            this.remove();
        };
        playerDiv.appendChild(avatarImg);
    }

    const label = document.createElement('div');
    label.className = 'player-label';
    label.textContent = player.username;
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
            // Show bubble above current user
            showMessageBubble(currentUserId, message);
            
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
                // Check for new messages and show bubbles
                checkForNewMessages(data.messages);
                displayMessages(data.messages);
            }
        })
        .catch(error => console.error('Error fetching messages:', error));
}

function checkForNewMessages(messages) {
    if (messages.length === 0) return;
    
    // On first load, just set the last message ID without showing bubbles
    if (isFirstChatLoad) {
        lastChatMessageId = Math.max(...messages.map(m => m.id));
        isFirstChatLoad = false;
        return;
    }
    
    // Find new messages since last update
    const newMessages = messages.filter(msg => msg.id > lastChatMessageId);
    
    newMessages.forEach(msg => {
        // Show bubble for player if they're in the lobby and it's not the current user
        // (current user already shows bubble immediately when sending)
        if (msg.user_id !== currentUserId && players[msg.user_id]) {
            showMessageBubble(msg.user_id, msg.message);
        }
    });
    
    // Update last message ID
    if (messages.length > 0) {
        lastChatMessageId = Math.max(...messages.map(m => m.id));
    }
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

        // Add profile picture if available
        const profilePictureUrl = msg.profile_picture_url || null;
        
        if (profilePictureUrl) {
            const avatarImg = document.createElement('img');
            avatarImg.className = 'chat-avatar-img';
            avatarImg.src = profilePictureUrl;
            avatarImg.alt = msg.username;
            avatarImg.onerror = function() {
                this.remove();
            };
            avatar.appendChild(avatarImg);
        }

        const usernameSpan = document.createElement('span');
        usernameSpan.className = 'chat-username';
        usernameSpan.textContent = msg.username;

        const timestamp = document.createElement('span');
        timestamp.className = 'chat-timestamp';
        timestamp.textContent = formatTime(msg.timestamp);

        headerDiv.appendChild(avatar);
        headerDiv.appendChild(usernameSpan);
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

function showMessageBubble(userId, message) {
    const playerElement = document.querySelector(`.player[data-user-id="${userId}"]`);
    if (!playerElement) return;
    
    // Remove existing bubble if any
    const existingBubble = playerElement.querySelector('.message-bubble');
    if (existingBubble) {
        existingBubble.remove();
    }
    
    // Clear existing timeout for this player
    if (messageBubbleTimeouts[userId]) {
        clearTimeout(messageBubbleTimeouts[userId]);
    }
    
    // Create new bubble
    const bubble = document.createElement('div');
    bubble.className = 'message-bubble';
    // Truncate to first 20 characters
    const truncatedMessage = message.length > 20 ? message.substring(0, 20) + '...' : message;
    bubble.textContent = truncatedMessage;
    
    playerElement.appendChild(bubble);
    
    // Auto-hide after 4 seconds
    messageBubbleTimeouts[userId] = setTimeout(() => {
        bubble.classList.add('fade-out');
        setTimeout(() => {
            if (bubble.parentElement) {
                bubble.remove();
            }
        }, 500); // Wait for fade-out animation
    }, 4000);
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

    const profilePictureHTML = user.profile_picture_url 
        ? `<img src="${escapeHtml(user.profile_picture_url)}" alt="${escapeHtml(user.username)}" class="profile-avatar-img" onerror="this.style.display='none'">`
        : '';

    profileContent.innerHTML = `
        <div class="profile-header">
            <div class="profile-avatar" style="background-color: ${user.avatar_color};">${profilePictureHTML}</div>
            <div class="profile-name">
                <h2>${escapeHtml(user.username)}</h2>
            </div>
        </div>
        <div class="profile-info">
            ${user.country ? `
                <div class="profile-field">
                    <span class="profile-label">Country:</span>
                    <span class="profile-value">${escapeHtml(user.country)}</span>
                </div>
            ` : ''}
            ${user.major ? `
                <div class="profile-field">
                    <span class="profile-label">Studying:</span>
                    <span class="profile-value">${escapeHtml(user.major)}</span>
                </div>
            ` : ''}
            ${user.instagram_handle ? `
                <div class="profile-field">
                <span class="profile-label">Instagram:</span>
                    <a class="instagram-link" href="https://www.instagram.com/${escapeHtml(user.instagram_handle)}" target="_blank">
                        <span class="profile-value">@${escapeHtml(user.instagram_handle)}</span>
                    </a>
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

    listContainer.innerHTML = requests.map(req => {
        const reqPictureHTML = req.profile_picture_url 
            ? `<img src="${escapeHtml(req.profile_picture_url)}" alt="${escapeHtml(req.username)}" class="avatar-img" onerror="this.style.display='none'">`
            : '';
        return `
        <div class="request-item">
            <div class="request-avatar" style="background-color: ${req.avatar_color};">${reqPictureHTML}</div>
            <div class="request-info">
                <div class="request-name">${escapeHtml(req.username)}</div>
            </div>
            <div class="request-actions">
                <button class="btn btn-success btn-sm" onclick="respondToRequest(${req.id}, 'accept')">Accept</button>
                <button class="btn btn-danger btn-sm" onclick="respondToRequest(${req.id}, 'reject')">Decline</button>
            </div>
        </div>
    `}).join('');
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

    listContainer.innerHTML = friends.map(friend => {
        const friendPictureHTML = friend.profile_picture_url 
            ? `<img src="${escapeHtml(friend.profile_picture_url)}" alt="${escapeHtml(friend.username)}" class="avatar-img" onerror="this.style.display='none'">`
            : '';
        return `
        <div class="friend-item" onclick="showUserProfile(${friend.id}); friendsListModal.style.display='none';" style="cursor: pointer;">
            <div class="friend-avatar" style="background-color: ${friend.avatar_color};">${friendPictureHTML}</div>
            <div class="friend-info">
                <div class="friend-name">
                    ${escapeHtml(friend.username)}
                    <span class="online-status ${friend.is_online ? 'online' : 'offline'}"></span>
                </div>
                <div class="friend-details">${friend.country ? escapeHtml(friend.country) : ''}</div>
            </div>
        </div>
    `}).join('');
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

