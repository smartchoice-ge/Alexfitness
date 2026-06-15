<?php
// Handle logout
if (isset($_POST['logout'])) {
    session_destroy();
    header('Location: /login.php');
    exit;
}

// Get current page for active state
$currentPage = basename($_SERVER['PHP_SELF']);
function isActive($page) {
    global $currentPage;
    return $currentPage === $page ? 'sc-nav-link active' : 'sc-nav-link';
}

// Determine if we're in the admin folder for link path handling
$isAdminFolder = strpos($_SERVER['REQUEST_URI'], '/admin/') !== false;

// Use absolute web paths so the logo works from any sub-folder depth
$logoCandidates = ['/img/smartchoice_logo.png', '/img/gym.png', '/img/logo.svg', '/img/logo_black.svg'];
$logoPath = '/img/smartchoice_logo.png'; // Default
$docRoot = rtrim($_SERVER['DOCUMENT_ROOT'] ?? '', '/');
foreach ($logoCandidates as $candidate) {
    if (is_file($docRoot . $candidate)) {
        $logoPath = $candidate;
        break;
    }
}
$faviconPath = '/img/smartchoice_logo.png';
?>

<script>
    (function() {
        var href = '<?= htmlspecialchars($faviconPath, ENT_QUOTES, 'UTF-8') ?>';
        var links = document.querySelectorAll('link[rel="icon"], link[rel="shortcut icon"]');

        if (links.length === 0) {
            var link = document.createElement('link');
            link.rel = 'icon';
            link.type = 'image/png';
            link.href = href;
            document.head.appendChild(link);
            return;
        }

        links.forEach(function(link) {
            link.href = href;
        });
    })();
</script>

<!-- Ensure Font Awesome is loaded -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

<style>
    :root {
        --sc-primary: #667eea;
        --sc-secondary: #764ba2;
    }
    #sidebar {
        background: linear-gradient(180deg, #ffffff 0%, #f8f9fa 100%);
        border-right: 1px solid #e9ecef;
    }
    .sc-nav-link {
        display: flex;
        align-items: center;
        padding: 12px 16px;
        margin: 2px 8px;
        border-radius: 8px;
        color: #495057;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.3s ease;
    }
    .sc-nav-link:hover {
        background: linear-gradient(135deg, var(--sc-primary) 0%, var(--sc-secondary) 100%);
        color: white;
        transform: translateX(5px);
    }
    .sc-nav-link.active {
        background: linear-gradient(135deg, var(--sc-primary) 0%, var(--sc-secondary) 100%);
        color: white;
    }
    .sc-nav-link i {
        width: 20px;
        margin-right: 10px;
        text-align: center;
    }
    .sc-logo-area {
        background: #ffffff;
        border-bottom: 2px solid #e9ecef;
        padding: 16px;
        display: flex;
        justify-content: center;
    }
    .sc-logo-area img {
        height: 50px;
        width: auto;
    }
    .sc-logout-btn {
        background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
        color: white;
        border: none;
        font-weight: 600;
        padding: 10px 16px;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .sc-logout-btn:hover {
        opacity: 0.9;
        transform: translateY(-1px);
    }
    .sc-section-title {
        font-size: 0.65rem;
        font-weight: 600;
        color: #adb5bd;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 0 16px;
        margin-bottom: 8px;
    }
</style>

<!-- Sidebar Navigation -->
<div id="sidebar" class="fixed left-0 top-0 h-full w-64 shadow-sm z-30 transition-transform duration-300 transform flex flex-col">
    <!-- Sidebar Header / Logo -->
    <div class="sc-logo-area flex-shrink-0">
        <img src="<?= $logoPath ?>" alt="Smart Choice Logo">
    </div>

    <!-- Navigation Menu - Scrollable Area -->
    <nav class="flex-1 overflow-y-auto py-4">
        <!-- Client Management Section -->
        <div class="mb-4">
            <ul style="list-style:none; padding:0; margin:0;">
                <li>
                    <a href="<?= $isAdminFolder ? 'users_list.php' : '/admin/users_list.php' ?>" class="<?= isActive('users_list.php') ?>">
                        <i class="fas fa-users"></i>
                        <span>კლიენტები</span>
                    </a>
                </li>
                <li>
                    <a href="<?= $isAdminFolder ? 'package_settings.php' : '/admin/package_settings.php' ?>" class="<?= isActive('package_settings.php') ?>">
                        <i class="fas fa-cog"></i>
                        <span>საიტზე ფასის მართვა</span>
                    </a>
                </li>
                <li>
                    <a href="<?= $isAdminFolder ? 'payments.php' : '/admin/payments.php' ?>" class="<?= isActive('payments.php') ?>">
                        <i class="fas fa-credit-card"></i>
                        <span>გადახდები საიტზე</span>
                    </a>
                </li>
                <li>
                    <a href="<?= $isAdminFolder ? 'transfer_requests.php' : '/admin/transfer_requests.php' ?>" class="<?= isActive('transfer_requests.php') ?>" style="position:relative">
                        <i class="fas fa-paper-plane"></i>
                        <span>გადარიცხვის მოთხოვნები</span>
                        <?php
                        // Show pending badge
                        if ($mssqlconn ?? false) {
                            $badge = sqlsrv_query($mssqlconn, "SELECT COUNT(*) AS cnt FROM TransferRequests WHERE status='pending'");
                            if ($badge) {
                                $brow = sqlsrv_fetch_array($badge, SQLSRV_FETCH_ASSOC);
                                if ($brow && $brow['cnt'] > 0) {
                                    echo '<span style="margin-left:auto;background:#ef4444;color:#fff;font-size:.65rem;font-weight:700;padding:1px 6px;border-radius:99px;">' . $brow['cnt'] . '</span>';
                                }
                                sqlsrv_free_stmt($badge);
                            }
                        }
                        ?>
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Logout Section - Fixed at Bottom -->
    <div class="flex-shrink-0 p-4 border-t border-gray-200">
        <form method="post" action="">
            <button type="submit" name="logout" class="sc-logout-btn">
                <i class="fas fa-sign-out-alt mr-2"></i>
                გასვლა
            </button>
        </form>
    </div>
</div>

<!-- Mobile Menu Toggle Button -->
<button id="mobileMenuToggle" class="lg:hidden fixed top-4 left-4 z-40 bg-white shadow-lg rounded-lg p-3 text-gray-600 hover:text-purple-600 transition-colors">
    <i class="fas fa-bars w-5 h-5"></i>
</button>

<!-- Mobile Menu Overlay -->
<div id="mobileOverlay" class="lg:hidden fixed inset-0 bg-black bg-opacity-50 z-20 hidden"></div>

<!-- Main Content Wrapper -->
<div id="mainContent" class="lg:ml-64 transition-all duration-300">

<script>
// Sidebar toggle functionality
document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuToggle = document.getElementById('mobileMenuToggle');
    const sidebar = document.getElementById('sidebar');
    const mobileOverlay = document.getElementById('mobileOverlay');
    
    // Mobile menu toggle
    if (mobileMenuToggle) {
        mobileMenuToggle.addEventListener('click', function() {
            sidebar.classList.toggle('-translate-x-full');
            mobileOverlay.classList.toggle('hidden');
        });
    }
    
    // Close mobile menu when clicking overlay
    if (mobileOverlay) {
        mobileOverlay.addEventListener('click', function() {
            sidebar.classList.add('-translate-x-full');
            mobileOverlay.classList.add('hidden');
        });
    }
    
    // Handle responsive behavior
    function handleResize() {
        if (window.innerWidth >= 1024) { // lg breakpoint
            sidebar.classList.remove('-translate-x-full');
            mobileOverlay.classList.add('hidden');
        } else {
            sidebar.classList.add('-translate-x-full');
        }
    }
    
    // Initial check
    handleResize();
    
    // Listen for window resize
    window.addEventListener('resize', handleResize);
});
</script>

<style>
/* Custom styles for better sidebar appearance */
#sidebar {
    height: 100vh;
}

#sidebar nav {
    scrollbar-width: thin;
    scrollbar-color: #cbd5e0 #f7fafc;
}

#sidebar nav::-webkit-scrollbar {
    width: 6px;
}

#sidebar nav::-webkit-scrollbar-track {
    background: #f7fafc;
}

#sidebar nav::-webkit-scrollbar-thumb {
    background: #cbd5e0;
    border-radius: 3px;
}

#sidebar nav::-webkit-scrollbar-thumb:hover {
    background: #a0aec0;
}

/* Ensure sidebar is hidden on mobile initially */
@media (max-width: 1023px) {
    #sidebar {
        transform: translateX(-100%);
    }
}

/* Smooth transitions for content */
#mainContent {
    min-height: 100vh;
}

/* Add some padding to the bottom of the nav to prevent content from being cut off */
#sidebar nav > div:last-child {
    padding-bottom: 1rem;
}
</style>
