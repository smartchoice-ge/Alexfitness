# Zoho Mail API Setup

This is an alternative email sending method that bypasses SMTP port blocking (common on DigitalOcean).

## Setup Steps

### 1. Authorize Zoho Mail
Visit: `/zoho_email/callback.php`

Click "Authorize Zoho Mail" and log in with your Zoho account.

### 2. Get Your Account ID
After authorization, visit: `/zoho_email/get_account_id.php`

Copy the Account ID shown (looks like a long number).

### 3. Add Account ID to params.php
Edit `params.php` and add your Account ID:

```php
$zoho_account_id = 'YOUR_ACCOUNT_ID_HERE';
```

### 4. Test Email Sending
Visit: `/zoho_email/send_via_api.php?test=1&email=your@email.com`

## Usage in Code

```php
require_once 'zoho_email/send_via_api.php';

$result = sendEmailViaZohoAPI(
    'recipient@example.com',
    'Email Subject',
    '<h1>HTML Content</h1><p>Your message here</p>'
);

if ($result['success']) {
    echo 'Email sent!';
} else {
    echo 'Error: ' . $result['error'];
}
```

## Files Created

- `callback.php` - OAuth authorization handler
- `get_account_id.php` - Retrieves your Zoho account ID
- `zoho_api_helper.php` - API wrapper class with auto token refresh
- `send_via_api.php` - Simple function to send emails
- `tokens.json` - Stores OAuth tokens (auto-created, keep secure)

## Security

- `tokens.json` has 0600 permissions (owner read/write only)
- Never commit tokens.json to git
- Tokens auto-refresh when expired

## Advantages vs SMTP

- ✅ Works when SMTP ports are blocked
- ✅ No firewall issues
- ✅ Automatic token refresh
- ✅ Uses HTTPS (port 443) - never blocked
- ✅ Same Zoho account, same sender address
