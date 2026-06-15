<?php
$serverName = "smartchoice.zapto.org,1433";

// Try AlexFitness DB first
$connOptions = array(
    "Database"               => "AlexFitness",
    "Uid"                    => "fitnes_tonus",
    "PWD"                    => "fitnes!@#",
    "Encrypt"                => false,
    "TrustServerCertificate" => true,
    "CharacterSet"           => "UTF-8",
    "LoginTimeout"           => 8,
);

$conn = sqlsrv_connect($serverName, $connOptions);

if (!$conn) {
    // Try FitnesTonus DB
    $connOptions["Database"] = "FitnesTonus";
    $conn = sqlsrv_connect($serverName, $connOptions);
    if (!$conn) {
        echo "CONNECTION FAILED:\n";
        print_r(sqlsrv_errors());
        exit;
    }
    echo "Connected to: FitnesTonus\n";
} else {
    echo "Connected to: AlexFitness\n";
}

// Show existing users
echo "\n--- Existing Users ---\n";
$stmt = sqlsrv_query($conn, "SELECT id, UserName, IsActive FROM users");
if ($stmt) {
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        echo "ID: {$row['id']} | User: {$row['UserName']} | Active: {$row['IsActive']}\n";
    }
    sqlsrv_free_stmt($stmt);
} else {
    echo "Could not query users table: ";
    print_r(sqlsrv_errors());
}

// Create new admin user
$newUser = "admin";
$newPass = "Alex@2025";
$hashedPass = md5($newPass);

$check = sqlsrv_query($conn, "SELECT COUNT(*) as cnt FROM users WHERE UserName = ?", array($newUser));
$row = sqlsrv_fetch_array($check, SQLSRV_FETCH_ASSOC);
sqlsrv_free_stmt($check);

if ($row['cnt'] > 0) {
    // Update existing
    $upd = sqlsrv_query($conn, "UPDATE users SET Password = ?, IsActive = 1 WHERE UserName = ?", array($hashedPass, $newUser));
    if ($upd !== false) {
        echo "\nUpdated existing user '$newUser' with new password.\n";
    } else {
        echo "\nFailed to update user:\n";
        print_r(sqlsrv_errors());
    }
} else {
    // Insert new
    $ins = sqlsrv_query($conn, "INSERT INTO users (UserName, Password, IsActive) VALUES (?, ?, 1)", array($newUser, $hashedPass));
    if ($ins !== false) {
        echo "\nCreated new user '$newUser'.\n";
    } else {
        echo "\nFailed to insert user:\n";
        print_r(sqlsrv_errors());
    }
}

echo "\n--- Login Credentials ---\n";
echo "Username: $newUser\n";
echo "Password: $newPass\n";

sqlsrv_close($conn);
?>
