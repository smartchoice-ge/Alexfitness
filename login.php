<?php
// Prevent direct script access
if (empty($_SERVER['HTTP_HOST'])) {
    die('Direct script access is not allowed.');
}

// Start session with enhanced security
session_start([
    'cookie_httponly' => true,  // Prevent JavaScript access to session cookie
    'cookie_samesite' => 'Strict'  // Prevent CSRF
]);

// Initialize error handling
$error = '';

// Include database connection
require_once 'mssql_connection.php';

/**
 * Try to authenticate against a given MSSQL users table.
 */
function authenticateUser($conn, $username, $hashedPassword)
{
    if (!$conn) {
        return null;
    }

    $sql = "SELECT TOP 1 id, UserName, Password, IsActive FROM users WHERE UserName = ? AND Password = ? AND IsActive = 1";
    $params = array($username, $hashedPassword);
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        return null;
    }

    $user = null;
    if (sqlsrv_has_rows($stmt)) {
        $user = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    }
    sqlsrv_free_stmt($stmt);

    return $user ?: null;
}

// Session fixation and CSRF protection
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Login logic
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF token validation
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        $error = 'Invalid request. Please try again.';
    } else {
        // Sanitize and validate input
        $username = filter_input(INPUT_POST, 'username', FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
        $password = $_POST['password']; // Password from form

        // Hash the input password with MD5
        $hashed_password = md5($password);

        // 1) Try Synergy DB first
        $user = authenticateUser($mssqlconn, $username, $hashed_password);

        // 2) Fallback: shared Tonus users DB (same credentials as tonus.ge admin)
        if (!$user) {
            $sharedConn = sqlsrv_connect("smartchoice.zapto.org,1433", array(
                "Database" => "FitnesTonus",
                "Uid" => "fitnes_tonus",
                "PWD" => "fitnes!@#",
                "Encrypt" => false,
                "TrustServerCertificate" => true,
                "CharacterSet" => "UTF-8",
                "LoginTimeout" => 5,
            ));

            $user = authenticateUser($sharedConn, $username, $hashed_password);

            if ($sharedConn) {
                sqlsrv_close($sharedConn);
            }
        }

        if ($user) {
            session_regenerate_id(true); // Prevent session fixation
            $_SESSION['user_logged_in'] = true;
            $_SESSION['username'] = $username; // Store username in session
            $_SESSION['user_id'] = $user['id']; // Store user ID in session
            header('Location: admin/users_list.php'); // Redirect to admin users list page
            exit;
        }

        $error = 'Invalid username or password or account is not active.';
    }
}

// Check if already logged in
if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {
    header('Location: admin/users_list.php');
    exit;
}
// Close the database connection when done
if ($mssqlconn) {
    sqlsrv_close($mssqlconn);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Login</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-sm">
        <form method="post" class="space-y-4">
            <!-- CSRF Token -->
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
            
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                <input 
                    type="text" 
                    name="username" 
                    id="username" 
                    required 
                    autocomplete="username"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                >
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input 
                    type="password" 
                    name="password" 
                    id="password" 
                    required 
                    autocomplete="current-password"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                >
            </div>
            <div>
                <input 
                    type="submit" 
                    value="Login" 
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-500 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                >
            </div>
        </form>
        <?php if ($error): ?>
            <p class="mt-4 text-center text-sm text-red-600"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>
    </div>
</body>
</html>