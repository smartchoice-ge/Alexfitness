<?php
/**
 * Zoho Mail API Helper Class
 * Handles OAuth token refresh and API requests
 */

class ZohoMailAPI {
    private $client_id = '1000.GAV29K5MFDAJ9OQNU0N2LU6DJN3WIH';
    private $client_secret = 'SYNERGY_ZOHO_CLIENT_SECRET';
    private $token_file;
    private $tokens;
    
    public function __construct() {
        $this->token_file = __DIR__ . '/tokens.json';
        $this->loadTokens();
    }
    
    private function loadTokens() {
        if (!file_exists($this->token_file)) {
            throw new Exception('Tokens file not found. Please authorize first at: zoho_email/callback.php');
        }
        
        $this->tokens = json_decode(file_get_contents($this->token_file), true);
        
        if (!$this->tokens || !isset($this->tokens['access_token'])) {
            throw new Exception('Invalid tokens file');
        }
    }
    
    private function saveTokens() {
        file_put_contents($this->token_file, json_encode($this->tokens, JSON_PRETTY_PRINT));
        chmod($this->token_file, 0600);
    }
    
    private function refreshAccessToken() {
        $url = 'https://accounts.zoho.com/oauth/v2/token';
        $post_data = [
            'grant_type' => 'refresh_token',
            'client_id' => $this->client_id,
            'client_secret' => $this->client_secret,
            'refresh_token' => $this->tokens['refresh_token']
        ];
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $response = curl_exec($ch);
        
        if ($response === false) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new Exception("cURL error during token refresh: " . $error);
        }
        curl_close($ch);
        
        $new_tokens = json_decode($response, true);
        
        if (isset($new_tokens['access_token'])) {
            $this->tokens['access_token'] = $new_tokens['access_token'];
            $this->tokens['expires_in'] = $new_tokens['expires_in'];
            $this->saveTokens();
            return true;
        }
        
        throw new Exception("Token refresh failed. Response: " . $response);
    }
    
    private function makeRequest($url, $method = 'GET', $data = null, $retry = true) {
        $headers = [
            'Authorization: Zoho-oauthtoken ' . $this->tokens['access_token'],
            'Content-Type: application/json'
        ];
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            if ($data) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            }
        }
        
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);

        $response = curl_exec($ch);
        
        if ($response === false) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new Exception('cURL Error: ' . $error);
        }

        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        $result = json_decode($response, true);
        
        // If unauthorized and we haven't retried yet, refresh token and retry
        if ($http_code == 401 && $retry) {
            try {
                $this->refreshAccessToken();
                return $this->makeRequest($url, $method, $data, false);
            } catch (Exception $e) {
                // If refresh fails, throw an exception with both errors
                $apiErrorMessage = $result['message'] ?? $response;
                throw new Exception('API Error: ' . $apiErrorMessage . ' | Token Refresh Error: ' . $e->getMessage());
            }
        }
        
        if ($http_code >= 400) {
            throw new Exception('API Error: ' . ($result['message'] ?? $response));
        }
        
        return $result;
    }
    
    public function getAccounts() {
        $url = 'https://mail.zoho.com/api/accounts';
        return $this->makeRequest($url);
    }
    
    public function sendEmail($accountId, $toAddress, $subject, $content, $fromAddress = 'info@synergy-gym.ge') {
        $url = "https://mail.zoho.com/api/accounts/{$accountId}/messages";
        
        $data = [
            'fromAddress' => $fromAddress,
            'toAddress' => $toAddress,
            'subject' => $subject,
            'content' => $content,
            'mailFormat' => 'html'
        ];
        
        return $this->makeRequest($url, 'POST', $data);
    }
}
