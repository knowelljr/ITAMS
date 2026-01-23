<?php
require 'vendor/autoload.php';
require 'config/database.php';

$conn = \App\Database\Connection::getInstance();
$db = $conn->getConnection();

// Get test asset and user IDs
$assetStmt = $db->query("SELECT TOP 1 id FROM assets");
$asset = $assetStmt->fetch();

$userStmt = $db->query("SELECT TOP 2 id FROM users");
$users = $userStmt->fetchAll();

if (!$asset || count($users) < 2) {
    echo "Not enough test data. Need at least 1 asset and 2 users.\n";
    exit;
}

$assetId = $asset['id'];
$issuerId = $users[0]['id'];
$recipientId = isset($users[1]) ? $users[1]['id'] : $users[0]['id'];

// Create test issuances with different dates
$testIssuances = [
    [
        'asset_id' => $assetId,
        'quantity' => 2,
        'issued_by' => $issuerId,
        'issued_at' => date('Y-m-d H:i:s', strtotime('-5 days')),
        'issuance_type' => 'UNPLANNED',
        'approval_status' => 'APPROVED',
        'status' => 'PENDING'
    ],
    [
        'asset_id' => $assetId,
        'quantity' => 3,
        'issued_by' => $issuerId,
        'issued_at' => date('Y-m-d H:i:s', strtotime('-3 days')),
        'issuance_type' => 'UNPLANNED',
        'approval_status' => 'APPROVED',
        'status' => 'PENDING'
    ],
    [
        'asset_id' => $assetId,
        'quantity' => 1,
        'issued_by' => $issuerId,
        'issued_at' => date('Y-m-d H:i:s', strtotime('-1 day')),
        'accepted_at' => date('Y-m-d H:i:s'),
        'issuance_type' => 'UNPLANNED',
        'approval_status' => 'APPROVED',
        'status' => 'RECEIVED'
    ]
];

$inserted = 0;
foreach ($testIssuances as $issuance) {
    $stmt = $db->prepare("
        INSERT INTO asset_issuances 
        (asset_id, quantity, issued_by, issued_at, accepted_at, issuance_type, approval_status, status, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    $accepted = $issuance['accepted_at'] ?? null;
    $result = $stmt->execute([
        $issuance['asset_id'],
        $issuance['quantity'],
        $issuance['issued_by'],
        $issuance['issued_at'],
        $accepted,
        $issuance['issuance_type'],
        $issuance['approval_status'],
        $issuance['status'],
        date('Y-m-d H:i:s')
    ]);
    
    if ($result) {
        $inserted++;
        echo "Inserted issuance: " . $issuance['issued_at'] . " - Status: " . $issuance['status'] . "\n";
    }
}

echo "\nTotal inserted: $inserted\n";

// Verify
$countStmt = $db->query("SELECT COUNT(*) as count FROM asset_issuances");
$countResult = $countStmt->fetch();
echo "Total issuances in DB: " . $countResult['count'] . "\n";
?>
