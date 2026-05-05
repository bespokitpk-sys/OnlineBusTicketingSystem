<?php
require_once __DIR__ . '/includes/auth.php';

$user = currentUser();
$loggedIn = isLoggedIn();
$dashboardPath = 'passenger/dashboard.php';

if (($user['role'] ?? '') === 'admin') {
    $dashboardPath = 'admin/dashboard.php';
} elseif (($user['role'] ?? '') === 'operator') {
    $dashboardPath = 'operator/dashboard.php';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Smarter, Travel Better</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            background: #f5f7fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            color: #1f2937;
        }

        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: linear-gradient(90deg, #e8f4f8 0%, #d4e9f7 100%);
            color: #0f1c33;
            padding: 20px 40px;
            box-shadow: 0 8px 24px rgba(15, 28, 51, 0.12);
            position: sticky;
            top: 0;
            z-index: 100;
            flex-wrap: nowrap;
            gap: 10px;
            min-height: 70px;
        }

        nav h2 {
            font-size: 1.6rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            gap: 10px;
            white-space: nowrap;
            margin: 0;
            flex-shrink: 0;
        }

        nav div {
            display: flex;
            gap: 10px;
            align-items: center;
            justify-content: flex-end;
            flex-wrap: nowrap;
        }

        nav a {
            color: #0f1c33;
            text-decoration: none;
            transition: all 0.3s ease;
            font-weight: 600;
            font-size: 0.85rem;
            padding: 10px 16px;
            border-radius: 6px;
            background: rgba(0, 114, 255, 0.1);
            border: 1px solid rgba(0, 114, 255, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: auto;
            height: 38px;
            white-space: nowrap;
            line-height: 1;
            box-sizing: border-box;
            cursor: pointer;
        }

        nav a:hover {
            color: #ffffff;
            background: #0072ff;
            border-color: #0072ff;
            box-shadow: 0 4px 12px rgba(0, 114, 255, 0.5);
            transform: translateY(-2px);
        }

        .page-shell {
            max-width: 1400px;
            margin: 0 auto;
            padding: 30px 20px 40px;
        }

        .hero {
            background: white;
            color: #0f1c33;
            padding: 36px;
            border-radius: 14px;
            margin-bottom: 24px;
            box-shadow: 0 2px 10px rgba(15, 28, 51, 0.08);
            border: 1px solid #e7edf5;
        }

        .hero-grid {
            display: grid;
            grid-template-columns: 1.15fr 0.85fr;
            gap: 28px;
            align-items: stretch;
        }

        .eyebrow {
            display: inline-block;
            margin-bottom: 12px;
            padding: 8px 14px;
            border-radius: 999px;
            background: linear-gradient(90deg, #e8f4f8 0%, #d4e9f7 100%);
            color: #0f1c33;
            font-size: 0.82rem;
            font-weight: 700;
            letter-spacing: 0.4px;
            text-transform: uppercase;
        }

        .hero h1 {
            font-size: 2.9rem;
            line-height: 1.1;
            margin-bottom: 14px;
            letter-spacing: -0.5px;
        }

        .hero p {
            font-size: 1rem;
            line-height: 1.75;
            color: #5f6b7a;
            max-width: 700px;
        }

        .hero-actions {
            display: flex;
            gap: 14px;
            flex-wrap: wrap;
            margin-top: 26px;
        }

        .hero-actions a,
        .support-actions a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 14px 22px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 700;
            transition: all 0.3s ease;
            min-height: 48px;
        }

        .btn-primary-solid {
            background: #0072ff;
            color: white;
            box-shadow: 0 8px 24px rgba(0, 114, 255, 0.25);
        }

        .btn-primary-solid:hover {
            background: #005fd6;
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: #f8fbff;
            color: #0f1c33;
            border: 1px solid #d8e3ef;
        }

        .btn-secondary:hover {
            background: #eef5ff;
            transform: translateY(-1px);
        }

        .hero-highlights {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
            margin-top: 26px;
        }

        .highlight-card {
            background: #fbfcfe;
            padding: 18px;
            border-radius: 12px;
            border: 1px solid #e5edf5;
        }

        .highlight-card strong {
            display: block;
            font-size: 1.2rem;
            color: #0f1c33;
            margin-bottom: 6px;
        }

        .highlight-card span {
            display: block;
            font-size: 0.92rem;
            color: #5f6b7a;
            line-height: 1.55;
        }

        .hero-side {
            background: linear-gradient(180deg, #f8fbff 0%, #eef5ff 100%);
            border: 1px solid #dbe7f4;
            border-radius: 14px;
            padding: 28px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .hero-side h3 {
            font-size: 1.6rem;
            margin-bottom: 12px;
            color: #0f1c33;
        }

        .hero-side p {
            color: #5f6b7a;
            line-height: 1.75;
            margin-bottom: 18px;
        }

        .feature-list {
            display: grid;
            gap: 12px;
        }

        .feature-item {
            background: white;
            border: 1px solid #e5edf5;
            border-radius: 10px;
            padding: 14px 16px;
        }

        .feature-item label {
            display: block;
            font-size: 0.76rem;
            color: #7a8696;
            text-transform: uppercase;
            letter-spacing: 0.35px;
            margin-bottom: 8px;
            font-weight: 700;
        }

        .feature-item span {
            display: block;
            color: #0f1c33;
            font-weight: 600;
            line-height: 1.55;
        }

        .content-section {
            padding-top: 8px;
            margin-bottom: 24px;
        }

        .section-panel {
            background: white;
            border: 1px solid #e7edf5;
            border-radius: 14px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(15, 28, 51, 0.08);
        }

        .section-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 24px;
            margin-bottom: 24px;
        }

        .section-head h2 {
            font-size: 2rem;
            color: #0f1c33;
            margin-bottom: 10px;
            letter-spacing: -0.3px;
        }

        .section-head p {
            color: #5f6b7a;
            max-width: 700px;
            line-height: 1.7;
        }

        .cards-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 18px;
        }

        .info-card {
            background: #fbfcfe;
            padding: 24px;
            border-radius: 12px;
            border: 1px solid #e5edf5;
            transition: all 0.3s ease;
        }

        .info-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(15, 28, 51, 0.08);
        }

        .mini-tag {
            display: inline-block;
            padding: 7px 12px;
            border-radius: 999px;
            background: linear-gradient(90deg, #e8f4f8 0%, #d4e9f7 100%);
            color: #0f1c33;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.35px;
            margin-bottom: 16px;
        }

        .info-card h3 {
            font-size: 1.1rem;
            margin-bottom: 12px;
            color: #0f1c33;
        }

        .info-card p {
            color: #5f6b7a;
            line-height: 1.7;
            font-size: 0.94rem;
        }

        .steps-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 18px;
        }

        .step-card {
            padding: 28px;
            border-radius: 12px;
            background: #fbfcfe;
            border: 1px solid #e5edf5;
        }

        .step-number {
            width: 52px;
            height: 52px;
            border-radius: 12px;
            display: grid;
            place-items: center;
            margin-bottom: 18px;
            background: #0072ff;
            color: #fff;
            font-size: 1rem;
            font-weight: 700;
        }

        .step-card h3 {
            font-size: 1.18rem;
            margin-bottom: 10px;
            color: #0f1c33;
        }

        .step-card p {
            color: #5f6b7a;
            line-height: 1.7;
        }

        .split-panel {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(320px, 0.9fr);
            gap: 20px;
        }

        .quote-card,
        .support-card {
            padding: 30px;
            border-radius: 14px;
            box-shadow: 0 2px 10px rgba(15, 28, 51, 0.08);
        }

        .quote-card {
            background: white;
            border: 1px solid #e7edf5;
        }

        .quote-card h3,
        .support-card h3 {
            font-size: 1.8rem;
            margin-bottom: 14px;
            color: #0f1c33;
        }

        .quote-card p,
        .support-card p {
            color: #5f6b7a;
            line-height: 1.7;
        }

        .quote-metrics {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
            margin-top: 24px;
        }

        .quote-metrics div {
            padding: 16px 18px;
            border-radius: 12px;
            background: #fbfcfe;
            border: 1px solid #e5edf5;
        }

        .quote-metrics strong {
            display: block;
            font-size: 1.35rem;
            margin-bottom: 6px;
            color: #0f1c33;
        }

        .quote-metrics span {
            display: block;
            color: #5f6b7a;
            font-size: 0.9rem;
            line-height: 1.6;
        }

        .support-card {
            background: linear-gradient(180deg, #f8fbff 0%, #eef5ff 100%);
            border: 1px solid #dbe7f4;
        }

        .support-list {
            display: grid;
            gap: 12px;
            margin-top: 22px;
        }

        .support-item {
            padding: 16px 18px;
            border-radius: 10px;
            background: white;
            border: 1px solid #e5edf5;
        }

        .support-item strong {
            display: block;
            margin-bottom: 6px;
            font-size: 1rem;
            color: #0f1c33;
        }

        .support-item span {
            display: block;
            color: #5f6b7a;
            font-size: 0.92rem;
            line-height: 1.6;
        }

        .support-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: 22px;
        }

        .footer {
            background: white;
            border: 1px solid #e7edf5;
            border-radius: 14px;
            box-shadow: 0 2px 10px rgba(15, 28, 51, 0.08);
            padding: 32px 30px 24px;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 1.2fr repeat(4, minmax(0, 1fr));
            gap: 18px;
        }

        .footer-brand {
            padding-right: 26px;
        }

        .footer-brand p,
        .footer-column a,
        .footer-column p {
            color: #5f6b7a;
            font-size: 0.94rem;
            line-height: 1.7;
        }

        .footer-column strong {
            display: block;
            margin-bottom: 14px;
            font-size: 0.95rem;
            font-weight: 700;
            color: #0f1c33;
        }

        .footer-column {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .footer-bottom {
            margin-top: 28px;
            padding-top: 22px;
            border-top: 1px solid #e7edf5;
            display: flex;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
            color: #5f6b7a;
            font-size: 0.9rem;
        }

        @media (max-width: 1120px) {
            .hero-grid,
            .split-panel,
            .footer-grid,
            .glass-grid,
            .steps-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .section-head,
            nav {
                align-items: flex-start;
                flex-direction: column;
            }

            .footer-brand {
                padding-right: 0;
            }
        }

        @media (max-width: 820px) {
            .page-shell {
                padding: 24px 16px 30px;
            }

            .hero-grid,
            .cards-grid,
            .steps-grid,
            .split-panel,
            .footer-grid,
            .hero-highlights,
            .quote-metrics {
                grid-template-columns: 1fr;
            }

            nav {
                padding: 16px 20px;
            }

            nav h2 {
                font-size: 1.2rem;
            }

            nav div {
                flex-wrap: wrap;
                justify-content: flex-start;
            }

            .hero,
            .section-panel,
            .footer,
            .hero-side,
            .quote-card,
            .support-card {
                padding: 24px 20px;
            }
        }
    </style>
</head>
<body>
<div class="page-shell">
    <nav>
        <h2><span style="font-size: 2.5rem; display: inline-block;">🚌</span> Book Smarter, Travel Better</h2>
        <div>
            <a href="#why-choose-us">Why Choose Us</a>
            <a href="#about-us">About Us</a>
            <a href="#need-help">Need Help</a>
            <a href="<?php echo BASE_URL; ?>public/search.php">Search</a>
            <?php if ($loggedIn): ?>
                <a href="<?php echo BASE_URL . htmlspecialchars($dashboardPath); ?>">Dashboard</a>
                <a href="<?php echo BASE_URL; ?>auth/logout.php">Logout</a>
            <?php else: ?>
                <a href="<?php echo BASE_URL; ?>public/auth_router.php?action=login">Sign In</a>
                <a href="<?php echo BASE_URL; ?>public/auth_router.php?action=register">Sign Up</a>
            <?php endif; ?>
        </div>
    </nav>

    <section class="hero">
        <div class="hero-grid">
            <div>
                <span class="eyebrow">Modern Bus Booking Experience</span>
                <h1>Book bus tickets online with a cleaner and more reliable travel experience.</h1>
                <p>Search routes, reserve your seats, manage bookings, and keep your trip details organized in one place. The home page now follows the same visual theme used across the rest of the project, with a clearer layout and more professional presentation.</p>
                <div class="hero-actions">
                    <a class="btn-primary-solid" href="<?php echo BASE_URL; ?>public/search.php">Search Available Routes</a>
                    <?php if ($loggedIn): ?>
                        <a class="btn-secondary" href="<?php echo BASE_URL . htmlspecialchars($dashboardPath); ?>">Open Dashboard</a>
                    <?php else: ?>
                        <a class="btn-secondary" href="<?php echo BASE_URL; ?>public/auth_router.php?action=register">Create Your Account</a>
                    <?php endif; ?>
                </div>
                <div class="hero-highlights">
                    <div class="highlight-card">
                        <strong>Fast Search</strong>
                        <span>Check available routes quickly and move straight into booking.</span>
                    </div>
                    <div class="highlight-card">
                        <strong>Secure Booking</strong>
                        <span>Reserve seats with a clear process and professional ticket handling.</span>
                    </div>
                    <div class="highlight-card">
                        <strong>Easy Access</strong>
                        <span>Sign in, manage your account, and keep your trip details available anytime.</span>
                    </div>
                </div>
            </div>

            <aside class="hero-side">
                <div>
                    <h3>Travel with clarity from booking to departure</h3>
                    <p>Passengers can explore routes, book with confidence, and view trip details in a consistent interface that matches the rest of the system.</p>
                    <div class="feature-list">
                        <div class="feature-item">
                            <label>Booking Flow</label>
                            <span>Simple route search, seat selection, and ticket generation.</span>
                        </div>
                        <div class="feature-item">
                            <label>Trip Details</label>
                            <span>Keep departure, route, and receipt information easy to review.</span>
                        </div>
                        <div class="feature-item">
                            <label>Professional UI</label>
                            <span>Cleaner sections, clearer actions, and the same theme used across the project.</span>
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    </section>

    <section id="why-choose-us" class="content-section">
        <div class="section-panel">
            <div class="section-head">
                <div>
                    <h2>Why Choose Us</h2>
                    <p>Our platform is designed to make bus booking feel simple, professional, and dependable for everyday travelers.</p>
                </div>
            </div>

            <div class="cards-grid">
            <article class="info-card">
                <span class="mini-tag">Simple access</span>
                <h3>Cleaner navigation</h3>
                <p>The home page keeps the same navbar style used in the project, with clear actions and a less cluttered first impression.</p>
            </article>
            <article class="info-card">
                <span class="mini-tag">Reliable search</span>
                <h3>Find routes faster</h3>
                <p>Passengers can move from the landing page to route search quickly without unnecessary steps.</p>
            </article>
            <article class="info-card">
                <span class="mini-tag">Clear booking</span>
                <h3>Professional ticket flow</h3>
                <p>From booking to ticket view, the experience stays readable, structured, and easy to follow.</p>
            </article>
            <article class="info-card">
                <span class="mini-tag">Consistent theme</span>
                <h3>Matches the full project</h3>
                <p>The homepage now follows the same light-blue, white-card design language already used in the dashboards and receipts.</p>
            </article>
            </div>
        </div>
    </section>

    <section id="about-us" class="content-section">
        <div class="section-panel">
            <div class="section-head">
                <div>
                    <h2>About Us</h2>
                    <p>This bus ticketing platform is built to give travelers a straightforward way to search routes, reserve seats, and keep track of their journeys with less friction.</p>
                </div>
            </div>

            <div class="steps-grid">
                <article class="step-card">
                    <div class="step-number">01</div>
                    <h3>Search routes</h3>
                    <p>Explore available schedules, compare departures, and move into booking with a simple flow.</p>
                </article>
                <article class="step-card">
                    <div class="step-number">02</div>
                    <h3>Reserve your seats</h3>
                    <p>Choose the number of seats you need and complete your booking with a clean, guided process.</p>
                </article>
                <article class="step-card">
                    <div class="step-number">03</div>
                    <h3>Manage your trip</h3>
                    <p>Keep your ticket, trip details, and account actions organized after sign in.</p>
                </article>
            </div>
        </div>
    </section>

    <section class="content-section">
        <div class="split-panel">
            <article class="quote-card">
                <h3>Built to feel more complete and trustworthy</h3>
                <p>The homepage now uses clearer sections, stronger content hierarchy, and a layout that feels closer to a real booking product while still matching the rest of the system.</p>
                <div class="quote-metrics">
                    <div>
                        <strong>About Us</strong>
                        <span>Explain what the platform offers and what travelers can expect.</span>
                    </div>
                    <div>
                        <strong>Contact Us</strong>
                        <span>Guide users toward support and communication channels.</span>
                    </div>
                    <div>
                        <strong>Why Choose Us</strong>
                        <span>Highlight reliability, better design, and a smoother booking flow.</span>
                    </div>
                    <div>
                        <strong>Need Help</strong>
                        <span>Point travelers toward sign-in, account, and booking support.</span>
                    </div>
                </div>
            </article>

            <aside id="need-help" class="support-card">
                <h3>Need help?</h3>
                <p>If you need help creating an account, signing in, searching routes, or recovering your password, the key actions are kept easy to find from the public area.</p>
                <div class="support-list">
                    <div class="support-item">
                        <strong>Create an account</strong>
                        <span>Register quickly, verify your details, and start booking with confidence.</span>
                    </div>
                    <div class="support-item">
                        <strong>Sign in support</strong>
                        <span>Return to your account to review bookings, tickets, and travel details.</span>
                    </div>
                    <div class="support-item">
                        <strong>Password recovery</strong>
                        <span>Reset your password if you cannot access your account.</span>
                    </div>
                </div>
                <div class="support-actions">
                    <a class="btn-primary-solid" href="<?php echo BASE_URL; ?>public/auth_router.php?action=login">Sign In</a>
                    <a class="btn-secondary" href="<?php echo BASE_URL; ?>public/auth_router.php?action=forgotPassword">Need Help</a>
                </div>
            </aside>
        </div>
    </section>

    <footer class="footer">
        <div class="footer-grid">
            <div class="footer-brand">
                <h2 style="font-size: 1.5rem; color: #0f1c33; margin-bottom: 12px;"><span style="font-size: 2rem;">🚌</span> Book Smarter, Travel Better</h2>
                <p>A cleaner public website for route search, booking, account access, and travel support with the same theme used across the rest of the project.</p>
            </div>

            <div class="footer-column">
                <strong>About Us</strong>
                <a href="#about-us">Platform overview</a>
                <a href="#why-choose-us">Why choose us</a>
                <p>Built to make online bus booking simpler and more professional.</p>
            </div>

            <div class="footer-column">
                <strong>Contact Us</strong>
                <p>Email: support@busticketing.local</p>
                <p>Phone: +92 300 0000000</p>
                <p>Hours: Mon-Sat, 9:00 AM to 8:00 PM</p>
            </div>

            <div class="footer-column">
                <strong>Why Choose Us</strong>
                <p>Clean interface</p>
                <p>Quick route search</p>
                <p>Reliable booking flow</p>
            </div>

            <div class="footer-column">
                <strong>Need Help</strong>
                <a href="<?php echo BASE_URL; ?>public/auth_router.php?action=login">Sign in support</a>
                <a href="<?php echo BASE_URL; ?>public/auth_router.php?action=register">Create passenger account</a>
                <a href="<?php echo BASE_URL; ?>public/auth_router.php?action=forgotPassword">Reset password</a>
            </div>
        </div>

        <div class="footer-bottom">
            <span>© <?php echo date('Y'); ?> Bus Ticketing System. All rights reserved.</span>
            <span>Designed with the same navbar style and visual theme used throughout the project.</span>
        </div>
    </footer>
</div>
</body>
</html>