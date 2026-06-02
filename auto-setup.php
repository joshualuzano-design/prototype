<?php

/**
 * AUTO-SETUP - Idempotent database initialization
 * Visit: http://localhost/thesis/auto-setup.php
 */

header('Content-Type: application/json; charset=UTF-8');

$host = 'localhost';
$user = 'root';
$password = '';
$dbName = 'intelligent_id_db';
$schemaPath = __DIR__ . '/schema.sql';

$db = @new mysqli($host, $user, $password);

if ($db->connect_error) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'MySQL connection failed.',
        'error' => $db->connect_error,
        'fix' => 'Start Apache and MySQL in XAMPP, then open http://localhost/thesis/setup.html'
    ]);
    exit;
}

if (!file_exists($schemaPath)) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'schema.sql was not found.',
        'error' => $schemaPath
    ]);
    exit;
}

$schema = file_get_contents($schemaPath);
if ($schema === false || trim($schema) === '') {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'schema.sql is empty or unreadable.'
    ]);
    exit;
}

$statements = array_filter(array_map('trim', explode(';', $schema)));
$executed = 0;
$errors = [];

foreach ($statements as $statement) {
    if ($db->query($statement)) {
        $executed++;
        continue;
    }

    $errors[] = [
        'statement' => substr($statement, 0, 120),
        'error' => $db->error
    ];
}

if (!$db->select_db($dbName)) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database was not created successfully.',
        'error' => $db->error,
        'details' => $errors
    ]);
    exit;
}

$checks = [
    'admin_table' => false,
    'student_table' => false,
    'default_admin' => false
];

$tableResult = $db->query("SHOW TABLES LIKE 'Admin'");
if ($tableResult && $tableResult->num_rows > 0) {
    $checks['admin_table'] = true;
}

$tableResult = $db->query("SHOW TABLES LIKE 'Student'");
if ($tableResult && $tableResult->num_rows > 0) {
    $checks['student_table'] = true;
}

$adminResult = $db->query("SELECT COUNT(*) AS total FROM Admin WHERE username = 'admin'");
if ($adminResult) {
    $row = $adminResult->fetch_assoc();
    $checks['default_admin'] = (int) $row['total'] > 0;
}

$db->close();

$isReady = $checks['admin_table'] && $checks['student_table'] && $checks['default_admin'];

if (!$isReady) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Setup did not finish cleanly.',
        'checks' => $checks,
        'details' => $errors
    ]);
    exit;
}

echo json_encode([
    'success' => true,
    'message' => 'System initialized successfully.',
    'database' => $dbName,
    'statements_executed' => $executed,
    'warnings' => $errors,
    'credentials' => [
        'username' => 'admin',
        'password' => 'admin123'
    ],
    'next_step' => 'http://localhost/thesis/admin-login.html'
]);
