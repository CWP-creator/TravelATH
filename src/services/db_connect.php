<?php
/**
 * Database Connection Configuration
 * 
 * IMPORTANT: This file contains sensitive information.
 * Make sure it's listed in .gitignore
 */

// Database configuration
$db_host = 'localhost';
$db_user = 'root';          // Change this to your database username
$db_pass = '';              // Change this to your database password
$db_name = 'travel_agency_db';     // Change this to your database name

// Create connection
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_error) {
    // Log error without exposing details
    error_log("Database connection failed: " . $conn->connect_error);
    die(json_encode([
        'status' => 'error',
        'message' => 'Database connection failed. Please contact administrator.'
    ]));
}

// Set character set
$conn->set_charset("utf8mb4");

// Optional: Set timezone
date_default_timezone_set('Asia/Kathmandu');
?>