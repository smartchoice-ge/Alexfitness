<?php
/**
 * Send email using Zoho Mail API (bypasses SMTP port blocking)
 * 
 * Usage:
 *   require_once 'zoho_email/send_via_api.php';
 *   $result = sendEmailViaZohoAPI('user@example.com', 'Subject', 'HTML content');
 */

require_once __DIR__ . '/zoho_api_helper.php';
require_once __DIR__ . '/../params.php';

function sendEmailViaZohoAPI($to, $subject, $htmlContent, $from = null) {
    global $zoho_account_id, $user;
    
    if (empty($zoho_account_id)) {
        return [
            'success' => false,
            'error' => 'Zoho Account ID not configured in params.php. Please visit /zoho_email/get_account_id.php'
        ];
    }
    
    if ($from === null) {
        $from = $user; // Use default from params.php
    }
    
    try {
        $api = new ZohoMailAPI();
        $response = $api->sendEmail($zoho_account_id, $to, $subject, $htmlContent, $from);
        
        return [
            'success' => true,
            'response' => $response
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}
