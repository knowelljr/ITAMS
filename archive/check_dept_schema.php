<?php
require 'vendor/autoload.php';

use App\Database\Connection;

try {
    $db = Connection::getInstance()->getConnection();
    
    echo "Checking departments columns...\n";
    $result = $db->query("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'departments' ORDER BY ORDINAL_POSITION")->fetchAll();
    foreach ($result as $col) {
        echo "  - " . $col['COLUMN_NAME'] . "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
