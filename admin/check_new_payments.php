<?php
// Disable error reporting to prevent HTML output
error_reporting(0);
ini_set('display_errors', 0);

// Start output buffering to catch any unexpected output
ob_start();

session_start();

// Set JSON headers immediately
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

// Include database connections
include '../mssql_connection.php';
include '../mssql_packages_payments_helper.php';

// Check if admin is logged in
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    // Clear any buffered output
    ob_clean();
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

// Check database connection
if (!isset($mssqlconn)) {
    ob_clean();
    echo json_encode(['success' => false, 'error' => 'Database connection failed']);
    exit;
}

try {
    // Get last payment check timestamp from session
    $lastCheck = isset($_SESSION['last_payment_check']) ? $_SESSION['last_payment_check'] : date('Y-m-d H:i:s', strtotime('-1 hour'));
    
    // Get list of already notified payment IDs
    $notifiedPayments = isset($_SESSION['notified_payments']) ? $_SESSION['notified_payments'] : [];
    
    // Query for new payments since last check using MSSQL
    $sql = "SELECT COUNT(*) as new_count, MAX(time) as latest_payment, 
                   SUM(amount) as total_amount
            FROM PaymentsWebsite 
            WHERE time > ? AND status = 'Success'";
    
    // If we have notified payments, exclude them
    if (!empty($notifiedPayments)) {
        $idList = implode(',', array_map('intval', $notifiedPayments));
        $sql .= " AND id NOT IN ($idList)";
    }
    
    $stmt = sqlsrv_query($mssqlconn, $sql, array($lastCheck));
    if ($stmt === false) {
        throw new Exception("Failed to execute statement: " . print_r(sqlsrv_errors(), true));
    }
    
    $data = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    
    // Convert DateTime objects if needed
    if ($data && $data['latest_payment'] instanceof DateTime) {
        $data['latest_payment'] = $data['latest_payment']->format('Y-m-d H:i:s');
    }
    
    sqlsrv_free_stmt($stmt);
    
    // Get details of new payments for better notification
    $newPayments = [];
    $newPaymentIds = [];
    
    if ($data && $data['new_count'] > 0) {
        $detailSql = "SELECT TOP 5 id, amount, client_mobile_number, time 
                     FROM PaymentsWebsite 
                     WHERE time > ? AND status = 'Success'";
        
        // Exclude already notified payments
        if (!empty($notifiedPayments)) {
            $detailSql .= " AND id NOT IN ($idList)";
        }
        
        $detailSql .= " ORDER BY time DESC";
        
        $detailStmt = sqlsrv_query($mssqlconn, $detailSql, array($lastCheck));
        
        if ($detailStmt) {
            while ($payment = sqlsrv_fetch_array($detailStmt, SQLSRV_FETCH_ASSOC)) {
                // Convert DateTime objects
                if ($payment['time'] instanceof DateTime) {
                    $payment['time'] = $payment['time']->format('Y-m-d H:i:s');
                }
                $newPayments[] = $payment;
                $newPaymentIds[] = $payment['id'];
            }
            
            // Add new payment IDs to the notified list
            if (!isset($_SESSION['notified_payments'])) {
                $_SESSION['notified_payments'] = [];
            }
            $_SESSION['notified_payments'] = array_merge($_SESSION['notified_payments'], $newPaymentIds);
            
            // Keep only last 100 notified payments to prevent session from growing too large
            if (count($_SESSION['notified_payments']) > 100) {
                $_SESSION['notified_payments'] = array_slice($_SESSION['notified_payments'], -100);
            }
            
            sqlsrv_free_stmt($detailStmt);
        }
    }
    
    // Update last check time only if we found new payments
    if ($data && $data['new_count'] > 0) {
        $_SESSION['last_payment_check'] = date('Y-m-d H:i:s');
    }
    
    // Clear any buffered output before sending JSON
    ob_clean();
    
    // Return response
    $response = [
        'success' => true,
        'new_payments' => $data ? intval($data['new_count']) : 0,
        'total_amount' => $data ? floatval($data['total_amount']) : 0,
        'latest_payment_time' => $data ? $data['latest_payment'] : null,
        'payment_details' => $newPayments,
        'timestamp' => date('Y-m-d H:i:s')
    ];
    
    echo json_encode($response);
    
} catch (Exception $e) {
    // Clear any buffered output
    ob_clean();
    
    // Log the error for debugging
    error_log("Payment check error: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
    
    echo json_encode([
        'success' => false,
        'error' => 'Failed to check payments',
        'debug' => 'Check server logs for details'
    ]);
}

// End output buffering and close connection
ob_end_flush();
if (isset($mssqlconn)) {
    sqlsrv_close($mssqlconn);
}
?>
