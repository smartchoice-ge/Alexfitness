<?php
session_start();
// Make sure this path is correct for your server setup.
include '../mssql_connection.php'; 

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Do NOT remove '995' prefix, as database stores it with the prefix.
    // If the user enters without '995', prepend it for database lookup.
    if (!str_starts_with($username, '995') && strlen($username) == 9) { // Assuming 9 digits for local number
        $username = '995' . $username;
    }

    // --- Your original database logic is now active ---
    // Prepare SQL query
    $sql = "SELECT IdNumber, Phone FROM Clients WHERE Phone = ? AND IdNumber = ?";
    $params = array($username, $password);

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
                $_SESSION['username'] = $username; // Store username in session
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
    <meta name="title" content="Sign In - Luka Qaliashvili, ID: 0172409681">
    <meta name="description" content="Access your Luka Qaliashvili, ID: 0172409681 account. Sign in to manage your membership, book classes, and track your progress.">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="shortcut icon" href="/img/favicon.ico" type="image/x-icon">
    <link rel="icon" href="/img/favicon.ico" type="image/x-icon">
    <title>Sign In | Luka Qaliashvili, ID: 0172409681</title>

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
            border-color: #1a73e8;
            box-shadow: 0 0 0 3px rgba(26, 115, 232, 0.22);
        }
        /* Use the Synergy palette for auth actions */
        .signin-btn {
            background-color: #c8e600;
            color: #000000;
            transition: background-color 0.3s ease, box-shadow 0.3s ease, transform 0.3s ease;
            border-radius: 0.5rem;
        }
        .signin-btn:hover {
            background-color: #1a73e8;
            box-shadow: 0 10px 24px rgba(26, 115, 232, 0.28);
            transform: translateY(-1px);
        }
        /* Language toggle styling */
        .lang-btn {
            background-color: #c8e600;
            color: #000;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease, box-shadow 0.3s ease, transform 0.3s ease;
        }
        .lang-btn:hover {
            background-color: #1a73e8;
            box-shadow: 0 8px 20px rgba(26, 115, 232, 0.28);
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
    </style>
</head>

<body>

<!-- WhatsApp Button -->
<a href="https://wa.me/+995-XXX-XXX-XXX" class="whatsapp-float" target="_blank">
    <div class="whatsapp-button">
        <img src="https://upload.wikimedia.org/wikipedia/commons/6/6b/WhatsApp.svg" alt="WhatsApp" class="whatsapp-icon" onerror="this.onerror=null; this.src='https://placehold.co/28x28/ffffff/25d366?text=WA';">
        <span class="whatsapp-text" name="key_contact_whatsapp">Contact Us</span>
    </div>
</a>
    
<div id="cont">
    <!-- Simplified Header -->
    <header class="w-full p-4 flex justify-end items-center">
        <div onclick="SetLanguage()" name='key_lang' class="lang-btn">ქართული</div>
    </header>
    
    <!-- Sign In Form Section -->
    <div class="flex-grow flex items-center justify-center pb-12">
        <div class="container mx-auto px-4">
            <!-- The form container is now transparent -->
            <div class="max-w-sm w-full p-8 mx-auto">
                <div class="text-center mb-8">
                    <a href="/" class="inline-block bg-black p-3 rounded-lg hover:bg-gray-800 transition-colors duration-300">
                        <!-- Increased logo size -->
                        <img src="/img/logo.png" alt="Luka Qaliashvili, ID: 0172409681 Logo" class="h-16 mx-auto" onerror="this.onerror=null; this.src='https://placehold.co/180x60/cccccc/000000?text=Synergy+Logo';">
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
document.addEventListener('DOMContentLoaded', () => {
    const translations = {
      en: {
        key_lang: 'ქართული',
        key_email_placeholder: 'Phone Number (like 551195819)',
        key_password_placeholder: 'Your ID or passport number',
        key_sign_in_button: 'Sign In',
        key_forgot_password: 'Forgot Username/Password?',
        key_recover_title: 'Recover Your Credentials',
        key_email_label: 'Enter your email address:',
        key_send_credentials: 'Send Credentials to Email',
        key_email_recovery_placeholder: 'your.email@example.com',
        key_contact_whatsapp: 'Contact Us',
      },
      ka: {
        key_lang: 'English',
        key_email_placeholder: 'ტელეფონის ნომერი (551195819)',
        key_password_placeholder: 'პირადი ნომერი ან პასპორტი',
        key_sign_in_button: 'შესვლა',
        key_forgot_password: 'დაგავიწყდათ მონაცემები?',
        key_recover_title: 'მონაცემების აღდგენა',
        key_email_label: 'შეიყვანეთ თქვენი ელ-ფოსტა:',
        key_send_credentials: 'გაგზავნა ელ-ფოსტაზე',
        key_email_recovery_placeholder: 'your.email@example.com',
        key_contact_whatsapp: 'დაგვიკავშირდით',
      }
    };

    // Function to get URL parameters
    function getUrlParameter(name) {
        name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
        var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
        var results = regex.exec(location.search);
        return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
    }

    function applyTranslations(lang) {
        if (!translations[lang]) return;
        document.documentElement.lang = lang;
        const langData = translations[lang];
        
        // Translate elements with 'name' attribute
        document.querySelectorAll('[name^="key_"]').forEach(el => {
            const key = el.getAttribute('name');
            if (langData[key]) {
                el.innerHTML = langData[key];
            }
        });

        // Translate placeholders
        document.querySelectorAll('[data-translate-placeholder]').forEach(el => {
            const key = el.getAttribute('data-translate-placeholder');
            if(langData[key]) {
                el.placeholder = langData[key];
            }
        });
    }

    window.SetLanguage = function() {
        const currentLang = document.documentElement.lang;
        const newLang = currentLang === 'ka' ? 'en' : 'ka';
        // Set both localStorage keys for compatibility
        localStorage.setItem('language', newLang);
        localStorage.setItem('ActiveLanguage', newLang);
        applyTranslations(newLang);
    }

    // Load initial language - check URL parameter first, then localStorage
    const urlLang = getUrlParameter('lang');
    let initialLang = 'ka';
    
    if (urlLang && (urlLang === 'ka' || urlLang === 'en')) {
        // Set both localStorage keys for compatibility
        localStorage.setItem('language', urlLang);
        localStorage.setItem('ActiveLanguage', urlLang);
        initialLang = urlLang;
    } else {
        // Fallback to localStorage
        initialLang = localStorage.getItem('language') || localStorage.getItem('ActiveLanguage') || 'ka';
    }
    
    applyTranslations(initialLang);
    
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
