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
    
    // Generate random avatar color from predefined palette
    $avatar_colors = ['#3498db', '#e74c3c', '#2ecc71', '#f39c12', '#9b59b6', '#1abc9c', '#34495e', '#e67e22', '#16a085', '#c0392b', '#8e44ad', '#27ae60', '#d35400', '#2980b9', '#7f8c8d'];
    $avatar_color = $avatar_colors[array_rand($avatar_colors)];
    
    // Random initial position in lobby (within bounds: 50-750 x, 50-550 y)
    $pos_x = rand(50, 750);
    $pos_y = rand(50, 550);

    $stmt = $conn->prepare("INSERT INTO users(username, password, bio, country, major, instagram_handle, avatar_color, pos_x, pos_y) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

    if ($stmt === false) {
        die("Error fatal en SQL: " . $conn->error); 
    }

    $stmt->bind_param("sssssssii", $username, $hashed_password, $bio, $country, $major, $instagram_handle, $avatar_color, $pos_x, $pos_y);

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
