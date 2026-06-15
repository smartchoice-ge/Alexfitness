<?php
require "mssql_connection.php";

if (!$mssqlconn) {
    die("<h2 style='color:red'>Database connection failed. Make sure you are on the gym network.</h2>");
}

$username = "admin";
$password = "Admin123!";
$hashed   = md5($password);

// Check if user already exists
$check = sqlsrv_query($mssqlconn, "SELECT COUNT(*) AS cnt FROM users WHERE UserName = ?", array($username));
$row   = sqlsrv_fetch_array($check, SQLSRV_FETCH_ASSOC);

if ($row["cnt"] > 0) {
    // Update existing user password and activate
    $stmt = sqlsrv_query($mssqlconn,
        "UPDATE users SET Password = ?, IsActive = 1 WHERE UserName = ?",
        array($hashed, $username)
    );
    if ($stmt) {
        echo "<h2 style='color:green;font-family:sans-serif'>User updated successfully!</h2>";
    } else {
        echo "<h2 style='color:red'>Update failed</h2><pre>" . print_r(sqlsrv_errors(), true) . "</pre>";
    }
} else {
    $stmt = sqlsrv_query($mssqlconn,
        "INSERT INTO users (UserName, Password, IsActive) VALUES (?, ?, 1)",
        array($username, $hashed)
    );
    if ($stmt) {
        echo "<h2 style='color:green;font-family:sans-serif'>User created successfully!</h2>";
    } else {
        echo "<h2 style='color:red'>Insert failed</h2><pre>" . print_r(sqlsrv_errors(), true) . "</pre>";
    }
}

echo "<hr style='font-family:sans-serif'>
<p style='font-family:sans-serif;font-size:18px'>
  <b>Username:</b> admin<br>
  <b>Password:</b> Admin123!<br><br>
  <a href='login.php'>Go to Admin Login</a>
</p>";

sqlsrv_close($mssqlconn);

// Delete this file after use for security
unlink(__FILE__);
echo "<p style='color:gray;font-family:sans-serif;font-size:12px'>This setup file has been deleted.</p>";
?>
