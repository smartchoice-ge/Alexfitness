<?php
// One-off migration runner: adds PackagesWebsite.duration_days.
// Open /admin/migrate_duration_days.php while logged in as an admin.
// Idempotent — running it again after the column exists is a no-op.
session_start([
    'cookie_httponly' => true,
    'cookie_samesite' => 'Strict'
]);

if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header('Location: ../login.php');
    exit;
}

require_once '../mssql_connection.php';

header('Content-Type: text/plain; charset=utf-8');

if (!$mssqlconn) {
    http_response_code(500);
    echo "Database connection failed. Make sure you are on the gym network.\n";
    exit;
}

// Each statement runs on its own; all are idempotent.
$statements = [
    // 1-3: structured + legacy columns.
    "IF NOT EXISTS (SELECT 1 FROM sys.columns WHERE object_id = OBJECT_ID('dbo.PackagesWebsite') AND name = 'duration_days')
     BEGIN ALTER TABLE dbo.PackagesWebsite ADD duration_days INT NULL; END",

    "IF NOT EXISTS (SELECT 1 FROM sys.columns WHERE object_id = OBJECT_ID('dbo.PackagesWebsite') AND name = 'duration_value')
     BEGIN ALTER TABLE dbo.PackagesWebsite ADD duration_value INT NULL; END",

    "IF NOT EXISTS (SELECT 1 FROM sys.columns WHERE object_id = OBJECT_ID('dbo.PackagesWebsite') AND name = 'duration_unit')
     BEGIN ALTER TABLE dbo.PackagesWebsite ADD duration_unit VARCHAR(10) NULL; END",

    // 4: duration_month must allow NULL (day/week packages have no month equivalent).
    "IF EXISTS (SELECT 1 FROM sys.columns WHERE object_id = OBJECT_ID('dbo.PackagesWebsite') AND name = 'duration_month')
     BEGIN ALTER TABLE dbo.PackagesWebsite ALTER COLUMN duration_month INT NULL; END",

    // 5: carry over data from an earlier duration_type column if it exists.
    "IF EXISTS (SELECT 1 FROM sys.columns WHERE object_id = OBJECT_ID('dbo.PackagesWebsite') AND name = 'duration_type')
     EXEC('UPDATE dbo.PackagesWebsite SET duration_unit = duration_type
           WHERE (duration_unit IS NULL OR duration_unit = '''') AND duration_type IS NOT NULL AND duration_type <> ''''')",

    // 6-7: infer unit/value from legacy columns for rows still lacking them.
    "UPDATE dbo.PackagesWebsite SET duration_unit = 'day', duration_value = duration_days
     WHERE (duration_unit IS NULL OR duration_unit = '') AND duration_days IS NOT NULL AND duration_days > 0",

    "UPDATE dbo.PackagesWebsite SET duration_unit = 'month', duration_value = duration_month
     WHERE (duration_unit IS NULL OR duration_unit = '')
       AND duration_month IS NOT NULL AND duration_month > 0 AND duration_month <> 50",

    // 8: normalize legacy columns to totals for every structured row.
    "UPDATE dbo.PackagesWebsite SET duration_days = duration_value, duration_month = NULL WHERE duration_unit = 'day' AND duration_value > 0",
    "UPDATE dbo.PackagesWebsite SET duration_days = duration_value * 7, duration_month = NULL WHERE duration_unit = 'week' AND duration_value > 0",
    "UPDATE dbo.PackagesWebsite SET duration_days = duration_value * 30, duration_month = duration_value WHERE duration_unit = 'month' AND duration_value > 0",
    "UPDATE dbo.PackagesWebsite SET duration_days = duration_value * 365, duration_month = duration_value * 12 WHERE duration_unit = 'year' AND duration_value > 0",
];

foreach ($statements as $i => $sql) {
    $stmt = sqlsrv_query($mssqlconn, $sql);
    if ($stmt === false) {
        http_response_code(500);
        echo "Migration step " . ($i + 1) . " failed:\n" . print_r(sqlsrv_errors(), true);
        exit;
    }
}

echo "OK: PackagesWebsite has duration_unit, duration_value, duration_days, duration_month (legacy backfilled).\n";
