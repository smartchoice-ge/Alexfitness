<?php
// Add immediate error output for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display to prevent JSON corruption

require 'mssql_connection.php';
require 'mssql_packages_payments_helper.php';
require 'email_sender.php';
include_once 'params.php';

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    
    if (empty($email)) {
        die(json_encode(['status' => 'error', 'message' => 'Please provide your email address']));
    }
    
    try {
        // Debug logging
        error_log("Recovery attempt - Email: " . $email);
        
        // Search by email - use MSSQL ClientDetailsWebsite table
        $userRecord = getClientDetailsByEmail($email);
        
        error_log("Query executed successfully");
        
        if (!$userRecord) {
            error_log("No rows found for email: " . $email);
            die(json_encode(['status' => 'error', 'message' => 'No account found with the provided email address']));
        }
        
        error_log("User found, fetching data");
        error_log("User data: " . print_r($userRecord, true));
        
        // Verify user has email
        if (empty($userRecord['email'])) {
            die(json_encode(['status' => 'error', 'message' => 'No email address associated with this account. Please contact gym administration.']));
        }
        
        // Generate credentials
        $username = $userRecord['mobile_number']; // Phone number (may include 995)
        $password = $userRecord['id_number']; // ID number
        $fullName = $userRecord['full_name'];
        
        // Remove 995 prefix for username display if present
        if (str_starts_with($username, '995')) {
            $username = substr($username, 3);
        }
        
        // Send email with credentials
        $emailSent = sendCredentialsEmail($userRecord['email'], $fullName, $username, $password);
        
        if ($emailSent === true) {
            // Log the email
            $stmt_log = $conn->prepare("INSERT INTO emails (email, date, content) VALUES (?, NOW(), ?)");
            $content = 'credentials recovery email';
            $stmt_log->bind_param("ss", $userRecord['email'], $content);
            $stmt_log->execute();
            
            echo json_encode([
                'status' => 'success', 
                'message' => 'Your login credentials have been sent to your email address.'
            ]);
        } elseif ($emailSent === false) {
            echo json_encode([
                'status' => 'error', 
                'message' => 'Failed to send email. Please try again or contact support.'
            ]);
        } else {
            // $emailSent contains error message
            echo json_encode([
                'status' => 'error', 
                'message' => 'Email error: ' . $emailSent
            ]);
        }
        
    } catch (Exception $e) {
        error_log("Credential recovery error: " . $e->getMessage());
        echo json_encode([
            'status' => 'error', 
            'message' => 'Debug Error: ' . $e->getMessage() . ' | File: ' . $e->getFile() . ' | Line: ' . $e->getLine()
        ]);
    }
}

function sendCredentialsEmail($email, $fullName, $username, $password) {
    global $user;
    
    $emailBody = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Luka Qaliashvili, ID: 0172409681 - Login Credentials</title>
        </head>
        <body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #1a1a1a; color: white;">
            <div style="max-width: 600px; margin: auto; padding: 40px 20px; text-align: center;">
                <h1 style="color: #FFD700;">Your Login Credentials</h1>
                <p>Hello <strong>' . htmlspecialchars($fullName) . '</strong>,</p>
                <p>Here are your login credentials for synergy-gym.ge:</p>
                
                <div style="background: rgba(255, 255, 255, 0.1); padding: 25px; border-radius: 10px; margin: 20px 0; border: 2px solid #FFD700;">
                    <p><strong style="color: #FFD700;">Username:</strong><br>
                    <span style="font-size: 24px; font-weight: bold; color: #FFD700;">' . htmlspecialchars($username) . '</span></p>
                    <p><strong style="color: #FFD700;">Password:</strong><br>
                    <span style="font-size: 24px; font-weight: bold; color: #FFD700;">' . htmlspecialchars($password) . '</span></p>
                </div>
                
                <p><a href="/user_profile/auth.php?lang=en" 
                   style="background: #FFD700; color: #000; padding: 15px 30px; text-decoration: none; border-radius: 5px; font-weight: bold;">
                    Login Now
                </a></p>
            </div>
        </body>
        </html>';
    
    $result = sendUnifiedEmail($email, 'Your Login Credentials - synergy-gym.ge', $emailBody, $user);
    
    if ($result['success']) {
        return true;
    } else {
        error_log("Email sending error: " . $result['error']);
        return "Email error: " . $result['error'];
    }
}
?>
