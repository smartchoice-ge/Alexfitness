<?php
// QR Generator is publicly accessible - no login required
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Generator - Synergy Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        @keyframes gradient-shift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }

        @keyframes pulse-glow {
            0%, 100% { box-shadow: 0 0 20px rgba(59, 130, 246, 0.5); }
            50% { box-shadow: 0 0 40px rgba(59, 130, 246, 0.8), 0 0 60px rgba(99, 102, 241, 0.6); }
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes scaleIn {
            from { opacity: 0; transform: scale(0.8); }
            to { opacity: 1; transform: scale(1); }
        }

        .qr-preview {
            min-height: 400px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(-45deg, #667eea, #764ba2, #f093fb, #4facfe);
            background-size: 400% 400%;
            animation: gradient-shift 15s ease infinite;
            border-radius: 1.5rem;
            padding: 3rem;
            position: relative;
            overflow: hidden;
        }

        .qr-preview::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at 50% 50%, rgba(255,255,255,0.1) 0%, transparent 50%);
            animation: pulse-glow 3s ease-in-out infinite;
        }

        .qr-code-container {
            background: white;
            padding: 2.5rem;
            border-radius: 1rem;
            box-shadow: 0 30px 60px -12px rgba(0, 0, 0, 0.25), 
                        0 18px 36px -18px rgba(0, 0, 0, 0.3),
                        inset 0 0 0 1px rgba(255, 255, 255, 0.1);
            animation: scaleIn 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            position: relative;
            z-index: 1;
        }

        .qr-code-container.generated {
            animation: float 6s ease-in-out infinite;
        }

        .qr-placeholder {
            width: 320px;
            height: 320px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.1);
            border: 3px dashed rgba(255, 255, 255, 0.3);
            border-radius: 1rem;
            backdrop-filter: blur(10px);
        }

        .qr-placeholder i {
            animation: float 3s ease-in-out infinite;
        }

        .size-option, .color-option {
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            position: relative;
            overflow: hidden;
        }

        .size-option::before, .color-option::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(59, 130, 246, 0.3);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .size-option:hover::before, .color-option:hover::before {
            width: 300px;
            height: 300px;
        }

        .size-option:hover, .color-option:hover {
            transform: scale(1.1) translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        .size-option.active, .color-option.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-color: transparent;
            transform: scale(1.05);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.5);
        }

        .color-picker {
            width: 100%;
            height: 50px;
            border-radius: 0.5rem;
            border: 3px solid #e5e7eb;
            cursor: pointer;
            transition: all 0.3s;
        }

        .color-picker:hover {
            border-color: #3B82F6;
            transform: scale(1.05);
        }

        #qrcode canvas {
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .history-item {
            transition: all 0.3s;
            animation: slideIn 0.5s ease-out;
        }

        .history-item:hover {
            transform: translateY(-10px) rotate(2deg);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
        }

        .btn-generate {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            background-size: 200% 200%;
            animation: gradient-shift 3s ease infinite;
            position: relative;
            overflow: hidden;
        }

        .btn-generate::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .btn-generate:hover::before {
            width: 400px;
            height: 400px;
        }

        .btn-generate:active {
            transform: scale(0.95);
        }

        .glass-effect {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .shine-effect {
            position: relative;
            overflow: hidden;
        }

        .shine-effect::after {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            bottom: -50%;
            left: -50%;
            background: linear-gradient(to bottom, rgba(255,255,255,0) 0%, rgba(255,255,255,0.3) 50%, rgba(255,255,255,0) 100%);
            transform: rotateZ(60deg) translate(-5em, 7.5em);
            animation: shine 3s infinite;
        }

        @keyframes shine {
            0% { transform: rotateZ(60deg) translate(-5em, 7.5em); }
            100% { transform: rotateZ(60deg) translate(25em, -25em); }
        }

        .pattern-option {
            cursor: pointer;
            transition: all 0.3s;
            border: 3px solid transparent;
        }

        .pattern-option:hover {
            transform: scale(1.1);
            border-color: #3B82F6;
        }

        .pattern-option.active {
            border-color: #667eea;
            box-shadow: 0 0 20px rgba(102, 126, 234, 0.5);
        }

        @keyframes particle-float {
            0%, 100% { transform: translateY(0) translateX(0); }
            25% { transform: translateY(-20px) translateX(10px); }
            50% { transform: translateY(-10px) translateX(-10px); }
            75% { transform: translateY(-30px) translateX(5px); }
        }

        .particle {
            position: absolute;
            background: white;
            border-radius: 50%;
            opacity: 0.2;
            animation: particle-float 6s infinite;
        }

        .qr-type-btn {
            transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }

        .qr-type-btn.active {
            background: linear-gradient(135deg, #3B82F6 0%, #8B5CF6 100%);
            color: white;
            border-color: transparent;
            box-shadow: 0 10px 25px rgba(59, 130, 246, 0.5);
        }

        .qr-type-btn:hover:not(.active) {
            border-color: #3B82F6;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(59, 130, 246, 0.3);
        }
    </style>
</head>
<body class="bg-gray-50">
    
    <div class="min-h-screen">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 shadow-2xl relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-white/10 to-transparent"></div>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 relative z-10">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-4xl font-extrabold text-white flex items-center">
                            <i class="fas fa-qrcode mr-4 animate-pulse"></i>QR Code Generator
                        </h1>
                        <p class="mt-3 text-lg text-blue-100">შექმენით მორგებული QR კოდები ბმულებისთვის, ტექსტისთვის, ტელეფონებისთვის და სხვა</p>
                    </div>
                    <div>
                        <a href="../index.php" class="inline-flex items-center px-6 py-3 bg-white/20 hover:bg-white/30 text-white font-semibold rounded-xl transition-all backdrop-blur-sm border-2 border-white/30">
                            <i class="fas fa-home mr-2"></i>მთავარი
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Input Section -->
                <div class="bg-white rounded-2xl shadow-2xl p-8 border-t-4 border-blue-500">
                    <h2 class="text-2xl font-extrabold text-gray-900 mb-8 flex items-center">
                        <i class="fas fa-sliders-h mr-3 text-blue-600"></i>QR კოდის პარამეტრები
                    </h2>

                    <!-- Type Selection -->
                    <div class="mb-8">
                        <label class="block text-gray-700 font-semibold mb-4 text-lg">
                            <i class="fas fa-layer-group mr-2"></i>QR კოდის ტიპი
                        </label>
                        <div class="grid grid-cols-2 gap-4">
                            <button onclick="setQRType('url')" id="type-url" class="qr-type-btn shine-effect flex items-center justify-center px-5 py-4 border-2 rounded-xl active transform transition-all hover:scale-105">
                                <i class="fas fa-link mr-2"></i>URL/ბმული
                            </button>
                            <button onclick="setQRType('text')" id="type-text" class="qr-type-btn shine-effect flex items-center justify-center px-5 py-4 border-2 rounded-xl transform transition-all hover:scale-105">
                                <i class="fas fa-font mr-2"></i>ტექსტი
                            </button>
                            <button onclick="setQRType('phone')" id="type-phone" class="qr-type-btn shine-effect flex items-center justify-center px-5 py-4 border-2 rounded-xl transform transition-all hover:scale-105">
                                <i class="fas fa-phone mr-2"></i>ტელეფონი
                            </button>
                            <button onclick="setQRType('email')" id="type-email" class="qr-type-btn shine-effect flex items-center justify-center px-5 py-4 border-2 rounded-xl transform transition-all hover:scale-105">
                                <i class="fas fa-envelope mr-2"></i>ელფოსტა
                            </button>
                        </div>
                    </div>

                    <!-- Content Input -->
                    <div class="mb-8">
                        <label for="qrContent" class="block text-gray-700 font-semibold mb-3 text-lg">
                            <i class="fas fa-keyboard mr-2"></i><span id="contentLabel">შეიყვანეთ URL</span>
                        </label>
                        <input type="text" id="qrContent" 
                               placeholder="https://example.com" 
                               class="w-full px-5 py-4 border-2 border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-500 focus:border-blue-500 transition-all text-lg">
                        <p class="mt-2 text-sm text-gray-500 flex items-center" id="contentHint">
                            <i class="fas fa-info-circle mr-2"></i>შეიყვანეთ ვალიდური URL (მაგ., /)
                        </p>
                    </div>

                    <!-- Size Selection -->
                    <div class="mb-8">
                        <label class="block text-gray-700 font-semibold mb-4 text-lg">
                            <i class="fas fa-expand-arrows-alt mr-2"></i>ზომა
                        </label>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div class="size-option shine-effect border-2 border-gray-300 rounded-lg p-4 text-center" data-size="200">
                                <i class="fas fa-mobile-alt text-3xl mb-2 text-gray-600"></i>
                                <div class="font-medium">მცირე</div>
                                <div class="text-sm text-gray-500">200px</div>
                            </div>
                            <div class="size-option active shine-effect border-2 border-gray-300 rounded-lg p-4 text-center" data-size="300">
                                <i class="fas fa-tablet-alt text-3xl mb-2 text-gray-600"></i>
                                <div class="font-medium">საშუალო</div>
                                <div class="text-sm text-gray-500">300px</div>
                            </div>
                            <div class="size-option shine-effect border-2 border-gray-300 rounded-lg p-4 text-center" data-size="400">
                                <i class="fas fa-desktop text-3xl mb-2 text-gray-600"></i>
                                <div class="font-medium">დიდი</div>
                                <div class="text-sm text-gray-500">400px</div>
                            </div>
                            <div class="size-option shine-effect border-2 border-gray-300 rounded-lg p-4 text-center" data-size="500">
                                <i class="fas fa-tv text-3xl mb-2 text-gray-600"></i>
                                <div class="font-medium">ძალიან დიდი</div>
                                <div class="text-sm text-gray-500">500px</div>
                            </div>
                        </div>
                    </div>

                    <!-- Color Customization -->
                    <div class="mb-8">
                        <label class="block text-gray-700 font-semibold mb-4 text-lg">
                            <i class="fas fa-palette mr-2"></i>ფერები
                        </label>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">QR კოდის ფერი</label>
                                <input type="color" id="qrColor" value="#000000" class="color-picker">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">ფონის ფერი</label>
                                <input type="color" id="bgColor" value="#ffffff" class="color-picker">
                            </div>
                            <div class="flex items-end">
                                <button type="button" id="resetColors" onclick="resetColors()" class="w-full bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-3 px-4 rounded-lg transition-all transform hover:scale-105">
                                    <i class="fas fa-undo mr-2"></i>ფერების გადატვირთვა
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Color Presets -->
                    <div class="mb-8">
                        <label class="block text-gray-700 font-semibold mb-4 text-lg">
                            <i class="fas fa-swatchbook mr-2"></i>ფერების შაბლონები
                        </label>
                        <div class="grid grid-cols-3 md:grid-cols-6 gap-3">
                            <div class="color-option shine-effect rounded-lg p-4 text-center cursor-pointer border-2 border-gray-300" onclick="applyColorPreset('#000000', '#ffffff')" data-fg="#000000" data-bg="#ffffff">
                                <div class="w-full h-12 rounded mb-2" style="background: linear-gradient(135deg, #000000 50%, #ffffff 50%);"></div>
                                <div class="text-xs font-medium">კლასიკური</div>
                            </div>
                            <div class="color-option shine-effect rounded-lg p-4 text-center cursor-pointer border-2 border-gray-300" onclick="applyColorPreset('#1E40AF', '#DBEAFE')" data-fg="#1E40AF" data-bg="#DBEAFE">
                                <div class="w-full h-12 rounded mb-2" style="background: linear-gradient(135deg, #1E40AF 50%, #DBEAFE 50%);"></div>
                                <div class="text-xs font-medium">ლურჯი</div>
                            </div>
                            <div class="color-option shine-effect rounded-lg p-4 text-center cursor-pointer border-2 border-gray-300" onclick="applyColorPreset('#BE123C', '#FFE4E6')" data-fg="#BE123C" data-bg="#FFE4E6">
                                <div class="w-full h-12 rounded mb-2" style="background: linear-gradient(135deg, #BE123C 50%, #FFE4E6 50%);"></div>
                                <div class="text-xs font-medium">წითელი</div>
                            </div>
                            <div class="color-option shine-effect rounded-lg p-4 text-center cursor-pointer border-2 border-gray-300" onclick="applyColorPreset('#15803D', '#DCFCE7')" data-fg="#15803D" data-bg="#DCFCE7">
                                <div class="w-full h-12 rounded mb-2" style="background: linear-gradient(135deg, #15803D 50%, #DCFCE7 50%);"></div>
                                <div class="text-xs font-medium">მწვანე</div>
                            </div>
                            <div class="color-option shine-effect rounded-lg p-4 text-center cursor-pointer border-2 border-gray-300" onclick="applyColorPreset('#7C3AED', '#F3E8FF')" data-fg="#7C3AED" data-bg="#F3E8FF">
                                <div class="w-full h-12 rounded mb-2" style="background: linear-gradient(135deg, #7C3AED 50%, #F3E8FF 50%);"></div>
                                <div class="text-xs font-medium">იისფერი</div>
                            </div>
                            <div class="color-option shine-effect rounded-lg p-4 text-center cursor-pointer border-2 border-gray-300" onclick="applyColorPreset('#EA580C', '#FFEDD5')" data-fg="#EA580C" data-bg="#FFEDD5">
                                <div class="w-full h-12 rounded mb-2" style="background: linear-gradient(135deg, #EA580C 50%, #FFEDD5 50%);"></div>
                                <div class="text-xs font-medium">ნარინჯისფერი</div>
                            </div>
                        </div>
                    </div>

                    <!-- Generate Button -->
                    <button onclick="generateQR()" class="btn-generate w-full text-white font-bold py-5 px-8 rounded-xl transition-all duration-300 shadow-2xl hover:shadow-3xl transform hover:scale-105">
                        <i class="fas fa-magic mr-2"></i>QR კოდის გენერირება
                    </button>

                    <!-- Quick Examples -->
                    <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                        <h3 class="text-sm font-semibold text-blue-900 mb-2">
                            <i class="fas fa-lightbulb mr-1"></i>Quick Examples
                        </h3>
                        <div class="space-y-2 text-xs">
                            <div class="flex items-start">
                                <i class="fas fa-link text-blue-600 mt-0.5 mr-2"></i>
                                <span class="text-gray-700">URL: /</span>
                            </div>
                            <div class="flex items-start">
                                <i class="fas fa-phone text-blue-600 mt-0.5 mr-2"></i>
                                <span class="text-gray-700">Phone: +995555123456</span>
                            </div>
                            <div class="flex items-start">
                                <i class="fas fa-envelope text-blue-600 mt-0.5 mr-2"></i>
                                <span class="text-gray-700">Email: info@synergy-gym.ge</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Preview Section -->
                <div class="bg-white rounded-2xl shadow-2xl p-8 border-t-4 border-purple-500">
                    <h2 class="text-2xl font-extrabold text-gray-900 mb-8 flex items-center">
                        <i class="fas fa-eye mr-3 text-purple-600"></i>წინასწარი ნახვა და ჩამოტვირთვა
                    </h2>

                    <!-- QR Code Preview -->
                    <div class="qr-preview mb-6">
                        <div id="qrPlaceholder" class="qr-placeholder">
                            <div class="text-center text-white">
                                <i class="fas fa-qrcode text-8xl mb-4 opacity-50"></i>
                                <p class="text-lg font-semibold">QR კოდი გამოჩნდება აქ</p>
                                <p class="text-sm opacity-75 mt-2">შეავსეთ ფორმა და დააჭირეთ გენერირებას</p>
                            </div>
                        </div>
                        <div id="qrCodeContainer" class="qr-code-container" style="display: none;">
                            <div id="qrcode"></div>
                        </div>
                    </div>

                    <!-- QR Info -->
                    <div id="qrInfo" class="mb-6 p-5 bg-gradient-to-br from-blue-50 to-purple-50 rounded-xl border-2 border-blue-100" style="display: none;">
                        <h3 class="text-sm font-bold text-gray-800 mb-3 flex items-center">
                            <i class="fas fa-info-circle mr-2 text-blue-600"></i>QR კოდის დეტალები
                        </h3>
                        <div class="space-y-2 text-sm text-gray-700">
                            <div class="flex justify-between items-center">
                                <span class="flex items-center"><i class="fas fa-tag mr-2 text-blue-500"></i>ტიპი:</span>
                                <span id="infoType" class="font-bold bg-blue-100 px-3 py-1 rounded-full text-blue-700">-</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="flex items-center"><i class="fas fa-expand-arrows-alt mr-2 text-purple-500"></i>ზომა:</span>
                                <span id="infoSize" class="font-bold bg-purple-100 px-3 py-1 rounded-full text-purple-700">-</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="flex items-center"><i class="fas fa-file-alt mr-2 text-green-500"></i>შინაარსი:</span>
                                <span id="infoContent" class="font-medium truncate ml-2 max-w-xs">-</span>
                            </div>
                        </div>
                    </div>

                    <!-- Download Button -->
                    <button id="downloadBtn" onclick="downloadQR()" class="shine-effect w-full bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-bold py-4 px-6 rounded-xl transition-all duration-300 shadow-2xl hover:shadow-3xl transform hover:scale-105" style="display: none;">
                        <i class="fas fa-download mr-2"></i>QR კოდის ჩამოტვირთვა
                    </button>
                </div>
            </div>

            <!-- Recent History -->
            <div class="mt-8 bg-white rounded-2xl shadow-2xl p-8 border-t-4 border-green-500">
                <h2 class="text-2xl font-extrabold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-history mr-3 text-green-600"></i>ბოლო QR კოდები
                </h2>
                <div id="historyContainer" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                    <div class="text-center text-gray-400 col-span-full py-8">
                        <i class="fas fa-inbox text-4xl mb-2"></i>
                        <p class="text-sm">ჯერ არ არის გენერირებული QR კოდები</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- QR Code Library -->
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
    
    <script>
        let currentType = 'url';
        let currentSize = 300;
        let qrCode = null;
        let history = [];

        // Load history from localStorage
        function loadHistory() {
            const saved = localStorage.getItem('qr_history');
            if (saved) {
                history = JSON.parse(saved);
                renderHistory();
            }
        }

        // Save history to localStorage
        function saveHistory() {
            localStorage.setItem('qr_history', JSON.stringify(history.slice(0, 12)));
        }

        // Set QR Type
        function setQRType(type) {
            currentType = type;
            
            // Update button states
            document.querySelectorAll('.qr-type-btn').forEach(btn => {
                btn.classList.remove('active', 'bg-blue-600', 'text-white', 'border-blue-600');
                btn.classList.add('border-gray-300', 'text-gray-700');
            });
            
            const activeBtn = document.getElementById(`type-${type}`);
            activeBtn.classList.add('active', 'bg-blue-600', 'text-white', 'border-blue-600');
            activeBtn.classList.remove('border-gray-300', 'text-gray-700');

            // Update input placeholder and label
            const input = document.getElementById('qrContent');
            const label = document.getElementById('contentLabel');
            const hint = document.getElementById('contentHint');

            switch(type) {
                case 'url':
                    label.textContent = 'შეიყვანეთ URL';
                    input.placeholder = 'https://example.com';
                    hint.innerHTML = '<i class="fas fa-info-circle mr-2"></i>შეიყვანეთ ვალიდური URL (მაგ., /)';
                    break;
                case 'text':
                    label.textContent = 'შეიყვანეთ ტექსტი';
                    input.placeholder = 'ნებისმიერი ტექსტი...';
                    hint.innerHTML = '<i class="fas fa-info-circle mr-2"></i>შეიყვანეთ ნებისმიერი ტექსტი რომელიც გინდათ დააკოდოთ QR კოდში';
                    break;
                case 'phone':
                    label.textContent = 'შეიყვანეთ ტელეფონის ნომერი';
                    input.placeholder = '+995555123456';
                    hint.innerHTML = '<i class="fas fa-info-circle mr-2"></i>შეიყვანეთ ტელეფონის ნომერი ქვეყნის კოდით';
                    break;
                case 'email':
                    label.textContent = 'შეიყვანეთ ელფოსტა';
                    input.placeholder = 'info@example.com';
                    hint.innerHTML = '<i class="fas fa-info-circle mr-2"></i>შეიყვანეთ ვალიდური ელფოსტის მისამართი';
                    break;
            }

            input.value = '';
        }

        // Set Size
        function setSize(size) {
            currentSize = size;
            
            document.querySelectorAll('.size-option').forEach(btn => {
                btn.classList.remove('active');
            });
            
            event.target.closest('.size-option').classList.add('active');
        }

        // Reset Colors
        function resetColors() {
            document.getElementById('qrColor').value = '#000000';
            document.getElementById('bgColor').value = '#ffffff';
        }

        // Apply Color Preset
        function applyColorPreset(fgColor, bgColor) {
            document.getElementById('qrColor').value = fgColor;
            document.getElementById('bgColor').value = bgColor;
            
            // Update active state
            document.querySelectorAll('.color-option').forEach(opt => {
                opt.classList.remove('active');
            });
            event.target.closest('.color-option').classList.add('active');
        }

        // Generate QR Code
        function generateQR() {
            const content = document.getElementById('qrContent').value.trim();
            
            if (!content) {
                alert('გთხოვთ შეიყვანოთ შინაარსი QR კოდისთვის');
                return;
            }

            // Get colors
            const qrColor = document.getElementById('qrColor').value;
            const bgColor = document.getElementById('bgColor').value;

            // Prepare content based on type
            let qrContent = content;
            switch(currentType) {
                case 'phone':
                    qrContent = `tel:${content}`;
                    break;
                case 'email':
                    qrContent = `mailto:${content}`;
                    break;
            }

            // Clear previous QR code
            const qrcodeDiv = document.getElementById('qrcode');
            qrcodeDiv.innerHTML = '';

            // Generate new QR code with colors
            qrCode = new QRCode(qrcodeDiv, {
                text: qrContent,
                width: currentSize,
                height: currentSize,
                colorDark: qrColor,
                colorLight: bgColor,
                correctLevel: QRCode.CorrectLevel.H
            });

            // Show QR code container with animation
            const container = document.getElementById('qrCodeContainer');
            document.getElementById('qrPlaceholder').style.display = 'none';
            container.style.display = 'block';
            container.classList.add('generated');
            document.getElementById('downloadBtn').style.display = 'block';
            document.getElementById('qrInfo').style.display = 'block';

            // Update info
            document.getElementById('infoType').textContent = currentType.toUpperCase();
            document.getElementById('infoSize').textContent = `${currentSize}x${currentSize}px`;
            document.getElementById('infoContent').textContent = content;

            // Add to history
            history.unshift({
                type: currentType,
                content: content,
                qrContent: qrContent,
                size: currentSize,
                qrColor: qrColor,
                bgColor: bgColor,
                timestamp: new Date().toISOString()
            });
            saveHistory();
            renderHistory();
        }

        // Download QR Code
        function downloadQR() {
            const canvas = document.querySelector('#qrcode canvas');
            if (!canvas) {
                alert('Please generate a QR code first');
                return;
            }

            const url = canvas.toDataURL('image/png');
            const link = document.createElement('a');
            link.download = `qrcode-${currentType}-${Date.now()}.png`;
            link.href = url;
            link.click();
        }

        // Render History
        function renderHistory() {
            const container = document.getElementById('historyContainer');
            
            if (history.length === 0) {
                container.innerHTML = `
                    <div class="text-center text-gray-400 col-span-full py-8">
                        <i class="fas fa-inbox text-4xl mb-2"></i>
                        <p class="text-sm">ჯერ არ არის გენერირებული QR კოდები</p>
                    </div>
                `;
                return;
            }

            container.innerHTML = history.slice(0, 12).map((item, index) => {
                const typeIcons = {
                    url: 'fa-link',
                    text: 'fa-font',
                    phone: 'fa-phone',
                    email: 'fa-envelope'
                };
                const icon = typeIcons[item.type] || 'fa-qrcode';
                const colorStyle = item.qrColor && item.bgColor 
                    ? `background: linear-gradient(135deg, ${item.qrColor} 50%, ${item.bgColor} 50%);`
                    : 'background: linear-gradient(135deg, #000 50%, #fff 50%);';
                
                return `
                    <div class="history-item glass-effect border-2 border-white rounded-xl p-4 hover:shadow-2xl transition-all cursor-pointer transform hover:-translate-y-2" onclick="regenerateFromHistory(${index})">
                        <div class="rounded-lg p-3 mb-3 flex items-center justify-center relative overflow-hidden" style="height: 100px; ${colorStyle}">
                            <div class="absolute inset-0 bg-gradient-to-br from-white/10 to-transparent"></div>
                            <i class="fas ${icon} text-4xl relative z-10" style="color: ${item.bgColor || '#fff'}; text-shadow: 0 2px 4px rgba(0,0,0,0.3);"></i>
                        </div>
                        <div class="text-sm font-semibold text-gray-700 truncate mb-1">${item.content}</div>
                        <div class="flex items-center justify-between text-xs text-gray-500">
                            <span class="flex items-center">
                                <i class="fas ${icon} mr-1"></i>${item.type.toUpperCase()}
                            </span>
                            <span>${item.size}px</span>
                        </div>
                    </div>
                `;
            }).join('');
        }

        // Regenerate from history
        function regenerateFromHistory(index) {
            const item = history[index];
            setQRType(item.type);
            document.getElementById('qrContent').value = item.content;
            
            // Set colors if available
            if (item.qrColor) {
                document.getElementById('qrColor').value = item.qrColor;
            }
            if (item.bgColor) {
                document.getElementById('bgColor').value = item.bgColor;
            }
            
            // Set size
            currentSize = item.size;
            document.querySelectorAll('.size-option').forEach((opt, idx) => {
                opt.classList.remove('active');
                if (parseInt(opt.getAttribute('data-size')) === item.size) {
                    opt.classList.add('active');
                }
            });
            
            generateQR();
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            loadHistory();
            createParticles();
            
            // Setup size option click handlers
            document.querySelectorAll('.size-option').forEach(opt => {
                opt.addEventListener('click', function() {
                    const size = parseInt(this.getAttribute('data-size'));
                    currentSize = size;
                    
                    document.querySelectorAll('.size-option').forEach(btn => {
                        btn.classList.remove('active');
                    });
                    this.classList.add('active');
                });
            });
            
            // Allow Enter key to generate
            document.getElementById('qrContent').addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    generateQR();
                }
            });
        });

        // Create floating particles in QR preview
        function createParticles() {
            const preview = document.querySelector('.qr-preview');
            const particleCount = 15;
            
            for (let i = 0; i < particleCount; i++) {
                const particle = document.createElement('div');
                particle.className = 'particle';
                particle.style.width = Math.random() * 10 + 5 + 'px';
                particle.style.height = particle.style.width;
                particle.style.left = Math.random() * 100 + '%';
                particle.style.top = Math.random() * 100 + '%';
                particle.style.animationDelay = Math.random() * 6 + 's';
                particle.style.animationDuration = (Math.random() * 4 + 4) + 's';
                preview.appendChild(particle);
            }
        }
    </script>

    <style>
        .qr-type-btn {
            transition: all 0.3s;
        }
        .qr-type-btn:hover {
            border-color: #3B82F6;
            transform: translateY(-2px);
        }
        .qr-type-btn.active {
            background: #3B82F6;
            color: white;
            border-color: #3B82F6;
        }
    </style>
</body>
</html>
