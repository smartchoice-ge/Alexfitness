<?php

// Report all PHP errors
error_reporting(-1);

// Same as error_reporting(E_ALL);
ini_set('error_reporting', E_ALL);

include_once 'mssql_connection.php';

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
    
                // Insert into SMSLog
                $smsSql = "INSERT INTO SMSLog (ClientID, SMSText, SmsSentStatusID, PhoneNumber, UserID) VALUES (?, ?, ?, ?, ?)";
                $smsParams = array($clientID, 'Welcome to Synergy Gym, Mokharulebi vart rom gakhdit chveni gundis tsevri.', 1, $phone, $userId);
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