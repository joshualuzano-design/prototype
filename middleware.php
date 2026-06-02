<?php
// Security Middleware for API Protection
// Note: session_start() should be called AFTER db_config.php is included

/**
 * Check if admin is authenticated
 */
function requireAuth()
{
    if (!isset($_SESSION['admin_id']) || !isset($_SESSION['admin_username'])) {
        http_response_code(401);
        sendJson(['success' => false, 'message' => 'Unauthorized. Please login first.']);
    }
}

/**
 * Validate request method
 */
function requireMethod($allowed_methods)
{
    $current_method = $_SERVER['REQUEST_METHOD'];
    if (!in_array($current_method, $allowed_methods)) {
        http_response_code(405);
        sendJson(['success' => false, 'message' => 'Method not allowed. Allowed: ' . implode(', ', $allowed_methods)]);
    }
}

/**
 * Validate required fields in JSON payload
 */
function validateRequired($data, $required_fields)
{
    $missing = [];
    foreach ($required_fields as $field) {
        if (!isset($data[$field]) || trim($data[$field] ?? '') === '') {
            $missing[] = $field;
        }
    }

    if (!empty($missing)) {
        http_response_code(400);
        sendJson([
            'success' => false,
            'message' => 'Missing required fields: ' . implode(', ', $missing)
        ]);
    }
}

/**
 * Sanitize input string
 */
function sanitizeInput($input)
{
    return trim(htmlspecialchars($input, ENT_QUOTES, 'UTF-8'));
}

/**
 * Log action to audit trail
 */
function logAction($db, $admin_id, $action, $details)
{
    try {
        $timestamp = date('Y-m-d H:i:s');
        $stmt = $db->prepare('INSERT INTO AuditLog (admin_id, action, details, timestamp) VALUES (?, ?, ?, ?)');
        $stmt->bind_param('ssss', $admin_id, $action, $details, $timestamp);
        $stmt->execute();
        $stmt->close();
    } catch (Exception $e) {
        error_log('Audit log error: ' . $e->getMessage());
    }
}

/**
 * Error handler
 */
function handleError($error_msg, $http_code = 500)
{
    http_response_code($http_code);
    sendJson(['success' => false, 'message' => $error_msg]);
}

/**
 * Success response
 */
function handleSuccess($data = null, $message = 'Success')
{
    $response = ['success' => true, 'message' => $message];
    if ($data !== null) {
        $response['data'] = $data;
    }
    sendJson($response);
}
