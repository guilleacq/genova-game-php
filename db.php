<?php

// This could come from .env file
$servername = "localhost";
$username_db = "root";
$password_db = "";
$dbname = "genova_game";

$conn = new mysqli($servername, $username_db, $password_db, $dbname);

if ($conn -> connect_error) {
    // error_log("Connection failed: " . $conn->connect_error) // We could use this 
    die("There was a problem with the database. Please try again later.");
}
?>