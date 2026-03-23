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

// Include database connection
include_once '../db_connection.php';
include_once '../mssql_connection.php';
include_once '../mssql_packages_payments_helper.php';

// Initialize message variables
$successMessage = '';
$errorMessage = '';

// Handle form submission to add new package
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_package'])) {
    $package_id = filter_var($_POST['package_id'], FILTER_VALIDATE_INT);
    $price = filter_var($_POST['price'], FILTER_VALIDATE_INT);
    $old_price = isset($_POST['old_price']) && $_POST['old_price'] !== '' ? filter_var($_POST['old_price'], FILTER_VALIDATE_INT) : null;
    $name_geo = trim($_POST['name_geo']);
    $name_eng = trim($_POST['name_eng']);
    $duration_month = filter_var($_POST['duration_month'], FILTER_VALIDATE_INT);
    $description = isset($_POST['description']) ? trim($_POST['description']) : null; // EN
    $description_geo = isset($_POST['description_geo']) ? trim($_POST['description_geo']) : null; // KA
    $deal = isset($_POST['deal']) && $_POST['deal'] !== '' ? trim($_POST['deal']) : '';
    $order_number = isset($_POST['order_number']) && $_POST['order_number'] !== '' ? filter_var($_POST['order_number'], FILTER_VALIDATE_INT) : 0;
    
    if ($package_id !== false && $price !== false && !empty($name_geo) && !empty($name_eng) && $duration_month !== false) {
        // Use MSSQL helper function
        $insertedId = insertPackageWebsite([
            'package_id' => $package_id,
            'price' => $price,
            'old_price' => $old_price,
            'name_geo' => $name_geo,
            'name_eng' => $name_eng,
            'duration_month' => $duration_month,
            'description' => $description,
            'description_geo' => $description_geo,
            'deal' => $deal,
            'order_number' => $order_number
        ]);
        
        if ($insertedId) {
            $successMessage = 'პაკეტი წარმატებით დაემატა!';
        } else {
            $errorMessage = 'შეცდომა პაკეტის დამატებისას';
        }
    } else {
        $errorMessage = 'გთხოვთ შეავსოთ ყველა საჭირო ველი სწორად.';
    }
}

// Handle form submission to update packages
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_packages'])) {
    if (isset($_POST['packages']) && is_array($_POST['packages'])) {
        $all_updated = true;
        foreach ($_POST['packages'] as $id => $package_data) {
            $sanitized_id = filter_var($id, FILTER_VALIDATE_INT);
            if ($sanitized_id === false) {
                $errorMessage = 'Invalid package ID encountered.';
                $all_updated = false;
                break;
            }

            $package_id = filter_var($package_data['package_id'], FILTER_VALIDATE_INT);
            $price = filter_var($package_data['price'], FILTER_VALIDATE_INT);
            $old_price = isset($package_data['old_price']) && $package_data['old_price'] !== '' ? filter_var($package_data['old_price'], FILTER_VALIDATE_INT) : null;
            $name_geo = trim($package_data['name_geo']);
            $name_eng = trim($package_data['name_eng']);
            $duration_month = filter_var($package_data['duration_month'], FILTER_VALIDATE_INT);
            $description = isset($package_data['description']) ? trim($package_data['description']) : null;
            $description_geo = isset($package_data['description_geo']) ? trim($package_data['description_geo']) : null;
            $deal = isset($package_data['deal']) && $package_data['deal'] !== '' ? trim($package_data['deal']) : '';
            $order_number = isset($package_data['order_number']) && $package_data['order_number'] !== '' ? filter_var($package_data['order_number'], FILTER_VALIDATE_INT) : 0;

            if ($package_id !== false && $price !== false && !empty($name_geo) && !empty($name_eng) && $duration_month !== false) {
                // Use MSSQL helper function
                $updated = updatePackageWebsite($sanitized_id, [
                    'package_id' => $package_id,
                    'price' => $price,
                    'old_price' => $old_price,
                    'name_geo' => $name_geo,
                    'name_eng' => $name_eng,
                    'duration_month' => $duration_month,
                    'description' => $description,
                    'description_geo' => $description_geo,
                    'deal' => $deal,
                    'order_number' => $order_number
                ]);
                
                if (!$updated) {
                    $errorMessage = 'Error updating package ID ' . $sanitized_id;
                    $all_updated = false;
                }
            } else {
                $errorMessage = 'Invalid data for package ID ' . $sanitized_id;
                $all_updated = false;
                break;
            }
        }
        if ($all_updated && empty($errorMessage)) {
            $successMessage = 'პაკეტები წარმატებით განახლდა!';
        }
    } else {
        $errorMessage = 'No package data submitted.';
    }
}

// Handle package deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_package'])) {
    $package_id = filter_var($_POST['package_id'], FILTER_VALIDATE_INT);
    if ($package_id !== false) {
        // Use MSSQL helper function
        if (deletePackageWebsite($package_id)) {
            $successMessage = 'პაკეტი წარმატებით წაიშალა!';
        } else {
            $errorMessage = 'შეცდომა პაკეტის წაშლისას';
        }
    } else {
        $errorMessage = 'Invalid package ID.';
    }
}

// Fetch all packages from the database (MSSQL PackagesWebsite)
$packages = getPackagesWebsite('order_number ASC, id ASC');
if (empty($packages) && !$mssqlconn) {
    $errorMessage = "Database connection not established.";
}

// Fetch active packages from MSSQL Packages table (for package_id dropdown)
$mssql_packages = [];
if ($mssqlconn) {
    $sql_mssql = "SELECT ID, Name FROM Packages WHERE Active = 1 ORDER BY Name ASC";
    $stmt_mssql = sqlsrv_query($mssqlconn, $sql_mssql);
    
    if ($stmt_mssql === false) {
        $errors = sqlsrv_errors();
        error_log("MSSQL packages query error: " . print_r($errors, true));
    } else {
        while ($row = sqlsrv_fetch_array($stmt_mssql, SQLSRV_FETCH_ASSOC)) {
            $mssql_packages[] = $row;
        }
        sqlsrv_free_stmt($stmt_mssql);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>პაკეტების მართვა</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .form-input, .form-textarea {
            min-height: 40px;
            transition: all 0.3s ease;
        }
        
        .form-input:focus {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }
        
        .table-row:hover {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        }

        /* Package cards with visible border and optional tinted backgrounds */
        .package-card {
            background-color: #f9fafb; /* gray-50 */
            border: 2px solid #e5e7eb; /* gray-300 */
        }

        .package-card--best {
            background-color: #fff5f5; /* light red tint */
            border-color: #ef4444; /* red-500 */
        }

        .package-card--student {
            background-color: #f0f9ff; /* light blue tint */
            border-color: #3b82f6; /* blue-500 */
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
<body class="bg-gray-50">
    <?php
        $navbarPath = '../components/adminNavbar.php';
        if (file_exists($navbarPath)) {
            include $navbarPath;
        } else {
            $altNavbarPath = '../components/adminNavbar.php';
            if (file_exists($altNavbarPath)) {
                include $altNavbarPath;
            } else {
                echo "<p class='text-red-600 bg-red-100 p-3 text-center fixed top-0 w-full z-10'>Navbar component not found. Please check path.</p>";
            }
        }
    ?>
    
        <div class="gradient-bg py-8">
        <div class="container mx-auto px-4">
            <div class="glass-card rounded-2xl p-6 shadow-xl">
                <h1 class="text-3xl font-bold text-gray-800 mb-2 flex items-center">
                    <i class="fas fa-box-open mr-3 text-blue-600"></i>
                    პაკეტების მართვა
                </h1>
                <p class="text-gray-600">მართეთ სათვისებო პაკეტები და მათი პარამეტრები</p>
            </div>
        </div>
    </div>

        <div class="container mx-auto px-4 -mt-4">

        <?php if ($successMessage): ?>
            <div id="success-alert" class="glass-card border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg shadow-lg" role="alert">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-500 mr-3 text-lg"></i>
                    <div>
                        <p class="font-semibold">წარმატება!</p>
                        <p class="text-sm"><?= htmlspecialchars($successMessage) ?></p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($errorMessage): ?>
            <div id="error-alert" class="glass-card border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg shadow-lg" role="alert">
                 <div class="flex items-center">
                    <i class="fas fa-exclamation-circle text-red-500 mr-3 text-lg"></i>
                    <div>
                        <p class="font-semibold">შეცდომა!</p>
                        <p class="text-sm"><?= htmlspecialchars($errorMessage) ?></p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- MSSQL Packages Display -->
        <div class="glass-card rounded-lg p-4 shadow-sm mb-6">
            <div class="flex items-center gap-3">
                <i class="fas fa-database text-blue-600"></i>
                <label for="mssql_packages" class="text-sm font-medium text-gray-700">MSSQL პაკეტები (Active = 1):</label>
                
                <?php if (!empty($mssql_packages)): ?>
                    <select id="mssql_packages" class="form-select text-xs border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">აირჩიეთ პაკეტი...</option>
                        <?php foreach ($mssql_packages as $mssql_package): ?>
                            <option value="<?= htmlspecialchars($mssql_package['ID']) ?>">
                                ID: <?= htmlspecialchars($mssql_package['ID']) ?> - <?= htmlspecialchars($mssql_package['Name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                <?php else: ?>
                    <span class="text-xs text-gray-500 italic">მონაცემები ვერ მოიძებნა</span>
                <?php endif; ?>
            </div>
        </div>

        <!-- Add New Package Form -->
        <div class="glass-card rounded-2xl p-6 shadow-xl mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                <i class="fas fa-plus-circle mr-3 text-green-600"></i>
                ახალი პაკეტის დამატება
            </h2>
            <form method="post" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
                <div>
                    <label for="package_id" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-hashtag mr-1"></i>პაკეტის ID *
                    </label>
                    <input type="number" 
                           id="package_id" 
                           name="package_id" 
                           required 
                           min="1"
                           placeholder="მაგ: 1"
                           class="form-input block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>
                <div>
                    <label for="price" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-dollar-sign mr-1"></i>ფასი *
                    </label>
                    <input type="number" 
                           id="price" 
                           name="price" 
                           required 
                           min="1"
                           placeholder="მაგ: 150"
                           class="form-input block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>
                <div>
                    <label for="old_price" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-tag mr-1"></i>ძველი ფასი
                    </label>
                    <input type="number" 
                           id="old_price" 
                           name="old_price" 
                           min="1"
                           placeholder="მაგ: 200"
                           class="form-input block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>
                <div>
                    <label for="name_geo" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-language mr-1"></i>სახელი (ქართული) *
                    </label>
                    <input type="text" 
                           id="name_geo" 
                           name="name_geo" 
                           required 
                           maxlength="255"
                           placeholder="მაგ: საშუალო პაკეტი"
                           class="form-input block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>
                <div>
                    <label for="name_eng" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-globe mr-1"></i>სახელი (ინგლისური) *
                    </label>
                    <input type="text" 
                           id="name_eng" 
                           name="name_eng" 
                           required 
                           maxlength="255"
                           placeholder="e.g. Medium Package"
                           class="form-input block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>
                <div>
                    <label for="duration_month" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-calendar mr-1"></i>ხანგრძლივობა (თვე) *
                    </label>
                    <input type="number" 
                           id="duration_month" 
                           name="duration_month" 
                           required 
                           min="1"
                           max="60"
                           placeholder="მაგ: 1"
                           class="form-input block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>
                <div class="lg:col-span-3">
                    <label for="description_geo" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-align-left mr-1"></i>აღწერა (ქართული)
                    </label>
                    <textarea id="description_geo" name="description_geo" rows="3" placeholder="მაგ: პაკეტის აღწერა ქართულად" class="form-textarea block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"></textarea>
                </div>
                <div class="lg:col-span-3">
                    <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-align-left mr-1"></i>Description (English)
                    </label>
                    <textarea id="description" name="description" rows="3" placeholder="e.g. Package description in English" class="form-textarea block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"></textarea>
                </div>
                <div>
                    <label for="deal" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-badge-check mr-1"></i>Deal Badge
                    </label>
                    <select id="deal" name="deal" class="form-input block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="">— None —</option>
                        <option value="BEST DEAL!">BEST DEAL!</option>
                        <option value="STUDENT DEAL!">STUDENT DEAL!</option>
                    </select>
                    <p class="text-xs text-gray-500 mt-1">აირჩიეთ ბეჯი ფასადზე ასასახად. ცარიელი = ბეჯის გარეშე.</p>
                </div>
                <div>
                    <label for="order_number" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-sort-numeric-down mr-1"></i>სორტირების ნომერი
                    </label>
                    <input type="number" id="order_number" name="order_number" min="0" value="0" placeholder="მაგ: 10" class="form-input block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <p class="text-xs text-gray-500 mt-1">0 ნიშნავს, რომ პაკეტი არ გამოჩნდება მთავარ გვერდზე.</p>
                </div>
                <div class="flex items-end">
                    <button type="submit" 
                            name="add_package" 
                            class="btn-primary w-full text-white font-semibold py-3 px-4 rounded-lg shadow-lg hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                        <i class="fas fa-plus mr-2"></i>დამატება
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Existing Packages - Card layout -->
        <?php if (!empty($packages)): ?>
        <form method="post" class="glass-card rounded-2xl p-6 shadow-xl">
            <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                <i class="fas fa-list mr-3 text-blue-600"></i>
                არსებული პაკეტები
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <?php foreach ($packages as $package): ?>
                <?php $dealVal = isset($package['deal']) ? $package['deal'] : ''; $cardTint = (stripos($dealVal, 'BEST') !== false) ? ' package-card--best' : ((stripos($dealVal, 'STUDENT') !== false) ? ' package-card--student' : ''); ?>
                <div class="package-card rounded-xl p-5 shadow<?= $cardTint ?>">
                    <div class="flex items-center justify-between mb-4">
                        <div class="text-sm font-semibold text-gray-700">
                            <i class="fas fa-hashtag mr-1 text-gray-500"></i>#<?= htmlspecialchars($package['id']) ?>
                        </div>
                        <button type="button" 
                                onclick="deletePackage(<?= $package['id'] ?>)"
                                class="text-red-600 hover:text-white bg-red-100 hover:bg-red-600 px-3 py-1.5 rounded-lg font-semibold transition-all duration-200 shadow-sm hover:shadow-md">
                            <i class="fas fa-trash mr-1"></i>წაშლა
                        </button>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1"><i class="fas fa-box mr-1"></i>პაკეტის ID</label>
                            <input type="number" name="packages[<?= $package['id'] ?>][package_id]" value="<?= htmlspecialchars($package['package_id'] ?? '') ?>" min="1" required class="form-input w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1"><i class="fas fa-calendar mr-1"></i>ხანგრძლივობა (თვე)</label>
                            <input type="number" name="packages[<?= $package['id'] ?>][duration_month]" value="<?= htmlspecialchars($package['duration_month'] ?? '') ?>" min="1" max="60" required class="form-input w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1"><i class="fas fa-dollar-sign mr-1"></i>ფასი</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">₾</span>
                                <input type="number" name="packages[<?= $package['id'] ?>][price]" value="<?= htmlspecialchars($package['price']) ?>" min="1" required class="form-input w-full pl-8 border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1"><i class="fas fa-tag mr-1"></i>ძველი ფასი</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">₾</span>
                                <input type="number" name="packages[<?= $package['id'] ?>][old_price]" value="<?= htmlspecialchars($package['old_price'] ?? '') ?>" min="1" class="form-input w-full pl-8 border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1"><i class="fas fa-language mr-1"></i>სახელი (ქართული)</label>
                            <input type="text" name="packages[<?= $package['id'] ?>][name_geo]" value="<?= htmlspecialchars($package['name_geo']) ?>" maxlength="255" required class="form-input w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1"><i class="fas fa-globe mr-1"></i>სახელი (ინგლისური)</label>
                            <input type="text" name="packages[<?= $package['id'] ?>][name_eng]" value="<?= htmlspecialchars($package['name_eng']) ?>" maxlength="255" required class="form-input w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="block text-xs font-semibold text-gray-600 mb-1"><i class="fas fa-align-left mr-1"></i>აღწერა (ქართული)</label>
                        <textarea name="packages[<?= $package['id'] ?>][description_geo]" rows="5" placeholder="აღწერა ქართულად" class="form-textarea w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm auto-grow"><?php echo htmlspecialchars($package['description_geo'] ?? ''); ?></textarea>
                    </div>
                    <div class="mt-4">
                        <label class="block text-xs font-semibold text-gray-600 mb-1"><i class="fas fa-align-left mr-1"></i>Description (English)</label>
                        <textarea name="packages[<?= $package['id'] ?>][description]" rows="5" placeholder="Description in English" class="form-textarea w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm auto-grow"><?php echo htmlspecialchars($package['description'] ?? ''); ?></textarea>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1"><i class="fas fa-badge-check mr-1"></i>Deal</label>
                            <?php $currentDeal = $package['deal'] ?? ''; ?>
                            <select name="packages[<?= $package['id'] ?>][deal]" class="form-input w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <option value="" <?= $currentDeal === '' ? 'selected' : '' ?>>— None —</option>
                                <option value="BEST DEAL!" <?= $currentDeal === 'BEST DEAL!' ? 'selected' : '' ?>>BEST DEAL!</option>
                                <option value="STUDENT DEAL!" <?= $currentDeal === 'STUDENT DEAL!' ? 'selected' : '' ?>>STUDENT DEAL!</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1"><i class="fas fa-sort-numeric-down mr-1"></i>Order</label>
                            <input type="number" name="packages[<?= $package['id'] ?>][order_number]" value="<?= htmlspecialchars($package['order_number'] ?? 0) ?>" min="0" class="form-input w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <p class="text-xs text-gray-500 mt-1">0 ნიშნავს, რომ პაკეტი არ გამოჩნდება მთავარ გვერდზე.</p>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="mt-8 pt-6 border-t border-gray-200">
                <div class="flex justify-end">
                    <button type="submit" name="update_packages" 
                            class="btn-primary text-white font-semibold py-3 px-8 rounded-lg shadow-lg hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                        <i class="fas fa-save mr-2"></i>ყველას განახლება
                    </button>
                </div>
            </div>
        </form>
        <?php elseif (!$errorMessage): ?>
            <div class="glass-card border-l-4 border-yellow-500 text-yellow-700 p-6 rounded-lg shadow-lg" role="alert">
                <div class="flex items-center">
                    <i class="fas fa-info-circle text-yellow-500 mr-3 text-lg"></i>
                    <div>
                        <p class="font-semibold">პაკეტები არ მოიძებნა</p>
                        <p class="text-sm">მონაცემთა ბაზაში პაკეტები არ არის. დაამატეთ პირველი პაკეტი ზემოთ მოცემული ფორმით.</p>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Hidden form for package deletion -->
    <form id="deleteForm" method="post" style="display: none;">
        <input type="hidden" name="package_id" id="deletePackageId">
        <input type="hidden" name="delete_package" value="1">
    </form>

    <script>
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
        }, 5000);

        // Delete package function
        function deletePackage(packageId) {
            if (confirm('დარწმუნებული ხართ, რომ გსურთ ამ პაკეტის წაშლა?')) {
                document.getElementById('deletePackageId').value = packageId;
                document.getElementById('deleteForm').submit();
            }
        }

        // Auto-grow textareas with class .auto-grow
        (function() {
            function autosize(el){
                el.style.height = 'auto';
                el.style.height = (el.scrollHeight + 2) + 'px';
            }
            document.querySelectorAll('textarea.auto-grow').forEach(function(t){
                autosize(t);
                t.addEventListener('input', function(){ autosize(t); });
            });
        })();
    </script>

    </div> <!-- Close mainContent -->
</body>
</html>
