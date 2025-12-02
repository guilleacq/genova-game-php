<?php
session_start();

// If user is already logged in, redirect to game
if (isset($_SESSION['logged_user'])) {
    header('Location: game.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Genova Erasmus Lobby</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400;0,500;0,600;0,700;1,400&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* ═══════════════════════════════════════════════════════════════════
           GENOVA GAME - Registration Page
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
            --espresso: #3D2E25;
            --coffee: #5C4A3D;
            --latte: #8B7355;
            --cappuccino: #A69478;
            --olive-soft: #9CAF88;
            --rose-soft: #D4A5A5;
            --font-display: 'Lora', Georgia, serif;
            --font-body: 'DM Sans', -apple-system, sans-serif;
            --radius-md: 10px;
            --radius-lg: 16px;
            --shadow-md: 0 4px 12px rgba(61, 46, 37, 0.12);
            --shadow-lg: 0 8px 24px rgba(61, 46, 37, 0.16);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: var(--font-body);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
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
            width: 100%;
            max-width: 480px;
            animation: fadeSlideUp 0.6s ease-out;
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
        
        .logo {
            text-align: center;
            margin-bottom: 28px;
        }
        
        .logo-icon {
            font-size: 2.5rem;
            margin-bottom: 10px;
            display: block;
        }
        
        h1 {
            font-family: var(--font-display);
            font-size: 1.7rem;
            font-weight: 600;
            color: var(--espresso);
            text-align: center;
            margin-bottom: 8px;
            letter-spacing: -0.02em;
        }
        
        .subtitle {
            text-align: center;
            color: var(--latte);
            font-size: 15px;
            margin-bottom: 28px;
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
        
        /* Section Titles */
        .section-title {
            font-family: var(--font-display);
            font-weight: 600;
            font-size: 1rem;
            color: var(--espresso);
            margin: 28px 0 16px 0;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--ochre-light);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .section-title:first-of-type {
            margin-top: 0;
        }
        
        .required-indicator {
            color: var(--terracotta);
        }
        
        .optional-section {
            color: var(--coffee);
        }
        
        .optional-label {
            color: var(--latte);
            font-size: 13px;
            margin-bottom: 16px;
            font-style: italic;
        }
        
        .form-group {
            margin-bottom: 18px;
        }
        
        label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            color: var(--coffee);
            font-size: 14px;
        }
        
        input[type="text"],
        input[type="password"],
        input[type="url"],
        select,
        textarea {
            width: 100%;
            padding: 12px 14px;
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
            background-position: right 14px center;
            padding-right: 40px;
        }
        
        textarea {
            resize: vertical;
            min-height: 80px;
            font-family: var(--font-body);
        }
        
        .hint {
            color: var(--latte);
            font-size: 12px;
            margin-top: 6px;
            font-style: italic;
        }
        
        input[type="submit"] {
            width: 100%;
            padding: 14px 24px;
            background: var(--terracotta);
            color: white;
            border: none;
            border-radius: var(--radius-md);
            font-family: var(--font-body);
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            margin-top: 12px;
        }
        
        input[type="submit"]:hover {
            background: var(--terracotta-dark);
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }
        
        input[type="submit"]:active {
            transform: translateY(0);
        }
        
        .footer-text {
            text-align: center;
            margin-top: 24px;
            padding-top: 24px;
            border-top: 1px solid var(--ochre-light);
            color: var(--coffee);
            font-size: 14px;
        }
        
        .footer-text a {
            color: var(--terracotta);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s ease;
        }
        
        .footer-text a:hover {
            color: var(--terracotta-dark);
            text-decoration: underline;
        }
        
        .back-home {
            display: block;
            text-align: center;
            margin-top: 16px;
            color: var(--latte);
            text-decoration: none;
            font-size: 14px;
            transition: color 0.2s ease;
        }
        
        .back-home:hover {
            color: var(--coffee);
        }
        
        /* Two column layout for optional fields on larger screens */
        @media (min-width: 500px) {
            .two-col {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 16px;
            }
            
            .two-col .form-group {
                margin-bottom: 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="logo">
                <span class="logo-icon">🇮🇹</span>
                <h1>Join the Lobby</h1>
                <p class="subtitle">Create your account and start exploring</p>
            </div>

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
                <!-- REQUIRED FIELDS -->
                <div class="section-title"><span class="required-indicator">✦</span> Account Details</div>
                
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" placeholder="Choose a username" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" placeholder="Create a password" required>
                </div>

                <!-- OPTIONAL FIELDS -->
                <div class="section-title optional-section">✧ About You (Optional)</div>
                <p class="optional-label">You can fill these later in your profile settings.</p>

                <div class="two-col">
                    <div class="form-group">
                        <label for="country">Country of Origin</label>
                        <select name="country" id="country">
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
                    </div>

                    <div class="form-group">
                        <label for="major">What are you studying?</label>
                        <input type="text" name="major" id="major" placeholder="e.g. Computer Science">
                    </div>
                </div>

                <div class="form-group">
                    <label for="instagram_handle">Instagram Handle</label>
                    <input type="text" name="instagram_handle" id="instagram_handle" placeholder="yourhandle">
                </div>

                <div class="form-group">
                    <label for="bio">Bio</label>
                    <textarea name="bio" id="bio" rows="3" placeholder="Tell us a bit about yourself..."></textarea>
                </div>

                <div class="form-group">
                    <label for="profile_picture_url">Profile Picture URL</label>
                    <input type="url" name="profile_picture_url" id="profile_picture_url" placeholder="https://example.com/your-image.jpg">
                    <p class="hint">Paste a direct link to an image (e.g. from Imgur, Discord, etc.)</p>
                </div>
                
                <input type="submit" value="Create Account">
            </form>

            <p class="footer-text">
                Already have an account? <a href="login_form.php">Sign in</a>
            </p>
        </div>
        
        <a href="index.php" class="back-home">← Back to home</a>
    </div>
</body>
</html>

