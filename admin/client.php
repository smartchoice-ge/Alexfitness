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
include('../db_connection.php');
include('../mssql_connection.php');
include('../mssql_packages_payments_helper.php');
include_once '../params.php';
require_once '../tcpdf_loader.php';

// Synergy runs on MSSQL only. Keep MySQL include for compatibility, but do not block page.
if (!$mssqlconn) {
    die("Connection failed: MSSQL is unavailable.");
}

function ensureClientAgreementPdf(array $client)
{
    if (!loadTcpdfLibrary(dirname(__DIR__))) {
        return null;
    }

    $idNumber = trim((string)($client['id_number'] ?? ''));
    if ($idNumber === '') {
        return null;
    }

    $docsDir = rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/docs/';
    if (!is_dir($docsDir) && !mkdir($docsDir, 0755, true) && !is_dir($docsDir)) {
        return null;
    }

    $pdfPath = $docsDir . $idNumber . '.pdf';
    if (is_file($pdfPath)) {
        return '/docs/' . rawurlencode($idNumber) . '.pdf';
    }

    $year = date('Y');
    $full_name = $client['full_name'] ?? '____________________';
    $id_number = $idNumber;
    $mobile_number = $client['mobile_number'] ?? '____________________';
    $email = $client['email'] ?? '____________________';

    ob_start();
    include '../agreement_template.php';
    $htmlContent = ob_get_clean();

    try {
        $pdf = new TCPDF();
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Synergy');
        $pdf->SetTitle('User Agreement');
        $pdf->SetSubject('User Agreement Form');
        $pdf->AddPage();
        $pdf->SetFont('dejavusans', '', 11);
        $pdf->writeHTML($htmlContent);
        $pdf->Output($pdfPath, 'F');
    } catch (Throwable $e) {
        error_log('Client agreement PDF generation failed: ' . $e->getMessage());
        return null;
    }

    return is_file($pdfPath) ? '/docs/' . rawurlencode($idNumber) . '.pdf' : null;
}

// Initialize success message variable
$successMessage = '';
$errorMessage = '';

// Fetch the client details based on user_id
if (isset($_GET['user_id'])) {
    $user_id = (int) $_GET['user_id']; // Sanitize the user_id input

    if ($user_id <= 0) {
        echo "Invalid user ID.";
        exit;
    }

    // Fetch client details from MSSQL ClientDetailsWebsite
    $client = getClientDetailsById($user_id);
    
    if (!$client) {
        echo "Client not found.";
        exit;
    }

    // Handle form submission for updating client details
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['update'])) {
            // Get updated values from POST data
            $id_number = $_POST['id_number'];
            $mobile_number = $_POST['mobile_number'];
            $full_name = $_POST['full_name'];
            $email = $_POST['email'];
            $birth_date = $_POST['birth_date'];

            // Update client details in MSSQL
            $updateResult = updateClientDetailsById($user_id, [
                'id_number' => $id_number,
                'mobile_number' => $mobile_number,
                'full_name' => $full_name,
                'email' => $email,
                'birth_date' => $birth_date
            ]);
            
            if ($updateResult) {
                $successMessage = 'Client details updated successfully!';
                // Refresh client data
                $client = getClientDetailsById($user_id);
            } else {
                $errorMessage = 'Error updating client details.';
            }
        }

        // Handle deletion of client
        if (isset($_POST['delete'])) {
            // Delete client from MSSQL
            $deleteResult = deleteClientDetailsById($user_id);

            if ($deleteResult) {
                $successMessage = 'Client deleted successfully!';
                header('Location: users_list.php'); // Redirect to client list after deletion
                exit;
            } else {
                $errorMessage = 'Error deleting client.';
            }
        }
    }

    $clientAgreementUrl = ensureClientAgreementPdf($client) ?? '/Synergy-gym-agreement.pdf';

    // Fetch payments for this client from MSSQL PaymentsWebsite
    $payments = [];
    if ($client && $mssqlconn) {
        $payment_sql = "SELECT TOP 10 p.id, p.amount, p.status, p.transaction_id, p.time, p.package_id, p.processed, pkg.name_geo as package_name 
                        FROM PaymentsWebsite p 
                        LEFT JOIN PackagesWebsite pkg ON p.package_id = pkg.package_id
                        WHERE p.user_id = ? AND p.status = 'Success'
                        ORDER BY p.time DESC";
        $payment_stmt = sqlsrv_query($mssqlconn, $payment_sql, array($user_id));
        if ($payment_stmt) {
            while ($row = sqlsrv_fetch_array($payment_stmt, SQLSRV_FETCH_ASSOC)) {
                // Convert DateTime objects
                if ($row['time'] instanceof DateTime) {
                    $row['time'] = $row['time']->format('Y-m-d H:i:s');
                }
                $payments[] = $row;
            }
            sqlsrv_free_stmt($payment_stmt);
        }
    }
} else {
    echo "User ID is required.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Client Details</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            overflow: hidden; /* Ensure inner elements respect rounded corners */
        }
        
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .action-card {
            transition: all 0.3s ease;
            transform: translateY(0);
        }
        
        .action-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }
        
        .client-avatar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-size: 2rem;
            font-weight: bold;
        }
        
        /* Profile picture styles */
        .profile-pic {
            width: 48px;
            height: 48px;
            border-radius: 8px; /* Square with rounded corners */
            object-fit: cover;
            object-position: center center;
            border: 3px solid #e5e7eb;
            transition: all 0.3s ease;
            image-orientation: from-image; /* Respect EXIF orientation */
            aspect-ratio: 1 / 1; /* Force square aspect ratio */
            display: block;
        }
        
        .profile-pic:hover {
            border-color: #3b82f6;
            cursor: pointer;
        }
        
        .no-photo-placeholder {
            width: 48px;
            height: 48px;
            border-radius: 8px; /* Square with rounded corners */
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            border: 3px solid #e5e7eb;
            font-size: 1.5rem;
            color: white;
            text-align: center;
            font-weight: bold;
            aspect-ratio: 1 / 1; /* Force square aspect ratio */
        }
        
        .field-display {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 0.75rem;
            border-radius: 0.5rem;
            color: #4a5568;
        }
        
        .field-edit {
            border: 2px solid #667eea;
            background: white;
        }
        
        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .status-active {
            background-color: #d1fae5;
            color: #065f46;
        }
        
        /* Sidebar mobile transform classes - ensure they work properly */
        .transform {
            transform: var(--tw-transform);
        }
        
        .-translate-x-full {
            --tw-translate-x: -100%;
            transform: translateX(-100%) !important;
        }
        
        .translate-x-0 {
            --tw-translate-x: 0px;
            transform: translateX(0) !important;
        }
        
        /* Ensure sidebar transitions work */
        #sidebar {
            transition: transform 0.3s ease-in-out !important;
        }
        
        /* Force sidebar to be visible when not having -translate-x-full class */
        #sidebar:not(.-translate-x-full) {
            transform: translateX(0) !important;
        }
        
        /* Hidden class for overlay */
        .hidden {
            display: none !important;
        }
        
        /* Mobile sidebar positioning and visibility */
        @media (max-width: 1023px) {
            /* Hide by default on mobile */
            #sidebar {
                position: fixed !important;
                left: 0 !important;
                top: 0 !important;
                height: 100vh !important;
                width: 16rem !important;
                z-index: 30 !important;
                transition: transform 0.3s ease-in-out !important;
                transform: translateX(-100%) !important; /* hidden by default */
            }

            /* When sidebar should be visible on mobile */
            #sidebar.mobile-show {
                transform: translateX(0) !important;
            }

            /* Mobile overlay positioning */
            #mobileOverlay {
                position: fixed !important;
                inset: 0 !important;
                background-color: rgba(0, 0, 0, 0.5) !important;
                z-index: 20 !important;
            }

            /* Mobile menu toggle button */
            #mobileMenuToggle {
                position: fixed !important;
                top: 1rem !important;
                left: 1rem !important;
                z-index: 50 !important;
            }
        }
        
        /* Mobile optimizations */
        @media (max-width: 768px) {
            .container {
                padding-left: 1rem !important;
                padding-right: 1rem !important;
            }
            .container {
                padding-left: 1rem !important;
                padding-right: 1rem !important;
            }
            
            .glass-card {
                padding: 1rem !important;
                margin-bottom: 1rem !important;
            }
            
            .gradient-bg {
                padding-top: 1rem !important;
                padding-bottom: 1rem !important;
            }
            
            /* Header mobile fixes */
            .glass-card .flex.items-center.justify-between {
                flex-direction: column !important;
                gap: 1rem !important;
            }
            
            .glass-card .flex.items-center.space-x-4 {
                width: 100% !important;
                justify-content: center !important;
                text-align: center !important;
            }
            
            .glass-card .flex.space-x-3 {
                width: 100% !important;
                flex-direction: column !important;
                gap: 0.5rem !important;
                padding-bottom: 0.25rem !important; /* keep content inside rounded area */
            }
            
            .glass-card .flex.space-x-3 button {
                display: block !important;
                width: 100% !important;
                box-sizing: border-box !important;
                margin-left: 0 !important;
                margin-right: 0 !important;
                align-self: stretch !important;
                padding: 0.75rem !important;
                font-size: 0.9rem !important;
            }
            
            /* Grid mobile fixes */
            .grid.grid-cols-1.md\\:grid-cols-3 {
                grid-template-columns: 1fr !important;
                gap: 1rem !important;
            }
            
            .grid.grid-cols-1.lg\\:grid-cols-3 {
                grid-template-columns: 1fr !important;
            }
            
            .grid.grid-cols-1.md\\:grid-cols-2 {
                grid-template-columns: 1fr !important;
                gap: 1rem !important;
            }
            
            /* Table mobile fixes */
            .overflow-x-auto {
                -webkit-overflow-scrolling: touch;
            }
            
            .overflow-x-auto table {
                min-width: 100% !important;
                font-size: 0.875rem !important;
            }
            
            .overflow-x-auto th,
            .overflow-x-auto td {
                padding: 0.5rem !important;
                white-space: nowrap;
            }
            
            /* Action cards mobile */
            .action-card {
                margin-bottom: 1rem !important;
            }
            
            .action-card h3 {
                font-size: 1rem !important;
            }
            
            .action-card button {
                font-size: 0.875rem !important;
                padding: 0.75rem 1rem !important;
            }
            
            /* Text sizing for mobile */
            .text-3xl {
                font-size: 1.5rem !important;
            }
            
            .text-2xl {
                font-size: 1.25rem !important;
            }
            
            .text-xl {
                font-size: 1.125rem !important;
            }
            
            /* Profile picture mobile */
            .profile-pic,
            .no-photo-placeholder {
                width: 40px !important;
                height: 40px !important;
                font-size: 1.25rem !important;
            }
            
            /* Mobile spacing */
            .space-y-6 > * + * {
                margin-top: 1rem !important;
            }
            
            .space-y-3 > * + * {
                margin-top: 0.75rem !important;
            }
            
            /* Mobile buttons */
            .bg-white.rounded-2xl.shadow-lg {
                padding: 1rem !important;
                margin-bottom: 1rem !important;
            }
        }
        
        @media (max-width: 576px) {
            .container {
                padding-left: 0.5rem !important;
                padding-right: 0.5rem !important;
            }
            
            .glass-card {
                padding: 0.75rem !important;
                border-radius: 1rem !important;
            }
            
            .bg-white.rounded-2xl {
                border-radius: 1rem !important;
                padding: 0.75rem !important;
            }
            
            /* Very small screen text */
            .text-3xl {
                font-size: 1.25rem !important;
            }
            
            .text-2xl {
                font-size: 1.125rem !important;
            }
            
            /* Compact buttons */
            button {
                padding: 0.5rem 0.75rem !important;
                font-size: 0.8rem !important;
            }
            
            /* Table ultra mobile */
            .overflow-x-auto th,
            .overflow-x-auto td {
                padding: 0.25rem !important;
                font-size: 0.75rem !important;
            }
        }
    </style>
</head>
<body class="bg-gray-50">
    <?php include '../components/adminNavbar.php'; ?>

        <script>
        // Delegated, robust mobile menu toggle (aligns with payments.php)
        document.addEventListener('click', function(e) {
            const toggle = e.target.closest('#mobileMenuToggle');
            if (!toggle) return;

            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('mobileOverlay');
            if (!sidebar || !overlay) return;

            const showing = sidebar.classList.contains('mobile-show');
            if (showing) {
                sidebar.classList.remove('mobile-show');
                overlay.classList.add('hidden');
            } else {
                sidebar.classList.add('mobile-show');
                overlay.classList.remove('hidden');
            }
        });

        // Close on overlay click
        document.addEventListener('click', function(e) {
            if (!e.target.closest('#mobileOverlay')) return;
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('mobileOverlay');
            if (!sidebar || !overlay) return;
            sidebar.classList.remove('mobile-show');
            overlay.classList.add('hidden');
        });

        // Keep state consistent on resize
        window.addEventListener('resize', function() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('mobileOverlay');
            if (!sidebar || !overlay) return;
            if (window.innerWidth >= 1024) {
                sidebar.classList.remove('mobile-show');
                overlay.classList.add('hidden');
            }
        });
        </script>

    <div class="gradient-bg py-8">
        <div class="container mx-auto px-4">
            <!-- Header Section -->
            <div class="glass-card rounded-2xl p-6 shadow-xl">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center space-x-4">
                        <?php 
                        $firstName = strtoupper(substr($client['full_name'], 0, 1));
                        $lastName = '';
                        if (strpos($client['full_name'], ' ') !== false) {
                            $nameParts = explode(' ', $client['full_name']);
                            $lastName = strtoupper(substr($nameParts[1], 0, 1));
                        }
                        $initials = $firstName . $lastName;
                        
                        if (!empty($client['picurl']) && file_exists('../' . $client['picurl'])): ?>
                            <img src="../<?php echo htmlspecialchars($client['picurl']); ?>" 
                                 alt="<?php echo htmlspecialchars($client['full_name']); ?>" 
                                 class="profile-pic"
                                 onclick="document.getElementById('profileImageModal').style.display='block'"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div class="no-photo-placeholder" style="display: none;">
                                <?php echo $initials; ?>
                            </div>
                        <?php else: ?>
                            <div class="no-photo-placeholder">
                                <?php echo $initials; ?>
                            </div>
                        <?php endif; ?>
                        <div>
                            <h1 class="text-3xl font-bold text-gray-800"><?= htmlspecialchars($client['full_name']) ?></h1>
                            <p class="text-gray-600">ID: <?= htmlspecialchars($client['id_number']) ?></p>
                            <span class="status-badge status-active">
                                <i class="fas fa-circle mr-1"></i>
                                Active Client
                            </span>
                        </div>
                    </div>
                    
                    <div class="flex flex-col md:flex-row md:justify-end md:space-x-3 space-y-2 md:space-y-0 items-stretch md:items-center w-full md:w-auto">
                        <button id="editButton" onclick="toggleEdit()" class="inline-flex items-center justify-center btn-primary text-white px-6 py-3 rounded-lg font-semibold w-full md:w-auto md:min-w-[220px]">
                            <i class="fas fa-edit mr-2"></i>
                            რედაქტირება
                        </button>
                        <button id="saveButton" onclick="saveChanges()" style="display: none;" class="inline-flex items-center justify-center bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg font-semibold w-full md:w-auto md:min-w-[180px]">
                            <i class="fas fa-save mr-2"></i>
                            შენახვა
                        </button>
                        <button id="cancelButton" onclick="cancelEdit()" style="display: none;" class="inline-flex items-center justify-center bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-semibold w-full md:w-auto md:min-w-[180px]">
                            <i class="fas fa-times mr-2"></i>
                            გაუქმება
                        </button>
                    </div>
                </div>

                <!-- Success/Error Messages -->
                <?php if ($successMessage): ?>
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
                        <i class="fas fa-check-circle mr-2"></i>
                        <?= htmlspecialchars($successMessage) ?>
                    </div>
                <?php elseif ($errorMessage): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <?= htmlspecialchars($errorMessage) ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    

    <div class="container mx-auto px-4 -mt-4">
        <!-- Client Payments Section -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                <i class="fas fa-credit-card mr-3 text-green-500"></i>
                გადახდების ისტორია
            </h2>
            
            <?php if (!empty($payments)): ?>
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr class="bg-gray-200">
                                <th class="p-3 text-left">ID</th>
                                <th class="p-3 text-left">თანხა</th>
                                <th class="p-3 text-left">სტატუსი</th>
                                <th class="p-3 text-left">ტრანზაქციის ID</th>
                                <th class="p-3 text-left">პაკეტის სახელი</th>
                                <th class="p-3 text-left">დამუშავებული</th>
                                <th class="p-3 text-left">თარიღი</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($payments as $payment): ?>
                                <tr class="border-t hover:bg-gray-50">
                                    <td class="p-3"><?= htmlspecialchars($payment['id']) ?></td>
                                    <td class="p-3 font-medium"><?= number_format($payment['amount'], 2) ?> GEL</td>
                                    <td class="p-3">
                                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <?= htmlspecialchars($payment['status']) ?>
                                        </span>
                                    </td>
                                    <td class="p-3 font-mono text-sm"><?= htmlspecialchars($payment['transaction_id']) ?></td>
                                    <td class="p-3"><?= htmlspecialchars($payment['package_name'] ?: 'N/A') ?></td>
                                    <td class="p-3">
                                        <?php if ($payment['processed'] == 0): ?>
                                            <button class="bg-green-500 hover:bg-green-600 text-white rounded-full p-2 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-green-300" 
                                                    onclick="markAsProcessed(<?= $payment['id'] ?>)" 
                                                    title="Mark as processed">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </button>
                                        <?php else: ?>
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                Done
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="p-3"><?= htmlspecialchars($payment['time']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-receipt text-gray-300 text-3xl mb-3"></i>
                    <p class="text-gray-500 text-base font-medium">გადახდები არ მოიძებნა</p>
                    <p class="text-gray-400 text-sm mt-1">ამ მომხმარებლისთვის წარმატებული გადახდები არ არის</p>
                </div>
            <?php endif; ?>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Main Details Card -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                        <i class="fas fa-user-circle mr-3 text-blue-500"></i>
                        Client Information
                    </h2>
                    
                    <form id="clientForm" method="post" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-user mr-2"></i>Full Name
                                </label>
                                <input type="text" name="full_name" id="full_name" 
                                       value="<?= htmlspecialchars($client['full_name']) ?>" 
                                       class="field-display w-full" readonly>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-id-card mr-2"></i>ID Number
                                </label>
                                <input type="text" name="id_number" id="id_number" 
                                       value="<?= htmlspecialchars($client['id_number']) ?>" 
                                       class="field-display w-full" readonly>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-phone mr-2"></i>Mobile Number
                                </label>
                                <input type="text" name="mobile_number" id="mobile_number" 
                                       value="<?= htmlspecialchars($client['mobile_number']) ?>" 
                                       class="field-display w-full" readonly>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-envelope mr-2"></i>Email Address
                                </label>
                                <input type="email" name="email" id="email" 
                                       value="<?= htmlspecialchars($client['email']) ?>" 
                                       class="field-display w-full" readonly>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-calendar mr-2"></i>Birth Date
                                </label>
                                <input type="text" name="birth_date" id="birth_date" 
                                       value="<?= htmlspecialchars($client['birth_date']) ?>" 
                                       class="field-display w-full" readonly>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-globe mr-2"></i>IP Address
                                </label>
                                <div class="field-display">
                                    <?= htmlspecialchars($client['user_ip']) ?>
                                </div>
                            </div>
                        </div>
                        
                        <input type="hidden" name="update" value="1">
                    </form>
                </div>
            </div>

            <!-- Quick Actions Card -->
            <div class="space-y-6">
                <!-- PDF Documents -->
                <div class="bg-white rounded-2xl shadow-lg p-6 action-card">
                    <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-file-pdf mr-3 text-red-500"></i>
                        PDF Documents
                    </h3>
                    <div class="space-y-3">
                        <a href="<?= htmlspecialchars($clientAgreementUrl) ?>" 
                           target="_blank"
                           class="flex items-center justify-between bg-red-50 hover:bg-red-100 p-3 rounded-lg transition-colors">
                            <span class="font-medium text-red-700">Client Agreement</span>
                            <i class="fas fa-external-link-alt text-red-500"></i>
                        </a>
                    </div>
                </div>

                <!-- Danger Zone -->
                <div class="bg-white rounded-2xl shadow-lg p-6 action-card border-l-4 border-red-500">
                    <button onclick="confirmDelete()" 
                            class="w-full bg-red-500 hover:bg-red-600 text-white font-semibold py-3 px-4 rounded-lg transition-colors">
                        <i class="fas fa-trash mr-2"></i>
                        კლიენტის წაშლა
                    </button>
                    
                </div>
            </div>
        </div>


    </div>

    <!-- Include jQuery for AJAX functionality -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Add loading state on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Hide any loading states
            document.body.style.visibility = 'visible';
            
            // Debug mobile menu elements
            setTimeout(function() {
                const mobileMenuToggle = document.getElementById('mobileMenuToggle');
                const sidebar = document.getElementById('sidebar');
                const mobileOverlay = document.getElementById('mobileOverlay');
                
                console.log('Debug mobile menu elements:', {
                    toggle: mobileMenuToggle ? 'found' : 'not found',
                    sidebar: sidebar ? 'found' : 'not found',
                    overlay: mobileOverlay ? 'found' : 'not found'
                });
                
                if (sidebar) {
                    console.log('Sidebar initial classes:', sidebar.className);
                    console.log('Sidebar computed style transform:', getComputedStyle(sidebar).transform);
                }
            }, 1000);
        });
        
        // Add error handling for images
        document.addEventListener('DOMContentLoaded', function() {
            const images = document.querySelectorAll('img');
            images.forEach(img => {
                img.addEventListener('error', function() {
                    console.log('Image failed to load:', this.src);
                });
            });
        });
        let isEditing = false;
        const originalValues = {};

        // Toggle edit mode
        function toggleEdit() {
            if (!isEditing) {
                // Store original values
                const fields = ['full_name', 'id_number', 'mobile_number', 'email', 'birth_date'];
                fields.forEach(field => {
                    const element = document.getElementById(field);
                    originalValues[field] = element.value;
                    element.classList.remove('field-display');
                    element.classList.add('field-edit');
                    element.removeAttribute('readonly');
                });

                // Toggle buttons
                document.getElementById('editButton').style.display = 'none';
                document.getElementById('saveButton').style.display = 'inline-block';
                document.getElementById('cancelButton').style.display = 'inline-block';
                
                isEditing = true;
            }
        }

        // Cancel edit mode
        function cancelEdit() {
            if (isEditing) {
                // Restore original values
                Object.keys(originalValues).forEach(field => {
                    const element = document.getElementById(field);
                    element.value = originalValues[field];
                    element.classList.remove('field-edit');
                    element.classList.add('field-display');
                    element.setAttribute('readonly', 'readonly');
                });

                // Toggle buttons
                document.getElementById('editButton').style.display = 'inline-block';
                document.getElementById('saveButton').style.display = 'none';
                document.getElementById('cancelButton').style.display = 'none';
                
                isEditing = false;
            }
        }

        // Save changes
        function saveChanges() {
            if (isEditing) {
                // Add loading state
                const saveBtn = document.getElementById('saveButton');
                const originalText = saveBtn.innerHTML;
                saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Saving...';
                saveBtn.disabled = true;

                // Submit the form
                document.getElementById('clientForm').submit();
            }
        }

        // Confirm delete with modern modal
        function confirmDelete() {
            if (confirm('⚠️ Are you sure you want to delete this client?\n\nThis action cannot be undone and will permanently remove all client data.')) {
                // Create a form to submit delete request
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = '<input type="hidden" name="delete" value="1">';
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Enhanced communication functions with better UX
        function sendQr(idNumber, mobile, email) {
            const button = event.target;
            const originalHTML = button.innerHTML;
            button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Sending...';
            button.disabled = true;

            $.ajax({
                url: '../send_qr.php',
                type: 'POST',
                data: { idNumber, mobile, email },
                dataType: 'json',
                success: function(response) {
                    showNotification(
                        response.status === 'success' ? '✅ ' + response.message : '❌ ' + response.message,
                        response.status === 'success' ? 'success' : 'error'
                    );
                },
                error: function(xhr, status, error) {
                    showNotification('❌ Communication error: ' + error, 'error');
                },
                complete: function() {
                    button.innerHTML = originalHTML;
                    button.disabled = false;
                }
            });
        }

        function sendWorkouts(mobile, email) {
            const button = event.target;
            const originalHTML = button.innerHTML;
            button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Sending...';
            button.disabled = true;

            $.ajax({
                url: '../send_workouts.php',
                type: 'POST',
                data: { mobile, email },
                dataType: 'json',
                success: function(response) {
                    showNotification(
                        response.status === 'success' ? '✅ ' + response.message : '❌ ' + response.message,
                        response.status === 'success' ? 'success' : 'error'
                    );
                },
                error: function(xhr, status, error) {
                    showNotification('❌ Communication error: ' + error, 'error');
                },
                complete: function() {
                    button.innerHTML = originalHTML;
                    button.disabled = false;
                }
            });
        }

        function sendProgram(idNumber, mobile) {
            const button = event.target;
            const originalHTML = button.innerHTML;
            button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Sending...';
            button.disabled = true;

            $.ajax({
                url: '../send_program.php',
                type: 'POST',
                data: { idNumber: idNumber, mobile: mobile },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        showNotification('✅ ' + response.message, 'success');
                    } else {
                        showNotification('❌ ' + (response.message || 'Unknown error'), 'error');
                    }
                },
                error: function(xhr, status, error) {
                    showNotification('❌ Communication error: ' + error, 'error');
                },
                complete: function() {
                    button.innerHTML = originalHTML;
                    button.disabled = false;
                }
            });
        }

        // Modern notification system
        function showNotification(message, type = 'info') {
            // Remove existing notifications
            const existing = document.querySelector('.notification');
            if (existing) existing.remove();

            // Create notification
            const notification = document.createElement('div');
            notification.className = `notification fixed top-4 right-4 z-50 max-w-sm p-4 rounded-lg shadow-lg transition-all duration-300 transform translate-x-full`;
            
            if (type === 'success') {
                notification.classList.add('bg-green-100', 'border', 'border-green-400', 'text-green-700');
            } else if (type === 'error') {
                notification.classList.add('bg-red-100', 'border', 'border-red-400', 'text-red-700');
            } else {
                notification.classList.add('bg-blue-100', 'border', 'border-blue-400', 'text-blue-700');
            }

            notification.innerHTML = `
                <div class="flex items-center justify-between">
                    <span class="font-medium">${message}</span>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-2 text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;

            document.body.appendChild(notification);

            // Animate in
            setTimeout(() => {
                notification.classList.remove('translate-x-full');
            }, 100);

            // Auto remove - 10 seconds for errors, 5 seconds for others
            const autoRemoveDelay = type === 'error' ? 10000 : 5000;
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.classList.add('translate-x-full');
                    setTimeout(() => notification.remove(), 300);
                }
            }, autoRemoveDelay);
        }

        // Payment processing function
        function markAsProcessed(paymentId) {
            if (!confirm('Are you sure you want to mark this payment as processed?')) {
                return;
            }

            const button = event.target.closest('button');
            const originalHTML = button.innerHTML;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            button.disabled = true;

            fetch('mark_payment_processed.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    payment_id: paymentId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('✅ Payment processed successfully!', 'success');
                    // Reload the page to show updated status
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    showNotification('❌ Error: ' + (data.message || 'Unknown error'), 'error');
                    button.innerHTML = originalHTML;
                    button.disabled = false;
                }
            })
            .catch(error => {
                showNotification('❌ Network error: ' + error, 'error');
                button.innerHTML = originalHTML;
                button.disabled = false;
            });
        }
    </script>
    
    <!-- Fallback mobile menu script for client.php -->
    <script>
        // Additional mobile menu handler as backup
        document.addEventListener('DOMContentLoaded', function() {
            // Wait a bit to ensure adminNavbar script has loaded
            setTimeout(function() {
                const mobileMenuToggle = document.getElementById('mobileMenuToggle');
                const sidebar = document.getElementById('sidebar');
                const mobileOverlay = document.getElementById('mobileOverlay');
                
                // Add additional click handler if the first one doesn't work
                if (mobileMenuToggle && sidebar && mobileOverlay) {
                    mobileMenuToggle.addEventListener('click', function(e) {
                        console.log('Backup mobile menu handler triggered');
                        
                        // Force toggle the sidebar visibility
                        const isHidden = sidebar.classList.contains('-translate-x-full') || 
                                        getComputedStyle(sidebar).transform === 'matrix(1, 0, 0, 1, -256, 0)';
                        
                        if (isHidden) {
                            sidebar.classList.remove('-translate-x-full');
                            sidebar.style.transform = 'translateX(0)';
                            mobileOverlay.classList.remove('hidden');
                            console.log('Showing sidebar');
                        } else {
                            sidebar.classList.add('-translate-x-full');
                            sidebar.style.transform = 'translateX(-100%)';
                            mobileOverlay.classList.add('hidden');
                            console.log('Hiding sidebar');
                        }
                    });
                }
            }, 500);
        });
    </script>

    <!-- Profile Image Modal -->
    <div id="profileImageModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 1000; cursor: pointer;" onclick="this.style.display='none'">
        <div style="display: flex; justify-content: center; align-items: center; height: 100%; padding: 20px;">
            <?php if (!empty($client['picurl']) && file_exists('../' . $client['picurl'])): ?>
                <img src="../<?php echo htmlspecialchars($client['picurl']); ?>" 
                     alt="<?php echo htmlspecialchars($client['full_name']); ?>" 
                     style="max-width: 90%; max-height: 90%; border-radius: 10px; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
            <?php endif; ?>
        </div>
    </div>
</div> <!-- Close main content wrapper -->
</body>
</html>
