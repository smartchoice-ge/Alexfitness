<?php
// unipay_callback.php - Save to text file and update payment status
require_once 'db_connection.php';
require_once 'mssql_connection.php';
require_once 'mssql_packages_payments_helper.php';
date_default_timezone_set('Asia/Tbilisi');

// Function to process payment immediately
function processPaymentImmediately($payment_id, $mysql_conn) {
    // Make HTTP request to mark_payment_processed.php using cURL
    $url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/admin/mark_payment_processed.php';
    
    $data = json_encode(['payment_id' => $payment_id]);
    
    // Initialize cURL
    $ch = curl_init();
    
    // Set cURL options
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($data),
        'X-Callback-Token: synergy_internal_callback', // Internal callback token
        'X-Requested-With: XMLHttpRequest'
    ]);
    
    try {
        // Execute the request
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if ($response === false) {
            $error = curl_error($ch);
            return [
                'success' => false,
                'message' => 'cURL error: ' . $error,
                'url' => $url
            ];
        }
        
        if ($http_code !== 200) {
            return [
                'success' => false,
                'message' => 'HTTP error: ' . $http_code,
                'response' => $response,
                'url' => $url
            ];
        }
        
        // Parse the JSON response
        $result = json_decode($response, true);
        
        if ($result && is_array($result)) {
            return $result;
        } else {
            return [
                'success' => false,
                'message' => 'Invalid JSON response from payment processor',
                'response' => $response,
                'url' => $url
            ];
        }
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => 'Exception during payment processing: ' . $e->getMessage()
        ];
    } finally {
        curl_close($ch);
    }
}

// Accept both POST and GET requests
$timestamp = date('Y-m-d H:i:s');
$raw_data = file_get_contents('php://input');
$get_data = $_GET;
$post_data = $_POST;

$log_entry = "=== CALLBACK RECEIVED at $timestamp ===\n";
$log_entry .= "RAW BODY: " . $raw_data . "\n";
$log_entry .= "GET: " . print_r($get_data, true) . "\n";
$log_entry .= "POST: " . print_r($post_data, true) . "\n";
$log_entry .= "REQUEST_URI: " . $_SERVER['REQUEST_URI'] . "\n";
$log_entry .= "REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD'] . "\n";
$log_entry .= "=======================================\n\n";
$log_file = 'unipay_test.log';
if (is_writable($log_file)) {
    file_put_contents($log_file, $log_entry, FILE_APPEND | LOCK_EX);
} else {
    error_log('Cannot write to unipay_test.log. Check permissions.');
}

// Try to parse data from raw body, POST, or GET
$data = json_decode($raw_data, true);
if (!$data || !is_array($data)) {
    // Try POST
    $data = $_POST;
}
if (!$data || !is_array($data)) {
    // Try GET
    $data = $_GET;
}

// Extract transaction ID and mobile number - Unipay sends both
$transaction_id = null;
$mobile_number = null;
$merchant_order_id = null;

if (isset($data['UnipayOrderID'])) {
    $transaction_id = $data['UnipayOrderID'];
}
if (isset($data['MerchantOrderID'])) {
    $merchant_order_id = $data['MerchantOrderID'];
    // Extract mobile number from MerchantOrderID (format: mobile_XXXXX)
    if (strpos($merchant_order_id, '_') !== false) {
        $mobile_number = substr($merchant_order_id, 0, strpos($merchant_order_id, '_'));
    } else {
        // Fallback for old format without suffix
        $mobile_number = $merchant_order_id;
    }
}

// Fallback for other formats
if (!$transaction_id) {
    if (isset($data['order_hash'])) {
        $transaction_id = $data['order_hash'];
    } elseif (isset($data['transaction_id'])) {
        $transaction_id = $data['transaction_id'];
    }
}

// Extract status - Unipay sends numeric Status codes
$status = null;
$status_debug = "Status detection: ";

if (isset($data['Status'])) {
    $status_code = (int)$data['Status'];
    $status_debug .= "Found Status field with value '{$data['Status']}' (int: {$status_code}) ";
    // According to Unipay documentation:
    // Status 1 = Success
    // Status 3 = Success (alternative success code)
    // Status 13 = Failed (as shown in your example)
    // Other codes generally indicate failure
    if ($status_code === 1 || $status_code === 3) {
        $status = 'Success';
        $status_debug .= "-> SUCCESS";
    } else {
        $status = 'Fail';
        $status_debug .= "-> FAIL";
    }
} elseif (isset($data['ErrorCode'])) {
    $error_code = (int)$data['ErrorCode'];
    $status_debug .= "Found ErrorCode field with value '{$data['ErrorCode']}' (int: {$error_code}) ";
    if ($error_code === 0) {
        $status = 'Success';
        $status_debug .= "-> SUCCESS";
    } else {
        $status = 'Fail';
        $status_debug .= "-> FAIL";
    }
} elseif (isset($data['status'])) {
    $status_debug .= "Found status field with value '{$data['status']}' ";
    // Fallback for other formats
    if (strtolower($data['status']) === 'success') {
        $status = 'Success';
        $status_debug .= "-> SUCCESS";
    } elseif (strtolower($data['status']) === 'fail' || strtolower($data['status']) === 'failed') {
        $status = 'Fail';
        $status_debug .= "-> FAIL";
    } else {
        $status_debug .= "-> UNKNOWN STATUS VALUE";
    }
} else {
    $status_debug .= "NO STATUS FIELD FOUND in data - checking all fields: " . implode(', ', array_keys($data));
    // Default to Fail if no status information found
    $status = 'Fail';
    $status_debug .= " -> DEFAULTING TO FAIL";
}
if ($transaction_id || $mobile_number) {
    $log_entry = "=== DATABASE UPDATE at $timestamp ===\n";
    $log_entry .= "Transaction ID: " . ($transaction_id ?: 'N/A') . "\n";
    $log_entry .= "Merchant Order ID: " . ($merchant_order_id ?: 'N/A') . "\n";
    $log_entry .= "Mobile Number: " . ($mobile_number ?: 'N/A') . "\n";
    $log_entry .= "Status: " . ($status ?: 'NULL') . "\n";
    $log_entry .= "Status Debug: " . $status_debug . "\n";
    
    $update_result = false;
    $affected_rows = 0;
    
    // Only proceed with database update if we have a valid status
    if ($status) {
        // Try to update by transaction_id first (UnipayOrderID) - MSSQL
        if ($transaction_id) {
            $affected_rows = updatePaymentStatusByTransactionId($transaction_id, $status);
            $update_result = $affected_rows > 0;
        }
        
        // If no rows affected and we have mobile number, try updating by mobile number - MSSQL
        if ($affected_rows === 0 && $mobile_number) {
            $affected_rows = updatePaymentStatusByMobile($mobile_number, $status, $transaction_id ?: $mobile_number);
            $update_result = $affected_rows > 0;
        }
        
        $log_entry .= "Update result: " . ($update_result ? 'SUCCESS' : 'FAILED') . "\n";
        $log_entry .= "Affected rows: $affected_rows\n";
        
        // If payment was successfully updated to Success status, process it immediately
        if ($update_result && $affected_rows > 0 && $status === 'Success') {
            $log_entry .= "Payment updated to Success - attempting immediate processing...\n";
            
            // Get the payment ID that was just updated - MSSQL
            $payment_id = null;
            if ($transaction_id) {
                $payment = getPaymentByTransactionId($transaction_id, 'Success');
                if ($payment) {
                    $payment_id = $payment['id'];
                }
            } elseif ($mobile_number) {
                $payment = getPaymentByMobile($mobile_number, 'Success');
                if ($payment) {
                    $payment_id = $payment['id'];
                }
            }
            
            if ($payment_id) {
                $log_entry .= "Found payment ID: $payment_id - calling mark_payment_processed.php...\n";
                
                // Call the payment processing function
                $processing_result = processPaymentImmediately($payment_id, $conn);
                $log_entry .= "Processing result: " . ($processing_result['success'] ? 'SUCCESS' : 'FAILED') . "\n";
                $log_entry .= "Processing message: " . $processing_result['message'] . "\n";
                if (isset($processing_result['qr_sent'])) {
                    $log_entry .= "QR code sent: " . ($processing_result['qr_sent'] ? 'YES' : 'NO') . "\n";
                }
            } else {
                $log_entry .= "Could not find payment ID for immediate processing\n";
            }
        }
    } else {
        $log_entry .= "SKIPPED DATABASE UPDATE - No valid status detected\n";
    }
    
    $log_entry .= "=======================================\n\n";
    file_put_contents($log_file, $log_entry, FILE_APPEND | LOCK_EX);
} else {
    $log_entry = "=== MISSING DATA at $timestamp ===\n";
    $log_entry .= "Transaction ID: " . ($transaction_id ?: 'MISSING') . "\n";
    $log_entry .= "Mobile Number: " . ($mobile_number ?: 'MISSING') . "\n";
    $log_entry .= "Status: " . ($status ?: 'MISSING') . "\n";
    $log_entry .= "=======================================\n\n";
    
    file_put_contents($log_file, $log_entry, FILE_APPEND | LOCK_EX);
}
// Return OK to Unipay so they know we received it
http_response_code(200);
echo "OK";
?>