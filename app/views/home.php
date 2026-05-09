<?php
require_once APP_ROOT . '/app/core/Auth.php';

$user    = currentUser();
$loggedIn = isLoggedIn();
$dashboardPath = 'passenger/dashboard';

if (($user['role'] ?? '') === 'admin') {
    $dashboardPath = 'admin/dashboard';
} elseif (($user['role'] ?? '') === 'operator') {
    $dashboardPath = 'operator/dashboard';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Smarter, Travel Better</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --blue:    #1a56db;
            --blue-dk: #1344b8;
            --blue-lt: #eff6ff;
            --text:    #111827;
            --muted:   #6b7280;
            --border:  #e5e7eb;
            --bg:      #f8faff;
            --white:   #ffffff;
            --shadow:  0 1px 3px rgba(0,0,0,.06), 0 4px 16px rgba(26,86,219,.06);
        }

        body {
            font-family: 'Inter', 'Segoe UI', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            font-size: 15px;
            line-height: 1.6;
        }

        /* -- Navbar ------------------------------------------ */
        .navbar {
            position: sticky;
            top: 0;
            z-index: 100;
            background: var(--white);
            border-bottom: 1px solid var(--border);
            padding: 0 32px;
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 1rem;
            font-weight: 800;
            color: var(--blue);
            text-decoration: none;
            white-space: nowrap;
        }

        .navbar-brand .bus-icon { font-size: 1.4rem; }

        .navbar-links {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .navbar-links a {
            padding: 8px 14px;
            border-radius: 6px;
            font-size: 0.88rem;
            font-weight: 500;
            color: var(--muted);
            text-decoration: none;
            transition: background .15s, color .15s;
        }

        .navbar-links a:hover { background: var(--bg); color: var(--text); }

        .navbar-actions {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn { display: inline-flex; align-items: center; justify-content: center;
               padding: 9px 18px; border-radius: 7px; font-size: 0.88rem;
               font-weight: 600; text-decoration: none; transition: all .2s; cursor: pointer;
               border: none; white-space: nowrap; }

        .btn-ghost { background: transparent; color: var(--text); border: 1px solid var(--border); }
        .btn-ghost:hover { background: var(--bg); }

        .btn-primary { background: var(--blue); color: #fff; }
        .btn-primary:hover { background: var(--blue-dk); }

        .btn-lg { padding: 13px 28px; font-size: 0.97rem; border-radius: 8px; }

        /* -- Layout ------------------------------------------ */
        .container { max-width: 1100px; margin: 0 auto; padding: 0 24px; }

        /* -- Hero -------------------------------------------- */
        .hero-wrapper {
            background: linear-gradient(160deg, #eef4ff 0%, #f8faff 55%, #f0f7ff 100%);
            border-bottom: 1px solid #dde8fb;
            position: relative;
            overflow: hidden;
        }

        /* Subtle decorative circle blobs */
        .hero-wrapper::before,
        .hero-wrapper::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            pointer-events: none;
        }
        .hero-wrapper::before {
            width: 520px; height: 520px;
            background: radial-gradient(circle, rgba(26,86,219,.07) 0%, transparent 70%);
            top: -140px; right: -100px;
        }
        .hero-wrapper::after {
            width: 340px; height: 340px;
            background: radial-gradient(circle, rgba(99,179,237,.09) 0%, transparent 70%);
            bottom: -80px; left: -60px;
        }

        .hero {
            padding: 88px 0 80px;
            text-align: center;
            position: relative;
            z-index: 1;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            background: #eff6ff;
            color: var(--blue);
            border-radius: 999px;
            font-size: 0.8rem;
            font-weight: 600;
            letter-spacing: 0.3px;
            margin-bottom: 22px;
            border: 1px solid #bfdbfe;
        }

        .hero h1 {
            font-size: clamp(2rem, 5vw, 3.2rem);
            font-weight: 800;
            line-height: 1.15;
            letter-spacing: -0.5px;
            color: var(--text);
            max-width: 640px;
            margin: 0 auto 18px;
        }

        .hero h1 span { color: var(--blue); }

        .hero p {
            font-size: 1.05rem;
            color: var(--muted);
            max-width: 520px;
            margin: 0 auto 36px;
        }

        .hero-cta {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
        }

        /* -- Stats bar --------------------------------------- */
        .stats-wrapper {
            background: var(--white);
            border-bottom: 1px solid var(--border);
            padding: 0;
        }

        .stats-bar {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            text-align: center;
        }

        .stat-item {
            padding: 28px 20px;
            border-right: 1px solid var(--border);
        }
        .stat-item:last-child { border-right: none; }

        .stat-item strong {
            display: block;
            font-size: 2rem;
            font-weight: 800;
            color: var(--blue);
            letter-spacing: -0.5px;
        }

        .stat-item span {
            font-size: 0.83rem;
            color: var(--muted);
            margin-top: 4px;
            display: block;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            font-weight: 500;
        }

        /* -- Section headings -------------------------------- */
        .section { padding: 64px 0; }

        .section-label {
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            color: var(--blue);
            margin-bottom: 10px;
        }

        .section-title {
            font-size: clamp(1.5rem, 3vw, 2.1rem);
            font-weight: 800;
            letter-spacing: -0.3px;
            margin-bottom: 14px;
        }

        .section-desc {
            color: var(--muted);
            max-width: 560px;
            font-size: 0.97rem;
        }

        .section-head { margin-bottom: 40px; }

        /* -- Steps ------------------------------------------- */
        .steps-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .step-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-top: 3px solid var(--blue);
            border-radius: 12px;
            padding: 28px;
            box-shadow: var(--shadow);
            transition: transform .2s, box-shadow .2s;
        }

        .step-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 28px rgba(26,86,219,.12);
        }

        .step-num {
            width: 40px; height: 40px;
            background: var(--blue-lt);
            color: var(--blue);
            border-radius: 10px;
            display: grid;
            place-items: center;
            font-weight: 800;
            font-size: 0.9rem;
            margin-bottom: 18px;
            border: 1.5px solid #bfdbfe;
        }

        .step-card h3 { font-size: 1.05rem; font-weight: 700; margin-bottom: 8px; }
        .step-card p  { color: var(--muted); font-size: 0.92rem; }

        /* -- Features ---------------------------------------- */
        .features-section {
            background: linear-gradient(180deg, var(--white) 0%, #f0f7ff 100%);
            border-top: 1px solid var(--border);
            border-bottom: 1px solid var(--border);
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .feature-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 28px 30px;
            display: flex;
            gap: 20px;
            align-items: flex-start;
            box-shadow: var(--shadow);
            transition: transform .2s, box-shadow .2s;
        }

        .feature-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(26,86,219,.1);
        }

        .feature-icon {
            flex-shrink: 0;
            width: 48px; height: 48px;
            background: var(--blue-lt);
            border: 1.5px solid #bfdbfe;
            border-radius: 12px;
            display: grid;
            place-items: center;
            font-size: 1.4rem;
        }

        .feature-card h3 { font-size: 1rem; font-weight: 700; margin-bottom: 6px; }
        .feature-card p  { color: var(--muted); font-size: 0.9rem; line-height: 1.65; }

        /* -- CTA banner -------------------------------------- */
        .cta-banner {
            background: linear-gradient(135deg, #1a56db 0%, #2563eb 50%, #3b82f6 100%);
            border-radius: 16px;
            padding: 56px 40px;
            text-align: center;
            color: #fff;
            margin-bottom: 72px;
            box-shadow: 0 12px 40px rgba(26,86,219,.25);
            position: relative;
            overflow: hidden;
        }

        .cta-banner::before {
            content: '';
            position: absolute;
            inset: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.04'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            pointer-events: none;
        }

        .cta-banner h2 {
            font-size: clamp(1.5rem, 3vw, 2rem);
            font-weight: 800;
            margin-bottom: 12px;
        }

        .cta-banner p { opacity: .85; margin-bottom: 28px; font-size: 1rem; }

        .btn-white { background: #fff; color: var(--blue); }
        .btn-white:hover { background: #f0f7ff; }

        .btn-outline-white {
            background: transparent;
            color: #fff;
            border: 1.5px solid rgba(255,255,255,.5);
        }
        .btn-outline-white:hover { background: rgba(255,255,255,.1); }

        /* -- Footer ------------------------------------------ */
        .footer {
            background: #f1f5fd;
            border-top: 1px solid #d8e4f8;
            padding: 44px 0 28px;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 1.6fr 1fr 1fr 1fr;
            gap: 32px;
            margin-bottom: 36px;
        }

        .footer-brand-name {
            font-size: 1.05rem;
            font-weight: 800;
            color: var(--blue);
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 10px;
        }

        .footer-brand p { color: var(--muted); font-size: 0.88rem; line-height: 1.65; }

        .footer-col strong {
            display: block;
            font-size: 0.82rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text);
            margin-bottom: 14px;
        }

        .footer-col a, .footer-col p {
            display: block;
            color: var(--muted);
            font-size: 0.88rem;
            text-decoration: none;
            line-height: 1.5;
            margin-bottom: 8px;
        }

        .footer-col a:hover { color: var(--blue); }

        .footer-bottom {
            padding-top: 20px;
            border-top: 1px solid var(--border);
            color: var(--muted);
            font-size: 0.83rem;
            display: flex;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }

        /* -- Divider ----------------------------------------- */
        .divider { border: none; border-top: 1px solid var(--border); }

        /* -- Responsive -------------------------------------- */
        @media (max-width: 900px) {
            .navbar-links { display: none; }
            .stats-bar { grid-template-columns: repeat(2, 1fr); }
            .steps-grid { grid-template-columns: 1fr; }
            .features-grid { grid-template-columns: 1fr; }
            .footer-grid { grid-template-columns: 1fr 1fr; }
        }

        @media (max-width: 560px) {
            .navbar { padding: 0 16px; }
            .container { padding: 0 16px; }
            .hero { padding: 52px 0 44px; }
            .stats-bar { grid-template-columns: 1fr 1fr; }
            .cta-banner { padding: 40px 24px; }
            .footer-grid { grid-template-columns: 1fr; }
            .section { padding: 48px 0; }
        }
    </style>
</head>
<body>

<!-- -- Navbar ----------------------------------------------- -->
<nav class="navbar">
    <a class="navbar-brand" href="<?php echo BASE_URL; ?>">
        <span class="bus-icon">&#128652;</span> Book Smarter, Travel Better
    </a>

    <div class="navbar-links">
        <a href="#how-it-works">How It Works</a>
        <a href="#features">Features</a>
        <a href="#contact">Contact</a>
        <a href="<?php echo BASE_URL; ?>search">Search Routes</a>
    </div>

    <div class="navbar-actions">
        <?php if ($loggedIn): ?>
            <a class="btn btn-ghost" href="<?php echo BASE_URL . htmlspecialchars($dashboardPath); ?>">Dashboard</a>
            <a class="btn btn-primary" href="<?php echo BASE_URL; ?>logout">Logout</a>
        <?php else: ?>
            <a class="btn btn-ghost" href="<?php echo BASE_URL; ?>login">Sign In</a>
            <a class="btn btn-primary" href="<?php echo BASE_URL; ?>register">Sign Up</a>
        <?php endif; ?>
    </div>
</nav>

<!-- -- Hero ------------------------------------------------- -->
<div class="hero-wrapper">
    <div class="container">
        <section class="hero">
            <div class="hero-badge">&#127981; Pakistan's Online Bus Ticketing</div>
            <h1>Book bus tickets from anywhere, <span>anytime</span></h1>
            <p>Search available routes, reserve your seat, and get your ticket instantly &#10004; all without visiting the bus stand.</p>
            <div class="hero-cta">
                <a class="btn btn-primary btn-lg" href="<?php echo BASE_URL; ?>search">Search Routes</a>
                <?php if ($loggedIn): ?>
                    <a class="btn btn-ghost btn-lg" href="<?php echo BASE_URL . htmlspecialchars($dashboardPath); ?>">My Dashboard</a>
                <?php else: ?>
                    <a class="btn btn-ghost btn-lg" href="<?php echo BASE_URL; ?>register">Create Free Account</a>
                <?php endif; ?>
            </div>
        </section>
    </div>
</div>

<!-- Stats bar -->
<div class="stats-wrapper">
    <div class="container" style="padding:0;">
        <div class="stats-bar">
            <div class="stat-item"><strong>50+</strong><span>Routes Available</span></div>
            <div class="stat-item"><strong>20+</strong><span>Cities Covered</span></div>
            <div class="stat-item"><strong>500+</strong><span>Tickets Booked</span></div>
            <div class="stat-item"><strong>24/7</strong><span>Online Booking</span></div>
        </div>
    </div>


<hr class="divider">

<!-- -- How It Works ----------------------------------------- -->
<div class="container">
    <section id="how-it-works" class="section">
        <div class="section-head">
            <p class="section-label">Simple Process</p>
            <h2 class="section-title">Book your seat in 3 steps</h2>
            <p class="section-desc">No phone calls, no queues. Just search, book, and travel.</p>
        </div>

        <div class="steps-grid">
            <div class="step-card">
                <div class="step-num">01</div>
                <h3>Search your route</h3>
                <p>Enter your departure and destination city. Browse all available schedules with timings and fares.</p>
            </div>
            <div class="step-card">
                <div class="step-num">02</div>
                <h3>Reserve your seat</h3>
                <p>Choose the number of seats you need and complete your booking with your account. It takes under a minute.</p>
            </div>
            <div class="step-card">
                <div class="step-num">03</div>
                <h3>Get your ticket</h3>
                <p>Download or print your ticket with a unique QR code. Show it at boarding � no paper needed.</p>
            </div>
        </div>
    </section>
</div>

<hr class="divider">

<!-- -- Features --------------------------------------------- -->
<div class="features-section">
    <div class="container">
        <section id="features" class="section">
        <div class="section-head">
            <p class="section-label">Why Choose Us</p>
            <h2 class="section-title">Everything you need for your journey</h2>
            <p class="section-desc">Built for everyday travellers in Pakistan &mdash; fast, simple, and reliable.</p>
        </div>

        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">&#128197;</div>
                <div>
                    <h3>Real-time availability</h3>
                    <p>See which buses have seats available right now. Schedules are updated by operators in real time.</p>
                </div>
            </div>
            <div class="feature-card">
                <div class="feature-icon">&#127915;</div>
                <div>
                    <h3>Digital tickets with QR codes</h3>
                    <p>Your ticket is stored in your account. Download it or show directly from your phone at the bus stand.</p>
                </div>
            </div>
            <div class="feature-card">
                <div class="feature-icon">&#128203;</div>
                <div>
                    <h3>Manage all your bookings</h3>
                    <p>View past and upcoming trips from your passenger dashboard. Everything in one place.</p>
                </div>
            </div>
            <div class="feature-card">
                <div class="feature-icon">&#128274;</div>
                <div>
                    <h3>Secure account with OTP</h3>
                    <p>Your account is protected with OTP email verification. Password reset is quick if you ever get locked out.</p>
                </div>
            </div>
        </div>
    </section>
    </div>
</div>

<!-- -- CTA Banner ------------------------------------------- -->
<div class="container">
    <div class="cta-banner">
        <h2>Ready to travel smarter?</h2>
        <p>Create a free account and book your first ticket in minutes.</p>
        <div class="hero-cta">
            <?php if ($loggedIn): ?>
                <a class="btn btn-white btn-lg" href="<?php echo BASE_URL; ?>search">Search Routes</a>
                <a class="btn btn-outline-white btn-lg" href="<?php echo BASE_URL . htmlspecialchars($dashboardPath); ?>">My Dashboard</a>
            <?php else: ?>
                <a class="btn btn-white btn-lg" href="<?php echo BASE_URL; ?>register">Create Free Account</a>
                <a class="btn btn-outline-white btn-lg" href="<?php echo BASE_URL; ?>login">Sign In</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- -- Footer ----------------------------------------------- -->
<footer id="contact" class="footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-brand">
                <div class="footer-brand-name"><span>??</span> Book Smarter, Travel Better</div>
                <p>Online bus ticketing for Pakistan. Search routes, book seats, and travel without the queue.</p>
            </div>

            <div class="footer-col">
                <strong>Quick Links</strong>
                <a href="<?php echo BASE_URL; ?>search">Search Routes</a>
                <a href="#how-it-works">How It Works</a>
                <a href="#features">Features</a>
            </div>

            <div class="footer-col">
                <strong>Account</strong>
                <a href="<?php echo BASE_URL; ?>login">Sign In</a>
                <a href="<?php echo BASE_URL; ?>register">Create Account</a>
                <a href="<?php echo BASE_URL; ?>forgot-password">Forgot Password</a>
            </div>

            <div class="footer-col">
                <strong>Contact Us</strong>
                <p>?? support@busticketing.pk</p>
                <p>?? +92 300 0000000</p>
                <p>?? Mon�Sat, 9 AM � 8 PM</p>
            </div>
        </div>

        <div class="footer-bottom">
            <span>� <?php echo date('Y'); ?> Bus Ticketing System. All rights reserved.</span>
            <span>Made for Pakistani travellers ????</span>
        </div>
    </div>
</footer>

<script src="<?php echo BASE_URL; ?>assets/js/script.js"></script>
</body>

</html>
