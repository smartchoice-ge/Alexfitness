<?php
// Start session with security settings
session_start([
    'cookie_httponly' => true,  // Prevent JavaScript access to session cookie
    'cookie_samesite' => 'Strict'  // Prevent CSRF
]);

// Check if user is logged in
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header('Location: ../login.php');
    exit;
}

// Include MSSQL database connection
include_once '../mssql_connection.php';

function getSharedSettingsConnection()
{
    return sqlsrv_connect("smartchoice.zapto.org,1433", array(
        "Database" => "FitnesTonus",
        "Uid" => "fitnes_tonus",
        "PWD" => "fitnes!@#",
        "Encrypt" => false,
        "TrustServerCertificate" => true,
        "CharacterSet" => "UTF-8",
        "LoginTimeout" => 5,
    ));
}

function resolveSettingsConnection($primaryConn)
{
    if ($primaryConn) {
        $probe = sqlsrv_query($primaryConn, "SELECT TOP 1 id FROM WebsiteSettings");
        if ($probe !== false) {
            $hasRow = sqlsrv_fetch_array($probe, SQLSRV_FETCH_ASSOC);
            sqlsrv_free_stmt($probe);
            if ($hasRow) {
                return $primaryConn;
            }
        }
    }

    return getSharedSettingsConnection();
}

// Initialize success message variable
$successMessage = '';
$errorMessage = '';
$settingsConn = resolveSettingsConnection($mssqlconn);

// Handle form submission to update settings
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    if (isset($_POST['settings']) && is_array($_POST['settings'])) {
        $all_updated = true;
        foreach ($_POST['settings'] as $id => $value) {
            $sanitized_id = filter_var($id, FILTER_VALIDATE_INT);
            if ($sanitized_id === false) {
                $errorMessage = 'Invalid setting ID encountered.';
                $all_updated = false;
                break;
            }

            $stmt = sqlsrv_query($settingsConn, "UPDATE WebsiteSettings SET value = ? WHERE id = ?", array($value, $sanitized_id));
            if ($stmt === false) {
                $errors = sqlsrv_errors();
                $errorMessage = 'Error updating setting ID ' . $sanitized_id . ': ' . ($errors ? $errors[0]['message'] : 'Unknown error');
                $all_updated = false;
            } else {
                sqlsrv_free_stmt($stmt);
            }
        }
        if ($all_updated && empty($errorMessage)) {
            $successMessage = 'განახლება წარმატებით შესრულდა!';
        }
    } else {
        $errorMessage = 'No settings data submitted.';
    }
}

// Fetch all settings from the database
$settings = [];
if ($mssqlconn) {
    $sql = "SELECT id, what, value FROM WebsiteSettings ORDER BY id ASC";
    $result = sqlsrv_query($mssqlconn, $sql);
    if ($result) {
        while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
            $settings[] = $row;
        }
        sqlsrv_free_stmt($result);
    } else {
        $errors = sqlsrv_errors();
        $errorMessage = "Error fetching settings: " . ($errors ? $errors[0]['message'] : 'Unknown error');
    }

    // If Synergy DB has no WebsiteSettings, fall back to shared Tonus settings table.
    if (empty($settings)) {
        $sharedConn = getSharedSettingsConnection();
        if ($sharedConn) {
            $sharedResult = sqlsrv_query($sharedConn, $sql);
            if ($sharedResult) {
                while ($row = sqlsrv_fetch_array($sharedResult, SQLSRV_FETCH_ASSOC)) {
                    $settings[] = $row;
                }
                sqlsrv_free_stmt($sharedResult);
                if (!empty($settings)) {
                    $settingsConn = $sharedConn;
                    $errorMessage = '';
                }
            }
            if ($settingsConn !== $sharedConn) {
                sqlsrv_close($sharedConn);
            }
        }
    }
} else {
    $errorMessage = "Database connection not established.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>პარამეტრები</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        /* Custom style for textarea to make it look like an input but be expandable */
        textarea.form-textarea {
            min-height: 40px; /* Start with a single line height */
            resize: vertical; /* Allow vertical resizing */
        }

        /* Mobile menu button positioning fix */
        @media (max-width: 1023px) {
            #mobileMenuToggle {
                position: fixed !important;
                top: 1rem !important;
                left: 1rem !important;
                z-index: 50 !important;
                display: block !important;
            }
            
            /* Ensure main content has proper padding on mobile */
            .container {
                padding-top: 4rem !important;
            }
            
            /* Ensure sidebar is properly positioned on mobile */
            #sidebar {
                position: fixed !important;
                left: 0 !important;
                top: 0 !important;
                height: 100vh !important;
                width: 16rem !important;
                z-index: 30 !important;
                transition: transform 0.3s ease-in-out !important;
                transform: translateX(-100%) !important; /* Hide by default */
            }
            
            /* When sidebar should be visible on mobile */
            #sidebar.mobile-show {
                transform: translateX(0) !important;
            }
            
            /* Show overlay when sidebar is open */
            #mobileOverlay {
                position: fixed !important;
                inset: 0 !important;
                background-color: rgba(0, 0, 0, 0.5) !important;
                z-index: 20 !important;
            }
        }
    </style>
</head>
<body class="bg-gray-100 text-gray-800">
    <?php
        // Assuming adminNavbar.php is correctly styled and provides necessary JS for its own toggle
        // The path 'components/adminNavbar.php' implies this settings.php file is in the root
        // or one level above a 'components' directory. Adjust if needed.
        $navbarPath = '../components/adminNavbar.php'; // Corrected path assuming settings.php is in admin folder
        if (file_exists($navbarPath)) {
            include $navbarPath;
        } else {
            // Fallback for navbar path if settings.php is in a subdirectory like /admin/
            $altNavbarPath = '../components/adminNavbar.php';
            if (file_exists($altNavbarPath)) {
                include $altNavbarPath;
            } else {
                echo "<p class='text-red-600 bg-red-100 p-3 text-center fixed top-0 w-full z-10'>Navbar component not found. Please check path.</p>";
            }
        }
    ?>
    
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">

        <?php if ($successMessage): ?>
            <div id="success-alert" class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-md shadow-sm" role="alert">
                <div class="flex">
                    <div class="py-1"><svg class="fill-current h-6 w-6 text-green-500 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z"/></svg></div>
                    <div>
                        <p class="font-bold">Success</p>
                        <p class="text-sm"><?= htmlspecialchars($successMessage) ?></p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($errorMessage): ?>
            <div id="error-alert" class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-md shadow-sm" role="alert">
                 <div class="flex">
                    <div class="py-1"><svg class="fill-current h-6 w-6 text-red-500 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 5v6h2V5H9zm0 8h2v2H9v-2z"/></svg></div>
                    <div>
                        <p class="font-bold">Error</p>
                        <p class="text-sm"><?= htmlspecialchars($errorMessage) ?></p>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($settings)): ?>
        <form method="post" class="bg-white shadow-xl rounded-lg p-6 sm:p-8">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/3">
                                აღწერა
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-2/3">
                                მნიშვნელობა
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($settings as $setting): ?>
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    <?= htmlspecialchars($setting['what']) ?>
                                    <input type="hidden" name="settings_id[]" value="<?= $setting['id'] ?>"> </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    <textarea 
                                        name="settings[<?= $setting['id'] ?>]" 
                                        rows="3" 
                                        class="form-textarea block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition-shadow duration-150 ease-in-out hover:shadow-md focus:shadow-lg"
                                        aria-label="Value for <?= htmlspecialchars($setting['what']) ?>"
                                    ><?= htmlspecialchars($setting['value']) ?></textarea>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="mt-8 pt-5 border-t border-gray-200">
                <div class="flex justify-end">
                    <button type="submit" name="update" 
                           class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-md shadow-sm hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-150 ease-in-out">
                        განახლება
                    </button>
                </div>
            </div>
        </form>
        <?php elseif (!$errorMessage): // Only show "No settings found" if there wasn't a DB error already displayed ?>
            <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded-md shadow-sm" role="alert">
                <p class="font-bold">No Settings Found</p>
                <p>There are no settings available in the database to configure.</p>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // JavaScript for the navbar toggle (if not already part of adminNavbar.php)
        // Assuming your navbar has a button with id="menuToggle" and a menu with id="mobileMenu"
        const menuToggle = document.getElementById('menuToggle');
        const mobileMenu = document.getElementById('mobileMenu');
        // Also assuming your navbar has icons for open/close states if you implemented that
        // const menuIconOpen = document.getElementById('menuIconOpen'); 
        // const menuIconClose = document.getElementById('menuIconClose');

        if (menuToggle && mobileMenu) {
            menuToggle.addEventListener('click', function() {
                mobileMenu.classList.toggle('hidden');
                // if (menuIconOpen && menuIconClose) { // If using icon swapping
                //     menuIconOpen.classList.toggle('hidden');
                //     menuIconClose.classList.toggle('hidden');
                // }
                const isExpanded = !mobileMenu.classList.contains('hidden');
                menuToggle.setAttribute('aria-expanded', isExpanded.toString());
            });
        }

        // Mobile menu functionality
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuToggle = document.getElementById('mobileMenuToggle');
            const sidebar = document.getElementById('sidebar');
            const mobileOverlay = document.getElementById('mobileOverlay');
            
            if (mobileMenuToggle && sidebar && mobileOverlay) {
                mobileMenuToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    const isVisible = sidebar.classList.contains('mobile-show');
                    
                    if (isVisible) {
                        sidebar.classList.remove('mobile-show');
                        mobileOverlay.classList.add('hidden');
                    } else {
                        sidebar.classList.add('mobile-show');
                        mobileOverlay.classList.remove('hidden');
                    }
                });
                
                mobileOverlay.addEventListener('click', function() {
                    sidebar.classList.remove('mobile-show');
                    mobileOverlay.classList.add('hidden');
                });
                
                window.addEventListener('resize', function() {
                    if (window.innerWidth >= 1024) {
                        sidebar.classList.remove('mobile-show');
                        mobileOverlay.classList.add('hidden');
                    }
                });
            }
        });

        // Auto-dismiss success/error alerts after a few seconds
        setTimeout(function() {
            const successAlert = document.getElementById('success-alert');
            const errorAlert = document.getElementById('error-alert');
            if (successAlert) {
                successAlert.style.transition = 'opacity 0.5s ease-out';
                successAlert.style.opacity = '0';
                setTimeout(() => successAlert.remove(), 500);
            }
            if (errorAlert) {
                errorAlert.style.transition = 'opacity 0.5s ease-out';
                errorAlert.style.opacity = '0';
                setTimeout(() => errorAlert.remove(), 500);
            }
        }, 5000); // Dismiss after 5 seconds
    </script>

    </div> <!-- Close mainContent -->
</body>
</html>
