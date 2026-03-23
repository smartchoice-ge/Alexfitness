<?php
session_start();
header('Content-Type: application/json');

// Debug session
error_log("Group Workout API - Session ID: " . session_id());
error_log("Group Workout API - Session data: " . print_r($_SESSION, true));

// Check if user is logged in (matching profilepage.php session check)
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo json_encode([
        'success' => false, 
        'message' => 'User not logged in',
        'debug' => [
            'session_id' => session_id(),
            'loggedin' => $_SESSION['loggedin'] ?? 'not set',
            'session_keys' => array_keys($_SESSION)
        ]
    ]);
    exit;
}

require_once '../mssql_connection.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$response = ['success' => false];

// Get user's phone number from session
$username = $_SESSION['username'] ?? '';

// Get client ID from phone number
$clientId = null;
if ($username) {
    $query = "SELECT id FROM Clients WHERE Phone = ?";
    $params = [$username];
    $stmt = sqlsrv_query($mssqlconn, $query, $params);
    if ($stmt && $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $clientId = $row['id'];
    }
}

if (!$clientId) {
    echo json_encode(['success' => false, 'message' => 'Client not found']);
    exit;
}

switch ($action) {
    case 'get_available_sessions':
        // Get upcoming workout sessions (next 30 days)
        $query = "SELECT 
                    gws.ID as session_id,
                    gws.StartDateTime,
                    gws.EndDateTime,
                    gws.Capacity,
                    gws.BookedCount,
                    gws.Status,
                    gws.IsCancelled,
                    gw.Title,
                    gw.GroupID,
                    gw.TrainerID,
                    gw.Location,
                    g.Name as GroupName,
                    u.UserName as TrainerName,
                    CASE WHEN gwb.ID IS NOT NULL THEN 1 ELSE 0 END as IsBooked,
                    gwb.ID as UserBookingID
                FROM GroupWorkoutSessions gws
                INNER JOIN GroupWorkouts gw ON gws.GroupWorkoutID = gw.ID
                INNER JOIN Groups g ON gw.GroupID = g.ID
                LEFT JOIN users u ON gw.TrainerID = u.id
                LEFT JOIN GroupWorkoutBookings gwb ON gws.ID = gwb.GroupWorkoutSessionID AND gwb.ClientID = ? AND gwb.Status = 'confirmed'
                WHERE gws.StartDateTime >= GETDATE()
                    AND gws.StartDateTime <= DATEADD(day, 30, GETDATE())
                    AND gws.Status = 'scheduled'
                    AND gws.IsCancelled = 0
                    AND gw.Active = 1
                    AND g.Active = 1
                ORDER BY gws.StartDateTime ASC";
        
        $params = [$clientId];
        $stmt = sqlsrv_query($mssqlconn, $query, $params);
        
        if ($stmt === false) {
            $response['message'] = 'Database error: ' . print_r(sqlsrv_errors(), true);
            break;
        }
        
        $sessions = [];
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $availableSpots = $row['Capacity'] - $row['BookedCount'];
            $sessions[] = [
                'session_id' => $row['session_id'],
                'title' => $row['Title'],
                'group_name' => $row['GroupName'],
                'trainer_name' => $row['TrainerName'] ?? 'TBA',
                'start_time' => $row['StartDateTime']->format('Y-m-d H:i:s'),
                'end_time' => $row['EndDateTime']->format('Y-m-d H:i:s'),
                'location' => $row['Location'] ?? 'Main Studio',
                'capacity' => $row['Capacity'],
                'booked_count' => $row['BookedCount'],
                'available_spots' => $availableSpots,
                'is_full' => ($availableSpots <= 0),
                'is_booked' => (bool)$row['IsBooked'],
                'user_booking_id' => $row['UserBookingID']
            ];
        }
        
        $response = [
            'success' => true,
            'sessions' => $sessions
        ];
        break;
    
    case 'get_my_bookings':
        // Get user's current bookings
        $query = "SELECT 
                    gwb.ID as booking_id,
                    gwb.BookedAt,
                    gwb.Status,
                    gws.ID as session_id,
                    gws.StartDateTime,
                    gws.EndDateTime,
                    gw.Title,
                    g.Name as GroupName,
                    u.UserName as TrainerName,
                    gw.Location
                FROM GroupWorkoutBookings gwb
                INNER JOIN GroupWorkoutSessions gws ON gwb.GroupWorkoutSessionID = gws.ID
                INNER JOIN GroupWorkouts gw ON gws.GroupWorkoutID = gw.ID
                INNER JOIN Groups g ON gw.GroupID = g.ID
                LEFT JOIN users u ON gw.TrainerID = u.id
                WHERE gwb.ClientID = ?
                    AND gwb.Status = 'confirmed'
                    AND gws.StartDateTime >= GETDATE()
                ORDER BY gws.StartDateTime ASC";
        
        $params = [$clientId];
        $stmt = sqlsrv_query($mssqlconn, $query, $params);
        
        if ($stmt === false) {
            $response['message'] = 'Database error';
            break;
        }
        
        $bookings = [];
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $bookings[] = [
                'booking_id' => $row['booking_id'],
                'session_id' => $row['session_id'],
                'title' => $row['Title'],
                'group_name' => $row['GroupName'],
                'trainer_name' => $row['TrainerName'] ?? 'TBA',
                'start_time' => $row['StartDateTime']->format('Y-m-d H:i:s'),
                'end_time' => $row['EndDateTime']->format('Y-m-d H:i:s'),
                'location' => $row['Location'] ?? 'Main Studio',
                'booked_at' => $row['BookedAt']->format('Y-m-d H:i:s')
            ];
        }
        
        $response = [
            'success' => true,
            'bookings' => $bookings
        ];
        break;
    
    case 'book_session':
        $sessionId = intval($_POST['session_id'] ?? 0);
        
        if (!$sessionId) {
            $response['message'] = 'Invalid session ID';
            break;
        }
        
        // Check if session is available
        $checkQuery = "SELECT gws.Capacity, gws.BookedCount, gws.Status, gws.IsCancelled,
                              (SELECT COUNT(*) FROM GroupWorkoutBookings 
                               WHERE GroupWorkoutSessionID = gws.ID 
                               AND ClientID = ? 
                               AND Status = 'confirmed') as AlreadyBooked
                       FROM GroupWorkoutSessions gws
                       WHERE gws.ID = ?";
        
        $params = [$clientId, $sessionId];
        $stmt = sqlsrv_query($mssqlconn, $checkQuery, $params);
        
        if ($stmt === false || !($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC))) {
            $response['message'] = 'Session not found';
            break;
        }
        
        if ($row['AlreadyBooked'] > 0) {
            $response['message'] = 'You have already booked this session';
            break;
        }
        
        if ($row['IsCancelled']) {
            $response['message'] = 'This session has been cancelled';
            break;
        }
        
        if ($row['BookedCount'] >= $row['Capacity']) {
            $response['message'] = 'This session is full';
            break;
        }
        
        // Start transaction
        sqlsrv_begin_transaction($mssqlconn);
        
        try {
            // Insert booking
            $insertQuery = "INSERT INTO GroupWorkoutBookings 
                           (GroupWorkoutSessionID, ClientID, Source, Status, BookedAt, RecordDate)
                           VALUES (?, ?, 'profile', 'confirmed', GETDATE(), GETDATE())";
            
            $params = [$sessionId, $clientId];
            $stmt = sqlsrv_query($mssqlconn, $insertQuery, $params);
            
            if ($stmt === false) {
                $errors = sqlsrv_errors();
                $errorMsg = 'Failed to create booking: ';
                if ($errors) {
                    $errorMsg .= $errors[0]['message'];
                }
                throw new Exception($errorMsg);
            }
            
            // Update booked count
            $updateQuery = "UPDATE GroupWorkoutSessions 
                           SET BookedCount = BookedCount + 1
                           WHERE ID = ?";
            
            $params = [$sessionId];
            $stmt = sqlsrv_query($mssqlconn, $updateQuery, $params);
            
            if ($stmt === false) {
                $errors = sqlsrv_errors();
                $errorMsg = 'Failed to update session: ';
                if ($errors) {
                    $errorMsg .= $errors[0]['message'];
                }
                throw new Exception($errorMsg);
            }
            
            sqlsrv_commit($mssqlconn);
            
            $response = [
                'success' => true,
                'message' => 'Booking successful!'
            ];
            
        } catch (Exception $e) {
            sqlsrv_rollback($mssqlconn);
            $response['message'] = $e->getMessage();
        }
        break;
    
    case 'cancel_booking':
        $sessionId = intval($_POST['session_id'] ?? 0);
        
        if (!$sessionId) {
            $response['message'] = 'Invalid session ID';
            break;
        }
        
        // Check if booking exists and session hasn't started
        $checkQuery = "SELECT gwb.ID, gws.StartDateTime
                       FROM GroupWorkoutBookings gwb
                       INNER JOIN GroupWorkoutSessions gws ON gwb.GroupWorkoutSessionID = gws.ID
                       WHERE gwb.GroupWorkoutSessionID = ?
                       AND gwb.ClientID = ?
                       AND gwb.Status = 'confirmed'";
        
        $params = [$sessionId, $clientId];
        $stmt = sqlsrv_query($mssqlconn, $checkQuery, $params);
        
        if ($stmt === false || !($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC))) {
            $response['message'] = 'Booking not found';
            break;
        }
        
        $bookingId = $row['ID'];
        $startTime = $row['StartDateTime'];
        
        // Check if session is within 2 hours (optional cancellation policy)
        $now = new DateTime();
        $timeDiff = $startTime->getTimestamp() - $now->getTimestamp();
        
        if ($timeDiff < 7200) { // 2 hours = 7200 seconds
            $response['message'] = 'Cannot cancel booking within 2 hours of session start';
            break;
        }
        
        // Start transaction
        sqlsrv_begin_transaction($mssqlconn);
        
        try {
            // Update booking status to cancelled
            $updateBookingQuery = "UPDATE GroupWorkoutBookings 
                                  SET Status = 'cancelled'
                                  WHERE ID = ?";
            
            $params = [$bookingId];
            $stmt = sqlsrv_query($mssqlconn, $updateBookingQuery, $params);
            
            if ($stmt === false) {
                throw new Exception('Failed to cancel booking');
            }
            
            // Decrease booked count
            $updateSessionQuery = "UPDATE GroupWorkoutSessions 
                                  SET BookedCount = BookedCount - 1
                                  WHERE ID = ?";
            
            $params = [$sessionId];
            $stmt = sqlsrv_query($mssqlconn, $updateSessionQuery, $params);
            
            if ($stmt === false) {
                throw new Exception('Failed to update session');
            }
            
            sqlsrv_commit($mssqlconn);
            
            $response = [
                'success' => true,
                'message' => 'Booking cancelled successfully'
            ];
            
        } catch (Exception $e) {
            sqlsrv_rollback($mssqlconn);
            $response['message'] = $e->getMessage();
        }
        break;
    
    default:
        $response['message'] = 'Invalid action';
}

sqlsrv_close($mssqlconn);
echo json_encode($response);
?>
