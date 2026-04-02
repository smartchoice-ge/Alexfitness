<?php
session_start();
include '../mssql_connection.php'; // Include database connection
include '../db_connection.php'; // Include MySQL database connection
include '../mssql_packages_payments_helper.php'; // Include helper functions

// Debug output at the top of the page
echo "<!-- DEBUG: Session username: " . (isset($_SESSION['username']) ? $_SESSION['username'] : 'NOT SET') . " -->";
echo "<!-- DEBUG: Session loggedin: " . (isset($_SESSION['loggedin']) ? ($_SESSION['loggedin'] ? 'TRUE' : 'FALSE') : 'NOT SET') . " -->";

// Check if the user is logged in, if not then redirect to login page
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: auth.php');
    exit;
}

// Logout logic
if (isset($_GET['logout']) && $_GET['logout'] == 'true') {
    // Unset all of the session variables
    $_SESSION = array();

    // Destroy the session.
    session_destroy();

    // Redirect to login page
    header('Location: auth.php');
    exit;
}

$userFullName = 'Guest'; // Default value
$membershipEndDate = 'N/A'; // Default for end date
$membershipType = 'No Membership'; // Default membership type
$showQrCode = true; // Default to show QR code
$renewButtonTextKey = 'key_renew_button'; // Default button text
$membershipStatusKey = 'key_active_member'; // Default membership status
$activeUsersCount = 0; // Default value for active users
$canPause = false; // Default pause eligibility
$pauseStatus = 0; // Default pause status (0 = not paused, 1 = paused)
$remainingPauseDays = 0; // Default remaining pause days
$currentSoldPackageID = null; // Track current sold package ID

// Fetch active users count
$sqlActiveUsers = "SELECT COUNT(*) AS ActiveUsers FROM Visits WHERE InDateTame >= DATEADD(hour, -1, GETDATE())";
$stmtActiveUsers = sqlsrv_query($mssqlconn, $sqlActiveUsers);

if ($stmtActiveUsers === false) {
    error_log("Error fetching active users count: " . print_r(sqlsrv_errors(), true));
} else {
    if (sqlsrv_fetch($stmtActiveUsers)) {
        $activeUsersCount = sqlsrv_get_field($stmtActiveUsers, 0);
    }
}

if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
    $qrCodePhone = str_starts_with($username, '995') ? substr($username, 3) : $username;
    
    // Fetch user_id from MSSQL ClientDetailsWebsite table based on mobile number
    $mysqlUserId = null;
    echo "<!-- DEBUG: Trying to find user_id for phone: " . htmlspecialchars($username) . " -->";
    
    // Use helper function to get client by mobile
    $clientDetails = getClientDetailsByMobile($username);
    if ($clientDetails) {
        $mysqlUserId = $clientDetails['id'];
        echo "<!-- DEBUG: SUCCESS! Found user_id: " . htmlspecialchars($mysqlUserId) . " -->";
    } else {
        echo "<!-- DEBUG: FAILED! No user_id found in ClientDetailsWebsite -->";
    }

    // Fetch FullName and ID from Clients table
    $sqlClient = "SELECT ID, FullName FROM Clients WHERE Phone = ?";
    $paramsClient = array($username);
    $stmtClient = sqlsrv_query($mssqlconn, $sqlClient, $paramsClient);

    if ($stmtClient === false) {
        error_log("Error fetching client ID and FullName: " . print_r(sqlsrv_errors(), true));
    } else {
        if (sqlsrv_fetch($stmtClient)) {
            $clientID = sqlsrv_get_field($stmtClient, 0);
            $userFullName = sqlsrv_get_field($stmtClient, 1);

            // Fetch package details from SoldPackages with Package Name and pause info
            // Order by EndDate DESC to get the latest package, then check if it's expired
            $sqlSoldPackages = "SELECT sp.ID, sp.EndDate, sp.Expired, p.Name AS PackageName, 
                                       sp.PauseStatus, sp.PauseStartDate, sp.PauseEndDate, sp.Remainingpause
                               FROM SoldPackages sp 
                               JOIN Packages p ON sp.PackageID = p.ID 
                               WHERE sp.ClientID = ? 
                               ORDER BY sp.EndDate DESC";
            $paramsSoldPackages = array($clientID);
            $stmtSoldPackages = sqlsrv_query($mssqlconn, $sqlSoldPackages, $paramsSoldPackages);

            if ($stmtSoldPackages === false) {
                error_log("Error fetching sold packages: " . print_r(sqlsrv_errors(), true));
            } else {
                if (sqlsrv_fetch($stmtSoldPackages)) {
                    $currentSoldPackageID = sqlsrv_get_field($stmtSoldPackages, 0);
                    $endDate = sqlsrv_get_field($stmtSoldPackages, 1);
                    $expiredStatus = sqlsrv_get_field($stmtSoldPackages, 2);
                    $membershipType = sqlsrv_get_field($stmtSoldPackages, 3); // Get package name
                    $pauseStatus = sqlsrv_get_field($stmtSoldPackages, 4) ?: 0;
                    $pauseStartDate = sqlsrv_get_field($stmtSoldPackages, 5);
                    $pauseEndDate = sqlsrv_get_field($stmtSoldPackages, 6);
                    $remainingPauseDays = sqlsrv_get_field($stmtSoldPackages, 7); // Get from database, keep NULL if NULL
                    
                    // Debug: Check what we actually got from database
                    echo "<!-- DEBUG: Raw remainingPauseDays: '" . var_export($remainingPauseDays, true) . "' -->";
                    echo "<!-- DEBUG: Type: " . gettype($remainingPauseDays) . " -->";
                    echo "<!-- DEBUG: Is null: " . ($remainingPauseDays === null ? 'YES' : 'NO') . " -->";

                    // Check if package is eligible for pausing (specific packages only)
                    // Get Package ID instead of package name for more reliable checking
                    $sqlGetPackageID = "SELECT sp.PackageID FROM SoldPackages sp WHERE sp.ClientID = ? AND sp.Expired != 1 ORDER BY sp.EndDate DESC";
                    $paramsPackageID = array($clientID);
                    $stmtPackageID = sqlsrv_query($mssqlconn, $sqlGetPackageID, $paramsPackageID);
                    
                    $currentPackageID = null;
                    if ($stmtPackageID !== false && sqlsrv_fetch($stmtPackageID)) {
                        $currentPackageID = sqlsrv_get_field($stmtPackageID, 0);
                    }
                    
                    // Define eligible package IDs for pause functionality with their maximum pause days
                    $packagePauseDays = [
                        // 60-day pause allowance packages
                        1237 => 60, 1317 => 60, 1321 => 60, 1328 => 60, 1329 => 60, 1330 => 60,
                        1335 => 60, 1337 => 60, 1346 => 60, 1349 => 60, 1350 => 60, 1357 => 60, 1358 => 60,
                        1370 => 60, 1376 => 60, 1379 => 60, 1380 => 60, 1381 => 60, 
                        1383 => 60, 1384 => 60, 1385 => 60, 1386 => 60, 1387 => 60, 
                        1388 => 60, 1396 => 60,
                        
                        // 30-day pause allowance packages
                        1205 => 30, 1280 => 30, 1283 => 30, 1303 => 30, 1332 => 30,
                        1333 => 30, 1334 => 30, 1342 => 30, 1354 => 30, 1359 => 30,
                        1360 => 30, 1365 => 30, 1371 => 30, 1372 => 30, 1409 => 30
                    ];
                    $eligiblePackageIDs = array_keys($packagePauseDays);
                    $canPause = in_array($currentPackageID, $eligiblePackageIDs);
                    
                    // Get max pause days for current package
                    $maxPauseDays = isset($packagePauseDays[$currentPackageID]) ? $packagePauseDays[$currentPackageID] : 60;

                    if ($expiredStatus != 1) {
                        $membershipEndDate = $endDate->format('F d, Y'); // Format the date
                        $membershipStatusKey = 'key_active_member';
                    } else {
                        $showQrCode = false;
                        $renewButtonTextKey = 'key_buy_subscription';
                        $membershipStatusKey = 'key_inactive_member';
                    }
                } else {
                    // No packages found for the client
                    $membershipType = 'No Membership'; // Default value
                    $showQrCode = false;
                    $renewButtonTextKey = 'key_buy_subscription';
                    $membershipStatusKey = 'key_inactive_member';
                }
            }
        } else {
            // Client not found in Clients table (should not happen if authenticated)
            $showQrCode = false;
            $renewButtonTextKey = 'key_buy_subscription';
            $membershipStatusKey = 'key_inactive_member';
        }
    }
}

// Initialize visit summary array
$visitSummary = array();

// Only process if we have a valid client ID
if (isset($clientID)) {
    // Get current month and previous two months
    $currentMonth = date('n'); // Current month as a number (1-12)
    $currentYear = date('Y');  // Current year

    // For each of the three months (current and 2 previous)
    for ($i = 0; $i < 3; $i++) {
        $monthNum = $currentMonth - $i;
        $year = $currentYear;
        
        // Handle wrapping around to previous year
        if ($monthNum <= 0) {
            $monthNum += 12;
            $year -= 1;
        }
        
        // SQL Server specific date formatting for BETWEEN clause
        $firstDayOfMonth = "$year-$monthNum-01";
        $lastDayOfMonth = date('Y-m-t', strtotime($firstDayOfMonth));
        
        // Query to count visits for this month using BETWEEN for date range
        $sqlVisits = "SELECT COUNT(*) AS VisitCount 
                     FROM Visits 
                     WHERE ClientID = ? 
                     AND CONVERT(date, InDateTame) BETWEEN ? AND ?";
                     
        $paramsVisits = array($clientID, $firstDayOfMonth, $lastDayOfMonth);
        $stmtVisits = sqlsrv_query($mssqlconn, $sqlVisits, $paramsVisits);
        
        $visitCount = 0; // Default count
        if ($stmtVisits !== false && sqlsrv_fetch($stmtVisits)) {
            $visitCount = sqlsrv_get_field($stmtVisits, 0);
        }
        
        // Month name in English
        $monthName = date('F', mktime(0, 0, 0, $monthNum, 1, $year));
        
        // Create translation key (lowercase month name)
        $monthKey = 'key_' . strtolower($monthName);
        
        // Store in array
        $visitSummary[] = array(
            'month' => $monthName,
            'count' => $visitCount,
            'key' => $monthKey
        );
    }
}
?>
<!DOCTYPE html>
<html lang="ka">
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="title" content="Member Profile - Luka Qaliashvili, ID: 0172409681">
    <meta name="description" content="View your Luka Qaliashvili, ID: 0172409681 member profile, check your membership status, and renew your plan.">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="shortcut icon" href="/img/favicon.ico" type="image/x-icon">
    <link rel="icon" href="/img/favicon.ico" type="image/x-icon">
    <title>Member Profile | Luka Qaliashvili, ID: 0172409681</title>

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
        .profile-card {
            background-color: #111;
            border: 1px solid #333;
            border-radius: 1rem;
        }
        .qr-code-container {
            background-color: white;
            padding: 1rem;
            border-radius: 0.75rem;
        }
        .renew-btn {
            background-color: #00b4d8;
            color: #000000;
            transition: background-color 0.3s ease;
            border-radius: 0.5rem;
        }
        .renew-btn:hover {
            background-color: #e6c605;
        }
        .lang-btn {
            background-color: #00b4d8;
            color: #000;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .lang-btn:hover {
            background-color: #e6c605;
        }
        /* Style for the new logout button */
        .logout-btn {
            color: #cbd5e1; /* Light gray */
            font-weight: 600;
            padding: 8px 16px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 1px solid #4a5568;
        }
        .logout-btn:hover {
            background-color: #ef4444; /* Red on hover */
            color: #fff;
            border-color: #ef4444;
        }
        .live-dot {
            height: 10px;
            width: 10px;
            background-color: #25d366;
            border-radius: 50%;
            display: inline-block;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(37, 211, 102, 0.7); }
            70% { box-shadow: 0 0 0 10px rgba(37, 211, 102, 0); }
            100% { box-shadow: 0 0 0 0 rgba(37, 211, 102, 0); }
        }
        .summary-row {
            background-color: #1f1f1f;
        }
        .contact-link {
            color: #00b4d8;
            transition: color 0.3s ease;
        }
        .contact-link:hover {
            color: #e6c605;
        }
        .support-section {
            background-color: #111;
            border: 1px solid #333;
            border-radius: 1rem;
        }
        
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
            
            /* Mobile social media buttons */
            .support-section a[href*="facebook"],
            .support-section a[href*="instagram"] {
                padding: 12px 20px !important;
                font-size: 0.9rem !important;
                min-height: 44px; /* iOS recommended touch target size */
                touch-action: manipulation;
                -webkit-tap-highlight-color: transparent;
            }
            
            .support-section .flex.justify-center.gap-4 {
                flex-direction: column;
                gap: 12px !important;
                align-items: center;
            }
            
            .support-section .flex.justify-center.gap-4 a {
                width: 100%;
                max-width: 280px;
                justify-content: center;
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
            
            /* Smaller mobile social buttons */
            .support-section a[href*="facebook"],
            .support-section a[href*="instagram"] {
                padding: 14px 24px !important;
                font-size: 1rem !important;
                font-weight: 600 !important;
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
        
        /* Inactive membership styling with subtle pulse animation */
        .inactive-membership {
            animation: subtle-pulse 3s infinite;
        }
        
        @keyframes subtle-pulse {
            0% { transform: scale(1); box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3); }
            50% { transform: scale(1.02); box-shadow: 0 6px 20px rgba(239, 68, 68, 0.5); }
            100% { transform: scale(1); box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3); }
        }
        
        /* Group Workout Booking Styles */
        .workout-tab {
            background-color: #1f1f1f;
            color: #9ca3af;
            border: 1px solid #333;
        }
        
        .workout-tab.active {
            background-color: #00b4d8;
            color: #000;
            border-color: #00b4d8;
        }
        
        .workout-tab:hover:not(.active) {
            background-color: #2a2a2a;
        }
        
        .workout-session-card {
            background-color: #1f1f1f;
            border: 1px solid #333;
            border-radius: 0.75rem;
            padding: 1rem;
            transition: all 0.3s ease;
        }
        
        .workout-session-card:hover {
            border-color: #00b4d8;
            box-shadow: 0 4px 12px rgba(255, 223, 6, 0.1);
        }
        
        .workout-book-btn {
            background-color: #00b4d8;
            color: #000;
            padding: 0.5rem 1.25rem;
            border-radius: 0.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }
        
        .workout-book-btn:hover:not(:disabled) {
            background-color: #e6c605;
            transform: translateY(-2px);
        }
        
        .workout-book-btn:disabled {
            background-color: #666;
            color: #999;
            cursor: not-allowed;
        }
        
        .workout-cancel-btn {
            background-color: transparent;
            color: #ef4444;
            padding: 0.5rem 1.25rem;
            border-radius: 0.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
            border: 1px solid #ef4444;
            cursor: pointer;
        }
        
        .workout-cancel-btn:hover {
            background-color: #ef4444;
            color: #fff;
        }
        
        .workout-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .workout-badge.full {
            background-color: rgba(239, 68, 68, 0.2);
            color: #ef4444;
        }
        
        /* Calendar Styles */
        .week-day-cell {
            background-color: #1f1f1f;
            border: 2px solid #333;
            border-radius: 0.75rem;
            padding: 0.75rem 0.5rem;
            cursor: pointer;
            transition: all 0.2s ease;
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100px;
        }
        
        .week-day-cell:hover {
            border-color: #00b4d8;
            background-color: #2a2a2a;
            transform: translateY(-2px);
        }
        
        .week-day-cell.inactive {
            opacity: 0.4;
            cursor: default;
            background-color: #1a1a1a;
        }
        
        .week-day-cell.inactive:hover {
            border-color: #333;
            background-color: #1a1a1a;
            transform: none;
        }
        
        .week-day-cell.today {
            border-color: #00b4d8;
            border-width: 2px;
            background-color: rgba(255, 223, 6, 0.05);
        }
        
        .week-day-cell.has-sessions {
            background-color: rgba(255, 223, 6, 0.08);
            border-color: #555;
        }
        
        .week-day-cell.has-sessions:hover {
            background-color: rgba(255, 223, 6, 0.15);
        }
        
        .week-day-cell.selected {
            border-color: #00b4d8;
            border-width: 3px;
            background-color: rgba(255, 223, 6, 0.12);
        }
        
        .week-day-name {
            font-size: 0.75rem;
            font-weight: 600;
            color: #9ca3af;
            margin-bottom: 0.25rem;
        }
        
        .week-day-number {
            font-size: 1.25rem;
            font-weight: bold;
            color: #fff;
            margin-bottom: 0.5rem;
        }
        
        .week-day-cell.inactive .week-day-number {
            color: #555;
        }
        
        .week-sessions-count {
            font-size: 0.75rem;
            background-color: rgba(255, 223, 6, 0.2);
            color: #00b4d8;
            padding: 0.25rem 0.5rem;
            border-radius: 9999px;
            margin-top: auto;
        }
        
        .calendar-day {
            aspect-ratio: 1;
            background-color: #1f1f1f;
            border: 1px solid #333;
            border-radius: 0.5rem;
            padding: 0.5rem;
            cursor: pointer;
            transition: all 0.2s ease;
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
        }
        
        .calendar-day:hover {
            border-color: #00b4d8;
            background-color: #2a2a2a;
        }
        
        .calendar-day.inactive {
            opacity: 0.3;
            cursor: default;
        }
        
        .calendar-day.inactive:hover {
            border-color: #333;
            background-color: #1f1f1f;
        }
        
        .calendar-day.today {
            border-color: #00b4d8;
            border-width: 2px;
        }
        
        .calendar-day.has-sessions {
            background-color: rgba(255, 223, 6, 0.05);
        }
        
        .calendar-day.has-sessions:hover {
            background-color: rgba(255, 223, 6, 0.1);
        }
        
        .calendar-day-number {
            font-size: 0.875rem;
            font-weight: 600;
            color: #fff;
            margin-bottom: 0.25rem;
        }
        
        .calendar-day.inactive .calendar-day-number {
            color: #666;
        }
        
        .calendar-sessions-indicator {
            width: 6px;
            height: 6px;
            background-color: #00b4d8;
            border-radius: 50%;
            margin-top: auto;
        }
        
        .session-time-badge {
            font-size: 0.625rem;
            background-color: rgba(255, 223, 6, 0.2);
            color: #00b4d8;
            padding: 0.125rem 0.375rem;
            border-radius: 0.25rem;
            margin-top: 0.25rem;
        }
        
        .workout-badge.available {
            background-color: rgba(34, 197, 94, 0.2);
            color: #22c55e;
        }
        
        .workout-badge.booked {
            background-color: rgba(255, 223, 6, 0.2);
            color: #00b4d8;
        }
        
        /* Mobile-specific styles for workout booking */
        @media (max-width: 768px) {
            .week-day-cell {
                min-height: 80px;
                padding: 0.5rem 0.25rem;
                -webkit-tap-highlight-color: rgba(255, 223, 6, 0.2);
                touch-action: manipulation;
            }
            
            .week-day-name {
                font-size: 0.7rem;
            }
            
            .week-day-number {
                font-size: 1.1rem;
            }
            
            #day-sessions-container {
                position: relative;
                z-index: 10;
                margin-top: 1rem;
            }
            
            .workout-session-card {
                padding: 0.875rem;
            }
        }
    </style>
</head>

<body>

<a href="https://wa.me/+995-XXX-XXX-XXX" class="whatsapp-float" target="_blank">
    <div class="whatsapp-button">
        <img src="https://upload.wikimedia.org/wikipedia/commons/6/6b/WhatsApp.svg" alt="WhatsApp" class="whatsapp-icon" onerror="this.onerror=null; this.src='https://placehold.co/24x24/ffffff/25d366?text=WA';">
        <span class="whatsapp-text" name="key_contact_whatsapp">Contact Us</span>
    </div>
</a>
    
<div id="cont">
    <header class="w-full p-4 flex justify-between items-center">
        <!-- Logo on the left -->
        <div class="flex items-center">
            <a href="/" class="inline-block bg-black p-2 rounded-lg hover:bg-gray-800 transition-colors duration-300">
                <img src="/img/logo.png" alt="Luka Qaliashvili, ID: 0172409681 Logo" class="h-10" onerror="this.onerror=null; this.src='https://placehold.co/140x40/cccccc/000000?text=Synergy+Logo';">
            </a>
        </div>
        <!-- Language and logout buttons on the right -->
        <div class="flex items-center gap-4">
            <div onclick="SetLanguage()" name='key_lang' class="lang-btn">ქართული</div>

            <a href="?logout=true" name="key_logout" class="logout-btn">Log Out</a>
        </div>
    </header>
    
    <div class="flex-grow flex items-center justify-center py-12">
        <div class="container mx-auto px-4">
            <div class="max-w-sm w-full p-6 md:p-8 mx-auto profile-card">
                
                <div class="text-center mb-6">
                    <h2 class="text-3xl font-bold text-white" name="key_user_name"><?php echo htmlspecialchars($userFullName); ?></h2>
                    <?php if ($membershipStatusKey === 'key_inactive_member'): ?>
                        <div class="bg-gradient-to-r from-red-500 to-orange-500 text-white font-bold py-4 px-6 rounded-lg mt-4 shadow-lg border-2 border-red-400 inactive-membership">
                            <i class="fas fa-exclamation-triangle mr-2 text-yellow-300"></i>
                            <p class="text-lg text-white font-bold" name="<?php echo htmlspecialchars($membershipStatusKey); ?>"></p>
                        </div>
                    <?php else: ?>
                        <div class="bg-gradient-to-r from-green-500 to-emerald-500 text-white font-semibold py-3 px-4 rounded-lg mt-3 shadow-lg border-2 border-green-400">
                            <i class="fas fa-check-circle mr-2"></i>
                            <p class="text-lg" name="<?php echo htmlspecialchars($membershipStatusKey); ?>"></p>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Membership Type Display -->
                    <div class="bg-gray-800 border border-gray-600 p-3 rounded-lg mt-4">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-400 flex items-center">
                                <i class="fas fa-id-card mr-2"></i>
                                <span name="key_membership_type">Membership Type</span>
                            </span>
                            <span class="font-bold text-yellow-400"><?php echo htmlspecialchars($membershipType); ?></span>
                        </div>
                    </div>
                </div>

                <?php if ($showQrCode): ?> 
                <div class="qr-code-container mb-6">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=<?php echo htmlspecialchars($qrCodePhone); ?>&bgcolor=ffffff" 
                         alt="Member QR Code" 
                         class="mx-auto rounded-md"
                         onerror="this.onerror=null; this.src='https://placehold.co/250x250/ffffff/000000?text=QR+Code';">
                </div>
                <?php endif; ?>

                <!-- Pause Membership Section - Visible to All Members -->
                <div class="bg-gray-800 border border-gray-600 p-4 rounded-lg mb-6" id="pause-section">
                    <h3 class="text-lg font-semibold text-white mb-3 flex items-center">
                        <i class="fas fa-pause-circle mr-2 text-yellow-400"></i>
                        <span name="key_pause_membership">Pause Membership</span>
                    </h3>
                    
                    <?php if ($pauseStatus == 1): ?>
                    <!-- Currently Paused -->
                    <div class="bg-orange-900 border border-orange-600 p-3 rounded-lg mb-4">
                        <p class="text-orange-200 text-sm" name="key_membership_paused">Your membership is currently paused</p>
                        <?php if ($pauseStartDate): ?>
                        <p class="text-orange-200 text-xs mt-1">
                            <span name="key_paused_since">Paused since:</span> <?php echo $pauseStartDate->format('F d, Y'); ?>
                        </p>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

                    <?php if ($canPause): ?>
                    <div class="space-y-3 mb-4">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-400" name="key_remaining_pause_days">Remaining Pause Days</span>
                            <span class="font-bold text-white" data-pause-display><?php 
// Handle NULL, empty, or "null" string values properly
// Check for various ways SQL Server might return NULL
$isNullish = ($remainingPauseDays === null || 
              $remainingPauseDays === '' || 
              $remainingPauseDays === 'null' || 
              $remainingPauseDays === 'NULL' ||
              !is_numeric($remainingPauseDays) ||
              (is_string($remainingPauseDays) && trim($remainingPauseDays) === ''));

$displayRemainingPause = $isNullish ? $maxPauseDays : $remainingPauseDays;

// Debug output
echo "<!-- DEBUG Display: remainingPauseDays='" . var_export($remainingPauseDays, true) . "', maxPauseDays=$maxPauseDays, isNullish=" . ($isNullish ? 'YES' : 'NO') . ", displaying=$displayRemainingPause -->";

echo $displayRemainingPause . '/' . $maxPauseDays; 
?></span>
                        </div>
                        <?php if ($pauseStatus == 1 && $pauseStartDate): ?>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-400" name="key_days_paused">Days Currently Paused</span>
                            <span class="font-bold text-orange-300">
                                <?php 
                                $currentDate = new DateTime();
                                echo $pauseStartDate->diff($currentDate)->days; 
                                ?>
                            </span>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

                    <?php if ($pauseStatus == 0): ?>
                    <!-- Not Paused - Show Pause Button -->
                    <button id="pause-btn" class="w-full bg-orange-600 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded-lg transition-colors" 
                            <?php 
                            // Check if remaining pause days is effectively null or zero
                            $hasNoPauseDays = ($remainingPauseDays === null || 
                                             $remainingPauseDays === '' || 
                                             $remainingPauseDays === 'null' || 
                                             $remainingPauseDays === 'NULL' ||
                                             !is_numeric($remainingPauseDays) ||
                                             (is_string($remainingPauseDays) && trim($remainingPauseDays) === '')) 
                                             ? false // If null-ish, they have full pause days available
                                             : (intval($remainingPauseDays) <= 0); // If numeric, check if <= 0
                            
                            echo (!$canPause || $hasNoPauseDays) ? 'disabled' : ''; 
                            ?>>
                        <i class="fas fa-pause mr-2"></i>
                        <span name="key_pause_membership_btn">Pause Membership</span>
                    </button>
                    <?php if (!$canPause): ?>
                    <p class="text-red-400 text-xs mt-2 text-center" name="key_package_not_eligible">
                        Your package is not eligible for pausing. Only yearly packages can be paused.
                    </p>
                    <?php elseif (($remainingPauseDays !== null && 
                                  $remainingPauseDays !== '' && 
                                  $remainingPauseDays !== 'null' && 
                                  $remainingPauseDays !== 'NULL' &&
                                  is_numeric($remainingPauseDays) && 
                                  intval($remainingPauseDays) <= 0)): ?>
                    <p class="text-red-400 text-xs mt-2 text-center" name="key_no_pause_days_left">
                        You have used all your pause days (<?php echo isset($maxPauseDays) ? $maxPauseDays : 60; ?> days maximum).
                    </p>
                    <?php endif; ?>
                    <?php else: ?>
                    <!-- Paused - Show Resume Button -->
                    <button id="resume-btn" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition-colors">
                        <i class="fas fa-play mr-2"></i>
                        <span name="key_resume_membership_btn">Resume Membership</span>
                    </button>
                    <p class="text-yellow-400 text-xs mt-2 text-center" name="key_resume_info">
                        Your membership end date will be extended by the paused days when you resume.
                    </p>
                    <?php endif; ?>
                </div>
                
                <div class="space-y-4 mb-6">
                    <div class="flex justify-between items-center bg-gray-900 p-3 rounded-lg">
                        <span class="text-gray-400 flex items-center">
                            <i class="fas fa-users mr-2"></i>
                            <span name="key_active_users_status"></span> </span>
                        <span class="font-bold text-xl text-white flex items-center">
                            <span class="live-dot mr-2"></span>
                            <span id="active-users-count"><?php echo htmlspecialchars($activeUsersCount); ?></span>
                        </span>
                    </div>
                    <?php if ($membershipStatusKey === 'key_active_member'): ?>
                    <div class="flex justify-between items-center bg-gray-900 p-3 rounded-lg">
                        <span class="text-gray-400 flex items-center">
                            <i class="fas fa-calendar-times mr-2"></i>
                            <span name="key_expires_on">Expires on</span>
                        </span>
                        <span class="font-bold text-white"><?php echo htmlspecialchars($membershipEndDate); ?></span>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Renew Membership Button -->
                <div class="mb-8">
                    <a href="../success.php?phone=<?php echo urlencode($username ?? ''); ?><?php echo ($mysqlUserId ? '&user_id=' . urlencode($mysqlUserId) : ''); ?>" class="renew-btn w-full font-bold py-3 px-4 text-lg text-center block" name="<?php echo htmlspecialchars($renewButtonTextKey); ?>" id="renew-link">Renew Membership</a>
                </div>

                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-white mb-3" name="key_visit_summary_title">Visit Summary (Last 3 Months)</h3>
                    <div class="space-y-2" id="visit-summary-list">
                        <?php foreach ($visitSummary as $visit): ?>
                        <div class="summary-row p-3 rounded-lg flex justify-between items-center">
                            <span class="text-gray-300" name="<?php echo htmlspecialchars($visit['key']); ?>"><?php echo htmlspecialchars($visit['month']); ?></span>
                            <span class="font-bold text-white"><?php echo htmlspecialchars($visit['count']); ?> <span name="key_visits">Visits</span></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Group Workout Booking Section -->
                <div class="mb-8 pt-4 border-t border-gray-700">
                    <h3 class="text-lg font-semibold text-white mb-4" name="key_group_workouts_title">Group Workouts</h3>
                    
                    <!-- Tab Navigation -->
                    <div class="flex gap-2 mb-4">
                        <button onclick="switchWorkoutTab('available')" id="tab-available" class="workout-tab active flex-1 py-2 px-4 rounded-lg font-semibold transition-colors">
                            <span name="key_available_classes">Available Classes</span>
                        </button>
                        <button onclick="switchWorkoutTab('booked')" id="tab-booked" class="workout-tab flex-1 py-2 px-4 rounded-lg font-semibold transition-colors">
                            <span name="key_my_bookings">My Bookings</span>
                        </button>
                    </div>
                    
                    <!-- Available Sessions Tab -->
                    <div id="available-sessions-container" class="workout-tab-content">
                        <!-- Week Navigation -->
                        <div class="flex items-center justify-between mb-4 bg-gray-800 p-3 rounded-lg">
                            <button onclick="changeWeek(-1)" class="p-2 hover:bg-gray-700 rounded-lg transition-colors">
                                <i class="fas fa-chevron-left text-yellow-400"></i>
                            </button>
                            <div class="text-center">
                                <h4 id="week-range" class="text-base font-semibold text-white"></h4>
                                <p class="text-xs text-gray-400 mt-1" name="key_click_day_to_see_sessions">Click on a day to see sessions</p>
                            </div>
                            <button onclick="changeWeek(1)" class="p-2 hover:bg-gray-700 rounded-lg transition-colors">
                                <i class="fas fa-chevron-right text-yellow-400"></i>
                            </button>
                        </div>
                        
                        <!-- Week Calendar Grid -->
                        <div class="grid grid-cols-7 gap-2 mb-4" id="week-calendar-grid">
                            <!-- Days will be populated by JavaScript -->
                        </div>
                        
                        <!-- Selected Day Sessions -->
                        <div id="day-sessions-container" class="hidden">
                            <div class="flex justify-between items-center mb-3 pb-2 border-b border-gray-700">
                                <h4 id="selected-day-title" class="text-base font-semibold text-white"></h4>
                                <button onclick="closeDaySessions()" class="text-gray-400 hover:text-white text-sm">
                                    <i class="fas fa-times mr-1"></i> Close
                                </button>
                            </div>
                            <div id="day-sessions-list" class="space-y-3"></div>
                        </div>
                        
                        <!-- Loading State -->
                        <div id="available-sessions-loading" class="text-center py-8">
                            <i class="fas fa-spinner fa-spin text-2xl text-yellow-400"></i>
                            <p class="text-gray-400 mt-2" name="key_loading">Loading...</p>
                        </div>
                        
                        <div id="available-sessions-empty" class="text-center py-8 hidden">
                            <i class="fas fa-calendar-times text-4xl text-gray-600 mb-3"></i>
                            <p class="text-gray-400" name="key_no_available_sessions">No available sessions at the moment</p>
                        </div>
                    </div>
                    
                    <!-- My Bookings Tab -->
                    <div id="my-bookings-container" class="workout-tab-content hidden">
                        <div id="my-bookings-loading" class="text-center py-8">
                            <i class="fas fa-spinner fa-spin text-2xl text-yellow-400"></i>
                            <p class="text-gray-400 mt-2" name="key_loading">Loading...</p>
                        </div>
                        <div id="my-bookings-list" class="space-y-3 hidden"></div>
                        <div id="my-bookings-empty" class="text-center py-8 hidden">
                            <i class="fas fa-calendar-check text-4xl text-gray-600 mb-3"></i>
                            <p class="text-gray-400" name="key_no_bookings">You have no upcoming bookings</p>
                        </div>
                    </div>
                </div>

                <!-- Mobile App Download Images -->
                <div class="mb-6 pt-4 border-t border-gray-700">
                    <div style="padding: 1.5rem 0; display: flex; justify-content: center; align-items: center; gap: 25px; flex-wrap: wrap;">
                        <a href="https://play.google.com/store/apps/details?id=ge.Synergy.gym" target="_blank" rel="noopener noreferrer" style="transition: all 0.3s ease; display: inline-block; filter: drop-shadow(0 4px 12px rgba(255, 255, 255, 0.3));" onmouseover="this.style.transform='scale(1.1) translateY(-5px)'; this.style.filter='drop-shadow(0 8px 20px rgba(255, 255, 255, 0.5))'" onmouseout="this.style.transform='scale(1) translateY(0)'; this.style.filter='drop-shadow(0 4px 12px rgba(255, 255, 255, 0.3))'">
                            <img src="../img/android.png" alt="Download on Google Play" style="height: 70px; width: auto; border-radius: 8px;">
                        </a>
                        <a href="https://apps.apple.com/ge/app/Synergy-gym/id6752832860" target="_blank" rel="noopener noreferrer" style="transition: all 0.3s ease; display: inline-block; filter: drop-shadow(0 4px 12px rgba(255, 255, 255, 0.3));" onmouseover="this.style.transform='scale(1.1) translateY(-5px)'; this.style.filter='drop-shadow(0 8px 20px rgba(255, 255, 255, 0.5))'" onmouseout="this.style.transform='scale(1) translateY(0)'; this.style.filter='drop-shadow(0 4px 12px rgba(255, 255, 255, 0.3))'">
                            <img src="../img/ios.png" alt="Download on App Store" style="height: 70px; width: auto; border-radius: 8px;">
                        </a>
                    </div>
                </div>

                <!-- (Old mobile app section removed; replaced by banner under Renew) -->

                <!-- Support Contact Section -->
                <div class="support-section p-6 mb-6">
                    <h3 class="text-xl font-bold text-white mb-4 text-center" name="key_need_help">
                        Need Help?
                    </h3>
                    <p class="text-gray-400 mb-6 text-center" name="key_contact_support">
                        Contact us anytime
                    </p>
                    <div class="flex flex-wrap justify-center gap-6 mb-6">
                        <a href="tel:+995322195119" class="contact-link inline-flex items-center">
                            <i class="fas fa-phone mr-2"></i>
                            <span>+995-XXX-XXX-XXX</span>
                        </a>
                        <a href="mailto:info@synergy-gym.ge" class="contact-link inline-flex items-center">
                            <i class="fas fa-envelope mr-2"></i>
                            <span>info@synergy-gym.ge</span>
                        </a>
                        <a href="https://wa.me/995551195819" target="_blank" class="contact-link inline-flex items-center">
                            <i class="fab fa-whatsapp mr-2"></i>
                            <span>WhatsApp</span>
                        </a>
                    </div>
                    
                    <!-- Social Media Section -->
                    <div class="text-center">
                        <div class="flex justify-center gap-4">
                            <a href="https://www.facebook.com/SynergyGymTbilisi" target="_blank" rel="noopener noreferrer" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all duration-300 transform hover:scale-105 active:scale-95 touch-manipulation" onclick="openSocialMedia(event, 'https://www.facebook.com/SynergyGymTbilisi')">
                                <i class="fab fa-facebook-f mr-2"></i>
                                <span>Facebook</span>
                            </a>
                            <a href="https://www.instagram.com/synergy_gym_tbilisi/" target="_blank" rel="noopener noreferrer" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white rounded-lg transition-all duration-300 transform hover:scale-105 active:scale-95 touch-manipulation" onclick="openSocialMedia(event, 'https://www.instagram.com/synergy_gym_tbilisi/')">
                                <i class="fab fa-instagram mr-2"></i>
                                <span>Instagram</span>
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
// Translation system - inline for reliability
const translations = {
    en: {
        key_lang: 'ქართული',
        key_user_name: 'Member Name',
        key_membership_status: 'Active Member',
        key_active_member: 'Active Member',
        key_inactive_member: 'Your membership is not active',
        key_expires_on: 'Expires on',
        key_renew_button: 'Renew Membership',
        key_buy_subscription: 'Buy Subscription',
        key_logout: 'Log Out',
        key_visit_summary_title: 'Visit Summary (Last 3 Months)',
        key_january: 'January',
        key_february: 'February',
        key_march: 'March',
        key_april: 'April',
        key_may: 'May',
        key_june: 'June',
        key_july: 'July',
        key_august: 'August',
        key_september: 'September',
        key_october: 'October',
        key_november: 'November',
        key_december: 'December',
        key_visits: 'Visits',
        key_almost_none: 'Almost no one in the gym',
        key_less_than_30: 'Less than 30 people in the gym',
        key_quite_busy: 'more than 30 people in the gym',
        key_active_users_status: 'Active Users Now',
        key_membership_type: 'Membership Type',
        key_pause_membership: 'Pause Membership',
        key_membership_paused: 'Your membership is currently paused',
        key_remaining_pause_days: 'Remaining Pause Days',
        key_pause_membership_btn: 'Pause Membership',
        key_resume_membership_btn: 'Resume Membership',
        key_paused_since: 'Paused since',
        key_days_paused: 'Days Currently Paused',
        key_package_not_eligible: 'Your package is not eligible for pausing. Only yearly packages can be paused.',
        key_no_pause_days_left: 'You have used all your pause days (maximum varies by package).',
        key_resume_info: 'Your membership end date will be extended by the paused days when you resume.',
        key_pause_success: 'Membership paused successfully',
        key_resume_success: 'Membership resumed successfully',
        key_resume_success_capped: 'Membership resumed successfully. Note: You were paused for {days_paused} days, but only {days_extended} days were added to your membership (your package limit).',
        key_pause_confirm: 'Are you sure you want to pause your membership? (Days will be deducted based on actual pause duration when you resume)',
        key_need_help: 'Need Help?',
        key_contact_support: 'Contact us anytime',
        key_contact_whatsapp: 'Contact Us',
        // Mobile App Section
        key_get_mobile_app: 'Get Our Mobile App',
        key_available_on_play: 'Available on Google Play',
        key_app_benefits: 'Access your membership, check gym status, and manage your account on-the-go!',
        key_download_app: 'Download Now',
        key_app_feature_qr: 'QR Access',
        key_app_feature_renew: 'Easy Renew',
        key_app_feature_status: 'Live Status',
        key_app_tap_to_install: 'Tap to install from Google Play',
        key_free: 'Free',
        key_secure: 'Secure',
        // Group Workouts Section
        key_group_workouts_title: 'Group Workouts',
        key_available_classes: 'Available Classes',
        key_my_bookings: 'My Bookings',
        key_loading: 'Loading...',
        key_no_available_sessions: 'No available sessions at the moment',
        key_no_bookings: 'You have no upcoming bookings',
        key_click_day_to_see_sessions: 'Click on a day to see sessions',
        key_spots_left: 'spots left',
        key_book_now: 'Book Now',
        key_cancel_booking: 'Cancel Booking',
        key_full: 'Full',
        key_booked: 'Booked',
        key_confirmed: 'Confirmed',
        key_close: 'Close',
        key_booked_on: 'Booked',
        key_sunday: 'Sunday',
        key_monday: 'Monday',
        key_tuesday: 'Tuesday',
        key_wednesday: 'Wednesday',
        key_thursday: 'Thursday',
        key_friday: 'Friday',
        key_saturday: 'Saturday',
        key_sun: 'Sun',
        key_mon: 'Mon',
        key_tue: 'Tue',
        key_wed: 'Wed',
        key_thu: 'Thu',
        key_fri: 'Fri',
        key_sat: 'Sat',
        key_jan: 'Jan',
        key_feb: 'Feb',
        key_mar: 'Mar',
        key_apr: 'Apr',
        key_may_short: 'May',
        key_jun: 'Jun',
        key_jul: 'Jul',
        key_aug: 'Aug',
        key_sep: 'Sep',
        key_oct: 'Oct',
        key_nov: 'Nov',
        key_dec: 'Dec'
    },
    ka: {
        key_lang: 'English',
        key_user_name: 'წევრის სახელი',
        key_membership_status: 'აქტიური წევრი',
        key_active_member: 'აქტიური წევრი',
        key_inactive_member: 'აბონემენტი არ არის აქტიური',
        key_expires_on: 'ვადა იწურება',
        key_renew_button: 'აბონიმენტის განახლება',
        key_buy_subscription: 'აბონიმენტის ყიდვა',
        key_logout: 'გასვლა',
        key_visit_summary_title: 'ვიზიტების შეჯამება (ბოლო 3 თვე)',
        key_january: 'იანვარი',
        key_february: 'თებერვალი',
        key_march: 'მარტი',
        key_april: 'აპრილი',
        key_may: 'მაისი',
        key_june: 'ივნისი',
        key_july: 'ივლისი',
        key_august: 'აგვისტო',
        key_september: 'სექტემბერი',
        key_october: 'ოქტომბერი',
        key_november: 'ნოემბერი',
        key_december: 'დეკემბერი',
        key_visits: 'ვიზიტები',
        key_almost_none: 'დარბაზში თითქმის არავინ არ არი',
        key_less_than_30: '30-ზე ნაკლები ადამიანია ჯიმში',
        key_quite_busy: 'დარბაზში 30-ზე მეტი ადამიანია',
        key_active_users_status: 'ამჟამინდელი აქტიური მომხმარებლები',
        key_membership_type: 'აბონიმენტის ტიპი',
        key_pause_membership: 'აბონიმენტის შეჩერება',
        key_membership_paused: 'თქვენი აბონემენტი ამჟამად შეჩერებულია',
        key_remaining_pause_days: 'დარჩენილი შეჩერების დღეები',
        key_pause_membership_btn: 'აბონიმენტის შეჩერება',
        key_resume_membership_btn: 'აბონიმენტის გაგრძელება',
        key_paused_since: 'შეჩერდა',
        key_days_paused: 'შეჩერებული დღეები',
        key_package_not_eligible: 'თქვენი აბონემენტი არ არის შეჩერების უფლებამოსილი. მხოლოდ წლიური აბონემენტები შეიძლება შეჩერდეს.',
        key_no_pause_days_left: 'თქვენ ამოიწურეთ ყველა შეჩერების დღე (მაქსიმუმი განსხვავდება პაკეტის მიხედვით).',
        key_resume_info: 'თქვენი აბონემენტის ვადა გაგრძელდება შეჩერებული დღეების რაოდენობით.',
        key_pause_success: 'აბონემენტი წარმატებით შეჩერდა',
        key_resume_success: 'აბონემენტი წარმატებით განახლდა',
        key_resume_success_capped: 'აბონემენტი წარმატებით განახლდა. შენიშვნა: თქვენ იყავით შეჩერებული {days_paused} დღე, მაგრამ მხოლოდ {days_extended} დღე დაემატა თქვენს აბონემენტს (თქვენი პაკეტის ლიმიტი).',
        key_pause_confirm: 'დარწმუნებული ხართ, რომ გსურთ აბონემენტის დაპაუზება? (დღეები ჩამოიხსნება რეალური პაუზის ხანგრძლივობის მიხედვით, როცა განაახლებთ)',
        key_need_help: 'გჭირდებათ დახმარება?',
        key_contact_support: 'დაგვიკავშირდით ნებისმიერ დროს',
        key_contact_whatsapp: 'დაგვიკავშირდით',
        // Mobile App Section
        key_get_mobile_app: 'ჩვენი მობილური აპი',
        key_available_on_play: 'Google Play Store-ზე',
        key_app_benefits: 'მიიღეთ წვდომა თქვენს აბონიმენტზე, შეამოწმეთ ჯიმის სტატუსი და მართეთ თქვენი ანგარიში მობილურად!',
        key_download_app: 'ჩამოტვირთვა',
        key_app_feature_qr: 'QR წვდომა',
        key_app_feature_renew: 'მარტივი განახლება',
        key_app_feature_status: 'ლაივ სტატუსი',
        key_app_tap_to_install: 'Google Play Store-დან ჩამოტვირთეთ',
        key_free: 'უფასო',
        key_secure: 'უსაფრთხო',
        // Group Workouts Section
        key_group_workouts_title: 'ჯგუფური ვარჯიშები',
        key_available_classes: 'ხელმისაწვდომი გაკვეთილები',
        key_my_bookings: 'ჩემი ჯავშნები',
        key_loading: 'იტვირთება...',
        key_no_available_sessions: 'ამ მომენტში არ არის ხელმისაწვდომი სესიები',
        key_no_bookings: 'თქვენ არ გაქვთ მომავალი ჯავშნები',
        key_click_day_to_see_sessions: 'დააჭირეთ დღეს სესიების სანახავად',
        key_spots_left: 'ადგილი დარჩა',
        key_book_now: 'დაჯავშნა',
        key_cancel_booking: 'გაუქმება',
        key_full: 'სავსეა',
        key_booked: 'დაჯავშნილი',
        key_confirmed: 'დადასტურებული',
        key_close: 'დახურვა',
        key_booked_on: 'დაჯავშნილია',
        key_sunday: 'კვირა',
        key_monday: 'ორშაბათი',
        key_tuesday: 'სამშაბათი',
        key_wednesday: 'ოთხშაბათი',
        key_thursday: 'ხუთშაბათი',
        key_friday: 'პარასკევი',
        key_saturday: 'შაბათი',
        key_sun: 'კვი',
        key_mon: 'ორშ',
        key_tue: 'სამ',
        key_wed: 'ოთხ',
        key_thu: 'ხუთ',
        key_fri: 'პარ',
        key_sat: 'შაბ',
        key_jan: 'იან',
        key_feb: 'თებ',
        key_mar: 'მარ',
        key_apr: 'აპრ',
        key_may_short: 'მაი',
        key_jun: 'ივნ',
        key_jul: 'ივლ',
        key_aug: 'აგვ',
        key_sep: 'სექ',
        key_oct: 'ოქტ',
        key_nov: 'ნოე',
        key_dec: 'დეკ'
    }
};

// Translation functions - available immediately
function updateActiveUsersStatus(lang) {
    const countElement = document.getElementById('active-users-count');
    const textElement = document.querySelector('[name="key_active_users_status"]');
    if (!countElement || !textElement) return;

    const count = parseInt(countElement.textContent, 10);
    const langData = translations[lang];

    let statusKey = '';
    if (count < 10) {
        statusKey = 'key_almost_none';
    } else if (count >= 10 && count <= 35) {
        statusKey = 'key_less_than_30';
    } else {
        statusKey = 'key_quite_busy';
    }

    if (langData[statusKey]) {
        textElement.textContent = langData[statusKey];
    }
}

function applyTranslations(lang) {
    console.log('Applying translations for language:', lang);
    if (!translations[lang]) {
        console.error('No translations found for language:', lang);
        return;
    }
    document.documentElement.lang = lang;
    const langData = translations[lang];
    console.log('Translation data loaded:', Object.keys(langData).length, 'keys');
    
    const elementsToTranslate = document.querySelectorAll('[name^="key_"]');
    console.log('Found elements to translate:', elementsToTranslate.length);
    
    elementsToTranslate.forEach(el => {
        const key = el.getAttribute('name');
        // Skip the status text as it's handled separately
        if (key === 'key_active_users_status') return;
        // Skip key_user_name as it's dynamically populated by PHP
        if (key === 'key_user_name') return;
        // Skip the renew/buy button as its name attribute is dynamic
        if (key === 'key_renew_button' || key === 'key_buy_subscription') return;
        // Skip membership status as its name attribute is dynamic
        if (key === 'key_active_member' || key === 'key_inactive_member') return;

        if (langData[key]) {
            console.log('Translating:', key, '->', langData[key]);
            // Handle the special case for the visit counts
            if (el.parentElement.textContent.match(/\d+/)) {
                // This is a visit count span, just translate the "Visits" part
                el.innerHTML = langData[key];
            } else {
                el.innerHTML = langData[key];
            }
        } else {
            console.warn('No translation found for key:', key);
        }
    });
    
    // Update the active user status text after other translations
    updateActiveUsersStatus(lang);

    // Manually update the renew/buy button text based on its dynamic name attribute
    const renewButton = document.querySelector('.renew-btn');
    if (renewButton) {
        const currentButtonKey = renewButton.getAttribute('name');
        if (langData[currentButtonKey]) {
            renewButton.innerHTML = langData[currentButtonKey];
        }
    }

    // Manually update the membership status text based on its dynamic name attribute
    const membershipStatusElement = document.querySelector('p[name^="key_membership_status"], p[name="key_active_member"], p[name="key_inactive_member"]');
    if (membershipStatusElement) {
        const currentStatusKey = membershipStatusElement.getAttribute('name');
        if (langData[currentStatusKey]) {
            membershipStatusElement.innerHTML = langData[currentStatusKey];
        }
    }
}

// Function to update renew link with current language
function updateRenewLink() {
    const renewLink = document.getElementById('renew-link');
    if (renewLink) {
        const currentLang = localStorage.getItem('language') || 'ka';
        const currentHref = renewLink.getAttribute('href');
        
        // Remove existing lang parameter if present
        let baseUrl = currentHref.split('&lang=')[0].split('?lang=')[0];
        
        // Add lang parameter
        const separator = baseUrl.includes('?') ? '&' : '?';
        renewLink.setAttribute('href', baseUrl + separator + 'lang=' + encodeURIComponent(currentLang));
        
        console.log('Updated renew link with language:', currentLang);
    }
}

// Global function for language switching
window.SetLanguage = function() {
    const currentLang = localStorage.getItem('language') || localStorage.getItem('ActiveLanguage') || 'ka';
    const newLang = currentLang === 'ka' ? 'en' : 'ka';
    console.log('Language switch:', currentLang, '->', newLang);
    // Set both keys for compatibility with different systems
    localStorage.setItem('language', newLang);
    localStorage.setItem('ActiveLanguage', newLang);
    applyTranslations(newLang);
    updateRenewLink(); // Update renew link when language changes
}

// Social Media Button Function for Mobile
function openSocialMedia(event, url) {
    // Prevent default link behavior
    event.preventDefault();
    
    // Check if we're on mobile
    const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
    
    if (isMobile) {
        // For mobile, use a timeout to ensure the page doesn't close
        setTimeout(() => {
            try {
                // Try to open in new window first
                const newWindow = window.open(url, '_blank', 'noopener,noreferrer');
                
                // If popup was blocked, try direct navigation
                if (!newWindow || newWindow.closed || typeof newWindow.closed == 'undefined') {
                    window.location.href = url;
                }
            } catch (error) {
                // Fallback to direct navigation
                window.location.href = url;
            }
        }, 100);
    } else {
        // For desktop, normal behavior
        window.open(url, '_blank', 'noopener,noreferrer');
    }
}

// Initialize translations when page loads
document.addEventListener('DOMContentLoaded', () => {
    console.log('=== TRANSLATION DEBUG START ===');
    console.log('Available translations:', Object.keys(translations));
    console.log('EN translations count:', Object.keys(translations.en).length);
    console.log('KA translations count:', Object.keys(translations.ka).length);
    
    // Apply initial language from localStorage or default to Georgian
    const savedLang = localStorage.getItem('language') || localStorage.getItem('ActiveLanguage') || 'ka';
    console.log('Initializing with language:', savedLang);
    console.log('=== APPLYING TRANSLATIONS ===');
    applyTranslations(savedLang);
    updateRenewLink(); // Update renew link with current language on page load

    // Pause membership functionality
    let pauseInfo = null;

    async function loadPauseInfo() {
        try {
            const response = await fetch('pause_membership.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=get_pause_info&client_id=<?php echo $clientID ?? 0; ?>'
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            console.log('Pause info loaded:', data);

            if (data.status === 'success') {
                pauseInfo = data;
                updatePauseUI();
            } else {
                console.error('Failed to load pause info:', data.message);
            }
        } catch (error) {
            console.error('Error loading pause info:', error);
        }
    }

    function updatePauseUI() {
        if (!pauseInfo) return;

        const currentLang = localStorage.getItem('language') || 'ka';
        const langData = translations[currentLang];

        const pauseBtn = document.getElementById('pause-btn');
        const resumeBtn = document.getElementById('resume-btn');
        const pauseDaysRemaining = document.querySelector('.text-white[data-pause-display]');
        
        // Update remaining pause days display if element exists
        if (pauseDaysRemaining && pauseInfo.max_pause_days !== undefined) {
            // Handle NULL/null values properly - same logic as PHP
            let displayDays = pauseInfo.remaining_pause_days;
            
            // Check if the value is null-ish (null, "null", empty, etc.)
            if (displayDays === null || 
                displayDays === 'null' || 
                displayDays === 'NULL' || 
                displayDays === '' || 
                displayDays === undefined ||
                !Number.isInteger(Number(displayDays))) {
                displayDays = pauseInfo.max_pause_days; // Use max if null-ish
            }
            
            pauseDaysRemaining.textContent = displayDays + '/' + pauseInfo.max_pause_days;
        }

        // The UI is already handled by PHP, but we can update dynamic elements here
        console.log('Pause status:', pauseInfo.is_paused ? 'paused' : 'active');
    }

    async function pauseMembership() {
        const currentLang = localStorage.getItem('language') || 'ka';
        const langData = translations[currentLang];
        
        if (!confirm(langData.key_pause_confirm || 'Are you sure you want to pause your membership? (Minimum 1 day will be deducted from your pause allowance)')) {
            return;
        }
        
        try {
            const response = await fetch('pause_membership.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=request_pause&client_id=<?php echo $clientID ?? 0; ?>'
            });

            const data = await response.json();
            
            if (data.status === 'success') {
                alert(langData.key_pause_success || 'Membership paused successfully');
                location.reload(); // Refresh to update UI
            } else {
                alert(data.message || 'Failed to pause membership');
            }
        } catch (error) {
            console.error('Failed to pause membership:', error);
            alert('Error pausing membership');
        }
    }

    async function resumeMembership() {
        const currentLang = localStorage.getItem('language') || 'ka';
        const langData = translations[currentLang];
        
        if (!confirm(langData.key_resume_confirm || 'Are you sure you want to resume your membership?')) {
            return;
        }
        
        try {
            const response = await fetch('pause_membership.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=resume_membership&client_id=<?php echo $clientID ?? 0; ?>'
            });

            const data = await response.json();
            
            if (data.status === 'success') {
                const messageKey = data.message_key || 'key_resume_success';
                let message = langData[messageKey] || data.message || 'Membership resumed successfully';
                
                // Replace placeholders in the message if it's the capped version
                if (data.was_capped && message.includes('{days_paused}')) {
                    message = message
                        .replace('{days_paused}', data.days_paused)
                        .replace('{days_extended}', data.days_extended);
                }
                
                alert(message);
                location.reload(); // Refresh to update UI and end date
            } else {
                alert(data.message || 'Failed to resume membership');
            }
        } catch (error) {
            console.error('Failed to resume membership:', error);
            alert('Error resuming membership');
        }
    }

    // Event listeners for pause buttons
    const pauseBtn = document.getElementById('pause-btn');
    const resumeBtn = document.getElementById('resume-btn');
    
    if (pauseBtn) {
        pauseBtn.addEventListener('click', pauseMembership);
    }
    
    if (resumeBtn) {
        resumeBtn.addEventListener('click', resumeMembership);
    }

    // Load pause info on page load if pause section exists
    if (document.getElementById('pause-section')) {
        loadPauseInfo();
    }

    // --- Server Fetching Logic would go here ---
    /*
    async function fetchActiveUsers() {
        try {
            const response = await fetch('https://api.synergy-gym.ge/gym/active-users');
            if (!response.ok) throw new Error('Failed to fetch');
            const data = await response.json();
            const countElement = document.getElementById('active-users-count');
            countElement.textContent = data.count;
            // After updating the count, re-run the status text update
            const currentLang = localStorage.getItem('language') || 'ka';
            updateActiveUsersStatus(currentLang);
        } catch (error) {
            console.error('Failed to fetch active users:', error);
        }
    }
    
    // Fetch active users when the page loads
    fetchActiveUsers();
    
    // Optionally, fetch every 30 seconds to keep it live
    // setInterval(fetchActiveUsers, 30000);
    */
}); // Close DOMContentLoaded event listener

// ============================================
// GROUP WORKOUT BOOKING FUNCTIONALITY
// ============================================

console.log('=== GROUP WORKOUT CODE LOADED ===');
    
    let currentTab = 'available';
    let allSessions = [];
    let currentWeekStart = null;
    let selectedDate = null;
    
    console.log('Variables initialized:', { currentTab, allSessions, currentWeekStart, selectedDate });
    
    // Get start of week (Sunday)
    function getWeekStart(date) {
        const d = new Date(date);
        const day = d.getDay();
        const diff = d.getDate() - day;
        return new Date(d.setDate(diff));
    }
    
    // Change week
    window.changeWeek = function(delta) {
        if (!currentWeekStart) {
            currentWeekStart = getWeekStart(new Date());
        }
        currentWeekStart.setDate(currentWeekStart.getDate() + (delta * 7));
        renderWeekCalendar();
    }
    
    // Close day sessions view
    window.closeDaySessions = function() {
        document.getElementById('day-sessions-container').classList.add('hidden');
        selectedDate = null;
        // Remove selected class from all days
        document.querySelectorAll('.week-day-cell').forEach(cell => {
            cell.classList.remove('selected');
        });
    }
    
    // Render week calendar
    function renderWeekCalendar() {
        if (!currentWeekStart) {
            currentWeekStart = getWeekStart(new Date());
        }
        
        const weekEnd = new Date(currentWeekStart);
        weekEnd.setDate(weekEnd.getDate() + 6);
        
        const currentLang = localStorage.getItem('language') || 'ka';
        const t = translations[currentLang];
        
        // Update week range display
        const monthNames = [
            t.key_jan, t.key_feb, t.key_mar, t.key_apr, 
            t.key_may_short, t.key_jun, t.key_jul, t.key_aug, 
            t.key_sep, t.key_oct, t.key_nov, t.key_dec
        ];
        const rangeText = currentWeekStart.getMonth() === weekEnd.getMonth() 
            ? `${monthNames[currentWeekStart.getMonth()]} ${currentWeekStart.getDate()} - ${weekEnd.getDate()}, ${currentWeekStart.getFullYear()}`
            : `${monthNames[currentWeekStart.getMonth()]} ${currentWeekStart.getDate()} - ${monthNames[weekEnd.getMonth()]} ${weekEnd.getDate()}, ${currentWeekStart.getFullYear()}`;
        
        document.getElementById('week-range').textContent = rangeText;
        
        const weekGrid = document.getElementById('week-calendar-grid');
        weekGrid.innerHTML = '';
        
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        
        const dayNames = [
            t.key_sun, t.key_mon, t.key_tue, t.key_wed, 
            t.key_thu, t.key_fri, t.key_sat
        ];
        
        // Create 7 day cells for the week
        for (let i = 0; i < 7; i++) {
            const dayDate = new Date(currentWeekStart);
            dayDate.setDate(dayDate.getDate() + i);
            dayDate.setHours(0, 0, 0, 0);
            
            const dayCell = document.createElement('div');
            dayCell.className = 'week-day-cell';
            
            // Check if today
            if (dayDate.getTime() === today.getTime()) {
                dayCell.classList.add('today');
            }
            
            // Check if in the past
            if (dayDate < today) {
                dayCell.classList.add('inactive');
            }
            
            // Count sessions for this day
            const daySessions = getSessionsForDate(dayDate);
            if (daySessions.length > 0 && dayDate >= today) {
                dayCell.classList.add('has-sessions');
            }
            
            // Day name
            const dayName = document.createElement('div');
            dayName.className = 'week-day-name';
            dayName.textContent = dayNames[i];
            dayCell.appendChild(dayName);
            
            // Day number
            const dayNumber = document.createElement('div');
            dayNumber.className = 'week-day-number';
            dayNumber.textContent = dayDate.getDate();
            dayCell.appendChild(dayNumber);
            
            // Add click handler for days with sessions (with mobile touch support)
            if (daySessions.length > 0 && dayDate >= today) {
                dayCell.style.cursor = 'pointer';
                const clickHandler = (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    showDaySessions(dayDate, dayCell);
                };
                dayCell.onclick = clickHandler;
                dayCell.ontouchend = clickHandler;
            } else if (dayDate < today) {
                dayCell.style.cursor = 'default';
            }
            
            weekGrid.appendChild(dayCell);
        }
    }
    
    // Get sessions for a specific date
    function getSessionsForDate(date) {
        return allSessions.filter(session => {
            const sessionDate = new Date(session.start_time);
            sessionDate.setHours(0, 0, 0, 0);
            return sessionDate.getTime() === date.getTime();
        });
    }
    
    // Show sessions for a specific day
    function showDaySessions(date, cellElement) {
        console.log('showDaySessions called for date:', date);
        const sessions = getSessionsForDate(date);
        console.log('Found sessions:', sessions.length);
        if (sessions.length === 0) return;
        
        selectedDate = date;
        
        // Remove selected class from all cells
        document.querySelectorAll('.week-day-cell').forEach(cell => {
            cell.classList.remove('selected');
        });
        
        // Add selected class to clicked cell
        if (cellElement) {
            cellElement.classList.add('selected');
        }
        
        const currentLang = localStorage.getItem('language') || 'ka';
        const t = translations[currentLang];
        
        const monthNames = [
            t.key_january, t.key_february, t.key_march, t.key_april, 
            t.key_may, t.key_june, t.key_july, t.key_august, 
            t.key_september, t.key_october, t.key_november, t.key_december
        ];
        const dayNames = [
            t.key_sunday, t.key_monday, t.key_tuesday, t.key_wednesday, 
            t.key_thursday, t.key_friday, t.key_saturday
        ];
        
        // Set day title
        document.getElementById('selected-day-title').textContent = 
            `${dayNames[date.getDay()]}, ${monthNames[date.getMonth()]} ${date.getDate()}`;
        
        // Populate sessions list
        const daySessionsList = document.getElementById('day-sessions-list');
        daySessionsList.innerHTML = sessions.map(session => {
            const startTime = new Date(session.start_time);
            const endTime = new Date(session.end_time);
            const timeStr = `${startTime.getHours().toString().padStart(2, '0')}:${startTime.getMinutes().toString().padStart(2, '0')} - ${endTime.getHours().toString().padStart(2, '0')}:${endTime.getMinutes().toString().padStart(2, '0')}`;
            
            const availableSpots = session.available_spots;
            const isFull = session.is_full;
            const isBooked = session.is_booked;
            
            return `
                <div class="workout-session-card">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <h4 class="text-lg font-semibold text-white mb-1">${session.title}</h4>
                            <p class="text-sm text-gray-400">${session.group_name}</p>
                        </div>
                        ${isFull ? `<span class="workout-badge full">${t.key_full}</span>` : ''}
                        ${isBooked ? `<span class="workout-badge" style="background-color: rgba(34, 197, 94, 0.2); color: #22c55e;">${t.key_booked}</span>` : ''}
                    </div>
                    
                    <div class="grid grid-cols-2 gap-2 text-sm mb-3">
                        <div class="flex items-center text-gray-400">
                            <i class="fas fa-clock mr-2 text-yellow-400"></i>
                            <span>${timeStr}</span>
                        </div>
                        <div class="flex items-center text-gray-400">
                            <i class="fas fa-user mr-2 text-yellow-400"></i>
                            <span>${session.trainer_name}</span>
                        </div>
                        <div class="flex items-center text-gray-400">
                            <i class="fas fa-map-marker-alt mr-2 text-yellow-400"></i>
                            <span>${session.location}</span>
                        </div>
                        <div class="flex items-center text-gray-400">
                            <i class="fas fa-users mr-2 text-yellow-400"></i>
                            <span>${availableSpots} ${t.key_spots_left}</span>
                        </div>
                    </div>
                    
                    <div class="flex gap-2">
                        ${isBooked 
                          ? `<button onclick="cancelBooking(${session.session_id})" class="workout-cancel-btn">
                                <i class="fas fa-times mr-1"></i> ${t.key_cancel_booking}
                             </button>`
                          : `<button onclick="bookSession(${session.session_id})" 
                                class="workout-book-btn" 
                                ${isFull ? 'disabled' : ''}>
                                <i class="fas fa-check mr-1"></i> ${isFull ? t.key_full : t.key_book_now}
                             </button>`
                        }
                    </div>
                </div>
            `;
        }).join('');
        
        // Show day sessions container
        const daySessionsContainer = document.getElementById('day-sessions-container');
        daySessionsContainer.classList.remove('hidden');
        
        // Scroll to sessions on mobile for better visibility
        setTimeout(() => {
            daySessionsContainer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }, 100);
    }
    
    // Switch between tabs
    window.switchWorkoutTab = function(tab) {
        currentTab = tab;
        
        // Update tab buttons
        document.getElementById('tab-available').classList.toggle('active', tab === 'available');
        document.getElementById('tab-booked').classList.toggle('active', tab === 'booked');
        
        // Update tab content
        document.getElementById('available-sessions-container').classList.toggle('hidden', tab !== 'available');
        document.getElementById('my-bookings-container').classList.toggle('hidden', tab !== 'booked');
        
        // Load data for the selected tab
        if (tab === 'available') {
            loadAvailableSessions();
        } else {
            loadMyBookings();
        }
    }
    
    // Format date and time
    function formatDateTime(dateTimeString) {
        const dt = new Date(dateTimeString);
        const currentLang = localStorage.getItem('language') || 'ka';
        const t = translations[currentLang];
        
        const days = [
            t.key_sun, t.key_mon, t.key_tue, t.key_wed, 
            t.key_thu, t.key_fri, t.key_sat
        ];
        const months = [
            t.key_jan, t.key_feb, t.key_mar, t.key_apr, 
            t.key_may_short, t.key_jun, t.key_jul, t.key_aug, 
            t.key_sep, t.key_oct, t.key_nov, t.key_dec
        ];
        
        const dayName = days[dt.getDay()];
        const day = dt.getDate();
        const month = months[dt.getMonth()];
        const hours = dt.getHours().toString().padStart(2, '0');
        const minutes = dt.getMinutes().toString().padStart(2, '0');
        
        return {
            date: `${dayName}, ${month} ${day}`,
            time: `${hours}:${minutes}`
        };
    }
    
    // Load available sessions
    async function loadAvailableSessions() {
        const loadingEl = document.getElementById('available-sessions-loading');
        const emptyEl = document.getElementById('available-sessions-empty');
        
        loadingEl.classList.remove('hidden');
        emptyEl.classList.add('hidden');
        
        console.log('Loading available sessions...');
        console.log('API URL:', window.location.origin + window.location.pathname.replace('profilepage.php', 'group_workout_api.php'));
        
        try {
            const response = await fetch('group_workout_api.php?action=get_available_sessions', {
                method: 'GET',
                credentials: 'same-origin', // Include cookies/session
                headers: {
                    'Accept': 'application/json'
                }
            });
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);
            
            const data = await response.json();
            console.log('API Response:', data);
            
            loadingEl.classList.add('hidden');
            
            if (data.success && data.sessions.length > 0) {
                allSessions = data.sessions;
                console.log('Sessions loaded:', allSessions.length);
                renderWeekCalendar();
            } else {
                console.log('No sessions or API returned error:', data.message || 'No sessions');
                allSessions = [];
                emptyEl.classList.remove('hidden');
            }
        } catch (error) {
            console.error('Error loading sessions:', error);
            console.error('Error details:', error.message);
            loadingEl.classList.add('hidden');
            emptyEl.classList.remove('hidden');
        }
    }
    
    // Load my bookings
    async function loadMyBookings() {
        const loadingEl = document.getElementById('my-bookings-loading');
        const listEl = document.getElementById('my-bookings-list');
        const emptyEl = document.getElementById('my-bookings-empty');
        
        loadingEl.classList.remove('hidden');
        listEl.classList.add('hidden');
        emptyEl.classList.add('hidden');
        
        const currentLang = localStorage.getItem('language') || 'ka';
        const t = translations[currentLang];
        
        try {
            const response = await fetch('group_workout_api.php?action=get_my_bookings', {
                method: 'GET',
                credentials: 'same-origin', // Include cookies/session
                headers: {
                    'Accept': 'application/json'
                }
            });
            const data = await response.json();
            
            loadingEl.classList.add('hidden');
            
            if (data.success && data.bookings.length > 0) {
                listEl.innerHTML = '';
                data.bookings.forEach(booking => {
                    const dt = formatDateTime(booking.start_time);
                    const bookedDt = formatDateTime(booking.booked_at);
                    
                    const card = document.createElement('div');
                    card.className = 'workout-session-card';
                    card.innerHTML = `
                        <div class="flex justify-between items-start mb-3">
                            <div class="flex-1">
                                <h4 class="text-white font-bold text-lg">${booking.title}</h4>
                                <p class="text-gray-400 text-sm">${booking.group_name}</p>
                            </div>
                            <span class="workout-badge booked"><i class="fas fa-check mr-1"></i>${t.key_confirmed}</span>
                        </div>
                        <div class="grid grid-cols-2 gap-2 text-sm mb-3">
                            <div class="flex items-center text-gray-300">
                                <i class="fas fa-calendar-alt mr-2 text-yellow-400"></i>
                                <span>${dt.date}</span>
                            </div>
                            <div class="flex items-center text-gray-300">
                                <i class="fas fa-clock mr-2 text-yellow-400"></i>
                                <span>${dt.time}</span>
                            </div>
                            <div class="flex items-center text-gray-300">
                                <i class="fas fa-user mr-2 text-yellow-400"></i>
                                <span>${booking.trainer_name}</span>
                            </div>
                            <div class="flex items-center text-gray-300">
                                <i class="fas fa-calendar-check mr-2 text-yellow-400"></i>
                                <span>${t.key_booked_on} ${bookedDt.date}</span>
                            </div>
                        </div>
                        <div class="flex justify-end">
                            <button onclick="cancelBooking(${booking.session_id})" class="workout-cancel-btn">
                                <i class="fas fa-times mr-1"></i>${t.key_cancel_booking}
                            </button>
                        </div>
                    `;
                    listEl.appendChild(card);
                });
                listEl.classList.remove('hidden');
            } else {
                emptyEl.classList.remove('hidden');
            }
        } catch (error) {
            console.error('Error loading bookings:', error);
            loadingEl.classList.add('hidden');
            emptyEl.classList.remove('hidden');
        }
    }
    
    // Book a session
    window.bookSession = async function(sessionId) {
        const currentLang = localStorage.getItem('language') || 'ka';
        const t = translations[currentLang];
        
        if (!confirm('Are you sure you want to book this session?')) {
            return;
        }
        
        // Find and disable the button to prevent double-clicks
        const button = event?.target?.closest('button');
        if (button) {
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Booking...';
        }
        
        try {
            const formData = new FormData();
            formData.append('action', 'book_session');
            formData.append('session_id', sessionId);
            
            const response = await fetch('group_workout_api.php', {
                method: 'POST',
                credentials: 'same-origin', // Include cookies/session
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                alert('Booking successful!');
                // Reload sessions and re-render the week
                await loadAvailableSessions();
                // Re-show the selected day if one was selected
                if (selectedDate) {
                    showDaySessions(selectedDate, null);
                }
            } else {
                // Re-enable button on error
                if (button) {
                    button.disabled = false;
                    button.innerHTML = `<i class="fas fa-check mr-1"></i> ${t.key_book_now}`;
                }
                alert(data.message || 'Booking failed');
            }
        } catch (error) {
            // Re-enable button on error
            if (button) {
                button.disabled = false;
                button.innerHTML = `<i class="fas fa-check mr-1"></i> ${t.key_book_now}`;
            }
            console.error('Error booking session:', error);
            alert('An error occurred while booking');
        }
    }
    
    // Cancel a booking
    window.cancelBooking = async function(sessionId) {
        if (!confirm('Are you sure you want to cancel this booking?')) {
            return;
        }
        
        try {
            const formData = new FormData();
            formData.append('action', 'cancel_booking');
            formData.append('session_id', sessionId);
            
            const response = await fetch('group_workout_api.php', {
                method: 'POST',
                credentials: 'same-origin', // Include cookies/session
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                alert('Booking cancelled successfully');
                // Reload sessions and re-render the week
                await loadAvailableSessions();
                // Also reload My Bookings if that tab is active
                if (currentTab === 'booked') {
                    await loadMyBookings();
                }
                // Re-show the selected day if one was selected
                if (selectedDate) {
                    showDaySessions(selectedDate, null);
                }
            } else {
                alert(data.message || 'Cancellation failed');
            }
        } catch (error) {
            console.error('Error cancelling booking:', error);
            alert('An error occurred while cancelling');
        }
    }
    
    // Initialize workout sessions on page load
    // This runs after all functions are defined for better compatibility
    console.log('Scheduling workout session initialization...');
    console.log('loadAvailableSessions function exists?', typeof loadAvailableSessions);
    console.log('allSessions variable exists?', typeof allSessions);
    
    if (document.readyState === 'loading') {
        // Still loading, wait for DOMContentLoaded
        console.log('Document still loading, waiting for DOMContentLoaded');
        document.addEventListener('DOMContentLoaded', () => {
            console.log('DOM loaded - initializing workout sessions');
            setTimeout(() => {
                console.log('Calling loadAvailableSessions...');
                loadAvailableSessions();
            }, 100);
        });
    } else {
        // DOM already loaded, initialize immediately
        console.log('DOM already loaded, readyState:', document.readyState);
        setTimeout(() => {
            console.log('Calling loadAvailableSessions immediately...');
            loadAvailableSessions();
        }, 100);
    }

    // Also try on window load as fallback
    window.addEventListener('load', () => {
        console.log('Window fully loaded - ensuring workout sessions are initialized');
        console.log('allSessions.length:', allSessions.length);
        // Only reload if sessions haven't been loaded yet
        if (allSessions.length === 0) {
            console.log('No sessions loaded yet, calling loadAvailableSessions...');
            loadAvailableSessions();
        } else {
            console.log('Sessions already loaded, skipping...');
        }
    });
</script>

</body>
</html>