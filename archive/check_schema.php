<?php
require 'vendor/autoload.php';

use App\Database\Connection;

try {
    $db = Connection::getInstance()->getConnection();
    
    echo "Checking asset_issuances columns...\n";
    $result = $db->query("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'asset_issuances' ORDER BY ORDINAL_POSITION")->fetchAll();
    foreach ($result as $col) {
        echo "  - " . $col['COLUMN_NAME'] . "\n";
    }
    
    echo "\nChecking asset_requests columns...\n";
    $result = $db->query("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'asset_requests' ORDER BY ORDINAL_POSITION")->fetchAll();
    foreach ($result as $col) {
        echo "  - " . $col['COLUMN_NAME'] . "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
