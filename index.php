<?php
$lang = isset($_GET['lang']) ? $_GET['lang'] : 'ka';
// Redirect disabled for local development - show packages page directly
// header("Location: agreement.php?lang=" . $lang);
// exit;
?>
<!DOCTYPE html>
<html lang="ka">
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="google-site-verification" content="cuzzQ9TWsviGVMIV1TXJX7cI4eWu9Y6CnRYiVYX0aqw" />
    <meta name="title" content="Alex Fit – Gym &amp; Boxing in Kobuleti | Fitness Club">
    <meta name="description" content="Discover Alex Fit in Kobuleti. Premier fitness club combining gym training and boxing. State-of-the-art equipment, personal training, boxing classes. Join us today.">
    <meta name="keywords" content="Alex Fit, gym in Kobuleti, boxing kobuleti, fitness kobuleti, gym kobuleti, personal trainer kobuleti, boxing classes georgia, weight loss kobuleti, bodybuilding georgia, women's fitness kobuleti, affordable gym kobuleti, ფიტნესი ქობულეთში, ბოქსი ქობულეთი, დარბაზი ქობულეთი, ჯიმი ქობულეთი, affordable gym in kobuleti, fitness club Kobuleti">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, viewport-fit=cover">
    
    <!-- Performance and caching meta tags -->
    <meta http-equiv="Cache-Control" content="public, max-age=3600">
    <meta http-equiv="Expires" content="<?php echo gmdate('D, d M Y H:i:s', time() + 3600); ?> GMT">
    <meta name="format-detection" content="telephone=no">
    <meta name="theme-color" content="#22c55e">
    
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
    <link rel="preload" href="img/gym.webp" as="image" type="image/webp">

    <!-- DNS prefetch for external resources -->
    <link rel="dns-prefetch" href="//ajax.googleapis.com">
    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    <link rel="dns-prefetch" href="//cdn.web-fonts.ge">
    <link rel="preconnect" href="https://ajax.googleapis.com" crossorigin>

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
    <title>Alex Fit – Gym &amp; Boxing in Kobuleti</title>

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
            background: linear-gradient(135deg, #0d1a11 0%, #1a2820 100%);
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
            border: 3px solid rgba(34, 197, 94, 0.3);
            border-top: 3px solid #22c55e;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        .loader-text {
            color: #22c55e;
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
                background: linear-gradient(135deg, #0d1a11 0%, #1a2820 100%);
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
                background: linear-gradient(135deg, #0d1a11 0%, #1a2820 100%);
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
                border: 4px solid rgba(34, 197, 94, 0.3);
                border-top: 4px solid #22c55e;
                border-radius: 50%;
                animation: spin 1s linear infinite;
            }
            
            .desktop-loader .loader-text {
                color: #22c55e;
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
        
        /* Fix white background flash - use dark placeholder */
        body {
            font-family: 'Inter', sans-serif;
            background-color: #1a1a1a; /* Dark placeholder while image loads */
        }
        .price-box {
            background-color: #166534; color: #ffffff;
            padding: 10px 20px;
            border-radius: 8px;
            display: inline-block;
            font-weight: bold;
            font-size: 1.2rem;
        }

        /* Enhanced pricing styles for deals */
        .price-box-offer {
            background: linear-gradient(135deg, #166534 0%, #14532d 100%);
            color: #000000;
            padding: 15px 25px;
            border-radius: 12px;
            display: inline-block;
            font-weight: bold;
            position: relative;
            box-shadow: 0 8px 25px rgba(34, 197, 94, 0.3);
            border: 2px solid #22c55e;
            animation: pulse-glow 2s ease-in-out infinite;
        }

        .price-box-offer::before {
            content: '';
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            background: linear-gradient(45deg, #22c55e, #166534, #16a34a, #22c55e);
            background-size: 400% 400%;
            border-radius: 12px;
            z-index: -1;
            animation: gradient-border 3s ease infinite;
        }

        @keyframes pulse-glow {
            0%, 100% {
                box-shadow: 0 8px 25px rgba(34, 197, 94, 0.3);
                transform: scale(1);
            }
            50% {
                box-shadow: 0 12px 35px rgba(34, 197, 94, 0.5);
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
            background-color: #22c55e; /* Same yellow as your other buttons */
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
            border: 2px solid #22c55e;
            display: inline-block;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
        }

        .price-block:hover .price-cta {
            background-color: #166534; color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(34, 197, 94, 0.4);
            border-color: #16a34a;
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
            margin-bottom: 12px;
        }

        .packages .price-block {
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            width: 100%;
            min-height: 360px;
            position: relative;
            padding: 34px 24px 24px 24px !important;
        }

        /* Ensure all price boxes have consistent spacing */
        .packages [class*='col-'] {
            margin-top: 12px !important;
            margin-bottom: 12px !important;
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
            background-color: #166534; color: #ffffff;
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
            background-color: #1e40af;
            color: #000000;
            text-decoration: none;
            transform: translateY(-1px);
            box-shadow: 0 3px 8px rgba(30, 64, 175, 0.35);
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
                    background-color: rgba(34, 197, 94, 0.95) !important;
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
                background-color: #22c55e !important;
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
                background-color: #16a34a !important;
                color: #000000 !important;
                text-decoration: none !important;
                transform: translateY(-1px) !important;
                box-shadow: 0 3px 8px rgba(34, 197, 94, 0.3) !important;
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
                background: #22c55e;
                background-size: 300% 300%;
                color: #000000;
                padding: 15px 25px;
                border-radius: 12px;
                font-size: 1.1rem;
                font-weight: 700;
                text-decoration: none;
                transition: all 0.3s ease;
                border: 2px solid #22c55e;
                cursor: pointer;
                display: inline-block;
                text-align: center;
                margin: 0 !important;
                white-space: nowrap;
                text-transform: uppercase;
                letter-spacing: 1px;
                animation: pulseGlow 2s ease-in-out infinite;
                box-shadow: 0 8px 25px rgba(34, 197, 94, 0.4);
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
                box-shadow: 0 12px 35px rgba(34, 197, 94, 0.6);
                border-color: #22c55e;
            }
            
            /* Log In Button - Simple and clean */
            .mobile-btn:last-child {
                background-color: #166534; color: #ffffff;
                padding: 12px 20px;
                border-radius: 8px;
                font-size: 0.95rem;
                font-weight: 600;
                text-decoration: none;
                transition: all 0.3s ease;
                border: 2px solid #22c55e;
                cursor: pointer;
                display: inline-block;
                text-align: center;
                margin: 0 !important;
                white-space: nowrap;
            }
            
            .mobile-btn:last-child:hover {
                background-color: #16a34a;
                color: #000000;
                text-decoration: none;
                transform: translateY(-1px);
                box-shadow: 0 3px 8px rgba(34, 197, 94, 0.3);
            }
        }

        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        @keyframes pulseGlow {
            0%, 100% {
                box-shadow: 0 8px 25px rgba(34, 197, 94, 0.4);
                transform: scale(1);
            }
            50% {
                box-shadow: 0 12px 35px rgba(34, 197, 94, 0.6);
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
                background-color: #22c55e !important;
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
                background-color: #16a34a !important;
                color: #000000 !important;
                text-decoration: none !important;
                transform: translateY(-1px) !important;
                box-shadow: 0 3px 8px rgba(34, 197, 94, 0.3) !important;
            }
            
            /* Better button layout for tablets */
            .mobile-buttons-container .mobile-btn:first-child {
                background: #22c55e !important;
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

    <style>
    /* ── Fixed Top Navbar ─────────────────────────────────────────── */
    .site-navbar {
        position: fixed;
        top: 0; left: 0;
        width: 100%;
        z-index: 1000;
        background: rgba(8, 14, 10, 0.96);
        backdrop-filter: blur(14px);
        -webkit-backdrop-filter: blur(14px);
        border-bottom: 1px solid rgba(34, 197, 94, 0.15);
        box-shadow: 0 2px 20px rgba(0, 0, 0, 0.5);
    }
    .site-navbar-inner {
        display: flex;
        align-items: center;
        height: 82px;
        padding: 0 24px;
        max-width: 1400px;
        margin: 0 auto;
        position: relative;
    }

    /* Logo – left */
    .navbar-logo-link {
        flex-shrink: 0;
        display: flex;
        align-items: center;
        text-decoration: none;
    }
    .navbar-logo-img {
        height: 70px;
        width: auto;
        display: block;
        filter: drop-shadow(0 2px 10px rgba(34, 197, 94, 0.35));
        transition: filter 0.25s ease, transform 0.25s ease;
    }
    .navbar-logo-img:hover {
        filter: drop-shadow(0 4px 18px rgba(34, 197, 94, 0.60));
        transform: scale(1.04);
    }

    /* Desktop center links – truly centered on the page */
    .navbar-desktop-links {
        position: absolute;
        left: 50%;
        transform: translateX(-50%);
        display: flex;
        align-items: center;
        gap: 8px;
        pointer-events: auto;
    }
    .navbar-link {
        color: #c8c8c8;
        font-size: 0.95rem;
        font-weight: 600;
        padding: 8px 20px;
        border-radius: 6px;
        text-decoration: none;
        letter-spacing: 0.4px;
        transition: color 0.2s ease, background 0.2s ease;
        white-space: nowrap;
    }
    .navbar-link:hover {
        color: #4ade80;
        background: rgba(74, 222, 128, 0.10);
        text-decoration: none;
    }
    @keyframes nav-btn-shimmer {
        0%   { background-position: 200% center; }
        100% { background-position: -200% center; }
    }
    .navbar-register {
        background: linear-gradient(90deg, #16a34a 0%, #4ade80 40%, #22c55e 55%, #4ade80 70%, #16a34a 100%);
        background-size: 250% auto;
        color: #000 !important;
        font-size: 0.92rem;
        font-weight: 700;
        padding: 9px 24px;
        border-radius: 6px;
        border: 1px solid rgba(74, 222, 128, 0.70);
        text-decoration: none;
        letter-spacing: 0.8px;
        text-transform: uppercase;
        white-space: nowrap;
        box-shadow: 0 4px 18px rgba(74, 222, 128, 0.30);
        animation: nav-btn-shimmer 3.5s linear infinite;
        transition: box-shadow 0.22s ease, transform 0.22s ease;
    }
    .navbar-register:hover {
        color: #000 !important;
        border-color: #4ade80;
        text-decoration: none;
        transform: translateY(-2px);
        box-shadow: 0 8px 28px rgba(74, 222, 128, 0.55);
    }

    /* Auth button – right group */
    .navbar-auth-btn {
        color: #d0d0d0;
        font-size: .88rem;
        font-weight: 600;
        padding: 7px 28px;
        border-radius: 6px;
        border: 1px solid rgba(56,189,248,0.38);
        background: rgba(56,189,248,0.08);
        text-decoration: none;
        letter-spacing: .3px;
        white-space: nowrap;
        transition: all .2s ease;
    }
    .navbar-auth-btn:hover {
        color: #38bdf8;
        border-color: #38bdf8;
        background: rgba(56,189,248,0.16);
        text-decoration: none;
    }
    .nm-auth {
        color: #c8c8c8;
        font-size: 1rem;
        font-weight: 600;
        padding: 11px 16px;
        border-radius: 6px;
        text-decoration: none;
        border: 1px solid rgba(56,189,248,0.30);
        background: rgba(56,189,248,0.07);
        text-align: center;
        transition: color .2s, background .2s;
    }
    .nm-auth:hover { color: #38bdf8; background: rgba(56,189,248,0.14); text-decoration: none; }

    /* Right group – lang + hamburger */
    .navbar-right-group {
        flex-shrink: 0;
        display: flex;
        align-items: center;
        gap: 12px;
        margin-left: auto;
    }
    .navbar-lang-btn {
        color: #d0d0d0;
        font-size: 0.88rem;
        font-weight: 600;
        padding: 7px 28px;
        border-radius: 6px;
        border: 1px solid rgba(74, 222, 128, 0.38);
        background: rgba(74, 222, 128, 0.08);
        transition: all 0.2s ease;
        letter-spacing: 0.3px;
        white-space: nowrap;
    }
    .navbar-lang-btn:hover {
        color: #4ade80;
        border-color: #4ade80;
        background: rgba(74, 222, 128, 0.16);
        box-shadow: 0 0 10px rgba(74, 222, 128, 0.20);
    }

    /* Hamburger – hidden on desktop */
    .navbar-hamburger {
        display: none;
        flex-direction: column;
        justify-content: center;
        gap: 5px;
        width: 36px;
        height: 36px;
        padding: 0;
        background: none;
        border: none;
        cursor: pointer;
    }
    .navbar-hamburger span {
        display: block;
        width: 24px;
        height: 2px;
        background: #c8c8c8;
        border-radius: 2px;
        transition: background 0.2s ease;
    }
    .navbar-hamburger:hover span { background: #22c55e; }

    /* Mobile dropdown – hidden by default */
    .navbar-mobile-menu {
        display: none;
        flex-direction: column;
        padding: 12px 20px 18px;
        border-top: 1px solid rgba(34, 197, 94, 0.12);
        background: rgba(6, 12, 8, 0.98);
        gap: 4px;
    }
    .navbar-mobile-menu.open { display: flex; }
    .nm-link {
        color: #c8c8c8;
        font-size: 1rem;
        font-weight: 600;
        padding: 11px 16px;
        border-radius: 6px;
        text-decoration: none;
        transition: color 0.2s, background 0.2s;
    }
    .nm-link:hover {
        color: #ffffff;
        background: rgba(34, 197, 94, 0.10);
        text-decoration: none;
    }
    .nm-register {
        background: linear-gradient(90deg, #16a34a 0%, #4ade80 50%, #16a34a 100%);
        background-size: 200% auto;
        color: #000 !important;
        font-size: 0.95rem;
        font-weight: 700;
        padding: 12px 20px;
        border-radius: 6px;
        border: 1px solid rgba(74, 222, 128, 0.60);
        text-decoration: none;
        letter-spacing: 0.8px;
        text-transform: uppercase;
        text-align: center;
        margin: 6px 0;
        box-shadow: 0 4px 16px rgba(74, 222, 128, 0.28);
        animation: nav-btn-shimmer 3.5s linear infinite;
        transition: box-shadow 0.22s ease, transform 0.22s ease;
    }
    .nm-register:hover {
        color: #000 !important;
        text-decoration: none;
        box-shadow: 0 6px 24px rgba(74, 222, 128, 0.50);
    }
    .nm-lang {
        color: #a0a0a0;
        font-size: 0.88rem;
        font-weight: 600;
        padding: 10px 16px;
        border-radius: 6px;
        border: 1px solid rgba(34, 197, 94, 0.22);
        text-align: center;
        margin-top: 6px;
        transition: color 0.2s, border-color 0.2s;
    }
    .nm-lang:hover { color: #22c55e; border-color: #22c55e; }

    /* Show hamburger, hide desktop links on small screens */
    @media (max-width: 900px) {
        .navbar-desktop-links { display: none; }
        .navbar-hamburger { display: flex; }
    }
    @media (min-width: 901px) {
        .navbar-mobile-menu { display: none !important; }
    }

    /* ── Hero / Top-Block ─────────────────────────────────────────── */
    .site-header {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 134px 20px 90px; /* 68px navbar + 66px breathing room */
        position: relative;
        z-index: 2;
    }

    @keyframes logo-float {
        0%, 100% { transform: translateY(0px);   filter: drop-shadow(0 6px 28px rgba(74,222,128,0.42)) drop-shadow(0 2px 8px rgba(0,0,0,0.7)); }
        50%       { transform: translateY(-10px); filter: drop-shadow(0 14px 40px rgba(74,222,128,0.62)) drop-shadow(0 4px 12px rgba(0,0,0,0.8)); }
    }
    @keyframes hero-btn-shimmer {
        0%   { background-position: 200% center; }
        100% { background-position: -200% center; }
    }
    @keyframes hero-btn-pulse {
        0%, 100% { box-shadow: 0 8px 28px rgba(74,222,128,0.40); }
        50%       { box-shadow: 0 14px 48px rgba(74,222,128,0.70); }
    }

    /* Logo */
    .hero-logo-wrap { margin-bottom: 36px; }
    .hero-logo {
        width: 210px;
        height: auto;
        display: block;
        animation: logo-float 5s ease-in-out infinite;
    }
    .hero-logo:hover {
        animation: none;
        transform: scale(1.08) translateY(-6px);
        filter: drop-shadow(0 16px 50px rgba(74, 222, 128, 0.70))
                drop-shadow(0 4px 14px rgba(0, 0, 0, 0.85));
    }

    /* CTA */
    .hero-cta {
        display: flex;
        gap: 14px;
        flex-wrap: wrap;
        justify-content: center;
        margin-bottom: 55px;
    }
    .hero-cta-btn {
        background: linear-gradient(90deg, #16a34a 0%, #4ade80 35%, #22c55e 50%, #4ade80 65%, #16a34a 100%);
        background-size: 250% auto;
        color: #000;
        padding: 17px 52px;
        border-radius: 8px;
        font-size: 1.18rem;
        font-weight: 700;
        text-decoration: none;
        border: 2px solid rgba(74, 222, 128, 0.70);
        letter-spacing: 1.8px;
        text-transform: uppercase;
        display: inline-block;
        animation: hero-btn-shimmer 3s linear infinite, hero-btn-pulse 2.5s ease-in-out infinite;
        transition: transform 0.28s ease, box-shadow 0.28s ease;
    }
    .hero-cta-btn:hover {
        color: #000;
        transform: translateY(-4px) scale(1.03);
        box-shadow: 0 18px 55px rgba(74, 222, 128, 0.75);
        text-decoration: none;
        border-color: #4ade80;
    }

    /* Slogan */
    .slogan {
        color: #ffffff;
        text-align: center;
        text-shadow: 0 2px 24px rgba(0, 0, 0, 0.75);
        margin: 0;
        padding: 0 20px;
    }

    /* Mobile hero adjustments */
    @media (max-width: 900px) {
        .site-header { padding: 110px 16px 55px; }
        .hero-logo { width: 160px; }
        .hero-logo-wrap { margin-bottom: 28px; }
        .hero-cta-btn { padding: 14px 30px; font-size: 1rem; letter-spacing: 1px; }
    }
    @media (max-width: 480px) {
        .hero-logo { width: 130px; }
        .hero-cta { flex-direction: column; align-items: center; }
        .hero-cta-btn { width: 240px; text-align: center; }
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
                '/img/gym.png'
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
            // no external icon prefetch needed
            document.head.appendChild(linkPrefetch);
        });
    </script>

    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "ExerciseGym",
      "name": "Alex Fit",
      "description": "Alex Fit in Kobuleti — gym and boxing training centre with modern equipment, personal training, and boxing classes.",
      "url": "/",
      "logo": "/img/gym.png",
      "telephone": "+995599061572",
      "priceRange": "$$",
      "address": {
        "@type": "PostalAddress",
        "streetAddress": "Shota Rustaveli 170-25",
        "addressLocality": "Kobuleti",
        "postalCode": "6200",
        "addressCountry": "GE"
      },
      "geo": {
        "@type": "GeoCoordinates",
        "latitude": "41.8323",
        "longitude": "41.7712"
      },
      "openingHoursSpecification": [
        {
          "@type": "OpeningHoursSpecification",
          "dayOfWeek": ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday"],
          "opens": "09:00",
          "closes": "00:00"
        },
        { "@type": "OpeningHoursSpecification", "dayOfWeek": "Saturday", "opens": "09:00", "closes": "00:00" },
        { "@type": "OpeningHoursSpecification", "dayOfWeek": "Sunday", "opens": "09:00", "closes": "00:00" }
      ],
      "sameAs": [
        "https://www.facebook.com/Alexfitnesskobulrti/",
        "https://www.instagram.com/alex_fitness_kobuleti/"
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

    <!-- Mobile Tonus-style layout overrides -->
    <style>
    @media (max-width: 900px) {
        .site-navbar { display: none !important; }

        /* Language pill — top right */
        .mobile-lang-fixed {
            position: fixed;
            top: max(16px, env(safe-area-inset-top, 16px));
            right: max(12px, env(safe-area-inset-right, 12px));
            z-index: 1100;
            background: rgba(34, 197, 94, 0.88);
            backdrop-filter: blur(6px);
            color: #000 !important;
            padding: 7px 14px;
            border-radius: 20px;
            font-size: 0.82rem;
            font-weight: 700;
            cursor: pointer;
            display: block;
            border: 1px solid rgba(74, 222, 128, 0.4);
            letter-spacing: 0.3px;
            white-space: nowrap;
            box-sizing: border-box;
            max-width: calc(100vw - 24px);
        }

        /* Hero full-screen centered column */
        .top-block { min-height: 100svh; display: flex; align-items: stretch; }
        .site-header {
            min-height: 100svh;
            width: 100%;
            padding: 0 24px 100px !important;
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 0 !important;
        }

        /* Logo */
        .hero-logo-wrap { margin-bottom: 28px !important; }
        .hero-logo { width: 140px !important; filter: drop-shadow(0 4px 20px rgba(34,197,94,0.4)); }

        /* Prices / Contact — compact nav buttons */
        .mobile-hero-nav-btn {
            display: block;
            background: #22c55e;
            color: #000 !important;
            padding: 12px 0;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 700;
            text-decoration: none !important;
            width: 240px;
            text-align: center;
            margin-bottom: 10px;
            letter-spacing: 0.5px;
        }

        /* Bigger gap after Contact before Registration group */
        .mobile-hero-nav-btn + .mobile-hero-nav-btn { margin-bottom: 32px; }

        /* JOIN NOW */
        .hero-cta {
            width: 100%;
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
            gap: 0 !important;
            margin-bottom: 0 !important;
        }
        .hero-cta-btn {
            background: linear-gradient(135deg, #15803d 0%, #22c55e 45%, #4ade80 75%, #22c55e 100%) !important;
            background-size: 250% 250% !important;
            color: #000 !important;
            padding: 16px 0 !important;
            border-radius: 12px !important;
            font-size: 1.1rem !important;
            font-weight: 800 !important;
            letter-spacing: 2px !important;
            width: 280px !important;
            text-align: center !important;
            box-shadow: 0 8px 28px rgba(34, 197, 94, 0.45) !important;
            animation: none !important;
            margin-bottom: 8px !important;
        }

        /* Log In — outlined style, same size as Registration */
        .mobile-hero-login-btn {
            display: block;
            background: transparent;
            color: #22c55e !important;
            padding: 16px 0;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 800;
            text-decoration: none !important;
            width: 280px;
            text-align: center;
            border: 2px solid #22c55e;
            letter-spacing: 2px;
        }

        /* Slogan at bottom */
        .slogan {
            margin-top: 24px !important;
            font-size: 0.95rem !important;
            opacity: 0.8 !important;
            letter-spacing: 0.5px !important;
        }

        .mobile-buttons-container { display: none !important; }
    }
    @media (min-width: 901px) {
        .mobile-lang-fixed { display: none !important; }
        .mobile-hero-nav-btn { display: none !important; }
        .mobile-hero-login-btn { display: none !important; }
    }
    </style>

</head>
<body>

<!-- Mobile fixed language button (Tonus-style, top-right) -->
<div class="mobile-lang-fixed pointer" onclick="SetLanguage()">
    <span name="key_lang">key_lang</span>
</div>

<!-- Mobile Loader - Only visible on mobile devices -->
<div class="mobile-loader" id="mobileLoader">
    <img src="img/gym.png" alt="Alex Fit" class="loader-logo" onerror="this.style.display='none';">
    <div class="loader-spinner"></div>
    <div class="loader-text">Loading...</div>
</div>

<!-- Desktop Loader - Only visible on desktop devices -->
<div class="desktop-loader" id="desktopLoader">
    <img src="img/gym.png" alt="Alex Fit" class="loader-logo" onerror="this.style.display='none';">
    <div class="loader-spinner"></div>
    <div class="loader-text">Loading...</div>
</div>


<div id="cont" class="content-visible">

    <!-- ── Fixed Top Navbar ─────────────────────────────────────────────── -->
    <nav class="site-navbar" id="siteNavbar">
        <div class="site-navbar-inner">

            <!-- Logo – left -->
            <a href="/" class="navbar-logo-link">
                <picture>
                    <source srcset="img/gym.webp" type="image/webp">
                    <img src="img/gym.png" alt="Alex Fit" class="navbar-logo-img" loading="eager"
                         onerror="this.onerror=null;this.src='https://placehold.co/55x55/0d1a11/22c55e?text=AF';">
                </picture>
            </a>

            <!-- Desktop links + register button in center -->
            <div class="navbar-desktop-links">
                <a href="#packages" class="navbar-link menu_links" name="key_packages">key_packages</a>
                <a href="javascript:void(0);" onclick="openAgreementWithLang(); return false;"
                   class="navbar-register banner" name="key_become_member">key_become_member</a>
                <a href="#contact"  class="navbar-link menu_links" name="key_contact">key_contact</a>
            </div>

            <!-- Language + login + hamburger – right -->
            <div class="navbar-right-group">
                <a href="javascript:void(0);" onclick="openAuthWithLang(); return false;"
                   class="navbar-auth-btn" name="key_sign_in">key_sign_in</a>
                <div class="navbar-lang-btn pointer" onclick="SetLanguage()">
                    <span name="key_lang">key_lang</span>
                </div>
                <button class="navbar-hamburger" id="navToggle"
                        onclick="document.getElementById('navMobileMenu').classList.toggle('open')">
                    <span></span><span></span><span></span>
                </button>
            </div>
        </div>

        <!-- Mobile dropdown -->
        <div class="navbar-mobile-menu" id="navMobileMenu">
            <a href="#packages" class="nm-link menu_links" name="key_packages">key_packages</a>
            <a href="javascript:void(0);" onclick="openAgreementWithLang(); return false;"
               class="nm-register banner" name="key_become_member">key_become_member</a>
            <a href="#contact"  class="nm-link menu_links" name="key_contact">key_contact</a>
            <a href="javascript:void(0);" onclick="openAuthWithLang(); return false;"
               class="nm-auth" name="key_sign_in">key_sign_in</a>
            <div class="nm-lang pointer" onclick="SetLanguage()">
                <span name="key_lang">key_lang</span>
            </div>
        </div>
    </nav>

    <!-- ── Hero / Top Block ─────────────────────────────────────────────── -->
    <div class="container-fluid top-block no-padding">
        <header class="site-header">

            <!-- Logo – large and centered -->
            <div class="hero-logo-wrap">
                <a href="/"><picture>
                    <source srcset="img/gym.webp" type="image/webp">
                    <img src="img/gym.png" alt="Alex Fit" class="hero-logo" fetchpriority="high"
                        onerror="this.onerror=null;this.src='https://placehold.co/220x220/0d1a11/22c55e?text=Logo';">
                </picture></a>
            </div>

            <!-- Mobile nav buttons: Prices + Contact (hidden on desktop) -->
            <a href="#packages" class="mobile-hero-nav-btn menu_links" name="key_packages">key_packages</a>
            <a href="#contact"  class="mobile-hero-nav-btn menu_links" name="key_contact">key_contact</a>

            <!-- Big centered CTA -->
            <div class="hero-cta">
                <a href="javascript:void(0);" onclick="openAgreementWithLang(); return false;"
                   class="hero-cta-btn banner" name="key_become_member">key_become_member</a>
            </div>

            <!-- Mobile Log In button (hidden on desktop) -->
            <a href="javascript:void(0);" onclick="openAuthWithLang(); return false;"
               class="mobile-hero-login-btn" name="key_sign_in">key_sign_in</a>

            <!-- Slogan -->
            <h1 class="slogan banner" name="key_motivational"><i>key_motivational</i></h1>

        </header>
    </div>
    <div id="packages" class="content full-width packages">
        <div class="container-fluid d-none d-sm-block shadow-bg"></div>
        <div class="packages-caption">
            <h2 name="key_gym_name">Alex Fitness</h2>
            <p name="key_choose_plan">აირჩიეთ თქვენი გეგმა &bull; Choose Your Plan</p>
        </div>
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
                        // Structured duration (day/week/month/year) with legacy fallback.
                        $durationParts = websitePackageDurationParts($p);
                        $duration = htmlspecialchars((string)$durationParts['value']);
                        $durationKeyMap = ['day' => 'key_days', 'week' => 'key_week', 'month' => 'key_month', 'year' => 'key_year'];
                        $durationLabelKey = $durationKeyMap[$durationParts['type']] ?? 'key_month';
                        $descGeo = htmlspecialchars((string)($p['description_geo'] ?: $p['name_geo'] ?: ''));
                        $descEn = htmlspecialchars((string)($p['description'] ?: $p['name_eng'] ?: ''));
                    ?>
                    <div class="<?php echo $colCls; ?>">
                        <div class="<?php echo $blockCls; ?>" onclick="openAgreementWithLang(); return false;" style="cursor: pointer;">
                            <h1 class="middle"><?php echo $duration; ?></h1>
                            <h2 class="mt-5" name='<?php echo $durationLabelKey; ?>'><?php echo $durationLabelKey; ?></h2>
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
        <h2 name='key_find' style="font-size: 2.5rem; font-weight: bold; color: #22c55e; text-transform: uppercase; letter-spacing: 1px;">key_find</h2>

        <div class="contact-info mt-4">

            <p style="font-size: 1.4rem; margin: 1.2rem 0;">
                <a href="tel:+995599061572"
                   style="text-decoration:none; color:#4ade80; font-weight:700; letter-spacing:1px; transition:color 0.2s;"
                   onmouseover="this.style.color='#22c55e'" onmouseout="this.style.color='#4ade80'">
                    <i class="fas fa-phone-alt" style="margin-right:10px;"></i>+995 599 061 572
                </a>
            </p>

            <div style="display:inline-flex; align-items:center; gap:18px; background:rgba(74,222,128,0.07); border:1px solid rgba(74,222,128,0.25); border-radius:50px; padding:10px 28px; margin:0.5rem 0 1.5rem;">
                <span style="color:#4ade80; font-size:1.4rem;"><i class="fas fa-clock"></i></span>
                <div style="text-align:left; line-height:1.35;">
                    <div style="color:#ececec; font-size:1.25rem; font-weight:800; letter-spacing:1px;">09:00 – 00:00</div>
                    <div style="color:#7a7a7a; font-size:0.82rem; letter-spacing:0.5px; text-transform:uppercase;" name="key_every_day">Every Day</div>
                </div>
            </div>

            <div class="social-links" style="margin-top: 2rem;">
                <a href="https://www.facebook.com/Alexfitnesskobulrti/" target="_blank" rel="noopener noreferrer"
                   style="font-size:1.3rem; margin:0 15px; text-decoration:none; color:#1877F2; transition:color 0.3s;"
                   onmouseover="this.style.color='#60a5fa'" onmouseout="this.style.color='#1877F2'">
                    <i class="fab fa-facebook-f fa-lg" style="margin-right:8px;"></i>Facebook
                </a>
                <a href="https://www.instagram.com/alex_fitness_kobuleti/" target="_blank" rel="noopener noreferrer"
                   style="font-size:1.3rem; margin:0 15px; text-decoration:none; color:#E4405F; transition:color 0.3s;"
                   onmouseover="this.style.color='#f472b6'" onmouseout="this.style.color='#E4405F'">
                    <i class="fab fa-instagram fa-lg" style="margin-right:8px;"></i>Instagram
                </a>
                <a href="https://t.me/alex_fitness_kobuleti" target="_blank" rel="noopener noreferrer"
                   style="font-size:1.3rem; margin:0 15px; text-decoration:none; color:#27A1DE; transition:color 0.3s;"
                   onmouseover="this.style.color='#7dd3fc'" onmouseout="this.style.color='#27A1DE'">
                    <i class="fab fa-telegram-plane fa-lg" style="margin-right:8px;"></i>Telegram
                </a>
            </div>
        </div>
    </div>
    <!-- ── Map ── -->
    <style>
        .map-wrap {
            position: relative;
            overflow: hidden;
            border-top: 2px solid rgba(74,222,128,0.25);
            border-bottom: 2px solid rgba(74,222,128,0.25);
        }
        .map-wrap iframe {
            display: block;
            width: 100%;
            height: 420px;
            border: 0;
            filter: invert(90%) hue-rotate(165deg) saturate(0.6) brightness(0.85);
        }
        .map-wrap { overflow: hidden; width: 100%; box-sizing: border-box; }
        .map-overlay-card {
            position: absolute;
            bottom: 24px;
            left: 50%;
            transform: translateX(-50%);
            max-width: calc(100% - 32px);
            background: rgba(7,13,9,0.90);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(74,222,128,0.30);
            border-radius: 12px;
            padding: 14px 20px;
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px 12px;
            white-space: normal;
            z-index: 5;
            box-shadow: 0 8px 28px rgba(0,0,0,0.55);
        }
        .map-overlay-card i {
            color: #4ade80;
            font-size: 1.2rem;
            flex-shrink: 0;
        }
        .map-overlay-card span {
            color: #e8e8e8;
            font-size: 0.95rem;
            font-weight: 600;
            letter-spacing: 0.3px;
        }
        .map-overlay-card a {
            color: #38bdf8;
            font-size: 0.82rem;
            font-weight: 600;
            text-decoration: none;
            margin-left: 8px;
            transition: color 0.2s;
        }
        .map-overlay-card a:hover { color: #7dd3fc; }
        /* mobile: make iframe tappable */
        .map-mobile-link {
            display: none;
            position: absolute;
            inset: 0;
            z-index: 10;
        }
        @media (max-width: 768px) {
            .map-mobile-link { display: block; }
            .map-wrap iframe { height: 320px; }
            .map-overlay-card { font-size: 0.85rem; padding: 10px 16px; bottom: 14px; }
        }
    </style>

    <div class="map-wrap">
        <iframe
            src="https://www.google.com/maps?q=41.8457672,41.7811427&z=17&output=embed"
            allowfullscreen=""
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade">
        </iframe>
        <!-- Floating address card -->
        <div class="map-overlay-card">
            <i class="fas fa-map-marker-alt"></i>
            <span name="key_address">Shota Rustaveli 170-25, Kobuleti 6200</span>
            <a href="https://www.google.com/maps/place/Aleks+Fitness/@41.8457672,41.7811427,17z/data=!3m1!4b1!4m6!3m5!1s0x405d774945ab373d:0x6ba6d134edcc8426!8m2!3d41.8457672!4d41.7811427!16s%2Fg%2F11y17fmysr"
               target="_blank" rel="noopener noreferrer">
               Open in Maps &rsaquo;
            </a>
        </div>
        <!-- Mobile tap-through overlay -->
        <a href="https://www.google.com/maps/place/Aleks+Fitness/@41.8457672,41.7811427,17z/data=!3m1!4b1!4m6!3m5!1s0x405d774945ab373d:0x6ba6d134edcc8426!8m2!3d41.8457672!4d41.7811427!16s%2Fg%2F11y17fmysr"
           target="_blank" rel="noopener noreferrer"
           class="map-mobile-link"
           aria-label="Open location in Google Maps"></a>
    </div>

    <!-- ── Footer ── -->
    <style>
        .site-footer {
            background: #080e0a;
            border-top: 2px solid rgba(74,222,128,0.22);
            padding: 22px 0 12px;
            color: #a0a0a0;
            overflow-x: hidden;
            width: 100%;
            box-sizing: border-box;
        }
        .footer-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 16px;
            max-width: 960px;
            margin: 0 auto;
            padding: 0 16px;
            text-align: center;
        }
        @media (max-width: 640px) {
            .footer-grid { grid-template-columns: 1fr; gap: 24px; }
        }
        .footer-col-title {
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: #4ade80;
            margin-bottom: 14px;
        }
        .footer-logo-img {
            height: 44px;
            width: auto;
            filter: drop-shadow(0 2px 10px rgba(74,222,128,0.30));
            margin-bottom: 6px;
        }
        .footer-brand-name {
            font-size: 1.05rem;
            font-weight: 800;
            color: #ececec;
            letter-spacing: 1px;
            margin-bottom: 4px;
        }
        .footer-tagline {
            font-size: 0.80rem;
            color: #606060;
        }
        .footer-contact-item {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            margin-bottom: 10px;
            font-size: 0.90rem;
        }
        .footer-contact-item i { color: #4ade80; width: 16px; flex-shrink: 0; }
        .footer-contact-item a {
            color: #d0d0d0;
            text-decoration: none;
            transition: color 0.2s;
        }
        .footer-contact-item a:hover { color: #4ade80; }
        .footer-social-row {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 8px;
        }
        .footer-social-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            padding: 9px 14px;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 700;
            text-decoration: none;
            border: 1px solid transparent;
            transition: all 0.22s ease;
            white-space: nowrap;
        }
        @media (max-width: 400px) {
            .footer-social-btn { padding: 8px 10px; font-size: 0.80rem; gap: 5px; }
            .footer-social-row { gap: 6px; }
        }
        .footer-social-btn.fb {
            background: rgba(24,119,242,0.12);
            border-color: rgba(24,119,242,0.30);
            color: #60a5fa;
        }
        .footer-social-btn.fb:hover {
            background: rgba(24,119,242,0.22);
            border-color: #1877F2;
            color: #93c5fd;
            text-decoration: none;
        }
        .footer-social-btn.ig {
            background: rgba(228,64,95,0.10);
            border-color: rgba(228,64,95,0.28);
            color: #f472b6;
        }
        .footer-social-btn.ig:hover {
            background: rgba(228,64,95,0.20);
            border-color: #E4405F;
            color: #fda4af;
            text-decoration: none;
        }
        .footer-social-btn.tg {
            background: rgba(39,161,222,0.12);
            border-color: rgba(39,161,222,0.30);
            color: #38bdf8;
        }
        .footer-social-btn.tg:hover {
            background: rgba(39,161,222,0.22);
            border-color: #27A1DE;
            color: #7dd3fc;
            text-decoration: none;
        }
        .footer-divider {
            border: none;
            border-top: 1px solid rgba(74,222,128,0.10);
            margin: 16px auto 10px;
            max-width: 960px;
        }
        .footer-bottom {
            text-align: center;
            font-size: 0.80rem;
            color: #454545;
            padding: 0 12px;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
            gap: 4px 8px;
        }
        .footer-bottom a {
            color: #555;
            text-decoration: none;
            transition: color 0.2s;
            white-space: nowrap;
        }
        .footer-bottom a:hover { color: #4ade80; }
    </style>

    <footer class="site-footer">
        <div class="footer-grid">

            <!-- Brand -->
            <div>
                <picture>
                    <source srcset="img/gym.webp" type="image/webp">
                    <img src="img/gym.png" alt="Alex Fit" class="footer-logo-img" loading="lazy"
                         onerror="this.style.display='none'">
                </picture>
                <div class="footer-brand-name">Alex Fit</div>
                <div class="footer-tagline" name="key_footer_tagline">Gym &amp; Boxing · Kobuleti</div>
            </div>

            <!-- Contact -->
            <div>
                <div class="footer-col-title" name="key_contact">Contact</div>
                <div class="footer-contact-item">
                    <i class="fas fa-phone-alt"></i>
                    <a href="tel:+995599061572">+995 599 061 572</a>
                </div>
                <div class="footer-contact-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <a href="https://www.google.com/maps/place/Aleks+Fitness/@41.8457672,41.7811427,17z/data=!3m1!4b1!4m6!3m5!1s0x405d774945ab373d:0x6ba6d134edcc8426!8m2!3d41.8457672!4d41.7811427!16s%2Fg%2F11y17fmysr"
                       target="_blank" rel="noopener noreferrer">
                        <span name="key_address">Shota Rustaveli 170-25, Kobuleti 6200</span>
                    </a>
                </div>
                <div class="footer-contact-item">
                    <i class="fas fa-clock"></i>
                    <span style="color:#d0d0d0;">09:00 – 00:00 &nbsp;<span style="color:#606060; font-size:0.80rem;" name="key_every_day">Every Day</span></span>
                </div>
            </div>

            <!-- Social -->
            <div>
                <div class="footer-col-title" name="key_follow_us">Follow Us</div>
                <div class="footer-social-row">
                    <a href="https://www.facebook.com/Alexfitnesskobulrti/"
                       target="_blank" rel="noopener noreferrer"
                       class="footer-social-btn fb">
                        <i class="fab fa-facebook-f"></i> Facebook
                    </a>
                    <a href="https://www.instagram.com/alex_fitness_kobuleti/"
                       target="_blank" rel="noopener noreferrer"
                       class="footer-social-btn ig">
                        <i class="fab fa-instagram"></i> Instagram
                    </a>
                    <a href="https://t.me/alex_fitness_kobuleti"
                       target="_blank" rel="noopener noreferrer"
                       class="footer-social-btn tg">
                        <i class="fab fa-telegram-plane"></i> Telegram
                    </a>
                </div>
            </div>

        </div>

        <hr class="footer-divider">

        <div class="footer-bottom">
            © 2026 Alex Fit. All rights reserved.
            &nbsp;·&nbsp;
            <a href="terms.php">Terms</a>
            &nbsp;·&nbsp;
            <a href="privacy.php">Privacy Policy</a>
        </div>
    </footer>
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
