<?php
$conn = new PDO('sqlsrv:server=localhost;database=ITAMS', 'sa', 'password');

echo "Checking asset_requests table schema...\n";
$stmt = $conn->query("SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='asset_requests' ORDER BY ORDINAL_POSITION");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $nullable = $row['IS_NULLABLE'] === 'YES' ? 'NULL' : 'NOT NULL';
    echo "  {$row['COLUMN_NAME']} ({$row['DATA_TYPE']}) - $nullable\n";
}

echo "\nTrying a SELECT from asset_requests...\n";
$result = $conn->query("SELECT COUNT(*) as cnt FROM asset_requests");
$data = $result->fetch();
echo "Records: {$data['cnt']}\n";
