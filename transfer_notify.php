<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$user_id      = isset($data['user_id'])      ? (int)$data['user_id']                                         : 0;
$phone        = isset($data['phone'])        ? substr(preg_replace('/[^0-9+]/', '', $data['phone']), 0, 50)  : '';
$package_id   = isset($data['package_id'])   ? (int)$data['package_id']                                      : 0;
$package_name = isset($data['package_name']) ? substr(trim($data['package_name']), 0, 200)                   : '';
$amount       = isset($data['amount'])       ? round((float)$data['amount'], 2)                               : 0;

require_once 'mssql_connection.php';

if (!$mssqlconn) {
    echo json_encode(['status' => 'error', 'message' => 'DB connection failed']);
    exit;
}

$sql = "INSERT INTO TransferRequests (user_id, phone, package_id, package_name, amount)
        VALUES (?, ?, ?, ?, ?)";
$params = [$user_id, $phone, $package_id ?: null, $package_name, $amount];
$stmt = sqlsrv_query($mssqlconn, $sql, $params);

if ($stmt === false) {
    $err = sqlsrv_errors();
    echo json_encode(['status' => 'error', 'message' => $err[0]['message'] ?? 'Insert failed']);
    exit;
}

sqlsrv_free_stmt($stmt);
sqlsrv_close($mssqlconn);

echo json_encode(['status' => 'ok']);
