<?php
// Prevent output buffering issues
ob_start();
header('Content-Type: application/json; charset=UTF-8');

// Start session before any output
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database configuration
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'intelligent_id_db';

// Connect to MySQL server
$db = @new mysqli($db_host, $db_user, $db_pass);

if ($db->connect_error) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Cannot connect to MySQL: ' . $db->connect_error,
        'hint' => 'Make sure MySQL is running in XAMPP!'
    ]);
    exit;
}

// Create database if it doesn't exist
$db->query("CREATE DATABASE IF NOT EXISTS $db_name CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

// Select database
if (!$db->select_db($db_name)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Cannot select database: ' . $db->error]);
    exit;
}

$db->set_charset('utf8mb4');

// --- Schema Maintenance (Ensure columns exist) ---
$schema_tables = ['Student', 'FacultyStaff', 'Vehicle', 'Visitor', 'StudentViolation'];
foreach ($schema_tables as $t) {
    // Create tables if missing
    if ($t === 'StudentViolation') {
        $db->query("CREATE TABLE IF NOT EXISTS StudentViolation (
            violation_id INT AUTO_INCREMENT PRIMARY KEY,
            student_id VARCHAR(50),
            violation_type VARCHAR(100),
            penalty VARCHAR(100) DEFAULT 'Warning',
            status VARCHAR(50) DEFAULT 'Active',
            description TEXT,
            date_violation DATE,
            is_archived TINYINT(1) DEFAULT 0
        )");
    }
    // Add is_archived if missing
    $check = $db->query("SHOW COLUMNS FROM `$t` LIKE 'is_archived'");
    if ($check && $check->num_rows === 0) {
        $db->query("ALTER TABLE `$t` ADD COLUMN is_archived TINYINT(1) DEFAULT 0");
    }

    // Violation specific: penalty and status
    if ($t === 'StudentViolation') {
        // Change date_violation to DATETIME if it is DATE
        $check = $db->query("SHOW COLUMNS FROM `StudentViolation` LIKE 'date_violation'");
        if ($check) {
            $colData = $check->fetch_assoc();
            if (strtolower($colData['Type']) === 'date') {
                $db->query("ALTER TABLE `StudentViolation` MODIFY COLUMN date_violation DATETIME");
            }
        }
        foreach (['penalty' => "VARCHAR(100) DEFAULT 'Warning'", 'status' => "VARCHAR(50) DEFAULT 'Active'"] as $col => $def) {
            $check = $db->query("SHOW COLUMNS FROM `StudentViolation` LIKE '$col'");
            if ($check && $check->num_rows === 0) {
                $db->query("ALTER TABLE `StudentViolation` ADD COLUMN $col $def");
            }
        }
    }
    
    // Student specific: Parent info for SMS
    if ($t === 'Student') {
        $check = $db->query("SHOW COLUMNS FROM `Student` LIKE 'parent_name'");
        if ($check && $check->num_rows === 0) {
            $db->query("ALTER TABLE `Student` ADD COLUMN parent_name VARCHAR(100) DEFAULT ''");
            $db->query("ALTER TABLE `Student` ADD COLUMN parent_contact VARCHAR(20) DEFAULT ''");
        }
    }

    // Visitor specific: Visiting column
    if ($t === 'Visitor') {
        $check = $db->query("SHOW COLUMNS FROM `Visitor` LIKE 'visiting'");
        if ($check && $check->num_rows === 0) {
            $db->query("ALTER TABLE `Visitor` ADD COLUMN visiting VARCHAR(100) DEFAULT ''");
        }
    }

    // Add UNIQUE to plate_number for Vehicle if missing
    if ($t === 'Vehicle') {
        $check = $db->query("SHOW INDEX FROM `Vehicle` WHERE Key_name = 'plate_number_unique'");
        if ($check && $check->num_rows === 0) {
            $db->query("ALTER TABLE `Vehicle` ADD CONSTRAINT plate_number_unique UNIQUE (plate_number)");
        }
    }

    // Add created_at and updated_at if missing
    $columns = [
        'created_at' => "TIMESTAMP DEFAULT CURRENT_TIMESTAMP",
        'updated_at' => "TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP"
    ];

    foreach ($columns as $col => $def) {
        $check = $db->query("SHOW COLUMNS FROM `$t` LIKE '$col'");
        if ($check && $check->num_rows === 0) {
            $db->query("ALTER TABLE `$t` ADD COLUMN $col $def");
        }
    }
}

function getJsonPayload(): array
{
    $body = file_get_contents('php://input');
    $data = json_decode($body, true);
    if (!is_array($data)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid JSON payload.']);
        exit;
    }
    return $data;
}

function sendJson(array $payload): void
{
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode($payload);
    exit;
}
?>
