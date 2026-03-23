<?php
// OAuth callback handler for Zoho Mail API

$client_id = '1000.GAV29K5MFDAJ9OQNU0N2LU6DJN3WIH';
$client_secret = 'SYNERGY_ZOHO_CLIENT_SECRET';
$redirect_uri = '/zoho_email/callback.php';

// Step 1: If no code, show authorization link
if (!isset($_GET['code'])) {
    $auth_url = 'https://accounts.zoho.com/oauth/v2/auth?' . http_build_query([
        'scope' => 'ZohoMail.messages.CREATE,ZohoMail.accounts.READ',
        'client_id' => $client_id,
        'response_type' => 'code',
        'redirect_uri' => $redirect_uri,
        'access_type' => 'offline',
        'prompt' => 'consent'
    ]);
    
    echo '<!DOCTYPE html>
<html>
<head><title>Zoho Mail API Setup</title></head>
<body style="font-family: Arial; max-width: 600px; margin: 50px auto; padding: 20px;">
    <h2>Zoho Mail API Authorization</h2>
    <p>Click the button below to authorize your Zoho Mail account:</p>
    <a href="' . htmlspecialchars($auth_url) . '" style="background: #0066cc; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">Authorize Zoho Mail</a>
    <div style="margin-top: 30px; padding: 15px; background: #f0f0f0; border-radius: 5px;">
        <strong>Note:</strong> This is a one-time setup to enable email sending via Zoho API (bypassing blocked SMTP ports).
    </div>
</body>
</html>';
    exit;
}

// Step 2: Exchange code for tokens
$code = $_GET['code'];

$token_url = 'https://accounts.zoho.com/oauth/v2/token';
$post_data = [
    'grant_type' => 'authorization_code',
    'client_id' => $client_id,
    'client_secret' => $client_secret,
    'redirect_uri' => $redirect_uri,
    'code' => $code
];

$ch = curl_init($token_url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$tokens = json_decode($response, true);

if ($http_code !== 200 || !isset($tokens['access_token'])) {
    echo '<!DOCTYPE html>
<html>
<head><title>Authorization Error</title></head>
<body style="font-family: Arial; max-width: 600px; margin: 50px auto; padding: 20px;">
    <h2 style="color: red;">✗ Authorization Error</h2>
    <p>Failed to get access token from Zoho.</p>
    <pre style="background: #f5f5f5; padding: 15px; overflow: auto;">' . htmlspecialchars(print_r($tokens, true)) . '</pre>
    <a href="callback.php">Try Again</a>
</body>
</html>';
    exit;
}

// Save tokens to a file
$token_file = __DIR__ . '/tokens.json';
file_put_contents($token_file, json_encode($tokens, JSON_PRETTY_PRINT));
chmod($token_file, 0600); // Secure permissions

echo '<!DOCTYPE html>
<html>
<head><title>Authorization Successful</title></head>
<body style="font-family: Arial; max-width: 600px; margin: 50px auto; padding: 20px;">
    <h2 style="color: green;">✓ Authorization Successful!</h2>
    <p>Zoho Mail API has been configured successfully.</p>
    <div style="background: #e8f5e9; padding: 15px; border-radius: 5px; margin: 20px 0;">
        <strong>Tokens saved:</strong>
        <ul>
            <li>Access Token: <code>' . substr($tokens['access_token'], 0, 20) . '...</code></li>
            <li>Refresh Token: <code>' . substr($tokens['refresh_token'], 0, 20) . '...</code></li>
            <li>Expires in: ' . ($tokens['expires_in'] / 3600) . ' hours</li>
        </ul>
    </div>
    <div style="background: #fff3e0; padding: 15px; border-radius: 5px; margin-top: 20px;">
        <strong>Next Steps:</strong>
        <ol>
            <li>Now we need to get your Account ID</li>
            <li>Visit the account setup page to complete the configuration</li>
        </ol>
    </div>
    <p style="margin-top: 20px;">
        <a href="get_account_id.php" style="background: #0066cc; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">Get Account ID</a>
    </p>
</body>
</html>';
?>
