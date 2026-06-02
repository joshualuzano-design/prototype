<?php
require_once 'db_config.php';

$tables = ['Student', 'FacultyStaff', 'Vehicle', 'Visitor'];
$counts = [];

foreach ($tables as $table) {
    $res = $db->query("SELECT COUNT(*) as total FROM $table");
    if ($res) {
        $row = $res->fetch_assoc();
        $counts[$table] = $row['total'];
    } else {
        $counts[$table] = "Error: " . $db->error;
    }
}

header('Content-Type: application/json');
echo json_encode($counts, JSON_PRETTY_PRINT);
?>
