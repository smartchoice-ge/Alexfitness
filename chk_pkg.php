<?php
require_once '/home/alexfitness.ge/public_html/mssql_connection.php';

echo "=== SoldPackages ===\n";
$st = sqlsrv_query($mssqlconn, "SELECT TOP 1 * FROM SoldPackages ORDER BY Id DESC");
if ($st && $r = sqlsrv_fetch_array($st, SQLSRV_FETCH_ASSOC)) {
    foreach ($r as $k => $v) echo $k . "=[" . ($v instanceof DateTime ? $v->format('Y-m-d') : $v) . "]\n";
} else { echo "empty\n"; }

echo "\n=== PackagesWebsite ===\n";
$st2 = sqlsrv_query($mssqlconn, "SELECT TOP 3 * FROM PackagesWebsite");
if ($st2) {
    while ($r2 = sqlsrv_fetch_array($st2, SQLSRV_FETCH_ASSOC)) {
        foreach ($r2 as $k => $v) echo $k . "=[" . ($v instanceof DateTime ? $v->format('Y-m-d') : $v) . "] ";
        echo "\n";
    }
}

echo "\n=== PaymentsWebsite ===\n";
$st3 = sqlsrv_query($mssqlconn, "SELECT TOP 1 * FROM PaymentsWebsite ORDER BY Id DESC");
if ($st3 && $r3 = sqlsrv_fetch_array($st3, SQLSRV_FETCH_ASSOC)) {
    foreach ($r3 as $k => $v) echo $k . "=[" . ($v instanceof DateTime ? $v->format('Y-m-d') : $v) . "]\n";
} else { echo "empty\n"; }

echo "\n=== Packages (sample) ===\n";
$st4 = sqlsrv_query($mssqlconn, "SELECT TOP 3 Id, Name, Duration, Price FROM Packages");
if ($st4) {
    while ($r4 = sqlsrv_fetch_array($st4, SQLSRV_FETCH_ASSOC)) {
        echo "Id=[" . $r4['Id'] . "] Name=[" . $r4['Name'] . "] Duration=[" . $r4['Duration'] . "] Price=[" . $r4['Price'] . "]\n";
    }
}
