<?php
require_once __DIR__ . '/../config.php';

if (!isAdmin() || currentUser()['role'] !== 'owner') {
    die("صلاحية الوصول محصورة لمالك المنصة فقط.");
}

$db = getDB();

$filename = 'qima_backup_' . date('Y-m-d_H-i-s') . '.sql';
header('Content-Type: text/plain; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

echo "-- نسخ احتياطي كامل لقاعدة بيانات منصة قيمة (Qima Platform Backup)\n";
echo "-- تاريخ التصدير: " . date('Y-m-d H:i:s') . "\n\n";
echo "SET NAMES utf8mb4;\n";
echo "SET FOREIGN_KEY_CHECKS = 0;\n\n";

$tables = $db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);

foreach ($tables as $table) {
    echo "-- هيكل الجدول `$table`\n";
    echo "DROP TABLE IF EXISTS `$table`;\n";
    $create = $db->query("SHOW CREATE TABLE `$table`")->fetch();
    echo $create['Create Table'] . ";\n\n";

    echo "-- بيانات الجدول `$table`\n";
    $rows = $db->query("SELECT * FROM `$table`")->fetchAll(PDO::FETCH_ASSOC);
    if (!empty($rows)) {
        foreach ($rows as $row) {
            $cols = array_map(function($c) { return "`$c`"; }, array_keys($row));
            $vals = array_map(function($v) use ($db) {
                if ($v === null) return 'NULL';
                return $db->quote($v);
            }, array_values($row));
            echo "INSERT INTO `$table` (" . implode(', ', $cols) . ") VALUES (" . implode(', ', $vals) . ");\n";
        }
        echo "\n";
    }
}

echo "SET FOREIGN_KEY_CHECKS = 1;\n";
exit;
