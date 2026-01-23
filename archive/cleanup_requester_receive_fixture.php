<?php
require 'vendor/autoload.php';

use App\Database\Connection;

try {
    $db = Connection::getInstance()->getConnection();

    // Find test requests created by the fixture
    $reqStmt = $db->query("SELECT id FROM asset_requests WHERE reason = 'Test for requester receive'");
    $requests = $reqStmt->fetchAll();

    if (!$requests || count($requests) === 0) {
        echo "No test requests found. Nothing to clean.\n";
        exit(0);
    }

    $requestIds = array_map(function($r){ return (int)$r['id']; }, $requests);

    // For each linked issuance, revert asset quantities if still ISSUED
    $issuanceStmt = $db->prepare("SELECT id, asset_id, quantity, status FROM asset_issuances WHERE asset_request_id = ?");
    $reverted = 0;
    foreach ($requestIds as $rid) {
        $issuanceStmt->execute([$rid]);
        $issRows = $issuanceStmt->fetchAll();
        foreach ($issRows as $iss) {
            if (strtoupper((string)$iss['status']) === 'ISSUED') {
                $upd = $db->prepare("UPDATE assets SET quantity_onhand = quantity_onhand + ?, quantity_issued = quantity_issued - ?, updated_at = GETDATE() WHERE id = ?");
                $upd->execute([(int)$iss['quantity'], (int)$iss['quantity'], (int)$iss['asset_id']]);
                $reverted++;
            }
        }
    }

    // Delete issuances
    $inPlaceholders = implode(',', array_fill(0, count($requestIds), '?'));
    $delIss = $db->prepare("DELETE FROM asset_issuances WHERE asset_request_id IN ($inPlaceholders)");
    $delIss->execute($requestIds);

    // Delete requests
    $delReq = $db->prepare("DELETE FROM asset_requests WHERE id IN ($inPlaceholders)");
    $delReq->execute($requestIds);

    echo "Reverted $reverted issuance(s) still ISSUED.\n";
    echo "Deleted " . count($requestIds) . " test request(s) and their issuances.\n";
    echo "Cleanup complete.\n";
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
    exit(1);
}
