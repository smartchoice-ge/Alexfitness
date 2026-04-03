<?php
// Redirect mobile app requests immediately before rendering any HTML
if (isset($_GET['source']) && $_GET['source'] === 'app') {
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $isAndroid = stripos($userAgent, 'android') !== false;

    $androidIntent = 'intent://payment/fail#Intent;scheme=synergy;package=ge.synergy.gym;end';
    $fallbackLinks = [
        'synergy://payment/fail',
        'synergygym://payment/fail',
    ];
    $primaryLink = $isAndroid ? $androidIntent : $fallbackLinks[0];
    $fallbackJson = json_encode($fallbackLinks, JSON_UNESCAPED_SLASHES);

    echo '<!DOCTYPE html><html><head><meta charset="UTF-8">';
    echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
    echo '<title>Returning to Synergy App</title>';
    echo '<style>body{font-family:Inter,Arial,sans-serif;background:#101114;color:#fff;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0;padding:24px}.card{max-width:420px;width:100%;background:#181a1f;border:1px solid #2b2f38;border-radius:16px;padding:28px;text-align:center;box-shadow:0 20px 40px rgba(0,0,0,.25)}h1{font-size:22px;margin:0 0 12px;color:#ff7676}p{line-height:1.5;color:#d4d7dd;margin:0 0 18px}.btn{display:inline-block;background:#ff7676;color:#111;text-decoration:none;padding:12px 18px;border-radius:10px;font-weight:700;margin:6px 0}.btn-secondary{background:#2b2f38;color:#fff}.links{margin-top:16px;font-size:14px}.links a{color:#9fd3ff;word-break:break-all;display:block;margin-top:8px}</style>';
    echo '</head><body>';
    echo '<div class="card">';
    echo '<h1>Returning to Synergy App</h1>';
    echo '<p>If the app does not open automatically, tap the button below.</p>';
    echo '<a class="btn" href="' . htmlspecialchars($primaryLink, ENT_QUOTES, 'UTF-8') . '">Open App</a>';
    echo '<div class="links"><a class="btn btn-secondary" href="https://synergyfitness.ge/payment_fail.php">Stay on Website</a></div>';
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
<html lang="ka">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>გადახდის შეცდომა - Synergy Gym</title>
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

        .error-card {
            background-color: #111;
            border: 1px solid #333;
            border-radius: 1rem;
            transition: all 0.3s ease;
            box-shadow: 0 0 30px rgba(239, 68, 68, 0.1);
        }

        .error-card:hover {
            border-color: #ef4444;
            box-shadow: 0 0 40px rgba(239, 68, 68, 0.2);
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

        .error-icon {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            animation: shake 0.5s ease-in-out;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
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

        .fade-in {
            animation: fadeIn 0.8s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .retry-btn {
            background-color: #ef4444;
            color: white;
            transition: all 0.3s ease;
        }

        .retry-btn:hover {
            background-color: #dc2626;
            transform: translateY(-2px);
        }

        .pulse-error {
            animation: pulse-error 2s infinite;
        }

        @keyframes pulse-error {
            0%, 100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7); }
            50% { box-shadow: 0 0 0 10px rgba(239, 68, 68, 0); }
        }

        .troubleshoot-item {
            border-left: 3px solid #c8e600;
            padding-left: 1rem;
            margin-bottom: 1rem;
        }
    </style>
</head>

<body class="bg-black text-white">
    <div id="cont" class="flex-grow flex flex-col">
        <div class="container mx-auto px-4 py-8 flex items-center justify-center min-h-screen">
            <div class="error-card p-8 md:p-12 max-w-2xl w-full fade-in">
                
                <!-- Header Section -->
                <div class="header-section">
                    <img src="img/logo.png" alt="Synergy Gym" class="logo-img" onerror="this.onerror=null; this.src='https://placehold.co/120x50/ffdf06/000000?text=Synergy';">
                    
                    <div class="error-icon inline-flex items-center justify-center w-24 h-24 rounded-full mb-6 pulse-error">
                        <i class="fas fa-exclamation-triangle text-white text-4xl"></i>
                    </div>
                    
                    <h1 class="text-4xl md:text-5xl font-bold mb-4 text-white" name="key_payment_failed">
                        გადახდა ვერ დასრულდა
                    </h1>
                    <p class="text-xl text-gray-300 mb-8" name="key_payment_failed_message">
                        თქვენი გადახდის პროცესი ვერ დასრულდა. გთხოვთ, სცადოთ ხელახლა.
                    </p>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center mb-8">
                    <button onclick="window.history.back()" class="brand-btn px-8 py-3 rounded-lg text-lg font-semibold transition-all duration-300 hover:scale-105">
                        <i class="fas fa-redo mr-2"></i>
                        ხელახლა ცდა
                    </button>
                </div>

                <!-- Contact Info -->
                <div class="text-center mt-8 pt-6 border-t border-gray-700">
                    <p class="text-gray-400 text-sm mb-4">
                        <i class="fas fa-headset mr-2"></i>
                        24/7 მხარდაჭერის სერვისი
                    </p>
                    <div class="flex justify-center space-x-6 text-sm">
                        <a href="mailto:support@synergy-gym.ge" class="text-ffdf06 hover:text-yellow-400 transition-colors">
                            <i class="fas fa-envelope mr-1"></i>
                            support@synergy-gym.ge
                        </a>
                        <a href="https://wa.me/+995-XXX-XXX-XXX" class="text-ffdf06 hover:text-yellow-400 transition-colors" target="_blank">
                            <i class="fab fa-whatsapp mr-1"></i>
                            WhatsApp
                        </a>
                    </div>
                    
                    <!-- Social Media Links -->
                    <div class="flex justify-center space-x-4 mt-4">
                        <a href="https://www.facebook.com/SynergyGymTbilisi" target="_blank" class="text-gray-400 hover:text-blue-500 transition-colors text-xl">
                            <i class="fab fa-facebook"></i>
                        </a>
                        <a href="https://www.instagram.com/synergy_gym_tbilisi/" target="_blank" class="text-gray-400 hover:text-pink-500 transition-colors text-xl">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="https://wa.me/+995-XXX-XXX-XXX" target="_blank" class="text-gray-400 hover:text-green-500 transition-colors text-xl">
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
            // Initialize language system
            if (typeof ChangeData === 'function') {
                ChangeData();
            }
        });
        
        // Add some interactivity
        document.addEventListener('DOMContentLoaded', function() {
            // Add click tracking for troubleshooting items
            const troubleshootItems = document.querySelectorAll('.troubleshoot-item');
            troubleshootItems.forEach(item => {
                item.addEventListener('click', function() {
                    this.style.backgroundColor = '#1f2937';
                    setTimeout(() => {
                        this.style.backgroundColor = 'transparent';
                    }, 200);
                });
            });

            // Auto-focus on retry button after 3 seconds
            setTimeout(() => {
                const retryBtn = document.querySelector('.brand-btn');
                if (retryBtn) {
                    retryBtn.focus();
                    retryBtn.classList.add('ring-2', 'ring-ffdf06');
                }
            }, 3000);
        });

        // Add error reporting functionality
        function reportError() {
            // This would typically send error details to your analytics
            console.log('Payment error reported at:', new Date().toISOString());
            alert('შეცდომის მოხსენება გაიგზავნა. გმადლობთ!');
        }

        // Keyboard navigation
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                const focusedElement = document.activeElement;
                if (focusedElement.tagName === 'BUTTON') {
                    focusedElement.click();
                }
            }
        });
    </script>
</body>
</html>