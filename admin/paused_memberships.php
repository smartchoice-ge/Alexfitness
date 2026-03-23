<?php
session_start([
    'cookie_httponly' => true,
    'cookie_samesite' => 'Strict'
]);

// Redirect to login if user is not logged in
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header('Location: ../login.php');
    exit;
}

// Handle logout
if (isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header("Location: ../login.php");
    exit;
}

include_once '../mssql_connection.php';
include_once '../params.php';
 

// Handle resume paused package
if (isset($_POST['resume_package']) && isset($_POST['sold_package_id'])) {
    $soldPackageID = $_POST['sold_package_id'];
    $clientID = $_POST['client_id'];
    
    try {
        sqlsrv_begin_transaction($mssqlconn);
        
        // Load current status and pause start date to decide how to resume
        $sqlFetch = "SELECT sp.Expired, cc.PauseStartDate
                     FROM SoldPackages sp
                     LEFT JOIN ClientCards cc ON cc.ClientID = sp.ClientID
                     WHERE sp.ID = ?";
        $stmtFetch = sqlsrv_query($mssqlconn, $sqlFetch, [$soldPackageID]);
        if (!$stmtFetch) {
            throw new Exception('Failed to fetch package state');
        }
        $row = sqlsrv_fetch_array($stmtFetch, SQLSRV_FETCH_ASSOC);
        if (!$row) {
            throw new Exception('Package not found');
        }

        $expired = (int)($row['Expired'] ?? 0);
        $pauseDays = 0;
        if (!empty($row['PauseStartDate']) && $row['PauseStartDate'] instanceof DateTime) {
            $pauseDays = (new DateTime('now'))->diff($row['PauseStartDate'])->days;
        }

        if ($expired === 1) {
            // Unpause, mark as active (not expired), and extend end date by paused days
            $sqlResumeExpired = "UPDATE SoldPackages
                                 SET PauseStatus = 0,
                                     Expired = 0,
                                     EndDate = DATEADD(day, ?, ISNULL(EndDate, GETDATE()))
                                 WHERE ID = ?";
            $stmtResume = sqlsrv_query($mssqlconn, $sqlResumeExpired, [ (int)$pauseDays, $soldPackageID ]);
            if (!$stmtResume) {
                throw new Exception('Failed to resume expired package');
            }
        } else {
            // Just unpause for active packages
            $sqlResumePackage = "UPDATE SoldPackages SET PauseStatus = 0 WHERE ID = ?";
            $stmtResume = sqlsrv_query($mssqlconn, $sqlResumePackage, [$soldPackageID]);
            if (!$stmtResume) {
                throw new Exception('Failed to resume active package');
            }
        }

        // Reactivate client card and device access
        $sqlActivateCards = "UPDATE ClientCards 
                             SET IsActive = 1, IsPaused = 0, PauseStartDate = NULL, ActiveIndevice = 1 
                             WHERE ClientID = ?";
        $stmtCard = sqlsrv_query($mssqlconn, $sqlActivateCards, [$clientID]);
        if (!$stmtCard) {
            throw new Exception('Failed to activate client card');
        }

        sqlsrv_commit($mssqlconn);
        $success = ($expired === 1)
            ? 'Package resumed, unexpired, end date updated, and card activated.'
            : 'Package resumed successfully!';
    } catch (Exception $e) {
        sqlsrv_rollback($mssqlconn);
        $error = "Failed to resume package: " . $e->getMessage();
    }
}

// Get paused memberships from SoldPackages table
$pausedMemberships = [];

// Debug: Count all paused packages
$countSql = "SELECT COUNT(*) as total_paused FROM SoldPackages WHERE PauseStatus = 1";
$countStmt = sqlsrv_query($mssqlconn, $countSql);
$totalPaused = 0;
if ($countStmt && sqlsrv_fetch($countStmt)) {
    $totalPaused = sqlsrv_get_field($countStmt, 0);
}

// Debug: Get all paused packages with basic info
$debugAllSql = "SELECT sp.ID, sp.ClientID, sp.PauseStatus, c.FullName, p.Name as PackageName
                FROM SoldPackages sp
                LEFT JOIN Clients c ON sp.ClientID = c.ID
                LEFT JOIN Packages p ON sp.PackageID = p.ID
                WHERE sp.PauseStatus = 1";
$debugAllStmt = sqlsrv_query($mssqlconn, $debugAllSql);
$allPausedInfo = [];
if ($debugAllStmt) {
    while ($row = sqlsrv_fetch_array($debugAllStmt, SQLSRV_FETCH_ASSOC)) {
        $allPausedInfo[] = $row;
    }
}

// Main query for paused memberships - Using LEFT JOINs to be more inclusive
$sqlPaused = "SELECT 
                sp.ID as SoldPackageID,
                sp.ClientID,
                sp.PauseStatus,
                sp.StartDate,
                sp.EndDate,
                ISNULL(c.FullName, 'Unknown Client') as FullName,
                ISNULL(c.Phone, 'No Phone') as Phone,
                ISNULL(p.Name, 'Unknown Package') as PackageName,
                ISNULL(p.Price, 0) as PackagePrice,
                ISNULL(cc.IsActive, 0) as CardActive,
                ISNULL(cc.IsPaused, 0) as CardPaused,
                cc.PauseStartDate,
                CASE 
                    WHEN cc.PauseStartDate IS NOT NULL 
                    THEN DATEDIFF(day, cc.PauseStartDate, GETDATE()) 
                    ELSE 0 
                END as DaysPaused
              FROM SoldPackages sp
              LEFT JOIN Clients c ON sp.ClientID = c.ID
              LEFT JOIN Packages p ON sp.PackageID = p.ID
              LEFT JOIN ClientCards cc ON c.ID = cc.ClientID
              WHERE sp.PauseStatus = 1
              ORDER BY sp.StartDate DESC";

$stmtPaused = sqlsrv_query($mssqlconn, $sqlPaused);
if ($stmtPaused) {
    while ($row = sqlsrv_fetch_array($stmtPaused, SQLSRV_FETCH_ASSOC)) {
        $pausedMemberships[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>დაპაუზებული პაკეტები - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.0.1/css/buttons.dataTables.min.css">
    <!-- Toast Notification System -->
    <link rel="stylesheet" href="toast-notifications.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        /* Custom styles for better table appearance */
        .status-badge {
            @apply px-3 py-1 rounded-full text-sm font-medium;
        }
        .status-active {
            @apply bg-green-100 text-green-800;
        }
        .status-paused {
            @apply bg-yellow-100 text-yellow-800;
        }
        .status-inactive {
            @apply bg-red-100 text-red-800;
        }
        .days-badge {
            @apply px-2 py-1 rounded text-sm font-medium bg-orange-100 text-orange-800;
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Load Navigation -->
    <?php include '../components/adminNavbar.php'; ?>
    
    <!-- Main Content -->
    <div class="p-6">
        <!-- Page Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 flex items-center">
                        <i class="fas fa-pause-circle text-yellow-500 mr-3"></i>
                        დაპაუზებული პაკეტები
                    </h1>
                    <p class="text-gray-600 mt-1">მომხმარებლების დაპაუზებული საწევრო პაკეტების მართვა</p>
                </div>
                <div class="text-right">
                    <div class="text-3xl font-bold text-yellow-600"><?php echo $totalPaused; ?></div>
                    <div class="text-sm text-gray-500">დაპაუზებული პაკეტი</div>
                </div>
            </div>
        </div>

        <!-- Status Messages -->
        <?php if (isset($success)): ?>
        <div id="successToast" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                <?php echo htmlspecialchars($success); ?>
            </div>
        </div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
        <div id="errorToast" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($totalPaused == 0): ?>
        <!-- Empty State -->
        <div class="bg-white rounded-lg shadow-sm p-12 text-center">
            <div class="w-24 h-24 mx-auto mb-4 text-gray-300">
                <i class="fas fa-check-circle text-6xl"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-700 mb-2">ყველა პაკეტი აქტიურია!</h3>
            <p class="text-gray-500">ამ მომენტში დაპაუზებული პაკეტები არ არის.</p>
        </div>
        <?php else: ?>
        <!-- Paused Memberships Table -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <i class="fas fa-user mr-2"></i>წევრი
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <i class="fas fa-box mr-2"></i>პაკეტი
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <i class="fas fa-calendar mr-2"></i>დაპაუზების თარიღი
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <i class="fas fa-clock mr-2"></i>დაპაუზებული დღეები
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <i class="fas fa-credit-card mr-2"></i>ბარათის სტატუსი
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <i class="fas fa-comment mr-2"></i>მიზეზი
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <i class="fas fa-cogs mr-2"></i>მოქმედებები
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php 
                        // Show the detailed memberships if available, otherwise show the basic paused info
                        $displayData = !empty($pausedMemberships) ? $pausedMemberships : $allPausedInfo;
                        foreach ($displayData as $pause): 
                        ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                        <i class="fas fa-user text-blue-600"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">
                                            <?php echo htmlspecialchars($pause['FullName'] ?? 'Unknown Client'); ?>
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            <i class="fas fa-id-badge mr-1"></i>
                                            ID: <?php echo $pause['ClientID'] ?? 'N/A'; ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">
                                        <?php echo htmlspecialchars($pause['PackageName'] ?? 'Unknown Package'); ?>
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        <?php if (isset($pause['EndDate']) && $pause['EndDate']): ?>
                                        <i class="fas fa-calendar-times mr-1"></i>
                                        ბოლო: <?php echo $pause['EndDate']->format('Y-m-d'); ?>
                                        <?php else: ?>
                                        <i class="fas fa-box mr-1"></i>
                                        SoldPackage ID: <?php echo $pause['SoldPackageID'] ?? $pause['ID']; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <div class="flex items-center">
                                    <i class="fas fa-pause text-yellow-500 mr-2"></i>
                                    <?php 
                                    if (isset($pause['StartDate']) && $pause['StartDate']) {
                                        echo $pause['StartDate']->format('Y-m-d');
                                    } else {
                                        echo 'დაპაუზებული';
                                    }
                                    ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="days-badge">
                                    <?php echo $pause['DaysPaused'] ?? '0'; ?> დღე
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php if (isset($pause['CardActive']) && $pause['CardActive'] && !($pause['CardPaused'] ?? false)): ?>
                                <span class="status-badge status-active">
                                    <i class="fas fa-check mr-1"></i>აქტიური
                                </span>
                                <?php else: ?>
                                <span class="status-badge status-inactive">
                                    <i class="fas fa-times mr-1"></i>არააქტიური
                                </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">
                                    <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded text-xs font-medium">
                                        დაპაუზებული
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <form method="POST" class="inline" 
                                      onsubmit="return confirm('დარწმუნებული ხართ, რომ გსურთ ამ საწევროს აღდგენა?')">
                                    <input type="hidden" name="sold_package_id" value="<?php echo $pause['SoldPackageID'] ?? $pause['ID']; ?>">
                                    <input type="hidden" name="client_id" value="<?php echo $pause['ClientID']; ?>">
                                    <button type="submit" name="resume_package" 
                                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors flex items-center">
                                        <i class="fas fa-play mr-2"></i>აღდგენა
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Auto-hide toast messages -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-hide success and error messages after 5 seconds
            const successToast = document.getElementById('successToast');
            const errorToast = document.getElementById('errorToast');
            
            if (successToast) {
                setTimeout(() => {
                    successToast.style.transition = 'opacity 0.5s ease';
                    successToast.style.opacity = '0';
                    setTimeout(() => successToast.remove(), 500);
                }, 5000);
            }
            
            if (errorToast) {
                setTimeout(() => {
                    errorToast.style.transition = 'opacity 0.5s ease';
                    errorToast.style.opacity = '0';
                    setTimeout(() => errorToast.remove(), 500);
                }, 5000);
            }
        });
    </script>
</body>
</html>
