# Genova Exchange Student Mini-MMO

A 2D social lobby game for exchange students in Genova, Italy. Users can walk around a virtual Piazza de Ferrari, chat with others, and make friends!

## Features

- **User Registration & Authentication**: Secure login system with profile information
- **Interactive 2D Lobby**: Click to move around the lobby (Club Penguin style)
- **Global Chat**: Real-time chat system to communicate with all players
- **Friend System**: Send and accept friend requests, view your friends list
- **Player Profiles**: View detailed profiles including country, major, Instagram, and bio
- **Online Status**: See who's currently in the lobby

## Setup Instructions

### 1. Database Setup

First, make sure XAMPP is running with Apache and MySQL.

1. Open phpMyAdmin at `http://localhost/phpmyadmin`
2. Create a new database called `login_system`
3. Select the database and go to the "Import" tab
4. Import the `database_schema.sql` file
   - OR manually run the SQL queries from `database_schema.sql` in the SQL tab

### 2. Configure Database Connection

Edit `db.php` if needed to match your MySQL credentials:

```php
$servername = "localhost";
$username_db = "root";
$password_db = "";
$dbname = "login_system";
```

### 3. Optional: Add Background Image

To use a real image of Piazza de Ferrari:

1. Find an image of Piazza de Ferrari (800x600px recommended)
2. Save it as `assets/piazza-ferrari-bg.jpg`
3. The game will automatically use it as the lobby background

If no image is provided, a gradient background will be used.

### 4. Access the Application

1. Start XAMPP (Apache and MySQL must be running)
2. Open your browser and go to: `http://localhost/genova-game-php/`
3. Create an account by clicking "Sign up"
4. Fill in your profile information
5. Start playing!

## How to Play

### Movement
- Click anywhere in the lobby area to move your character there
- Your character is highlighted with a golden border

### Chat
- Type your message in the chat box on the right
- Press Enter or click "Send" to send messages
- Maximum 200 characters per message

### Making Friends
1. Click on any player in the lobby to view their profile
2. Click "Add Friend" to send a friend request
3. Check "Friend Requests" button to accept/decline requests
4. View your friends in the "Friends List"

### Editing Profile
- Click "My Profile" to view and edit your information
- You can change your nickname, bio, country, major, Instagram handle, and avatar color

## Technical Details

### Technology Stack
- **Backend**: PHP 7.4+ with MySQLi
- **Frontend**: Vanilla JavaScript (no frameworks)
- **Styling**: Custom CSS with modern design
- **Architecture**: REST API with AJAX polling

### File Structure

```
/genova-game-php/
├── index.php                    # Login page
├── register.php                 # Registration form
├── register_process.php         # Registration handler
├── auth.php                     # Authentication handler
├── game.php                     # Main game lobby
├── edit_profile.php            # Profile editing page
├── update_profile_process.php  # Profile update handler
├── logout.php                  # Logout handler
├── db.php                      # Database connection
├── profile.php                 # Redirects to game.php
├── database_schema.sql         # Database setup script
├── api/
│   ├── get_lobby_state.php    # Returns active players
│   ├── update_position.php    # Updates player position
│   ├── get_user_info.php      # Returns user profile
│   ├── send_message.php       # Sends chat message
│   ├── get_messages.php       # Returns chat messages
│   ├── send_friend_request.php
│   ├── respond_friend_request.php
│   ├── get_friend_requests.php
│   └── get_friends.php
└── assets/
    ├── game.css               # Main stylesheet
    ├── game.js                # Game logic
    └── piazza-ferrari-bg.jpg  # (optional) Background image
```

### Polling System

The game uses AJAX polling instead of WebSockets for simplicity:
- Player positions: Updated every 2 seconds
- Chat messages: Updated every 3 seconds
- Friend requests: Updated every 5 seconds

This works well for up to 50-100 simultaneous users.

### Security Features

- Password hashing with PHP's `password_hash()`
- Prepared statements for all database queries
- Input sanitization with `htmlspecialchars()`
- Session management with `session_regenerate_id()`
- CSRF protection through session validation

## Customization

### Change Available Countries

Edit `register.php` and `edit_profile.php` to add more countries to the dropdown.

### Change Avatar Colors

Edit the `$avatar_colors` array in `register_process.php` to use different colors.

### Adjust Polling Intervals

In `assets/game.js`, modify the `setInterval` calls to change update frequencies.

### Modify Lobby Size

The lobby is currently 800x600px. To change:
1. Update CSS in `assets/game.css` (`.lobby-area` dimensions)
2. Update validation in `api/update_position.php` (bounds checking)

## Troubleshooting

### Players not appearing
- Check that the database schema was imported correctly
- Verify that MySQL is running in XAMPP
- Check browser console for JavaScript errors

### Chat not working
- Ensure `chat_messages` table exists in the database
- Check that the API endpoints are accessible

### Can't move character
- Make sure JavaScript is enabled in your browser
- Check browser console for errors
- Verify that `api/update_position.php` is accessible

### Database connection errors
- Verify MySQL is running in XAMPP
- Check credentials in `db.php`
- Ensure the `login_system` database exists

## Future Enhancements

Possible features to add:
- Private messaging between friends
- User avatars/sprites instead of circles
- Mini-games within the lobby
- Rooms or different areas to explore
- Notifications for friend requests
- User status messages
- Groups or clubs

## Credits

Created for exchange students in Genova, Italy.

Enjoy making new friends! 🇮🇹

