<?php
$conn = sqlsrv_connect('192.168.2.57,1433', [
    'Database'               => 'AlexFitness',
    'Uid'                    => 'fitnes_tonus',
    'PWD'                    => 'fitnes!@#',
    'Encrypt'                => true,
    'TrustServerCertificate' => true,
]);

if (!$conn) {
    echo '<h2 style="color:red">✗ Connection failed</h2><pre>';
    print_r(sqlsrv_errors());
    echo '</pre>';
    exit;
}

echo '<h2 style="color:green">✓ Connected to AlexFitness</h2>';

// All tables
$tables = sqlsrv_query($conn, "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_TYPE='BASE TABLE' ORDER BY TABLE_NAME");
echo '<h3>Tables:</h3><ul>';
while ($row = sqlsrv_fetch_array($tables, SQLSRV_FETCH_ASSOC)) {
    echo '<li>' . htmlspecialchars($row['TABLE_NAME']) . '</li>';
}
echo '</ul>';

// Check specifically for ClientDetailsWebsite
$check = sqlsrv_query($conn, "SELECT COUNT(*) AS cnt FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'ClientDetailsWebsite'");
$row = sqlsrv_fetch_array($check, SQLSRV_FETCH_ASSOC);
if ($row['cnt'] > 0) {
    echo '<p style="color:green"><strong>✓ ClientDetailsWebsite table EXISTS</strong></p>';
    $cnt = sqlsrv_query($conn, "SELECT COUNT(*) AS total FROM ClientDetailsWebsite");
    $r = sqlsrv_fetch_array($cnt, SQLSRV_FETCH_ASSOC);
    echo '<p>Rows in ClientDetailsWebsite: <strong>' . $r['total'] . '</strong></p>';
} else {
    echo '<p style="color:red"><strong>✗ ClientDetailsWebsite table MISSING</strong></p>';
}

sqlsrv_close($conn);
?>
