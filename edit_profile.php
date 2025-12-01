<?php
session_start();

if (!isset($_SESSION['logged_user'])) {
    header('Location: index.php');
    exit();
}

require 'db.php';

// Get current user data
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT bio, country, major, instagram_handle, avatar_color FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($bio, $country, $major, $instagram_handle, $avatar_color);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - Genova Game</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-top: 15px;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }
        input[type="text"], textarea, select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 14px;
        }
        textarea {
            resize: vertical;
            font-family: Arial, sans-serif;
        }
        .color-picker-container {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 10px;
        }
        .color-option {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
            border: 3px solid transparent;
            transition: transform 0.2s;
        }
        .color-option:hover {
            transform: scale(1.1);
        }
        .color-option.selected {
            border-color: #000;
            transform: scale(1.15);
        }
        input[type="submit"] {
            background-color: #3498db;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 20px;
        }
        input[type="submit"]:hover {
            background-color: #2980b9;
        }
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
        .back-link {
            display: inline-block;
            margin-top: 15px;
            color: #3498db;
            text-decoration: none;
        }
        .back-link:hover {
            text-decoration: underline;
        }
        .optional-note {
            color: #888;
            font-size: 0.9em;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Edit Your Profile</h1>
        <p class="optional-note">All fields are optional. Fill in what you'd like others to see.</p>

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

        <form action="update_profile_process.php" method="POST">
            <label for="country">Country of Origin:</label>
            <select name="country" id="country">
                <option value="">Select your country</option>
                <option value="Argentina" <?php echo $country === 'Argentina' ? 'selected' : ''; ?>>Argentina</option>
                <option value="Brazil" <?php echo $country === 'Brazil' ? 'selected' : ''; ?>>Brazil</option>
                <option value="Chile" <?php echo $country === 'Chile' ? 'selected' : ''; ?>>Chile</option>
                <option value="Colombia" <?php echo $country === 'Colombia' ? 'selected' : ''; ?>>Colombia</option>
                <option value="France" <?php echo $country === 'France' ? 'selected' : ''; ?>>France</option>
                <option value="Germany" <?php echo $country === 'Germany' ? 'selected' : ''; ?>>Germany</option>
                <option value="Italy" <?php echo $country === 'Italy' ? 'selected' : ''; ?>>Italy</option>
                <option value="Mexico" <?php echo $country === 'Mexico' ? 'selected' : ''; ?>>Mexico</option>
                <option value="Spain" <?php echo $country === 'Spain' ? 'selected' : ''; ?>>Spain</option>
                <option value="United Kingdom" <?php echo $country === 'United Kingdom' ? 'selected' : ''; ?>>United Kingdom</option>
                <option value="United States" <?php echo $country === 'United States' ? 'selected' : ''; ?>>United States</option>
                <option value="Uruguay" <?php echo $country === 'Uruguay' ? 'selected' : ''; ?>>Uruguay</option>
                <option value="Other" <?php echo $country === 'Other' ? 'selected' : ''; ?>>Other</option>
            </select>

            <label for="major">What are you studying?</label>
            <input type="text" name="major" id="major" value="<?php echo htmlspecialchars($major, ENT_QUOTES, 'UTF-8'); ?>">

            <label for="instagram_handle">Instagram Handle:</label>
            <input type="text" name="instagram_handle" id="instagram_handle" value="<?php echo htmlspecialchars($instagram_handle, ENT_QUOTES, 'UTF-8'); ?>">

            <label for="bio">Bio:</label>
            <textarea name="bio" id="bio" rows="4"><?php echo htmlspecialchars($bio, ENT_QUOTES, 'UTF-8'); ?></textarea>

            <label>Avatar Color:</label>
            <input type="hidden" name="avatar_color" id="avatar_color" value="<?php echo htmlspecialchars($avatar_color, ENT_QUOTES, 'UTF-8'); ?>">
            <div class="color-picker-container">
                <div class="color-option" style="background-color: #3498db;" data-color="#3498db"></div>
                <div class="color-option" style="background-color: #e74c3c;" data-color="#e74c3c"></div>
                <div class="color-option" style="background-color: #2ecc71;" data-color="#2ecc71"></div>
                <div class="color-option" style="background-color: #f39c12;" data-color="#f39c12"></div>
                <div class="color-option" style="background-color: #9b59b6;" data-color="#9b59b6"></div>
                <div class="color-option" style="background-color: #1abc9c;" data-color="#1abc9c"></div>
                <div class="color-option" style="background-color: #34495e;" data-color="#34495e"></div>
                <div class="color-option" style="background-color: #e67e22;" data-color="#e67e22"></div>
                <div class="color-option" style="background-color: #16a085;" data-color="#16a085"></div>
                <div class="color-option" style="background-color: #c0392b;" data-color="#c0392b"></div>
                <div class="color-option" style="background-color: #8e44ad;" data-color="#8e44ad"></div>
                <div class="color-option" style="background-color: #27ae60;" data-color="#27ae60"></div>
                <div class="color-option" style="background-color: #d35400;" data-color="#d35400"></div>
                <div class="color-option" style="background-color: #2980b9;" data-color="#2980b9"></div>
                <div class="color-option" style="background-color: #7f8c8d;" data-color="#7f8c8d"></div>
            </div>

            <input type="submit" value="Save Changes">
        </form>

        <a href="game.php" class="back-link">← Back to Lobby</a>
    </div>

    <script>
        // Color picker functionality
        const colorOptions = document.querySelectorAll('.color-option');
        const colorInput = document.getElementById('avatar_color');
        const currentColor = colorInput.value;

        // Set initial selected color
        colorOptions.forEach(option => {
            if (option.dataset.color === currentColor) {
                option.classList.add('selected');
            }
            
            option.addEventListener('click', function() {
                colorOptions.forEach(opt => opt.classList.remove('selected'));
                this.classList.add('selected');
                colorInput.value = this.dataset.color;
            });
        });
    </script>
</body>
</html>

