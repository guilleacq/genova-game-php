<?php
    session_start();

    if (!isset($_SESSION['logged_user'])) {
        header('Location: index.php');
        exit();
    }

    // Redirect to game lobby (this file is now deprecated)
    header('Location: game.php');
    exit();
?>