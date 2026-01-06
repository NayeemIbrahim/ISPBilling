<?php
require_once __DIR__ . '/../config/database.php';
$db = (new Database())->getConnection();

echo "<pre>";
$tables = $db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
foreach ($tables as $table) {
    echo "\nTable: $table\n";
    $cols = $db->query("DESCRIBE $table")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($cols as $c) {
        echo "  - {$c['Field']} ({$c['Type']})\n";
    }
}
echo "</pre>";
