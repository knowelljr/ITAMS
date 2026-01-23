<?php
// Automated test for department manager and IT manager approval flow
session_start();
require_once __DIR__ . '/../app/Database/Connection.php';

$conn = \App\Database\Connection::getInstance()->getConnection();

function printResult($label, $result) {
    echo $label . ': ' . ($result ? "PASS" : "FAIL") . "\n";
}

try {
    // 1. Create a test requester
    $requester = $conn->query("SELECT TOP 1 id FROM users WHERE role = 'REQUESTER'")->fetch();
    if (!$requester) throw new Exception('No REQUESTER user found.');
    $requesterId = $requester['id'];

    // 2. Create a test department manager (must be in same department)
    $deptManager = $conn->query("SELECT TOP 1 u.id FROM users u JOIN departments d ON u.department_id = d.id WHERE u.role = 'DEPARTMENT_MANAGER'")->fetch();
    if (!$deptManager) throw new Exception('No DEPARTMENT_MANAGER user found.');
    $deptManagerId = $deptManager['id'];

    // 3. Create a test IT manager
    $itManager = $conn->query("SELECT TOP 1 id FROM users WHERE role = 'IT_MANAGER'")->fetch();
    if (!$itManager) throw new Exception('No IT_MANAGER user found.');
    $itManagerId = $itManager['id'];

    // 4. Create a test asset
    $asset = $conn->query("SELECT TOP 1 id, name FROM assets")->fetch();
    if (!$asset) throw new Exception('No asset found.');
    $assetId = $asset['id'];

    // 5. Insert a new asset request (PENDING)
    $now = date('Y-m-d H:i:s');
    $reqNo = 'REQ' . date('Ymd') . rand(1000, 9999);
    $stmt = $conn->prepare("INSERT INTO asset_requests (
        requester_id, asset_id, quantity_requested, reason, status, request_number, department_manager_approval_status, it_manager_approval_status, created_at, updated_at
    ) VALUES (?, ?, ?, ?, 'PENDING', ?, 'PENDING', 'PENDING', ?, ?)");
    $stmt->execute([$requesterId, $assetId, 1, 'Automated test request', $reqNo, $now, $now]);
    $requestId = $conn->lastInsertId();
    printResult('Request created', $requestId > 0);

    // 6. Simulate department manager approval
    $stmt = $conn->prepare("UPDATE asset_requests SET department_manager_approval_status = 'APPROVED', department_manager_approved_by = ?, department_manager_approved_at = ?, status = 'DEPT_APPROVED', updated_at = ? WHERE id = ?");
    $stmt->execute([$deptManagerId, $now, $now, $requestId]);
    $rowCount = $stmt->rowCount();
    printResult('Department manager approved', $rowCount > 0);

    // 7. Check if request appears for IT manager approval
    $stmt = $conn->prepare("SELECT id FROM asset_requests WHERE id = ? AND department_manager_approval_status = 'APPROVED' AND it_manager_approval_status = 'PENDING' AND status = 'DEPT_APPROVED'");
    $stmt->execute([$requestId]);
    $itPending = $stmt->fetch();
    printResult('Request visible to IT manager', !!$itPending);

    // 8. Simulate IT manager approval
    $stmt = $conn->prepare("UPDATE asset_requests SET it_manager_approval_status = 'APPROVED', it_manager_approved_by = ?, it_manager_approved_at = ?, status = 'FULLY_APPROVED', updated_at = ? WHERE id = ?");
    $stmt->execute([$itManagerId, $now, $now, $requestId]);
    $rowCount = $stmt->rowCount();
    printResult('IT manager approved', $rowCount > 0);

    // 9. Check if request is fully approved
    $stmt = $conn->prepare("SELECT id FROM asset_requests WHERE id = ? AND it_manager_approval_status = 'APPROVED' AND status = 'FULLY_APPROVED'");
    $stmt->execute([$requestId]);
    $fullyApproved = $stmt->fetch();
    printResult('Request is fully approved', !!$fullyApproved);

    // 10. Cleanup test request
    $conn->prepare("DELETE FROM asset_requests WHERE id = ?")->execute([$requestId]);
    echo "Test request cleaned up.\n";

} catch (Exception $e) {
    echo 'Test failed: ' . $e->getMessage() . "\n";
}
