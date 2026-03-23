<?php
include '../mssql_connection.php';

header('Content-Type: application/json');

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Define eligible package IDs for pause functionality with their maximum pause days
$packagePauseDays = [
    // 60-day pause allowance packages
    1237 => 60, // Package 1237
    1317 => 60, // 1 წლიანი - 1 year 299
    1321 => 60, // Package 1321
    1328 => 60, // Package 1328
    1329 => 60, // Package 1329
    1330 => 60, // Package 1330
    1335 => 60, // Package 1335
    1337 => 60, // Package 1337
    1346 => 60, // Package 1346
    1349 => 60, // Package 1349
    1350 => 60, // Package 1350
    1357 => 60, // Package 1357
    1358 => 60, // Package 1358
    1370 => 60, // Package 1370
    1376 => 60, // Package 1376
    1379 => 60, // Package 1379
    1380 => 60, // Package 1380
    1381 => 60, // Package 1381
    1383 => 60, // Package 1383
    1384 => 60, // Package 1384
    1385 => 60, // Package 1385
    1386 => 60, // Package 1386
    1387 => 60, // Package 1387
    1388 => 60, // Package 1388
    1396 => 60, // Package 1396
    
    // 30-day pause allowance packages
    1205 => 30, // Package 1205
    1280 => 30, // Package 1280
    1283 => 30, // Package 1283
    1303 => 30, // Package 1303
    1332 => 30, // Package 1332
    1333 => 30, // Package 1333
    1334 => 30, // Package 1334
    1342 => 30, // Package 1342
    1354 => 30, // Package 1354
    1359 => 30, // Package 1359
    1360 => 30, // Package 1360
    1365 => 30, // Package 1365
    1371 => 30, // Package 1371
    1372 => 30, // Package 1372
    1409 => 30  // Package 1409
];

// Get all eligible package IDs
$eligiblePackageIDs = array_keys($packagePauseDays);

// Helper function to get maximum pause days for a package
function getMaxPauseDays($packageID) {
    global $packagePauseDays;
    return $packagePauseDays[$packageID] ?? 60; // Default to 60 if not found
}

if (!$mssqlconn) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    $username = $_POST['username'] ?? '';
    $client_id = $_POST['client_id'] ?? '';
    
    // Debug logging
    error_log("DEBUG: Received POST data: " . print_r($_POST, true));
    error_log("DEBUG: Action: '$action', Username: '$username', Client ID: '$client_id'");
    
    if (empty($action) || (empty($username) && empty($client_id))) {
        echo json_encode([
            'status' => 'error', 
            'message' => 'Missing required parameters (need action and either username or client_id)',
            'debug' => [
                'action' => $action,
                'username' => $username,
                'client_id' => $client_id,
                'post_data' => $_POST
            ]
        ]);
        exit;
    }
    
    // Use client_id if provided, otherwise use username
    $identifier = !empty($client_id) ? $client_id : $username;
    $use_client_id = !empty($client_id);
    
    switch ($action) {
        case 'get_pause_info':
            getPauseInfo($mssqlconn, $identifier, $use_client_id);
            break;
        case 'request_pause':
            requestPause($mssqlconn, $identifier, $use_client_id);
            break;
        case 'resume_membership':
            resumeMembership($mssqlconn, $identifier, $use_client_id);
            break;
        default:
            echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
            break;
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Only POST method allowed']);
}

function getPauseInfo($mssqlconn, $identifier, $use_client_id = false) {
    global $eligiblePackageIDs;
    
    // Trim whitespace from identifier
    $identifier = trim($identifier);
    
    // Build query based on whether we're using client_id or username
    if ($use_client_id) {
        $whereClause = "sp.ClientID = ?";
    } else {
        $whereClause = "sp.ClientID = (SELECT ID FROM Clients WHERE TRIM(FullName) = ?)";
    }
    
    // Create IN clause for eligible package IDs
    $packageIdsList = implode(',', $eligiblePackageIDs);
    
    $query = "SELECT sp.*, p.Name AS PackageName 
              FROM SoldPackages sp 
              LEFT JOIN Packages p ON sp.PackageID = p.ID 
              WHERE $whereClause AND sp.PackageID IN ($packageIdsList) AND sp.Expired = 0 AND sp.EndDate > GETDATE()
              ORDER BY sp.RecordDate DESC";
    
    $stmt = sqlsrv_prepare($mssqlconn, $query, array($identifier));
    
    if (!$stmt) {
        echo json_encode(['status' => 'error', 'message' => 'Query preparation failed']);
        return;
    }
    
    if (!sqlsrv_execute($stmt)) {
        $errors = sqlsrv_errors();
        echo json_encode(['status' => 'error', 'message' => 'Query execution failed', 'sql_errors' => $errors]);
        return;
    }
    
    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    
    if ($row) {
        $pauseStartDate = $row['PauseStartDate'] ? $row['PauseStartDate']->format('Y-m-d') : null;
        $pauseEndDate = $row['PauseEndDate'] ? $row['PauseEndDate']->format('Y-m-d') : null;
        $endDate = $row['EndDate'] ? $row['EndDate']->format('Y-m-d') : null;
        $maxPauseDays = getMaxPauseDays($row['PackageID']);
        
        // Handle Remainingpause - this represents days CONSUMED, not days remaining
        $daysConsumed = $row['Remainingpause'] ?? 0; // Default to 0 if null
        $daysRemainingAvailable = max(0, $maxPauseDays - $daysConsumed);
        
        echo json_encode([
            'status' => 'success',
            'is_paused' => ($row['PauseStatus'] == 1),
            'pause_status' => $row['PauseStatus'],
            'pause_start_date' => $pauseStartDate,
            'pause_end_date' => $pauseEndDate,
            'end_date' => $endDate,
            'remaining_pause_days' => $daysRemainingAvailable, // Days still available to use
            'days_consumed' => $daysConsumed, // Days already consumed
            'max_pause_days' => $maxPauseDays,
            'package_name' => $row['PackageName'],
            'package_id' => $row['PackageID']
        ]);
    } else {
        echo json_encode([
            'status' => 'success',
            'is_paused' => false,
            'message' => 'No package found for this user'
        ]);
    }
    
    sqlsrv_free_stmt($stmt);
}

function requestPause($mssqlconn, $identifier, $use_client_id = false) {
    global $eligiblePackageIDs;
    
    // Trim whitespace from identifier
    $identifier = trim($identifier);
    
    // Build query based on whether we're using client_id or username
    if ($use_client_id) {
        $whereClause = "sp.ClientID = ?";
    } else {
        $whereClause = "sp.ClientID = (SELECT ID FROM Clients WHERE TRIM(FullName) = ?)";
    }
    
    // Create IN clause for eligible package IDs
    $packageIdsList = implode(',', $eligiblePackageIDs);
    
    // Check if user has eligible package
    $checkQuery = "SELECT TOP 1 sp.*, p.Name AS PackageName 
                   FROM SoldPackages sp 
                   LEFT JOIN Packages p ON sp.PackageID = p.ID 
                   WHERE $whereClause AND sp.PackageID IN ($packageIdsList) AND sp.Expired = 0 AND sp.EndDate > GETDATE()
                   ORDER BY sp.RecordDate DESC";
    
    $checkStmt = sqlsrv_prepare($mssqlconn, $checkQuery, array($identifier));
    
    if (!$checkStmt || !sqlsrv_execute($checkStmt)) {
        // Add more debug info
        $errors = sqlsrv_errors();
        echo json_encode([
            'status' => 'error', 
            'message' => 'Failed to check package eligibility',
            'debug' => [
                'identifier' => $identifier,
                'use_client_id' => $use_client_id,
                'sql_errors' => $errors
            ]
        ]);
        return;
    }
    
    $packageRow = sqlsrv_fetch_array($checkStmt, SQLSRV_FETCH_ASSOC);
    
    if (!$packageRow) {
        // Debug: Let's see what we actually found
        $debugQuery = "SELECT c.ID, c.FullName, sp.PackageID, sp.Expired, sp.PauseStatus, sp.Remainingpause
                       FROM Clients c
                       LEFT JOIN SoldPackages sp ON c.ID = sp.ClientID
                       WHERE TRIM(c.FullName) = ?
                       ORDER BY sp.RecordDate DESC";
        
        $debugStmt = sqlsrv_prepare($mssqlconn, $debugQuery, array($username));
        
        $debugInfo = [];
        if ($debugStmt && sqlsrv_execute($debugStmt)) {
            while ($debugRow = sqlsrv_fetch_array($debugStmt, SQLSRV_FETCH_ASSOC)) {
                $debugInfo[] = $debugRow;
            }
        }
        
        echo json_encode([
            'status' => 'error', 
            'message' => 'No eligible package found for pause',
            'debug' => [
                'identifier' => $identifier,
                'use_client_id' => $use_client_id,
                'packages_found' => $debugInfo
            ]
        ]);
        return;
    }
    
    // Check if already paused
    if ($packageRow['PauseStatus'] == 1) {
        echo json_encode(['status' => 'error', 'message' => 'Membership is already paused']);
        return;
    }
    
    // Check remaining pause days available (max - consumed)
    $currentPackageID = $packageRow['PackageID'];
    $maxPauseDays = getMaxPauseDays($currentPackageID);
    $daysConsumed = $packageRow['Remainingpause'] ?? 0; // Days already consumed
    $daysAvailable = $maxPauseDays - $daysConsumed; // Days still available
    
    if ($daysAvailable <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'No pause days remaining']);
        return;
    }
    
    // Start pause - no days deducted until resume
    $pauseStartDate = date('Y-m-d');
    
    // Only update PauseStatus and PauseStartDate when pausing
    // Remainingpause (days consumed) will be calculated when resuming as PauseEndDate - PauseStartDate
    $updateFields = "PauseStatus = 1, PauseStartDate = ?";
    $updateParams = [$pauseStartDate, $identifier, $currentPackageID];
    
    // Build update query based on identifier type
    if ($use_client_id) {
        $updateWhereClause = "ClientID = ?";
    } else {
        $updateWhereClause = "ClientID = (SELECT ID FROM Clients WHERE TRIM(FullName) = ?)";
    }
    
    $updateQuery = "UPDATE SoldPackages 
                    SET $updateFields 
                    WHERE $updateWhereClause AND PackageID = ? AND Expired = 0 AND EndDate > GETDATE()";
    
    $updateStmt = sqlsrv_prepare($mssqlconn, $updateQuery, $updateParams);
    
    if (!$updateStmt || !sqlsrv_execute($updateStmt)) {
        echo json_encode(['status' => 'error', 'message' => 'Failed to pause membership']);
        return;
    }
    
    // Block gym access by setting activeindevice = false in clientcards table
    $clientIDForCard = $use_client_id ? $identifier : $packageRow['ClientID'];
    $cardUpdateQuery = "UPDATE clientcards SET activeindevice = 0 WHERE ClientID = ?";
    $cardUpdateStmt = sqlsrv_prepare($mssqlconn, $cardUpdateQuery, array($clientIDForCard));
    
    if (!$cardUpdateStmt || !sqlsrv_execute($cardUpdateStmt)) {
        // Log the error but don't fail the pause operation
        error_log("Warning: Failed to update clientcards for pause - Client ID: $clientIDForCard");
    }
    
    echo json_encode([
        'status' => 'success',
        'message' => 'Membership paused successfully',
        'pause_start_date' => $pauseStartDate,
        'remaining_pause_days' => $daysAvailable, // Days still available to use
        'max_pause_days' => $maxPauseDays,
        'package_id' => $currentPackageID,
        'note' => 'Days consumed will be calculated based on actual pause duration when you resume'
    ]);
    
    sqlsrv_free_stmt($checkStmt);
    sqlsrv_free_stmt($updateStmt);
}

function resumeMembership($mssqlconn, $identifier, $use_client_id = false) {
    global $eligiblePackageIDs;
    
    // Trim whitespace from identifier
    $identifier = trim($identifier);
    
    // Build query based on whether we're using client_id or username
    if ($use_client_id) {
        $whereClause = "ClientID = ?";
    } else {
        $whereClause = "ClientID = (SELECT ID FROM Clients WHERE TRIM(FullName) = ?)";
    }
    
    // Create IN clause for eligible package IDs
    $packageIdsList = implode(',', $eligiblePackageIDs);
    
    // Get current pause info - allow resuming even if membership expired during pause
    $checkQuery = "SELECT * FROM SoldPackages 
                   WHERE $whereClause AND PackageID IN ($packageIdsList) AND PauseStatus = 1 AND Expired = 0
                   ORDER BY RecordDate DESC";
    
    $checkStmt = sqlsrv_prepare($mssqlconn, $checkQuery, array($identifier));
    
    if (!$checkStmt || !sqlsrv_execute($checkStmt)) {
        echo json_encode(['status' => 'error', 'message' => 'Failed to check pause status']);
        return;
    }
    
    $pauseRow = sqlsrv_fetch_array($checkStmt, SQLSRV_FETCH_ASSOC);
    
    if (!$pauseRow) {
        echo json_encode(['status' => 'error', 'message' => 'No active pause found']);
        return;
    }
    
    // Calculate days paused in this session - only count days while membership was valid
    $pauseStartDate = $pauseRow['PauseStartDate'];
    $originalEndDate = $pauseRow['EndDate'];
    $currentDate = new DateTime();
    $currentPackageID = $pauseRow['PackageID'];
    $maxPauseDays = getMaxPauseDays($currentPackageID);
    
    // Only count pause days while membership was still valid
    $effectivePauseEndDate = min($originalEndDate, $currentDate);
    $daysPausedThisSession = $pauseStartDate->diff($effectivePauseEndDate)->days;
    
    // Enforce minimum 1 day pause - ensure at least 1 day is extended and counted
    $daysPausedThisSession = max(1, $daysPausedThisSession);
    
    // Get previously consumed days
    $previouslyConsumed = $pauseRow['Remainingpause'] ?? 0;
    
    // Calculate total days that would be consumed after this pause session
    $totalDaysConsumed = $previouslyConsumed + $daysPausedThisSession;
    
    // Check if user exceeds their total allowance
    $wasCapped = ($totalDaysConsumed > $maxPauseDays);
    
    if ($wasCapped) {
        // Cap the total consumed days to the maximum allowed
        $totalDaysConsumed = $maxPauseDays;
        $daysPausedThisSession = $maxPauseDays - $previouslyConsumed;
        $daysPausedThisSession = max(1, $daysPausedThisSession); // Ensure at least 1 day
        
        // Log this for admin review
        $totalCalendarDays = $pauseStartDate->diff($currentDate)->days;
        error_log("User exceeded pause allowance: Package ID {$currentPackageID}, Total calendar days: {$totalCalendarDays}, Valid pause days: {$effectivePauseEndDate->diff($pauseStartDate)->days}, Max allowed total: {$maxPauseDays}, Capping to: {$daysPausedThisSession} days");
    }
    
    // Days to extend membership = days paused this session (capped if necessary)
    $daysToExtend = $daysPausedThisSession;
    
    // Calculate new end date (extend by days paused this session)
    $currentEndDate = $pauseRow['EndDate'];
    $newEndDate = clone $currentEndDate;
    $newEndDate->add(new DateInterval('P' . $daysToExtend . 'D'));
    
    // Special handling: If membership expired during pause, extend from today instead
    $today = new DateTime();
    if ($currentEndDate < $today) {
        // Membership expired during pause - extend from today to ensure future validity
        $newEndDate = clone $today;
        $newEndDate->add(new DateInterval('P' . $daysToExtend . 'D'));
        
        error_log("Membership expired during pause: Client ID {$identifier}, Original EndDate: " . $currentEndDate->format('Y-m-d') . ", New EndDate: " . $newEndDate->format('Y-m-d'));
    }
    
    $resumeDate = date('Y-m-d');
    
    // Update membership - set Remainingpause to total days consumed
    $updateQuery = "UPDATE SoldPackages 
                    SET PauseStatus = 0, 
                        PauseEndDate = ?, 
                        Remainingpause = ?, 
                        EndDate = ? 
                    WHERE $whereClause AND PackageID = ? AND PauseStatus = 1 AND Expired = 0";
    
    $updateStmt = sqlsrv_prepare($mssqlconn, $updateQuery, array(
        $resumeDate,
        $totalDaysConsumed, // Total days consumed (this session + previous)
        $newEndDate->format('Y-m-d'),
        $identifier,
        $currentPackageID
    ));
    
    if (!$updateStmt || !sqlsrv_execute($updateStmt)) {
        echo json_encode(['status' => 'error', 'message' => 'Failed to resume membership']);
        return;
    }
    
    // Restore gym access by setting activeindevice = true in clientcards table
    $clientIDForCard = $use_client_id ? $identifier : $pauseRow['ClientID'];
    $cardUpdateQuery = "UPDATE clientcards SET activeindevice = 1 WHERE ClientID = ?";
    $cardUpdateStmt = sqlsrv_prepare($mssqlconn, $cardUpdateQuery, array($clientIDForCard));
    
    if (!$cardUpdateStmt || !sqlsrv_execute($cardUpdateStmt)) {
        // Log the error but don't fail the resume operation
        error_log("Warning: Failed to update clientcards for resume - Client ID: $clientIDForCard");
    }
    
    // Calculate days still available for future pauses
    $daysRemainingAvailable = max(0, $maxPauseDays - $totalDaysConsumed);
    
    // Check if membership expired during pause
    $membershipExpiredDuringPause = $pauseRow['EndDate'] < $today;
    
    // Calculate total calendar days for user information
    $totalCalendarDays = $pauseStartDate->diff($currentDate)->days;
    
    $successMessage = 'Membership resumed successfully';
    if ($membershipExpiredDuringPause) {
        $successMessage .= ". Note: Your membership expired during the pause, so only {$daysPausedThisSession} valid pause days (out of {$totalCalendarDays} total days) were added, and membership has been extended from today.";
    }
    if ($wasCapped) {
        $successMessage = "Membership resumed successfully. Note: You were paused for {$totalCalendarDays} calendar days, but only {$daysToExtend} days were added to your membership (your package limit).";
        if ($membershipExpiredDuringPause) {
            $successMessage .= " Your membership expired during the pause, so membership has been extended from today.";
        }
    }
    
    echo json_encode([
        'status' => 'success',
        'message' => $successMessage,
        'message_key' => $wasCapped ? 'key_resume_success_capped' : 'key_resume_success',
        'days_paused_this_session' => $daysPausedThisSession, // Valid pause days only
        'total_calendar_days' => $totalCalendarDays, // Total days from start to resume
        'days_extended' => $daysToExtend,
        'total_days_consumed' => $totalDaysConsumed,
        'days_remaining_available' => $daysRemainingAvailable,
        'new_end_date' => $newEndDate->format('Y-m-d'),
        'was_capped' => $wasCapped,
        'membership_expired_during_pause' => $membershipExpiredDuringPause
    ]);
    
    sqlsrv_free_stmt($checkStmt);
    sqlsrv_free_stmt($updateStmt);
}

sqlsrv_close($mssqlconn);
?>
