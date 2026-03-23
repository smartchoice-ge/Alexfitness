<?php
// Prevent any output before JSON
ob_start();
error_reporting(E_ERROR); // Only show fatal errors
ini_set('display_errors', 0); // Don't display errors

session_start();

// Include MSSQL connection
try {
    require_once '../mssql_connection.php';
    
    if (!$mssqlconn) {
        throw new Exception("MSSQL connection failed");
    }
} catch (Exception $e) {
    if (ob_get_level()) {
        ob_clean();
    }
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Database connection failed']);
    exit;
}

// Clear any output that might have been generated
if (ob_get_level()) {
    ob_clean();
}

// Check if admin is logged in
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header('Content-Type: application/json');
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

try {
    // Get last user check timestamp from session
    $lastCheck = $_SESSION['last_user_check'] ?? date('Y-m-d H:i:s', strtotime('-1 hour'));
    
    // Get list of already notified user IDs
    $notifiedUsers = $_SESSION['notified_users'] ?? [];
    
    // Build the exclude clause for notified users
    $excludeClause = "";
    $params = [$lastCheck];
    if (!empty($notifiedUsers)) {
        $placeholders = implode(',', array_fill(0, count($notifiedUsers), '?'));
        $excludeClause = " AND id NOT IN ($placeholders)";
        $params = array_merge($params, $notifiedUsers);
    }
    
    // Query for new users count since last check
    $sql = "SELECT COUNT(*) as new_count, MAX(agreement_date) as latest_user
            FROM ClientDetailsWebsite 
            WHERE agreement_date > ? AND agreed = 1" . $excludeClause;
    
    $stmt = sqlsrv_query($mssqlconn, $sql, $params);
    if ($stmt === false) {
        throw new Exception("Query failed: " . print_r(sqlsrv_errors(), true));
    }
    $data = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    sqlsrv_free_stmt($stmt);
    
    // Convert DateTime if needed
    if ($data['latest_user'] instanceof DateTime) {
        $data['latest_user'] = $data['latest_user']->format('Y-m-d H:i:s');
    }
    
    // Get details of new users for better notification
    $newUsers = [];
    $newUserIds = [];
    if ($data['new_count'] > 0) {
        $detailSql = "SELECT TOP 5 id, full_name, mobile_number, agreement_date, id_number 
                     FROM ClientDetailsWebsite 
                     WHERE agreement_date > ? AND agreed = 1" . $excludeClause . "
                     ORDER BY agreement_date DESC";
        
        $detailStmt = sqlsrv_query($mssqlconn, $detailSql, $params);
        if ($detailStmt) {
            while ($user = sqlsrv_fetch_array($detailStmt, SQLSRV_FETCH_ASSOC)) {
                // Convert DateTime objects
                if ($user['agreement_date'] instanceof DateTime) {
                    $user['agreement_date'] = $user['agreement_date']->format('Y-m-d H:i:s');
                }
                $newUsers[] = $user;
                $newUserIds[] = $user['id'];
            }
            sqlsrv_free_stmt($detailStmt);
        }
        
        // Add new user IDs to the notified list
        $_SESSION['notified_users'] = array_merge($notifiedUsers, $newUserIds);
        
        // Keep only last 100 notified users to prevent session from growing too large
        if (count($_SESSION['notified_users']) > 100) {
            $_SESSION['notified_users'] = array_slice($_SESSION['notified_users'], -100);
        }
    }
    
    // Update last check time only if we found new users
    if ($data['new_count'] > 0) {
        $_SESSION['last_user_check'] = date('Y-m-d H:i:s');
    }
    
    // Return response
    $response = [
        'success' => true,
        'new_users' => intval($data['new_count']),
        'latest_user_time' => $data['latest_user'],
        'user_details' => $newUsers,
        'timestamp' => date('Y-m-d H:i:s'),
        'debug_info' => [
            'last_check' => $lastCheck,
            'notified_before' => $notifiedUsers,
            'notified_after' => $_SESSION['notified_users'],
            'new_user_ids' => $newUserIds ?? []
        ]
    ];
    
    // Clean any unwanted output and send JSON
    if (ob_get_level()) {
        ob_clean();
    }
    echo json_encode($response);
    
} catch (Exception $e) {
    // Clean any unwanted output and send error JSON
    if (ob_get_level()) {
        ob_clean();
    }
    error_log("User check error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Failed to check users: ' . $e->getMessage()
    ]);
}

// End output buffering
if (ob_get_level()) {
    ob_end_flush();
}
?>
