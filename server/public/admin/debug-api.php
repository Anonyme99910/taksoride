<?php
/**
 * Debug API Testing - Shows actual responses
 */

// Get the API key from query string
$apiKey = isset($_GET['key']) ? $_GET['key'] : '';

if(empty($apiKey)) {
    die('Please provide API key: debug-api.php?key=YOUR_KEY');
}

echo "<h2>Google Maps API Debug Test</h2>";
echo "<p>Testing API Key: " . htmlspecialchars(substr($apiKey, 0, 20)) . "...</p>";

$url = "https://maps.googleapis.com/maps/api/geocode/json?address=New+York&key=" . urlencode($apiKey);

echo "<h3>Request URL:</h3>";
echo "<pre>" . htmlspecialchars($url) . "</pre>";

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

echo "<h3>HTTP Code:</h3>";
echo "<pre>" . $httpCode . "</pre>";

if($curlError) {
    echo "<h3>cURL Error:</h3>";
    echo "<pre style='color:red;'>" . htmlspecialchars($curlError) . "</pre>";
}

echo "<h3>Raw Response:</h3>";
echo "<pre>" . htmlspecialchars($response) . "</pre>";

$data = json_decode($response, true);

echo "<h3>Parsed Response:</h3>";
echo "<pre>" . print_r($data, true) . "</pre>";

if($data) {
    echo "<h3>Status:</h3>";
    echo "<pre>" . htmlspecialchars($data['status']) . "</pre>";
    
    if(isset($data['error_message'])) {
        echo "<h3>Error Message:</h3>";
        echo "<pre style='color:red;'>" . htmlspecialchars($data['error_message']) . "</pre>";
    }
    
    if($data['status'] === 'OK') {
        echo "<h2 style='color:green;'>✓ API KEY IS WORKING!</h2>";
    } else {
        echo "<h2 style='color:red;'>✗ API KEY HAS ISSUES</h2>";
    }
}
?>
