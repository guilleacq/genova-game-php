<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Genova Game</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400;0,500;0,600;0,700;1,400&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* ═══════════════════════════════════════════════════════════════════
           GENOVA GAME - Login Page
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
            padding: 20px;
            position: relative;
        }
        
        /* Decorative background pattern */
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
            max-width: 420px;
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
        }
        
        .logo {
            text-align: center;
            margin-bottom: 32px;
        }
        
        .logo-icon {
            font-size: 3rem;
            margin-bottom: 12px;
            display: block;
        }
        
        h1 {
            font-family: var(--font-display);
            font-size: 1.8rem;
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
            margin-bottom: 32px;
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
        input[type="password"] {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid var(--ochre-light);
            border-radius: var(--radius-md);
            font-family: var(--font-body);
            font-size: 15px;
            background: white;
            color: var(--espresso);
            transition: all 0.2s ease;
        }
        
        input[type="text"]::placeholder,
        input[type="password"]::placeholder {
            color: var(--latte);
        }
        
        input[type="text"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: var(--terracotta);
            box-shadow: 0 0 0 4px rgba(196, 105, 74, 0.12);
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
            margin-top: 8px;
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
        
        /* Decorative corner flourish */
        .card::before {
            content: '';
            position: absolute;
            top: -30px;
            right: -30px;
            width: 100px;
            height: 100px;
            background: radial-gradient(circle, rgba(196, 105, 74, 0.1) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }
        
        .card {
            position: relative;
            overflow: visible;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="logo">
                <span class="logo-icon">🇮🇹</span>
                <h1>Genova Game</h1>
                <p class="subtitle">Welcome back to Piazza Ferrari</p>
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

            <form action="auth.php" method="POST">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" placeholder="Enter your username" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" placeholder="Enter your password" required>
                </div>

                <input type="submit" value="Sign In">
            </form>

            <p class="footer-text">
                Don't have an account? <a href="register.php">Create one</a>
            </p>
        </div>
    </div>
</body>
</html>
