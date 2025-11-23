<?php
// Mock environment
$_REQUEST['action'] = 'getTrips';
$_SERVER['REQUEST_METHOD'] = 'GET';
define('IS_API', true);

// Capture output
ob_start();
include 'api.php';
$output = ob_get_clean();

echo "Output length: " . strlen($output) . "\n";
echo "First 100 chars: " . substr($output, 0, 100) . "\n";
$json = json_decode($output);
if ($json) {
    echo "JSON is VALID.\n";
    echo "Status: " . ($json->status ?? 'no status') . "\n";
    if (isset($json->message)) echo "Message: " . $json->message . "\n";
} else {
    echo "JSON is INVALID.\n";
    echo "Last JSON error: " . json_last_error_msg() . "\n";
}
?>
