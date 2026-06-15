<?php

require '/home/synergy-gym.ge/public_html/mssql_connection.php';
require '/home/synergy-gym.ge/public_html/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;


$host = 'smtppro.zoho.com';
$user = 'info@synergy-gym.ge';
$pass = 'info@synergy-gym.ge_PASS';

$sql = "SELECT 
            ClientID, 
            EndDate, 
            Expired, 
            Clients.FullName, 
            Clients.Email,
            Clients.unsubscribe_token
        FROM SoldPackages 
        JOIN Clients ON Clients.ID = SoldPackages.ClientID
        WHERE Expired = 0 
          AND Clients.SendEmail = 1
          AND Clients.Email != ''
          AND CAST(EndDate AS DATE) = CAST(GETDATE() AS DATE)";

$result = sqlsrv_query($mssqlconn, $sql);

if ($result === false) {
    die("Query failed: " . print_r(sqlsrv_errors(), true));
}

while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
    $email = $row['Email'];
    $unsubscribe_token = $row['unsubscribe_token'];

    // Generate a token if it doesn't exist
    if (empty($unsubscribe_token)) {
        $unsubscribe_token = bin2hex(random_bytes(16)); // Generate a unique token
        $update_sql = "UPDATE Clients SET unsubscribe_token = ? WHERE Email = ?";
        $update_stmt = sqlsrv_prepare($mssqlconn, $update_sql, array(&$unsubscribe_token, &$email));
        sqlsrv_execute($update_stmt);
    }

    sendEmail($email, $unsubscribe_token);
    //sendEmail('endeladzegivi@gmail.com', $unsubscribe_token);
}

sqlsrv_free_stmt($result);
sqlsrv_close($mssqlconn);

// Function to send email
function sendEmail($email, $unsubscribe_token)
{
    $mail = new PHPMailer(true);

    try {
        // SMTP Configuration
        $mail->isSMTP();
        $mail->Host = 'smtppro.zoho.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'info@synergy-gym.ge';
        $mail->Password = 'info@synergy-gym.ge_PASS';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('info@synergy-gym.ge', 'synergy-gym.ge');
        $mail->addAddress($email);
        $mail->Subject = 'Membership Ending Tomorrow';
        $mail->isHTML(true);

        // Unsubscribe link
        $unsubscribe_link = "/auto_emails/unsubscribe.php?token=$unsubscribe_token";

        $mail->Body = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Synergy Gym QR Code</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; text-align: center;">

    <!-- Main Wrapper -->
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" 
        style="width: 100%; min-height: 600px; background: url(\'/img/bg.png\') no-repeat center center; background-size: cover;">
        
        ';
        
        ob_start();
        include 'email_templates/header.php';
        $mail->Body .= ob_get_clean();
        
        $mail->Body .= '

        <!-- Welcome Message -->
        <tr>
            <td align="center" style="padding: 10px; background: rgba(0, 0, 0, 0.6);">
                <div style="max-width: 90%; margin: auto; padding: 10px;">
                    <p style="color:rgb(255, 255, 255); font-size: 16px; line-height: 1.5;">
                    <b>
                        Hi there! Your membership <span style="color: #FFD700 !important; font-size: 18px;">will expire tomorrow</span> , but your fitness journey doesn’t have to stop! Stay on track with full access to our facilities, expert trainers, and exciting classes.
Renew today by contacting us on <a href="https://www.facebook.com/Alexfitnesskobulrti">Facebook</a> and keep pushing toward your goals!
                    </b>
                    </p>
                </div>
            </td>
        </tr>
        ';
        
        ob_start();
        include 'email_templates/price_includes.php';
        include 'email_templates/footer.php';
        $mail->Body .= ob_get_clean(); 
        
        $mail->Body .= '
         <p style="color: #FFD700; font-size: 14px; line-height: 1.5;">
                        <a href="' . $unsubscribe_link . '" style="color: #FFD700; text-decoration: underline;">UNSUBSCRIBE</a>
                    </p>
    </table>

</body>
</html>';

        if ($mail->send()) {
            echo "Email sent to: $email\n";
            $servername = "localhost";
            $username = "synergy_main";
            $password = "SYNERGY_MYSQL_PASS";
            $database = "synergy_main";

            // Create connection
            $conn = new mysqli($servername, $username, $password, $database);
            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }
            mysqli_set_charset($conn, "utf8mb4");


            $stmt = $conn->prepare("INSERT INTO emails (email, date, content) VALUES (?, NOW(), ?)");
            $content = 'package expires tomorrow';
            $stmt->bind_param("ss", $email, $content);
            $stmt->execute();
            $stmt->close();
        } else {
            echo "Failed to send email to $email: " . $mail->ErrorInfo . "\n";
        }
    } catch (Exception $e) {
        echo "Error sending email to $email: " . $e->getMessage() . "\n";
    }
}

?>
