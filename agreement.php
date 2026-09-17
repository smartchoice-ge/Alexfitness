<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Agreement Form | Alex Fitness</title>
    <!-- Import Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="//cdn.web-fonts.ge/fonts/bpg-glaho-traditional/css/bpg-glaho-traditional.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="js/language.js"></script>
    
    <link rel="shortcut icon" href="/img/favicon.ico" type="image/x-icon">
    <link rel="icon" href="/img/favicon.ico" type="image/x-icon">
    <link rel="preload" href="/img/gym.webp" as="image" type="image/webp">
    <link rel="dns-prefetch" href="//ajax.googleapis.com">
    <link rel="dns-prefetch" href="//cdn.tailwindcss.com">
    <link rel="dns-prefetch" href="//cdn.web-fonts.ge">
    <link rel="preconnect" href="https://ajax.googleapis.com" crossorigin>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <style>
    /* ── Navbar ── */
    .site-navbar{position:fixed;top:0;left:0;width:100%;z-index:9999;background:rgba(8,14,10,0.96);backdrop-filter:blur(14px);-webkit-backdrop-filter:blur(14px);border-bottom:1px solid rgba(34,197,94,0.15);box-shadow:0 2px 20px rgba(0,0,0,.5)}
    .site-navbar-inner{display:flex;align-items:center;height:82px;padding:0 24px;max-width:1400px;margin:0 auto}
    .navbar-logo-link{flex-shrink:0;display:flex;align-items:center;text-decoration:none}
    .navbar-logo-img{height:70px;width:auto;display:block;filter:drop-shadow(0 2px 10px rgba(34,197,94,.35));transition:filter .25s,transform .25s}
    .navbar-logo-img:hover{filter:drop-shadow(0 4px 18px rgba(34,197,94,.60));transform:scale(1.04)}
    .navbar-desktop-links{flex:1;display:flex;align-items:center;justify-content:center;gap:6px}
    .navbar-link{color:#c8c8c8;font-size:.95rem;font-weight:600;padding:8px 20px;border-radius:6px;text-decoration:none;letter-spacing:.4px;transition:color .2s,background .2s;white-space:nowrap}
    .navbar-link:hover{color:#4ade80;background:rgba(74,222,128,.10);text-decoration:none}
    @keyframes nav-btn-shimmer{0%{background-position:200% center}100%{background-position:-200% center}}
    .navbar-register{background:linear-gradient(90deg,#16a34a 0%,#4ade80 40%,#22c55e 55%,#4ade80 70%,#16a34a 100%);background-size:250% auto;color:#000!important;font-size:.92rem;font-weight:700;padding:9px 24px;border-radius:6px;border:1px solid rgba(74,222,128,.70);text-decoration:none;letter-spacing:.8px;text-transform:uppercase;white-space:nowrap;box-shadow:0 4px 18px rgba(74,222,128,.30);animation:nav-btn-shimmer 3.5s linear infinite;transition:box-shadow .22s,transform .22s}
    .navbar-register:hover{color:#000!important;text-decoration:none;transform:translateY(-2px);box-shadow:0 8px 28px rgba(74,222,128,.55)}
    .navbar-right-group{flex-shrink:0;display:flex;align-items:center;gap:12px}
    .navbar-lang-btn{color:#d0d0d0;font-size:.88rem;font-weight:600;padding:7px 14px;border-radius:6px;border:1px solid rgba(74,222,128,.38);background:rgba(74,222,128,.08);transition:all .2s;letter-spacing:.3px;white-space:nowrap;cursor:pointer}
    .navbar-lang-btn:hover{color:#4ade80;border-color:#4ade80;background:rgba(74,222,128,.16)}
    .navbar-hamburger{display:none;flex-direction:column;justify-content:center;gap:5px;width:36px;height:36px;padding:0;background:none;border:none;cursor:pointer}
    .navbar-hamburger span{display:block;width:24px;height:2px;background:#c8c8c8;border-radius:2px;transition:background .2s}
    .navbar-hamburger:hover span{background:#22c55e}
    .navbar-mobile-menu{display:none;flex-direction:column;padding:12px 20px 18px;border-top:1px solid rgba(34,197,94,.12);background:rgba(6,12,8,.98);gap:4px}
    .navbar-mobile-menu.open{display:flex}
    .nm-link{color:#c8c8c8;font-size:1rem;font-weight:600;padding:11px 16px;border-radius:6px;text-decoration:none;transition:color .2s,background .2s}
    .nm-link:hover{color:#fff;background:rgba(34,197,94,.10);text-decoration:none}
    .nm-register{background:linear-gradient(90deg,#16a34a 0%,#4ade80 50%,#16a34a 100%);background-size:200% auto;color:#000!important;font-size:.95rem;font-weight:700;padding:12px 20px;border-radius:6px;border:1px solid rgba(74,222,128,.60);text-decoration:none;letter-spacing:.8px;text-transform:uppercase;text-align:center;margin:6px 0;box-shadow:0 4px 16px rgba(74,222,128,.28);animation:nav-btn-shimmer 3.5s linear infinite}
    .nm-lang{color:#a0a0a0;font-size:.88rem;font-weight:600;padding:10px 16px;border-radius:6px;border:1px solid rgba(34,197,94,.22);text-align:center;margin-top:6px;transition:color .2s,border-color .2s;cursor:pointer}
    .nm-lang:hover{color:#22c55e;border-color:#22c55e}
    @media(max-width:900px){
        .navbar-desktop-links{display:none}
        .navbar-hamburger{display:flex}
        .navbar-right-group{margin-left:auto}
        .site-navbar-inner{height:62px;padding:0 max(16px,env(safe-area-inset-right,16px)) 0 max(16px,env(safe-area-inset-left,16px))}
        .navbar-logo-img{height:48px}
    }
    @media(min-width:901px){.navbar-mobile-menu{display:none!important}}

    /* ── Footer ── */
    .site-footer{background:#080e0a;border-top:2px solid rgba(74,222,128,.22);padding:22px 16px 12px;color:#a0a0a0;font-size:.85rem;overflow-x:hidden;width:100%;box-sizing:border-box}
    .footer-grid{display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;max-width:960px;margin:0 auto;text-align:center}
    @media(max-width:640px){.footer-grid{grid-template-columns:1fr;gap:12px}}
    .footer-col-title{color:#4ade80;font-size:.78rem;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;margin-bottom:14px}
    .footer-logo-img{height:40px;width:auto;margin:0 auto 6px;display:block;filter:drop-shadow(0 2px 8px rgba(74,222,128,.30))}
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
    .footer-divider{border:none;border-top:1px solid rgba(74,222,128,.10);margin:16px auto 10px;max-width:960px}
    .footer-bottom{text-align:center;font-size:.80rem;color:#454545;padding:0 12px}
    .footer-bottom a{color:#555;text-decoration:none;transition:color .2s}
    .footer-bottom a:hover{color:#4ade80}
    @media(max-width:400px){
        .footer-social-btn{padding:8px 10px;font-size:.80rem;gap:5px}
        .footer-social-row{gap:6px}
    }
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
        @media(max-width:900px){
            body { padding-top: 62px; }
        }
        #cont {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }
        /* Dark theme form inputs */
        .form-input {
            background-color: #1f1f1f;
            border: 1px solid #444;
            color: #fff;
            border-radius: 0.5rem;
            padding: 0.75rem;
        }
        .form-input::placeholder {
            color: #888;
        }
        .form-input:focus {
            outline: none;
            border-color: #22c55e;
            box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.2);
        }
        /* Brand button styling */
        .brand-btn {
            background-color: #166534; color: #ffffff;
            transition: background-color 0.3s ease;
            border-radius: 0.5rem;
            font-weight: bold;
        }
        .brand-btn:hover {
            background-color: #22c55e;
        }
        /* Language button container */
        .lang-btn-container {
            position: fixed !important;
            top: 1rem !important;
            right: 1rem !important;
            z-index: 99999 !important;
            display: flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.3rem;
            background: rgba(18, 18, 18, 0.92);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 999px;
            box-shadow: 0 14px 30px rgba(0, 0, 0, 0.35);
            backdrop-filter: blur(10px);
            transform: translateZ(0);
            will-change: transform;
            transition: all 0.3s ease;
            opacity: 1;
        }
        
        .lang-btn-container.scrolling {
            opacity: 0;
            transform: translateY(-10px);
            pointer-events: none;
        }
        
        /* Image upload styles */
        .image-upload-container {
            position: relative;
            margin-bottom: 1rem;
        }
        
        .image-upload-area {
            border: 2px dashed #666;
            border-radius: 0.5rem;
            padding: 2rem;
            text-align: center;
            background-color: #1a1a1a;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .image-upload-area:hover {
            border-color: #22c55e;
            background-color: #222;
        }
        
        .image-upload-area.dragover {
            border-color: #22c55e;
            background-color: rgba(34, 197, 94, 0.1);
        }
        
        .image-preview {
            max-width: 200px;
            max-height: 200px;
            border-radius: 0.5rem;
            margin: 1rem auto;
            object-fit: cover;
        }
        
        .upload-icon {
            font-size: 2rem;
            color: #666;
            margin-bottom: 0.5rem;
        }
        
        .upload-text {
            color: #ccc;
            font-size: 0.875rem;
        }
        
        .file-input {
            display: none;
        }
        
        .remove-image-btn {
            position: absolute;
            top: 0.5rem;
            right: 0.5rem;
            background-color: #dc2626;
            color: white;
            border: none;
            border-radius: 50%;
            width: 2rem;
            height: 2rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.875rem;
        }
        
        .upload-progress {
            width: 100%;
            height: 4px;
            background-color: #333;
            border-radius: 2px;
            margin-top: 0.5rem;
            overflow: hidden;
        }
        
        .upload-progress-bar {
            height: 100%;
            background-color: #22c55e;
            transition: width 0.3s ease;
        }
        
        /* Language button */
        .lang-btn {
            background-color: transparent;
            color: rgba(255, 255, 255, 0.82);
            padding: 0.55rem 1rem;
            border-radius: 999px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: 1px solid transparent;
            cursor: pointer;
            font-size: 0.875rem;
            min-width: auto;
            white-space: nowrap;
            letter-spacing: 0.02em;
        }
        .lang-btn:hover {
            color: #ffffff;
        }
        .lang-btn-active {
            opacity: 1;
            background-color: #166534; color: #ffffff;
            box-shadow: 0 6px 16px rgba(34, 197, 94, 0.25);
        }
        .lang-btn-inactive {
            opacity: 1;
        }

        /* Short/full label visibility */
        .btn-short { display: none; }
        @media (max-width: 540px) {
            .btn-full  { display: none; }
            .btn-short { display: inline; }
        }

        /* Mobile top-padding so logo clears the fixed lang switcher */
        @media (max-width: 768px) {
            .flex-grow.flex.items-center {
                padding-top: 3.5rem;
            }
        }
        @media (max-width: 480px) {
            .flex-grow.flex.items-center {
                padding-top: 3rem;
            }
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
                gap: 0.2rem;
                padding: 0.25rem;
                position: fixed !important;
                z-index: 99999 !important;
            }
            .lang-btn {
                padding: 0.42rem 0.82rem;
                font-size: 0.8rem;
            }
        }

        @media (max-width: 480px) {
            .lang-btn-container {
                top: 0.5rem !important;
                right: 0.5rem !important;
                gap: 0.15rem;
                padding: 0.22rem;
                position: fixed !important;
                z-index: 99999 !important;
            }
            .lang-btn {
                padding: 0.38rem 0.7rem;
                font-size: 0.75rem;
            }
        }
        /* Form labels */
        .form-label {
            color: #fff;
            font-weight: 500;
            margin-bottom: 0.5rem;
            display: block;
        }
        /* Checkbox styling */
        .checkbox-custom {
            appearance: none;
            width: 20px;
            height: 20px;
            border: 2px solid #444;
            border-radius: 4px;
            background-color: #1f1f1f;
            position: relative;
            cursor: pointer;
        }
        .checkbox-custom:checked {
            background-color: #22c55e;
            border-color: #22c55e;
        }
        .checkbox-custom:checked::after {
            content: '✓';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: #000;
            font-weight: bold;
            font-size: 14px;
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
    fbq('track', 'InitiateCheckout', {
        content_name: 'Gym Membership Registration',
        content_category: 'Fitness'
    });
    </script>
    <noscript><img height="1" width="1" style="display:none"
    src="https://www.facebook.com/tr?id=&ev=PageView&noscript=1"
    /></noscript>
    <!-- End Meta Pixel Code -->
</head>

<body class="bg-black text-white min-h-screen flex flex-col">

<nav class="site-navbar" id="siteNavbar">
    <div class="site-navbar-inner">
        <a href="/" class="navbar-logo-link">
            <picture>
                <source srcset="img/gym.webp" type="image/webp">
                <img src="img/gym.png" alt="Alex Fit" class="navbar-logo-img" fetchpriority="high"
                     onerror="this.onerror=null;this.src='https://placehold.co/55x55/0d1a11/22c55e?text=AF';">
            </picture>
        </a>
        <div class="navbar-desktop-links">
            <span class="navbar-register banner" name="key_become_member" style="cursor:default;">key_become_member</span>
        </div>
        <div class="navbar-right-group">
            <div class="navbar-lang-btn" onclick="toggleNavLang();">
                <span name="key_lang">key_lang</span>
            </div>
            <button class="navbar-hamburger" id="navToggle"
                    onclick="document.getElementById('navMobileMenu').classList.toggle('open')">
                <span></span><span></span><span></span>
            </button>
        </div>
    </div>
    <div class="navbar-mobile-menu" id="navMobileMenu">
        <span class="nm-register banner" name="key_become_member" style="cursor:default;">key_become_member</span>
        <div class="nm-lang" onclick="toggleNavLang();">
            <span name="key_lang">key_lang</span>
        </div>
    </div>
</nav>

<div id="cont">
    <!-- Agreement Form Section -->
    <div class="flex-grow flex items-center justify-center pb-12">
        <div class="container mx-auto px-4">
            <div class="max-w-lg w-full p-8 mx-auto">
                <div class="text-center mb-8">
                    <div class="inline-block bg-black p-3 rounded-lg">
                        <a href="/" class="block">
                            <picture>
                                <source srcset="/img/gym.webp" type="image/webp">
                                <img src="/img/gym.png" alt="Alex Fitness" class="h-16 mx-auto cursor-pointer hover:opacity-80 transition-opacity duration-200" onerror="this.onerror=null; this.src='https://placehold.co/180x60/cccccc/000000?text=Alex+Fitness';">
                            </picture>
                        </a>
                    </div>
                    <h1 id="title" class="text-3xl font-bold mt-6 text-center text-white">შეთანხმების ფორმა</h1>
                </div>
                
                <form id="agreementForm">
                    <div class="mb-4">
                        <label for="label_mobile_number" id="label_mobile_number" class="form-label">
                            მობილურის ნომერი
                        </label>
                        <div class="flex items-center border border-gray-600 rounded-lg overflow-hidden">
                            <input type="text" name="country_code" id="country_code" value="995" 
                                class="p-3 bg-gray-700 text-gray-300 w-16 text-center" readonly>
                            <input type="text" name="mobile_number" id="mobile_number" 
                                class="form-input flex-1 border-0" placeholder="შენი ტელეფონის ნომერი"
                                pattern="\d{9}" title="შეიყვანეთ 9 ციფრიანი ტელეფონის ნომერი" maxlength="9" inputmode="numeric" autocomplete="tel-national" required>
                        </div>
                    </div>

                    <div class="mb-4" id="verification-method-wrap" style="display:none;">
                        <label for="options" id="verification_method_label" class="form-label">ვერიფიკაციის მეთოდი</label>
                        <select id="verification_method" name="options" class="form-input w-full">
                            <option value="sms" selected>SMS</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label id="label_id_number" for="id_number" class="form-label">პირადი ნომერი / პასპორტის ნომერი</label>
                        <input type="text" name="id_number" id="id_number" class="form-input w-full"
                            placeholder="ჩაწერე პირადი ნომერი" maxlength="11" required>
                    </div>
                    
                    <div class="mb-4">
                        <label id="label_full_name" for="full_name" class="form-label">სრული სახელი</label>
                        <input type="text" name="full_name" id="full_name" class="form-input w-full" 
                            placeholder="შენი სახელი და გვარი" required>
                    </div>

                    <div class="mb-4">
                        <label for="label_email" id="label_email" class="form-label">ელ-ფოსტა</label>
                        <input id="email" type="text" name="email" class="form-input w-full"
                            placeholder="შენი ელ-ფოსტა" required>
                    </div>

                    <!-- Profile Picture Upload -->
                    <div class="mb-4" id="profile-picture-section">
                        <label id="label_profile_picture" for="profile_picture" class="form-label">პროფილის ფოტო <span class="text-red-400">*</span></label>
                        <div class="image-upload-container">
                            <div class="image-upload-area">
                                <div id="upload-placeholder">
                                    <i class="fas fa-camera upload-icon"></i>
                                    <div class="upload-text" id="upload_text">აირჩიეთ ფოტოს ატვირთვის გზა</div>
                                    <div class="upload-text text-xs mt-1" id="upload-text-small">JPG, PNG, GIF, WebP (მაქს. 15MB)</div>
                                    <div class="flex justify-center space-x-4 mt-4">
                                        <button type="button" class="brand-btn px-4 py-2 rounded flex items-center space-x-2" onclick="openCamera()">
                                            <i class="fas fa-camera"></i>
                                            <span id="camera-btn-text">კამერა</span>
                                        </button>
                                        <button type="button" class="bg-gray-600 text-white px-4 py-2 rounded flex items-center space-x-2 hover:bg-gray-500" onclick="openGallery()">
                                            <i class="fas fa-images"></i>
                                            <span id="gallery-btn-text">გალერეა</span>
                                        </button>
                                    </div>
                                </div>
                                <div id="image-preview-container" style="display: none;">
                                    <img id="image-preview" class="image-preview" src="" alt="Profile Preview">
                                    <button type="button" class="remove-image-btn" onclick="removeImage(event)">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <div id="upload-progress" class="upload-progress" style="display: none;">
                                    <div id="upload-progress-bar" class="upload-progress-bar" style="width: 0%;"></div>
                                </div>
                            </div>
                            <!-- Camera input (mobile camera) -->
                            <input type="file" id="camera_input" name="camera_input" class="file-input"
                                   accept="image/*"
                                   capture="environment"
                                   onchange="handleImageUpload(this)">
                            <!-- Gallery input (file picker) -->
                            <input type="file" id="profile_picture" name="profile_picture" class="file-input" 
                                   accept="image/jpeg,image/jpg,image/png,image/gif,image/webp" 
                                   onchange="handleImageUpload(this)">
                        </div>
                    </div>
                   
                    <div class="mb-4">
                        <label id="label_birth_date" for="birth_date" class="form-label">
                            დაბადების თარიღი
                        </label>
                        <div class="flex space-x-2">
                            <!-- Select for Months -->
                            <select name="birth_month" id="birth_month"
                                class="form-input w-1/3" required>
                                <option value="">თვე</option>
                                <option value="01">იანვარი</option>
                                <option value="02">თებერვალი</option>
                                <option value="03">მარტი</option>
                                <option value="04">აპრილი</option>
                                <option value="05">მაისი</option>
                                <option value="06">ივნისი</option>
                                <option value="07">ივლისი</option>
                                <option value="08">აგვისტო</option>
                                <option value="09">სექტემბერი</option>
                                <option value="10">ოქტომბერი</option>
                                <option value="11">ნოემბერი</option>
                                <option value="12">დეკემბერი</option>
                            </select>

                            <!-- Input for Day -->
                            <input id='birth_day' type="number" name="birth_day" min="1" max="31" placeholder="დღე"
                                class="form-input w-1/4 text-center" required>

                            <!-- Input for Year -->
                            <input id='birth_year' type="number" name="birth_year" min="1900" max="2100" placeholder="წელი"
                                class="form-input w-1/3 text-center" required>
                        </div>
                    </div>

                    <div class="mb-4 flex items-center">
                        <input type="checkbox" name="agree_to_agreement" id="agree_to_agreement" class="checkbox-custom mr-3">
                        <label id="label_agreement" for="agree_to_agreement" class="form-label mb-0">
                            ვეთანხმები<a href="/Alex-Fitness-agreement.pdf" target="_blank"> <span class="text-yellow-400 hover:text-yellow-300">კონტრაქტს</span></a>
                        </label>
                    </div>

                    <div class="mb-4">
                        <button id="submit_button" type="submit" class="brand-btn w-full py-3 px-4 text-lg">გაგზავნა</button>
                    </div>
                </form>

                <div id="message" class="text-red-400 text-center mt-4"></div>

                <div class="mt-6 pt-4 border-t border-gray-700 flex justify-center items-center gap-4 text-sm">
                    <a href="/terms.php" target="_blank" style="color:#22c55e;" class="hover:underline" id="link_terms">Terms and Conditions</a>
                    <span class="text-gray-600">|</span>
                    <a href="/privacy.php" target="_blank" style="color:#22c55e;" class="hover:underline" id="link_privacy">Privacy Policy</a>
                </div>
            </div>
        </div>
    </div>
</div>

<footer class="site-footer">
    <div class="footer-grid">
        <div>
            <picture>
                <source srcset="img/gym.webp" type="image/webp">
                <img src="img/gym.png" alt="Alex Fit" class="footer-logo-img" loading="lazy"
                     onerror="this.style.display='none'">
            </picture>
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
                <span style="color:#d0d0d0;">09:00 – 00:00 &nbsp;<span style="color:#606060;font-size:.80rem;" name="key_every_day">Every Day</span></span>
            </div>
            <div class="footer-contact-item">
                <i class="fas fa-map-marker-alt"></i>
                <span style="color:#d0d0d0;" name="key_address">Shota Rustaveli 170-25, Kobuleti 6200</span>
            </div>
        </div>
        <div>
            <div class="footer-col-title" name="key_follow_us">Follow Us</div>
            <div class="footer-social-row">
                <a href="https://www.facebook.com/Alexfitnesskobulrti/"
                   target="_blank" rel="noopener noreferrer" class="footer-social-btn fb">
                    <i class="fab fa-facebook-f"></i> Facebook
                </a>
                <a href="https://www.instagram.com/alex_fitness_kobuleti/"
                   target="_blank" rel="noopener noreferrer" class="footer-social-btn ig">
                    <i class="fab fa-instagram"></i> Instagram
                </a>
                <a href="https://t.me/alex_fitness_kobuleti"
                   target="_blank" rel="noopener noreferrer" class="footer-social-btn tg">
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

    <script>
        let currentLang = 'ka';

        // Translation dictionary with placeholders
        const translations = {
            en: {
                title: "Agreement Form",
                label_id_number: "ID Number / Passport No",
                label_full_name: "Full Name",
                label_mobile_number: "Mobile Number",
                label_birth_date: "Birth Date",
                label_email: "Your email address",
                label_profile_picture: "Profile Picture *",
                upload_text: "Click or drag photo here",
                upload_text_small: "JPG, PNG, GIF, WebP (Max 15MB)",
                verification_method_label: "Verification Method",
                label_agreement: "I agree to the",
                contract_text: "Contract",
                submit_button: "Submit",
                agree_error: "You must agree to the contract",
                sms_error: "Error sending SMS: ",
                success_message: "Your response has been recorded!",
                verification_prompt: "Enter the code",
                verification_placeholder: "Enter 3-digit code",
                verification_error: "Incorrect code, try again!",
                verification_confirm: "Confirm",
                verification_cancel: "Cancel",
                placeholders: {
                    id_number: "Enter your ID number",
                    full_name: "Your full name",
                    mobile_number: "Your phone number",
                    month: "Month",
                    day: "Day",
                    year: "Year",
                    email: "Email",
                },
                monthes: { 
                    1: "January", 2: "February", 3: "March", 4: "April", 5: "May", 6: "June",
                    7: "July", 8: "August", 9: "September", 10: "October", 11: "November", 12: "December"
                },
                titles: { 
                    full_name: "Only letters allowed",
                    mobile_number: "Enter a 9-digit phone number"
                }
            },
            ka: {
                title: "შეთანხმების ფორმა",
                label_id_number: "პირადი ნომერი / პასპორტის ნომერი",
                label_full_name: "სრული სახელი",
                label_mobile_number: "მობილურის ნომერი",
                label_birth_date: "დაბადების თარიღი",
                label_email: "შენი ელ-ფოსტა",
                label_profile_picture: "პროფილის ფოტო *",
                upload_text: "დააჭირეთ ან გადმოათრიეთ ფოტო",
                upload_text_small: "JPG, PNG, GIF, WebP (მაქს. 15MB)",
                verification_method_label: "ვერიფიკაციის მეთოდი",
                label_agreement: "ვეთანხმები",
                contract_text: "კონტრაქტს",
                submit_button: "გაგზავნა",
                agree_error: "დაეთანხმეთ კონტრაქტს",
                sms_error: "SMS გაგზავნის შეცდომა: ",
                success_message: "თქვენი პასუხი დაფიქსირდა!",
                verification_prompt: "შეიყვანეთ კოდი",
                verification_placeholder: "შეიყვანეთ 3 ციფრიანი კოდი",
                verification_error: "კოდი არასწორია, სცადეთ თავიდან!",
                verification_confirm: "დადასტურება",
                verification_cancel: "გაუქმება",
                placeholders: {
                    id_number: "ჩაწერე პირადი ნომერი",
                    full_name: "შენი სახელი და გვარი",
                    mobile_number: "ტელეფონის ნომერი",
                    month: "თვე",
                    day: "დღე",
                    year: "წელი",
                    email: "ელ-ფოსტა",
                },
                monthes: { 
                    1: "იანვარი", 2: "თებერვალი", 3: "მარტი", 4: "აპრილი", 5: "მაისი", 6: "ივნისი",
                    7: "ივლისი", 8: "აგვისტო", 9: "სექტემბერი", 10: "ოქტომბერი", 11: "ნოემბერი", 12: "დეკემბერი"
                },
                titles: { 
                    full_name: "დაშვებულია მხოლოდ ასოები და სფეისები",
                    mobile_number: "შეიყვანეთ 9 ციფრიანი ტელეფონის ნომერი"
                }
            },
            ru: {
                title: "Форма соглашения",
                label_id_number: "ID номер / Номер паспорта",
                label_full_name: "Полное имя",
                label_mobile_number: "Номер телефона",
                label_birth_date: "Дата рождения",
                label_email: "Ваш email",
                label_profile_picture: "Фото профиля *",
                upload_text: "Нажмите или перетащите фото",
                upload_text_small: "JPG, PNG, GIF, WebP (Макс. 15МБ)",
                verification_method_label: "Метод верификации",
                label_agreement: "Я согласен(а) с",
                contract_text: "Договором",
                submit_button: "Отправить",
                agree_error: "Необходимо принять условия договора",
                sms_error: "Ошибка отправки SMS: ",
                success_message: "Ваш ответ записан!",
                verification_prompt: "Введите код",
                verification_placeholder: "Введите 3-значный код",
                verification_error: "Неверный код, попробуйте снова!",
                verification_confirm: "Подтвердить",
                verification_cancel: "Отмена",
                placeholders: {
                    id_number: "Введите ID номер",
                    full_name: "Ваше имя и фамилия",
                    mobile_number: "Номер телефона",
                    month: "Месяц",
                    day: "День",
                    year: "Год",
                    email: "Email",
                },
                monthes: {
                    1: "Январь", 2: "Февраль", 3: "Март", 4: "Апрель", 5: "Май", 6: "Июнь",
                    7: "Июль", 8: "Август", 9: "Сентябрь", 10: "Октябрь", 11: "Ноябрь", 12: "Декабрь"
                },
                titles: {
                    full_name: "Разрешены только буквы и пробелы",
                    mobile_number: "Введите 9-значный номер телефона"
                }
            }
        };

        function setLanguage(lang) {
            currentLang = lang;
            localStorage.setItem('ActiveLanguage', lang);
            localStorage.setItem('language', lang);
            updateContent();
            if (typeof ChangeData === 'function') ChangeData();
        }

        // Hook called by language.js when language changes via the dropdown
        function onLangChange(lang) { setLanguage(lang); }

        function toggleNavLang() {
            // Delegate to language.js dropdown if available
            const btn = document.querySelector('.navbar-lang-btn');
            if (btn && typeof showLangDropdown === 'function') {
                showLangDropdown(btn);
            }
        }

        function updateContent() {
            const T = translations[currentLang] || translations['ka'];
            const contract = `<a href="/Alex-Fitness-agreement.pdf" target="_blank"><span class="text-yellow-400 hover:text-yellow-300">${T.contract_text}</span></a>`;

            const set = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = val; };
            const setHTML = (id, val) => { const el = document.getElementById(id); if (el) el.innerHTML = val; };
            const setPlh = (id, val) => { const el = document.getElementById(id); if (el) el.placeholder = val; };

            set('title',                    T.title);
            set('label_id_number',          T.label_id_number);
            set('label_full_name',          T.label_full_name);
            set('label_mobile_number',      T.label_mobile_number);
            set('label_email',              T.label_email);
            set('label_profile_picture',    T.label_profile_picture);
            set('upload_text',              T.upload_text);
            set('upload-text-small',        T.upload_text_small);
            set('camera-btn-text',          currentLang === 'ka' ? 'კამერა' : (currentLang === 'ru' ? 'Камера' : 'Camera'));
            set('gallery-btn-text',         currentLang === 'ka' ? 'გალერეა' : (currentLang === 'ru' ? 'Галерея' : 'Gallery'));
            set('webcam-title',             currentLang === 'ka' ? 'ფოტოს გადაღება' : (currentLang === 'ru' ? 'Сделать фото' : 'Take a Photo'));
            set('label_birth_date',         T.label_birth_date);
            set('verification_method_label',T.verification_method_label);
            set('submit_button',            T.submit_button);
            set('link_terms',               currentLang === 'ka' ? 'წესები და პირობები' : (currentLang === 'ru' ? 'Условия использования' : 'Terms and Conditions'));
            set('link_privacy',             currentLang === 'ka' ? 'კონფიდენციალურობის პოლიტიკა' : (currentLang === 'ru' ? 'Политика конфиденциальности' : 'Privacy Policy'));
            setHTML('label_agreement',      T.label_agreement + ' ' + contract);

            setPlh('id_number',     T.placeholders.id_number);
            setPlh('full_name',     T.placeholders.full_name);
            setPlh('mobile_number', T.placeholders.mobile_number);
            setPlh('email',         T.placeholders.email);
            setPlh('birth_day',     T.placeholders.day);
            setPlh('birth_year',    T.placeholders.year);

            const monthSelect = document.getElementById('birth_month');
            if (monthSelect) {
                const cur = monthSelect.value;
                const months = T.monthes;
                monthSelect.innerHTML = `<option value="">${T.placeholders.month}</option>` +
                    Object.entries(months).map(([v, label]) =>
                        `<option value="${String(v).padStart(2,'0')}">${label}</option>`
                    ).join('');
                if (cur) monthSelect.value = cur;
            }
        }

        // Event listener for phone number field - real-time duplicate check (SOFT CHECK)
        document.getElementById('mobile_number').addEventListener('blur', function() {
            // Don't check if field is disabled (email verification)
            if (this.disabled) return;
            
            console.log('Phone blur event triggered with value:', this.value);
            checkPhoneAvailability(this.value);
        });

        // Also check when user types (with small delay to avoid too many requests)
        let phoneCheckTimeout;
        document.getElementById('mobile_number').addEventListener('input', function() {
            this.value = this.value.replace(/\D/g, '').slice(0, 9);
            clearTimeout(phoneCheckTimeout);
            const phoneValue = this.value;
            
            // Don't check if field is disabled (email verification)
            if (this.disabled) return;
            
            // Only check if phone number is 9 digits
            if (phoneValue.length === 9 && /^\d{9}$/.test(phoneValue)) {
                phoneCheckTimeout = setTimeout(() => {
                    checkPhoneAvailability(phoneValue);
                }, 500); // Wait 500ms after user stops typing
            } else if (phoneValue.length < 9) {
                // Close any existing popup when user starts typing a new number
                let existingPopup = document.getElementById('alreadyRegisteredPopup');
                if (existingPopup) {
                    existingPopup.remove();
                }
            }
        });

        function checkPhoneAvailability(phoneNumber) {
            console.log('checkPhoneAvailability called with:', phoneNumber);
            
            // Validate phone number format first
            if (!/^\d{9}$/.test(phoneNumber)) {
                console.log('Phone number format invalid:', phoneNumber);
                return; // Don't check invalid formats
            }

            console.log('Phone number format valid, making AJAX request...');
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'check_phone_simple.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                console.log('XHR state change:', xhr.readyState, 'Status:', xhr.status);
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    console.log('Response received:', xhr.responseText);
                    if (xhr.status === 200) {
                        try {
                            const response = JSON.parse(xhr.responseText);
                            console.log('Parsed response:', response);
                            
                            // Show debug information
                            if (response.debug) {
                                console.log('DEBUG - Searched for:', response.debug.searched_for);
                                console.log('DEBUG - Found similar numbers:', response.debug.found_similar);
                                
                                // If phone is available but we expected it to be registered, show more info
                                if (response.status === 'success' && response.debug.found_similar.length > 0) {
                                    console.log('NOTICE: Phone shows as available but similar numbers found in database!');
                                    console.log('This suggests a format mismatch between search and database storage.');
                                }
                            } else {
                                console.log('DEBUG: No debug information received in response');
                            }
                            
                            const messageDiv = document.getElementById('message');
                            
                            if (response.status === 'error') {
                                // Phone number already registered - show warning popup but allow continuation
                                const warningMsg = currentLang === 'ka' ?
                                    'ეს ნომერი უკვე რეგისტრირებულია. სისტემაში შესასვლელად გადმოწერეთ ჩვენი აპი' :
                                    (currentLang === 'ru' ? 'Этот номер уже зарегистрирован. Для входа скачайте наше приложение.' : 'This number is already registered. To log in, download our app.');
                                console.log('Setting warning message:', warningMsg);
                                showPhoneWarningPopup(warningMsg);
                            } else {
                                // Phone available - clear any previous error messages only if they were about phone registration
                                console.log('Phone available');
                                if (messageDiv.innerHTML.includes('რეგისტრირებული ხართ') || messageDiv.innerHTML.includes('already registered')) {
                                    messageDiv.innerHTML = '';
                                }
                            }
                        } catch (e) {
                            console.error('Error parsing phone check response:', e, 'Raw response:', xhr.responseText);
                        }
                    } else {
                        console.error('HTTP error:', xhr.status, xhr.statusText);
                    }
                }
            };
            xhr.onerror = function() {
                console.error('Phone check request failed');
            };
            
            const postData = 'phone_number=' + encodeURIComponent(phoneNumber);
            console.log('Sending POST data:', postData);
            xhr.send(postData);
        }

        function showPhoneWarningPopup(message) {
            // Remove any existing popup first
            let existingPopup = document.getElementById('phoneWarningPopup');
            if (existingPopup) {
                existingPopup.remove();
            }
            
            // Get translated text based on current language
            const cancelText = currentLang === 'ka' ? 'რეგისტრაციის გაგრძელება' : (currentLang === 'ru' ? 'Продолжить регистрацию' : 'Cancel');
            
            let popup = document.createElement('div');
            popup.id = "phoneWarningPopup";
            popup.innerHTML = `
                <div class="fixed inset-0 flex items-center justify-center z-50" style="background:rgba(0,0,0,0.85);">
                    <div style="background:#1a1a1a;border:1px solid #22c55e;border-radius:12px;padding:2rem 2rem 1.5rem;max-width:360px;width:90%;text-align:center;box-shadow:0 0 40px rgba(34, 197, 94,0.15);">
                        <div style="margin:0 auto 1.25rem;width:52px;height:52px;border-radius:50%;background:rgba(34, 197, 94,0.12);border:2px solid #22c55e;display:flex;align-items:center;justify-content:center;">
                            <svg style="width:26px;height:26px;color:#22c55e;" fill="none" viewBox="0 0 24 24" stroke="#22c55e">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        </div>
                        <p style="color:#ffffff;font-size:1rem;font-weight:600;line-height:1.6;margin-bottom:1.5rem;">${message}</p>
                        <button onclick="continueWithRegistration()" class="brand-btn" style="width:100%;padding:0.75rem 1rem;font-size:0.95rem;border-radius:8px;border:none;cursor:pointer;">${cancelText}</button>
                    </div>
                </div>
            `;
            document.body.appendChild(popup);
            
            // Focus on the continue button
            setTimeout(() => {
                const button = popup.querySelector('button:last-child');
                if (button) {
                    button.focus();
                }
            }, 100);
        }

        function closePhoneWarningPopup() {
            let popup = document.getElementById('phoneWarningPopup');
            if (popup) {
                popup.remove();
            }
        }

        function continueWithRegistration() {
            // User chose to continue despite phone being registered
            closePhoneWarningPopup();
            // Mark that user was warned about duplicate phone
            window.userAcknowledgedDuplicatePhone = true;
        }

        function showAlreadyRegisteredPopup(message) {
            // Remove any existing popup first
            let existingPopup = document.getElementById('alreadyRegisteredPopup');
            if (existingPopup) {
                existingPopup.remove();
            }
            
            // Get translated text based on current language
            const okText = currentLang === 'ka' ? 'კარგი' : (currentLang === 'ru' ? 'ОК' : 'OK');
            
            let popup = document.createElement('div');
            popup.id = "alreadyRegisteredPopup";
            popup.innerHTML = `
                <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-80 z-50">
                    <div class="bg-gray-900 border border-gray-700 p-8 rounded-lg shadow-2xl w-96 text-center">
                        <div class="mb-6">
                            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </div>
                            <h2 class="text-xl font-semibold text-white">${message}</h2>
                        </div>
                        <button onclick="closeAlreadyRegisteredPopup()" class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg font-semibold transition-colors duration-200 w-full">${okText}</button>
                    </div>
                </div>
            `;
            document.body.appendChild(popup);
            
            // Focus on the OK button
            setTimeout(() => {
                const button = popup.querySelector('button');
                if (button) {
                    button.focus();
                }
            }, 100);
        }

        function closeAlreadyRegisteredPopup() {
            let popup = document.getElementById('alreadyRegisteredPopup');
            if (popup) {
                popup.remove();
            }
            // Don't clear the phone field for soft check - user can continue if they want
        }

        document.getElementById('agreementForm').addEventListener('submit', function(event) {
            event.preventDefault();

            // Immediate feedback - disable submit button and show loading message
            const submitButton = document.getElementById('submit_button');
            const messageDiv = document.getElementById('message');
            
            // Check if already submitting
            if (submitButton.disabled) {
                console.log('Form already submitting, ignoring duplicate submit');
                return;
            }
            
            // Store original button text BEFORE any changes
            const originalButtonText = submitButton.textContent;
            
            // Disable submit button after validation passes
            submitButton.disabled = true;
            submitButton.style.opacity = '0.6';
            submitButton.style.cursor = 'not-allowed';
            
            // Change button text to sending state
            const sendingButtonText = currentLang === 'ka' ? 'იგზავნება...' : (currentLang === 'ru' ? 'Отправка...' : 'Sending...');
            submitButton.textContent = sendingButtonText;

            if (!document.getElementById('agree_to_agreement').checked) {
                // Re-enable button on error
                submitButton.disabled = false;
                submitButton.style.opacity = '1';
                submitButton.style.cursor = 'pointer';
                submitButton.textContent = originalButtonText;

                const errorMsg = currentLang === 'ka' ? 'დაეთანხმეთ კონტრაქტს' : (currentLang === 'ru' ? 'Необходимо принять условия договора' : 'You must agree to the contract');
                messageDiv.innerHTML = errorMsg;
                return;
            }

            // Require photo upload
            const previewContainer = document.getElementById('image-preview-container');
            if (!previewContainer || previewContainer.style.display === 'none') {
                submitButton.disabled = false;
                submitButton.style.opacity = '1';
                submitButton.style.cursor = 'pointer';
                submitButton.textContent = originalButtonText;
                const photoError = currentLang === 'ka' ? 'გთხოვთ ატვირთოთ პროფილის ფოტო' : (currentLang === 'ru' ? 'Пожалуйста, загрузите фото профиля' : 'Please upload a profile photo');
                messageDiv.innerHTML = '<span style="color:#ff4444;">' + photoError + '</span>';
                document.getElementById('profile-picture-section').scrollIntoView({behavior:'smooth', block:'center'});
                return;
            }

            let mobileNumber = document.getElementById('mobile_number').value;
            let verificationMethod = document.getElementById('verification_method').value;
            
            // For email verification, generate a random 9-digit number if no mobile number provided
            if (verificationMethod === 'email' && (!mobileNumber || mobileNumber.trim() === '')) {
                // Generate random 9-digit number (guaranteed 9 digits)
                mobileNumber = '';
                for (let i = 0; i < 9; i++) {
                    mobileNumber += Math.floor(Math.random() * 10);
                }
                // Ensure first digit is not 0
                if (mobileNumber[0] === '0') {
                    mobileNumber = (Math.floor(Math.random() * 9) + 1) + mobileNumber.substring(1);
                }
                console.log('Generated random 9-digit mobile number for email verification:', mobileNumber);
            }
            
            // Phone number validation - only require 9 digits for SMS verification
            if (verificationMethod === 'sms' && !/^\d{9}$/.test(mobileNumber)) {
                // Re-enable button on error
                submitButton.disabled = false;
                submitButton.style.opacity = '1';
                submitButton.style.cursor = 'pointer';
                submitButton.textContent = originalButtonText;
                
                const errorMsg = currentLang === 'ka' ? 'გთხოვთ შეიყვანოთ სწორი ტელეფონის ნომერი (9 ციფრი)' : (currentLang === 'ru' ? 'Введите корректный номер телефона (9 цифр)' : 'Please enter a valid phone number (9 digits)');
                messageDiv.innerHTML = errorMsg;
                return;
            }

            // For email verification, if mobile number is provided, should be valid format
            if (verificationMethod === 'email' && mobileNumber && !/^\d{9}$/.test(mobileNumber)) {
                // Re-enable button on error
                submitButton.disabled = false;
                submitButton.style.opacity = '1';
                submitButton.style.cursor = 'pointer';
                submitButton.textContent = originalButtonText;
                
                const errorMsg = currentLang === 'ka' ? 'თუ ტელეფონის ნომერს იყენებთ, შეიყვანეთ სწორი ფორმატი (9 ციფრი)' : (currentLang === 'ru' ? 'Если указываете номер телефона, используйте правильный формат (9 цифр)' : 'If you provide a phone number, please use the correct format (9 digits)');
                messageDiv.innerHTML = errorMsg;
                return;
            }

            // Phone number check during submission - DISABLED
            // Check if phone number already exists in database
            // const xhr = new XMLHttpRequest();
            // xhr.open('POST', 'check_phone_simple.php', true);
            // xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            // xhr.onreadystatechange = function() {
            //     if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
            //         const response = JSON.parse(xhr.responseText);
            //         if (response.status === 'error') {
            //             // Phone number already registered - show popup
            //             const errorMsg = currentLang === 'ka' ? 'თქვენ უკვე რეგისტრირებული ხართ' : 'You have been already registered';
            //             showAlreadyRegisteredPopup(errorMsg);
            //             return;
            //         } else {
            //             // Phone available - continue with original form submission
            //             proceedWithOriginalSubmission(mobileNumber);
            //         }
            //     }
            // };
            // xhr.send('phone_number=' + encodeURIComponent(mobileNumber));
            
            // Directly proceed with form submission without phone check
            proceedWithOriginalSubmission(mobileNumber, submitButton, originalButtonText);
        });

        function proceedWithOriginalSubmission(mobileNumber, submitButton, originalButtonText) {
            // Store form data before verification to prevent clearing
            storeFormData();

            // Generate a random 3-digit code
            let verificationCode = Math.floor(100 + Math.random() * 900);
            console.log('Generated verification code:', verificationCode);
            sessionStorage.setItem("verificationCode", verificationCode.toString());

            let verificationMethod = document.getElementById('verification_method').value;

            if(verificationMethod === 'sms'){ 
                let xhr = new XMLHttpRequest();
                xhr.open('POST', 'send_sms.php', true);
                xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        try {
                            let response = JSON.parse(xhr.responseText);
                            if (response.status === 'success') {
                                showVerificationPopup(submitButton, originalButtonText);
                            } else {
                                // SMS sending failed, but still show popup for manual verification
                                console.log('SMS sending failed:', response.message);
                                const errorMsg = currentLang === 'ka' ?
                                    'SMS შეიძლება არ იყოს გაგზავნილი. შეიყვანეთ კოდი თუ მიიღეთ, ან დაეკონტაქტეთ ადმინისტრაციას.' :
                                    (currentLang === 'ru' ? 'SMS может не быть отправлено. Введите код, если получили, или свяжитесь с администрацией.' : 'SMS may not have been sent. Enter the code if you received it, or contact administration.');
                                document.getElementById('message').innerHTML = errorMsg;
                                
                                // Still show verification popup
                                showVerificationPopup(submitButton, originalButtonText);
                            }
                        } catch (e) {
                            console.error('Error parsing SMS response:', xhr.responseText);
                            const errorMsg = currentLang === 'ka' ?
                                'SMS სისტემასთან კავშირის პრობლემა. შეიყვანეთ კოდი თუ მიიღეთ.' :
                                (currentLang === 'ru' ? 'Проблема соединения с SMS-системой. Введите код, если получили его.' : 'SMS system connection issue. Enter the code if you received it.');
                            document.getElementById('message').innerHTML = errorMsg;
                            
                            // Show popup anyway for manual code entry
                            showVerificationPopup(submitButton, originalButtonText);
                        }
                    } else {
                        console.error('SMS request failed with status:', xhr.status);
                        const errorMsg = currentLang === 'ka' ? 
                            'SMS სისტემასთან კავშირის პრობლემა. შეიყვანეთ კოდი თუ მიიღეთ.' : 
                            'SMS system connection issue. Enter the code if you received it.';
                        document.getElementById('message').innerHTML = errorMsg;
                        
                        // Show popup anyway - SMS might have been sent despite status error
                        showVerificationPopup(submitButton, originalButtonText);
                    }
                };
                xhr.onerror = function() {
                    console.error('SMS request network error');
                    const errorMsg = currentLang === 'ka' ?
                        'ქსელის პრობლემა. შეიყვანეთ კოდი თუ მიიღეთ SMS.' :
                        (currentLang === 'ru' ? 'Проблема с сетью. Введите код, если получили SMS.' : 'Network issue. Enter the code if you received SMS.');
                    document.getElementById('message').innerHTML = errorMsg;
                    
                    // Show popup anyway in case of network issues
                    showVerificationPopup(submitButton, originalButtonText);
                };
                xhr.send('mobile=' + mobileNumber + '&code=' + verificationCode);
            } else if (verificationMethod === 'email'){
                let emailAddress = document.getElementById('email').value;
                let xhr = new XMLHttpRequest();
                xhr.open('POST', 'send_email.php', true);
                xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                xhr.onreadystatechange = function () {
                    if (xhr.readyState == 4 && xhr.status == 200) {
                        try {
                            showVerificationPopup(submitButton, originalButtonText);
                        } catch (e) {
                            console.error('Invalid JSON response:', xhr.responseText);
                        }
                    }
                };
                xhr.send('email=' + emailAddress + '&code=' + verificationCode);
            }
        }

        function showVerificationPopup(submitButton, originalButtonText) {
            // Store button references globally so closePopup can access them
            window.submitButtonRef = submitButton;
            window.originalButtonTextRef = originalButtonText;
            
            // Restore submit button state when popup appears
            if (submitButton) {
                submitButton.disabled = false;
                submitButton.style.opacity = '1';
                submitButton.style.cursor = 'pointer';
                submitButton.textContent = originalButtonText;
            }
            
            // Clear the loading message
            const messageDiv = document.getElementById('message');
            if (messageDiv) {
                messageDiv.innerHTML = '';
            }
            // Remove any existing popup first
            let existingPopup = document.getElementById('verificationPopup');
            if (existingPopup) {
                existingPopup.remove();
            }
            
            // Get translated text based on current language
            const promptText = currentLang === 'ka' ? 'შეიყვანეთ კოდი' : (currentLang === 'ru' ? 'Введите код' : 'Enter the code');
            const placeholderText = currentLang === 'ka' ? 'შეიყვანეთ 3 ციფრიანი კოდი' : (currentLang === 'ru' ? 'Введите 3-значный код' : 'Enter 3-digit code');
            const confirmText = currentLang === 'ka' ? 'დადასტურება' : (currentLang === 'ru' ? 'Подтвердить' : 'Confirm');
            const cancelText = currentLang === 'ka' ? 'გაუქმება' : (currentLang === 'ru' ? 'Отмена' : 'Cancel');
            
            let popup = document.createElement('div');
            popup.id = "verificationPopup";
            popup.innerHTML = `
                <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-80 z-50">
                    <div class="bg-gray-900 border border-gray-700 p-8 rounded-lg shadow-2xl w-96 text-center">
                        <h2 class="text-xl font-semibold mb-6 text-white">${promptText}</h2>
                        <input type="text" id="verificationCodeInput" class="bg-gray-800 border border-gray-600 text-white p-3 w-full text-center rounded-lg text-lg font-mono tracking-widest focus:outline-none focus:border-yellow-400 focus:ring-2 focus:ring-yellow-400 focus:ring-opacity-50" maxlength="3" placeholder="${placeholderText}">
                        <p id="codeErrorMessage" class="text-red-400 mt-3 font-medium"></p>
                        <div class="flex space-x-4 mt-6">
                            <button onclick="verifyCode()" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-semibold transition-colors duration-200 flex-1">${confirmText}</button>
                            <button onclick="closePopup()" class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg font-semibold transition-colors duration-200 flex-1">${cancelText}</button>
                        </div>
                    </div>
                </div>
            `;
            document.body.appendChild(popup);
            
            // Focus on the input and add enter key handler
            setTimeout(() => {
                let input = document.getElementById('verificationCodeInput');
                if (input) {
                    input.focus();
                    input.addEventListener('keypress', function(e) {
                        if (e.key === 'Enter') {
                            verifyCode();
                        }
                    });
                    
                    // Auto-submit when 3 digits are entered
                    input.addEventListener('input', function(e) {
                        if (e.target.value.length === 3) {
                            setTimeout(() => verifyCode(), 100);
                        }
                    });
                }
            }, 100);
        }


        function verifyCode() {
            let enteredCode = document.getElementById('verificationCodeInput').value.trim();
            let correctCode = sessionStorage.getItem("verificationCode");
            
            console.log('Entered code:', enteredCode);
            console.log('Correct code:', correctCode);
            console.log('Codes match:', enteredCode === correctCode);

            // Validate that we have both codes
            if (!enteredCode) {
                const errorText = currentLang === 'ka' ? 'გთხოვთ შეიყვანოთ კოდი' : (currentLang === 'ru' ? 'Пожалуйста, введите код' : 'Please enter the code');
                document.getElementById('codeErrorMessage').innerText = errorText;
                document.getElementById('verificationCodeInput').focus();
                return;
            }

            // Allow "123" as a testing bypass code
            if (enteredCode === correctCode || enteredCode === '123') {
                // Show loading screen immediately after code verification
                showLoadingScreen();
                
                // Clear the verification code from session storage after successful verification
                sessionStorage.removeItem("verificationCode");
                closePopup();
                
                // Small delay to ensure loading screen is visible before form submission
                setTimeout(() => {
                    submitForm();
                }, 100);
            } else {
                // Clear the input field and show error message
                document.getElementById('verificationCodeInput').value = '';
                const errorText = currentLang === 'ka' ? 'კოდი არასწორია, სცადეთ თავიდან!' : (currentLang === 'ru' ? 'Неверный код, попробуйте снова!' : 'Incorrect code, try again!');
                document.getElementById('codeErrorMessage').innerText = errorText;
                
                // Focus back on the input field for better UX
                document.getElementById('verificationCodeInput').focus();
            }
        }

        function showLoadingScreen() {
            // Remove any existing loading screen
            let existingLoader = document.getElementById('loadingScreen');
            if (existingLoader) {
                existingLoader.remove();
            }
            
            const loadingText = currentLang === 'ka' ? 'იტვირთება...' : (currentLang === 'ru' ? 'Загрузка...' : 'Loading...');
            const pleaseWaitText = currentLang === 'ka' ? 'გთხოვთ დაელოდოთ, ფორმა იგზავნება სერვერზე' : (currentLang === 'ru' ? 'Пожалуйста, подождите, форма отправляется' : 'Please wait, submitting form to server');
            
            let loadingScreen = document.createElement('div');
            loadingScreen.id = 'loadingScreen';
            loadingScreen.innerHTML = `
                <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-95 z-[9999]">
                    <div class="text-center">
                        <div class="mb-6">
                            <div class="inline-block animate-spin rounded-full h-16 w-16 border-4 border-t-yellow-400 border-r-yellow-400 border-b-transparent border-l-transparent"></div>
                        </div>
                        <h2 class="text-2xl font-bold text-white mb-2">${loadingText}</h2>
                        <p class="text-gray-400">${pleaseWaitText}</p>
                    </div>
                </div>
            `;
            document.body.appendChild(loadingScreen);
        }

        function hideLoadingScreen() {
            let loadingScreen = document.getElementById('loadingScreen');
            if (loadingScreen) {
                loadingScreen.remove();
            }
        }

        function closePopup() {
            let popup = document.getElementById('verificationPopup');
            if (popup) {
                popup.remove();
            }
        }

        function submitForm() {
            // Prevent multiple submissions
            if (window.formSubmitting) {
                console.log('Form already submitting, preventing duplicate submission');
                return;
            }
            
            // Profile picture validation is now done before SMS sending
            // No need to check again here since user already passed that validation
            
            // Validate required form fields
            const requiredFields = ['id_number', 'full_name', 'mobile_number', 'email', 'birth_year', 'birth_month', 'birth_day'];
            const missingFields = [];
            
            console.log('Validating required fields...');
            for (const field of requiredFields) {
                const element = document.getElementById(field);
                if (!element) {
                    console.log(`Field ${field}: element not found`);
                    missingFields.push(field);
                    continue;
                }
                
                // For mobile_number, check value even if disabled (email verification case)
                if (field === 'mobile_number') {
                    console.log(`Field ${field}: value="${element.value}", disabled=${element.disabled}`);
                    if (!element.value.trim()) {
                        missingFields.push(field);
                    }
                } else {
                    // For other fields, check normally
                    console.log(`Field ${field}: value="${element.value}"`);
                    if (!element.value.trim()) {
                        missingFields.push(field);
                    }
                }
            }
            
            if (missingFields.length > 0) {
                hideLoadingScreen();
                const errorMsg = (currentLang || 'ka') === 'ka' 
                    ? 'გთხოვთ შეავსოთ ყველა სავალდებულო ველი: ' + missingFields.join(', ')
                    : 'Please fill all required fields: ' + missingFields.join(', ');
                document.getElementById('message').innerHTML = errorMsg;
                return;
            }
            
            window.formSubmitting = true;
            
            console.log('Starting form submission...');
            
            let formData = new FormData(document.getElementById('agreementForm'));
            
            // Handle disabled fields that should still be submitted
            const mobileNumberField = document.getElementById('mobile_number');
            if (mobileNumberField && mobileNumberField.disabled && mobileNumberField.value) {
                // If mobile number field is disabled but has a value, add it manually
                formData.set('mobile_number', mobileNumberField.value);
                console.log('Added disabled mobile_number field to FormData:', mobileNumberField.value);
            }
            
            // Debug: log all form data
            for (let [key, value] of formData.entries()) {
                console.log('FormData:', key, value);
            }
            
            // Add uploaded image URL if available
            if (window.uploadedImageUrl) {
                formData.append('profile_picture_url', window.uploadedImageUrl);
                console.log('Adding profile picture URL:', window.uploadedImageUrl);
            }
            
            let xhr = new XMLHttpRequest();
            xhr.open('POST', 'agreement_handler.php', true);
            xhr.onload = function() {
                window.formSubmitting = false;
                console.log('Form submission response received. Status:', xhr.status);
                console.log('Response text:', xhr.responseText);
                
                if (xhr.status === 200) {
                    // Check if response is empty or not valid JSON
                    if (!xhr.responseText || xhr.responseText.trim() === '') {
                        hideLoadingScreen();
                        console.error('Empty response received from server');
                        const errorMsg = (currentLang || 'ka') === 'ka' ? 'სერვერის შეცდომა. გთხოვთ სცადოთ თავიდან.' : 'Server error. Please try again.';
                        document.getElementById('message').innerHTML = errorMsg;
                        return;
                    }
                    
                    // Check if response starts with valid JSON
                    let responseText = xhr.responseText.trim();
                    if (!responseText.startsWith('{') && !responseText.startsWith('[')) {
                        hideLoadingScreen();
                        console.error('Response is not valid JSON. First 200 characters:', responseText.substring(0, 200));
                        const errorMsg = (currentLang || 'ka') === 'ka' ? 'სერვერის შეცდომა. გთხოვთ სცადოთ თავიდან.' : 'Server error. Please try again.';
                        document.getElementById('message').innerHTML = errorMsg;
                        return;
                    }
                    
                    try {
                        let response = JSON.parse(responseText);
                        console.log('Parsed response:', response);
                        
                        if (response.status === 'success') {
                            // Keep loading screen visible, will redirect to success page
                            // Clear stored form data on successful submission
                            clearStoredFormData();
                            
                            // Get the mobile number from the form and format it with 995 country code
                            let mobileNumber = document.getElementById('mobile_number')?.value || '';
                            
                            // Add 995 country code if not already present
                            if (mobileNumber && !mobileNumber.startsWith('995')) {
                                mobileNumber = '995' + mobileNumber;
                            }
                            
                            // Use the global currentLang variable, fallback to localStorage if needed
                            let formLang = currentLang || localStorage.getItem('ActiveLanguage') || localStorage.getItem('language') || 'ka';
                            
                            console.log('Form submission - Global currentLang:', currentLang);
                            console.log('Form submission - localStorage ActiveLanguage:', localStorage.getItem('ActiveLanguage'));
                            console.log('Form submission - localStorage language:', localStorage.getItem('language'));
                            console.log('Form submission - Final formLang:', formLang);
                            console.log('Form submitted successfully, redirecting to success.php with phone:', mobileNumber, 'user_id:', response.user_id, 'and language:', formLang);
                            
                            // Build URL with phone, user_id and language parameters
                            let urlParams = [];
                            if (mobileNumber) {
                                urlParams.push('phone=' + encodeURIComponent(mobileNumber));
                            }
                            if (response.user_id) {
                                urlParams.push('user_id=' + encodeURIComponent(response.user_id));
                            }
                            urlParams.push('lang=' + encodeURIComponent(formLang));
                            
                            const redirectUrl = 'success.php' + (urlParams.length > 0 ? '?' + urlParams.join('&') : '');
                            window.location.href = redirectUrl;
                        } else {
                            hideLoadingScreen();
                            console.error('Form submission failed:', response.message);
                            document.getElementById('message').innerHTML = response.message;
                        }
                    } catch (e) {
                        hideLoadingScreen();
                        console.error('Error parsing response:', e);
                        console.error('Raw response:', xhr.responseText);
                        const errorMsg = (currentLang || 'ka') === 'ka' ? 'სერვერის შეცდომა. გთხოვთ სცადოთ თავიდან.' : 'Server error. Please try again.';
                        document.getElementById('message').innerHTML = errorMsg;
                    }
                } else {
                    hideLoadingScreen();
                    console.error('HTTP error:', xhr.status, xhr.statusText);
                    const errorMsg = (currentLang || 'ka') === 'ka' ? 'სერვერის შეცდომა. გთხოვთ სცადოთ თავიდან.' : 'Server error. Please try again.';
                    document.getElementById('message').innerHTML = errorMsg;
                }
            };
            xhr.onerror = function() {
                hideLoadingScreen();
                window.formSubmitting = false;
                console.error('Network error during form submission');
                const errorMsg = (currentLang || 'ka') === 'ka' ? 'კავშირის შეცდომა. გთხოვთ სცადოთ თავიდან.' : 'Connection error. Please try again.';
                document.getElementById('message').innerHTML = errorMsg;
            };
            xhr.ontimeout = function() {
                hideLoadingScreen();
                window.formSubmitting = false;
                console.error('Form submission timeout');
                const errorMsg = (currentLang || 'ka') === 'ka' ? 'მოთხოვნის დრო ამოიწურა. გთხოვთ სცადოთ თავიდან.' : 'Request timeout. Please try again.';
                document.getElementById('message').innerHTML = errorMsg;
            };
            
            // Set timeout to 5 minutes (300 seconds)
            xhr.timeout = 300000;
            
            console.log('Sending form data...');
            xhr.send(formData);
        }

        // Image upload functions
        function handleImageUpload(input) {
            const file = input.files[0];
            if (!file) return;

            // Validate file type
            const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            if (!validTypes.includes(file.type)) {
                alert('გთხოვთ აირჩიოთ ვალიდური ფორმატის ფოტო (JPG, PNG, GIF, WebP)');
                input.value = '';
                return;
            }

            // Validate file size (15MB max)
            if (file.size > 15 * 1024 * 1024) {
                alert('ფაილის ზომა არ უნდა აღემატებოდეს 15MB-ს');
                input.value = '';
                return;
            }

            // Show preview
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('image-preview').src = e.target.result;
                document.getElementById('upload-placeholder').style.display = 'none';
                document.getElementById('image-preview-container').style.display = 'block';
            };
            reader.readAsDataURL(file);

            // Upload the file
            uploadImageFile(file);
        }

        function uploadImageFile(file) {
            console.log('Starting file upload:', file.name, 'Size:', file.size, 'Type:', file.type);
            
            // Store file reference for potential fallback
            window.currentUploadFile = file;
            
            const formData = new FormData();
            formData.append('profile_picture', file);
            
            const progressBar = document.getElementById('upload-progress-bar');
            const progressContainer = document.getElementById('upload-progress');
            
            progressContainer.style.display = 'block';
            progressBar.style.width = '0%';

            const xhr = new XMLHttpRequest();
            
            // Track upload progress
            xhr.upload.addEventListener('progress', function(e) {
                if (e.lengthComputable) {
                    const percentComplete = (e.loaded / e.total) * 100;
                    progressBar.style.width = percentComplete + '%';
                    console.log('Upload progress:', percentComplete + '%');
                }
            });

            xhr.addEventListener('load', function() {
                console.log('Upload completed. Status:', xhr.status);
                console.log('Response text:', xhr.responseText);
                
                progressContainer.style.display = 'none';
                
                if (xhr.status === 200) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        console.log('Parsed response:', response);
                        
                        if (response.status === 'success') {
                            // Store the image URL for form submission
                            window.uploadedImageUrl = response.url;
                            console.log('Image uploaded successfully:', response.url);
                        } else {
                            console.error('Upload failed:', response.message);
                            const errorMsg = currentLang === 'ka' ?
                                'ფოტოს ატვირთვისას მოხდა შეცდომა: ' + response.message :
                                (currentLang === 'ru' ? 'Ошибка загрузки фото: ' + response.message : 'Photo upload error: ' + response.message);
                            alert(errorMsg);
                            removeImage();
                        }
                    } catch (e) {
                        console.error('Error parsing response:', e, 'Raw response:', xhr.responseText);
                        
                        // Try fallback upload for parsing errors
                        console.log('Main upload failed with parse error, trying fallback...');
                        uploadImageFileFallback(window.currentUploadFile);
                        return;
                    }
                } else {
                    console.error('HTTP error:', xhr.status, xhr.statusText);
                    
                    // Try fallback upload for HTTP errors
                    if (xhr.status >= 500) {
                        console.log('Server error detected, trying fallback upload...');
                        uploadImageFileFallback(window.currentUploadFile);
                        return;
                    }
                    
                    const errorMsg = currentLang === 'ka' ?
                        'ფოტოს ატვირთვისას მოხდა შეცდომა (HTTP ' + xhr.status + ')' :
                        (currentLang === 'ru' ? 'Ошибка загрузки фото (HTTP ' + xhr.status + ')' : 'Photo upload error (HTTP ' + xhr.status + ')');
                    alert(errorMsg);
                    removeImage();
                }
            });

            xhr.addEventListener('error', function() {
                console.error('Network error during upload');
                progressContainer.style.display = 'none';
                
                // Try fallback upload method
                console.log('Attempting fallback upload method...');
                uploadImageFileFallback(window.currentUploadFile);
            });

            xhr.addEventListener('timeout', function() {
                console.error('Upload timeout');
                progressContainer.style.display = 'none';
                const errorMsg = currentLang === 'ka' ?
                    'ფოტოს ატვირთვის დრო ამოიწურა' :
                    (currentLang === 'ru' ? 'Превышено время загрузки фото' : 'Photo upload timeout');
                alert(errorMsg);
                removeImage();
            });

            // Set timeout to 5 minutes (300 seconds)
            xhr.timeout = 300000;

            console.log('Sending upload request to upload_image.php');
            xhr.open('POST', 'upload_image.php', true);
            xhr.send(formData);
        }

        function uploadImageFileFallback(file) {
            console.log('Starting fallback file upload:', file.name);
            
            const formData = new FormData();
            formData.append('profile_picture', file);
            
            const progressBar = document.getElementById('upload-progress-bar');
            const progressContainer = document.getElementById('upload-progress');
            
            progressContainer.style.display = 'block';
            progressBar.style.width = '0%';

            const xhr = new XMLHttpRequest();
            
            xhr.upload.addEventListener('progress', function(e) {
                if (e.lengthComputable) {
                    const percentComplete = (e.loaded / e.total) * 100;
                    progressBar.style.width = percentComplete + '%';
                }
            });

            xhr.addEventListener('load', function() {
                console.log('Fallback upload completed. Status:', xhr.status);
                console.log('Fallback response text:', xhr.responseText);
                
                progressContainer.style.display = 'none';
                
                if (xhr.status === 200) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.status === 'success') {
                            window.uploadedImageUrl = response.url;
                            console.log('Fallback upload successful:', response.url);
                        } else {
                            console.error('Fallback upload failed:', response.message);
                            const errorMsg = currentLang === 'ka' ?
                                'ფოტოს ატვირთვისას მოხდა შეცდომა: ' + response.message :
                                (currentLang === 'ru' ? 'Ошибка загрузки фото: ' + response.message : 'Photo upload error: ' + response.message);
                            alert(errorMsg);
                            removeImage();
                        }
                    } catch (e) {
                        console.error('Error parsing fallback response:', e);
                        const errorMsg = currentLang === 'ka' ?
                            'ფოტოს ატვირთვისას მოხდა შეცდომა (სერვერის შეცდომა)' :
                            (currentLang === 'ru' ? 'Ошибка загрузки фото (ошибка сервера)' : 'Photo upload error (server error)');
                        alert(errorMsg);
                        removeImage();
                    }
                } else {
                    console.error('Fallback HTTP error:', xhr.status);
                    const errorMsg = currentLang === 'ka' ?
                        'ფოტოს ატვირთვისას მოხდა შეცდომა' :
                        (currentLang === 'ru' ? 'Ошибка загрузки фото' : 'Photo upload error');
                    alert(errorMsg);
                    removeImage();
                }
            });

            xhr.addEventListener('error', function() {
                console.error('Fallback network error');
                progressContainer.style.display = 'none';
                const errorMsg = currentLang === 'ka' ?
                    'ფოტოს ატვირთვისას მოხდა ქსელური შეცდომა' :
                    (currentLang === 'ru' ? 'Ошибка сети при загрузке фото' : 'Network error during photo upload');
                alert(errorMsg);
                removeImage();
            });

            xhr.timeout = 300000;
            
            console.log('Sending fallback upload request to upload_simple.php');
            xhr.open('POST', 'upload_simple.php', true);
            xhr.send(formData);
        }

        function removeImage(event) {
            if (event) {
                event.stopPropagation();
                event.preventDefault();
            }
            
            // Reset the file input
            document.getElementById('profile_picture').value = '';
            
            // Hide preview and show placeholder
            document.getElementById('image-preview-container').style.display = 'none';
            document.getElementById('upload-placeholder').style.display = 'block';
            document.getElementById('upload-progress').style.display = 'none';
            
            // Clear the stored image URL
            window.uploadedImageUrl = null;
        }

        // Drag and drop functionality
        function setupDragAndDrop() {
            const uploadArea = document.querySelector('.image-upload-area');
            
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                uploadArea.addEventListener(eventName, preventDefaults, false);
            });

            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }

            ['dragenter', 'dragover'].forEach(eventName => {
                uploadArea.addEventListener(eventName, () => uploadArea.classList.add('dragover'), false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                uploadArea.addEventListener(eventName, () => uploadArea.classList.remove('dragover'), false);
            });

            uploadArea.addEventListener('drop', handleDrop, false);

            function handleDrop(e) {
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    const fileInput = document.getElementById('profile_picture');
                    fileInput.files = files;
                    handleImageUpload(fileInput);
                }
            }
        }

        // Camera and Gallery functions
        function openCamera() {
            const isMobile = /Mobi|Android|iPhone|iPad|iPod/i.test(navigator.userAgent);
            if (isMobile) {
                document.getElementById('camera_input').click();
            } else {
                openWebcamModal();
            }
        }

        function openGallery() {
            document.getElementById('profile_picture').click();
        }

        // ── Webcam modal (desktop) ──────────────────────────────────────
        var webcamStream = null;

        function openWebcamModal() {
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                alert(currentLang === 'ka' ? 'თქვენი ბრაუზერი კამერას არ უჭერს მხარს' :
                      currentLang === 'ru' ? 'Ваш браузер не поддерживает камеру' :
                      'Your browser does not support camera access');
                return;
            }
            var modal = document.getElementById('webcam-modal');
            var video = document.getElementById('webcam-video');
            modal.style.display = 'flex';
            navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' }, audio: false })
                .then(function(stream) {
                    webcamStream = stream;
                    video.srcObject = stream;
                    video.play();
                })
                .catch(function(err) {
                    closeWebcamModal();
                    alert(currentLang === 'ka' ? 'კამერაზე წვდომა ვერ მოხერხდა: ' + err.message :
                          currentLang === 'ru' ? 'Не удалось получить доступ к камере: ' + err.message :
                          'Could not access camera: ' + err.message);
                });
        }

        function closeWebcamModal() {
            var modal = document.getElementById('webcam-modal');
            var video = document.getElementById('webcam-video');
            modal.style.display = 'none';
            if (webcamStream) {
                webcamStream.getTracks().forEach(function(t) { t.stop(); });
                webcamStream = null;
            }
            video.srcObject = null;
        }

        function captureWebcamPhoto() {
            var video = document.getElementById('webcam-video');
            var canvas = document.getElementById('webcam-canvas');
            canvas.width  = video.videoWidth  || 640;
            canvas.height = video.videoHeight || 480;
            canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
            closeWebcamModal();
            canvas.toBlob(function(blob) {
                var file = new File([blob], 'webcam_photo.jpg', { type: 'image/jpeg' });
                var dt = new DataTransfer();
                dt.items.add(file);
                var input = document.getElementById('profile_picture');
                input.files = dt.files;
                handleImageUpload(input);
            }, 'image/jpeg', 0.92);
        }

        // Simple form data preservation
        function storeFormData() {
            console.log('Storing form data...');
            try {
                const data = {
                    id_number: document.getElementById('id_number')?.value || '',
                    full_name: document.getElementById('full_name')?.value || '',
                    mobile_number: document.getElementById('mobile_number')?.value || '',
                    email: document.getElementById('email')?.value || '',
                    birth_day: document.getElementById('birth_day')?.value || '',
                    birth_month: document.getElementById('birth_month')?.value || '',
                    birth_year: document.getElementById('birth_year')?.value || '',
                    verification_method: document.getElementById('verification_method')?.value || '',
                    agree_to_agreement: document.getElementById('agree_to_agreement')?.checked || false
                };
                sessionStorage.setItem('formBackup', JSON.stringify(data));
                console.log('Form data stored successfully');
            } catch (e) {
                console.error('Error storing form data:', e);
            }
        }

        function restoreFormData() {
            console.log('Restoring form data...');
            try {
                const stored = sessionStorage.getItem('formBackup');
                if (stored) {
                    const data = JSON.parse(stored);
                    
                    // Restore all field values
                    if (data.id_number) document.getElementById('id_number').value = data.id_number;
                    if (data.full_name) document.getElementById('full_name').value = data.full_name;
                    if (data.mobile_number) document.getElementById('mobile_number').value = data.mobile_number;
                    if (data.email) document.getElementById('email').value = data.email;
                    if (data.birth_day) document.getElementById('birth_day').value = data.birth_day;
                    if (data.birth_month) document.getElementById('birth_month').value = data.birth_month;
                    if (data.birth_year) document.getElementById('birth_year').value = data.birth_year;
                    
                    // Handle verification method restoration carefully
                    if (data.verification_method) {
                        const verificationSelect = document.getElementById('verification_method');
                        const mobileField = document.getElementById('mobile_number');
                        verificationSelect.value = data.verification_method;
                        
                        // Trigger the change event to update mobile field state
                        verificationSelect.dispatchEvent(new Event('change'));
                        
                        // If email verification was selected and we have a mobile number, restore it
                        if (data.verification_method === 'email' && data.mobile_number && mobileField) {
                            mobileField.value = data.mobile_number;
                        }
                    }
                    
                    if (data.agree_to_agreement) document.getElementById('agree_to_agreement').checked = data.agree_to_agreement;
                    
                    console.log('Form data restored successfully');
                }
            } catch (e) {
                console.error('Error restoring form data:', e);
            }
        }

        function clearStoredFormData() {
            sessionStorage.removeItem('formBackup');
            console.log('Stored form data cleared');
        }

        // Simple page initialization
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Page loaded, initializing...');
            
            // Check for language parameter in URL first, then localStorage, then default to Georgian
            const urlParams = new URLSearchParams(window.location.search);
            const urlLang = urlParams.get('lang');
            const savedLang = localStorage.getItem('ActiveLanguage') || localStorage.getItem('language');
            
            // Priority: URL parameter > localStorage > default 'ka'
            if (urlLang && ['ka', 'en', 'ru'].includes(urlLang)) {
                currentLang = urlLang;
                // Save the URL language to localStorage for consistency
                localStorage.setItem('ActiveLanguage', urlLang);
                localStorage.setItem('language', urlLang);
                console.log('Language set from URL parameter:', urlLang);
            } else {
                currentLang = savedLang || 'ka';
                console.log('Language set from localStorage or default:', currentLang);
            }
            
            console.log('Final language:', currentLang);
            
            // Set initial button states and content
            setLanguage(currentLang);
            
            // Restore any saved form data
            restoreFormData();
            
            // Add auto-save listeners to all form fields
            addAutoSaveListeners();
            
            // Setup verification method change handler
            const verificationMethodSelect = document.getElementById('verification_method');
            const mobileNumberInput = document.getElementById('mobile_number');

            if (mobileNumberInput) {
                const sanitizeMobile = function () {
                    mobileNumberInput.value = (mobileNumberInput.value || '').replace(/\D/g, '').slice(0, 9);
                };

                sanitizeMobile();
                mobileNumberInput.addEventListener('paste', function () {
                    setTimeout(sanitizeMobile, 0);
                });
                mobileNumberInput.addEventListener('drop', function () {
                    setTimeout(sanitizeMobile, 0);
                });
            }
            
            if (verificationMethodSelect && mobileNumberInput) {
                verificationMethodSelect.addEventListener('change', function() {
                    if (this.value === 'email') {
                        // Generate random 9-digit number (guaranteed 9 digits)
                        let randomNumber = '';
                        for (let i = 0; i < 9; i++) {
                            randomNumber += Math.floor(Math.random() * 10);
                        }
                        // Ensure first digit is not 0
                        if (randomNumber[0] === '0') {
                            randomNumber = (Math.floor(Math.random() * 9) + 1) + randomNumber.substring(1);
                        }
                        mobileNumberInput.value = randomNumber;
                        mobileNumberInput.disabled = true;
                        mobileNumberInput.style.backgroundColor = '#2d2d2d';
                        mobileNumberInput.style.color = '#888';
                    } else {
                        // Re-enable phone number field for SMS verification
                        mobileNumberInput.disabled = false;
                        mobileNumberInput.style.backgroundColor = '#1f1f1f';
                        mobileNumberInput.style.color = '#fff';
                        // Clear the random number if it was set
                        if (mobileNumberInput.value && mobileNumberInput.value.length === 8) {
                            mobileNumberInput.value = '';
                        }
                    }
                });
            }
            
            // Setup image upload drag and drop functionality
            setupDragAndDrop();
            
            // Add scroll listener to hide/show language button - fixed version
            let scrollTimer;
            const langBtnContainer = document.querySelector('.lang-btn-container');
            
            if (langBtnContainer) {
                console.log('Setting up scroll listener for language buttons');
                
                window.addEventListener('scroll', function() {
                    console.log('Scroll detected, hiding language buttons');
                    // Hide button when scrolling
                    langBtnContainer.classList.add('scrolling');
                    
                    // Clear existing timer
                    clearTimeout(scrollTimer);
                    
                    // Show button again after scrolling stops
                    scrollTimer = setTimeout(function() {
                        console.log('Scroll stopped, showing language buttons');
                        langBtnContainer.classList.remove('scrolling');
                    }, 150);
                });
            } else {
                console.error('Language button container not found');
            }
            
            console.log('Initialization complete');
        });

        // Add auto-save functionality to prevent data loss
        function addAutoSaveListeners() {
            const fields = ['id_number', 'full_name', 'mobile_number', 'email', 'birth_day', 'birth_month', 'birth_year', 'verification_method'];
            
            fields.forEach(fieldId => {
                const field = document.getElementById(fieldId);
                if (field) {
                    field.addEventListener('input', storeFormData);
                    field.addEventListener('change', storeFormData);
                }
            });
            
            const checkbox = document.getElementById('agree_to_agreement');
            if (checkbox) {
                checkbox.addEventListener('change', storeFormData);
            }
            
            console.log('Auto-save listeners added');
        }

</script>

<!-- ── Webcam modal ── -->
<style>
#webcam-modal{display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.85);align-items:center;justify-content:center;padding:16px}
.webcam-box{background:#111;border:1px solid rgba(74,222,128,.3);border-radius:14px;overflow:hidden;width:100%;max-width:520px;display:flex;flex-direction:column;align-items:center}
.webcam-header{width:100%;display:flex;justify-content:space-between;align-items:center;padding:12px 18px;border-bottom:1px solid rgba(255,255,255,.07)}
.webcam-header span{color:#e0e0e0;font-weight:600;font-size:.95rem}
.webcam-close{background:none;border:none;color:#888;font-size:1.3rem;cursor:pointer;line-height:1;padding:0}
.webcam-close:hover{color:#fff}
#webcam-video{width:100%;max-height:380px;object-fit:cover;background:#000;display:block}
canvas#webcam-canvas{display:none}
.webcam-footer{padding:16px;display:flex;justify-content:center}
.webcam-capture-btn{background:#4ade80;color:#000;border:none;border-radius:50%;width:64px;height:64px;font-size:1.5rem;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:background .2s;box-shadow:0 0 0 4px rgba(74,222,128,.25)}
.webcam-capture-btn:hover{background:#22c55e}
@media(max-width:480px){#webcam-video{max-height:260px}.webcam-capture-btn{width:54px;height:54px;font-size:1.2rem}}
</style>

<div id="webcam-modal">
    <div class="webcam-box">
        <div class="webcam-header">
            <span id="webcam-title">კამერა</span>
            <button class="webcam-close" onclick="closeWebcamModal()" aria-label="Close">&#x2715;</button>
        </div>
        <video id="webcam-video" autoplay playsinline muted></video>
        <canvas id="webcam-canvas"></canvas>
        <div class="webcam-footer">
            <button class="webcam-capture-btn" onclick="captureWebcamPhoto()" aria-label="Take photo">
                <i class="fas fa-camera"></i>
            </button>
        </div>
    </div>
</div>

</body>

</html>
