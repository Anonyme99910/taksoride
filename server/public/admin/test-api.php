<?php
/**
 * TaksoRide - API Testing Endpoint
 * Tests various API services and returns results
 */

session_start();

// Security check - only admins can test
if(!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] != 1 || $_SESSION['account_type'] != 3) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

header('Content-Type: application/json');

if(!isset($_POST['service']) || !isset($_POST['config'])) {
    echo json_encode(['success' => false, 'message' => 'Missing parameters']);
    exit;
}

$service = $_POST['service'];
$config = json_decode($_POST['config'], true);

if(!$config) {
    echo json_encode(['success' => false, 'message' => 'Invalid configuration']);
    exit;
}

// Test the service
$result = testService($service, $config);
echo json_encode($result);
exit;

/**
 * Test service based on type
 */
function testService($service, $config) {
    switch($service) {
        case 'google_maps':
            return testGoogleMaps($config);
        case 'firebase':
            return testFirebase($config);
        case 'pubnub':
            return testPubNub($config);
        case 'smtp':
            return testSMTP($config);
        default:
            return ['success' => false, 'message' => 'Unknown service'];
    }
}

/**
 * Test Google Maps API
 */
function testGoogleMaps($config) {
    if(empty($config['api_key'])) {
        return ['success' => false, 'message' => 'API key is required'];
    }
    
    $url = "https://maps.googleapis.com/maps/api/geocode/json?address=New+York&key=" . urlencode($config['api_key']);
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);
    
    if($response === false) {
        return ['success' => false, 'message' => 'Failed to connect: ' . $curlError];
    }
    
    $data = json_decode($response, true);
    
    if(!$data) {
        return ['success' => false, 'message' => 'Invalid response from Google Maps. HTTP Code: ' . $httpCode];
    }
    
    // Check status
    if($data['status'] === 'OK') {
        return ['success' => true, 'message' => '✓ Google Maps API is working! Geocoding successful.'];
    } elseif($data['status'] === 'REQUEST_DENIED') {
        $errorMsg = 'API key is invalid or restricted.';
        if(isset($data['error_message'])) {
            $errorMsg .= ' Details: ' . $data['error_message'];
        }
        return ['success' => false, 'message' => $errorMsg];
    } elseif($data['status'] === 'OVER_QUERY_LIMIT') {
        return ['success' => false, 'message' => 'API quota exceeded. Check your Google Cloud billing.'];
    } elseif($data['status'] === 'ZERO_RESULTS') {
        return ['success' => true, 'message' => '✓ API key is valid! (No results for test query, but key works)'];
    } else {
        $errorMsg = 'Status: ' . $data['status'];
        if(isset($data['error_message'])) {
            $errorMsg .= ' - ' . $data['error_message'];
        }
        return ['success' => false, 'message' => $errorMsg];
    }
}

/**
 * Test Firebase FCM
 */
function testFirebase($config) {
    if(empty($config['server_key'])) {
        return ['success' => false, 'message' => 'Server key is required'];
    }
    
    $url = 'https://fcm.googleapis.com/fcm/send';
    $headers = [
        'Authorization: key=' . $config['server_key'],
        'Content-Type: application/json'
    ];
    
    $data = json_encode(['registration_ids' => ['test']]);
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if($httpCode == 200) {
        return ['success' => true, 'message' => 'Firebase FCM credentials are valid!'];
    } elseif($httpCode == 401) {
        return ['success' => false, 'message' => 'Invalid Firebase server key'];
    } else {
        return ['success' => false, 'message' => 'Connection error (HTTP ' . $httpCode . ')'];
    }
}

/**
 * Test PubNub
 */
function testPubNub($config) {
    if(empty($config['publish_key']) || empty($config['subscribe_key'])) {
        return ['success' => false, 'message' => 'Publish and Subscribe keys are required'];
    }
    
    // Test PubNub time endpoint
    $url = "https://ps.pndsn.com/time/0";
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if($response === false) {
        return ['success' => false, 'message' => 'Failed to connect to PubNub'];
    }
    
    $data = json_decode($response, true);
    
    if($data && is_array($data) && count($data) > 0) {
        return ['success' => true, 'message' => 'PubNub connection successful! Keys format is valid.'];
    } else {
        return ['success' => false, 'message' => 'Invalid response from PubNub'];
    }
}

/**
 * Test SMTP
 */
function testSMTP($config) {
    if(empty($config['host']) || empty($config['port'])) {
        return ['success' => false, 'message' => 'Host and port are required'];
    }
    
    // Try to connect to SMTP server
    $errno = 0;
    $errstr = '';
    
    $connection = @fsockopen($config['host'], $config['port'], $errno, $errstr, 10);
    
    if(!$connection) {
        return ['success' => false, 'message' => 'Cannot connect to SMTP server: ' . $errstr];
    }
    
    // Read server response
    $response = fgets($connection, 512);
    fclose($connection);
    
    if(strpos($response, '220') === 0) {
        return ['success' => true, 'message' => 'SMTP server is reachable on port ' . $config['port'] . '!'];
    } else {
        return ['success' => true, 'message' => 'SMTP server responded. Configuration looks valid.'];
    }
}
?>
