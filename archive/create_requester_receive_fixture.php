<?php
require 'vendor/autoload.php';

use App\Database\Connection;

try {
    $db = Connection::getInstance()->getConnection();

    // Use existing requester (ID 3) if available
    $user = $db->query("SELECT TOP 1 id, name FROM users WHERE role = 'REQUESTER' ORDER BY id")->fetch();
    if (!$user) {
        throw new Exception('No REQUESTER user found. Please create one first.');
    }
    $requesterId = (int)$user['id'];

    // Pick first asset
    $asset = $db->query("SELECT TOP 1 id, name FROM assets ORDER BY id")->fetch();
    if (!$asset) {
        throw new Exception('No assets found. Please run setup_test_assets.php first.');
    }
    $assetId = (int)$asset['id'];

    // Create asset request fully approved
    $now = date('Y-m-d H:i:s');
    $reqNo = 'REQ' . date('Ymd') . rand(1000, 9999);

    $stmt = $db->prepare("INSERT INTO asset_requests (
        requester_id, asset_id, quantity_requested, priority, reason, status,
        request_number,
        department_manager_approval_status, department_manager_approved_by, department_manager_approved_at,
        it_manager_approval_status, it_manager_approved_by, it_manager_approved_at,
        created_at, updated_at
    ) VALUES (?, ?, ?, 'Low', 'Test for requester receive', 'FULLY_APPROVED', ?, 'APPROVED', 1, ?, 'APPROVED', 4, ?, ?, ?)");

    $stmt->execute([$requesterId, $assetId, 1, $reqNo, $now, $now, $now, $now]);

    $requestId = (int)$db->lastInsertId();

    // Update generated request number to include the new ID tail if needed
    $db->prepare("UPDATE asset_requests SET request_number = ? WHERE id = ?")
       ->execute([$reqNo, $requestId]);

    // Create issuance linked to that request with status ISSUED
    $stmt = $db->prepare("INSERT INTO asset_issuances (
        asset_request_id, asset_id, issued_by, quantity, issuance_type, approval_status, status, issued_at, created_at
    ) VALUES (?, ?, ?, ?, 'REQUEST_BASED', 'APPROVED', 'ISSUED', ?, ?)");

    $issuedBy = 2; // IT_STAFF user id assumed from seed (sam@xyz.com)
    $stmt->execute([$requestId, $assetId, $issuedBy, 1, $now, $now]);

    // Adjust asset quantities
    $db->prepare("UPDATE assets SET quantity_onhand = quantity_onhand - 1, quantity_issued = quantity_issued + 1, updated_at = GETDATE() WHERE id = ?")
       ->execute([$assetId]);

    echo "✓ Created approved request {$reqNo} for requester ID {$requesterId}\n";
    echo "✓ Created issuance (ISSUED) linked to that request\n";
    echo "Done. You can now log in as the requester and visit /assets/receive.\n";
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
}
