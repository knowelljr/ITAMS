<?php
require 'vendor/autoload.php';

use App\Database\Connection;

try {
    echo "Testing database connection...\n";
    $db = Connection::getInstance()->getConnection();
    echo "✓ Database connected successfully!\n";
    
    // Check if tables exist
    $tables = ['users', 'assets', 'asset_requests', 'asset_issuances', 'departments'];
    foreach ($tables as $table) {
        $result = $db->query("SELECT COUNT(*) as cnt FROM $table")->fetch();
        echo "✓ $table: " . $result['cnt'] . " records\n";
    }
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}
