<?php
session_start([
    'cookie_httponly' => true,
    'cookie_samesite' => 'Strict'
]);
header('Content-Type: application/json');

// Basic Authentication/Authorization Check
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access. Please login.']);
    exit;
}

if (strtoupper($_SERVER['REQUEST_METHOD']) !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit;
}

// Include MSSQL database connection
include_once '../mssql_connection.php';

$db_connection_error_for_settings = false;
if (!isset($mssqlconn) || $mssqlconn === false) {
    error_log("MSSQL Connection Error in send_promo_sms.php (for settings)");
    $db_connection_error_for_settings = true;
}

$firstName = isset($_POST['firstName']) ? trim($_POST['firstName']) : null;
$mobileNumber = isset($_POST['mobileNumber']) ? trim($_POST['mobileNumber']) : null;
$clientIdNum = isset($_POST['clientIdNum']) ? trim($_POST['clientIdNum']) : null;

if (empty($firstName)) {
    echo json_encode(['status' => 'error', 'message' => 'First name is required.']);
    exit;
}
if (empty($mobileNumber)) {
    echo json_encode(['status' => 'error', 'message' => 'Mobile number is required.']);
    exit;
}
if (empty($clientIdNum)) {
    echo json_encode(['status' => 'error', 'message' => 'Client ID Number is required for logging.']);
    exit;
}

if (!preg_match('/^(995)?[0-9]{9}$/', $mobileNumber)) {
    // Optional: Add stricter validation if needed
}

function transliterateGeorgianToLatin($georgianString) {
    if (empty($georgianString)) {
        return '';
    }

    // Try using intl Transliterator first (preferred method)
    if (extension_loaded('intl') && class_exists('Transliterator')) {
        $transliterator = Transliterator::create('Georgian-Latin/BGN');
        if ($transliterator) {
            $latinString = $transliterator->transliterate($georgianString);
            if ($latinString !== false) {
                return $latinString;
            }
        }
    }

    $geoToLatinMap = [
        'ა' => 'a', 'ბ' => 'b', 'გ' => 'g', 'დ' => 'd', 'ე' => 'e', 'ვ' => 'v', 'ზ' => 'z',
        'თ' => 't', 'ი' => 'i', 'კ' => 'k', 'ლ' => 'l', 'მ' => 'm', 'ნ' => 'n', 'ო' => 'o',
        'პ' => 'p', 'ჟ' => 'zh', 'რ' => 'r', 'ს' => 's', 'ტ' => 't', 'უ' => 'u', 'ფ' => 'f',
        'ქ' => 'q', 'ღ' => 'gh', 'ყ' => 'y', 'შ' => 'sh', 'ჩ' => 'ch', 'ც' => 'ts', 'ძ' => 'dz',
        'წ' => 'ts', 'ჭ' => 'ch', 'ხ' => 'kh', 'ჯ' => 'j', 'ჰ' => 'h',
    ];
    return strtr($georgianString, $geoToLatinMap);
}


// --- Fetch SMS main text from settings table ---
$promoMessageCore = ""; // Default message
$facebookLink = "https://www.facebook.com/SynergyGymTbilisi"; // Keep this part separate or also make it a setting
$nosms = "No2981To90775";

$latinFirstName = transliterateGeorgianToLatin($firstName);

if (!$db_connection_error_for_settings) {
    $setting_id = 6;
    $stmt_settings = sqlsrv_query($mssqlconn, "SELECT value FROM WebsiteSettings WHERE id = ?", array($setting_id));
    if ($stmt_settings) {
        $setting_row = sqlsrv_fetch_array($stmt_settings, SQLSRV_FETCH_ASSOC);
        if ($setting_row && isset($setting_row['value']) && !empty(trim($setting_row['value']))) {
            $promoMessageCore = trim($setting_row['value']);
            error_log("SMS promo text fetched from settings (ID: {$setting_id}).");
        } else {
            error_log("SMS promo text setting (ID: {$setting_id}) not found or empty. Using default.");
        }
        sqlsrv_free_stmt($stmt_settings);
    } else {
        error_log("Failed to query SMS setting (ID: {$setting_id})");
    }
} else {
    error_log("Cannot fetch SMS promo text from settings due to DB connection error. Using default.");
}
// --- End Fetch SMS main text ---

// Construct the final SMS message
$messageText = "Hello " . ucfirst(htmlspecialchars($latinFirstName)) . ' '. $promoMessageCore . "\n\n" . $nosms;

$apikey = 'SYNERGY_SMS_API_KEY'; 

$url = "https://sender.ge/api/send.php";
$fields = [
    'apikey' => $apikey,
    'smsno' => 2,
    'destination' => $mobileNumber,
    'content' => $messageText
];
$fields_string = http_build_query($fields);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);

$response_body = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_error_msg = curl_error($ch);
$curl_error_no = curl_errno($ch);
curl_close($ch);

$log_message = ''; 

if ($curl_error_no) {
    error_log("cURL Error to sender.ge: [#{$curl_error_no}] {$curl_error_msg}. URL: {$url}, Fields: " . $fields_string);
    echo json_encode([
        'status' => 'error',
        'message' => 'SMS gateway communication error. cURL: ' . htmlspecialchars($curl_error_msg)
    ]);
    exit;
}

error_log("sender.ge API Response - HTTP Code: {$httpCode} - Body: " . $response_body);
$sms_api_call_status = 'unknown_api_status';

if ($httpCode >= 200 && $httpCode < 300) {
    $responseData = json_decode($response_body, true);
    if ($responseData !== null && isset($responseData['data'][0]['statusId']) && $responseData['data'][0]['statusId'] == 1) {
        $sms_api_call_status = 'success';
        
        if (isset($conn) && !$conn->connect_error) { // Check connection again before logging
            $stmt_log = $conn->prepare("INSERT INTO active_promo_sms_logs (client_id_num, record_date) VALUES (?, NOW())");
            if ($stmt_log) {
                $stmt_log->bind_param("s", $clientIdNum);
                if ($stmt_log->execute()) {
                    $log_message = ' SMS successfully logged.';
                    error_log("Promo SMS logged for client_id_num: " . $clientIdNum);
                } else {
                    $log_message = ' SMS sent, but logging failed: ' . $stmt_log->error;
                    error_log("Failed to log promo SMS for client_id_num {$clientIdNum}: " . $stmt_log->error);
                }
                $stmt_log->close();
            } else {
                $log_message = ' SMS sent, but DB prepare statement failed for logging: ' . $conn->error;
                error_log("Failed to prepare statement for promo_sms_logs: " . $conn->error);
            }
        } else {
            // This case handles if $conn was initially bad OR became bad after settings fetch (unlikely for short script)
            $log_message = ' SMS sent, but DB connection for logging is unavailable.';
            error_log("Promo SMS sent for client_id_num {$clientIdNum}, but DB connection for logging unavailable.");
        }
    } else {
        $sms_api_call_status = 'api_logic_error';
        $log_message = ' SMS API call executed, but API reported an issue or unexpected format.';
        error_log("sender.ge API reported an issue or unexpected format. HTTP: $httpCode, Body: $response_body");
    }
    
    echo json_encode([
        'status' => $sms_api_call_status,
        'message' => 'SMS API call executed. HTTP Status: ' . $httpCode . $log_message,
        'api_response_http_code' => $httpCode,
        'api_response_body' => $response_body
    ]);

} else { 
    echo json_encode([
        'status' => 'api_http_error',
        'message' => 'SMS API returned an HTTP error.' . $log_message,
        'api_response_http_code' => $httpCode,
        'api_response_body' => $response_body
    ]);
}

// Close MySQL connection if it was opened and is valid
if (isset($conn) && !$conn->connect_error && !$db_connection_error_for_settings) {
    // Only close if it was successfully opened for settings (or assumed open for logging)
    $conn->close();
}
?>
