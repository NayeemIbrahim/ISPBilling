<?php
require_once 'config/database.php';

$db = (new Database())->getConnection();

$queries = [
    "ALTER TABLE customers ADD COLUMN auto_disable TINYINT(1) DEFAULT 0",
    "ALTER TABLE customers ADD COLUMN auto_disable_month INT DEFAULT 0",
    "ALTER TABLE customers ADD COLUMN extra_days INT DEFAULT 0",
    "ALTER TABLE customers ADD COLUMN extra_days_type ENUM('One month', 'Every month') DEFAULT 'One month'",
    "ALTER TABLE customers ADD COLUMN extra_expire_date DATE NULL"
];

foreach ($queries as $query) {
    try {
        $db->exec($query);
        echo "Executed: $query\n";
    } catch (PDOException $e) {
        echo "Error or already exists: " . $e->getMessage() . "\n";
    }
}
