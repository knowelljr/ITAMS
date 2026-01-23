<?php
require 'vendor/autoload.php';

use App\Database\Connection;

try {
    $db = Connection::getInstance()->getConnection();
    
    echo "Checking asset_issuances table schema...\n";
    $result = $db->query("
        SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE 
        FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_NAME = 'asset_issuances' 
        ORDER BY ORDINAL_POSITION
    ")->fetchAll();
    
    foreach ($result as $col) {
        $nullable = $col['IS_NULLABLE'] === 'YES' ? 'NULL' : 'NOT NULL';
        echo "  {$col['COLUMN_NAME']} ({$col['DATA_TYPE']}) - {$nullable}\n";
    }
    
    echo "\n\nTrying a SELECT from asset_issuances...\n";
    $count = $db->query("SELECT COUNT(*) as cnt FROM asset_issuances")->fetch();
    echo "Records: {$count['cnt']}\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
