<?php
session_start();

if (!isset($_SESSION['logged_user'])) {
    header('Location: index.php');
    exit();
}

require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $country = $_POST['country'] ?? '';
    $major = $_POST['major'] ?? '';
    $instagram_handle = $_POST['instagram_handle'] ?? '';
    $bio = $_POST['bio'] ?? '';
    $avatar_color = $_POST['avatar_color'] ?? '#3498db';

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
        $avatar_color = '#3498db'; // Default color if invalid
    }

    // Update user profile
    $stmt = $conn->prepare("UPDATE users SET bio = ?, country = ?, major = ?, instagram_handle = ?, avatar_color = ? WHERE id = ?");
    $stmt->bind_param("sssssi", $bio, $country, $major, $instagram_handle, $avatar_color, $user_id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Profile updated successfully!";
        header('Location: edit_profile.php');
    } else {
        $_SESSION['error'] = "There was an error updating your profile: " . $conn->error;
        header('Location: edit_profile.php');
    }

    $stmt->close();
    $conn->close();
    exit();
} else {
    header('Location: edit_profile.php');
    exit();
}

