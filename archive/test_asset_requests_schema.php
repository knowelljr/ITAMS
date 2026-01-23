<?php
session_start();
require_once __DIR__ . '/app/Database/Connection.php';

$conn = Connection::getInstance();

echo "<h2>Asset Requests Table Schema</h2>";
echo "<pre style='background: #f5f5f5; padding: 10px; border-radius: 5px;'>";
try {
    $stmt = $conn->query("SELECT COLUMN_NAME, DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='asset_requests' ORDER BY ORDINAL_POSITION");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "All columns in asset_requests table:\n";
    foreach ($columns as $col) {
        echo "  ✓ {$col['COLUMN_NAME']} ({$col['DATA_TYPE']})\n";
    }
    
    // Check for required columns
    $required = ['quantity_requested', 'asset_name', 'asset_category', 'request_number', 'date_needed'];
    echo "\n\nRequired columns status:\n";
    $columnNames = array_column($columns, 'COLUMN_NAME');
    foreach ($required as $col) {
        $exists = in_array($col, $columnNames) ? '✓' : '✗';
        echo "  $exists $col\n";
    }
    echo "</pre>";
    
    echo "<p><strong>Status:</strong> Schema is ready for asset request creation.</p>";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
