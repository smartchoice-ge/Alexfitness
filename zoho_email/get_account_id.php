<?php
require_once __DIR__ . '/zoho_api_helper.php';

try {
    $api = new ZohoMailAPI();
    $accounts = $api->getAccounts();
    
    echo '<!DOCTYPE html>
    <html>
    <head>
        <title>Zoho Mail Account ID</title>
        <style>
            body { font-family: Arial, sans-serif; max-width: 800px; margin: 40px auto; padding: 20px; line-height: 1.6; }
            .account { background: #f5f5f5; padding: 15px; border-radius: 5px; margin-bottom: 20px; border-left: 4px solid #0066cc; }
            .id { font-size: 1.2em; font-weight: bold; color: #333; }
            .email { color: #666; }
            code { background: #eee; padding: 2px 5px; border-radius: 3px; }
            .copy-btn { background: #0066cc; color: white; border: none; padding: 5px 10px; border-radius: 3px; cursor: pointer; margin-left: 10px; }
        </style>
    </head>
    <body>
        <h2>Zoho Mail Account IDs</h2>';

    if (isset($accounts['data'])) {
        foreach ($accounts['data'] as $account) {
            echo '<div class="account">';
            echo '<div class="email">Email: ' . htmlspecialchars($account['incomingUserName']) . '</div>';
            echo '<div class="id">Account ID: <span id="id_' . $account['accountId'] . '">' . $account['accountId'] . '</span>';
            echo '<button class="copy-btn" onclick="copyToClipboard(\'' . $account['accountId'] . '\')">Copy</button></div>';
            echo '</div>';
        }
        
        echo '<div style="background: #e8f5e9; padding: 15px; border-radius: 5px; margin-top: 20px;">
            <strong>Next Step:</strong><br>
            Update your <code>params.php</code> file with this Account ID:<br>
            <code>$zoho_account_id = \'YOUR_ACCOUNT_ID_HERE\';</code>
        </div>';
    } else {
        echo '<div style="color: red;">No accounts found or API error. Response: ' . htmlspecialchars(json_encode($accounts)) . '</div>';
    }

    echo '<script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                alert("Copied to clipboard!");
            }, function(err) {
                console.error("Could not copy text: ", err);
            });
        }
    </script>
    </body>
    </html>';

} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
?>