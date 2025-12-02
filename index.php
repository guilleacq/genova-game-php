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
    <title>Genova Erasmus Lobby</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400;0,500;0,600;0,700;1,400&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --ivory: #FDF8F0;
            --cream: #F5EDE0;
            --sand: #E8DCC8;
            --ochre-light: #D4C4A8;
            --ochre: #C9B896;
            --terracotta: #C4694A;
            --terracotta-dark: #A85438;
            --sienna: #8B5E3C;
            --espresso: #3D2E25;
            --coffee: #5C4A3D;
            --latte: #8B7355;
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
        
        html {
            scroll-behavior: smooth;
        }
        
        body {
            font-family: var(--font-body);
            background: linear-gradient(180deg, var(--cream) 0%, var(--sand) 50%, var(--ochre-light) 100%);
            background-attachment: fixed;
            color: var(--coffee);
            line-height: 1.7;
            min-height: 100vh;
        }
        
        /* Subtle texture overlay */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image: 
                radial-gradient(circle at 15% 50%, rgba(196, 105, 74, 0.06) 0%, transparent 50%),
                radial-gradient(circle at 85% 20%, rgba(139, 94, 60, 0.05) 0%, transparent 40%),
                radial-gradient(circle at 50% 80%, rgba(201, 184, 150, 0.08) 0%, transparent 50%);
            pointer-events: none;
            z-index: 0;
        }
        
        .page {
            position: relative;
            z-index: 1;
        }
        
        /* ═══════════════════════════════════════════════════════════════════
           Hero Section
           ═══════════════════════════════════════════════════════════════════ */
        .hero {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 60px 24px;
            text-align: center;
        }
        
        .hero-content {
            max-width: 700px;
        }
        
        .hero h1 {
            font-family: var(--font-display);
            font-size: 3.2rem;
            font-weight: 600;
            color: var(--espresso);
            margin-bottom: 16px;
            line-height: 1.2;
            animation: fadeUp 0.8s ease-out;
        }
        
        .hero .subtitle {
            font-size: 1.25rem;
            color: var(--sienna);
            margin-bottom: 24px;
            animation: fadeUp 0.8s ease-out 0.1s both;
        }
        
        .hero .description {
            font-size: 1.1rem;
            color: var(--coffee);
            margin-bottom: 48px;
            max-width: 540px;
            margin-left: auto;
            margin-right: auto;
            animation: fadeUp 0.8s ease-out 0.2s both;
        }
        
        .hero-buttons {
            display: flex;
            gap: 16px;
            justify-content: center;
            flex-wrap: wrap;
            animation: fadeUp 0.8s ease-out 0.3s both;
        }
        
        .btn {
            display: inline-block;
            padding: 16px 32px;
            border-radius: var(--radius-md);
            font-family: var(--font-body);
            font-size: 1rem;
            font-weight: 600;
            text-decoration: none;
            text-align: center;
            transition: all 0.25s ease;
            cursor: pointer;
            border: none;
        }
        
        .btn-primary {
            background: var(--terracotta);
            color: white;
        }
        
        .btn-primary:hover {
            background: var(--terracotta-dark);
            transform: translateY(-3px);
            box-shadow: var(--shadow-lg);
        }
        
        .btn-secondary {
            background: transparent;
            color: var(--coffee);
            border: 2px solid var(--ochre);
        }
        
        .btn-secondary:hover {
            background: var(--ivory);
            border-color: var(--sienna);
            transform: translateY(-3px);
        }
        
        .scroll-hint {
            margin-top: 60px;
            animation: fadeUp 0.8s ease-out 0.5s both;
        }
        
        .scroll-hint a {
            color: var(--latte);
            text-decoration: none;
            font-size: 0.9rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            transition: color 0.2s ease;
        }
        
        .scroll-hint a:hover {
            color: var(--terracotta);
        }
        
        .scroll-hint .arrow {
            animation: bounce 2s infinite;
        }
        
        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(6px); }
            60% { transform: translateY(3px); }
        }
        
        /* ═══════════════════════════════════════════════════════════════════
           Content Sections
           ═══════════════════════════════════════════════════════════════════ */
        .section {
            padding: 100px 24px;
            max-width: 800px;
            margin: 0 auto;
        }
        
        .section-title {
            font-family: var(--font-display);
            font-size: 2rem;
            font-weight: 600;
            color: var(--espresso);
            margin-bottom: 24px;
            text-align: center;
        }
        
        .section p {
            font-size: 1.1rem;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .section p:last-of-type {
            margin-bottom: 0;
        }
        
        /* Features Grid */
        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 32px;
            margin-top: 48px;
        }
        
        .feature {
            text-align: center;
            padding: 32px 24px;
            background: rgba(253, 248, 240, 0.6);
            border-radius: var(--radius-lg);
            border: 1px solid var(--ochre-light);
            transition: all 0.3s ease;
        }
        
        .feature:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-md);
            background: var(--ivory);
        }
        
        .feature-icon {
            font-size: 2.5rem;
            margin-bottom: 16px;
        }
        
        .feature h3 {
            font-family: var(--font-display);
            font-size: 1.2rem;
            color: var(--espresso);
            margin-bottom: 12px;
        }
        
        .feature p {
            font-size: 0.95rem;
            color: var(--latte);
            text-align: center;
            margin-bottom: 0;
        }
        
        /* Story Section */
        .story {
            background: rgba(253, 248, 240, 0.5);
            border-top: 1px solid var(--ochre-light);
            border-bottom: 1px solid var(--ochre-light);
        }
        
        .story .section {
            max-width: 650px;
        }
        
        .story p {
            text-align: left;
        }
        
        .highlight {
            color: var(--terracotta);
            font-weight: 500;
        }
        
        /* CTA Section */
        .cta {
            text-align: center;
            padding: 100px 24px 120px;
        }
        
        .cta .section-title {
            margin-bottom: 16px;
        }
        
        .cta p {
            margin-bottom: 40px;
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .cta-buttons {
            display: flex;
            gap: 16px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        /* Footer */
        .footer {
            text-align: center;
            padding-top: 48px;
            color: var(--latte);
            font-size: 0.85rem;
        }
        
        /* ═══════════════════════════════════════════════════════════════════
           Animations
           ═══════════════════════════════════════════════════════════════════ */
        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(24px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .fade-in {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.7s ease-out;
        }
        
        .fade-in.visible {
            opacity: 1;
            transform: translateY(0);
        }
        
        /* Staggered animation delays for features */
        .feature:nth-child(1) { transition-delay: 0s; }
        .feature:nth-child(2) { transition-delay: 0.1s; }
        .feature:nth-child(3) { transition-delay: 0.2s; }
        .feature:nth-child(4) { transition-delay: 0.3s; }
        
        /* ═══════════════════════════════════════════════════════════════════
           Responsive
           ═══════════════════════════════════════════════════════════════════ */
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2.4rem;
            }
            
            .hero .subtitle {
                font-size: 1.1rem;
            }
            
            .hero .description {
                font-size: 1rem;
            }
            
            .section {
                padding: 70px 20px;
            }
            
            .section-title {
                font-size: 1.7rem;
            }
            
            .hero-buttons {
                flex-direction: column;
                align-items: center;
            }
            
            .btn {
                width: 100%;
                max-width: 280px;
            }
        }
        
        @media (max-width: 480px) {
            .hero h1 {
                font-size: 2rem;
            }
            
            .features {
                gap: 20px;
            }
            
            .feature {
                padding: 24px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="page">
        <!-- Hero Section -->
        <section class="hero">
            <div class="hero-content">
                <h1>Genova Erasmus Lobby</h1>
                <p class="subtitle">A virtual meeting place at Piazza Ferrari</p>
                <p class="description">
                    Connect with fellow exchange students in Genova. Walk around the lobby, 
                    chat with others, discover new friends, and follow each other on Instagram.
                </p>
                <div class="hero-buttons">
                    <a href="login_form.php" class="btn btn-primary">Sign In & Play</a>
                    <a href="register_form.php" class="btn btn-secondary">Create Account</a>
                </div>
            </div>
            <div class="scroll-hint">
                <a href="#about">
                    <span>Learn more</span>
                    <span class="arrow">↓</span>
                </a>
            </div>
        </section>
        
        <!-- About Section -->
        <section id="about" class="section fade-in">
            <h2 class="section-title">What is this?</h2>
            <p>
                The Genova Erasmus Lobby is a <span class="highlight">virtual social space</span> 
                for your Erasmus journey. It's a place where students in Genova can meet, 
                chat, and make new friends — all without leaving their room.
            </p>
            <p>
                Click to walk around <span class="highlight">Piazza Ferrari</span>, 
                the heart of the city. See who's online, read their profiles, 
                learn what they're studying, and connect on Instagram.
            </p>
            
            <div class="features fade-in">
                <div class="feature">
                    <div class="feature-icon">👋</div>
                    <h3>Meet People</h3>
                    <p>See other students in the lobby and click on them to view their profile</p>
                </div>
                <div class="feature">
                    <div class="feature-icon">💬</div>
                    <h3>Chat Together</h3>
                    <p>Talk with everyone in the global chat or send messages in the lobby</p>
                </div>
                <div class="feature">
                    <div class="feature-icon">🤝</div>
                    <h3>Make Friends</h3>
                    <p>Send friend requests and build your network of exchange students</p>
                </div>
                <div class="feature">
                    <div class="feature-icon">📸</div>
                    <h3>Connect</h3>
                    <p>Find each other on Instagram and stay in touch beyond the lobby</p>
                </div>
            </div>
        </section>
        
        <!-- Story Section -->
        <section class="story">
            <div class="section fade-in">
                <h2 class="section-title">Why we built this</h2>
                <p>
                    Arriving in a new city as an exchange student can be overwhelming. 
                    You're surrounded by thousands of people, but it's hard to know 
                    who else is in the same boat as you.
                </p>
                <p>
                    We built the Genova Erasmus Lobby to make those first connections easier. 
                    No need to wait for orientation events or hope you bump into someone 
                    at the university. Just step into the virtual piazza and start meeting 
                    the people who'll become your friends, study partners, and travel buddies.
                </p>
                <p>
                    Whether you just arrived or you've been here for months, 
                    there's always someone new to meet at <span class="highlight">Piazza Ferrari</span>.
                </p>
            </div>
        </section>
        
        <!-- Final CTA -->
        <section class="cta">
            <div class="fade-in">
                <h2 class="section-title">Ready to join?</h2>
                <p>
                    Create your profile in 30 seconds and start meeting 
                    other Erasmus students in Genova.
                </p>
                <div class="cta-buttons">
                    <a href="register_form.php" class="btn btn-primary">Create Account</a>
                    <a href="login_form.php" class="btn btn-secondary">I already have one</a>
                </div>
                <p class="footer">Made by Guille 🇺🇾 & Mali 🇩🇪 · Erasmus students, Genova 2025</p>
            </div>
        </section>
    </div>
    
    <script>
        // Intersection Observer for fade-in animations
        const observerOptions = {
            threshold: 0.15,
            rootMargin: '0px 0px -50px 0px'
        };
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, observerOptions);
        
        document.querySelectorAll('.fade-in').forEach(el => {
            observer.observe(el);
        });
    </script>
</body>
</html>
