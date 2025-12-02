<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require 'db.php';

if (isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $country = $_POST['country'] ?? '';
    $major = $_POST['major'] ?? '';
    $instagram_handle = $_POST['instagram_handle'] ?? '';
    $bio = $_POST['bio'] ?? '';
    $profile_picture_url = $_POST['profile_picture_url'] ?? '';

    // make sure required fields are not empty (Might be unnecessary)
    if (empty($username) || empty($password)) {
        $_SESSION['error'] = "Username and password are required";
        header("Location: register.php");
        exit();
    }

    // Clear and sanitize all inputs
    $username = trim($username);
    $username = stripslashes($username);
    
    $country = trim($country);
    $country = stripslashes($country);
    
    $major = trim($major);
    $major = stripslashes($major);
    
    $instagram_handle = trim($instagram_handle);
    $instagram_handle = stripslashes($instagram_handle);
    
    $bio = trim($bio);
    $bio = stripslashes($bio);
    
    $profile_picture_url = trim($profile_picture_url);
    $profile_picture_url = stripslashes($profile_picture_url);

    // Verify if user already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username); // string
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $_SESSION['error'] = "User already exists. Choose another one";
        header('Location: register.php');
        exit();
    }
    $stmt->close();

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Generate random avatar color from Mediterranean palette
    $avatar_colors = [
        '#C4694A', // Terracotta
        '#D4A54A', // Gold
        '#9CAF88', // Olive soft
        '#8B5E3C', // Sienna
        '#A85438', // Terracotta dark
        '#7A8F68', // Olive dark
        '#D4856B', // Terracotta light
        '#B88888', // Rose dark
        '#A69478', // Cappuccino
        '#6B4A2F', // Sienna dark
        '#5C4A3D', // Coffee
        '#C9B896'  // Ochre
    ];
    $avatar_color = $avatar_colors[array_rand($avatar_colors)];
    
    // If no profile picture provided, assign a random default avatar
    if (empty($profile_picture_url)) {
        $default_avatars = [
            'https://i.imgur.com/IbOXVHy.jpeg',
            'https://i.imgur.com/LEneITN.jpeg',
            'https://i.imgur.com/n0aTIKs.jpeg'
        ];
        $profile_picture_url = $default_avatars[array_rand($default_avatars)];
    }
    
    // Random initial position in lobby (within bounds: 50-750 x, 50-550 y)
    $pos_x = rand(50, 750);
    $pos_y = rand(50, 550);

    $stmt = $conn->prepare("INSERT INTO users(username, password, bio, country, major, instagram_handle, avatar_color, pos_x, pos_y, profile_picture_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    if ($stmt === false) {
        die("Error fatal en SQL: " . $conn->error); 
    }

    $stmt->bind_param("sssssssiis", $username, $hashed_password, $bio, $country, $major, $instagram_handle, $avatar_color, $pos_x, $pos_y, $profile_picture_url);

    if ($stmt->execute()) {
        $_SESSION['success'] = "User registered successfully";
        header('Location: index.php');
    } else {
        $_SESSION['error'] = "There's been an error with the database: " . $conn->error;
        header('Location: register.php'); 
    }


    $stmt-> close();
    $conn-> close();
    exit();
} else {
    header('Location: register.php');
    exit();
}
