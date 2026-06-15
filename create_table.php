<?php
$conn = sqlsrv_connect('192.168.2.57,1433', [
    'Database'               => 'AlexFitness',
    'Uid'                    => 'fitnes_tonus',
    'PWD'                    => 'fitnes!@#',
    'Encrypt'                => true,
    'TrustServerCertificate' => true,
]);

if (!$conn) { echo 'No connection'; exit; }

// Check columns of ClientDetailsWebsite
$sql = "SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'ClientDetailsWebsite' ORDER BY ORDINAL_POSITION";
$stmt = sqlsrv_query($conn, $sql);
echo '<h3>ClientDetailsWebsite columns:</h3><table border=1 cellpadding=5>';
echo '<tr><th>Column</th><th>Type</th><th>Nullable</th></tr>';
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    echo '<tr><td>'.$row['COLUMN_NAME'].'</td><td>'.$row['DATA_TYPE'].'</td><td>'.$row['IS_NULLABLE'].'</td></tr>';
}
echo '</table>';

// Try a test insert
$testSql = "INSERT INTO ClientDetailsWebsite (id_number, full_name, mobile_number, email, agreed, agreement_date, user_ip, birth_date, picurl, home_address, child_name, child_id)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);
            SELECT SCOPE_IDENTITY() AS id;";

$params = ['TEST123', 'Test User', '995511000000', 'test@test.com', 1, date('Y-m-d H:i:s'), '127.0.0.1', '2000-01-01', null, null, null, null];
$insertStmt = sqlsrv_query($conn, $testSql, $params);

if ($insertStmt === false) {
    echo '<h2 style="color:red">✗ Insert failed:</h2><pre>';
    print_r(sqlsrv_errors());
    echo '</pre>';
} else {
    sqlsrv_next_result($insertStmt);
    $idRow = sqlsrv_fetch_array($insertStmt, SQLSRV_FETCH_ASSOC);
    $newId = $idRow['id'] ?? 'unknown';
    echo '<h2 style="color:green">✓ Insert succeeded! New ID: ' . $newId . '</h2>';

    // Clean up test row
    sqlsrv_query($conn, "DELETE FROM ClientDetailsWebsite WHERE id_number = 'TEST123'");
    echo '<p>Test row deleted.</p>';
}

sqlsrv_close($conn);
?>
