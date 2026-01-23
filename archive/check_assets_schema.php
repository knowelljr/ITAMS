<?php
require 'vendor/autoload.php';

use App\Database\Connection;

try {
    $db = Connection::getInstance()->getConnection();
    
    echo "Checking assets table schema...\n";
    $result = $db->query("
        SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE 
        FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_NAME = 'assets' 
        ORDER BY ORDINAL_POSITION
    ")->fetchAll();
    
    foreach ($result as $col) {
        echo "  {$col['COLUMN_NAME']} ({$col['DATA_TYPE']})\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
