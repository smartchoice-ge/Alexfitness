<?php
// flitt_callback.php - Handle Flitt payment gateway callbacks
require_once 'db_connection.php';
require_once 'mssql_connection.php';
require_once 'mssql_packages_payments_helper.php';
date_default_timezone_set('Asia/Tbilisi');

// Flitt credentials
define('FLITT_MERCHANT_ID', 4055998);
define('FLITT_SECRET_KEY', 'jNQSv9muOFcSLo1cxiTlvvDGt2vu8D9I');

// Function to generate Flitt signature for verification
function generateFlittSignature($params, $secretKey) {
    // Remove signature and response_signature_string from params
    $filtered = [];
    foreach ($params as $key => $value) {
        if ($key === 'signature' || $key === 'response_signature_string') continue;
        if ($value !== '' && $value !== null) {
            $filtered[$key] = $value;
        }
    }
    // Sort by key alphabetically
    ksort($filtered);
    $values = array_values($filtered);
    // Prepend secret key, join with |
    array_unshift($values, $secretKey);
    $signatureString = implode('|', $values);
    return sha1($signatureString);
}

// Function to process payment immediately
function processPaymentImmediately($payment_id, $mysql_conn) {
    $basePath = rtrim(dirname($_SERVER['REQUEST_URI']), '/\\');
    $url = 'https://' . $_SERVER['HTTP_HOST'] . ($basePath ? $basePath : '') . '/admin/mark_payment_processed.php';
    
    $data = json_encode(['payment_id' => $payment_id]);
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($data),
        'X-Callback-Token: synergy_internal_callback',
        'X-Requested-With: XMLHttpRequest'
    ]);
    
    try {
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if ($response === false) {
            return ['success' => false, 'message' => 'cURL error: ' . curl_error($ch), 'url' => $url];
        }
        
        if ($http_code !== 200) {
            return ['success' => false, 'message' => 'HTTP error: ' . $http_code, 'response' => $response, 'url' => $url];
        }
        
        $result = json_decode($response, true);
        return ($result && is_array($result)) ? $result : ['success' => false, 'message' => 'Invalid JSON response', 'response' => $response, 'url' => $url];
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Exception: ' . $e->getMessage()];
    } finally {
        curl_close($ch);
    }
}

// Accept both POST and GET requests
$timestamp = date('Y-m-d H:i:s');
$raw_data = file_get_contents('php://input');

$log_entry = "=== FLITT CALLBACK RECEIVED at $timestamp ===\n";
$log_entry .= "RAW BODY: " . $raw_data . "\n";
$log_entry .= "POST: " . print_r($_POST, true) . "\n";
$log_entry .= "REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD'] . "\n";
$log_entry .= "=======================================\n\n";
$log_file = 'flitt_callback.log';
file_put_contents($log_file, $log_entry, FILE_APPEND | LOCK_EX);

// Flitt sends callback as JSON POST
$data = json_decode($raw_data, true);
if (!$data || !is_array($data)) {
    $data = $_POST;
}

if (!$data || !is_array($data)) {
    $log_entry = "=== NO DATA RECEIVED at $timestamp ===\n\n";
    file_put_contents($log_file, $log_entry, FILE_APPEND | LOCK_EX);
    http_response_code(200);
    echo "OK";
    exit;
}

// Verify signature
$received_signature = isset($data['signature']) ? $data['signature'] : null;
$signature_valid = false;
if ($received_signature) {
    $calculated_signature = generateFlittSignature($data, FLITT_SECRET_KEY);
    $signature_valid = ($received_signature === $calculated_signature);
    
    $log_entry = "Signature check: " . ($signature_valid ? "VALID" : "INVALID") . "\n";
    $log_entry .= "Received: $received_signature\n";
    $log_entry .= "Calculated: $calculated_signature\n\n";
    file_put_contents($log_file, $log_entry, FILE_APPEND | LOCK_EX);
}

// Extract Flitt callback fields
$order_id = isset($data['order_id']) ? $data['order_id'] : null;
$order_status = isset($data['order_status']) ? $data['order_status'] : null; // approved, declined, expired, processing, reversed
$payment_id_flitt = isset($data['payment_id']) ? $data['payment_id'] : null;
$amount = isset($data['amount']) ? $data['amount'] : null;
$currency = isset($data['currency']) ? $data['currency'] : null;
$masked_card = isset($data['masked_card']) ? $data['masked_card'] : null;
$merchant_data = isset($data['merchant_data']) ? $data['merchant_data'] : null;

// Extract mobile number from order_id (format: mobile_XXXXX)
$mobile_number = null;
$transaction_id = $order_id; // Use order_id as transaction_id for DB compatibility
if ($order_id && strpos($order_id, '_') !== false) {
    $mobile_number = substr($order_id, 0, strpos($order_id, '_'));
}

// Also try extracting from merchant_data
if (!$mobile_number && $merchant_data) {
    $merchant_info = json_decode($merchant_data, true);
    if ($merchant_info && isset($merchant_info['mobileNumber'])) {
        $mobile_number = $merchant_info['mobileNumber'];
    }
}

// Normalize mobile number with 995 prefix for consistency
// PaymentsWebsite now stores phone with 995 prefix
$mobile_number_995 = $mobile_number;
if ($mobile_number && strpos($mobile_number, '995') !== 0) {
    $mobile_number_995 = '995' . $mobile_number;
}

// Map Flitt order_status to our status
$status = null;
$status_debug = "Status detection: order_status='$order_status' ";

if ($order_status === 'approved') {
    $status = 'Success';
    $status_debug .= "-> SUCCESS";
} elseif ($order_status === 'declined' || $order_status === 'expired' || $order_status === 'reversed') {
    $status = 'Fail';
    $status_debug .= "-> FAIL";
} elseif ($order_status === 'processing') {
    $status = 'Pending';
    $status_debug .= "-> PENDING (still processing)";
} else {
    $status = 'Fail';
    $status_debug .= "-> UNKNOWN STATUS, DEFAULTING TO FAIL";
}

if ($transaction_id || $mobile_number) {
    $log_entry = "=== DATABASE UPDATE at $timestamp ===\n";
    $log_entry .= "Order ID (transaction_id): " . ($transaction_id ?: 'N/A') . "\n";
    $log_entry .= "Flitt Payment ID: " . ($payment_id_flitt ?: 'N/A') . "\n";
    $log_entry .= "Mobile Number: " . ($mobile_number ?: 'N/A') . "\n";
    $log_entry .= "Status: " . ($status ?: 'NULL') . "\n";
    $log_entry .= "Status Debug: " . $status_debug . "\n";
    $log_entry .= "Amount: " . ($amount ?: 'N/A') . "\n";
    $log_entry .= "Masked Card: " . ($masked_card ?: 'N/A') . "\n";
    
    $update_result = false;
    $affected_rows = 0;
    
    // Only proceed with database update if we have a valid status (not processing)
    if ($status && $status !== 'Pending') {
        // Try to update by transaction_id first (order_id) - MSSQL
        if ($transaction_id) {
            $affected_rows = updatePaymentStatusByTransactionId($transaction_id, $status);
            $update_result = $affected_rows > 0;
        }
        
        // If no rows affected and we have mobile number, try updating by mobile number - MSSQL
        // Try both with and without 995 prefix
        if ($affected_rows === 0 && $mobile_number) {
            $affected_rows = updatePaymentStatusByMobile($mobile_number_995, $status, $transaction_id ?: $mobile_number);
            $update_result = $affected_rows > 0;
            // Fallback: try without 995 prefix
            if ($affected_rows === 0) {
                $affected_rows = updatePaymentStatusByMobile($mobile_number, $status, $transaction_id ?: $mobile_number);
                $update_result = $affected_rows > 0;
            }
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
                $payment = getPaymentByMobile($mobile_number_995, 'Success');
                if (!$payment) {
                    $payment = getPaymentByMobile($mobile_number, 'Success');
                }
                if ($payment) {
                    $payment_id = $payment['id'];
                }
            }
            
            if ($payment_id) {
                $log_entry .= "Found payment ID: $payment_id - calling mark_payment_processed.php...\n";
                
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
        $log_entry .= "SKIPPED DATABASE UPDATE - Status is still processing/pending\n";
    }
    
    $log_entry .= "=======================================\n\n";
    file_put_contents($log_file, $log_entry, FILE_APPEND | LOCK_EX);
} else {
    $log_entry = "=== MISSING DATA at $timestamp ===\n";
    $log_entry .= "Order ID: " . ($transaction_id ?: 'MISSING') . "\n";
    $log_entry .= "Mobile Number: " . ($mobile_number ?: 'MISSING') . "\n";
    $log_entry .= "Status: " . ($status ?: 'MISSING') . "\n";
    $log_entry .= "=======================================\n\n";
    file_put_contents($log_file, $log_entry, FILE_APPEND | LOCK_EX);
}

// Return 200 OK to Flitt so they know we received it
http_response_code(200);
echo "OK";
?>
