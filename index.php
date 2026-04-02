<?php
$lang = isset($_GET['lang']) ? $_GET['lang'] : 'ka';
header("Location: agreement.php?lang=" . $lang);
exit;
?>
<!DOCTYPE html>
<html lang="ka">
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="title" content="Synergy Gym in Didi Digomi, Tbilisi | Fitness Club & Training">
    <meta name="description" content="Discover Synergy Gym in Didi Digomi, Tbilisi's premier fitness club. We offer state-of-the-art equipment, personal training, and a motivating environment to help you achieve your health and fitness goals. Join us today">
    <meta name="keywords" content="gym in Didi Digomi, fitness tbilisi, Synergy Gym, gym tbilisi, personal trainer tbilisi, weight loss tbilisi, bodybuilding georgia, women's fitness tbilisi, affordable gym tbilisi, ფიტნესი დიდ დიღომში, დარბაზი დიდ დიღომში, ჯიმი დიდ დიღომში, affordable gym in digomi, fitness club Didi Digomi, fitness in Didi Digomi, საუკეთესო სავარჯიშო დარბაზი, ფიტნესი თბილისი, Didi Digomi ფიტნესი">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    
    <!-- Performance and caching meta tags -->
    <meta http-equiv="Cache-Control" content="public, max-age=3600">
    <meta http-equiv="Expires" content="<?php echo gmdate('D, d M Y H:i:s', time() + 3600); ?> GMT">
    <meta name="format-detection" content="telephone=no">
    <meta name="theme-color" content="#c8e600">
    
    <!-- Resource hints for better performance -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://ajax.googleapis.com">
    <link rel="preconnect" href="https://connect.facebook.net">
    <link rel="preconnect" href="https://upload.wikimedia.org">
    <link rel="dns-prefetch" href="//ajax.googleapis.com">
    <link rel="dns-prefetch" href="//connect.facebook.net">
    <link rel="dns-prefetch" href="//upload.wikimedia.org">
    
    <!-- Preload critical CSS resources -->
    <link rel="preload" href="css/bootstrap.min.css" as="style" crossorigin="anonymous">
    <link rel="preload" href="css/style.css" as="style" crossorigin="anonymous">
    <link rel="preload" href="css/fontawesome-all.min.css" as="style" crossorigin="anonymous">
    <link rel="preload" href="css/jquery-ui.min.css" as="style" crossorigin="anonymous">
    
    <!-- Preload critical JavaScript -->
    <link rel="preload" href="js/bootstrap.min.js" as="script" crossorigin="anonymous">
    <link rel="preload" href="js/popper.min.js" as="script" crossorigin="anonymous">
    <link rel="preload" href="js/scripts.js" as="script" crossorigin="anonymous">
    <link rel="preload" href="js/language.js" as="script" crossorigin="anonymous">
    
    <!-- Preload critical fonts -->
    <link rel="preload" href="css/webfonts/bpg_banner.woff" as="font" type="font/woff" crossorigin="anonymous">
    <link rel="preload" href="css/webfonts/fa-solid-900.woff2" as="font" type="font/woff2" crossorigin="anonymous">
    <link rel="preload" href="css/webfonts/fa-brands-400.woff2" as="font" type="font/woff2" crossorigin="anonymous">
    
    <!-- Preload critical images -->
    <link rel="preload" href="img/logo.png" as="image" type="image/png">
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js" defer crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js" defer crossorigin="anonymous"></script>
    
    <link rel="stylesheet" href="css/bootstrap.min.css" crossorigin="anonymous">
    
    <!-- Use local jQuery UI with non-blocking load -->
    <link rel="stylesheet" href="css/jquery-ui.min.css" crossorigin="anonymous">
    
    <!-- Use local Font Awesome with non-blocking load -->
    <link rel="stylesheet" href="css/fontawesome-all.min.css" crossorigin="anonymous">
    
    <!-- Optimized Google Fonts loading -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" media="print" onload="this.media='all'">
    <noscript><link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"></noscript>
    
    <!-- Load style.css -->
    <link rel="stylesheet" href="css/style.css" crossorigin="anonymous">

    <link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon">
    <link rel="icon" href="img/favicon.ico" type="image/x-icon">
    <title>Synergy Gym in Didi Digomi, Tbilisi | Fitness Club & Training</title>

    <style>
        /* Remove default browser focus outlines and yellow circles on mobile */
        * {
            outline: none !important;
            -webkit-tap-highlight-color: transparent !important;
        }
        
        button:focus,
        a:focus,
        .menu-link:focus,
        .nav-item:focus,
        .btn:focus,
        .top-btn:focus,
        .mobile-btn:focus,
        .navbar-toggler:focus {
            outline: none !important;
            box-shadow: none !important;
            border: none !important;
            -webkit-tap-highlight-color: transparent !important;
        }
        
        /* Disable touch highlight on mobile devices */
        .menu-link,
        .nav-item,
        .btn,
        .top-btn,
        .mobile-btn,
        .navbar-toggler,
        a,
        button {
            -webkit-tap-highlight-color: transparent !important;
            -webkit-touch-callout: none !important;
            -webkit-user-select: none !important;
            -khtml-user-select: none !important;
            -moz-user-select: none !important;
            -ms-user-select: none !important;
            user-select: none !important;
        }

        /* FIX: Added smooth scrolling and top padding for anchor links */
        html {
            scroll-behavior: smooth;
            scroll-padding-top: 80px; /* Adjust this value to match your header's height */
        }

        /* Content visibility styles */
        .content-visible {
            opacity: 1;
            visibility: visible;
        }

        /* FOUC (Flash of Untranslated Content) fix - All devices */
        .content-visible {
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease-in;
        }
        
        .content-visible.translations-loaded,
        .content-visible.mobile-ready {
            opacity: 1;
            visibility: visible;
        }
        
        /* Fallback: show content after 800ms if translations don't load */
        .content-visible.fallback-show {
            opacity: 1;
            visibility: visible;
        }

        /* Universal loader styles - shared between mobile and desktop */
        .page-loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            opacity: 1;
            visibility: visible;
        }
        
        .page-loader.hide {
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
        }
        
        .loader-logo {
              width: 150px;
              height: auto;
            object-fit: contain;
              border-radius: 0;
            margin-bottom: 20px;
              display: block;
            animation: logoFade 1.2s ease-in-out infinite alternate;
        }
        
        /* Nav logo sizing for square brand icon */
        .menu-logo img {
            max-height: 90px;
            width: auto !important;
            height: auto !important;
            object-fit: contain;
                border-radius: 0;
            display: block;
        }
        .logo-sm img {
            max-height: 50px;
            width: auto !important;
            height: auto !important;
            object-fit: contain;
                border-radius: 0;
        }
        .nav-item.menu-logo .menu-link {
            padding: 5px 15px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }
        .nav-item.menu-logo {
            overflow: visible !important;
        }
        .navbar, .navbar-nav {
            overflow: visible !important;
        }
        
        .loader-spinner {
            width: 40px;
            height: 40px;
            border: 3px solid rgba(200, 230, 0, 0.3);
            border-top: 3px solid #c8e600;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        .loader-text {
            color: #c8e600;
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            margin-top: 15px;
            font-weight: 600;
            letter-spacing: 1px;
        }

        /* Mobile loader - optimized for mobile devices */
        @media (max-width: 768px) {
            .mobile-loader {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                z-index: 9999;
                opacity: 1;
                visibility: visible;
                transition: opacity 0.2s ease-out;
            }
            
            .mobile-loader.hide {
                opacity: 0;
                visibility: hidden;
                pointer-events: none;
            }
            
            /* Hide desktop loader on mobile */
            .desktop-loader {
                display: none !important;
            }
        }

        /* Desktop loader - optimized for desktop devices */
        @media (min-width: 769px) {
            .desktop-loader {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                z-index: 9999;
                opacity: 1;
                visibility: visible;
                transition: opacity 0.3s ease-out;
            }
            
            .desktop-loader.hide {
                opacity: 0;
                visibility: hidden;
                pointer-events: none;
            }
            
            .desktop-loader .loader-logo {
                width: 150px;
                height: auto;
                margin-bottom: 25px;
                animation: logoFade 1.2s ease-in-out infinite alternate;
            }
            
            .desktop-loader .loader-spinner {
                width: 50px;
                height: 50px;
                border: 4px solid rgba(200, 230, 0, 0.3);
                border-top: 4px solid #c8e600;
                border-radius: 50%;
                animation: spin 1s linear infinite;
            }
            
            .desktop-loader .loader-text {
                color: #c8e600;
                font-family: 'Inter', sans-serif;
                font-size: 16px;
                margin-top: 20px;
                font-weight: 600;
                letter-spacing: 1.2px;
            }
            
            /* Hide mobile loader on desktop */
            .mobile-loader {
                display: none !important;
            }
        }
        
        @keyframes logoFade {
            0% { opacity: 0.7; transform: scale(1); }
            100% { opacity: 1; transform: scale(1.05); }
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Smooth content animation */
        #cont.content-visible {
            animation: fadeInSmooth 0.4s ease-in-out forwards;
        }

        @keyframes fadeInSmooth {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
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
            background: linear-gradient(135deg, #1a73e8 0%, #1557b0 100%);
            color: #ffffff;
            padding: 16px 28px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 700;
            font-size: 1.1rem;
            box-shadow: 0 10px 30px rgba(26, 115, 232, 0.5);
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
            box-shadow: 0 15px 40px rgba(26, 115, 232, 0.7);
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
                box-shadow: 0 10px 30px rgba(26, 115, 232, 0.5);
                transform: scale(1.1);
            }
            50% {
                box-shadow: 0 15px 40px rgba(26, 115, 232, 0.8);
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
        
        /* Fix white background flash - use dark placeholder */
        body {
            font-family: 'Inter', sans-serif;
            background-color: #1a1a1a; /* Dark placeholder while image loads */
        }
        .price-box {
            background-color: #c8e600;
            color: #000000;
            padding: 10px 20px;
            border-radius: 8px;
            display: inline-block;
            font-weight: bold;
            font-size: 1.2rem;
        }

        /* Enhanced pricing styles for deals */
        .price-box-offer {
            background: linear-gradient(135deg, #c8e600 0%, #a8c200 100%);
            color: #000000;
            padding: 15px 25px;
            border-radius: 12px;
            display: inline-block;
            font-weight: bold;
            position: relative;
            box-shadow: 0 8px 25px rgba(200, 230, 0, 0.3);
            border: 2px solid #c8e600;
            animation: pulse-glow 2s ease-in-out infinite;
        }

        .price-box-offer::before {
            content: '';
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            background: linear-gradient(45deg, #c8e600, #a8c200, #d4f000, #c8e600);
            background-size: 400% 400%;
            border-radius: 12px;
            z-index: -1;
            animation: gradient-border 3s ease infinite;
        }

        @keyframes pulse-glow {
            0%, 100% {
                box-shadow: 0 8px 25px rgba(200, 230, 0, 0.3);
                transform: scale(1);
            }
            50% {
                box-shadow: 0 12px 35px rgba(200, 230, 0, 0.5);
                transform: scale(1.02);
            }
        }

        @keyframes gradient-border {
            0%, 100% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
        }

        .original-price {
            font-size: 0.9rem;
            color: #666;
            text-decoration: line-through;
            display: block;
            margin-bottom: 5px;
            font-weight: normal;
        }

        .offer-price {
            font-size: 1.8rem;
            font-weight: 900;
            color: #000;
            display: block;
            line-height: 1;
        }

        /* Tailwind CSS replacements for production */
        .flex { display: flex; }
        .flex-wrap { flex-wrap: wrap; }
        .flex-col { flex-direction: column; }
        .justify-center { justify-content: center; }
        .items-center { align-items: center; }
        .text-center { text-align: center; }
        .gap-4 { gap: 1rem; }
        .my-4 { margin-top: 1rem; margin-bottom: 1rem; }
        .bg-black { background-color: #000000; }
        .py-12 { padding-top: 3rem; padding-bottom: 3rem; }
        .mx-auto { margin-left: auto; margin-right: auto; }
        .max-w-4xl { max-width: 56rem; }
        .w-full { width: 100%; }
        .bg-black-900 { background-color: #111827; }
        .rounded-2xl { border-radius: 1rem; }
        .shadow-lg { box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05); }
        .p-8 { padding: 2rem; }
        .h-12 { height: 3rem; }
        .w-12 { width: 3rem; }
        .mb-4 { margin-bottom: 1rem; }
        .mb-3 { margin-bottom: 0.75rem; }
        .text-5xl { font-size: 3rem; line-height: 1; }
        .text-3xl { font-size: 1.875rem; line-height: 2.25rem; }
        .text-2xl { font-size: 1.5rem; line-height: 2rem; }
        .font-bold { font-weight: 700; }
        .font-semibold { font-weight: 600; }
        .text-white { color: #ffffff; }
        .text-xl { font-size: 1.25rem; line-height: 1.75rem; }
        .text-black-400 { color: #9ca3af; }
        .text-gray-300 { color: #d1d5db; }
        .text-gray-400 { color: #9ca3af; }
        .text-yellow-400 { color: #fbbf24; }
        .mt-2 { margin-top: 0.5rem; }
        .ml-3 { margin-left: 0.75rem; }
        .my-5 { margin-top: 1.25rem; margin-bottom: 1.25rem; }
        .my-6 { margin-top: 1.5rem; margin-bottom: 1.5rem; }
        .border-gray-700 { border-color: #374151; }
        .grid { display: grid; }
        .grid-cols-1 { grid-template-columns: repeat(1, minmax(0, 1fr)); }
        .gap-8 { gap: 2rem; }
        .text-left { text-align: left; }
        .p-6 { padding: 1.5rem; }
        .rounded-lg { border-radius: 0.5rem; }
        .bg-gray-800 { background-color: #1f2937; }
        .hover\:underline:hover { text-decoration: underline; }
        
        @media (min-width: 768px) {
            .md\:grid-cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
        
        /* Style for the language change button */
        .topcorner {
            background-color: #c8e600; /* Same yellow as your other buttons */
            color: #000000;           /* Black text for good contrast */
            padding: 8px 12px;
            border-radius: 8px;       /* Rounded corners to match */
            margin-top: 5px;          /* A little space from the top */
        }

        /* Call-to-action text styling - enhanced visibility */
        .price-cta {
            margin-top: 15px;
            font-size: 0.95rem;
            color: #ffffff;
            background-color: rgba(0, 0, 0, 0.7);
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: 2px solid #c8e600;
            display: inline-block;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
        }

        .price-block:hover .price-cta {
            background-color: #c8e600;
            color: #000000;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(200, 230, 0, 0.4);
            border-color: #e6c500;
        }

        /* Pricing boxes alignment and proportional sizing */
        .packages .row {
            display: flex;
            flex-wrap: wrap;
            align-items: stretch;
            justify-content: center; /* center columns and avoid overly slim columns */
        }

        /* Let columns breathe on wide screens */
        @media (min-width: 1200px) {
            .packages .row > [class*='col-'] {
                max-width: 380px;
            }
        }

        .packages [class*='col-'] {
            display: flex;
            margin-bottom: 30px;
        }

        .packages .price-block {
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            width: 100%;
            min-height: 520px;
            position: relative;
            padding: 34px 24px 24px 24px !important;
        }

        /* Ensure all price boxes have consistent spacing */
        .packages [class*='col-'] {
            margin-top: 30px !important;
            margin-bottom: 30px !important;
        }

        /* Content structure and spacing */
        .price-block h1 {
            flex-shrink: 0;
            margin-bottom: 0;
        }
        
        .price-block h2 {
            flex-shrink: 0;
            margin-top: 20px !important;
            margin-bottom: 0;
        }
        
        .price-block h3 {
            flex-shrink: 0;
            margin-top: 20px !important;
            margin-bottom: 0;
        }

        .price-block .price-box,
        .price-block .price-box-offer {
            margin-top: 25px !important;
            margin-bottom: 20px;
            flex-shrink: 0;
        }

        .price-block .price-cta {
            margin-top: auto !important;
            margin-bottom: 0;
            flex-shrink: 0;
        }

        /* Top buttons container for login and language */
        .top-buttons-container {
            position: absolute;
            top: 15px;
            right: -200px;
            display: flex;
            gap: 10px;
            align-items: center;
            z-index: 1050;
        }

        .top-btn {
            background-color: #c8e600;
            color: #000000;
            padding: 8px 15px;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            display: inline-block;
        }

        .top-btn:hover {
            background-color: #1a73e8;
            color: #000000;
            text-decoration: none;
            transform: translateY(-1px);
            box-shadow: 0 3px 8px rgba(26, 115, 232, 0.35);
        }

        .top-btn span, .top-btn h6 {
            margin: 0;
            font-size: inherit;
            font-weight: inherit;
        }

        /* Mobile responsive adjustments */
        @media (max-width: 991px) {
            .top-buttons-container {
                position: static;
                justify-content: center;
                margin: 15px 0;
                gap: 10px;
                order: 3; /* Move after navigation */
            }
            
            .top-btn {
                padding: 10px 20px;
                font-size: 0.9rem;
                min-width: 120px;
                text-align: center;
            }
            
            /* Language button in top right corner on mobile */
            .top-btn:last-child {
                position: absolute;
                top: 15px;
                right: 15px;
                margin: 0;
                padding: 8px 12px;
                font-size: 0.8rem;
                min-width: auto;
                z-index: 1060;
            }
            
            /* Specific fixes for Pixel 4, iPhone SE, Galaxy S8 Ultra */
            @media (max-width: 414px) and (max-height: 896px) {
                .top-btn:last-child {
                    top: 8px !important;
                    right: 8px !important;
                    padding: 6px 10px !important;
                    font-size: 0.7rem !important;
                    background-color: rgba(200, 230, 0, 0.95) !important;
                    backdrop-filter: blur(5px) !important;
                    border: 1px solid rgba(0, 0, 0, 0.1) !important;
                    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15) !important;
                }
            }
            
            /* Additional fix for very narrow screens like iPhone SE */
            @media (max-width: 375px) {
                .top-btn:last-child {
                    top: 5px !important;
                    right: 5px !important;
                    padding: 5px 8px !important;
                    font-size: 0.65rem !important;
                }
            }
            
            /* Hide the login button from top container on mobile */
            .top-btn:first-child {
                display: none;
            }
            
            .navbar-toggler {
                border: none;
                padding: 0;
            }
            
            /* COMPLETELY HIDE the middle logo on mobile - this is causing the yellow box */
            .nav-item.menu-logo {
                display: none !important;
                visibility: hidden !important;
                width: 0 !important;
                height: 0 !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            
            /* Fix navbar centering for all mobile/tablet devices */
            .navbar {
                justify-content: center !important;
                align-items: center !important;
            }
            
            .navbar-nav {
                justify-content: center !important;
                align-items: center !important;
                text-align: center !important;
            }
            
            .nav-item.menu-logo img {
                display: none !important;
            }
            
            /* Consistent spacing for navigation items on mobile */
            .navbar-nav {
                display: flex !important;
                justify-content: center !important;
                width: 100% !important;
                max-width: 400px !important;
                margin: 0 auto !important;
                padding: 20px 10px !important;
                gap: 15px !important;
            }
            
            .navbar-nav .nav-item {
                flex: 0 0 auto !important;
                text-align: center !important;
                margin: 0 !important;
            }
            
            /* Remove any background colors from nav items that could cause yellow box */
            .navbar-nav .nav-item,
            .navbar-nav .nav-item.middle,
            .navbar-nav .nav-item.menu-logo {
                background: none !important;
                background-color: transparent !important;
            }
            
            .navbar-nav .nav-item .menu-link {
                background-color: #c8e600 !important;
                color: #000000 !important;
                padding: 10px 16px !important;
                border-radius: 8px !important;
                font-size: 0.9rem !important;
                font-weight: 600 !important;
                text-decoration: none !important;
                transition: all 0.3s ease !important;
                border: none !important;
                cursor: pointer !important;
                display: inline-block !important;
                text-align: center !important;
                white-space: nowrap !important;
            }
            
            .navbar-nav .nav-item .menu-link:hover {
                background-color: #e6c500 !important;
                color: #000000 !important;
                text-decoration: none !important;
                transform: translateY(-1px) !important;
                box-shadow: 0 3px 8px rgba(200, 230, 0, 0.3) !important;
            }
            
            /* Add mobile buttons container after nav */
            .mobile-buttons-container {
                display: flex !important;
                justify-content: center !important;
                margin: 15px auto !important;
                flex-wrap: wrap !important;
                padding: 15px 10px !important;
                width: 100% !important;
                max-width: 400px !important;
                gap: 15px !important;
            }
            
            /* Fix shadow background on all mobile devices */
            .container-fluid.shadow-bg {
                display: none !important;
                visibility: hidden !important;
            }
            
            .packages {
                position: relative !important;
            }
            
            /* Join Now Button - Eye-catching gradient with animation */
            .mobile-btn:first-child {
                background: #c8e600;
                background-size: 300% 300%;
                color: #000000;
                padding: 15px 25px;
                border-radius: 12px;
                font-size: 1.1rem;
                font-weight: 700;
                text-decoration: none;
                transition: all 0.3s ease;
                border: 2px solid #c8e600;
                cursor: pointer;
                display: inline-block;
                text-align: center;
                margin: 0 !important;
                white-space: nowrap;
                text-transform: uppercase;
                letter-spacing: 1px;
                animation: pulseGlow 2s ease-in-out infinite;
                box-shadow: 0 8px 25px rgba(200, 230, 0, 0.4);
                position: relative;
                overflow: hidden;
            }
            
            .mobile-btn:first-child::before {
                content: '';
                position: absolute;
                top: 0;
                left: -100%;
                width: 100%;
                height: 100%;
                background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
                transition: left 0.6s ease;
            }
            
            .mobile-btn:first-child:hover::before {
                left: 100%;
            }
            
            .mobile-btn:first-child:hover {
                transform: translateY(-3px) scale(1.05);
                box-shadow: 0 12px 35px rgba(200, 230, 0, 0.6);
                border-color: #c8e600;
            }
            
            /* Log In Button - Simple and clean */
            .mobile-btn:last-child {
                background-color: #c8e600;
                color: #000000;
                padding: 12px 20px;
                border-radius: 8px;
                font-size: 0.95rem;
                font-weight: 600;
                text-decoration: none;
                transition: all 0.3s ease;
                border: 2px solid #c8e600;
                cursor: pointer;
                display: inline-block;
                text-align: center;
                margin: 0 !important;
                white-space: nowrap;
            }
            
            .mobile-btn:last-child:hover {
                background-color: #e6c500;
                color: #000000;
                text-decoration: none;
                transform: translateY(-1px);
                box-shadow: 0 3px 8px rgba(200, 230, 0, 0.3);
            }
        }

        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        @keyframes pulseGlow {
            0%, 100% {
                box-shadow: 0 8px 25px rgba(200, 230, 0, 0.4);
                transform: scale(1);
            }
            50% {
                box-shadow: 0 12px 35px rgba(200, 230, 0, 0.6);
                transform: scale(1.02);
            }
        }

        @media (max-width: 576px) {
            .mobile-buttons-container {
                flex-direction: column;
                align-items: center;
                gap: 25px;
                justify-content: center;
                padding: 25px 20px;
            }
            
            .mobile-btn:first-child {
                width: 250px;
                padding: 18px 30px;
                font-size: 1.2rem;
            }
            
            .mobile-btn:last-child {
                width: 200px;
                padding: 14px 25px;
                font-size: 1rem;
            }
            
            .top-btn:last-child {
                right: 10px;
                top: 10px;
            }
        }

        /* Tablet-specific optimizations for iPad Mini (768px-991px) */
        @media (min-width: 768px) and (max-width: 991px) {
            .mobile-buttons-container {
                margin: 20px auto !important;
                padding: 20px !important;
                max-width: 500px !important;
                gap: 20px !important;
            }
            
            .mobile-btn {
                padding: 16px 32px !important;
                font-size: 1.1rem !important;
                min-width: 200px !important;
                margin: 8px !important;
            }
            
            /* Hide duplicate elements on tablet */
            .top-buttons-container .top-btn:first-child {
                display: none !important;
            }
            
            /* Move language button to top right on iPad Mini */
            .top-btn:last-child {
                position: absolute !important;
                top: 10px !important;
                right: 5px !important;
                margin: 0 !important;
                padding: 8px 12px !important;
                font-size: 0.8rem !important;
                min-width: auto !important;
                z-index: 1070 !important;
            }
            
            /* iPad Mini: HIDE the navbar logo completely to prevent yellow box */
            .nav-item.menu-logo {
                display: none !important;
                visibility: hidden !important;
                width: 0 !important;
                height: 0 !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            
            .nav-item.menu-logo img {
                display: none !important;
            }
            
            /* iPad Mini: Show logo above the entire header section */
            .navbar-toggler .logo-sm {
                display: block !important;
                text-align: center !important;
                margin: 0 auto 15px auto !important;
            }
            
            .navbar-toggler .logo-sm img {
                max-height: 50px !important;
                width: auto !important;
                display: block !important;
                margin: 0 auto !important;
            }
            
            /* Fix navbar structure for iPad Mini */
            .navbar {
                justify-content: center !important;
                flex-direction: column !important;
            }
            
            .navbar-nav {
                justify-content: center !important;
                align-items: center !important;
                width: 100% !important;
                flex-wrap: nowrap !important;
                gap: 15px !important;
            }
            
            /* Ensure proper spacing for tablet navigation buttons */
            .navbar-nav .nav-item .menu-link {
                background-color: #c8e600 !important;
                color: #000000 !important;
                padding: 12px 20px !important;
                border-radius: 8px !important;
                font-size: 1rem !important;
                font-weight: 600 !important;
                text-decoration: none !important;
                transition: all 0.3s ease !important;
                border: none !important;
                cursor: pointer !important;
                display: inline-block !important;
                text-align: center !important;
                white-space: nowrap !important;
                margin: 4px !important;
            }
            
            .navbar-nav .nav-item .menu-link:hover {
                background-color: #e6c500 !important;
                color: #000000 !important;
                text-decoration: none !important;
                transform: translateY(-1px) !important;
                box-shadow: 0 3px 8px rgba(200, 230, 0, 0.3) !important;
            }
            
            /* Better button layout for tablets */
            .mobile-buttons-container .mobile-btn:first-child {
                background: #c8e600 !important;
                background-size: 300% 300% !important;
                animation: pulseGlow 2s ease-in-out infinite !important;
            }
            
            /* Remove any background colors from nav items */
            .navbar-nav .nav-item,
            .navbar-nav .nav-item.middle {
                background: none !important;
                background-color: transparent !important;
            }
            
            /* Fix any spacing issues between sections */
            .packages {
                margin-bottom: 0 !important;
                padding-bottom: 0 !important;
            }
            
            .bg-black.py-12 {
                margin-top: 0 !important;
                padding-top: 3rem !important;
            }
            
            /* Remove any potential yellow background elements */
            .container-fluid.shadow-bg {
                display: none !important;
            }
            
            /* Ensure pricing section has proper spacing */
            .packages {
                position: relative !important;
                margin-bottom: 0 !important;
                padding-bottom: 0 !important;
            }
            
            .packages .container-fluid.shadow-bg {
                display: none !important;
                visibility: hidden !important;
            }
            
            /* Ensure contact section has proper spacing */
            #contact {
                margin-top: 0 !important;
                padding-top: 3rem !important;
            }
            
            /* Fix any potential background color issues */
            .full-width.join-shadow {
                background: #000 !important;
                display: none !important;
            }
        }
    </style>
    
    <script src="js/scripts.js" crossorigin="anonymous" defer></script>
    <script src="js/language.js" crossorigin="anonymous" defer></script>
    
    <!-- WebP Detection Script -->
    <script>
        // WebP format detection
        function supportsWebP() {
            return new Promise((resolve) => {
                const webP = new Image();
                webP.onload = webP.onerror = () => resolve(webP.height === 2);
                webP.src = 'data:image/webp;base64,UklGRjoAAABXRUJQVlA4IC4AAACyAgCdASoCAAIALmk0mk0iIiIiIgBoSygABc6WWgAA/veff/0PP8bA//LwYAAA';
            });
        }
        
        // Add WebP support class to HTML
        supportsWebP().then(hasWebP => {
            if (!hasWebP) {
                document.documentElement.classList.add('no-webp');
            }
        });
    </script>
    
    <!-- Universal loader and FOUC (Flash of Untranslated Content) fix - Layout Optimized -->
    <script>
        // Universal loader and FOUC fix for both mobile and desktop
        function initializeUniversalLoader() {
            const isMobile = window.matchMedia('(max-width: 768px)').matches;
            const mobileLoader = document.getElementById('mobileLoader');
            const desktopLoader = document.getElementById('desktopLoader');
            const currentLoader = isMobile ? mobileLoader : desktopLoader;
            const contentElements = document.querySelectorAll('.content-visible');
            let translationsLoaded = false;
            
            // Check if user has cache (faster loading)
            function hasCachedResources() {
                // Check if this is a fresh page load vs cached
                const navigationEntry = performance.getEntriesByType('navigation')[0];
                if (navigationEntry) {
                    // If transfer size is 0 or very small, resources are likely cached
                    const isCached = navigationEntry.transferSize < 1000; // Less than 1KB indicates cached
                    return isCached;
                }
                
                // Fallback: check if DOM is already ready (indicates fast loading)
                return document.readyState === 'complete' || document.readyState === 'interactive';
            }
            
            // Handle loader display for current device type
            if (currentLoader) {
                // Hide the non-active loader immediately
                if (isMobile && desktopLoader) {
                    desktopLoader.style.display = 'none';
                } else if (!isMobile && mobileLoader) {
                    mobileLoader.style.display = 'none';
                }
                
                // Skip loader if resources are cached (fast loading)
                if (hasCachedResources()) {
                    currentLoader.style.display = 'none';
                    console.log('Synergy: Loader skipped - cached resources detected');
                } else {
                    // Show loader for fresh loads
                    currentLoader.style.display = 'flex';
                    console.log('Synergy: Loader shown - fresh load detected for', isMobile ? 'mobile' : 'desktop');
                    
                    // Set loader duration based on device type
                    const loaderDuration = isMobile ? 650 : 650; // 650ms for mobile, 650ms for desktop
                    
                    // Hide loader after specified duration
                    setTimeout(() => {
                        currentLoader.classList.add('hide');
                        // Remove from DOM after transition
                        setTimeout(() => {
                            if (currentLoader.parentNode) {
                                currentLoader.parentNode.removeChild(currentLoader);
                            }
                        }, isMobile ? 200 : 300); // Different transition times
                    }, loaderDuration);
                }
            }
            
            // FOUC fix for translations (same for both mobile and desktop)
            function checkTranslationsReady() {
                if (typeof SetLanguage === 'function' && !translationsLoaded) {
                    translationsLoaded = true;
                    requestAnimationFrame(() => {
                        contentElements.forEach(element => {
                            element.classList.add('translations-loaded');
                        });
                    });
                }
            }
            
            function pollForTranslations() {
                if (!translationsLoaded) {
                    checkTranslationsReady();
                    if (!translationsLoaded) {
                        requestAnimationFrame(pollForTranslations);
                    }
                }
            }
            
            requestAnimationFrame(pollForTranslations);
            
            // Fallback: show content after timeout if translations don't load
            const fallbackTimeout = isMobile ? 800 : 1000; // Longer timeout for desktop
            setTimeout(function() {
                if (!translationsLoaded) {
                    requestAnimationFrame(() => {
                        contentElements.forEach(element => {
                            element.classList.add('fallback-show');
                        });
                    });
                }
            }, fallbackTimeout);
        }

        // Initialize immediately
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initializeUniversalLoader);
        } else {
            initializeUniversalLoader();
        }
    </script>

    <!-- Cache optimization script -->
    <script>
        // Service Worker registration for advanced caching (optional)
        if ('serviceWorker' in navigator && 'caches' in window) {
            // Basic cache management for critical resources
            const cacheVersion = 'Synergy-v1.0';
            const criticalResources = [
                '/css/bootstrap.min.css',
                '/css/style.css',
                '/js/bootstrap.min.js',
                '/js/popper.min.js',
                '/js/scripts.js',
                '/js/language.js',
                '/img/logo.png'
            ];
            
            // Preload critical resources into cache
            window.addEventListener('load', function() {
                if ('caches' in window) {
                    caches.open(cacheVersion).then(function(cache) {
                        cache.addAll(criticalResources.map(url => new Request(url, {
                            cache: 'force-cache'
                        })));
                    });
                }
            });
        }
        
        // Optimize external resource loading
        window.addEventListener('load', function() {
            // Prefetch external resources for next page loads
            const linkPrefetch = document.createElement('link');
            linkPrefetch.rel = 'prefetch';
            linkPrefetch.href = 'https://upload.wikimedia.org/wikipedia/commons/6/6b/WhatsApp.svg';
            document.head.appendChild(linkPrefetch);
        });
    </script>

    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "ExerciseGym",
      "name": "Synergy Gym",
      "description": "Tbilisi's fitness club in Didi Digomi, offering modern equipment, personal training, and a motivating atmosphere.",
      "url": "/",
      "logo": "/img/logo.png",
      "telephone": "+995-XXX-XXX-XXX",
      "priceRange": "$$",
      "address": {
        "@type": "PostalAddress",
        "addressLocality": "Tbilisi",
        "addressCountry": "GE"
      },
      "geo": {
        "@type": "GeoCoordinates",
        "latitude": "41.788453",
        "longitude": "44.762575"
      },
      "openingHoursSpecification": [
        {
          "@type": "OpeningHoursSpecification",
          "dayOfWeek": ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday"],
          "opens": "08:00",
          "closes": "02:00"
        },
        { "@type": "OpeningHoursSpecification", "dayOfWeek": "Saturday", "opens": "08:00", "closes": "02:00" },
        { "@type": "OpeningHoursSpecification", "dayOfWeek": "Sunday", "opens": "09:00", "closes": "22:00" }
      ],
      "sameAs": [
        "https://www.facebook.com/SynergyGymTbilisi",
        "https://www.instagram.com/synergy_gym_tbilisi/"
      ]
    }
    </script>
    
    <!-- Meta Pixel Code - Configure when FB Pixel ID is available -->
    <!--
    <script>
    !function(f,b,e,v,n,t,s)
    {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
    n.callMethod.apply(n,arguments):n.queue.push(arguments)};
    if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
    n.queue=[];t=b.createElement(e);t.async=!0;
    t.src=v;s=b.getElementsByTagName(e)[0];
    s.parentNode.insertBefore(t,s)}(window, document,'script',
    'https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', 'YOUR_PIXEL_ID');
    fbq('track', 'PageView');
    </script>
    -->
    <!-- End Meta Pixel Code -->
    
</head>
<body>

<!-- Mobile Loader - Only visible on mobile devices -->
<div class="mobile-loader" id="mobileLoader">
    <img src="img/logo.png" alt="Synergy Gym" class="loader-logo" onerror="this.style.display='none';">
    <div class="loader-spinner"></div>
    <div class="loader-text">Loading...</div>
</div>

<!-- Desktop Loader - Only visible on desktop devices -->
<div class="desktop-loader" id="desktopLoader">
    <img src="img/logo.png" alt="Synergy Gym" class="loader-logo" onerror="this.style.display='none';">
    <div class="loader-spinner"></div>
    <div class="loader-text">Loading...</div>
</div>

<a href="https://wa.me/+995-XXX-XXX-XXX" class="whatsapp-float" target="_blank">
    <div class="whatsapp-button">
        <img src="https://upload.wikimedia.org/wikipedia/commons/6/6b/WhatsApp.svg" alt="WhatsApp" class="whatsapp-icon" onerror="this.onerror=null; this.src='https://placehold.co/24x24/ffffff/25d366?text=WA';">
        <span class="whatsapp-text" name="key_contact_whatsapp">Contact Us</span>
    </div>
</a>

<div id="cont" class="content-visible">
    <div class="container-fluid top-block no-padding">
        <div class="container" > 
            <header class="full-width text-center pt-3 pb-3">
                <nav class="navbar navbar-expand-lg navbar-light bg-none no-padding col-md-8 offset-2" style="justify-content: center !important;">
                    <button class="navbar-toggler full-width" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <a href="/" class="logo-sm banner"><img src="img/logo.png" alt="Synergy Gym Logo" onerror="this.onerror=null; this.src='https://placehold.co/150x50/cccccc/000000?text=Synergy+Logo';"></a>
                    </button>
                    <div class="" id="navbarSupportedContent" style="justify-content: center; display: flex;">
                        <ul class="navbar-nav mr-auto middle">
                            <li class="nav-item middle"><a href="#packages" class="menu-link banner menu_links" name="key_packages">key_packages</a></li>
                            <li class="nav-item menu-logo d-none d-md-block"><a href="/" class="menu-link banner"><img src="img/logo.png" alt="Synergy Gym Logo" onerror="this.onerror=null; this.src='https://placehold.co/150x50/cccccc/000000?text=Synergy+Logo';"></a></li>
                            <li class="nav-item middle"><a href="#contact" class="menu-link banner menu_links" name="key_contact">key_contact</a></li>
                        </ul>
                    </div>
                    <div class="top-buttons-container">
                        <a href="javascript:void(0);" onclick="openAuthWithLang(); return false;" class="top-btn banner" name="key_sign_in">key_sign_in</a>
                        <div class="top-btn pointer" onclick="SetLanguage()"><span name='key_lang'>key_lang</span></div>
                    </div>
                </nav>
                
                <!-- Mobile buttons container - only visible on mobile/tablet -->
                <div class="mobile-buttons-container d-block d-lg-none">
                    <a href="javascript:void(0);" onclick="openAgreementWithLang(); return false;" class="mobile-btn banner" target="_blank" name="key_become_member">key_become_member</a>
                    <a href="javascript:void(0);" onclick="openAuthWithLang(); return false;" class="mobile-btn banner" name="key_sign_in">key_sign_in</a>
                </div>
                
                <div class="flex flex-wrap justify-center items-center gap-4 my-4 d-none d-lg-block">
                    <a href="javascript:void(0);" onclick="openAgreementWithLang(); return false;" class="btn btn-lg banner" target="_blank" name="key_become_member" style="background-color: #c8e600; color: #000000; font-size: 1.2rem; padding: 1rem 2rem; border-radius: 8px; font-weight: bold;">key_become_member</a>
                </div>
                <h1 class="slogan banner mt-xs-1" name='key_motivational'><i>key_motivational</i></h1>
            </header>
        </div>
    </div>
    <div id="packages" class="content full-width packages">
        <div class="container-fluid d-none d-sm-block shadow-bg"></div>
        <div class="container">
            <div class="col-sm-12">
                <?php
                // Load packages from MSSQL PackagesWebsite
                include_once __DIR__ . '/mssql_connection.php';
                include_once __DIR__ . '/mssql_packages_payments_helper.php';
                $packages = getPackagesWebsite('order_number ASC', 'ISNULL(order_number,0) > 0');

                // Demo fallback packages when DB is empty or unavailable
                if (empty($packages)) {
                    $packages = [
                        ['duration_month' => '1', 'price' => 150, 'old_price' => 0, 'name_eng' => 'Monthly', 'name_geo' => 'თვიური', 'description' => 'Full gym access', 'description_geo' => 'სრული წვდომა', 'deal' => ''],
                        ['duration_month' => '1', 'price' => 100, 'old_price' => 150, 'name_eng' => 'Student Monthly', 'name_geo' => 'სტუდენტური თვიური', 'description' => 'Student discount', 'description_geo' => 'სტუდენტური ფასდაკლება', 'deal' => 'STUDENT'],
                        ['duration_month' => '1', 'price' => 80, 'old_price' => 0, 'name_eng' => 'Morning Only', 'name_geo' => 'დილის საათები', 'description' => '8:00 - 14:00', 'description_geo' => '8:00 - 14:00', 'deal' => ''],
                        ['duration_month' => '3', 'price' => 350, 'old_price' => 450, 'name_eng' => 'Quarterly', 'name_geo' => 'კვარტალური', 'description' => 'Full gym access', 'description_geo' => 'სრული წვდომა', 'deal' => 'BEST'],
                        ['duration_month' => '3', 'price' => 250, 'old_price' => 350, 'name_eng' => 'Student Quarterly', 'name_geo' => 'სტუდენტური კვარტალური', 'description' => 'Student discount', 'description_geo' => 'სტუდენტური ფასდაკლება', 'deal' => 'STUDENT'],
                        ['duration_month' => '6', 'price' => 600, 'old_price' => 900, 'name_eng' => 'Semi-Annual', 'name_geo' => 'ნახევარწლიური', 'description' => 'Full gym access', 'description_geo' => 'სრული წვდომა', 'deal' => ''],
                        ['duration_month' => '6', 'price' => 750, 'old_price' => 900, 'name_eng' => 'Semi-Annual + Group', 'name_geo' => 'ნახევარწლიური + ჯგუფური', 'description' => 'Gym + group workouts', 'description_geo' => 'სრული + ჯგუფური ვარჯიშები', 'deal' => 'BEST'],
                        ['duration_month' => '12', 'price' => 1000, 'old_price' => 1800, 'name_eng' => 'Annual', 'name_geo' => 'წლიური', 'description' => 'Full gym access + locker', 'description_geo' => 'სრული წვდომა + საკეტი', 'deal' => 'BEST'],
                        ['duration_month' => '12', 'price' => 1400, 'old_price' => 2100, 'name_eng' => 'Annual Premium', 'name_geo' => 'წლიური პრემიუმ', 'description' => 'All-inclusive + personal trainer', 'description_geo' => 'სრული + პირადი მწვრთნელი', 'deal' => ''],
                    ];
                }
                ?>

                <?php if (!empty($packages)): ?>
                <div class="row">
                    <?php foreach ($packages as $p):
                        $price = isset($p['price']) ? (float)$p['price'] : 0;
                        $old = isset($p['old_price']) ? (float)$p['old_price'] : 0;
                        $hasDiscount = $old > $price && $old > 0;
                        $deal = isset($p['deal']) ? trim($p['deal']) : '';
                        $colCls = 'col-xl-4 col-lg-6 col-md-6';
                        $blockCls = 'price-block text-center banner pt-5 pb-5 pl-3 pr-3';
                        $duration = htmlspecialchars((string)($p['duration_month'] ?? ''));
                        $descGeo = htmlspecialchars((string)($p['description_geo'] ?: $p['name_geo'] ?: ''));
                        $descEn = htmlspecialchars((string)($p['description'] ?: $p['name_eng'] ?: ''));
                    ?>
                    <div class="<?php echo $colCls; ?>">
                        <div class="<?php echo $blockCls; ?>" onclick="openAgreementWithLang(); return false;" style="cursor: pointer;">
                            <h1 class="middle"><?php echo $duration; ?></h1>
                            <h2 class="mt-5" name='key_month'>key_month</h2>
                            <h3 class="mt-5 pkg-desc" data-geo="<?php echo $descGeo; ?>" data-en="<?php echo $descEn; ?>"></h3>

                            <?php if ($hasDiscount): ?>
                                <div class="mt-5 price-box-offer">
                                    <span class="original-price"><?php echo (int)$old; ?> <span name='key_currency'>key_currency</span></span>
                                    <span class="offer-price"><?php echo (int)$price; ?> <span name='key_currency'>key_currency</span></span>
                                </div>
                            <?php else: ?>
                                <div class="mt-5 price-box"><?php echo (int)$price; ?> <span name='key_currency'>key_currency</span></div>
                            <?php endif; ?>

                            <div class="price-cta" name='key_click_to_buy'>key_click_to_buy</div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="row"><div class="col-12"><p style="color:#fff;">Packages are temporarily unavailable.</p></div></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php include(__DIR__ . '/includes/google-reviews.php'); ?>
    
    <div id="contact" class="container-fluid text-center join banner pt-5 pb-5" style="background-color: #000000; color: #FFFFFF;">
        <div class="full-width join-shadow"></div>
        <h2 name='key_find' style="font-size: 2.5rem; font-weight: bold; color: #c8e600; text-transform: uppercase; letter-spacing: 1px;">key_find</h2>

        <div class="contact-info mt-4">

            <p style="font-size: 1.3rem; margin-top: 1.5rem; margin-bottom: 1.5rem;">
                <strong>Email:</strong> <a href="mailto:info@synergy-gym.ge" style="text-decoration: none; color: #FFFFFF;">info@synergy-gym.ge</a>
            </p>

            <!-- Mobile App Download Images -->
            <div style="margin: 3rem auto; padding: 2rem 0; max-width: 600px; display: flex; justify-content: center; align-items: center; gap: 30px; flex-wrap: wrap;">
                <a href="https://play.google.com/store/apps/details?id=ge.Synergy.gym" target="_blank" rel="noopener noreferrer" style="transition: all 0.3s ease; display: inline-block; filter: drop-shadow(0 4px 12px rgba(255, 255, 255, 0.3));" onmouseover="this.style.transform='scale(1.1) translateY(-5px)'; this.style.filter='drop-shadow(0 8px 20px rgba(255, 255, 255, 0.5))'" onmouseout="this.style.transform='scale(1) translateY(0)'; this.style.filter='drop-shadow(0 4px 12px rgba(255, 255, 255, 0.3))'">
                    <img src="img/android.png" alt="Download on Google Play" style="height: 80px; width: auto; border-radius: 8px;">
                </a>
                <a href="https://apps.apple.com/ge/app/Synergy-gym/id6752832860" target="_blank" rel="noopener noreferrer" style="transition: all 0.3s ease; display: inline-block; filter: drop-shadow(0 4px 12px rgba(255, 255, 255, 0.3));" onmouseover="this.style.transform='scale(1.1) translateY(-5px)'; this.style.filter='drop-shadow(0 8px 20px rgba(255, 255, 255, 0.5))'" onmouseout="this.style.transform='scale(1) translateY(0)'; this.style.filter='drop-shadow(0 4px 12px rgba(255, 255, 255, 0.3))'">
                    <img src="img/ios.png" alt="Download on App Store" style="height: 80px; width: auto; border-radius: 8px;">
                </a>
            </div>

            <div class="social-links" style="margin-top: 2rem;">
                <a href="https://www.facebook.com/SynergyGymTbilisi" target="_blank" style="font-size: 1.3rem; margin: 0 15px; text-decoration: none; color: #1877F2; transition: color 0.3s;" onmouseover="this.style.color='#4267B2'" onmouseout="this.style.color='#1877F2'">
                    <i class="fab fa-facebook-f fa-lg" style="margin-right: 8px;"></i>Facebook
                </a>
                <a href="https://www.instagram.com/synergy_gym_tbilisi/" target="_blank" style="font-size: 1.3rem; margin: 0 15px; text-decoration: none; color: #E4405F; transition: color 0.3s;" onmouseover="this.style.color='#C13584'" onmouseout="this.style.color='#E4405F'">
                    <i class="fab fa-instagram fa-lg" style="margin-right: 8px;"></i>Instagram
                </a>
            </div>
        </div>
    </div>
    <div class="container-fluid map" style="position:relative;">
        <!-- Google Maps embed -->
        <iframe src="https://www.google.com/maps?q=Tbilisi%2C%20Georgia&z=12&output=embed" width="100%" height="430" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        <a href="https://www.google.com/maps/search/?api=1&query=Tbilisi%2C%20Georgia" target="_blank" rel="noopener noreferrer"
           style="display:none;position:absolute;top:0;left:0;width:100%;height:100%;z-index:10;"
           class="map-mobile-overlay" aria-label="Open Tbilisi in Google Maps"></a>
    </div>
    <style>
        @media (max-width: 768px) {
            .map-mobile-overlay { display: block !important; }
        }
    </style>
    <div class="container-fluid text-center footer" style="background-color: #1a1a1a; color: #ffffff; padding: 2rem 0; border-top: 2px solid #c8e600;">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <p style="margin-bottom: 1rem; font-size: 1rem; color: #e0e0e0;">
                        © 2025 Synergy Gym. All rights reserved.
                    </p>
                    <div class="footer-links" style="margin-bottom: 1rem;">
                        <a href="terms.php" style="color: #c8e600; text-decoration: none; margin: 0 15px; font-weight: 500; transition: color 0.3s;" onmouseover="this.style.color='#a8c200'" onmouseout="this.style.color='#c8e600'">
                            Terms and Conditions
                        </a>
                        <span style="color: #666;">|</span>
                        <a href="privacy.php" style="color: #c8e600; text-decoration: none; margin: 0 15px; font-weight: 500; transition: color 0.3s;" onmouseover="this.style.color='#a8c200'" onmouseout="this.style.color='#c8e600'">
                            Privacy Policy
                        </a>                       
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="js/popper.min.js" crossorigin="anonymous" defer></script>
<script src="js/bootstrap.min.js" crossorigin="anonymous" defer></script>

<script>
// Function to open agreement page with current language
function openAgreementWithLang(event) {
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }
    const currentLang = window.localStorage.getItem('ActiveLanguage') || 'ka';
    const url = `agreement.php?lang=${currentLang}`;
    window.open(url, '_blank');
    return false;
}

function openAuthWithLang(event) {
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }
    const currentLang = window.localStorage.getItem('ActiveLanguage') || 'ka';
    const url = `user_profile/auth.php?lang=${currentLang}`;
    window.location.href = url;
    return false;
}
</script>

<script>
// Set dynamic package descriptions from data attributes by current language
document.addEventListener('DOMContentLoaded', function() {
    var lang = (localStorage && localStorage.getItem('ActiveLanguage')) || 'ka';
    document.querySelectorAll('.pkg-desc').forEach(function(el){
        var text = (lang === 'en') ? el.getAttribute('data-en') : el.getAttribute('data-geo');
        el.textContent = text || '';
    });
    // Also react to language changes (SetLanguage updates localStorage without reload)
    var last = lang;
    function updatePkgDesc() {
        var cur = (localStorage && localStorage.getItem('ActiveLanguage')) || 'ka';
        document.querySelectorAll('.pkg-desc').forEach(function(el){
            var t = (cur === 'en') ? el.getAttribute('data-en') : el.getAttribute('data-geo');
            el.textContent = t || '';
        });
    }
    setInterval(function(){
        var cur = (localStorage && localStorage.getItem('ActiveLanguage')) || 'ka';
        if (cur !== last) { last = cur; updatePkgDesc(); }
    }, 600);
});
</script>

</body>
</html>
