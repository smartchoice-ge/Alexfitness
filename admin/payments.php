<?php
session_start([
    'cookie_httponly' => true,
    'cookie_samesite' => 'Strict'
]);

// Set timezone to Tbilisi for proper date display
date_default_timezone_set('Asia/Tbilisi');

if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header('Location: ../login.php');
    exit;
}

include('../db_connection.php');
include('../mssql_connection.php');
include('../mssql_packages_payments_helper.php');

// Pagination settings
$limit = 30; // Number of payments per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max($page, 1);
$offset = ($page - 1) * $limit;

// Search filter
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Status filter
$statusFilter = isset($_GET['status']) ? trim($_GET['status']) : '';

// Use MSSQL helper to get payments
$paymentsResult = getPaymentsWebsite([
    'limit' => $limit,
    'offset' => $offset,
    'status' => 'Success',
    'search' => $search,
    'orderBy' => 'time DESC'
]);

$payments = $paymentsResult['payments'];
$totalPayments = $paymentsResult['total'];
$totalPages = ceil($totalPayments / $limit);

// Note: client_details (full_name, profile_image) will need separate lookup from MySQL if needed
// For now, we can add this info later or do a separate query
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payments</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <!-- Toast Notification System -->
    <link rel="stylesheet" href="toast-notifications.css">
    <style>
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
<body class="bg-gray-100">
    <?php include '../components/adminNavbar.php'; ?>

    <div class="container mx-auto p-4">
        <h1 class="text-xl font-bold mb-4">Payments</h1>

        <!-- Immediate Processing Notice -->
        <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg flex items-center">
            <div class="flex items-center">
                <div class="w-3 h-3 bg-blue-500 rounded-full mr-3"></div>
                <span class="text-blue-800 font-medium">Instant Processing Active</span>
                <span class="text-blue-600 text-sm ml-2">New payments are processed automatically when payment is confirmed</span>
            </div>
        </div>

        <!-- Search and Filter Form -->
        <form method="GET" class="mb-4 flex gap-4 flex-wrap">
            <input type="text" name="search" placeholder="Search by phone or transaction ID" value="<?= htmlspecialchars($search) ?>"
                   class="p-2 border rounded-md w-64">
            <select name="status" class="p-2 border rounded-md">
                <option value="">All Statuses</option>
                <option value="Pending" <?= $statusFilter === 'Pending' ? 'selected' : '' ?>>Pending</option>
                <option value="Success" <?= $statusFilter === 'Success' ? 'selected' : '' ?>>Success</option>
                <option value="Fail" <?= $statusFilter === 'Fail' ? 'selected' : '' ?>>Fail</option>
            </select>
            <button type="submit" class="p-2 bg-blue-500 text-white rounded-md">Search</button>
            <a href="payments.php" class="p-2 bg-gray-500 text-white rounded-md">Clear</a>
        </form>

        <!-- Summary Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <?php
            // Get summary stats from MSSQL PaymentsWebsite
            $statsQuery = "SELECT 
                COUNT(*) as total_count,
                SUM(CASE WHEN status = 'Success' THEN 1 ELSE 0 END) as success_count,
                SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) as pending_count,
                SUM(CASE WHEN status = 'Fail' THEN 1 ELSE 0 END) as fail_count,
                SUM(CASE WHEN status = 'Success' THEN amount ELSE 0 END) as total_revenue
                FROM PaymentsWebsite";
            $statsStmt = sqlsrv_query($mssqlconn, $statsQuery);
            $stats = sqlsrv_fetch_array($statsStmt, SQLSRV_FETCH_ASSOC);
            sqlsrv_free_stmt($statsStmt);
            ?>
            <div class="bg-white p-4 rounded-lg shadow">
                <h3 class="text-sm font-medium text-gray-500">Total Payments</h3>
                <p class="text-2xl font-bold"><?= number_format($stats['total_count']) ?></p>
            </div>
            <div class="bg-green-50 p-4 rounded-lg shadow">
                <h3 class="text-sm font-medium text-green-600">Successful</h3>
                <p class="text-2xl font-bold text-green-800"><?= number_format($stats['success_count']) ?></p>
            </div>
            <div class="bg-yellow-50 p-4 rounded-lg shadow">
                <h3 class="text-sm font-medium text-yellow-600">Pending</h3>
                <p class="text-2xl font-bold text-yellow-800"><?= number_format($stats['pending_count']) ?></p>
            </div>
            <div class="bg-blue-50 p-4 rounded-lg shadow">
                <h3 class="text-sm font-medium text-blue-600">Total Revenue</h3>
                <p class="text-2xl font-bold text-blue-800"><?= number_format($stats['total_revenue'], 2) ?> GEL</p>
            </div>
        </div>

        <!-- Payments Table -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="p-3 text-left">Profile</th>
                        <th class="p-3 text-left">Name</th>
                        <th class="p-3 text-left">ID</th>
                        <th class="p-3 text-left">Amount</th>
                        <th class="p-3 text-left">Status</th>
                        <th class="p-3 text-left">Phone Number</th>
                        <th class="p-3 text-left">Transaction ID</th>
                        <th class="p-3 text-left">Package Name</th>
                        <th class="p-3 text-left">User ID</th>
                        <th class="p-3 text-left">Processed</th>
                        <th class="p-3 text-left">Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($payments)): ?>
                        <?php foreach ($payments as $payment): ?>
                            <tr class="border-t">
                                <td class="p-3">
                                    <?php if (!empty($payment['profile_image']) && file_exists('../' . $payment['profile_image'])): ?>
                                        <img src="../<?= htmlspecialchars($payment['profile_image']) ?>" 
                                             alt="Profile" 
                                             class="w-10 h-10 rounded-full object-cover border-2 border-gray-200"
                                             onerror="this.src='../img/default-profile.png'">
                                    <?php else: ?>
                                        <div class="w-10 h-10 rounded-full bg-gray-300 flex items-center justify-center text-gray-600 text-sm font-medium border-2 border-gray-200">
                                            <?php if (!empty($payment['full_name'])): ?>
                                                <?= strtoupper(substr($payment['full_name'], 0, 1)) ?>
                                            <?php else: ?>
                                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                                </svg>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="p-3">
                                    <?php if (!empty($payment['full_name']) && !empty($payment['user_id'])): ?>
                                        <a href="client.php?user_id=<?= htmlspecialchars($payment['user_id']) ?>" 
                                           class="font-medium text-blue-600 hover:text-blue-800 hover:underline transition-colors duration-200">
                                            <?= htmlspecialchars($payment['full_name']) ?>
                                        </a>
                                    <?php elseif (!empty($payment['full_name'])): ?>
                                        <span class="font-medium text-gray-900"><?= htmlspecialchars($payment['full_name']) ?></span>
                                    <?php else: ?>
                                        <span class="text-gray-400 italic">No name</span>
                                    <?php endif; ?>
                                </td>
                                <td class="p-3"><?= htmlspecialchars($payment['id']) ?></td>
                                <td class="p-3 font-medium"><?= number_format($payment['amount'], 2) ?> GEL</td>
                                <td class="p-3">
                                    <?php
                                    $statusClass = '';
                                    switch($payment['status']) {
                                        case 'Success':
                                            $statusClass = 'bg-green-100 text-green-800';
                                            break;
                                        case 'Pending':
                                            $statusClass = 'bg-yellow-100 text-yellow-800';
                                            break;
                                        case 'Fail':
                                            $statusClass = 'bg-red-100 text-red-800';
                                            break;
                                        default:
                                            $statusClass = 'bg-gray-100 text-gray-800';
                                    }
                                    ?>
                                    <span class="px-2 py-1 rounded-full text-xs font-medium <?= $statusClass ?>">
                                        <?= htmlspecialchars($payment['status']) ?>
                                    </span>
                                </td>
                                <td class="p-3"><?= htmlspecialchars($payment['client_mobile_number']) ?></td>
                                <td class="p-3 font-mono text-sm"><?= htmlspecialchars($payment['transaction_id']) ?></td>
                                <td class="p-3"><?= htmlspecialchars($payment['package_name'] ?: 'N/A') ?></td>
                                <td class="p-3">
                                    <?php if (!empty($payment['user_id'])): ?>
                                        <a href="client.php?user_id=<?= htmlspecialchars($payment['user_id']) ?>" 
                                           target="_blank" 
                                           class="text-blue-600 hover:text-blue-800 hover:underline font-medium">
                                            <?= htmlspecialchars($payment['user_id']) ?>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-gray-400">N/A</span>
                                    <?php endif; ?>
                                </td>
                                <td class="p-3">
                                    <?php if ($payment['processed'] == 0 && $payment['status'] == 'Success'): ?>
                                        <button class="bg-green-500 hover:bg-green-600 text-white rounded-full p-2 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-green-300" 
                                                onclick="markAsProcessed(<?= $payment['id'] ?>)" 
                                                title="Mark as processed">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </button>
                                    <?php elseif ($payment['processed'] == 1): ?>
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Done
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.864-.833-2.634 0L4.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                            </svg>
                                            Cannot Process
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="p-3"><?= htmlspecialchars($payment['time']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="11" class="p-3 text-center">No payments found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            <?php if ($totalPages > 1): ?>
                <nav class="flex justify-center">
                    <ul class="inline-flex items-center -space-x-px text-sm">

                        <li>
                            <?php if ($page > 1): ?>
                                <a href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($statusFilter) ?>" class="flex items-center justify-center px-3 h-8 ms-0 leading-tight text-gray-500 bg-white border border-e-0 border-gray-300 rounded-s-lg hover:bg-gray-100 hover:text-gray-700">Previous</a>
                            <?php else: ?>
                                <span class="flex items-center justify-center px-3 h-8 ms-0 leading-tight text-gray-400 bg-white border border-e-0 border-gray-300 rounded-s-lg cursor-not-allowed">Previous</span>
                            <?php endif; ?>
                        </li>

                        <?php
                        $range = 2; // How many page links to show around the current page
                        $showEllipsisStart = false;
                        $showEllipsisEnd = false;

                        // Define the range of page numbers to display
                        $start = max(1, $page - $range);
                        $end = min($totalPages, $page + $range);

                        // Decide if ellipses are needed
                        if ($start > 2) { // Need ellipsis after page 1?
                            $showEllipsisStart = true;
                        }
                        if ($end < $totalPages - 1) { // Need ellipsis before last page?
                            $showEllipsisEnd = true;
                        }

                        // Always show page 1 unless it's the only page
                        if ($totalPages > 1) {
                             $activeClass = ($page == 1) ? 'text-blue-600 border-blue-300 bg-blue-50 hover:bg-blue-100 hover:text-blue-700' : 'text-gray-500 bg-white border-gray-300 hover:bg-gray-100 hover:text-gray-700';
                             echo '<li><a href="?page=1&search=' . urlencode($search) . '&status=' . urlencode($statusFilter) . '" class="flex items-center justify-center px-3 h-8 leading-tight border ' . $activeClass . '">1</a></li>';
                        }

                        // Show starting ellipsis if needed
                        if ($showEllipsisStart) {
                            echo '<li><span class="flex items-center justify-center px-3 h-8 leading-tight text-gray-500 bg-white border border-gray-300">...</span></li>';
                        }

                        // Show page numbers within the calculated range, avoiding duplicates of 1 and last page if ellipsis are shown
                        $loopStart = $showEllipsisStart ? max(2, $start) : max(2, $start);
                        $loopEnd = $showEllipsisEnd ? min($totalPages - 1, $end) : min($totalPages - 1, $end);

                        for ($i = $loopStart; $i <= $loopEnd; $i++) {
                            $activeClass = ($page == $i) ? 'text-blue-600 border-blue-300 bg-blue-50 hover:bg-blue-100 hover:text-blue-700' : 'text-gray-500 bg-white border-gray-300 hover:bg-gray-100 hover:text-gray-700';
                            echo '<li><a href="?page=' . $i . '&search=' . urlencode($search) . '&status=' . urlencode($statusFilter) . '" class="flex items-center justify-center px-3 h-8 leading-tight border ' . $activeClass . '">' . $i . '</a></li>';
                        }

                        // Show ending ellipsis if needed
                        if ($showEllipsisEnd) {
                             echo '<li><span class="flex items-center justify-center px-3 h-8 leading-tight text-gray-500 bg-white border border-gray-300">...</span></li>';
                        }

                        // Always show the last page, unless it's page 1 or already shown in the loop
                        if ($totalPages > 1 && $end < $totalPages) {
                             $activeClass = ($page == $totalPages) ? 'text-blue-600 border-blue-300 bg-blue-50 hover:bg-blue-100 hover:text-blue-700' : 'text-gray-500 bg-white border-gray-300 hover:bg-gray-100 hover:text-gray-700';
                            echo '<li><a href="?page=' . $totalPages . '&search=' . urlencode($search) . '&status=' . urlencode($statusFilter) . '" class="flex items-center justify-center px-3 h-8 leading-tight border ' . $activeClass . '">' . $totalPages . '</a></li>';
                        }
                        ?>

                        <li>
                            <?php if ($page < $totalPages): ?>
                                <a href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($statusFilter) ?>" class="flex items-center justify-center px-3 h-8 leading-tight text-gray-500 bg-white border border-gray-300 rounded-e-lg hover:bg-gray-100 hover:text-gray-700">Next</a>
                            <?php else: ?>
                                 <span class="flex items-center justify-center px-3 h-8 leading-tight text-gray-400 bg-white border border-gray-300 rounded-e-lg cursor-not-allowed">Next</span>
                            <?php endif; ?>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Removed auto-processing system - payments are now processed immediately in unipay_callback.php
        
        // Manual processing function (kept for manual intervention if needed)
        function markAsProcessed(paymentId, isAutoProcess = false, buttonElement = null) {
            // Find the button element if not provided
            let button = buttonElement;
            if (!button) {
                button = event.target.closest('button');
                if (!button) {
                    showMessage('Button element not found', 'error');
                    return;
                }
            }
            
            // Confirm action with user (only for manual processing)
            if (!isAutoProcess && !confirm('Are you sure you want to mark this payment as processed?')) {
                return;
            }
            
            const originalHTML = button.innerHTML;
            button.disabled = true;
            button.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>';
            
            // Make AJAX call to backend
            fetch('mark_payment_processed.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    payment_id: paymentId
                })
            })
            .then(response => {
                // Log the raw response for debugging
                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);
                
                // Get the response text first
                return response.text().then(text => {
                    console.log('Raw response text:', text);
                    
                    // Try to parse as JSON
                    try {
                        const data = JSON.parse(text);
                        return { data, status: response.status };
                    } catch (e) {
                        console.error('JSON parse error:', e);
                        console.error('Response text that failed to parse:', text);
                        throw new Error('Invalid JSON response: ' + text.substring(0, 100) + '...');
                    }
                });
            })
            .then(({ data, status }) => {
                console.log('Parsed data:', data);
                
                if (data.success) {
                    // Success - replace button with "Done" badge
                    button.outerHTML = `
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Done
                        </span>
                    `;
                    
                    // Show success message
                    let messageText = isAutoProcess ? 
                        data.message + ' (Auto-processed after 2 minutes)' : 
                        data.message;
                    
                    // Add QR code status if available
                    if (data.qr_sent !== undefined) {
                        if (data.qr_sent) {
                            messageText += ' 📱 QR code sent successfully!';
                        } else {
                            messageText += ' ⚠️ QR code sending failed - please send manually.';
                        }
                    }
                    
                    showMessage(messageText, 'success');
                } else {
                    // Error - restore button and show error
                    button.disabled = false;
                    button.innerHTML = originalHTML;
                    showMessage(data.message + (data.error_details ? ' (' + data.error_details + ')' : ''), 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                button.disabled = false;
                button.innerHTML = originalHTML;
                showMessage('An error occurred while processing the request', 'error');
            });
        }
        
        // Function to show messages to user
        function showMessage(message, type) {
            // Remove any existing message
            const existingMessage = document.getElementById('statusMessage');
            if (existingMessage) {
                existingMessage.remove();
            }
            
            // Create new message element
            const messageDiv = document.createElement('div');
            messageDiv.id = 'statusMessage';
            messageDiv.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-md ${
                type === 'success' 
                    ? 'bg-green-100 border border-green-400 text-green-700' 
                    : 'bg-red-100 border border-red-400 text-red-700'
            }`;
            messageDiv.innerHTML = `
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        ${type === 'success' 
                            ? '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>'
                            : '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>'
                        }
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium">${message}</p>
                    </div>
                    <div class="ml-auto pl-3">
                        <button onclick="this.parentElement.parentElement.parentElement.remove()" class="inline-flex rounded-md p-1.5 hover:bg-opacity-75 focus:outline-none">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            `;
            
            // Add to page
            document.body.appendChild(messageDiv);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                if (messageDiv && messageDiv.parentNode) {
                    messageDiv.remove();
                }
            }, 5000);
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
    </script>

    <!-- Toast Notification System -->
    <script src="toast-notifications.js"></script>
    <script>
        // Initialize the payment notification system
        document.addEventListener('DOMContentLoaded', function() {
            window.paymentNotificationSystem = new PaymentNotificationSystem();
            window.paymentNotificationSystem.init();
            console.log('✅ Payment notification system initialized');
            
            // Auto-processing is now handled immediately in unipay_callback.php
            console.log('📱 Payments are processed immediately when confirmed');
        });
    </script>

    </div> <!-- Close mainContent -->
</body>
</html>
