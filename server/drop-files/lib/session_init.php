<?php
/**
 * TaksoRide - Session Initialization
 * Must be included BEFORE any session_start() calls
 */

// Session configuration - must be set before session_start()
if (session_status() === PHP_SESSION_NONE) {
    @ini_set('session.cookie_httponly', '1');
    @ini_set('session.use_only_cookies', '1');
    @ini_set('session.cookie_secure', '0'); // Set to 1 for HTTPS in production
    @ini_set('session.cookie_lifetime', '0');
    @ini_set('session.gc_maxlifetime', '86400'); // 24 hours
    @ini_set('session.use_strict_mode', '1');
    @ini_set('session.use_trans_sid', '0');
}
?>
