<?php
$host = "localhost";
$db_name = "hk_isp_billing";
$username = "root";
$password = "";

try {
    $db = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $queries = [
        "ALTER TABLE customers ADD COLUMN IF NOT EXISTS auto_disable TINYINT(1) DEFAULT 0",
        "ALTER TABLE customers ADD COLUMN IF NOT EXISTS auto_disable_month INT DEFAULT 0",
        "ALTER TABLE customers ADD COLUMN IF NOT EXISTS extra_days INT DEFAULT 0",
        "ALTER TABLE customers ADD COLUMN IF NOT EXISTS extra_days_type ENUM('One month', 'Every month') DEFAULT 'One month'",
        "ALTER TABLE customers ADD COLUMN IF NOT EXISTS extra_expire_date DATE NULL"
    ];

    foreach ($queries as $query) {
        $db->exec($query);
        echo "Executed: $query\n";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
