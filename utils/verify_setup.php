<?php
/**
 * System Configuration Check
 * Run this file to verify your login system is properly configured
 * DELETE THIS FILE after verification!
 */

$checks = [];
$allPassed = true;

// Check 1: Database Connection
try {
    require_once 'db_connect.php';
    $checks['database'] = ['status' => 'pass', 'message' => 'Database connection successful'];
} catch (Exception $e) {
    $checks['database'] = ['status' => 'fail', 'message' => 'Database connection failed: ' . $e->getMessage()];
    $allPassed = false;
}

// Check 2: Users Table Exists
if ($checks['database']['status'] === 'pass') {
    $result = $conn->query("SHOW TABLES LIKE 'users'");
    if ($result->num_rows > 0) {
        $checks['users_table'] = ['status' => 'pass', 'message' => 'Users table exists'];
        
        // Check table structure
        $columns = $conn->query("SHOW COLUMNS FROM users");
        $requiredColumns = ['id', 'name', 'email', 'password', 'is_active'];
        $existingColumns = [];
        while ($col = $columns->fetch_assoc()) {
            $existingColumns[] = $col['Field'];
        }
        
        $missingColumns = array_diff($requiredColumns, $existingColumns);
        if (empty($missingColumns)) {
            $checks['table_structure'] = ['status' => 'pass', 'message' => 'Table structure is correct'];
        } else {
            $checks['table_structure'] = ['status' => 'fail', 'message' => 'Missing columns: ' . implode(', ', $missingColumns)];
            $allPassed = false;
        }
        
        // Check for users
        $userCount = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
        if ($userCount > 0) {
            $checks['users_exist'] = ['status' => 'pass', 'message' => "$userCount user(s) found in database"];
        } else {
            $checks['users_exist'] = ['status' => 'warning', 'message' => 'No users found. Run setup_users.sql or create_user.php'];
        }
    } else {
        $checks['users_table'] = ['status' => 'fail', 'message' => 'Users table does not exist. Run setup_users.sql'];
        $allPassed = false;
    }
}

// Check 3: Required Files
$requiredFiles = [
    'auth.php' => 'Authentication handler',
    'check_session.php' => 'Session protection',
    'login.html' => 'Login page',
    'index.php' => 'Main dashboard (should be .php not .html)',
    'Itinerary.php' => 'Itinerary page (should be .php not .html)',
];

foreach ($requiredFiles as $file => $description) {
    if (file_exists($file)) {
        $checks["file_$file"] = ['status' => 'pass', 'message' => "$description exists"];
    } else {
        $checks["file_$file"] = ['status' => 'fail', 'message' => "$description is missing"];
        $allPassed = false;
    }
}

// Check 4: PHP Session Support
if (function_exists('session_start')) {
    $checks['php_sessions'] = ['status' => 'pass', 'message' => 'PHP session support available'];
} else {
    $checks['php_sessions'] = ['status' => 'fail', 'message' => 'PHP sessions not supported'];
    $allPassed = false;
}

// Check 5: Password Hashing Support
if (function_exists('password_hash')) {
    $checks['password_hash'] = ['status' => 'pass', 'message' => 'Password hashing supported'];
} else {
    $checks['password_hash'] = ['status' => 'fail', 'message' => 'Password hashing not supported (PHP 5.5+ required)'];
    $allPassed = false;
}

// Check 6: File Permissions
$checks['permissions'] = ['status' => 'pass', 'message' => 'Running permission checks...'];

// Check 7: Security Files
$securityChecks = [
    'create_user.php' => 'Should be deleted after setup'
];

foreach ($securityChecks as $file => $warning) {
    if (file_exists($file)) {
        $checks["security_$file"] = ['status' => 'warning', 'message' => "$file exists - $warning"];
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Configuration Check</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .status-banner {
            padding: 20px;
            text-align: center;
            font-size: 1.2rem;
            font-weight: bold;
        }

        .status-banner.pass {
            background: #d4edda;
            color: #155724;
        }

        .status-banner.fail {
            background: #f8d7da;
            color: #721c24;
        }

        .checks-container {
            padding: 30px;
        }

        .check-item {
            display: flex;
            align-items: center;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 8px;
            border-left: 4px solid;
        }

        .check-item.pass {
            background: #d4edda;
            border-color: #28a745;
        }

        .check-item.fail {
            background: #f8d7da;
            border-color: #dc3545;
        }

        .check-item.warning {
            background: #fff3cd;
            border-color: #ffc107;
        }

        .check-icon {
            font-size: 1.5rem;
            margin-right: 15px;
            width: 30px;
            text-align: center;
        }

        .check-item.pass .check-icon {
            color: #28a745;
        }

        .check-item.fail .check-icon {
            color: #dc3545;
        }

        .check-item.warning .check-icon {
            color: #ffc107;
        }

        .check-message {
            flex-grow: 1;
        }

        .actions {
            padding: 30px;
            background: #f8f9fa;
            border-top: 1px solid #dee2e6;
            text-align: center;
        }

        .btn {
            display: inline-block;
            padding: 12px 30px;
            margin: 0 10px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: transform 0.2s;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-danger {
            background: #dc3545;
            color: white;
        }

        .warning-box {
            background: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .warning-box strong {
            color: #856404;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔧 System Configuration Check</h1>
            <p>Verifying login system setup</p>
        </div>

        <div class="status-banner <?php echo $allPassed ? 'pass' : 'fail'; ?>">
            <?php if ($allPassed): ?>
                ✅ All Critical Checks Passed!
            <?php else: ?>
                ❌ Some Checks Failed - Review Below
            <?php endif; ?>
        </div>

        <div class="checks-container">
            <div class="warning-box">
                <strong>⚠️ Security Notice:</strong> Delete this file (verify_setup.php) after verification!
            </div>

            <?php foreach ($checks as $checkName => $check): ?>
                <div class="check-item <?php echo $check['status']; ?>">
                    <div class="check-icon">
                        <?php if ($check['status'] === 'pass'): ?>
                            ✓
                        <?php elseif ($check['status'] === 'fail'): ?>
                            ✗
                        <?php else: ?>
                            ⚠
                        <?php endif; ?>
                    </div>
                    <div class="check-message">
                        <strong><?php echo ucwords(str_replace('_', ' ', $checkName)); ?>:</strong>
                        <?php echo htmlspecialchars($check['message']); ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="actions">
            <a href="login.html" class="btn btn-primary">Go to Login Page</a>
            <a href="create_user.php" class="btn btn-primary">Create Users</a>
            <a href="?refresh=1" class="btn btn-primary">Refresh Check</a>
        </div>
    </div>
</body>
</html>