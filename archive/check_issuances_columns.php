<?php

$connection = new PDO('sqlsrv:server=localhost,1433;database=itams;TrustServerCertificate=yes', 'sa', 'afh@1234');

$stmt = $connection->query("
    SELECT COLUMN_NAME, DATA_TYPE 
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_NAME = 'asset_issuances'
    ORDER BY ORDINAL_POSITION
");

$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "Columns in asset_issuances table:" . PHP_EOL;
echo "=================================" . PHP_EOL;

foreach ($columns as $col) {
    echo $col['COLUMN_NAME'] . ' (' . $col['DATA_TYPE'] . ')' . PHP_EOL;
}

// Check if updated_at exists
$updatedAtExists = false;
foreach ($columns as $col) {
    if ($col['COLUMN_NAME'] === 'updated_at') {
        $updatedAtExists = true;
        break;
    }
}

echo PHP_EOL;
if (!$updatedAtExists) {
    echo "❌ updated_at column is MISSING" . PHP_EOL;
} else {
    echo "✓ updated_at column exists" . PHP_EOL;
}
