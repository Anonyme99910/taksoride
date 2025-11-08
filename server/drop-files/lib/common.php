<?php
/**
 * TaksoRide - Common Initialization File
 * Core constants and helper functions
 */

// Prevent direct access
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(dirname(__DIR__)));
}

// Load Composer autoloaders
$autoloaders = [
    dirname(__DIR__) . '/vendor/autoload.php',           // Main dependencies (Firebase, libphonenumber)
    dirname(__DIR__) . '/google-client/vendor/autoload.php',  // Google API Client
    dirname(__DIR__) . '/pubnub/vendor/autoload.php',    // PubNub SDK
];

foreach ($autoloaders as $autoloader) {
    if (file_exists($autoloader)) {
        require_once $autoloader;
    }
}

// Core paths
define('FILES_FOLDER', dirname(__DIR__));
define('PUBLIC_PATH', BASE_PATH . '/public');
define('CUSTOMER_PHOTO_PATH', PUBLIC_PATH . '/assets/img/customers');
define('DRIVER_PHOTO_PATH', PUBLIC_PATH . '/assets/img/drivers');

// Site configuration
define('SITE_URL', 'http://localhost/hamma/server/public/');
define('SITE_NAME', 'TaksoRide');
define('SITE_TAGLINE', 'Your Reliable Ride Partner');

// Template constants (for compatibility with templates)
define('WEBSITE_NAME', SITE_NAME);
define('WEBSITE_DESC', SITE_TAGLINE);

// Application constants
define('APP_VERSION', '2.2.1');
define('DEMO', false); // Set to true for demo mode
define('GMAP_API_KEY', ''); // Google Maps API Key - Configure in Service Activation

// Driver allocation settings
define('DRIVER_ALLOCATE_ACCEPT_DURATION', 30); // seconds
define('LOCATION_INFO_VALID_AGE', 300); // seconds (5 minutes) - how old location data can be to consider driver online

// Error reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Timezone
date_default_timezone_set('UTC');

// Include subscription system
require_once(__DIR__ . '/subscription.php');

/**
 * Generate cryptographically secure random string
 */
function crypto_string($type = 'alnum', $len = 8) {
    switch ($type) {
        case 'alnum':
            $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            break;
        case 'alpha':
            $pool = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            break;
        case 'numeric':
            $pool = '0123456789';
            break;
        case 'distinct':
            $pool = '23456789abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ';
            break;
        default:
            $pool = (string)$type;
    }
    
    $str = '';
    $poolSize = strlen($pool);
    for ($i = 0; $i < $len; $i++) {
        $str .= $pool[random_int(0, $poolSize - 1)];
    }
    return $str;
}

/**
 * Sanitize input data
 */
function clean_input($data) {
    if (is_array($data)) {
        return array_map('clean_input', $data);
    }
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

/**
 * Check if user is logged in
 */
function is_logged_in() {
    return isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == 1;
}

/**
 * Get current user ID
 */
function get_user_id() {
    return isset($_SESSION['uid']) ? (int)$_SESSION['uid'] : 0;
}

/**
 * Simple translation function
 */
function __($text) {
    // Will be replaced by proper i18n later
    return $text;
}
