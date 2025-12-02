<?php
session_start();

if (!isset($_SESSION['logged_user'])) {
    header('Location: login_form.php');
    exit();
}

require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $country = $_POST['country'] ?? '';
    $major = $_POST['major'] ?? '';
    $instagram_handle = $_POST['instagram_handle'] ?? '';
    $bio = $_POST['bio'] ?? '';
    $avatar_color = $_POST['avatar_color'] ?? '#C4694A';
    $profile_picture_url = $_POST['profile_picture_url'] ?? '';

    // Sanitize inputs
    $country = trim($country);
    $country = stripslashes($country);
    
    $major = trim($major);
    $major = stripslashes($major);
    
    $instagram_handle = trim($instagram_handle);
    $instagram_handle = stripslashes($instagram_handle);
    
    $bio = trim($bio);
    $bio = stripslashes($bio);
    
    // Validate color (must be a valid hex color)
    if (!preg_match('/^#[a-f0-9]{6}$/i', $avatar_color)) {
        $avatar_color = '#C4694A'; // Default terracotta color if invalid
    }
    
    // Sanitize profile picture URL
    $profile_picture_url = trim($profile_picture_url);
    $profile_picture_url = stripslashes($profile_picture_url);
    // Set to null if empty so database stores NULL instead of empty string
    if (empty($profile_picture_url)) {
        $profile_picture_url = null;
    }

    // Update user profile
    $stmt = $conn->prepare("UPDATE users SET bio = ?, country = ?, major = ?, instagram_handle = ?, avatar_color = ?, profile_picture_url = ? WHERE id = ?");
    $stmt->bind_param("ssssssi", $bio, $country, $major, $instagram_handle, $avatar_color, $profile_picture_url, $user_id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Profile updated successfully!";
        header('Location: edit_profile_form.php');
    } else {
        $_SESSION['error'] = "There was an error updating your profile: " . $conn->error;
        header('Location: edit_profile_form.php');
    }

    $stmt->close();
    $conn->close();
    exit();
} else {
    header('Location: edit_profile_form.php');
    exit();
}

