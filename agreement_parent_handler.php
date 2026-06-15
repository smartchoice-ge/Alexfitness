<?php
require_once __DIR__ . '/tcpdf_loader.php';

if (!loadTcpdfLibrary(__DIR__)) {
    http_response_code(500);
    echo json_encode(array('status' => 'error', 'message' => 'Server configuration error - missing PDF dependency'));
    exit;
}

include_once 'db_connection.php';
include_once 'mssql_connection.php';
include_once 'mssql_packages_payments_helper.php';
date_default_timezone_set('Asia/Tbilisi');

function generateParentPDF($id_number, $html_content_1) {
    $pdf = new TCPDF();
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Synergy Gym');
    $pdf->SetTitle('User Agreement');
    $pdf->SetSubject('User Agreement Form');
    $pdf->SetKeywords('TCPDF, PDF, agreement');
    $pdf->AddPage();
    $pdf->SetFont('dejavusans', '', 11);
    $pdf->writeHTML($html_content_1);
    $docsDir = rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/docs/';
    if (!is_dir($docsDir)) {
        mkdir($docsDir, 0755, true);
    }
    $pdf->Output($docsDir . $id_number . 'parent.pdf', 'F');
}

function generatePDF($id_number, $html_content) {
    $pdf = new TCPDF();
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Synergy Gym');
    $pdf->SetTitle('User Agreement');
    $pdf->SetSubject('User Agreement Form');
    $pdf->SetKeywords('TCPDF, PDF, agreement');
    $pdf->AddPage();
    $pdf->SetFont('dejavusans', '', 11);
    $pdf->writeHTML($html_content);
    $docsDir = rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/docs/';
    if (!is_dir($docsDir)) {
        mkdir($docsDir, 0755, true);
    }
    $pdf->Output($docsDir . $id_number . '.pdf', 'F');
}

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
    $apikey = 'e774aad67ecaba4ba90b86da65be10d9';
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

// Function to log MSSQL save operations
function logMSSQLSave($conn, $mysql_client_id, $id_number, $full_name, $mobile_number, $email, $status, $error_message = null, $error_details = null, $mssql_client_id = null) {
    try {
        if (!$conn || !method_exists($conn, 'prepare')) {
            return;
        }
        $log_stmt = $conn->prepare("INSERT INTO mssql_save_logs (mysql_client_id, id_number, full_name, mobile_number, email, status, error_message, error_details, mssql_client_id, operation_type) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'auto_save')");
        if ($log_stmt) {
            $log_stmt->bind_param("isssssssi", $mysql_client_id, $id_number, $full_name, $mobile_number, $email, $status, $error_message, $error_details, $mssql_client_id);
            $log_stmt->execute();
            $log_stmt->close();
        } else {
            error_log("Failed to prepare log statement: " . $conn->error);
        }
    } catch (Exception $e) {
        error_log("Exception in logMSSQLSave: " . $e->getMessage());
    }
}

// Function to automatically save client to MSSQL Clients table when added
function saveClientToMSSQL($conn, $mssqlconn, $mysql_client_id) {
    global $mssqlconn;
    
    // Get client data from MSSQL ClientDetailsWebsite
    $client_data = getClientDetailsById($mysql_client_id);
    
    if (!$client_data) {
        $error_msg = "Client not found in ClientDetailsWebsite for auto-save to MSSQL Clients";
        error_log($error_msg);
        logMSSQLSave($conn, $mysql_client_id, null, null, null, null, 'failed', $error_msg, null, null);
        return false;
    }
    
    $id_number = $client_data['id_number'];
    $full_name = $client_data['full_name'];
    $birth_date = $client_data['birth_date'];
    $mobile_number = $client_data['mobile_number'];
    $email = $client_data['email'];
    $picurl = !empty($client_data['picurl']) ? '/' . $client_data['picurl'] : null;
    
    // Check MSSQL connection
    if (!$mssqlconn) {
        $error_msg = "MSSQL connection not available for auto-save";
        error_log($error_msg);
        logMSSQLSave($conn, $mysql_client_id, $id_number, $full_name, $mobile_number, $email, 'failed', $error_msg, 'MSSQL connection is null', null);
        return false;
    }
    
    // Ensure phone has 995 prefix
    $phone_for_mssql = $mobile_number;
    if (strpos($mobile_number, '995') !== 0) {
        $phone_for_mssql = '995' . $mobile_number;
    }
    
    // Check if client already exists in MSSQL (by phone number - both formats)
    $mssql_phone_without_995 = (strpos($phone_for_mssql, '995') === 0) ? substr($phone_for_mssql, 3) : $phone_for_mssql;
    $checkSql = "SELECT ID FROM Clients WHERE Phone = ? OR Phone = ?";
    $checkStmt = sqlsrv_query($mssqlconn, $checkSql, array($phone_for_mssql, $mssql_phone_without_995));
    
    if ($checkStmt === false) {
        $errors = sqlsrv_errors();
        $error_msg = "MSSQL check error during auto-save";
        $error_details = print_r($errors, true);
        error_log($error_msg . ": " . $error_details);
        logMSSQLSave($conn, $mysql_client_id, $id_number, $full_name, $mobile_number, $email, 'failed', $error_msg, $error_details, null);
        return false;
    }
    
    if ($existing_client = sqlsrv_fetch_array($checkStmt)) {
        $existing_id = $existing_client['ID'];
        error_log("Client already exists in MSSQL (phone: {$phone_for_mssql}), ID: {$existing_id}");
        logMSSQLSave($conn, $mysql_client_id, $id_number, $full_name, $mobile_number, $email, 'already_exists', "Client already exists with phone: {$phone_for_mssql}", null, $existing_id);
        sqlsrv_free_stmt($checkStmt);
        return true; // Not an error, client already exists
    }
    sqlsrv_free_stmt($checkStmt);
    
    // Insert new client to MSSQL
    $sql = "
    INSERT INTO Clients (FullName, IdNumber, BirthDate, Phone, Email, SexID, CardNumber, IndeviceID, ProfilePicture, SendSms, IsActive, SupplierID, ClientTypeID, CreatorUser, BlackListed, ClientParentOrgID, GroupSync, Comment)
    SELECT ?, ?, ?, ?, ?, 1, '', 
        COALESCE((SELECT MAX(InDeviceID) + 1 FROM Clients), 1),
        ?, 1, 1, 2, 2, 1, 0, 19, 0, ''
    ";
    
    $params = array($full_name, $id_number, $birth_date, $phone_for_mssql, $email, $picurl);
    $stmt = sqlsrv_query($mssqlconn, $sql, $params);
    
    if (!$stmt) {
        $errors = sqlsrv_errors();
        $error_msg = "MSSQL insert error during auto-save";
        $error_details = print_r($errors, true);
        error_log($error_msg . ": " . $error_details);
        logMSSQLSave($conn, $mysql_client_id, $id_number, $full_name, $mobile_number, $email, 'failed', $error_msg, $error_details, null);
        return false;
    }
    
    // Get the inserted Client ID
    $fetchSql = "SELECT ID FROM Clients WHERE Phone = ?";
    $fetchStmt = sqlsrv_query($mssqlconn, $fetchSql, array($phone_for_mssql));
    
    if ($fetchStmt && $row = sqlsrv_fetch_array($fetchStmt, SQLSRV_FETCH_ASSOC)) {
        $client_id = $row['ID'];
        error_log("Client auto-saved to MSSQL with ID: {$client_id}");
        
        // Send welcome SMS via sender.ge API (like tonus does)
        $welcome_message = 'Welcome to Synergy Gym, Mokharulebi vart rom gakhdit chveni gundis tsevri.';
        $sms_sent = sendSMSViaSenderGE($phone_for_mssql, $welcome_message);
        
        // Insert SMS log to track the send attempt
        $smsSql = "INSERT INTO SMSLog (ClientID, SMSText, SmsSentStatusID, PhoneNumber, UserID) VALUES (?, ?, ?, ?, ?)";
        $smsParams = array($client_id, $welcome_message, ($sms_sent ? 1 : 2), $phone_for_mssql, 1);
        $smsStmt = sqlsrv_query($mssqlconn, $smsSql, $smsParams);
        
        if ($smsStmt) {
            sqlsrv_free_stmt($smsStmt);
        }
        sqlsrv_free_stmt($fetchStmt);
        sqlsrv_free_stmt($stmt);
        
        // Log successful save
        logMSSQLSave($conn, $mysql_client_id, $id_number, $full_name, $mobile_number, $email, 'success', null, null, $client_id);
        return true;
    }
    
    sqlsrv_free_stmt($stmt);
    
    // Failed to fetch client ID after insert
    $error_msg = "Client inserted but failed to retrieve MSSQL client ID";
    error_log($error_msg);
    logMSSQLSave($conn, $mysql_client_id, $id_number, $full_name, $mobile_number, $email, 'failed', $error_msg, 'Insert succeeded but fetch failed', null);
    return false;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_number = $_POST['id_number'];
    $full_name = $_POST['full_name'];
    $mobile_number = $_POST['mobile_number'];
    $email = $_POST['email'];
    $agree_to_agreement = isset($_POST['agree_to_agreement']) ? 1 : 0;
    $agreement_date = date('Y-m-d H:i:s');
    $year = date('Y', strtotime($agreement_date));
    $date_without_time = date('Y-m-d');
    $birth_date = $_POST['birth_year'] . '-' . $_POST['birth_month'] . '-' . str_pad($_POST['birth_day'], 2, '0', STR_PAD_LEFT);
    $user_ip = $_SERVER['REMOTE_ADDR'];
    $address = $_POST['address'];
    $child_name = $_POST['child_name'];
    $child_id = $_POST['child_id'];

    // Check if client exists in MSSQL ClientDetailsWebsite
    $user_data = getClientDetailsByIdNumber($id_number);

    if ($user_data) {
        if ($user_data['agreed'] == 10) {
            echo json_encode(array('status' => 'error', 'message' => 'შენ უკვე შევსებული გაქვს ფორმა'));
        } else {
            // Update existing client in MSSQL ClientDetailsWebsite
            $updateData = [
                'full_name' => $full_name,
                'mobile_number' => $mobile_number,
                'agreed' => $agree_to_agreement,
                'email' => $email,
                'agreement_date' => $agreement_date,
                'birth_date' => $birth_date,
                'user_ip' => $user_ip,
                'home_address' => $address,
                'child_name' => $child_name,
                'child_id' => $child_id
            ];
            
            if (updateClientDetailsByIdNumber($id_number, $updateData)) {
                $user_id = $user_data['id']; // Get the user ID from the fetched data
                
                // Try to save client to MSSQL Clients table
                try {
                    saveClientToMSSQL($conn, $mssqlconn, $user_id);
                } catch (Exception $e) {
                    error_log("Exception during MSSQL Clients save on update: " . $e->getMessage());
                }
                
                ob_start();
                include('agreement_parent_template.php');
                $html_content_1 = ob_get_clean();
                generateParentPDF($id_number, $html_content_1);

                ob_start();
                include('agreement_template.php');
                $html_content = ob_get_clean();
                generatePDF($id_number, $html_content);


                echo json_encode(array('status' => 'success', 'message' => 'Record updated successfully'));
            } else {
                echo json_encode(array('status' => 'error', 'message' => 'Error updating record in ClientDetailsWebsite'));
            }
        }
    } else {
        // Insert new client to MSSQL ClientDetailsWebsite
        $insertData = [
            'id_number' => $id_number,
            'full_name' => $full_name,
            'mobile_number' => $mobile_number,
            'email' => $email,
            'agreed' => $agree_to_agreement,
            'agreement_date' => $agreement_date,
            'user_ip' => $user_ip,
            'birth_date' => $birth_date,
            'home_address' => $address,
            'child_name' => $child_name,
            'child_id' => $child_id
        ];
        
        $user_id = insertClientDetails($insertData);
        
        if ($user_id) {
            // Automatically save client to MSSQL Clients table when added
            try {
                saveClientToMSSQL($conn, $mssqlconn, $user_id);
            } catch (Exception $e) {
                error_log("Exception during MSSQL Clients save on insert: " . $e->getMessage());
                // Log the failure even if saveClientToMSSQL didn't log it
                try {
                    logMSSQLSave($conn, $user_id, $id_number, $full_name, $mobile_number, $email, 'failed', 'Exception during save: ' . $e->getMessage(), $e->getTraceAsString(), null);
                } catch (Exception $logEx) {
                    error_log("Failed to log MSSQL save failure: " . $logEx->getMessage());
                }
            }
            
            ob_start();
            include('agreement_parent_template.php');
            $html_content_1 = ob_get_clean();
            generateParentPDF($id_number, $html_content_1);

            ob_start();
            include('agreement_template.php');
            $html_content = ob_get_clean();
            generatePDF($id_number, $html_content);

            echo json_encode(array('status' => 'success', 'message' => 'New record created successfully'));
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'Error inserting into ClientDetailsWebsite'));
        }
    }
    exit;
}
?>