<?php

date_default_timezone_set('Asia/Tbilisi');

// Turn off all output buffering and ensure clean output
while (ob_get_level()) {
    ob_end_clean();
}

// Prevent any accidental output
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors in output
ini_set('log_errors', 1); // Log errors instead

// Set content type to JSON immediately
header('Content-Type: application/json; charset=utf-8');

session_start([
    'cookie_httponly' => true,
    'cookie_samesite' => 'Strict'
]);

// Check if user is logged in OR if this is an internal callback request with valid token
$callback_token = $_SERVER['HTTP_X_CALLBACK_TOKEN'] ?? '';
$is_internal_callback = $callback_token === 'synergy_internal_callback';

if (!$is_internal_callback && (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true)) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Include database connections
include('../db_connection.php');
include('../mssql_connection.php');
include('../mssql_packages_payments_helper.php');

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
    global $conn, $mssqlconn; // Make database connections available

    if (!$conn) {
        error_log("Skipping QR code send - MySQL is unavailable in Synergy");
        return false;
    }
    
    // Clean mobile number for SMS API (remove country codes)
    $cleanMobile = cleanMobileForSMS($mobile);
    
    error_log("Attempting to send QR code automatically for ID: {$idNumber}, Original Mobile: {$mobile}, Clean Mobile: {$cleanMobile}, Email: {$email}");
    
    // Include send_qr.php functionality directly instead of using cURL
    // This avoids URL resolution issues and is more efficient
    
    // Set up $_POST variables as if send_qr.php was called normally
    $original_post = $_POST;
    $_POST['idNumber'] = $idNumber;
    $_POST['mobile'] = $cleanMobile; // Use cleaned mobile number for SMS
    $_POST['email'] = $email;
    
    // Capture output from send_qr.php
    ob_start();
    try {
        include('../send_qr.php');
        $output = ob_get_contents();
    } catch (Exception $e) {
        error_log("QR Code sending exception: " . $e->getMessage());
        ob_end_clean();
        $_POST = $original_post; // Restore original $_POST
        return false;
    }
    ob_end_clean();
    
    // Restore original $_POST
    $_POST = $original_post;
    
    // Try to decode the response
    $result = json_decode($output, true);
    if ($result && isset($result['status'])) {
        if ($result['status'] === 'success') {
            error_log("QR Code sent successfully: " . $result['message']);
            return true;
        } else {
            error_log("QR Code sending failed: " . $result['message']);
            return false;
        }
    } else {
        error_log("QR Code sending - invalid response format: " . $output);
        return false;
    }
}

// Function to automatically send workout programs after successful payment processing
function sendProgramAutomatically($idNumber, $mobile, $email) {
    global $conn, $mssqlconn; // Make database connections available

    if (!$conn) {
        error_log("Skipping program send - MySQL is unavailable in Synergy");
        return false;
    }
    
    // Clean mobile number for SMS API (remove country codes)
    $cleanMobile = cleanMobileForSMS($mobile);
    
    error_log("Attempting to send program automatically for ID: {$idNumber}, Original Mobile: {$mobile}, Clean Mobile: {$cleanMobile}, Email: {$email}");
    
    // Set up $_POST variables as if send_program.php was called normally
    $original_post = $_POST;
    $_POST['idNumber'] = $idNumber;
    $_POST['mobile'] = $cleanMobile; // Use cleaned mobile number for SMS
    $_POST['email'] = $email;
    
    // Capture output from send_program.php
    ob_start();
    try {
        include('../send_program.php');
        $output = ob_get_contents();
    } catch (Exception $e) {
        error_log("Program sending exception: " . $e->getMessage());
        ob_end_clean();
        $_POST = $original_post; // Restore original $_POST
        return false;
    }
    ob_end_clean();
    
    // Restore original $_POST
    $_POST = $original_post;
    
    // Try to decode the response
    $result = json_decode($output, true);
    if ($result && isset($result['status'])) {
        if ($result['status'] === 'success') {
            error_log("Program sent successfully: " . $result['message']);
            return true;
        } else {
            error_log("Program sending failed: " . $result['message']);
            return false;
        }
    } else {
        error_log("Program sending - invalid response format: " . $output);
        return false;
    }
}

// Function to automatically send group workouts after successful payment processing
function sendWorkoutsAutomatically($mobile, $email) {
    global $conn, $mssqlconn; // Make database connections available

    if (!$conn) {
        error_log("Skipping workouts send - MySQL is unavailable in Synergy");
        return false;
    }
    
    // Clean mobile number for SMS API (remove country codes)
    $cleanMobile = cleanMobileForSMS($mobile);
    
    error_log("Attempting to send workouts automatically for Original Mobile: {$mobile}, Clean Mobile: {$cleanMobile}, Email: {$email}");
    
    // Set up $_POST variables as if send_workouts.php was called normally
    $original_post = $_POST;
    $_POST['mobile'] = $cleanMobile; // Use cleaned mobile number for SMS
    $_POST['email'] = $email;
    
    // Capture output from send_workouts.php
    ob_start();
    try {
        include('../send_workouts.php');
        $output = ob_get_contents();
    } catch (Exception $e) {
        error_log("Workouts sending exception: " . $e->getMessage());
        ob_end_clean();
        $_POST = $original_post; // Restore original $_POST
        return false;
    }
    ob_end_clean();
    
    // Restore original $_POST
    $_POST = $original_post;
    
    // Try to decode the response
    $result = json_decode($output, true);
    if ($result && isset($result['status'])) {
        if ($result['status'] === 'success') {
            error_log("Workouts sent successfully: " . $result['message']);
            return true;
        } else {
            error_log("Workouts sending failed: " . $result['message']);
            return false;
        }
    } else {
        error_log("Workouts sending - invalid response format: " . $output);
        return false;
    }
}

// Check database connections
if (!$conn) {
    error_log("MySQL connection unavailable in mark_payment_processed.php - continuing with MSSQL-only flow");
}

if (!$mssqlconn) {
    error_log("MSSQL connection failed in mark_payment_processed.php");
    echo json_encode(['success' => false, 'message' => 'MSSQL database connection failed']);
    exit;
}

// Ensure the in_progress column exists (idempotent; safe to run every request on old deployments)
$addColSql = "IF NOT EXISTS (SELECT 1 FROM sys.columns WHERE object_id = OBJECT_ID('PaymentsWebsite') AND name = 'in_progress')
    ALTER TABLE PaymentsWebsite ADD in_progress BIT NOT NULL DEFAULT 0";
sqlsrv_query($mssqlconn, $addColSql); // ignore errors — column may already exist

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (json_last_error() !== JSON_ERROR_NONE) {
    error_log("JSON decode error in mark_payment_processed.php: " . json_last_error_msg());
    echo json_encode(['success' => false, 'message' => 'Invalid JSON input']);
    exit;
}

if (!isset($input['payment_id']) || !is_numeric($input['payment_id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid payment ID']);
    exit;
}

$payment_id = (int)$input['payment_id'];

try {
    // Step 1: Get payment details from MSSQL PaymentsWebsite
    $payment = getPaymentWebsiteById($payment_id);
    
    if (!$payment) {
        echo json_encode([
            'success' => false, 
            'message' => "Payment not found (Payment ID: {$payment_id})",
            'payment_id' => $payment_id
        ]);
        exit;
    }
    
    $mobile_number = $payment['client_mobile_number'];
    $already_processed = $payment['processed'];
    
    // Determine PaymentTypeID based on payment source
    // Flitt (app) transaction_ids have format: phoneNumber_HEXCODE (e.g., 598997759_FD15E1)
    // Unipay (website) transaction_ids use Unipay's hash format
    $transaction_id = $payment['transaction_id'] ?? '';
    $is_flitt_payment = preg_match('/^\d+_[A-F0-9]+$/i', $transaction_id);
    $payment_type_id = $is_flitt_payment ? 15 : 7; // 15 = Flitt (app), 7 = Unipay (website)
    
    // Get package information from MSSQL PackagesWebsite
    $packageInfo = null;
    if (!empty($payment['package_id'])) {
        $packageInfo = getPackageWebsiteByPackageId($payment['package_id']);
    }
    
    $mysql_package_name_geo = $packageInfo['name_geo'] ?? null;
    $mysql_package_name_eng = $packageInfo['name_eng'] ?? null;
    $mysql_package_price = $packageInfo['price'] ?? null;
    $mysql_package_duration = $packageInfo['duration_month'] ?? null;
    
    // Check if package_id exists and package information was found
    if (empty($payment['package_id'])) {
        echo json_encode([
            'success' => false, 
            'message' => "Payment does not have a package ID (Payment ID: {$payment_id}, Mobile: {$mobile_number})",
            'payment_id' => $payment_id,
            'mobile_number' => $mobile_number
        ]);
        exit;
    }
    
    if (empty($mysql_package_name_eng) || empty($mysql_package_name_geo)) {
        echo json_encode([
            'success' => false, 
            'message' => "Package information not found in database (Payment ID: {$payment_id}, Package ID: {$payment['package_id']}, Mobile: {$mobile_number})",
            'payment_id' => $payment_id,
            'package_id' => $payment['package_id'],
            'mobile_number' => $mobile_number
        ]);
        exit;
    }
    
    if (empty($mysql_package_duration) || !is_numeric($mysql_package_duration)) {
        echo json_encode([
            'success' => false, 
            'message' => "Package duration not found or invalid in database (Payment ID: {$payment_id}, Package ID: {$payment['package_id']}, Mobile: {$mobile_number})",
            'payment_id' => $payment_id,
            'package_id' => $payment['package_id'],
            'mobile_number' => $mobile_number
        ]);
        exit;
    }
    
    // Use a dedicated "in_progress" flag to prevent race conditions from duplicate
    // Flitt callbacks, while still only marking "processed" at the end on success.
    // Two concurrent requests: only one wins the atomic UPDATE WHERE in_progress=0;
    // the loser exits immediately without creating a duplicate SoldPackages row.
    // If processing fails mid-way, in_progress stays 1 but processed stays 0,
    // so an admin can reset in_progress=0 and retry without financial data loss.
    $claimSql = "UPDATE PaymentsWebsite SET in_progress = 1, updated_at = GETDATE() WHERE id = ? AND processed = 0 AND in_progress = 0";
    $claimParams = [$payment_id];
    $claimStmt = sqlsrv_query($mssqlconn, $claimSql, $claimParams);
    if ($claimStmt === false) {
        // Column may not exist yet — fall back to the old processed check to avoid breaking existing deployments
        $claimErrors = sqlsrv_errors();
        $errorMsg = print_r($claimErrors, true);
        if (strpos($errorMsg, 'in_progress') !== false) {
            // Column doesn't exist: fall back silently and rely on processed flag
            error_log("in_progress column not found, falling back to processed check only");
        } else {
            error_log("Atomic claim error in mark_payment_processed: " . $errorMsg);
            echo json_encode(['success' => false, 'message' => 'Database error during payment claim']);
            exit;
        }
        // Fallback: check already_processed from earlier read
        if ($already_processed == 1) {
            echo json_encode([
                'success' => false,
                'message' => "Payment is already processed (Payment ID: {$payment_id}, Mobile: {$mobile_number})",
                'payment_id' => $payment_id,
                'mobile_number' => $mobile_number
            ]);
            exit;
        }
    } else {
        $claimedRows = sqlsrv_rows_affected($claimStmt);
        sqlsrv_free_stmt($claimStmt);

        if ($claimedRows === 0) {
            // Another request already claimed or completed this payment
            echo json_encode([
                'success' => false,
                'message' => "Payment is already processed or in progress (Payment ID: {$payment_id}, Mobile: {$mobile_number})",
                'payment_id' => $payment_id,
                'mobile_number' => $mobile_number
            ]);
            exit;
        }
    }
    
    // Step 2: First check if client exists in MSSQL ClientDetailsWebsite table
    $client_data_without_995 = $mobile_number;
    $client_data_with_995 = $mobile_number;
    
    // Prepare both formats for ClientDetailsWebsite table
    if (strpos($mobile_number, '995') === 0) {
        $client_data_without_995 = substr($mobile_number, 3);
    } else {
        $client_data_with_995 = '995' . $mobile_number;
    }
    
    // Check ClientDetailsWebsite table first (MSSQL)
    $client_details = getClientDetailsByMobile($mobile_number);
    
    // If not found, try alternative format
    if (!$client_details) {
        $client_details = getClientDetailsByMobile($client_data_with_995);
    }
    if (!$client_details) {
        $client_details = getClientDetailsByMobile($client_data_without_995);
    }
    
    if (!$client_details) {
        echo json_encode([
            'success' => false, 
            'message' => "Mobile number does not exist in ClientDetailsWebsite table (Payment ID: {$payment_id}, Mobile: {$mobile_number}). Client must be registered first.",
            'phone_check' => 'not_found_in_client_details',
            'payment_id' => $payment_id,
            'mobile_number' => $mobile_number,
            'searched_numbers' => [$client_data_with_995, $client_data_without_995]
        ]);
        exit;
    }
    
    // Step 3: Get client ID from MSSQL Clients table (client should already exist from registration)
    if (!$mssqlconn) {
        echo json_encode(['success' => false, 'message' => 'Failed to connect to MSSQL database']);
        exit;
    }
    
    // Get client data from MSSQL ClientDetailsWebsite
    $client_id_number = $client_details['id_number'];
    $client_email = $client_details['email'];
    
    // Prepare mobile number for MSSQL lookup (ensure it has 995 prefix)
    $phone_for_mssql = $mobile_number;
    if (strpos($mobile_number, '995') !== 0) {
        $phone_for_mssql = '995' . $mobile_number;
    }
    
    // Prepare both phone number formats for MSSQL lookup
    $mssql_phone_without_995 = $mobile_number;
    $mssql_phone_with_995 = $phone_for_mssql;
    
    // If mobile number starts with 995, remove it to get the version without 995
    if (strpos($mobile_number, '995') === 0) {
        $mssql_phone_without_995 = substr($mobile_number, 3);
    }
    
    // Log the lookup attempt for debugging
    error_log("MSSQL phone lookup - With 995: {$mssql_phone_with_995}, Without 995: {$mssql_phone_without_995}");
    
    // Check if client exists in MSSQL by phone number (both formats)
    $checkSql = "SELECT ID FROM Clients WHERE Phone = ? OR Phone = ?";
    $checkStmt = sqlsrv_query($mssqlconn, $checkSql, array($mssql_phone_with_995, $mssql_phone_without_995));
    
    if ($checkStmt === false) {
        $errors = sqlsrv_errors();
        error_log("MSSQL check client error: " . print_r($errors, true));
        echo json_encode(['success' => false, 'message' => 'Failed to check client in MSSQL database']);
        exit;
    }
    
    $client_id = null;
    if ($existing_client = sqlsrv_fetch_array($checkStmt, SQLSRV_FETCH_ASSOC)) {
        // Client exists in MSSQL
        $client_id = $existing_client['ID'];
        error_log("Client found in MSSQL with phone (checked both {$mssql_phone_with_995} and {$mssql_phone_without_995}) and ID: {$client_id}");
    } else {
        // Client doesn't exist in MSSQL - this shouldn't happen as clients are auto-saved during registration
        // As a fallback, save the client now (for backward compatibility with old clients)
        error_log("WARNING: Client not found in MSSQL during payment processing - saving now as fallback");
        
        $creator_user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1;
        
        $sql = "
        INSERT INTO Clients (FullName, IdNumber, BirthDate, Phone, Email, SexID, CardNumber, IndeviceID, ProfilePicture, SendSms, IsActive, SupplierID, ClientTypeID, CreatorUser, BlackListed, ClientParentOrgID, GroupSync, Comment)
        SELECT ?, ?, ?, ?, ?, 1, '', 
            COALESCE((SELECT MAX(InDeviceID) + 1 FROM Clients), 1),
            ?, 1, 1, 2, 2, ?, 0, 19, 0, ''
        ";
        
        // Get additional client data from MSSQL ClientDetailsWebsite for the save
        $full_name = $client_details['full_name'] ?? '';
        $birth_date = $client_details['birth_date'] ?? null;
        $picurl = !empty($client_details['picurl']) ? '/' . $client_details['picurl'] : null;
        
        $params = array($full_name, $client_id_number, $birth_date, $phone_for_mssql, $client_email, $picurl, $creator_user_id);
        $stmt_save = sqlsrv_query($mssqlconn, $sql, $params);
        
        if ($stmt_save === false) {
            $errors = sqlsrv_errors();
            error_log("MSSQL save client error: " . print_r($errors, true));
            echo json_encode(['success' => false, 'message' => 'Failed to save client to MSSQL database']);
            exit;
        }
        
        // Get the inserted Client ID by phone number
        $fetchSql = "SELECT ID FROM Clients WHERE Phone = ?";
        $fetchParams = array($phone_for_mssql);
        $fetchStmt = sqlsrv_query($mssqlconn, $fetchSql, $fetchParams);
        
        if ($fetchStmt && $row = sqlsrv_fetch_array($fetchStmt, SQLSRV_FETCH_ASSOC)) {
            $client_id = $row['ID'];
            error_log("Client saved to MSSQL as fallback with ID: {$client_id}");
            
            // Insert welcome SMS log
            $smsSql = "INSERT INTO SMSLog (ClientID, SMSText, SmsSentStatusID, PhoneNumber, UserID) VALUES (?, ?, ?, ?, ?)";
            $smsParams = array($client_id, 'Welcome to Alex Fitness, Mokharulebi vart rom gakhdit chveni gundis tsevri.', 1, $phone_for_mssql, $creator_user_id);
            $smsStmt = sqlsrv_query($mssqlconn, $smsSql, $smsParams);
            
            if ($smsStmt) {
                sqlsrv_free_stmt($smsStmt);
            }
            sqlsrv_free_stmt($fetchStmt);
        } else {
            echo json_encode(['success' => false, 'message' => 'Client saved but failed to retrieve client ID']);
            exit;
        }
        
        sqlsrv_free_stmt($stmt_save);
    }
    
    sqlsrv_free_stmt($checkStmt);
    
    // Step 5: Check for existing active subscriptions
    $sql_check_packages = "SELECT * FROM SoldPackages WHERE ClientID = ?";
    $params_packages = array($client_id);
    $stmt_packages = sqlsrv_query($mssqlconn, $sql_check_packages, $params_packages);
    
    if ($stmt_packages === false) {
        $errors = sqlsrv_errors();
        error_log("MSSQL SoldPackages query error: " . print_r($errors, true));
        echo json_encode(['success' => false, 'message' => 'Failed to check existing subscriptions']);
        exit;
    }
    
    // Check if any subscription is active (Expired = 0)
    $has_active_subscription = false;
    $active_subscription_id = null;
    $current_end_date = null;
    
    while ($package_row = sqlsrv_fetch_array($stmt_packages, SQLSRV_FETCH_ASSOC)) {
        if ($package_row['Expired'] == 0) {
            $has_active_subscription = true;
            $active_subscription_id = $package_row['ID'];
            $current_end_date = $package_row['EndDate'];
            break;
        }
    }
    
    // Step 6: Calculate subscription duration using duration_month from MySQL
    $months_to_add = intval($mysql_package_duration);
    
    // Log for debugging
    error_log("Package: {$mysql_package_name_eng} (ID: {$payment['package_id']}), Duration from DB: {$months_to_add} months");
    
    // Check if this is a one-time package (duration = 50)
    $is_one_time_package = ($months_to_add == 50);
        
    
    if ($is_one_time_package) {
        // One-time package: 1 day duration, 1 visit
        $days_to_add = 1;
        $visits_count = 0;
        $visits_left = 1;
        error_log("One-time package detected - setting 1 day duration, 1 visit");
    } else {
        // Regular package: calculate days based on months (30 days per month)
        $days_to_add = $months_to_add * 30;
        $visits_count = 999;
        $visits_left = 999;
    }
    
    if ($has_active_subscription) {
        // User has active subscription - expire old and create new with remaining days
        error_log("Expiring existing subscription ID: {$active_subscription_id} and creating new one");
        
        // Calculate remaining days from current subscription
        $current_date = new DateTime();
        $end_date_obj = $current_end_date;
        $remaining_days = 0;
        
        if ($end_date_obj > $current_date) {
            $interval = $current_date->diff($end_date_obj);
            $remaining_days = $interval->days;
            error_log("Remaining days from old subscription: {$remaining_days}");
        }
        
        // Step 7: Set existing subscription as expired
        $sql_expire = "UPDATE SoldPackages SET Expired = 1 WHERE ID = ?";
        $params_expire = array($active_subscription_id);
            $stmt_expire = sqlsrv_query($mssqlconn, $sql_expire, $params_expire);
            
            if ($stmt_expire === false) {
                $errors = sqlsrv_errors();
                error_log("MSSQL expire SoldPackages error: " . print_r($errors, true));
                echo json_encode(['success' => false, 'message' => 'Failed to expire old subscription in MSSQL database']);
                exit;
            }
            
            // Step 8: Create new subscription with new package duration + remaining days
            $total_days_to_add = $days_to_add + $remaining_days;
            $new_end_date = date('Y-m-d H:i:s', strtotime("+{$total_days_to_add} days"));
            
            // Use MySQL package information for new subscription
            $package_name_for_mssql = $mysql_package_name_geo; // Use Georgian name for MSSQL
            $package_price_for_mssql = $mysql_package_price; // Use MySQL price
            $payment_amount = $payment['amount']; // From MySQL payment record
            
            // Get logged in user ID for CreatorUserID
            $creator_user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1; // Default to 1 if not set
            
            // Insert new SoldPackages record
            $mysql_package_id = $payment['package_id']; // Use MySQL package ID
            
            $sql_insert = "INSERT INTO SoldPackages (ClientID, PackageID, SaleTypeID, Price, PayedAmount, PaymentTypeID, SaleParcent, CreatorUserID, VisitsCount, VisitsLeft, EndDate, Expired) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $params_insert = array(
                $client_id,               // ClientID
                $mysql_package_id,        // PackageID - using MySQL package ID
                63,                       // SaleTypeID
                $package_price_for_mssql, // Price from MySQL
                $payment_amount,          // PayedAmount from payment
                $payment_type_id,         // PaymentTypeID (15=Flitt/app, 7=Unipay/website)
                0,                        // SaleParcent
                $creator_user_id,         // CreatorUserID
                $visits_count,            // VisitsCount (0 for one-time, 999 for regular)
                $visits_left,             // VisitsLeft (1 for one-time, 999 for regular)
                $new_end_date,            // EndDate (new package duration + remaining days)
                0                         // Expired (0 = active)
            );
            
            $stmt_insert = sqlsrv_query($mssqlconn, $sql_insert, $params_insert);
            
            if ($stmt_insert === false) {
                $errors = sqlsrv_errors();
                error_log("MSSQL insert new SoldPackages error: " . print_r($errors, true));
                echo json_encode(['success' => false, 'message' => 'Failed to create new subscription in MSSQL database']);
                exit;
            }
            
            // Step 9: Mark payment as processed in MSSQL PaymentsWebsite
            if (markPaymentProcessed($payment_id)) {
                // Get client info for QR sending from MSSQL
                $client_info_sql = "SELECT IdNumber, Email FROM Clients WHERE ID = ?";
                $client_info_params = array($client_id);
                $client_info_stmt = sqlsrv_query($mssqlconn, $client_info_sql, $client_info_params);
                
                $client_id_number = '';
                $client_email = '';
                
                if ($client_info_stmt && $client_info_row = sqlsrv_fetch_array($client_info_stmt, SQLSRV_FETCH_ASSOC)) {
                    $client_id_number = $client_info_row['IdNumber'];
                    $client_email = $client_info_row['Email'];
                    sqlsrv_free_stmt($client_info_stmt);
                }
                
                // Automatically send QR code after successful payment processing
                $qr_sent = sendQrCodeAutomatically($client_id_number, $mobile_number, $client_email);
                
                // Automatically send program after successful payment processing
                $program_sent = sendProgramAutomatically($client_id_number, $mobile_number, $client_email);
                
                // Automatically send workouts after successful payment processing
                $workouts_sent = sendWorkoutsAutomatically($mobile_number, $client_email);
                
                $response_message = "Old subscription expired and new subscription created successfully!";
                
                // Update response message with all sending statuses
                if ($qr_sent) {
                    $response_message .= " QR code sent automatically.";
                } else {
                    $response_message .= " QR code sending failed - please send manually.";
                }
                
                if ($program_sent) {
                    $response_message .= " Program sent automatically.";
                } else {
                    $response_message .= " Program sending failed.";
                }
                
                if ($workouts_sent) {
                    $response_message .= " Workouts sent automatically.";
                } else {
                    $response_message .= " Workouts sending failed.";
                }
                
                echo json_encode([
                    'success' => true, 
                    'message' => $response_message,
                    'qr_sent' => $qr_sent,
                    'program_sent' => $program_sent,
                    'workouts_sent' => $workouts_sent,
                    'details' => [
                        'client_id' => $client_id,
                        'old_subscription_id' => $active_subscription_id,
                        'package_name_geo' => $mysql_package_name_geo,
                        'package_name_eng' => $mysql_package_name_eng,
                        'new_package_duration_days' => $days_to_add,
                        'remaining_days_from_old' => $remaining_days,
                        'total_days_added' => $total_days_to_add,
                        'new_end_date' => $new_end_date,
                        'action' => 'expired_and_created_new'
                    ]
                ]);
            } else {
                echo json_encode([
                    'success' => false, 
                    'message' => 'New subscription created but failed to update payment status'
                ]);
            }
            
            sqlsrv_free_stmt($stmt_expire);
            sqlsrv_free_stmt($stmt_insert);
            
        } else {
            // No active subscription - create new one
            if ($is_one_time_package) {
                // One-time package: 1 day from now
                $end_date = date('Y-m-d H:i:s', strtotime("+1 day"));
            } else {
                // Regular package: calculate based on months
                $end_date = date('Y-m-d H:i:s', strtotime("+{$days_to_add} days"));
            }
            
            // Use MySQL package information
            $package_name_for_mssql = $mysql_package_name_geo; // Use Georgian name for MSSQL
            $package_price_for_mssql = $mysql_package_price; // Use MySQL price
            $payment_amount = $payment['amount']; // From MySQL payment record
            
            // Step 7: Get logged in user ID for CreatorUserID
            $creator_user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1; // Default to 1 if not set
            
            // Step 8: Insert into SoldPackages using MySQL package information
            $mysql_package_id = $payment['package_id']; // Use MySQL package ID
            
            $sql_insert = "INSERT INTO SoldPackages (ClientID, PackageID, SaleTypeID, Price, PayedAmount, PaymentTypeID, SaleParcent, CreatorUserID, VisitsCount, VisitsLeft, EndDate, Expired) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $params_insert = array(
                $client_id,               // ClientID
                $mysql_package_id,        // PackageID - using MySQL package ID
                63,                       // SaleTypeID
                $package_price_for_mssql, // Price from MySQL
                $payment_amount,          // PayedAmount from payment
                $payment_type_id,         // PaymentTypeID (15=Flitt/app, 7=Unipay/website)
                0,                        // SaleParcent
                $creator_user_id,         // CreatorUserID
                $visits_count,            // VisitsCount (0 for one-time, 999 for regular)
                $visits_left,             // VisitsLeft (1 for one-time, 999 for regular)
                $end_date,                // EndDate
                0                         // Expired (0 = active)
            );
            
            $stmt_insert = sqlsrv_query($mssqlconn, $sql_insert, $params_insert);
            
            if ($stmt_insert === false) {
                $errors = sqlsrv_errors();
                error_log("MSSQL insert SoldPackages error: " . print_r($errors, true));
                echo json_encode(['success' => false, 'message' => 'Failed to create subscription in MSSQL database']);
                exit;
            }
            
            // Step 9: Handle ClientCards for new subscription
            // Check if ClientCard exists for this client
            $sql_check_card = "SELECT * FROM ClientCards WHERE ClientID = ?";
            $params_check_card = array($client_id);
            $stmt_check_card = sqlsrv_query($mssqlconn, $sql_check_card, $params_check_card);
            
            if ($stmt_check_card === false) {
                $errors = sqlsrv_errors();
                error_log("MSSQL ClientCards check error: " . print_r($errors, true));
                echo json_encode(['success' => false, 'message' => 'Failed to check client card information']);
                exit;
            }
            
            $card_row = sqlsrv_fetch_array($stmt_check_card, SQLSRV_FETCH_ASSOC);
            
            if ($card_row) {
                // ClientCard exists - update CardNumber, ActiveIndevice, and PaymentTypeID
                // Remove '995' prefix from mobile number if present for CardNumber
                $card_number = $mobile_number;
                if (strpos($card_number, '995') === 0) {
                    $card_number = substr($card_number, 3);
                }
                
                $sql_update_card = "UPDATE ClientCards SET CardNumber = ?, ActiveIndevice = 1, PaymentTypeID = ? WHERE ClientID = ?";
                $params_update_card = array($card_number, $payment_type_id, $client_id);
                $stmt_update_card = sqlsrv_query($mssqlconn, $sql_update_card, $params_update_card);
                
                if ($stmt_update_card === false) {
                    $errors = sqlsrv_errors();
                    error_log("MSSQL ClientCards update error: " . print_r($errors, true));
                    echo json_encode(['success' => false, 'message' => 'Failed to update client card']);
                    exit;
                }
                
                error_log("ClientCard updated for client ID: {$client_id} - CardNumber: {$card_number}, ActiveIndevice: 1, PaymentTypeID: {$payment_type_id}");
                sqlsrv_free_stmt($stmt_update_card);
            } else {
                // ClientCard doesn't exist - create new one
                // Remove '995' prefix from mobile number if present
                $card_number = $mobile_number;
                if (strpos($card_number, '995') === 0) {
                    $card_number = substr($card_number, 3);
                }
                
                $sql_insert_card = "INSERT INTO ClientCards (CardNumber, Price, ActiveIndevice, ClientID, PaymentTypeID) VALUES (?, ?, ?, ?, ?)";
                $params_insert_card = array(
                    $card_number,  // CardNumber (mobile number without 995)
                    0,             // Price
                    1,             // ActiveIndevice
                    $client_id,    // ClientID
                    $payment_type_id // PaymentTypeID (15=Flitt/app, 7=Unipay/website)
                );
                $stmt_insert_card = sqlsrv_query($mssqlconn, $sql_insert_card, $params_insert_card);
                
                if ($stmt_insert_card === false) {
                    $errors = sqlsrv_errors();
                    error_log("MSSQL ClientCards insert error: " . print_r($errors, true));
                    echo json_encode(['success' => false, 'message' => 'Failed to create client card']);
                    exit;
                }
                
                error_log("New ClientCard created for client ID: {$client_id}, CardNumber: {$card_number}");
                sqlsrv_free_stmt($stmt_insert_card);
            }
            
            sqlsrv_free_stmt($stmt_check_card);
            
            // Step 10: Mark payment as processed in MSSQL PaymentsWebsite
            if (markPaymentProcessed($payment_id)) {
                // Get client info for QR sending from MSSQL
                $client_info_sql = "SELECT IdNumber, Email FROM Clients WHERE ID = ?";
                $client_info_params = array($client_id);
                $client_info_stmt = sqlsrv_query($mssqlconn, $client_info_sql, $client_info_params);
                
                $client_id_number = '';
                $client_email = '';
                
                if ($client_info_stmt && $client_info_row = sqlsrv_fetch_array($client_info_stmt, SQLSRV_FETCH_ASSOC)) {
                    $client_id_number = $client_info_row['IdNumber'];
                    $client_email = $client_info_row['Email'];
                    sqlsrv_free_stmt($client_info_stmt);
                }
                
                // Automatically send QR code after successful payment processing
                $qr_sent = sendQrCodeAutomatically($client_id_number, $mobile_number, $client_email);
                
                // Automatically send program after successful payment processing
                $program_sent = sendProgramAutomatically($client_id_number, $mobile_number, $client_email);
                
                // Automatically send workouts after successful payment processing
                $workouts_sent = sendWorkoutsAutomatically($mobile_number, $client_email);
                
                $response_message = "New subscription created successfully!";
                
                // Update response message with all sending statuses
                if ($qr_sent) {
                    $response_message .= " QR code sent automatically.";
                } else {
                    $response_message .= " QR code sending failed - please send manually.";
                }
                
                if ($program_sent) {
                    $response_message .= " Program sent automatically.";
                } else {
                    $response_message .= " Program sending failed.";
                }
                
                if ($workouts_sent) {
                    $response_message .= " Workouts sent automatically.";
                } else {
                    $response_message .= " Workouts sending failed.";
                }
                
                echo json_encode([
                    'success' => true, 
                    'message' => $response_message,
                    'qr_sent' => $qr_sent,
                    'program_sent' => $program_sent,
                    'workouts_sent' => $workouts_sent,
                    'details' => [
                        'client_id' => $client_id,
                        'package_name_geo' => $mysql_package_name_geo,
                        'package_name_eng' => $mysql_package_name_eng,
                        'months_added' => $months_to_add,
                        'end_date' => $end_date,
                        'mysql_package_id' => $mysql_package_id,
                        'action' => 'created',
                        'card_number' => isset($card_number) ? $card_number : 'existing',
                        'card_action' => $card_row ? 'updated' : 'created'
                    ]
                ]);
            } else {
                echo json_encode([
                    'success' => false, 
                    'message' => 'Subscription created but failed to update payment status'
                ]);
            }
            
            sqlsrv_free_stmt($stmt_insert);
        }
        
        // Clean up MSSQL statements
        sqlsrv_free_stmt($stmt_packages);
    
    // Clean up
    sqlsrv_close($mssqlconn);
    if (is_object($conn) && method_exists($conn, 'close')) {
        $conn->close();
    }
    
} catch (Exception $e) {
    // Log the detailed error
    error_log("Error in mark_payment_processed.php: " . $e->getMessage());
    error_log("Error trace: " . $e->getTraceAsString());

    // Release the in_progress lock so the payment can be retried
    if (isset($payment_id) && isset($mssqlconn) && $mssqlconn) {
        sqlsrv_query($mssqlconn, "UPDATE PaymentsWebsite SET in_progress = 0 WHERE id = ? AND processed = 0", [$payment_id]);
    }

    // Clean up any open resources
    if (isset($stmt) && is_object($stmt) && method_exists($stmt, 'close')) {
        $stmt->close();
    }
    if (isset($update_stmt) && is_object($update_stmt) && method_exists($update_stmt, 'close')) {
        $update_stmt->close();
    }
    if (isset($mssqlconn)) {
        sqlsrv_close($mssqlconn);
    }
    if (isset($conn) && is_object($conn) && method_exists($conn, 'close')) {
        $conn->close();
    }

    // Return JSON error response
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while processing the request',
        'error_details' => $e->getMessage()
    ]);
} catch (Error $e) {
    // Handle fatal errors
    error_log("Fatal error in mark_payment_processed.php: " . $e->getMessage());

    // Release the in_progress lock so the payment can be retried
    if (isset($payment_id) && isset($mssqlconn) && $mssqlconn) {
        sqlsrv_query($mssqlconn, "UPDATE PaymentsWebsite SET in_progress = 0 WHERE id = ? AND processed = 0", [$payment_id]);
    }

    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'A fatal error occurred while processing the request'
    ]);
}
?>
