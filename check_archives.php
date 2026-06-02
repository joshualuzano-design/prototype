<?php
require_once 'db_config.php';

$tables = ['Student', 'FacultyStaff', 'Vehicle', 'Visitor'];
$archives = [];

foreach ($tables as $table) {
    $res = $db->query("SELECT * FROM $table WHERE is_archived = 1");
    if ($res) {
        while($row = $res->fetch_assoc()) {
            $archives[$table][] = $row;
        }
    }
}

header('Content-Type: application/json');
echo json_encode($archives, JSON_PRETTY_PRINT);
?>
