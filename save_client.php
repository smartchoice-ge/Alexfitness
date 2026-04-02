<?php

// Report all PHP errors
error_reporting(-1);

// Same as error_reporting(E_ALL);
ini_set('error_reporting', E_ALL);

include_once 'mssql_connection.php';

// Function to clean mobile number for SMS API (remove country codes)
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

// Function to send SMS via sender.ge API (like tonus does)
function sendSMSViaSenderGE($phone_number, $message) {
    $apikey = '0f132d23f162ca06a769128a5e866cf1';
    $url = "https://sender.ge/api/send.php";
    
    // Clean the phone number for SMS API
    $cleanMobile = cleanMobileForSMS($phone_number);
    error_log("SMS Send Attempt - Original: {$phone_number}, Cleaned: {$cleanMobile}, Message: {$message}");
    
    $fields = [
        'apikey'      => $apikey,
        'smsno'       => 2,
        'destination' => $cleanMobile,
        'content'     => $message
    ];
    
    $fields_string = http_build_query($fields);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    $responseData = json_decode($response, true);
    error_log("SMS API Response - HTTP Code: {$httpCode}, Response: " . $response);
    
    // Check if SMS was sent successfully
    if ($httpCode == 200 && isset($responseData['data'][0]['statusId']) && $responseData['data'][0]['statusId'] == 1) {
        error_log("SMS sent successfully to {$cleanMobile}");
        return true;
    } else {
        error_log("SMS send failed - HTTP: {$httpCode}, Response: " . $response);
        return false;
    }
}

// Handle the save request via AJAX
if (isset($_POST['save_client'])) {
    // Retrieve data from the AJAX request
    $fullName = $_POST['fullName'];
    $idNumber = $_POST['idNumber'];
    $birthDate = $_POST['birthDate'];
    $phone = isset($_POST['phone']) ? '995' . $_POST['phone'] : null;
    $email = $_POST['email'];
    $picurl = isset($_POST['picurl']) && !empty($_POST['picurl']) ? '/' . $_POST['picurl'] : null;
    $userId = $_POST['userId'];

    // Check if the client with the given IdNumber already exists
    $checkSql = "SELECT 1 FROM Clients WHERE IdNumber = ?";
    $checkStmt = sqlsrv_query($mssqlconn, $checkSql, array($idNumber));
    $stmt = null; // Initialize to avoid undefined variable errors

    if (sqlsrv_fetch_array($checkStmt)) {
        echo "Already saved";
    } else {
        $sql = "
        INSERT INTO Clients (FullName, IdNumber, BirthDate, Phone, Email, SexID, CardNumber, IndeviceID, ProfilePicture, SendSms, IsActive, SupplierID, ClientTypeID, CreatorUser, BlackListed, ClientParentOrgID, GroupSync, Comment, RecordDate)
        SELECT ?, ?, ?, ?, ?, 1, '', 
            COALESCE((SELECT MAX(InDeviceID) + 1 FROM Clients), 1),
            ?, 1, 1, 2, 2, ?, 0, 19, 0, '', GETDATE()
        ";
    
        $params = array($fullName, $idNumber, $birthDate, $phone, $email, $picurl, $userId);
        $stmt = sqlsrv_query($mssqlconn, $sql, $params);
    
        if (!$stmt) {
            echo "Error saving client: " . print_r(sqlsrv_errors(), true);
        } else {
            // Get the inserted Client ID
            $fetchSql = "SELECT ID FROM Clients WHERE IdNumber = ?";
            $fetchParams = array($idNumber);
            $fetchStmt = sqlsrv_query($mssqlconn, $fetchSql, $fetchParams);
    
            if ($fetchStmt && $row = sqlsrv_fetch_array($fetchStmt, SQLSRV_FETCH_ASSOC)) {
                $clientID = $row['ID'];
    
                // Send welcome SMS via sender.ge API (like tonus does)
                $welcome_message = 'Welcome to Synergy Gym, Mokharulebi vart rom gakhdit chveni gundis tsevri.';
                $sms_sent = sendSMSViaSenderGE($phone, $welcome_message);
                
                // Insert SMS log to track the send attempt
                $smsSql = "INSERT INTO SMSLog (ClientID, SMSText, SmsSentStatusID, PhoneNumber, UserID) VALUES (?, ?, ?, ?, ?)";
                $smsParams = array($clientID, $welcome_message, ($sms_sent ? 1 : 2), $phone, $userId);
                $smsStmt = sqlsrv_query($mssqlconn, $smsSql, $smsParams);
    
                if (!$smsStmt) {
                    echo "Error saving SMS log: " . print_r(sqlsrv_errors(), true);
                } else {
                    echo "Client saved successfully and SMS log recorded.";
                }
                
                // Clean up SMS statement
                if (isset($smsStmt) && $smsStmt) {
                    sqlsrv_free_stmt($smsStmt);
                }
            } else {
                echo "Error fetching client ID: " . print_r(sqlsrv_errors(), true);
            }
            
            // Clean up fetch statement
            if (isset($fetchStmt) && $fetchStmt) {
                sqlsrv_free_stmt($fetchStmt);
            }
        }
    }
    

    // Clean up
    sqlsrv_free_stmt($checkStmt);
    if ($stmt !== null) {
        sqlsrv_free_stmt($stmt);
    }
    sqlsrv_close($mssqlconn);
    exit;
}
?>