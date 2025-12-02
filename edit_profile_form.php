<?php
session_start();

if (!isset($_SESSION['logged_user'])) {
    header('Location: login_form.php');
    exit();
}

require 'db.php';

// Get current user data
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT bio, country, major, instagram_handle, avatar_color, profile_picture_url FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($bio, $country, $major, $instagram_handle, $avatar_color, $profile_picture_url);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - Genova Erasmus Lobby</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400;0,500;0,600;0,700;1,400&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* ═══════════════════════════════════════════════════════════════════
           GENOVA GAME - Edit Profile Page
           Mediterranean Warmth Design
           ═══════════════════════════════════════════════════════════════════ */
        
        :root {
            --ivory: #FDF8F0;
            --cream: #F5EDE0;
            --sand: #E8DCC8;
            --ochre-light: #D4C4A8;
            --ochre: #C9B896;
            --terracotta: #C4694A;
            --terracotta-dark: #A85438;
            --terracotta-light: #D4856B;
            --sienna: #8B5E3C;
            --sienna-dark: #6B4A2F;
            --espresso: #3D2E25;
            --coffee: #5C4A3D;
            --latte: #8B7355;
            --cappuccino: #A69478;
            --olive-soft: #9CAF88;
            --olive-dark: #7A8F68;
            --rose-soft: #D4A5A5;
            --rose-dark: #B88888;
            --gold: #D4A54A;
            --font-display: 'Lora', Georgia, serif;
            --font-body: 'DM Sans', -apple-system, sans-serif;
            --radius-md: 10px;
            --radius-lg: 16px;
            --radius-xl: 24px;
            --shadow-sm: 0 1px 3px rgba(61, 46, 37, 0.08);
            --shadow-md: 0 4px 12px rgba(61, 46, 37, 0.12);
            --shadow-lg: 0 8px 24px rgba(61, 46, 37, 0.16);
            --shadow-xl: 0 12px 40px rgba(61, 46, 37, 0.2);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: var(--font-body);
            min-height: 100vh;
            background: linear-gradient(135deg, var(--cream) 0%, var(--sand) 50%, var(--ochre-light) 100%);
            color: var(--espresso);
            line-height: 1.5;
            padding: 40px 20px;
            position: relative;
        }
        
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image: 
                radial-gradient(circle at 20% 80%, rgba(196, 105, 74, 0.08) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(139, 94, 60, 0.06) 0%, transparent 50%);
            pointer-events: none;
        }
        
        .container {
            max-width: 600px;
            margin: 0 auto;
            animation: fadeSlideUp 0.5s ease-out;
        }
        
        @keyframes fadeSlideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .card {
            background: var(--ivory);
            padding: 40px;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--ochre-light);
            position: relative;
        }
        
        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
            padding-bottom: 20px;
            border-bottom: 2px solid var(--ochre-light);
        }
        
        h1 {
            font-family: var(--font-display);
            font-size: 1.6rem;
            font-weight: 600;
            color: var(--espresso);
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        h1::before {
            content: '✎';
            font-size: 1.2rem;
            color: var(--terracotta);
        }
        
        .optional-note {
            color: var(--latte);
            font-size: 14px;
            margin-bottom: 24px;
            font-style: italic;
        }
        
        .error {
            color: var(--sienna);
            background-color: #FDF2F0;
            border: 1px solid var(--rose-soft);
            padding: 12px 16px;
            border-radius: var(--radius-md);
            margin-bottom: 20px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .error::before {
            content: '⚠';
            font-size: 18px;
        }
        
        .success {
            color: #4A6741;
            background-color: #F0F7ED;
            border: 1px solid var(--olive-soft);
            padding: 12px 16px;
            border-radius: var(--radius-md);
            margin-bottom: 20px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .success::before {
            content: '✓';
            font-size: 18px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--coffee);
            font-size: 14px;
        }
        
        input[type="text"],
        input[type="url"],
        select,
        textarea {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid var(--ochre-light);
            border-radius: var(--radius-md);
            font-family: var(--font-body);
            font-size: 14px;
            background: white;
            color: var(--espresso);
            transition: all 0.2s ease;
        }
        
        input::placeholder,
        textarea::placeholder {
            color: var(--cappuccino);
        }
        
        input:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: var(--terracotta);
            box-shadow: 0 0 0 4px rgba(196, 105, 74, 0.12);
        }
        
        select {
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%238B7355' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 16px center;
            padding-right: 44px;
        }
        
        textarea {
            resize: vertical;
            min-height: 100px;
            font-family: var(--font-body);
        }
        
        .hint {
            color: var(--latte);
            font-size: 12px;
            margin-top: 6px;
        }
        
        /* Profile Picture Preview */
        .profile-picture-section {
            background: var(--cream);
            padding: 20px;
            border-radius: var(--radius-md);
            margin-bottom: 24px;
            border: 1px solid var(--ochre-light);
        }
        
        .profile-picture-preview {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-top: 12px;
        }
        
        .preview-circle {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            border: 4px solid var(--ivory);
            box-shadow: var(--shadow-md);
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        
        .preview-circle img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }
        
        .preview-label {
            color: var(--latte);
            font-size: 13px;
        }
        
        /* Avatar Color Picker */
        .color-section {
            background: var(--cream);
            padding: 20px;
            border-radius: var(--radius-md);
            margin-bottom: 24px;
            border: 1px solid var(--ochre-light);
        }
        
        .color-section label {
            margin-bottom: 12px;
        }
        
        .color-picker-container {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .color-option {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
            border: 3px solid transparent;
            transition: all 0.2s ease;
            box-shadow: var(--shadow-sm);
        }
        
        .color-option:hover {
            transform: scale(1.1);
            box-shadow: var(--shadow-md);
        }
        
        .color-option.selected {
            border-color: var(--espresso);
            transform: scale(1.15);
            box-shadow: 0 0 0 3px rgba(61, 46, 37, 0.2);
        }
        
        /* Buttons */
        .button-group {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-top: 28px;
            padding-top: 24px;
            border-top: 1px solid var(--ochre-light);
        }
        
        input[type="submit"] {
            flex: 1;
            padding: 14px 24px;
            background: var(--terracotta);
            color: white;
            border: none;
            border-radius: var(--radius-md);
            font-family: var(--font-body);
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        input[type="submit"]:hover {
            background: var(--terracotta-dark);
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }
        
        input[type="submit"]:active {
            transform: translateY(0);
        }
        
        .btn-delete {
            padding: 14px 24px;
            background: var(--rose-soft);
            color: var(--sienna-dark);
            border: none;
            border-radius: var(--radius-md);
            font-family: var(--font-body);
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .btn-delete:hover {
            background: var(--rose-dark);
            color: white;
            transform: translateY(-2px);
        }
        
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-top: 20px;
            color: var(--terracotta);
            text-decoration: none;
            font-weight: 500;
            font-size: 14px;
            transition: all 0.2s ease;
        }
        
        .back-link:hover {
            color: var(--terracotta-dark);
            transform: translateX(-4px);
        }
        
        .back-link::before {
            content: '←';
        }
        
        /* Delete Account Modal */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(61, 46, 37, 0.6);
            z-index: 1000;
            justify-content: center;
            align-items: center;
            backdrop-filter: blur(4px);
            animation: fadeIn 0.2s ease-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        .modal-overlay.active {
            display: flex;
        }
        
        .modal {
            background: var(--ivory);
            padding: 32px;
            border-radius: var(--radius-lg);
            max-width: 420px;
            text-align: center;
            box-shadow: var(--shadow-xl);
            border: 1px solid var(--ochre-light);
            animation: slideUp 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
        
        .modal h2 {
            font-family: var(--font-display);
            color: var(--rose-dark);
            margin-bottom: 16px;
            font-size: 1.4rem;
        }
        
        .modal p {
            color: var(--coffee);
            margin-bottom: 28px;
            line-height: 1.6;
        }
        
        .modal-buttons {
            display: flex;
            justify-content: center;
            gap: 12px;
        }
        
        .modal-btn {
            padding: 12px 24px;
            border: none;
            border-radius: var(--radius-md);
            cursor: pointer;
            font-family: var(--font-body);
            font-size: 14px;
            font-weight: 600;
            transition: all 0.2s ease;
        }
        
        .modal-btn-cancel {
            background: var(--sand);
            color: var(--coffee);
            border: 1px solid var(--ochre-light);
        }
        
        .modal-btn-cancel:hover {
            background: var(--ochre-light);
        }
        
        .modal-btn-confirm {
            background: var(--rose-dark);
            color: white;
        }
        
        .modal-btn-confirm:hover {
            background: #A67777;
            transform: translateY(-1px);
        }
        
        /* Responsive */
        @media (max-width: 600px) {
            body {
                padding: 20px 16px;
            }
            
            .card {
                padding: 28px 24px;
            }
            
            h1 {
                font-size: 1.4rem;
            }
            
            .button-group {
                flex-direction: column;
            }
            
            .button-group input[type="submit"],
            .button-group .btn-delete {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="page-header">
                <h1>Edit Your Profile</h1>
            </div>
            
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

            <form action="edit_profile_process.php" method="POST">
                <div class="form-group">
                    <label for="country">Country of Origin</label>
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
                </div>

                <div class="form-group">
                    <label for="major">What are you studying?</label>
                    <input type="text" name="major" id="major" value="<?php echo htmlspecialchars($major, ENT_QUOTES, 'UTF-8'); ?>" placeholder="e.g. Computer Science, Business, etc.">
                </div>

                <div class="form-group">
                    <label for="instagram_handle">Instagram Handle</label>
                    <input type="text" name="instagram_handle" id="instagram_handle" value="<?php echo htmlspecialchars($instagram_handle, ENT_QUOTES, 'UTF-8'); ?>" placeholder="@yourhandle">
                </div>

                <div class="form-group">
                    <label for="bio">Bio</label>
                    <textarea name="bio" id="bio" rows="4" placeholder="Tell us a bit about yourself..."><?php echo htmlspecialchars($bio, ENT_QUOTES, 'UTF-8'); ?></textarea>
                </div>

                <div class="profile-picture-section">
                    <label for="profile_picture_url">Profile Picture URL</label>
                    <input type="url" name="profile_picture_url" id="profile_picture_url" value="<?php echo htmlspecialchars($profile_picture_url ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="https://example.com/your-image.jpg">
                    <p class="hint">Paste a direct link to an image (e.g. from Imgur, Discord, etc.). Leave empty to use avatar color.</p>
                    
                    <div class="profile-picture-preview">
                        <div class="preview-circle" id="previewCircle" style="background-color: <?php echo htmlspecialchars($avatar_color, ENT_QUOTES, 'UTF-8'); ?>;">
                            <?php if ($profile_picture_url): ?>
                                <img src="<?php echo htmlspecialchars($profile_picture_url, ENT_QUOTES, 'UTF-8'); ?>" alt="Preview" id="previewImage">
                            <?php endif; ?>
                        </div>
                        <span class="preview-label">Preview (how you'll appear in-game)</span>
                    </div>
                </div>

                <div class="color-section">
                    <label>Accent Color</label>
                    <input type="hidden" name="avatar_color" id="avatar_color" value="<?php echo htmlspecialchars($avatar_color, ENT_QUOTES, 'UTF-8'); ?>">
                    <div class="color-picker-container">
                        <div class="color-option" style="background-color: #C4694A;" data-color="#C4694A"></div>
                        <div class="color-option" style="background-color: #D4A54A;" data-color="#D4A54A"></div>
                        <div class="color-option" style="background-color: #9CAF88;" data-color="#9CAF88"></div>
                        <div class="color-option" style="background-color: #8B5E3C;" data-color="#8B5E3C"></div>
                        <div class="color-option" style="background-color: #A85438;" data-color="#A85438"></div>
                        <div class="color-option" style="background-color: #7A8F68;" data-color="#7A8F68"></div>
                        <div class="color-option" style="background-color: #D4856B;" data-color="#D4856B"></div>
                        <div class="color-option" style="background-color: #B88888;" data-color="#B88888"></div>
                        <div class="color-option" style="background-color: #A69478;" data-color="#A69478"></div>
                        <div class="color-option" style="background-color: #6B4A2F;" data-color="#6B4A2F"></div>
                        <div class="color-option" style="background-color: #5C4A3D;" data-color="#5C4A3D"></div>
                        <div class="color-option" style="background-color: #C9B896;" data-color="#C9B896"></div>
                    </div>
                </div>

                <div class="button-group">
                    <input type="submit" value="Save Changes">
                    <button type="button" class="btn-delete" onclick="showDeleteModal()">Delete Account</button>
                </div>
            </form>

            <a href="game.php" class="back-link">Back to Lobby</a>
        </div>
    </div>

    <!-- Delete Account Confirmation Modal -->
    <div class="modal-overlay" id="deleteModal">
        <div class="modal">
            <h2>⚠️ Delete Account</h2>
            <p>Are you sure you want to delete your account? This action is <strong>permanent</strong> and cannot be undone. All your data will be lost.</p>
            <div class="modal-buttons">
                <button class="modal-btn modal-btn-cancel" onclick="hideDeleteModal()">Cancel</button>
                <button class="modal-btn modal-btn-confirm" onclick="confirmDeleteAccount()">Yes, Delete</button>
            </div>
        </div>
    </div>

    <script>
        // Color picker functionality
        const colorOptions = document.querySelectorAll('.color-option');
        const colorInput = document.getElementById('avatar_color');
        const currentColor = colorInput.value;
        const previewCircle = document.getElementById('previewCircle');

        // Set initial selected color
        colorOptions.forEach(option => {
            if (option.dataset.color === currentColor) {
                option.classList.add('selected');
            }
            
            option.addEventListener('click', function() {
                colorOptions.forEach(opt => opt.classList.remove('selected'));
                this.classList.add('selected');
                colorInput.value = this.dataset.color;
                previewCircle.style.backgroundColor = this.dataset.color;
            });
        });

        // Profile picture URL preview
        const profilePictureInput = document.getElementById('profile_picture_url');
        
        profilePictureInput.addEventListener('input', function() {
            const url = this.value.trim();
            let previewImage = document.getElementById('previewImage');
            
            if (url) {
                if (!previewImage) {
                    previewImage = document.createElement('img');
                    previewImage.id = 'previewImage';
                    previewImage.alt = 'Preview';
                    previewCircle.appendChild(previewImage);
                }
                previewImage.src = url;
                previewImage.onerror = function() {
                    this.remove();
                };
            } else {
                if (previewImage) {
                    previewImage.remove();
                }
            }
        });

        // Delete account modal functionality
        function showDeleteModal() {
            document.getElementById('deleteModal').classList.add('active');
        }

        function hideDeleteModal() {
            document.getElementById('deleteModal').classList.remove('active');
        }

        // Close modal when clicking outside
        document.getElementById('deleteModal').addEventListener('click', function(e) {
            if (e.target === this) {
                hideDeleteModal();
            }
        });

        function confirmDeleteAccount() {
            fetch('api/delete_account.php', {
                method: 'POST',
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Your account has been deleted. Goodbye!');
                    window.location.href = 'index.php';
                } else {
                    alert('Error: ' + (data.error || 'Failed to delete account'));
                    hideDeleteModal();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while deleting your account.');
                hideDeleteModal();
            });
        }
    </script>
</body>
</html>

