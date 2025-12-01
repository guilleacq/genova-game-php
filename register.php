<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign up</title>
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
    <h1>Sign up</h1>

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

    <form action="register_process.php" method="POST">
        <label for="username">* Username:</label>
        <input type="text" name="username" id="username" placeholder="ex: carlitos" required>

        <br> <br>

        <label for="password">* Password:</label>
        <input type="password" name="password" id="password" placeholder="******" required>
        
        <br> <br>

        <label for="nickname">Nickname (Display Name):</label>
        <input type="text" name="nickname" id="nickname" placeholder="How others will see you" required>

        <br> <br>

        <label for="country">Country of Origin:</label>
        <select name="country" id="country" required>
            <option value="">Select your country</option>
            <option value="Argentina">Argentina</option>
            <option value="Brazil">Brazil</option>
            <option value="Chile">Chile</option>
            <option value="Colombia">Colombia</option>
            <option value="France">France</option>
            <option value="Germany">Germany</option>
            <option value="Italy">Italy</option>
            <option value="Mexico">Mexico</option>
            <option value="Spain">Spain</option>
            <option value="United Kingdom">United Kingdom</option>
            <option value="United States">United States</option>
            <option value="Uruguay">Uruguay</option>
            <option value="Other">Other</option>
        </select>

        <br> <br>

        <label for="major">What are you studying?</label>
        <input type="text" name="major" id="major" placeholder="ex: Computer Science, Business, etc." required>

        <br> <br>

        <label for="instagram_handle">Instagram Handle (optional):</label>
        <input type="text" name="instagram_handle" id="instagram_handle" placeholder="ex: @yourhandle">

        <br> <br>

        <label for="bio">Bio (optional):</label>
        <textarea name="bio" id="bio" rows="3" placeholder="Tell us a bit about yourself..."></textarea>
        
        <br> <br>
        <input type="submit" value="Sign up">
    </form>

    <br> <br>
    <p>Already have an account? <a href="index.php">Login</a></p>
</body>
</html>