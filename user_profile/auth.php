<?php
session_start();
// Make sure this path is correct for your server setup.
include '../mssql_connection.php'; 

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Build all phone number variants to match any storage format in DB
    $phone_raw = ltrim($username, '+');
    if (str_starts_with($phone_raw, '995')) {
        $phone_with_995    = $phone_raw;
        $phone_without_995 = substr($phone_raw, 3);
    } else {
        $phone_without_995 = $phone_raw;
        $phone_with_995    = '995' . $phone_raw;
    }
    $phone_plus = '+' . $phone_with_995;

    // Match any of the three formats stored in the database
    $sql = "SELECT IdNumber, Phone FROM Clients WHERE Phone IN (?, ?, ?) AND IdNumber = ?";
    $params = array($phone_without_995, $phone_with_995, $phone_plus, $password);

    // Ensure the database connection is valid before querying
    if ($mssqlconn === false) {
        // Generic error for connection failure
        $error_message = "Could not connect to the database.";
    } else {
        $stmt = sqlsrv_query($mssqlconn, $sql, $params);

        if ($stmt === false) {
            // Generic error for query failure. Avoid showing detailed SQL errors to users.
            // error_log(print_r(sqlsrv_errors(), true)); // Log detailed error for your records
            $error_message = "An error occurred. Please try again later.";
        } else {
            if (sqlsrv_has_rows($stmt)) {
                // Authentication successful
                $_SESSION['loggedin'] = true;
                $_SESSION['username'] = $phone_without_995;
                header("Location: profilepage.php");
                exit();
            } else {
                // Authentication failed
                $error_message = "Invalid username or password.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ka">
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="title" content="Sign In - Alex Fitness">
    <meta name="description" content="Access your Alex Fitness account. Sign in to manage your membership, book classes, and track your progress.">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="shortcut icon" href="/img/favicon.ico" type="image/x-icon">
    <link rel="icon" href="/img/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <title>Sign In | Alex Fitness</title>
    <script src="/js/language.js"></script>

    <style>
    /* ── Navbar ── */
    .site-navbar{position:fixed;top:0;left:0;width:100%;z-index:9999;background:rgba(8,14,10,0.96);backdrop-filter:blur(14px);-webkit-backdrop-filter:blur(14px);border-bottom:1px solid rgba(34,197,94,0.15);box-shadow:0 2px 20px rgba(0,0,0,.5)}
    .site-navbar-inner{display:flex;align-items:center;height:82px;padding:0 24px;max-width:1400px;margin:0 auto;position:relative}
    .navbar-logo-link{flex-shrink:0;display:flex;align-items:center;text-decoration:none}
    .navbar-logo-img{height:70px;width:auto;display:block;filter:drop-shadow(0 2px 10px rgba(34,197,94,.35));transition:filter .25s,transform .25s}
    .navbar-logo-img:hover{filter:drop-shadow(0 4px 18px rgba(34,197,94,.60));transform:scale(1.04)}
    .navbar-desktop-links{position:absolute;left:50%;transform:translateX(-50%);display:flex;align-items:center;gap:8px}
    @keyframes nav-btn-shimmer{0%{background-position:200% center}100%{background-position:-200% center}}
    .navbar-register{background:linear-gradient(90deg,#16a34a 0%,#4ade80 40%,#22c55e 55%,#4ade80 70%,#16a34a 100%);background-size:250% auto;color:#000!important;font-size:.92rem;font-weight:700;padding:9px 24px;border-radius:6px;border:1px solid rgba(74,222,128,.70);text-decoration:none;letter-spacing:.8px;text-transform:uppercase;white-space:nowrap;box-shadow:0 4px 18px rgba(74,222,128,.30);animation:nav-btn-shimmer 3.5s linear infinite}
    .navbar-right-group{flex-shrink:0;display:flex;align-items:center;gap:12px;margin-left:auto}
    .navbar-lang-btn{color:#d0d0d0;font-size:.88rem;font-weight:600;padding:7px 28px;border-radius:6px;border:1px solid rgba(74,222,128,.38);background:rgba(74,222,128,.08);transition:all .2s;letter-spacing:.3px;white-space:nowrap;cursor:pointer}
    .navbar-lang-btn:hover{color:#4ade80;border-color:#4ade80;background:rgba(74,222,128,.16)}
    .navbar-hamburger{display:none;flex-direction:column;justify-content:center;gap:5px;width:36px;height:36px;padding:0;background:none;border:none;cursor:pointer}
    .navbar-hamburger span{display:block;width:24px;height:2px;background:#c8c8c8;border-radius:2px;transition:background .2s}
    .navbar-hamburger:hover span{background:#22c55e}
    .navbar-mobile-menu{display:none;flex-direction:column;padding:12px 20px 18px;border-top:1px solid rgba(34,197,94,.12);background:rgba(6,12,8,.98);gap:4px}
    .navbar-mobile-menu.open{display:flex}
    .nm-register{background:linear-gradient(90deg,#16a34a 0%,#4ade80 50%,#16a34a 100%);background-size:200% auto;color:#000!important;font-size:.95rem;font-weight:700;padding:12px 20px;border-radius:6px;border:1px solid rgba(74,222,128,.60);text-decoration:none;letter-spacing:.8px;text-transform:uppercase;text-align:center;margin:6px 0;box-shadow:0 4px 16px rgba(74,222,128,.28);animation:nav-btn-shimmer 3.5s linear infinite}
    .nm-lang{color:#a0a0a0;font-size:.88rem;font-weight:600;padding:10px 16px;border-radius:6px;border:1px solid rgba(34,197,94,.22);text-align:center;margin-top:6px;transition:color .2s,border-color .2s;cursor:pointer}
    .nm-lang:hover{color:#22c55e;border-color:#22c55e}
    @media(max-width:900px){.navbar-desktop-links{display:none}.navbar-hamburger{display:flex}}
    @media(min-width:901px){.navbar-mobile-menu{display:none!important}}

    /* ── Footer ── */
    .site-footer{background:#080e0a;border-top:2px solid rgba(74,222,128,.22);padding:22px 16px 12px;color:#a0a0a0;font-size:.85rem;overflow-x:hidden;width:100%;box-sizing:border-box}
    .footer-grid{display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;max-width:960px;margin:0 auto;padding:0 4px;text-align:center}
    @media(max-width:640px){.footer-grid{grid-template-columns:1fr;gap:20px}}
    .footer-col-title{color:#4ade80;font-size:.78rem;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;margin-bottom:14px}
    .footer-logo-img{height:52px;width:auto;margin:0 auto 10px;display:block;filter:drop-shadow(0 2px 8px rgba(74,222,128,.30))}
    .footer-brand-name{color:#ececec;font-size:1.1rem;font-weight:800;letter-spacing:.5px;margin-bottom:4px}
    .footer-tagline{font-size:.80rem;color:#606060}
    .footer-contact-item{display:flex;align-items:center;justify-content:center;gap:9px;margin-bottom:10px;font-size:.90rem}
    .footer-contact-item i{color:#4ade80;width:16px;flex-shrink:0}
    .footer-contact-item a{color:#d0d0d0;text-decoration:none;transition:color .2s}
    .footer-contact-item a:hover{color:#4ade80}
    .footer-social-row{display:flex;flex-wrap:wrap;justify-content:center;gap:8px}
    .footer-social-btn{display:inline-flex;align-items:center;justify-content:center;gap:7px;padding:9px 14px;border-radius:8px;font-size:.85rem;font-weight:700;text-decoration:none;border:1px solid transparent;transition:all .22s;white-space:nowrap}
    .footer-social-btn.fb{background:rgba(24,119,242,.12);border-color:rgba(24,119,242,.30);color:#60a5fa}
    .footer-social-btn.fb:hover{background:rgba(24,119,242,.22);border-color:#1877F2;color:#93c5fd;text-decoration:none}
    .footer-social-btn.ig{background:rgba(228,64,95,.10);border-color:rgba(228,64,95,.28);color:#f472b6}
    .footer-social-btn.ig:hover{background:rgba(228,64,95,.20);border-color:#E4405F;color:#fda4af;text-decoration:none}
    .footer-social-btn.tg{background:rgba(39,161,222,.12);border-color:rgba(39,161,222,.30);color:#38bdf8}
    .footer-social-btn.tg:hover{background:rgba(39,161,222,.22);border-color:#27A1DE;color:#7dd3fc;text-decoration:none}
    @media(max-width:400px){.footer-social-btn{padding:8px 10px;font-size:.80rem;gap:5px}.footer-social-row{gap:6px}}
    .footer-divider{border:none;border-top:1px solid rgba(74,222,128,.10);margin:36px auto 20px;max-width:960px}
    .footer-bottom{text-align:center;font-size:.80rem;color:#454545;padding:0 12px;display:flex;flex-wrap:wrap;justify-content:center;align-items:center;gap:4px 8px}
    .footer-bottom a{color:#555;text-decoration:none;transition:color .2s;white-space:nowrap}
    .footer-bottom a:hover{color:#4ade80}
    </style>

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #000;
            color: #fff;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            padding-top: 82px;
        }
        #cont {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }
        /* Styles for the form inputs on a dark background */
        .form-input {
            background-color: #1f1f1f; /* Dark gray background for inputs */
            border: 1px solid #444; /* Slightly lighter border */
            color: #fff; /* White text inside input */
            border-radius: 0.5rem;
        }
        .form-input::placeholder {
            color: #888; /* Light gray for placeholder text */
        }
        .form-input:focus {
            outline: none;
            border-color: #1e40af;
            box-shadow: 0 0 0 3px rgba(30, 64, 175, 0.22);
        }
        /* Use the Synergy palette for auth actions */
        .signin-btn {
            background-color: #166534; color: #ffffff;
            transition: background-color 0.3s ease, box-shadow 0.3s ease, transform 0.3s ease;
            border-radius: 0.5rem;
        }
        .signin-btn:hover {
            background-color: #1e40af;
            box-shadow: 0 10px 24px rgba(30, 64, 175, 0.28);
            transform: translateY(-1px);
        }
        /* Language toggle styling */
        .lang-btn {
            background-color: #166534; color: #ffffff;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease, box-shadow 0.3s ease, transform 0.3s ease;
        }
        .lang-btn:hover {
            background-color: #1e40af;
            box-shadow: 0 8px 20px rgba(30, 64, 175, 0.28);
            transform: translateY(-1px);
        }
        
        /* WhatsApp Button Styles */
        .whatsapp-float {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 100;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .whatsapp-button {
            display: flex;
            align-items: center;
            background: linear-gradient(135deg, #1e3a8a 0%, #0f2d52 100%);
            color: #ffffff;
            padding: 16px 28px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 700;
            font-size: 1.1rem;
            box-shadow: 0 10px 30px rgba(30, 64, 175, 0.5);
            border: 3px solid #ffffff;
            animation: whatsappPulse 2.5s ease-in-out infinite, whatsappBounce 4s ease-in-out infinite;
            position: relative;
            overflow: hidden;
            transform: scale(1.1);
            min-width: 160px;
            justify-content: center;
        }

        .whatsapp-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.6s ease;
        }

        .whatsapp-button:hover::before {
            left: 100%;
        }

        .whatsapp-button:hover {
            transform: translateY(-5px) scale(1.15);
            box-shadow: 0 15px 40px rgba(30, 64, 175, 0.7);
            text-decoration: none;
            color: #ffffff;
            animation: whatsappPulse 1.5s ease-in-out infinite, whatsappShake 0.5s ease-in-out;
        }

        .whatsapp-icon {
            width: 28px;
            height: 28px;
            margin-right: 12px;
            filter: brightness(1.2);
            animation: whatsappIconSpin 3s linear infinite;
        }

        .whatsapp-text {
            white-space: nowrap;
            font-family: 'Inter', sans-serif;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            font-weight: 700;
        }

        @keyframes whatsappPulse {
            0%, 100% {
                box-shadow: 0 10px 30px rgba(30, 64, 175, 0.5);
                transform: scale(1.1);
            }
            50% {
                box-shadow: 0 15px 40px rgba(30, 64, 175, 0.8);
                transform: scale(1.15);
            }
        }

        @keyframes whatsappBounce {
            0%, 100% {
                transform: translateY(0) scale(1.1);
            }
            25% {
                transform: translateY(-3px) scale(1.12);
            }
            50% {
                transform: translateY(0) scale(1.1);
            }
            75% {
                transform: translateY(-1px) scale(1.11);
            }
        }

        @keyframes whatsappShake {
            0%, 100% { transform: translateY(-5px) scale(1.15) rotate(0deg); }
            25% { transform: translateY(-5px) scale(1.15) rotate(-2deg); }
            75% { transform: translateY(-5px) scale(1.15) rotate(2deg); }
        }

        @keyframes whatsappIconSpin {
            0% { transform: rotate(0deg); }
            10% { transform: rotate(10deg); }
            20% { transform: rotate(-8deg); }
            30% { transform: rotate(6deg); }
            40% { transform: rotate(-4deg); }
            50% { transform: rotate(2deg); }
            60% { transform: rotate(-1deg); }
            70% { transform: rotate(0deg); }
            100% { transform: rotate(0deg); }
        }

        /* Mobile responsive adjustments for WhatsApp button */
        @media (max-width: 768px) {
            .whatsapp-float {
                bottom: 20px;
                right: 20px;
            }
            
            .whatsapp-button {
                padding: 14px 24px;
                font-size: 1rem;
                transform: scale(1.05);
                min-width: 140px;
            }
            
            .whatsapp-icon {
                width: 26px;
                height: 26px;
                margin-right: 10px;
            }
        }

        @media (max-width: 576px) {
            .whatsapp-button {
                padding: 12px 20px;
                font-size: 0.9rem;
                transform: scale(1);
                min-width: 120px;
            }
            
            .whatsapp-text {
                font-size: 0.8rem;
            }
            
            .whatsapp-icon {
                width: 24px;
                height: 24px;
                margin-right: 8px;
            }
        }

        @media (max-width: 400px) {
            .whatsapp-text {
                display: none; /* Hide text on very small screens, show only icon */
            }
            
            .whatsapp-button {
                width: 60px;
                height: 60px;
                border-radius: 50%;
                padding: 0;
                justify-content: center;
                min-width: auto;
                transform: scale(1.1);
            }
            
            .whatsapp-icon {
                margin-right: 0;
                width: 30px;
                height: 30px;
            }
        }
    </style>
</head>

<body>

<!-- Telegram Button -->
<a href="https://t.me/alex_fitness_kobuleti" class="whatsapp-float" target="_blank">
    <div class="whatsapp-button">
        <i class="fab fa-telegram-plane whatsapp-icon" style="font-size:26px;color:#fff;margin-right:10px;"></i>
        <span class="whatsapp-text" name="key_contact_whatsapp">Contact Us</span>
    </div>
</a>
    
<nav class="site-navbar" id="siteNavbar">
    <div class="site-navbar-inner">
        <a href="/" class="navbar-logo-link">
            <img src="/img/gym.png" alt="Alex Fit" class="navbar-logo-img"
                 onerror="this.onerror=null;this.src='https://placehold.co/55x55/0d1a11/22c55e?text=AF';">
        </a>
        <div class="navbar-desktop-links">
            <span class="navbar-register banner" style="cursor:default;" name="key_sign_in_button">key_sign_in_button</span>
        </div>
        <div class="navbar-right-group">
            <div class="navbar-lang-btn" onclick="SetLanguage()">
                <span name="key_lang">ქართული</span>
            </div>
            <button class="navbar-hamburger" id="navToggle"
                    onclick="document.getElementById('navMobileMenu').classList.toggle('open')">
                <span></span><span></span><span></span>
            </button>
        </div>
    </div>
    <div class="navbar-mobile-menu" id="navMobileMenu">
        <span class="nm-register banner" style="cursor:default;" name="key_sign_in_button">key_sign_in_button</span>
        <div class="nm-lang" onclick="SetLanguage()">
            <span name="key_lang">ქართული</span>
        </div>
    </div>
</nav>

<div id="cont">
    
    <!-- Sign In Form Section -->
    <div class="flex-grow flex items-center justify-center pb-12">
        <div class="container mx-auto px-4">
            <!-- The form container is now transparent -->
            <div class="max-w-sm w-full p-8 mx-auto">
                <div class="text-center mb-8">
                    <a href="/" class="inline-block bg-black p-3 rounded-lg hover:bg-gray-800 transition-colors duration-300">
                        <!-- Increased logo size -->
                        <img src="/img/gym.png" alt="Synergy Gym Logo" class="h-16 mx-auto" onerror="this.onerror=null; this.src='https://placehold.co/180x60/cccccc/000000?text=Synergy+Logo';">
                    </a>
                </div>

                <form action="" method="POST">
                    <?php if ($error_message): ?>
                        <div class="bg-red-500 text-white p-3 rounded-md mb-4 text-center">
                            <?php echo htmlspecialchars($error_message); ?>
                        </div>
                    <?php endif; ?>
                    <div class="mb-4">
                        <input type="text" id="username" name="username" class="w-full px-4 py-3 form-input" required data-translate-placeholder="key_email_placeholder">
                    </div>
                    <div class="mb-6">
                        <input type="password" id="password" name="password" class="w-full px-4 py-3 form-input" required data-translate-placeholder="key_password_placeholder">
                    </div>
                    <div>
                        <button type="submit" class="w-full signin-btn font-bold py-3 px-4 text-lg" name="key_sign_in_button">Sign In</button>
                    </div>
                </form>

                <!-- Forgot Password Section -->
                <div class="text-center mt-4">
                    <button type="button" id="forgotPasswordBtn" class="text-lime-400 hover:text-blue-400 underline text-sm" name="key_forgot_password">
                        Forgot Username/Password?
                    </button>
                </div>

            </div>
        </div>
    </div>
</div>

<footer class="site-footer">
    <div class="footer-grid">
        <div>
            <img src="/img/gym.png" alt="Alex Fit" class="footer-logo-img" onerror="this.style.display='none'">
            <div class="footer-brand-name">Alex Fit</div>
            <div class="footer-tagline" name="key_footer_tagline">Gym &amp; Boxing · Kobuleti</div>
        </div>
        <div>
            <div class="footer-col-title" name="key_contact">Contact</div>
            <div class="footer-contact-item">
                <i class="fas fa-phone-alt"></i>
                <a href="tel:+995599061572">+995 599 061 572</a>
            </div>
            <div class="footer-contact-item">
                <i class="fas fa-clock"></i>
                <span style="color:#d0d0d0;">09:30 – 00:00 &nbsp;<span style="color:#606060;font-size:.80rem;" name="key_every_day">Every Day</span></span>
            </div>
            <div class="footer-contact-item">
                <i class="fas fa-map-marker-alt"></i>
                <span style="color:#d0d0d0;" name="key_address">Shota Rustaveli 170-25, Kobuleti 6200</span>
            </div>
        </div>
        <div>
            <div class="footer-col-title" name="key_follow_us">Follow Us</div>
            <div class="footer-social-row">
                <a href="https://www.facebook.com/Alexfitnesskobulrti/" target="_blank" rel="noopener noreferrer" class="footer-social-btn fb">
                    <i class="fab fa-facebook-f"></i> Facebook
                </a>
                <a href="https://www.instagram.com/alex_fitness_kobuleti/" target="_blank" rel="noopener noreferrer" class="footer-social-btn ig">
                    <i class="fab fa-instagram"></i> Instagram
                </a>
                <a href="https://t.me/alex_fitness_kobuleti" target="_blank" rel="noopener noreferrer" class="footer-social-btn tg">
                    <i class="fab fa-telegram-plane"></i> Telegram
                </a>
            </div>
        </div>
    </div>
    <hr class="footer-divider">
    <div class="footer-bottom">
        © 2026 Alex Fit. All rights reserved.
        &nbsp;·&nbsp;<a href="/terms.php">Terms</a>
        &nbsp;·&nbsp;<a href="/privacy.php">Privacy Policy</a>
    </div>
</footer>

<!-- Forgot Password Modal -->
<div id="forgotPasswordModal" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50" style="display: none;">
    <div class="bg-gray-900 border-2 border-lime-400 rounded-lg p-8 max-w-md w-full mx-4">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold text-lime-400" name="key_recover_title">Recover Your Credentials</h2>
            <button id="closeModal" class="text-lime-400 hover:text-blue-400 text-2xl">&times;</button>
        </div>
        
        <form id="forgotPasswordForm">
            <div class="mb-6">
                <label for="recoveryEmail" class="block text-sm font-medium text-lime-400 mb-2" name="key_email_label">
                    Enter your email address:
                </label>
                <input type="email" id="recoveryEmail" name="email" 
                       class="w-full px-4 py-3 form-input" 
                       data-translate-placeholder="key_email_recovery_placeholder" required>
            </div>
            
            <button type="submit" class="w-full signin-btn font-bold py-3 px-4" name="key_send_credentials">
                Send Credentials to Email
            </button>
        </form>
        
        <div id="recoveryMessage" class="mt-4"></div>
    </div>
</div>

<!-- Embedded Language Script -->
<script>
// Page-specific translations (footer/nav keys handled by language.js + JSON)
const authT = {
    ka: {
        key_email_placeholder: 'ტელეფონის ნომერი (551195819)',
        key_password_placeholder: 'პირადი ნომერი ან პასპორტი',
        key_sign_in_button: 'შესვლა',
        key_forgot_password: 'დაგავიწყდათ მონაცემები?',
        key_recover_title: 'მონაცემების აღდგენა',
        key_email_label: 'შეიყვანეთ თქვენი ელ-ფოსტა:',
        key_send_credentials: 'გაგზავნა ელ-ფოსტაზე',
        key_email_recovery_placeholder: 'your.email@example.com',
    },
    en: {
        key_email_placeholder: 'Phone Number (like 551195819)',
        key_password_placeholder: 'Your ID or passport number',
        key_sign_in_button: 'Sign In',
        key_forgot_password: 'Forgot Username/Password?',
        key_recover_title: 'Recover Your Credentials',
        key_email_label: 'Enter your email address:',
        key_send_credentials: 'Send Credentials to Email',
        key_email_recovery_placeholder: 'your.email@example.com',
    },
    ru: {
        key_email_placeholder: 'Номер телефона (551195819)',
        key_password_placeholder: 'Личный номер или паспорт',
        key_sign_in_button: 'Войти',
        key_forgot_password: 'Забыли данные?',
        key_recover_title: 'Восстановление данных',
        key_email_label: 'Введите вашу эл. почту:',
        key_send_credentials: 'Отправить на эл. почту',
        key_email_recovery_placeholder: 'your.email@example.com',
    }
};

// Global — called by language.js dropdown; must be in global scope to be reachable
function onLangChange(lang) {
    const d = authT[lang];
    if (!d) return;
    document.querySelectorAll('[name^="key_"]').forEach(el => {
        const k = el.getAttribute('name');
        if (d[k] !== undefined) el.textContent = d[k];
    });
    document.querySelectorAll('[data-translate-placeholder]').forEach(el => {
        const k = el.getAttribute('data-translate-placeholder');
        if (d[k]) el.placeholder = d[k];
    });
}

document.addEventListener('DOMContentLoaded', () => {
    // Determine initial language
    const urlParams = new URLSearchParams(window.location.search);
    const urlLang = urlParams.get('lang');
    let initialLang;
    if (urlLang && ['ka','en','ru'].includes(urlLang)) {
        localStorage.setItem('ActiveLanguage', urlLang);
        initialLang = urlLang;
    } else {
        initialLang = localStorage.getItem('ActiveLanguage') || 'ka';
    }
    onLangChange(initialLang);
    
    // Forgot Password Modal functionality
    const forgotPasswordBtn = document.getElementById('forgotPasswordBtn');
    const forgotPasswordModal = document.getElementById('forgotPasswordModal');
    const closeModal = document.getElementById('closeModal');
    const forgotPasswordForm = document.getElementById('forgotPasswordForm');
    
    forgotPasswordBtn.addEventListener('click', () => {
        forgotPasswordModal.style.display = 'flex';
    });
    
    closeModal.addEventListener('click', () => {
        forgotPasswordModal.style.display = 'none';
    });
    
    // Close modal when clicking outside
    forgotPasswordModal.addEventListener('click', (e) => {
        if (e.target === forgotPasswordModal) {
            forgotPasswordModal.style.display = 'none';
        }
    });
    
    // Handle form submission
    forgotPasswordForm.addEventListener('submit', (e) => {
        e.preventDefault();
        
        const email = document.getElementById('recoveryEmail').value.trim();
        const messageDiv = document.getElementById('recoveryMessage');
        
        if (!email) {
            messageDiv.innerHTML = '<div class="bg-red-500 text-white p-3 rounded-md text-center">Please enter your email address</div>';
            return;
        }
        
        // Show loading message
        messageDiv.innerHTML = '<div class="bg-blue-500 text-white p-3 rounded-md text-center">Sending credentials...</div>';
        
        const formData = new FormData();
        formData.append('email', email);
        
        fetch('../recover_credentials.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            const alertClass = data.status === 'success' ? 
                'bg-green-500 text-white p-3 rounded-md text-center' : 
                'bg-red-500 text-white p-3 rounded-md text-center';
            
            messageDiv.innerHTML = `<div class="${alertClass}">${data.message}</div>`;
            
            if (data.status === 'success') {
                // Clear form
                document.getElementById('recoveryEmail').value = '';
                
                // Close modal after 3 seconds
                setTimeout(() => {
                    forgotPasswordModal.style.display = 'none';
                }, 3000);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            messageDiv.innerHTML = '<div class="bg-red-500 text-white p-3 rounded-md text-center">An error occurred. Please try again.</div>';
        });
    });
});
</script>

</body>
</html>
