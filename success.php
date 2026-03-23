<?php
// Include database connections
include_once 'mssql_connection.php';
include_once 'mssql_packages_payments_helper.php';

// Fetch all packages from MSSQL PackagesWebsite and sort in PHP
$allPackages = getPackagesWebsite('id ASC');

// Sort packages based on name pattern
usort($allPackages, function($a, $b) {
    $getOrder = function($pkg) {
        $name = $pkg['name_eng'] ?? '';
        if (stripos($name, 'Student') !== false && stripos($name, '1 Month') !== false) return 1;
        if (stripos($name, 'Student') !== false && stripos($name, '2 Month') !== false) return 2;
        if (stripos($name, 'Student') !== false && stripos($name, '6 Month') !== false) return 3;
        if (stripos($name, 'Student') !== false && stripos($name, '12 Month') !== false) return 4;
        if (stripos($name, '1 Month') !== false && stripos($name, 'Student') === false && stripos($name, 'Group') === false) return 5;
        if (stripos($name, '2 Month') !== false && stripos($name, 'Student') === false) return 6;
        if (stripos($name, '6 Month') !== false && stripos($name, 'Student') === false) return 7;
        if (stripos($name, '12 Month') !== false && stripos($name, 'Student') === false) return 8;
        if (stripos($name, 'Group') !== false || stripos($name, 'group') !== false) return 9;
        return 10;
    };
    return $getOrder($a) - $getOrder($b);
});

$packages = $allPackages;
?>
<!DOCTYPE html>
<html lang="ka">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>პარტნიორობის არჩევა - Synergy Gym</title>
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

        .payment-card {
            background-color: #111;
            border: 1px solid #333;
            border-radius: 1rem;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .payment-card:hover {
            border-color: #c8e600;
            box-shadow: 0 0 20px rgba(255, 223, 6, 0.1);
        }

        .payment-card-content {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .payment-features {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            min-height: 120px;
        }

        .payment-btn {
            background-color: #c8e600;
            color: #000000;
            transition: all 0.3s ease;
            border-radius: 0.5rem;
            font-weight: 600;
        }

        .payment-btn:hover {
            background-color: #1a73e8;
            transform: translateY(-1px);
        }

        .membership-select {
            background-color: #1f1f1f;
            border: 1px solid #444;
            color: #fff;
            border-radius: 0.5rem;
            padding: 0.75rem;
            font-weight: 600;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='white' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6,9 12,15 18,9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 1em;
            padding-right: 2.5rem;
        }

        .membership-select:focus {
            outline: none;
            border-color: #c8e600;
            box-shadow: 0 0 0 3px rgba(255, 223, 6, 0.2);
        }

        .membership-select option {
            background-color: #1f1f1f;
            color: #fff;
            padding: 0.5rem;
        }

        /* iOS Safari specific fixes */
        @supports (-webkit-touch-callout: none) {
            .membership-select {
                -webkit-appearance: none;
                background-color: #1f1f1f !important;
                color: #fff !important;
                font-size: 16px; /* Prevents zoom on iOS */
                border-radius: 0.5rem;
                -webkit-border-radius: 0.5rem;
            }
            
            .membership-select option {
                background-color: #1f1f1f !important;
                color: #fff !important;
                -webkit-appearance: none;
            }
        }

        /* iOS 15+ specific fixes */
        @media screen and (-webkit-min-device-pixel-ratio: 2) {
            .membership-select {
                font-size: 16px !important; /* Prevents zoom */
                -webkit-tap-highlight-color: transparent;
            }
        }

        .success-icon {
            background-color: #22c55e;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { 
                opacity: 1; 
                transform: scale(1);
            }
            50% { 
                opacity: 0.8; 
                transform: scale(1.05);
            }
        }

        .lang-btn {
            background-color: #c8e600;
            color: #000;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
            position: fixed !important;
            top: 1rem !important;
            right: 1rem !important;
            z-index: 99999 !important;
            border: none;
            cursor: pointer;
            font-size: 0.875rem;
            min-width: auto;
            white-space: nowrap;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
            transform: translateZ(0);
            will-change: transform;
            opacity: 1;
        }

        .lang-btn.scrolling {
            opacity: 0;
            pointer-events: none;
        }

        .lang-btn:hover {
            background-color: #1a73e8;
            transform: translateY(-1px);
        }

        /* Mobile optimizations */
        @media (max-width: 768px) {
            body {
                position: relative;
                overflow-x: hidden;
            }
            .lang-btn-container {
                top: 0.75rem !important;
                right: 0.75rem !important;
                gap: 0.375rem;
                position: fixed !important;
                z-index: 99999 !important;
            }
            .lang-btn {
                padding: 0.4rem 0.8rem;
                font-size: 0.8rem;
                border-radius: 0.4rem;
            }
        }

        @media (max-width: 480px) {
            .lang-btn-container {
                top: 0.5rem !important;
                right: 0.5rem !important;
                gap: 0.25rem;
                position: fixed !important;
                z-index: 99999 !important;
            }
            .lang-btn {
                padding: 0.35rem 0.7rem;
                font-size: 0.75rem;
            }
        }

        .support-section {
            background-color: #111;
            border: 1px solid #333;
            border-radius: 1rem;
        }

        .feature-item {
            color: #ccc;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.875rem;
        }

        .feature-item i {
            margin-right: 0.5rem;
        }

        .contact-link {
            color: #c8e600;
            transition: color 0.3s ease;
        }

        .contact-link:hover {
            color: #1a73e8;
        }

        .header-section {
            text-align: center;
            margin-bottom: 2rem;
        }

        .logo-img {
            max-width: 120px;
            height: auto;
            margin: 0 auto;
            transition: opacity 0.3s ease;
        }

        .logo-link:hover .logo-img {
            opacity: 0.8;
        }

        .payment-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }

        @media (min-width: 768px) {
            .payment-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        .payment-option-header {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .payment-icon {
            width: 4rem;
            height: 4rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
        }

        .card-icon {
            background-color: rgba(59, 130, 246, 0.1);
        }

        .whatsapp-icon {
            background-color: rgba(34, 197, 94, 0.1);
        }

        .payment-methods {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 1rem;
            margin: 1rem 0;
            flex-wrap: wrap;
        }

        .payment-method-icon {
            width: 4rem;
            height: 2.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 0.5rem;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }

        .payment-method-icon:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }

        .payment-method-icon img {
            max-width: 2.5rem;
            max-height: 1.5rem;
            object-fit: contain;
        }

        .payment-method-text {
            font-size: 0.75rem;
            color: #9CA3AF;
            text-align: center;
            margin-bottom: 0.5rem;
        }

        /* WhatsApp Button Styles */
        .whatsapp-btn {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            z-index: 1000;
            background: linear-gradient(135deg, #25D366 0%, #128C7E 100%);
            color: white;
            border: none;
            border-radius: 50%;
            width: 70px;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            box-shadow: 0 8px 32px rgba(37, 211, 102, 0.3);
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            animation: pulse 2s infinite;
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 255, 255, 0.1);
        }

        .whatsapp-btn:hover {
            transform: translateY(-5px) scale(1.1);
            box-shadow: 0 15px 40px rgba(37, 211, 102, 0.4);
            background: linear-gradient(135deg, #2EE76F 0%, #15A085 100%);
        }

        .whatsapp-btn:active {
            transform: translateY(-2px) scale(1.05);
        }

        .whatsapp-btn i {
            animation: wiggle 3s ease-in-out infinite;
        }

        .whatsapp-text {
            position: absolute;
            right: 85px;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(0, 0, 0, 0.9);
            color: white;
            padding: 8px 16px;
            border-radius: 25px;
            font-size: 0.875rem;
            font-weight: 600;
            white-space: nowrap;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }

        .whatsapp-btn:hover .whatsapp-text {
            opacity: 1;
            visibility: visible;
            transform: translateY(-50%) translateX(-10px);
        }

        @keyframes pulse {
            0%, 100% {
                box-shadow: 0 8px 32px rgba(37, 211, 102, 0.3);
            }
            50% {
                box-shadow: 0 8px 32px rgba(37, 211, 102, 0.6), 0 0 0 10px rgba(37, 211, 102, 0.1);
            }
        }

        @keyframes wiggle {
            0%, 100% { transform: rotate(0deg); }
            10% { transform: rotate(-10deg); }
            20% { transform: rotate(10deg); }
            30% { transform: rotate(-10deg); }
            40% { transform: rotate(10deg); }
            50% { transform: rotate(0deg); }
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {
                transform: translateY(0);
            }
            40% {
                transform: translateY(-10px);
            }
            60% {
                transform: translateY(-5px);
            }
        }

        /* Mobile responsive adjustments */
        @media (max-width: 768px) {
            .whatsapp-btn {
                width: 75px;
                height: 75px;
                font-size: 1.8rem;
                bottom: 1.5rem;
                right: 1.5rem;
            }
            
            .whatsapp-text {
                display: none !important;
                opacity: 0 !important;
                visibility: hidden !important;
            }
            
            /* Mobile social media buttons */
            .support-section a[href*="facebook"],
            .support-section a[href*="instagram"] {
                padding: 12px 20px !important;
                font-size: 0.9rem !important;
                min-height: 44px; /* iOS recommended touch target size */
                touch-action: manipulation;
                -webkit-tap-highlight-color: transparent;
            }
            
            .support-section .flex.justify-center.gap-4 {
                flex-direction: column;
                gap: 12px !important;
                align-items: center;
            }
            
            .support-section .flex.justify-center.gap-4 a {
                width: 100%;
                max-width: 280px;
                justify-content: center;
            }
        }

        @media (max-width: 480px) {
            .whatsapp-btn {
                width: 65px;
                height: 65px;
                font-size: 1.6rem;
                bottom: 1rem;
                right: 1rem;
            }
            
            .whatsapp-text {
                display: none !important;
                opacity: 0 !important;
                visibility: hidden !important;
            }
            
            /* Smaller mobile social buttons */
            .support-section a[href*="facebook"],
            .support-section a[href*="instagram"] {
                padding: 14px 24px !important;
                font-size: 1rem !important;
                font-weight: 600 !important;
            }
        }
    </style>
    
    <!-- Meta Pixel Code -->
    <script>
    !function(f,b,e,v,n,t,s)
    {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
    n.callMethod.apply(n,arguments):n.queue.push(arguments)};
    if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
    n.queue=[];t=b.createElement(e);t.async=!0;
    t.src=v;s=b.getElementsByTagName(e)[0];
    s.parentNode.insertBefore(t,s)}(window, document,'script',
    'https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', '');
    fbq('track', 'PageView');
    fbq('track', 'ViewContent', {
        content_name: 'Gym Membership Packages',
        content_category: 'Fitness'
    });
    </script>
    <noscript><img height="1" width="1" style="display:none"
    src="https://www.facebook.com/tr?id=&ev=PageView&noscript=1"
    /></noscript>
    <!-- End Meta Pixel Code -->
</head>
<body>
    <!-- Language Button -->
    <button class="lang-btn" onclick="SetLanguage()">
        <span name="key_lang">ქართული</span>
    </button>

    <div id="cont">
        <div class="container mx-auto px-4 py-8 max-w-4xl">
            <!-- Header Section -->
            <div class="header-section">
                <a href="/" class="logo-link block mb-6">
                    <img src="img/logo.png" alt="Synergy Gym" class="logo-img" onerror="this.onerror=null; this.src='https://placehold.co/120x50/ffdf06/000000?text=Synergy';">
                </a>
                
                <div class="success-icon inline-flex items-center justify-center w-20 h-20 rounded-full mb-6 mx-auto">
                    <i class="fas fa-check text-white text-3xl"></i>
                </div>
                
                <h1 class="text-4xl font-bold mb-4" name="key_registration_success">
                    რეგისტრაცია წარმატებით დასრულდა!
                </h1>
                <p class="text-xl text-gray-300 mb-8" name="key_choose_payment">
                    აირჩიეთ გადახდის მეთოდი თქვენი საფიტნესო წევრობისთვის
                </p>
            </div>

            <!-- Membership Selection -->
            <div class="mb-8 text-center">
                <select id="membershipSelect" class="membership-select text-center mx-auto w-full max-w-md" onchange="enablePaymentOptions()" aria-label="Select membership type">
                    <option value="" disabled selected name="key_select_membership_placeholder">აირჩიეთ წევრობის ტიპი...</option>
                    <?php foreach ($packages as $package): ?>
                        <option value="<?= htmlspecialchars($package['price']) ?>" 
                                data-package-id="<?= htmlspecialchars($package['package_id']) ?>"
                                data-description="<?= htmlspecialchars($package['name_geo']) ?>"
                                data-description-en="<?= htmlspecialchars($package['name_eng']) ?>"
                                data-description-ka="<?= htmlspecialchars($package['name_geo']) ?>"> 
                                <?= htmlspecialchars($package['name_geo']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Payment Options -->
            <div id="paymentSection" class="payment-grid mb-8 opacity-50 pointer-events-none transition-all duration-500">
                <!-- Card Payment -->
                <div id="cardPaymentDiv" class="payment-card p-6 cursor-pointer" onclick="payByCard()">
                    <div class="payment-option-header">
                        <div class="payment-icon card-icon">
                            <i class="fas fa-credit-card text-blue-500 text-xl"></i>
                        </div>
                        <h2 class="text-xl font-bold text-white mb-2" name="key_pay_by_card">
                            Pay by Card
                        </h2>
                        <p class="payment-method-text" name="key_card_payment_desc">
                            Apple pay, Google pay, Visa, Mastercard
                        </p>
                        
                        <!-- Payment Method Icons -->
                        <div class="payment-methods">
                            <div class="payment-method-icon">
                                <svg viewBox="0 0 48 32" width="42" height="28" fill="none">
                                    <!-- Apple Pay Icon - Official Style -->
                                    <rect width="48" height="32" rx="4" fill="#000000"/>
                                    <!-- Apple logo symbol -->
                                    <path d="M16.5 8.5c0.8-1 1.3-2.4 1.2-3.8-1.1 0.1-2.5 0.8-3.3 1.7-0.7 0.8-1.3 2.1-1.2 3.3 1.3 0.1 2.6-0.7 3.3-1.2z" fill="white"/>
                                    <path d="M17.8 9.7c-1.8-0.1-3.3 1.0-4.2 1.0-0.9 0-2.2-1.0-3.7-0.9-1.9 0.1-3.6 1.1-4.6 2.8-2.0 3.4-0.5 8.5 1.4 11.3 0.9 1.3 2.0 2.8 3.5 2.7 1.4-0.1 1.9-0.9 3.6-0.9 1.7 0 2.1 0.9 3.6 0.9 1.5-0.1 2.5-1.3 3.4-2.6 1.1-1.5 1.5-3.0 1.5-3.1-0.1 0-2.9-1.1-2.9-4.4 0-2.9 2.4-4.3 2.5-4.4-1.4-2.0-3.5-2.2-4.1-2.4z" fill="white"/>
                                    <!-- Pay text -->
                                    <text x="28" y="18" fill="white" font-family="Arial, sans-serif" font-size="10" font-weight="400">Pay</text>
                                </svg>
                            </div>
                            <div class="payment-method-icon">
                                <svg viewBox="0 0 48 32" width="42" height="28" fill="none">
                                    <!-- Google Pay Icon - Clean Official Style -->
                                    <rect width="48" height="32" rx="4" fill="white"/>
                                    <!-- Google "G" logo -->
                                    <g transform="translate(6,8)">
                                        <path d="M17.64 9.2c0-0.637-0.057-1.251-0.164-1.84H9v3.481h4.844c-0.209 1.125-0.843 2.078-1.796 2.717v2.258h2.908c1.702-1.567 2.684-3.874 2.684-6.615z" fill="#4285F4"/>
                                        <path d="M9 18c2.43 0 4.467-0.806 5.956-2.18l-2.908-2.259c-0.806 0.54-1.837 0.86-3.048 0.86-2.344 0-4.328-1.584-5.036-3.711H0.957v2.332C2.438 15.983 5.482 18 9 18z" fill="#34A853"/>
                                        <path d="M3.964 10.71c-0.18-0.54-0.282-1.117-0.282-1.71s0.102-1.17 0.282-1.71V4.958H0.957C0.347 6.173 0 7.548 0 9s0.348 2.827 0.957 4.042l3.007-2.332z" fill="#FBBC05"/>
                                        <path d="M9 3.58c1.321 0 2.508 0.454 3.44 1.345l2.582-2.58C13.463 0.891 11.426 0 9 0 5.482 0 2.438 2.017 0.957 4.958L3.964 7.29C4.672 5.163 6.656 3.58 9 3.58z" fill="#EA4335"/>
                                    </g>
                                    <!-- "Pay" text -->
                                    <text x="30" y="18" fill="#5F6368" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif" font-size="7" font-weight="400">Pay</text>
                                </svg>
                            </div>
                            <div class="payment-method-icon">
                                <svg viewBox="0 0 48 32" width="42" height="28" fill="none">
                                    <!-- Visa Icon - Larger -->
                                    <rect width="48" height="32" rx="4" fill="#1A1F71"/>
                                    <text x="24" y="20" text-anchor="middle" fill="white" font-family="Arial, sans-serif" font-weight="bold" font-size="14">VISA</text>
                                </svg>
                            </div>
                            <div class="payment-method-icon">
                                <svg viewBox="0 0 48 32" width="42" height="28" fill="none">
                                    <!-- Mastercard Icon - Larger -->
                                    <rect width="48" height="32" rx="4" fill="white"/>
                                    <g transform="translate(6,6)">
                                        <circle cx="14" cy="10" r="11" fill="#EB001B"/>
                                        <circle cx="22" cy="10" r="11" fill="#FF5F00"/>
                                        <path d="M18 3.5c2.2 1.6 3.6 4.2 3.6 7.2s-1.4 5.6-3.6 7.2c-2.2-1.6-3.6-4.2-3.6-7.2s1.4-5.6 3.6-7.2z" fill="#FF5F00"/>
                                    </g>
                                </svg>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-6 space-y-3">
                        <div class="feature-item">
                            <i class="fas fa-shield-alt text-green-500"></i>
                            <span name="key_secure_payment">Secure Payment</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-bolt text-yellow-500"></i>
                            <span name="key_instant_activation">Instant Activation</span>
                        </div>
                    </div>
                    
                    <button id="cardPayBtn" class="payment-btn w-full py-3 px-6 text-lg font-bold disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                        <i class="fas fa-credit-card mr-2"></i>
                        <span name="key_continue_with_card">Continue with Card</span>
                    </button>
                </div>

                <!-- Bank Transfer -->
                <div class="payment-card p-6">
                    <div class="payment-option-header">
                        <div class="payment-icon whatsapp-icon">
                            <i class="fab fa-whatsapp text-green-500 text-xl"></i>
                        </div>
                        <h2 class="text-xl font-bold text-white mb-2" name="key_pay_by_transfer">
                            ტრანსფერით გადახდა
                        </h2>
                        <p class="text-gray-400 text-center" name="key_transfer_payment_desc">
                            გადაიხადეთ ბანკის ტრანსფერით და დაგვიკავშირდით WhatsApp-ზე
                        </p>
                        
                        <!-- Bank Transfer Icons -->
                        <div class="payment-methods">
                            <div class="payment-method-icon">
                                <img src="img/BGEO.L.png" alt="Bank of Georgia" 
                                     onerror="this.onerror=null; this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div style="display:none; width:100%; height:100%; background:#FF6B35; border-radius:4px; align-items:center; justify-content:center; font-size:8px; color:white; font-weight:bold;">BOG</div>
                            </div>
                            <div class="payment-method-icon">
                                <img src="img/pngegg (1).png" alt="TBC Bank" 
                                     onerror="this.onerror=null; this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div style="display:none; width:100%; height:100%; background:#FF6600; border-radius:4px; align-items:center; justify-content:center; font-size:10px; color:white; font-weight:bold;">TBC</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-6 space-y-3">
                        <div class="feature-item">
                            <i class="fab fa-whatsapp text-green-500"></i>
                            <span name="key_whatsapp_support">WhatsApp მხარდაჭერა</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-user-tie text-blue-500"></i>
                            <span name="key_personal_assistance">პერსონალური დახმარება</span>
                        </div>
                    </div>
                    
                    <button id="transferPayBtn" onclick="payByTransfer()" class="payment-btn w-full py-3 px-6 text-lg font-bold disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                        <i class="fab fa-whatsapp mr-2"></i>
                        <span name="key_continue_with_transfer">ტრანსფერით გაგრძელება</span>
                    </button>
                </div>
            </div>

            <!-- Support Section -->
            <div class="support-section p-6">
                <h3 class="text-xl font-bold text-white mb-4 text-center" name="key_need_help">
                    გჭირდებათ დახმარება?
                </h3>
                <p class="text-gray-400 mb-6 text-center" name="key_contact_support">
                    დაგვიკავშირდით ნებისმიერ დროს
                </p>
                <div class="flex flex-wrap justify-center gap-6 mb-6">
                    <a href="tel:+995322195119" class="contact-link inline-flex items-center">
                        <i class="fas fa-phone mr-2"></i>
                        <span>+995-XXX-XXX-XXX</span>
                    </a>
                    <a href="mailto:info@synergy-gym.ge" class="contact-link inline-flex items-center">
                        <i class="fas fa-envelope mr-2"></i>
                        <span>info@synergy-gym.ge</span>
                    </a>
                    <a href="https://wa.me/995551195819" target="_blank" class="contact-link inline-flex items-center">
                        <i class="fab fa-whatsapp mr-2"></i>
                        <span>WhatsApp</span>
                    </a>
                </div>
                
                <!-- Social Media Section -->
                <div class="text-center">
                    <div class="flex justify-center gap-4">
                        <a href="https://www.facebook.com/SynergyGymTbilisi" target="_blank" rel="noopener noreferrer" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all duration-300 transform hover:scale-105 active:scale-95 touch-manipulation" onclick="openSocialMedia(event, 'https://www.facebook.com/SynergyGymTbilisi')">
                            <i class="fab fa-facebook-f mr-2"></i>
                            <span>Facebook</span>
                        </a>
                        <a href="https://www.instagram.com/synergy_gym_tbilisi/" target="_blank" rel="noopener noreferrer" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white rounded-lg transition-all duration-300 transform hover:scale-105 active:scale-95 touch-manipulation" onclick="openSocialMedia(event, 'https://www.instagram.com/synergy_gym_tbilisi/')">
                            <i class="fab fa-instagram mr-2"></i>
                            <span>Instagram</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- WhatsApp Button -->
    <button class="whatsapp-btn" onclick="openWhatsApp()" aria-label="Contact us on WhatsApp">
        <span class="whatsapp-text" name="key_contact_whatsapp">დაგვიკავშირდით</span>
        <i class="fab fa-whatsapp"></i>
    </button>

    <script>
        // Enable payment options when membership is selected
        function enablePaymentOptions() {
            const membershipSelect = document.getElementById('membershipSelect');
            const paymentSection = document.getElementById('paymentSection');
            const cardPayBtn = document.getElementById('cardPayBtn');
            const cardPaymentDiv = document.getElementById('cardPaymentDiv');
            const transferPayBtn = document.getElementById('transferPayBtn');
            
            if (membershipSelect.value !== '') {
                // Get selected option details
                const selectedOption = membershipSelect.options[membershipSelect.selectedIndex];
                const packageDescription = selectedOption.getAttribute('data-description') || selectedOption.textContent;
                const packageDescriptionEn = selectedOption.getAttribute('data-description-en') || '';
                
                // Check if this is a personal trainer package (contains "visit with personal trainer")
                const isPersonalTrainerPackage = packageDescription.includes('ვიზიტი პირად მწვრთნელთან') || 
                                               packageDescriptionEn.includes('visit with personal trainer');
                
                // Enable payment section
                paymentSection.classList.remove('opacity-50', 'pointer-events-none');
                paymentSection.classList.add('opacity-100');
                
                // Enable transfer payment button (always available)
                transferPayBtn.removeAttribute('disabled');
                
                if (isPersonalTrainerPackage) {
                    // Disable card payment for personal trainer packages
                    cardPayBtn.setAttribute('disabled', 'disabled');
                    cardPayBtn.style.opacity = '0.5';
                    cardPayBtn.style.cursor = 'not-allowed';
                    cardPayBtn.title = 'Card payment not available for personal trainer packages';
                    
                    // Disable the entire card payment div
                    cardPaymentDiv.style.opacity = '0.5';
                    cardPaymentDiv.style.cursor = 'not-allowed';
                    cardPaymentDiv.style.pointerEvents = 'none';
                    cardPaymentDiv.title = 'Card payment not available for personal trainer packages';
                    cardPaymentDiv.onclick = null;
                } else {
                    // Enable card payment for regular packages
                    cardPayBtn.removeAttribute('disabled');
                    cardPayBtn.style.opacity = '';
                    cardPayBtn.style.cursor = '';
                    cardPayBtn.title = '';
                    
                    // Enable the entire card payment div
                    cardPaymentDiv.style.opacity = '';
                    cardPaymentDiv.style.cursor = 'pointer';
                    cardPaymentDiv.style.pointerEvents = '';
                    cardPaymentDiv.title = '';
                    cardPaymentDiv.onclick = function() { payByCard(); };
                }
                
                // Smooth scroll to payment options
                paymentSection.scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'start' 
                });
            } else {
                // Disable payment section
                paymentSection.classList.add('opacity-50', 'pointer-events-none');
                paymentSection.classList.remove('opacity-100');
                
                // Disable payment buttons
                cardPayBtn.setAttribute('disabled', 'disabled');
                transferPayBtn.setAttribute('disabled', 'disabled');
                
                // Reset card payment div
                cardPaymentDiv.style.opacity = '';
                cardPaymentDiv.style.cursor = 'pointer';
                cardPaymentDiv.style.pointerEvents = '';
                cardPaymentDiv.title = '';
                cardPaymentDiv.onclick = function() { payByCard(); };
            }
        }

        // Update package options based on current language (optimized)
        let updatePackageLanguageTimer = null;
        function updatePackageLanguage() {
            // Debounce to prevent multiple rapid calls
            if (updatePackageLanguageTimer) {
                clearTimeout(updatePackageLanguageTimer);
            }
            
            updatePackageLanguageTimer = setTimeout(() => {
                // Try multiple ways to get the current language
                let currentLang = 'ka'; // default
                
                // Method 1: Try GetLanguage function if available
                if (typeof GetLanguage === 'function') {
                    currentLang = GetLanguage() || 'ka';
                }
                // Method 2: Direct localStorage check
                else if (localStorage.getItem('ActiveLanguage')) {
                    currentLang = localStorage.getItem('ActiveLanguage');
                }
                // Method 3: Check alternative localStorage key
                else if (localStorage.getItem('language')) {
                    currentLang = localStorage.getItem('language');
                }
                
                const membershipSelect = document.getElementById('membershipSelect');
                if (!membershipSelect) return;
                
                const options = membershipSelect.querySelectorAll('option[data-package-id]');
                
                options.forEach(option => {
                    const descriptionKey = currentLang === 'en' ? 'data-description-en' : 'data-description-ka';
                    const newText = option.getAttribute(descriptionKey);
                    if (newText && option.textContent !== newText) {
                        option.textContent = newText;
                        // Update the main data-description attribute as well
                        option.setAttribute('data-description', newText);
                    }
                });
                
                updatePackageLanguageTimer = null;
            }, 50); // Single 50ms debounce
        }

        // Get selected membership details
        function getSelectedMembership() {
            const select = document.getElementById('membershipSelect');
            if (!select.value) {
                alert('გთხოვთ აირჩიოთ წევრობის ტიპი');
                return null;
            }
            
            const selectedOption = select.options[select.selectedIndex];
            return {
                price: select.value,
                description: selectedOption.text,
                package_id: selectedOption.getAttribute('data-package-id'),
                package_description: selectedOption.getAttribute('data-description')
            };
        }

        // Pay by Card - Redirect to payment provider
        function payByCard() {
            const membership = getSelectedMembership();
            if (!membership) return;
            
            const userData = getUserData();
            if (!userData.phoneNumber) {
                alert('ტელეფონის ნომერი ვერ მოიძებნა');
                return;
            }

            // Call backend to handle Unipay payment flow
            fetch('unipay_checkout.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    amount: membership.price,
                    client_mobile_number: userData.phoneNumber,
                    user_id: userData.userId,
                    description: membership.description,
                    package_id: membership.package_id
                })
            })
            .then(res => res.json())
            .then(result => {
                if (!result.success || !result.checkout_url) {
                    alert('გადახდის შეცდომა');
                    return;
                }
                window.location.href = result.checkout_url;
            })
            .catch(() => {
                alert('გადახდის შეცდომა');
            });
        }

        // Pay by Transfer - Redirect to WhatsApp
        function payByTransfer() {
            const membership = getSelectedMembership();
            if (!membership) return;
            
            // Open WhatsApp without pre-filled message
            window.open(`https://wa.me/995551195819`, '_blank');
        }

        // WhatsApp Button Function
        function openWhatsApp() {
            window.open('https://wa.me/995551195819', '_blank');
        }

        // Social Media Button Function for Mobile
        function openSocialMedia(event, url) {
            // Prevent default link behavior
            event.preventDefault();
            
            // Check if we're on mobile
            const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
            
            if (isMobile) {
                // For mobile, use a timeout to ensure the page doesn't close
                setTimeout(() => {
                    try {
                        // Try to open in new window first
                        const newWindow = window.open(url, '_blank', 'noopener,noreferrer');
                        
                        // If popup was blocked, try direct navigation
                        if (!newWindow || newWindow.closed || typeof newWindow.closed == 'undefined') {
                            window.location.href = url;
                        }
                    } catch (error) {
                        // Fallback to direct navigation
                        window.location.href = url;
                    }
                }, 100);
            } else {
                // For desktop, normal behavior
                window.open(url, '_blank', 'noopener,noreferrer');
            }
        }

        // Override SetLanguage function to also update package language (optimized)
        window.SetLanguage = function() {
            // Get current language using multiple methods
            let currentLang = 'ka';
            if (typeof GetLanguage === 'function') {
                currentLang = GetLanguage() || 'ka';
            } else if (localStorage.getItem('ActiveLanguage')) {
                currentLang = localStorage.getItem('ActiveLanguage');
            }
            
            if (currentLang === 'en') {
                window.localStorage.setItem('ActiveLanguage', 'ka');
            } else {
                window.localStorage.setItem('ActiveLanguage', 'en');
            }
            
            // Update main language elements
            if (typeof ChangeData === 'function') {
                ChangeData().then(() => {
                    // Single call after ChangeData completes
                    updatePackageLanguage();
                });
            } else {
                // If ChangeData is not available, just update packages
                updatePackageLanguage();
            }
        };

        // Get URL parameters and set language
        function getUrlParameter(name) {
            name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
            var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
            var results = regex.exec(location.search);
            return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
        }

        // Get user_id and phone from URL parameters
        function getUserData() {
            const phoneNumber = getUrlParameter('phone');
            const userId = getUrlParameter('user_id');
            return { phoneNumber, userId };
        }

        // Load translations on page load (optimized)
        $(document).ready(function() {
            // Check if language parameter is in URL
            var urlLang = getUrlParameter('lang');
            if (urlLang && (urlLang === 'ka' || urlLang === 'en')) {
                // Set both localStorage keys for compatibility
                localStorage.setItem('ActiveLanguage', urlLang);
                localStorage.setItem('language', urlLang);
            }
            
            // Override the original ChangeData function to include our package updates
            if (typeof ChangeData === 'function') {
                const originalChangeData = ChangeData;
                window.ChangeData = function() {
                    return originalChangeData().then(() => {
                        updatePackageLanguage();
                    });
                };
                
                // Call the overridden function
                ChangeData();
            } else {
                // Update package language immediately if no ChangeData function
                updatePackageLanguage();
            }
        });

        // Initialize page state (optimized)
        document.addEventListener('DOMContentLoaded', function() {
            // Ensure payment options are disabled on page load
            enablePaymentOptions();
            
            // Update package language based on localStorage (only once)
            updatePackageLanguage();
            
            // Add scroll listener to hide/show language button
            let scrollTimer;
            const langBtn = document.querySelector('.lang-btn');
            
            window.addEventListener('scroll', function() {
                // Hide button when scrolling
                if (langBtn) {
                    langBtn.classList.add('scrolling');
                }
                
                // Clear existing timer
                clearTimeout(scrollTimer);
                
                // Show button again after scrolling stops
                scrollTimer = setTimeout(function() {
                    if (langBtn) {
                        langBtn.classList.remove('scrolling');
                    }
                }, 150);
            });
            
            // iOS Safari specific fixes for select element
            function isiOS() {
                return /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
            }
            
            if (isiOS()) {
                const membershipSelect = document.getElementById('membershipSelect');
                if (membershipSelect) {
                    // Force repaint on iOS when selecting
                    membershipSelect.addEventListener('change', function() {
                        this.style.transform = 'translateZ(0)';
                        setTimeout(() => {
                            this.style.transform = '';
                        }, 10);
                    });
                    
                    // Add touch events for better iOS support
                    membershipSelect.addEventListener('touchstart', function() {
                        this.focus();
                    });
                    
                    // Fix for iOS select appearance
                    membershipSelect.addEventListener('focus', function() {
                        this.style.background = '#1f1f1f';
                        this.style.color = '#fff';
                    });
                    
                    membershipSelect.addEventListener('blur', function() {
                        // Force update the display after selection
                        setTimeout(() => {
                            if (this.value) {
                                enablePaymentOptions();
                            }
                        }, 100);
                    });
                }
            }
        });
    </script>
</body>
</html>
