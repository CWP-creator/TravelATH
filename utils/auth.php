<?php
session_start();
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Connect to the database
$db_path = __DIR__ . '/../src/services/db_connect.php';
if (file_exists($db_path)) {
    require_once $db_path;
} else {
    respond('error', 'Database connection file not found. Please check your setup.');
}

$action = isset($_GET['action']) ? $_GET['action'] : '';

function respond($status, $message, $data = null) {
    $response = [
        'status' => $status,
        'message' => $message
    ];
    if ($data !== null) {
        $response['data'] = $data;
    }
    echo json_encode($response);
    exit;
}

// Login action
if ($action === 'login') {
    error_log("Login attempt started for email: " . (isset($_POST['email']) ? $_POST['email'] : 'not set'));

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        error_log("Invalid request method: " . $_SERVER['REQUEST_METHOD']);
        respond('error', 'Invalid request method');
    }

    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    error_log("Email: '$email', Password provided: " . (empty($password) ? 'no' : 'yes'));

    if (empty($email) || empty($password)) {
        error_log("Empty email or password");
        respond('error', 'Email and password are required');
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        error_log("Invalid email format: $email");
        respond('error', 'Invalid email format');
    }

    try {
        // Check if connection exists
        if (!isset($conn)) {
            error_log("Database connection not set");
            respond('error', 'Database connection not available');
        }
        error_log("Database connection available");

        $stmt = $conn->prepare("SELECT id, name, email, password FROM users WHERE email = ? AND is_active = 1");
        if (!$stmt) {
            error_log("Query preparation failed: " . $conn->error);
            respond('error', 'Database query preparation failed: ' . $conn->error);
        }
        error_log("Query prepared successfully");

        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        error_log("Query executed, num_rows: " . $result->num_rows);

        if ($result->num_rows === 0) {
            error_log("No user found with email: $email");
            respond('error', 'Invalid email or password');
        }

        $user = $result->fetch_assoc();
        error_log("User found: ID " . $user['id'] . ", Name: " . $user['name']);

        // Verify password
        if (!password_verify($password, $user['password'])) {
            error_log("Password verification failed for user: $email");
            respond('error', 'Invalid email or password');
        }
        error_log("Password verified successfully");

        // Set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['logged_in'] = true;
        error_log("Session variables set");

        // Update last login
        $updateStmt = $conn->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
        if ($updateStmt) {
            $updateStmt->bind_param('i', $user['id']);
            $updateStmt->execute();
            error_log("Last login updated");
        } else {
            error_log("Failed to prepare update statement");
        }

        error_log("Login successful for user: $email");
        respond('success', 'Login successful', [
            'user' => [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email']
            ]
        ]);

    } catch (Exception $e) {
        error_log("Exception in login: " . $e->getMessage());
        respond('error', 'Database error: ' . $e->getMessage());
    }
}

// Logout action
if ($action === 'logout') {
    // Clear all session variables
    $_SESSION = array();
    
    // Delete the session cookie if it exists
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    // Destroy the session
    session_destroy();
    
    respond('success', 'Logged out successfully');
}

// Check authentication status
if ($action === 'checkAuth') {
    if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
        respond('success', 'Authenticated', [
            'user' => [
                'id' => $_SESSION['user_id'],
                'name' => $_SESSION['user_name'],
                'email' => $_SESSION['user_email']
            ]
        ]);
    } else {
        respond('error', 'Not authenticated');
    }
}

respond('error', 'Invalid action');
?>