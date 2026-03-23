<?php
// process_payment_auto.php - Automated payment processing for callback
// This is a simplified version of mark_payment_processed.php for automatic processing

date_default_timezone_set('Asia/Tbilisi');

// Turn off all output buffering and ensure clean output
while (ob_get_level()) {
    ob_end_clean();
}

// Prevent any accidental output
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Include database connections
include('db_connection.php');
include('mssql_connection.php');
include('mssql_packages_payments_helper.php');

// Function to clean mobile number for SMS sending (remove country codes)
function cleanMobileForSMS($mobile) {
    $mobile = trim($mobile);
    
    // Remove +995 prefix if present
    if (str_starts_with($mobile, '+995')) {
        $mobile = substr($mobile, 4);
    }
    // Remove 995 prefix if present
    elseif (str_starts_with($mobile, '995')) {
        $mobile = substr($mobile, 3);
    }
    
    return $mobile;
}

// Function to automatically send QR code after successful payment processing
function sendQrCodeAutomatically($idNumber, $mobile, $email) {
    global $conn, $mssqlconn;
    
    // Clean mobile number for SMS API (remove country codes)
    $cleanMobile = cleanMobileForSMS($mobile);
    
    error_log("Auto-processing: Attempting to send QR code for ID: {$idNumber}, Original Mobile: {$mobile}, Clean Mobile: {$cleanMobile}, Email: {$email}");
    
    // Set up $_POST variables as if send_qr.php was called normally
    $original_post = $_POST;
    $_POST['idNumber'] = $idNumber;
    $_POST['mobile'] = $cleanMobile;
    $_POST['email'] = $email;
    
    // Capture output from send_qr.php
    ob_start();
    try {
        include('send_qr.php');
        $output = ob_get_contents();
    } catch (Exception $e) {
        error_log("Auto-processing: QR Code sending exception: " . $e->getMessage());
        ob_end_clean();
        $_POST = $original_post;
        return false;
    }
    ob_end_clean();
    
    // Restore original $_POST
    $_POST = $original_post;
    
    // Try to decode the response
    $result = json_decode($output, true);
    if ($result && isset($result['status'])) {
        if ($result['status'] === 'success') {
            error_log("Auto-processing: QR Code sent successfully: " . $result['message']);
            return true;
        } else {
            error_log("Auto-processing: QR Code sending failed: " . $result['message']);
            return false;
        }
    } else {
        error_log("Auto-processing: QR Code sending - invalid response format: " . $output);
        return false;
    }
}

// Main processing function
function processPaymentAutomatically($payment_id) {
    global $conn, $mssqlconn;
    
    if (!$conn || !$mssqlconn) {
        return [
            'success' => false,
            'message' => 'Database connection failed'
        ];
    }
    
    try {
        // Include the main processing logic from mark_payment_processed.php
        // But simplified for automatic processing without session requirements
        
        // Step 1: Get payment details from MSSQL PaymentsWebsite
        $payment = getPaymentWebsiteById($payment_id);
        
        if (!$payment) {
            return [
                'success' => false,
                'message' => "Payment not found (Payment ID: {$payment_id})"
            ];
        }
        
        $mobile_number = $payment['client_mobile_number'];
        $already_processed = $payment['processed'];
        
        // Get package information from MSSQL PackagesWebsite
        $packageInfo = null;
        if (!empty($payment['package_id'])) {
            $packageInfo = getPackageWebsiteByPackageId($payment['package_id']);
        }
        
        // Check if already processed
        if ($already_processed == 1) {
            return [
                'success' => false,
                'message' => "Payment is already processed (Payment ID: {$payment_id})"
            ];
        }
        
        // Use the same logic as mark_payment_processed.php but with automatic user ID
        $creator_user_id = 1; // Automatic processing user ID
        
        // The rest of the logic would be identical to mark_payment_processed.php
        // For brevity, I'll call the actual processing file with a special flag
        
        // Set up temporary session for processing
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['user_logged_in'] = true;
        $_SESSION['user_id'] = 1;
        
        // Prepare the input
        $input_data = ['payment_id' => $payment_id];
        
        // Save current $_POST and set up for mark_payment_processed.php
        $original_post = $_POST;
        $original_server_method = $_SERVER['REQUEST_METHOD'] ?? '';
        
        $_SERVER['REQUEST_METHOD'] = 'POST';
        
        // Create a temporary stream to simulate php://input
        $temp_input = 'data://text/plain;base64,' . base64_encode(json_encode($input_data));
        
        // Temporarily override the input stream
        $original_input = ini_get('auto_prepend_file');
        
        // Use output buffering to capture the response
        ob_start();
        
        // Create a way to pass data to mark_payment_processed.php
        file_put_contents('temp_payment_data.json', json_encode($input_data));
        
        // Include the processing logic
        include('admin/mark_payment_processed_logic.php');
        
        $output = ob_get_contents();
        ob_end_clean();
        
        // Clean up
        $_POST = $original_post;
        $_SERVER['REQUEST_METHOD'] = $original_server_method;
        unset($_SESSION['user_logged_in']);
        unset($_SESSION['user_id']);
        
        if (file_exists('temp_payment_data.json')) {
            unlink('temp_payment_data.json');
        }
        
        // Parse result
        $result = json_decode($output, true);
        return $result ?: ['success' => false, 'message' => 'Invalid processing response'];
        
    } catch (Exception $e) {
        error_log("Auto-processing error: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Processing failed: ' . $e->getMessage()
        ];
    }
}

// If called directly (not included), process the payment
if (isset($GLOBALS['auto_process_payment_id'])) {
    $result = processPaymentAutomatically($GLOBALS['auto_process_payment_id']);
    header('Content-Type: application/json');
    echo json_encode($result);
}
?>
