<?php
/**
 * Sample DB and SMTP config for TravelATH services.
 *
 * Copy to src/services/db_connect.php and fill with real credentials.
 * NEVER commit real credentials.
 */

// Database configuration
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'travel_agency_db';

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    if (!headers_sent()) { header('Content-Type: application/json'); }
    echo json_encode(['status'=>'error','message'=>'Database connection failed']);
    exit;
}
$conn->set_charset('utf8mb4');

date_default_timezone_set('Asia/Kathmandu');

// SMTP Configuration (use provider/app password)
define('MAIL_SMTP_HOST', 'dulalpratyush@gmail.com');
define('MAIL_SMTP_PORT', 587);
define('MAIL_SMTP_USER', 'dulalpratyush@gmail.com');
define('MAIL_SMTP_PASS', 'crrv qsqi angt smks');
define('MAIL_FROM_EMAIL', 'user@example.com');
define('MAIL_FROM_NAME', 'Travel Agency');
