<?php
/**
 * Unified Email Sender
 * Tries Zoho API first (bypasses SMTP port blocking), falls back to SMTP if needed
 */

require_once __DIR__ . '/zoho_email/send_via_api.php';
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/params.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

/**
 * Send email using Zoho API (preferred) or SMTP fallback
 * 
 * @param string $to Recipient email
 * @param string $subject Email subject
 * @param string $htmlBody HTML email body
 * @param string $from From address (default: info@synergy-gym.ge)
 * @return array ['success' => bool, 'method' => string, 'error' => string]
 */
function sendUnifiedEmail($to, $subject, $htmlBody, $from = null) {
    global $user, $zoho_account_id;
    
    if ($from === null) {
        $from = $user;
    }
    
    // Use Zoho API exclusively
    if (!empty($zoho_account_id)) {
        $result = sendEmailViaZohoAPI($to, $subject, $htmlBody, $from);
        if ($result['success']) {
            return [
                'success' => true,
                'method' => 'zoho_api',
                'error' => null
            ];
        }
        
        return [
            'success' => false,
            'method' => 'zoho_api',
            'error' => $result['error'] ?? 'Unknown error'
        ];
    }
    
    return [
        'success' => false,
        'method' => 'zoho_api',
        'error' => 'Zoho Account ID not configured'
    ];
}

/**
 * Send email via SMTP (PHPMailer)
 */
function sendEmailViaSMTP($to, $subject, $htmlBody, $from = null) {
    global $host, $user, $pass;
    
    if ($from === null) {
        $from = $user;
    }
    
    $mail = new PHPMailer(true);
    
    try {
        $mail->isSMTP();
        $mail->Host = $host;
        $mail->SMTPAuth = true;
        $mail->Username = $user;
        $mail->Password = $pass;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->CharSet = 'UTF-8';
        $mail->Timeout = 10;
        
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        
        $mail->setFrom($from, 'synergy-gym.ge');
        $mail->addAddress($to);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $htmlBody;
        
        $mail->send();
        
        return [
            'success' => true,
            'method' => 'smtp',
            'error' => null
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'method' => 'smtp',
            'error' => $e->getMessage()
        ];
    }
}
