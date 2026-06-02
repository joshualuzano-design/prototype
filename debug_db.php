<?php
require_once 'db_config.php';

echo "<h3>Database Debug Info</h3>";

$tables = ['Student', 'FacultyStaff', 'Vehicle', 'Visitor'];
foreach ($tables as $table) {
    echo "<h4>Table: $table</h4>";
    $res = $db->query("DESCRIBE $table");
    if ($res) {
        echo "<table border='1'><tr><th>Field</th><th>Type</th><th>Key</th></tr>";
        while ($row = $res->fetch_assoc()) {
            echo "<tr><td>{$row['Field']}</td><td>{$row['Type']}</td><td>{$row['Key']}</td></tr>";
        }
        echo "</table>";
    } else {
        echo "Error describing $table: " . $db->error;
    }
}

// Test update logic for a dummy student
$test_id = 'DEBUG_TEST_001';
$db->query("DELETE FROM Student WHERE student_id = '$test_id'");
$db->query("INSERT INTO Student (student_id, full_name, course, year_level) VALUES ('$test_id', 'Initial Name', 'BSIT', 1)");

$db->query("INSERT INTO Student (student_id, full_name, course, year_level) 
            VALUES ('$test_id', 'Updated Name', 'BSCS', 2) 
            ON DUPLICATE KEY UPDATE full_name=VALUES(full_name), course=VALUES(course), year_level=VALUES(year_level)");

$check = $db->query("SELECT * FROM Student WHERE student_id = '$test_id'")->fetch_assoc();
echo "<h4>Update Test Results:</h4>";
echo "<pre>";
print_r($check);
echo "</pre>";

if ($check['full_name'] === 'Updated Name') {
    echo "<b style='color:green'>Update Logic Working!</b>";
} else {
    echo "<b style='color:red'>Update Logic FAILED!</b>";
}
?>
