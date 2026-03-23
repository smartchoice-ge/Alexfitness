<?php

require '/home/SYNERGY_DOMAIN/public_html/mssql_connection.php';
require '/home/SYNERGY_DOMAIN/public_html/email_sender.php';
require '/home/SYNERGY_DOMAIN/public_html/params.php';

$discount_percentages = [20, 25, 30, 30, 30, 30, 30, 30, 30, 30, 30, 30];
$days_back = 30;
$max_months = 24;
$month_count = 1;

while ($month_count <= $max_months) {
    $sql = "
        SELECT 
            sp.ClientID, 
            sp.EndDate, 
            sp.Expired, 
            c.FullName, 
            c.Email,
            c.unsubscribe_token
        FROM SoldPackages sp
        JOIN Clients c ON c.ID = sp.ClientID
        WHERE sp.Expired = 1 
          AND c.SendEmail = 1
          AND NOT EXISTS (
              SELECT 1 
              FROM SoldPackages sp2 
              WHERE sp2.ClientID = sp.ClientID 
                AND sp2.Expired = 0
          )
          AND CAST(sp.EndDate AS DATE) = CAST(DATEADD(DAY, -$days_back, GETDATE()) AS DATE)
          AND c.Email != ''
    ";

    $result = sqlsrv_query($mssqlconn, $sql);
    if ($result === false) {
        die("Query failed: " . print_r(sqlsrv_errors(), true));
    }

    $discount_percentage = $discount_percentages[$month_count - 1];

    while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
        $email = $row['Email'];
        $unsubscribe_token = $row['unsubscribe_token'];
    
        // Generate a token if it doesn't exist
        if (empty($unsubscribe_token)) {
            $unsubscribe_token = bin2hex(random_bytes(16));
            $update_sql = "UPDATE Clients SET unsubscribe_token = ? WHERE Email = ?";
            $update_stmt = sqlsrv_prepare($mssqlconn, $update_sql, array(&$unsubscribe_token, &$email));
            sqlsrv_execute($update_stmt);
        }
    
        sendEmail($email, $discount_percentage, $month_count, $unsubscribe_token);

        sleep(120);
    }

    sqlsrv_free_stmt($result);
    
    $month_count++;
    $days_back += 30; 
}

sqlsrv_close($mssqlconn);

function sendEmail($email, $discount_percentage, $month_count, $unsubscribe_token)
{
    global $user;
    
    $unsubscribe_link = "/auto_emails/unsubscribe.php?token=$unsubscribe_token";

    $emailBody = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Synergy Gym</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; text-align: center;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" 
        style="width: 100%; min-height: 600px; background: url(\'/img/bg.png\') no-repeat center center; background-size: cover;">
        ';
        
    ob_start();
    include 'email_templates/header.php';
    $emailBody .= ob_get_clean();
    
    $emailBody .= '
        <tr>
            <td align="center" style="padding: 10px; background: rgba(0, 0, 0, 0.6);">
                <div style="max-width: 90%; margin: auto; padding: 10px;">
                    <p style="color:rgb(255, 255, 255); font-size: 16px; line-height: 1.5;">
                        <b>
                            It\'s been over ' . ($month_count > 1 ? $month_count : "") . ' month' . ($month_count > 1 ? "s" : "") . ' since we\'ve seen you crushing it at our gym, and we\'ve got to admit – the gym just isn\'t the same without you! We\'re here to welcome you back with open arms (and maybe a few kettlebells) and offer you <span style="color: black; font-size:20px; color: #FFD700 !important;"><b>' . $discount_percentage . '% discount</b></span>
                        </b>
                    </p>
                </div>
            </td>
        </tr>
        ';
    
    ob_start();
    include 'email_templates/price_includes.php';
    include 'email_templates/footer.php';
    $emailBody .= ob_get_clean(); 
    
    $emailBody .= '
        <p style="color: #FFD700; font-size: 14px; line-height: 1.5;">
            <a href="' . $unsubscribe_link . '" style="color: #FFD700; text-decoration: underline;">UNSUBSCRIBE</a>
        </p>
    </table>
</body>
</html>';

    $result = sendUnifiedEmail($email, $discount_percentage . '% OFF - SYNERGY_DOMAIN', $emailBody, $user);
    
    if ($result['success']) {
        echo "Email sent to: $email\n";

        $servername = "localhost";
        $username = "synergy_main";
        $password = "SYNERGY_MYSQL_PASS";
        $database = "synergy_main";

        $conn = new mysqli($servername, $username, $password, $database);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        mysqli_set_charset($conn, "utf8mb4");

        $stmt = $conn->prepare("INSERT INTO emails (email, date, content) VALUES (?, NOW(), ?)");
        $content = 'Inactive user email';
        $stmt->bind_param("ss", $email, $content);
        $stmt->execute();
        $stmt->close();
        $conn->close();
    } else {
        echo "Failed to send email to $email: " . $result['error'] . "\n";
    }
}

?>
