<?php

/**
 * DIAGNOSTIC TOOL - Check all system components
 * Visit: http://localhost/thesis/diagnose.php
 */

header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html>

<head>
    <title>System Diagnostic</title>
    <style>
        body {
            font-family: Arial;
            background: #f5f5f5;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
        }

        .check {
            margin: 15px 0;
            padding: 15px;
            border-radius: 5px;
        }

        .pass {
            background: #d4edda;
            border-left: 4px solid #28a745;
        }

        .fail {
            background: #f8d7da;
            border-left: 4px solid #dc3545;
        }

        .warn {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
        }

        h1 {
            color: #333;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
        }

        h2 {
            color: #555;
            margin-top: 30px;
        }

        code {
            background: #f4f4f4;
            padding: 2px 6px;
            border-radius: 3px;
        }

        .details {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>🔍 Intelligent ID System - Diagnostic Report</h1>

        <?php
        $results = [];
        $critical_fail = false;

        // 1. Check PHP Version
        echo '<h2>1. PHP Configuration</h2>';
        $php_version = phpversion();
        if (version_compare($php_version, '7.4', '>=')) {
            echo '<div class="check pass">✓ PHP Version: ' . $php_version . '</div>';
        } else {
            echo '<div class="check fail">✗ PHP Version: ' . $php_version . ' (requires 7.4+)</div>';
            $critical_fail = true;
        }

        // 2. Check Extensions
        echo '<h2>2. Required Extensions</h2>';
        $extensions = ['mysqli', 'json', 'PDO'];
        foreach ($extensions as $ext) {
            if (extension_loaded($ext)) {
                echo '<div class="check pass">✓ ' . $ext . ' extension loaded</div>';
            } else {
                echo '<div class="check fail">✗ ' . $ext . ' extension NOT loaded</div>';
                $critical_fail = true;
            }
        }

        // 3. Check File Permissions
        echo '<h2>3. File System</h2>';
        $files_to_check = [
            'schema.sql' => 'Database schema',
            'db_config.php' => 'Database config',
            'middleware.php' => 'Middleware',
            'api/login.php' => 'Login API'
        ];

        foreach ($files_to_check as $file => $desc) {
            if (file_exists($file)) {
                echo '<div class="check pass">✓ ' . $desc . ' (' . $file . ') exists</div>';
            } else {
                echo '<div class="check fail">✗ ' . $desc . ' (' . $file . ') NOT FOUND</div>';
                $critical_fail = true;
            }
        }

        // 4. Check MySQL Connection
        echo '<h2>4. MySQL Connection</h2>';
        $host = 'localhost';
        $user = 'root';
        $password = '';

        $db = @new mysqli($host, $user, $password);
        if (!$db->connect_error) {
            echo '<div class="check pass">✓ Connected to MySQL at ' . $host . ' as ' . $user . '</div>';

            // Check if database exists
            $databases = $db->query("SHOW DATABASES LIKE 'intelligent_id_db'");
            if ($databases && $databases->num_rows > 0) {
                echo '<div class="check pass">✓ Database <code>intelligent_id_db</code> exists</div>';

                // Check if tables exist
                $db->select_db('intelligent_id_db');
                $tables = $db->query("SHOW TABLES");
                $table_count = $tables ? $tables->num_rows : 0;

                if ($table_count > 0) {
                    echo '<div class="check pass">✓ Database has ' . $table_count . ' tables</div>';

                    // Check Admin table
                    $admin_check = $db->query("SELECT COUNT(*) as count FROM Admin");
                    if ($admin_check) {
                        $result = $admin_check->fetch_assoc();
                        if ($result['count'] > 0) {
                            echo '<div class="check pass">✓ Admin user exists (count: ' . $result['count'] . ')</div>';
                        } else {
                            echo '<div class="check warn">⚠ Admin user NOT CREATED - run setup.php first</div>';
                        }
                    }
                } else {
                    echo '<div class="check warn">⚠ Database has NO TABLES - run setup.php to create them</div>';
                }
            } else {
                echo '<div class="check warn">⚠ Database <code>intelligent_id_db</code> does NOT exist - run setup.php to create it</div>';
            }

            $db->close();
        } else {
            echo '<div class="check fail">✗ MySQL Connection Failed: ' . $db->connect_error . '</div>';
            echo '<div class="details">Make sure MySQL is RUNNING in XAMPP!</div>';
            $critical_fail = true;
        }

        // 5. Check Web Server
        echo '<h2>5. Web Server</h2>';
        if (isset($_SERVER['SERVER_SOFTWARE'])) {
            echo '<div class="check pass">✓ Server: ' . $_SERVER['SERVER_SOFTWARE'] . '</div>';
        }
        echo '<div class="check pass">✓ Document Root: ' . $_SERVER['DOCUMENT_ROOT'] . '</div>';

        // 6. Session Configuration
        echo '<h2>6. Session Configuration</h2>';
        echo '<div class="check pass">✓ Session save path: ' . ini_get('session.save_path') . '</div>';
        if (ini_get('session.auto_start')) {
            echo '<div class="check warn">⚠ session.auto_start is ON (not required)</div>';
        } else {
            echo '<div class="check pass">✓ session.auto_start is OFF (recommended)</div>';
        }

        // RECOMMENDATIONS
        echo '<h2>7. Next Steps</h2>';
        if ($critical_fail) {
            echo '<div class="check fail"><strong>⚠ CRITICAL ISSUES FOUND!</strong><br>';
            echo 'Please fix the items marked with ✗ above before proceeding.</div>';
        } else {
            echo '<div class="check pass"><strong>✓ All systems operational!</strong></div>';
            echo '<div class="check warn"><strong>Next:</strong><br>';
            echo '1. <a href="setup.php" style="color: #0066cc;">Click here to run setup.php</a><br>';
            echo '2. Then visit <a href="admin-login.html" style="color: #0066cc;">admin-login.html</a><br>';
            echo '3. Login with: admin / admin123';
            echo '</div>';
        }
        ?>
    </div>
</body>

</html>