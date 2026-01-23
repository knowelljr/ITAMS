<?php
$dbConfig = [
    'host' => getenv('DB_HOST') ?: 'localhost',
    'port' => getenv('DB_PORT') ?: '1433',
    'dbname' => getenv('DB_NAME') ?: 'ITAMS',
    'username' => getenv('DB_USERNAME') ?: 'sa',
    'password' => getenv('DB_PASSWORD') ?: 'password',
];

try {
    $dsn = "sqlsrv:server={$dbConfig['host']},{$dbConfig['port']};database={$dbConfig['dbname']}";
    $conn = new PDO($dsn, $dbConfig['username'], $dbConfig['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    echo "Checking asset_requests table columns...\n";
    $stmt = $conn->query("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='asset_requests' ORDER BY ORDINAL_POSITION");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "Current columns:\n";
    foreach ($columns as $col) {
        echo "  - $col\n";
    }
    
    // Check for required columns
    $required = ['quantity_requested', 'asset_name', 'asset_category', 'request_number'];
    echo "\nChecking required columns:\n";
    foreach ($required as $col) {
        $exists = in_array($col, $columns) ? '✓' : '✗';
        echo "  $exists $col\n";
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
