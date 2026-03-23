<?php
// unipay_checkout.php
require_once 'db_connection.php';
require_once 'mssql_connection.php';
require_once 'mssql_packages_payments_helper.php';
date_default_timezone_set('Asia/Tbilisi');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$amount = isset($data['amount']) ? $data['amount'] : null;
$client_mobile_number = isset($data['client_mobile_number']) ? $data['client_mobile_number'] : null;
$user_id = isset($data['user_id']) ? $data['user_id'] : null;
$description = isset($data['description']) ? $data['description'] : '';
$package_id = isset($data['package_id']) ? $data['package_id'] : null;

if (!$amount || !$client_mobile_number) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required fields']);
    exit;
}

// Step 1: Get Unipay auth token
$auth_url = 'https://apiv2.unipay.com/v3/auth';
$auth_body = json_encode([
    'merchant_id' => '5017254030111',
    'api_key' => 'b87aeb0a-f0d8-4965-9584-f6b87ddc35d6'
]);
$ch = curl_init($auth_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POSTFIELDS, $auth_body);
$auth_response = curl_exec($ch);
$auth_data = json_decode($auth_response, true);
curl_close($ch);

if (!isset($auth_data['auth_token'])) {
    http_response_code(500);
    echo json_encode(['error' => 'Unipay auth failed']);
    exit;
}
$auth_token = $auth_data['auth_token'];

// Generate unique MerchantOrderID by adding random 5-character suffix to mobile number
$random_suffix = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 5));
$unique_merchant_order_id = $client_mobile_number . '_' . $random_suffix;

// Step 2: Create Unipay order
$order_url = 'https://apiv2.unipay.com/v3/api/order/create';
$order_body = json_encode([
    'MerchantUser' => 'info@synergy-gym.ge',
    'MerchantOrderID' => $unique_merchant_order_id,
    'OrderPrice' => $amount,
    'OrderCurrency' => 'GEL',
    'OrderName' => 'ACVNJSIWPM',
    'OrderDescription' => 'AKXU0BJ5LEV97HN',
    'SuccessRedirectUrl' => 'aHR0cHM6Ly90b251cy5nZS9wYXltZW50X3N1Y2Nlc3MucGhw',
    'CancelRedirectUrl' => 'aHR0cHM6Ly90b251cy5nZS9wYXltZW50X2ZhaWwucGhw',
    'CallBackUrl' => 'aHR0cHM6Ly90b251cy5nZS91bmlwYXlfY2FsbGJhY2sucGhw',
    'Mlogo' => '',
    'InApp' => 0,
    'Language' => 'GE'
]);
$ch = curl_init($order_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $auth_token
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, $order_body);
$order_response = curl_exec($ch);
$order_data = json_decode($order_response, true);
curl_close($ch);

if (!isset($order_data['data']['UnipayOrderHashID']) || !isset($order_data['data']['Checkout'])) {
    http_response_code(500);
    echo json_encode(['error' => 'Unipay order failed', 'details' => $order_data]);
    exit;
}
$unipayOrderHashID = $order_data['data']['UnipayOrderHashID'];
$checkoutUrl = $order_data['data']['Checkout'];

// Step 3: Save payment record to MSSQL PaymentsWebsite
$status = 'Pending';
$transaction_id = $unipayOrderHashID;
$time = date('Y-m-d H:i:s');

$paymentId = insertPaymentWebsite([
    'amount' => $amount,
    'status' => $status,
    'client_mobile_number' => $client_mobile_number,
    'transaction_id' => $transaction_id,
    'time' => $time,
    'package_id' => $package_id,
    'user_id' => $user_id
]);

if (!$paymentId) {
    error_log("Failed to insert payment to MSSQL: " . $transaction_id);
}

// Step 4: Return checkout URL
echo json_encode(['success' => true, 'checkout_url' => $checkoutUrl]);
?>
