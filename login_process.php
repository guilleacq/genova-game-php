<?php 
session_start();
require 'db.php';

if (isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password']; 

    // Clear username
    $username = trim($username);
    $username = stripslashes($username);

    // Get user and their password
    $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();

    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $db_password_hash);
        $stmt->fetch();

        // Successful login
        if (password_verify($password, $db_password_hash)) {
            $_SESSION['logged_user'] = $username;
            $_SESSION['user_id'] = $id;

            session_regenerate_id(true);

            header('Location: game.php');
            exit();
        } else {
            $_SESSION['error'] = "Invalid username or password";
            header('Location: login_form.php');
            exit();
        }
    } else {
        $_SESSION['error'] = "Invalid username or password";
        header('Location: login_form.php');
        exit();
    }

    $stmt->close();
} else {
    header('Location: login_form.php');
    exit();
}

