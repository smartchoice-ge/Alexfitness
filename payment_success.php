<?php
// Redirect mobile app requests immediately before rendering any HTML
if (isset($_GET['source']) && $_GET['source'] === 'app') {
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $isAndroid = stripos($userAgent, 'android') !== false;

    $androidIntent = 'intent://payment/success#Intent;scheme=synergy;package=ge.synergy.gym;end';
    $fallbackLinks = [
        'synergy://payment/success',
        'synergygym://payment/success',
    ];
    $primaryLink = $isAndroid ? $androidIntent : $fallbackLinks[0];
    $fallbackJson = json_encode($fallbackLinks, JSON_UNESCAPED_SLASHES);

    echo '<!DOCTYPE html><html><head><meta charset="UTF-8">';
    echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
    echo '<title>Returning to Synergy App</title>';
    echo '<style>body{font-family:Inter,Arial,sans-serif;background:#101114;color:#fff;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0;padding:24px}.card{max-width:420px;width:100%;background:#181a1f;border:1px solid #2b2f38;border-radius:16px;padding:28px;text-align:center;box-shadow:0 20px 40px rgba(0,0,0,.25)}h1{font-size:22px;margin:0 0 12px;color:#c8e600}p{line-height:1.5;color:#d4d7dd;margin:0 0 18px}.btn{display:inline-block;background:#c8e600;color:#000;text-decoration:none;padding:12px 18px;border-radius:10px;font-weight:700;margin:6px 0}.btn-secondary{background:#2b2f38;color:#fff}.links{margin-top:16px;font-size:14px}.links a{color:#9fd3ff;word-break:break-all;display:block;margin-top:8px}</style>';
    echo '</head><body>';
    echo '<div class="card">';
    echo '<h1>Returning to Synergy App</h1>';
    echo '<p>If the app does not open automatically, tap the button below.</p>';
    echo '<a class="btn" href="' . htmlspecialchars($primaryLink, ENT_QUOTES, 'UTF-8') . '">Open App</a>';
    echo '<div class="links"><a class="btn btn-secondary" href="https://synergyfitness.ge/payment_success.php">Stay on Website</a></div>';
    echo '</div>';
    echo '<script>';
    echo 'const isAndroid=' . ($isAndroid ? 'true' : 'false') . ';';
    echo 'const primaryLink=' . json_encode($primaryLink, JSON_UNESCAPED_SLASHES) . ';';
    echo 'const fallbackLinks=' . $fallbackJson . ';';
    echo 'function tryOpen(link){ window.location.href = link; }';
    echo 'setTimeout(function(){ tryOpen(primaryLink); }, 150);';
    echo 'if(!isAndroid){ fallbackLinks.forEach(function(link, index){ setTimeout(function(){ tryOpen(link); }, 400 + (index * 250)); }); }';
    echo '</script>';
    echo '</body></html>';
    exit;
}
?>
<!DOCTYPE html>
<html lang="en" id="html-root">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful - Synergy Gym</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="js/language.js"></script>
    
    <link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon">
    <link rel="icon" href="img/favicon.ico" type="image/x-icon">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #000;
            color: #fff;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        
        #cont {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .success-card {
            background-color: #111;
            border: 1px solid #333;
            border-radius: 1rem;
            transition: all 0.3s ease;
            box-shadow: 0 0 30px rgba(255, 223, 6, 0.1);
            overflow-wrap: break-word;
            word-wrap: break-word;
            hyphens: auto;
        }

        .success-card:hover {
            border-color: #c8e600;
            box-shadow: 0 0 40px rgba(255, 223, 6, 0.2);
        }

        .brand-btn {
            background-color: #c8e600;
            color: #000000;
            transition: background-color 0.3s ease;
            border-radius: 0.5rem;
            font-weight: bold;
        }
        
        .brand-btn:hover {
            background-color: #1a73e8;
        }

        .success-icon {
            background: linear-gradient(135deg, #c8e600 0%, #1a73e8 100%);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .header-section {
            text-align: center;
            margin-bottom: 3rem;
        }

        .logo-img {
            height: 60px;
            width: auto;
            margin: 0 auto 2rem;
            display: block;
        }

        .checkmark-animation {
            animation: checkmark 1s ease-in-out;
        }

        @keyframes checkmark {
            0% { transform: scale(0) rotate(45deg); }
            50% { transform: scale(1.2) rotate(45deg); }
            100% { transform: scale(1) rotate(45deg); }
        }

        .fade-in {
            animation: fadeIn 0.8s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .celebration-confetti {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 1000;
        }

        .confetti-piece {
            position: absolute;
            width: 10px;
            height: 10px;
            background: #c8e600;
            animation: confetti-fall 3s linear infinite;
        }

        @keyframes confetti-fall {
            0% { transform: translateY(-100vh) rotate(0deg); opacity: 1; }
            100% { transform: translateY(100vh) rotate(360deg); opacity: 0; }
        }

        /* Custom brand color utility */
        .text-brand {
            color: #c8e600 !important;
        }
        
        .text-brand:hover {
            color: #1a73e8 !important;
        }
    </style>
    
    <!-- Facebook Pixel Code -->
    <script>
    !function(f,b,e,v,n,t,s)
    {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
    n.callMethod.apply(n,arguments):n.queue.push(arguments)};
    if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
    n.queue=[];t=b.createElement(e);t.async=!0;
    t.src=v;s=b.getElementsByTagName(e)[0];
    s.parentNode.insertBefore(t,s)}(window, document,'script',
    'https://connect.facebook.net/en_US/fbevents.js');
    
    fbq('init', ''); // Your actual Pixel ID
    fbq('track', 'PageView');
    
    // Track Purchase conversion with detailed parameters and content_ids
    fbq('track', 'Purchase', {
        value: 50.00, // Set actual membership price
        currency: 'GEL', // Georgian Lari currency code
        content_name: 'Synergy Gym Membership',
        content_category: 'Fitness',
        content_type: 'product',
        content_ids: ['gym_membership_' + Date.now()], // Unique content ID
        num_items: 1,
        predicted_ltv: 150.00, // Estimated lifetime value
        content_data: [{
            id: 'gym_membership_' + Date.now(),
            quantity: 1,
            item_price: 50.00
        }]
    });
    
    // Track Lead event for gym membership signup
    fbq('track', 'Lead', {
        content_name: 'Gym Membership Signup',
        content_category: 'Fitness',
        content_ids: ['lead_gym_membership'],
        value: 50.00,
        currency: 'GEL'
    });
    
    // Track CompleteRegistration event
    fbq('track', 'CompleteRegistration', {
        content_name: 'Gym Membership Registration',
        content_category: 'Fitness',
        content_ids: ['registration_gym_membership'],
        value: 50.00,
        currency: 'GEL',
        status: 'completed'
    });
    
    // Custom event for gym membership purchase with enhanced tracking
    fbq('trackCustom', 'GymMembershipPurchase', {
        membership_type: 'Standard',
        payment_method: 'Card',
        gym_location: 'Didi Digomi',
        content_ids: ['custom_gym_membership_' + Date.now()],
        value: 50.00,
        currency: 'GEL'
    });
    </script>
    <noscript><img height="1" width="1" style="display:none"
    src="https://www.facebook.com/tr?id=&ev=PageView&noscript=1"
    /></noscript>
    <!-- End Facebook Pixel Code -->
</head>

<body class="bg-black text-white">
    <div id="cont" class="flex-grow flex flex-col">
        <!-- Celebration Confetti -->
        <div class="celebration-confetti" id="confetti"></div>
        
        <div class="container mx-auto px-4 py-8 flex items-center justify-center min-h-screen">
            <div class="success-card p-6 md:p-8 lg:p-12 max-w-2xl w-full fade-in overflow-hidden">
                
                <!-- Header Section -->
                <div class="header-section text-center">
                    <img src="img/logo.png" alt="Synergy Gym" class="logo-img" onerror="this.onerror=null; this.src='https://placehold.co/120x50/ffdf06/000000?text=Synergy';">
                    
                    <div class="success-icon inline-flex items-center justify-center w-20 h-20 md:w-24 md:h-24 rounded-full mb-6 checkmark-animation">
                        <i class="fas fa-check text-black text-3xl md:text-4xl"></i>
                    </div>
                    
                    <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold mb-4 text-white text-center break-words" name="key_payment_success">
                        Payment Successful!
                    </h1>
                    <p class="text-lg md:text-xl text-gray-300 mb-8 text-center break-words" name="key_payment_success_message">
                        Your payment was completed successfully. Thank you for choosing Synergy Gym! Please note that membership activation may take 5-10 minutes.
                    </p>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <button onclick="window.location.href='index.php'" class="brand-btn px-8 py-3 rounded-lg text-lg font-semibold transition-all duration-300 hover:scale-105">
                        <i class="fas fa-home mr-2"></i>
                        <span name="key_back_to_home">Back to Home</span>
                    </button>
                    <button onclick="window.location.href='user_profile/profilepage.php'" class="bg-gray-700 hover:bg-gray-600 text-white px-8 py-3 rounded-lg text-lg font-semibold transition-all duration-300 hover:scale-105">
                        <i class="fas fa-user mr-2"></i>
                        <span name="key_my_profile">My Profile</span>
                    </button>
                </div>

                <!-- Contact Info -->
                <div class="text-center mt-8 pt-6 border-t border-gray-700">
                    <p class="text-gray-400 text-sm mb-2" name="key_contact_questions">
                        If you have any questions, contact us:
                    </p>
                    <div class="flex justify-center flex-wrap gap-4 text-sm mb-4">
                        <a href="tel:+995-XXX-XXX-XXX" class="text-brand hover:text-yellow-300 transition-colors">
                            <i class="fas fa-phone mr-1"></i>
                            +995 551 195 819
                        </a>
                        <a href="mailto:info@synergy-gym.ge" class="text-brand hover:text-yellow-300 transition-colors">
                            <i class="fas fa-envelope mr-1"></i>
                            info@synergy-gym.ge
                        </a>
                        <a href="https://wa.me/+995-XXX-XXX-XXX" class="text-brand hover:text-yellow-300 transition-colors" target="_blank">
                            <i class="fab fa-whatsapp mr-1"></i>
                            WhatsApp
                        </a>
                    </div>
                    
                    <!-- Social Media Links -->
                    <div class="flex justify-center space-x-4 mt-4">
                        <a href="https://www.facebook.com/SynergyGymTbilisi" target="_blank" class="text-gray-400 hover:text-blue-500 transition-colors text-2xl">
                            <i class="fab fa-facebook"></i>
                        </a>
                        <a href="https://www.instagram.com/synergy_gym_tbilisi/" target="_blank" class="text-gray-400 hover:text-pink-500 transition-colors text-2xl">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="https://wa.me/+995-XXX-XXX-XXX" target="_blank" class="text-gray-400 hover:text-green-500 transition-colors text-2xl">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Language initialization - make page translatable
        document.addEventListener('DOMContentLoaded', function() {
            // Check if language parameter is passed in URL
            const urlParams = new URLSearchParams(window.location.search);
            const langParam = urlParams.get('lang');
            
            // If language parameter exists, set it in localStorage
            if (langParam && (langParam === 'en' || langParam === 'ka')) {
                window.localStorage.setItem('ActiveLanguage', langParam);
            }
            
            // Get current language and update HTML lang attribute
            const currentLang = window.localStorage.getItem('ActiveLanguage') || 'en';
            document.getElementById('html-root').setAttribute('lang', currentLang);
            
            // Initialize language system with a slight delay to ensure DOM is ready
            setTimeout(function() {
                if (typeof ChangeData === 'function') {
                    ChangeData();
                }
            }, 100);
        });
        
        // Set current date and generate transaction ID
        document.addEventListener('DOMContentLoaded', function() {
            const now = new Date();
            const dateStr = now.toLocaleDateString('ka-GE', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
            document.getElementById('payment-date').textContent = dateStr;
            
            // Generate random transaction ID
            const transactionId = 'TXN' + Math.random().toString(36).substr(2, 9).toUpperCase();
            document.getElementById('transaction-id').textContent = transactionId;
        });

        // Create confetti animation
        function createConfetti() {
            const confettiContainer = document.getElementById('confetti');
            const colors = ['#c8e600', '#1a73e8', '#ffffff', '#ffd700'];
            
            for (let i = 0; i < 50; i++) {
                const confetti = document.createElement('div');
                confetti.className = 'confetti-piece';
                confetti.style.left = Math.random() * 100 + '%';
                confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
                confetti.style.animationDelay = Math.random() * 3 + 's';
                confetti.style.animationDuration = (Math.random() * 3 + 2) + 's';
                confettiContainer.appendChild(confetti);
            }
            
            // Remove confetti after animation
            setTimeout(() => {
                confettiContainer.innerHTML = '';
            }, 6000);
        }

        // Start confetti animation on page load
        window.addEventListener('load', function() {
            setTimeout(createConfetti, 500);
        });
    </script>
</body>
</html>