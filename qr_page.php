<!DOCTYPE html>
<html lang="ka">
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="title" content="QR Codes - Synergy Gym">
    <meta name="description" content="Scan QR codes to buy membership or contact Synergy Gym for a tour.">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon">
    <link rel="icon" href="img/favicon.ico" type="image/x-icon">
    <title>QR Codes | Synergy Gym</title>

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #000;
            color: #fff;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
            justify-content: center;
            align-items: center;
        }
        
        .qr-container {
            background-color: #111;
            border: 1px solid #333;
            border-radius: 1rem;
            padding: 2rem;
            margin: 1rem;
            text-align: center;
            transition: all 0.4s ease;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 600px;
        }
        
        .qr-container::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.03), transparent);
            animation: shimmer 4s ease-in-out infinite;
            pointer-events: none;
        }
        
        .qr-container:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 20px 40px rgba(255, 223, 6, 0.2);
            border-color: #ffdf06;
        }
        
        .qr-code-box {
            background-color: white;
            padding: 1.5rem;
            border-radius: 1rem;
            margin: 2rem auto;
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: 0 4px 15px rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
            flex-shrink: 0;
        }
        
        .qr-code-box:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 25px rgba(255, 255, 255, 0.2);
        }
        
        .qr-description {
            color: #e0e0e0;
            font-size: 1.5rem;
            font-weight: 700;
            line-height: 1.6;
            margin-bottom: 2rem;
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.7);
            animation: textGlow 4s ease-in-out infinite;
        }
        
        .membership-qr {
            background: linear-gradient(135deg, #ffdf06 0%, #ffd700 100%);
            border: 3px solid #ffdf06;
        }
        
        .whatsapp-qr {
            background: linear-gradient(135deg, #25d366 0%, #128c7e 100%);
            border: 3px solid #25d366;
        }
        
        .membership-qr .qr-description {
            color: #1a1a1a;
            font-weight: 700;
            text-shadow: 0 1px 2px rgba(255, 223, 6, 0.3);
            animation: membershipTextPulse 3s ease-in-out infinite;
        }
        
        .whatsapp-qr .qr-description {
            color: #f0f0f0;
            font-weight: 700;
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.8);
            animation: whatsappTextGlow 3.5s ease-in-out infinite;
        }
        
        .bilingual-description {
            display: flex;
            flex-direction: column;
            gap: 0.8rem;
            align-items: center;
        }
        
        .text-en {
            color: #ffdf06;
            font-weight: 700;
            font-size: 1.2em;
            text-shadow: 0 2px 4px rgba(255, 223, 6, 0.5);
        }
        
        .text-ka {
            color: #e0e0e0;
            font-weight: 600;
            font-size: 1.1em;
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.7);
        }
        
        .membership-qr .text-en {
            color: #000;
            text-shadow: 0 2px 4px rgba(255, 223, 6, 0.5);
        }
        
        .membership-qr .text-ka {
            color: #1a1a1a;
            text-shadow: 0 1px 2px rgba(255, 223, 6, 0.3);
        }
        
        .whatsapp-qr .text-en {
            color: #fff;
            text-shadow: 0 2px 4px rgba(37, 211, 102, 0.5);
        }
        
        .whatsapp-qr .text-ka {
            color: #f0f0f0;
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.8);
        }
        
        /* Mobile responsive */
        @media (max-width: 768px) {
            body {
                padding: 10px;
            }
            
            .qr-container {
                margin: 0.5rem;
                padding: 1.5rem;
                min-height: 500px;
            }
            
            .qr-description {
                font-size: 1.3rem;
                max-width: 100%;
            }
            
            .bilingual-description {
                gap: 0.5rem;
            }
            
            .text-en {
                font-size: 1em;
            }
            
            .text-ka {
                font-size: 0.9em;
            }
        }
        
        @media (max-width: 480px) {
            .qr-code-box {
                padding: 1rem;
            }
            
            .qr-code-box img {
                max-width: 200px;
                height: auto;
            }
        }
        
        /* Eye-catching animations */
        @keyframes textGlow {
            0%, 100% {
                text-shadow: 0 1px 3px rgba(0, 0, 0, 0.7);
                transform: translateY(0);
            }
            50% {
                text-shadow: 0 0 10px rgba(255, 255, 255, 0.3);
                transform: translateY(-2px);
            }
        }
        
        @keyframes membershipTextPulse {
            0%, 100% {
                opacity: 0.9;
                transform: scale(1);
                text-shadow: 0 1px 2px rgba(255, 223, 6, 0.3);
            }
            50% {
                opacity: 1;
                transform: scale(1.02);
                text-shadow: 0 2px 4px rgba(255, 223, 6, 0.5);
            }
        }
        
        @keyframes whatsappTextGlow {
            0%, 100% {
                opacity: 0.95;
                text-shadow: 0 1px 3px rgba(0, 0, 0, 0.8);
                transform: scale(1);
            }
            33% {
                opacity: 1;
                text-shadow: 0 0 8px rgba(255, 255, 255, 0.4), 0 1px 3px rgba(0, 0, 0, 0.8);
                transform: scale(1.01);
            }
            66% {
                opacity: 1;
                text-shadow: 0 0 12px rgba(37, 211, 102, 0.4), 0 1px 3px rgba(0, 0, 0, 0.8);
                transform: scale(1.02);
            }
        }
        
        @keyframes shimmer {
            0% {
                transform: translateX(-100%) translateY(-100%) rotate(45deg);
            }
            50% {
                transform: translateX(50%) translateY(50%) rotate(45deg);
            }
            100% {
                transform: translateX(200%) translateY(200%) rotate(45deg);
            }
        }
    </style>
</head>

<body>
    <!-- QR Codes Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 max-w-6xl mx-auto w-full items-stretch">
        
        <!-- Membership QR Code -->
        <div class="qr-container membership-qr">
            <div class="flex-grow">
                <div class="bilingual-description">
                    <p class="qr-description text-en">
                        ⚡ SCAN NOW and Buy or Renew Your Membership in just two minutes!
                    </p>
                    <p class="qr-description text-ka">
                        ⚡ დაასკანირეთ QR კოდი და შეიძინეთ ან განაახლეთ აბონიმენტი 2 წუთში!
                    </p>
                </div>
            </div>
            <div class="qr-code-box">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=<?php echo urlencode('/'); ?>&bgcolor=ffffff&color=000000" 
                     alt="Membership QR Code" 
                     class="mx-auto"
                     onerror="this.onerror=null; this.src='https://placehold.co/250x250/ffffff/000000?text=Membership+QR';">
            </div>
        </div>

        <!-- WhatsApp QR Code -->
        <div class="qr-container whatsapp-qr">
            <div class="flex-grow">
                <div class="bilingual-description">
                    <p class="qr-description text-en">
                        🎯 Want to see our gym? Scan QR and we will get in touch with you in just two minutes!
                    </p>
                    <p class="qr-description text-ka">
                        🎯 გსურთ სპორტდარბაზის დათვალიერება? დაასკანირეთ QR, ჩვენ 2 წუთში დაგიკავშირდებით!
                    </p>
                </div>
            </div>
            <div class="qr-code-box">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=<?php echo urlencode('https://wa.me/995551195819?text=Hello! I would like to visit the gym and get a tour.'); ?>&bgcolor=ffffff&color=000000" 
                     alt="WhatsApp QR Code" 
                     class="mx-auto"
                     onerror="this.onerror=null; this.src='https://placehold.co/250x250/ffffff/000000?text=WhatsApp+QR';">
            </div>
        </div>

    </div>
</body>
</html>
