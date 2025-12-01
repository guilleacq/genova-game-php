-- Mini-MMO Genova Game - Database Schema
-- Run this file to set up the complete database structure

-- Use the login_system database
USE login_system;

-- Expand the existing users table with new fields for the game
ALTER TABLE users 
ADD COLUMN nickname VARCHAR(50) NOT NULL DEFAULT '',
ADD COLUMN bio TEXT,
ADD COLUMN country VARCHAR(100) NOT NULL DEFAULT '',
ADD COLUMN major VARCHAR(100) NOT NULL DEFAULT '',
ADD COLUMN instagram_handle VARCHAR(50),
ADD COLUMN avatar_color VARCHAR(7) NOT NULL DEFAULT '#3498db',
ADD COLUMN pos_x INT NOT NULL DEFAULT 400,
ADD COLUMN pos_y INT NOT NULL DEFAULT 300,
ADD COLUMN last_activity TIMESTAMP NULL DEFAULT NULL;

-- Create friend_requests table
CREATE TABLE IF NOT EXISTS friend_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    status ENUM('pending', 'accepted', 'rejected') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_request (sender_id, receiver_id)
);

-- Create friendships table (bidirectional friendships)
CREATE TABLE IF NOT EXISTS friendships (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    friend_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (friend_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_friendship (user_id, friend_id)
);

-- Create chat_messages table
CREATE TABLE IF NOT EXISTS chat_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Create indexes for better performance
CREATE INDEX idx_last_activity ON users(last_activity);
CREATE INDEX idx_friend_requests_status ON friend_requests(status);
CREATE INDEX idx_friend_requests_receiver ON friend_requests(receiver_id, status);
CREATE INDEX idx_chat_created ON chat_messages(created_at);

