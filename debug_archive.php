<?php
require_once 'db_config.php';

$tables = ['Student', 'FacultyStaff', 'Vehicle', 'Visitor'];
foreach ($tables as $t) {
    $res = $db->query("SELECT COUNT(*) as total FROM $t WHERE is_archived = 1");
    $row = $res->fetch_assoc();
    echo "$t Archived: " . $row['total'] . "\n";
}
?>
