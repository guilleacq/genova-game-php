<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Genova Game</title>
    <style>
        .error {
            color: #d9534f;
            background-color: #f2dede;
            border: 1px solid #ebccd1;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        .success {
            color: #3c763d;
            background-color: #dff0d8;
            border: 1px solid #d6e9c6;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <h1>Login</h1>

    <?php
    if (isset($_SESSION['error'])) {
        echo '<div class="error">' . htmlspecialchars($_SESSION['error'], ENT_QUOTES, 'UTF-8') . '</div>';
        unset($_SESSION['error']);
    }
    if (isset($_SESSION['success'])) {
        echo '<div class="success">' . htmlspecialchars($_SESSION['success'], ENT_QUOTES, 'UTF-8') . '</div>';
        unset($_SESSION['success']);
    }
    ?>

    <form action="auth.php" method="POST">
        <label for="username">Username:</label>
        <input type="text" name="username" id="username" placeholder="ex: pepe">

        <br> <br>

        <label for="password">Password:</label>
        <input type="password" name="password" id="password" placeholder="ex: 1234">
        
        <br> <br>
        <input type="submit" value="Login">
    </form>

    <br> <br>
    <p>Don't have an account? <a href="register.php">Sign up</a></p>
</body>
</html>