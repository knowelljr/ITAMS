<?php
require 'vendor/autoload.php';
require 'config/database.php';

$conn = \App\Database\Connection::getInstance();
$db = $conn->getConnection();

// Check issuances
$stmt = $db->query("SELECT COUNT(*) as count FROM asset_issuances");
$result = $stmt->fetch();
echo "Total Issuances: " . $result['count'] . "\n";

// Check receipts
$stmt = $db->query("SELECT COUNT(*) as count FROM asset_issuances WHERE status = 'RECEIVED'");
$result = $stmt->fetch();
echo "Total Receipts (status=RECEIVED): " . $result['count'] . "\n";

// Show recent issuances with dates
$stmt = $db->query("SELECT TOP 10 id, asset_id, issued_at, accepted_at, status FROM asset_issuances ORDER BY issued_at DESC");
$results = $stmt->fetchAll();
echo "\nRecent issuances:\n";
foreach ($results as $row) {
    echo "ID: " . $row['id'] . ", Asset: " . $row['asset_id'] . ", Issued: " . ($row['issued_at'] ?? 'NULL') . ", Accepted: " . ($row['accepted_at'] ?? 'NULL') . ", Status: " . $row['status'] . "\n";
}
?>
