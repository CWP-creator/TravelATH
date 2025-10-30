<?php
// Include this file at the top of any page that requires authentication
session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    $isApi = defined('IS_API') && IS_API === true; // treat API requests as AJAX for JSON responses
    $acceptsJson = isset($_SERVER['HTTP_ACCEPT']) && stripos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false;

    if ($isAjax || $isApi || $acceptsJson) {
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'error',
            'message' => 'Authentication required',
            'redirect' => 'login.html'
        ]);
        exit;
    }
    
    // Otherwise redirect to login page
    header('Location: login.html');
    exit;
}

// Optional: Set session timeout (30 minutes of inactivity)
$timeout_duration = 1800; // 30 minutes in seconds

if (isset($_SESSION['LAST_ACTIVITY']) && 
    (time() - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
    // Last request was more than 30 minutes ago
    session_unset();
    session_destroy();
    header('Location: login.html?timeout=1');
    exit;
}

// Update last activity time
$_SESSION['LAST_ACTIVITY'] = time();
?>