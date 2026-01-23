<?php

namespace App\Controllers;

use App\Database\Connection;
use App\Models\Store;

class AssetIssuanceController
{
    private $db;
    private $store;

    public function __construct()
    {
        $this->db = Connection::getInstance()->getConnection();
        $this->store = new Store();
    }

    /**
     * Show issue asset form (now store-based)
     */
    public function issueForm()
    {
        try {
            // Get all active stores with available inventory
            $stores = $this->store->getAllStores();
            
            // Get all users for direct issuance
            $stmt = $this->db->query("
                SELECT u.id, u.name, u.employee_number, u.email, d.department_name
                FROM users u
                LEFT JOIN departments d ON u.department_id = d.id
                WHERE u.archived = 0
                ORDER BY u.name
            ");
            $users = $stmt->fetchAll();

            $activePage = 'assets';
            include __DIR__ . '/../../resources/views/assets/issue.php';
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Failed to load issuance form: ' . $e->getMessage();
            header('Location: /assets');
            exit;
        }
    }

    /**
     * Process asset issuance (store-based)
     */
    public function processIssuance()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /assets/issue');
            exit;
        }

        $assetId = (int)($_POST['asset_id'] ?? 0);
        $userId = (int)($_POST['user_id'] ?? 0);
        $quantity = (int)($_POST['quantity'] ?? 1);
        $storeId = (int)($_POST['store_id'] ?? 0);
        $requestId = $_POST['request_id'] ?? null;
        $issuanceType = $_POST['issuance_type'] ?? 'REQUEST_BASED';

        if (!$assetId || !$userId || !$quantity || !$storeId) {
            $_SESSION['error'] = 'Asset, user, store, and quantity are required';
            header('Location: /assets/issue');
            exit;
        }

        // Validate issuance type
        if (!in_array($issuanceType, ['REQUEST_BASED', 'UNPLANNED'])) {
            $_SESSION['error'] = 'Invalid issuance type';
            header('Location: /assets/issue');
            exit;
        }

        // For REQUEST_BASED, ensure request_id is provided
        if ($issuanceType === 'REQUEST_BASED' && !$requestId) {
            $_SESSION['error'] = 'Request ID is required for request-based issuance';
            header('Location: /assets/issue');
            exit;
        }

        // For REQUEST_BASED, validate that request is fully approved
        if ($issuanceType === 'REQUEST_BASED') {
            $stmt = $this->db->prepare("
                SELECT status, department_manager_approval_status, it_manager_approval_status
                FROM asset_requests 
                WHERE id = ?
            ");
            $stmt->execute([$requestId]);
            $request = $stmt->fetch();

            if (!$request) {
                $_SESSION['error'] = 'Asset request not found';
                header('Location: /assets/issue');
                exit;
            }

            if ($request['status'] !== 'FULLY_APPROVED') {
                $_SESSION['error'] = 'Cannot issue asset: Request is not fully approved';
                header('Location: /assets/issue');
                exit;
            }
        }

        try {
            $this->db->beginTransaction();

            // Check store inventory availability
            $availableQty = $this->store->getAvailableQuantity($storeId, $assetId);
            if ($availableQty < $quantity) {
                throw new \Exception("Insufficient inventory in store. Available: $availableQty, Requested: $quantity");
            }

            // Get asset details
            $stmt = $this->db->prepare("SELECT * FROM assets WHERE id = ?");
            $stmt->execute([$assetId]);
            $asset = $stmt->fetch();
            if (!$asset) {
                throw new \Exception('Asset not found');
            }

            // Get store details
            $storeDetails = $this->store->getStoreById($storeId);
            if (!$storeDetails) {
                throw new \Exception('Store not found');
            }

            // Create issuance record with store information
            $stmt = $this->db->prepare("
                INSERT INTO asset_issuances (
                    asset_request_id, asset_id, issued_by, issued_from_store_id,
                    quantity, issuance_type, status, issued_at
                ) VALUES (?, ?, ?, ?, ?, ?, 'ISSUED', GETDATE())
            ");
            $stmt->execute([
                $requestId,
                $assetId,
                $_SESSION['user_id'],
                $storeId,
                $quantity,
                $issuanceType
            ]);

            $issuanceId = $this->db->lastInsertId();

            // Deduct from store inventory
            $this->store->updateInventory($storeId, $assetId, -$quantity, 'available');

            // Record movement: ISSUED
            $this->store->recordMovement([
                'asset_id' => $assetId,
                'movement_type' => 'ISSUED',
                'from_store_id' => $storeId,
                'from_location' => $storeDetails['location'],
                'quantity' => $quantity,
                'asset_request_id' => $requestId,
                'user_id' => $userId,
                'performed_by' => $_SESSION['user_id'],
                'reason' => $issuanceType,
                'reference_number' => 'ISSUANCE_' . $issuanceId
            ]);

            // Update request status if linked
            if ($requestId) {
                $stmt = $this->db->prepare("
                    UPDATE asset_requests 
                    SET status = 'ISSUED', updated_at = GETDATE()
                    WHERE id = ?
                ");
                $stmt->execute([$requestId]);
            }

            $this->db->commit();

            $_SESSION['success'] = "Asset issued successfully from {$storeDetails['store_name']}. Issuance ID: $issuanceId";
            header('Location: /assets');
            exit;
        } catch (\Exception $e) {
            $this->db->rollBack();
            $_SESSION['error'] = 'Failed to issue asset: ' . $e->getMessage();
            header('Location: /assets/issue');
            exit;
        }
    }

    /**
     * Show receive asset form
     */
    public function receiveForm()
    {
        try {
            if (($_SESSION['user_role'] ?? '') === 'REQUESTER') {
                $stmt = $this->db->prepare("
                    SELECT ai.*, 
                           a.asset_code, a.name as asset_name, a.category,
                           s.store_name
                    FROM asset_issuances ai
                    JOIN assets a ON ai.asset_id = a.id
                    JOIN asset_requests ar ON ai.asset_request_id = ar.id
                    LEFT JOIN inventory_stores s ON ai.issued_from_store_id = s.id
                    WHERE ai.status = 'ISSUED' AND ar.requester_id = ?
                    ORDER BY ai.created_at DESC
                ");
                $stmt->execute([$_SESSION['user_id']]);
                $issuances = $stmt->fetchAll();
            } else {
                $stmt = $this->db->query("
                    SELECT ai.*, 
                           a.asset_code, a.name as asset_name, a.category
                    FROM asset_issuances ai
                    JOIN assets a ON ai.asset_id = a.id
                    WHERE ai.status = 'ISSUED'
                    ORDER BY ai.created_at DESC
                ");
                $issuances = $stmt->fetchAll();
            }

            $activePage = 'assets';
            include __DIR__ . '/../../resources/views/assets/receive.php';
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Failed to load receive form: ' . $e->getMessage();
            header('Location: /assets');
            exit;
        }
    }

    /**
     * Process asset receipt/return (store-based)
     */
    public function processReceipt()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /assets/receive');
            exit;
        }

        $issuanceId = (int)($_POST['issuance_id'] ?? 0);
        $conditionStatus = $_POST['condition_status'] ?? 'GOOD';
        $receiptNotes = $_POST['receipt_notes'] ?? '';
        $endorsementType = $_POST['endorsement_type'] ?? 'DEPARTMENT';
        $endorsedEmployeeNumber = $_POST['endorsed_employee_number'] ?? '';
        $endorsementRemarks = $_POST['endorsement_remarks'] ?? '';

        if (!$issuanceId) {
            $_SESSION['error'] = 'Issuance ID is required';
            header('Location: /assets/receive');
            exit;
        }

        // Validate endorsement type
        if (!in_array($endorsementType, ['DEPARTMENT', 'INDIVIDUAL'])) {
            $_SESSION['error'] = 'Invalid endorsement type';
            header('Location: /assets/receive');
            exit;
        }

        // If endorsement is to individual, employee number is required
        if ($endorsementType === 'INDIVIDUAL' && empty($endorsedEmployeeNumber)) {
            $_SESSION['error'] = 'Employee number is required when endorsing to an individual';
            header('Location: /assets/receive');
            exit;
        }

        try {
            $this->db->beginTransaction();

            // Get issuance details with requester for access control
            $stmt = $this->db->prepare("
                SELECT ai.*, ar.requester_id, a.name as asset_name
                FROM asset_issuances ai
                LEFT JOIN asset_requests ar ON ai.asset_request_id = ar.id
                JOIN assets a ON ai.asset_id = a.id
                WHERE ai.id = ?
            ");
            $stmt->execute([$issuanceId]);
            $issuance = $stmt->fetch();

            if (!$issuance || $issuance['status'] !== 'ISSUED') {
                throw new \Exception('Invalid issuance or already received');
            }

            // Requesters can only receive items tied to their own requests
            if (($_SESSION['user_role'] ?? '') === 'REQUESTER') {
                if (empty($issuance['asset_request_id']) || (int)$issuance['requester_id'] !== (int)($_SESSION['user_id'] ?? 0)) {
                    throw new \Exception('You can only receive assets issued to your requests');
                }
            }

            // Validate condition status
            $validConditions = ['GOOD', 'MINOR_DAMAGE', 'MAJOR_DAMAGE', 'UNUSABLE'];
            if (!in_array($conditionStatus, $validConditions)) {
                throw new \Exception('Invalid condition status');
            }

            // Determine which store to return to (or default first store)
            $returnToStore = $issuance['issued_from_store_id'];
            if (!$returnToStore) {
                // If no store was recorded, find first active store
                $storeStmt = $this->db->query("SELECT TOP 1 id FROM inventory_stores WHERE is_active = 1");
                $storeResult = $storeStmt->fetch();
                $returnToStore = $storeResult ? $storeResult['id'] : null;
            }

            // Update issuance status with receipt details AND endorsement information
            $stmt = $this->db->prepare("
                UPDATE asset_issuances 
                SET status = 'RECEIVED',
                    condition_on_receipt = ?,
                    receipt_notes = ?,
                    received_at_location = 'USER_INVENTORY',
                    endorsement_type = ?,
                    endorsed_employee_number = ?,
                    endorsement_remarks = ?,
                    accepted_at = GETDATE(),
                    updated_at = GETDATE()
                WHERE id = ?
            ");
            $stmt->execute([$conditionStatus, $receiptNotes, $endorsementType, $endorsedEmployeeNumber, $endorsementRemarks, $issuanceId]);

            // Build endorsement information for audit trail
            $endorsementInfo = $endorsementType === 'INDIVIDUAL' 
                ? "Endorsed to Employee: $endorsedEmployeeNumber"
                : "Endorsed to Department";

            // Record movement: RECEIVED
            $this->store->recordMovement([
                'asset_id' => $issuance['asset_id'],
                'movement_type' => 'RECEIVED',
                'from_store_id' => $issuance['issued_from_store_id'],
                'quantity' => $issuance['quantity'],
                'asset_request_id' => $issuance['asset_request_id'],
                'user_id' => $_SESSION['user_id'] ?? null,
                'performed_by' => $_SESSION['user_id'],
                'reason' => "Asset received by requester - Condition: $conditionStatus - $endorsementInfo",
                'notes' => $receiptNotes,
                'reference_number' => 'RECEIPT_' . $issuanceId
            ]);

            // Handle damaged items - return to store or mark as damaged
            if ($conditionStatus === 'MAJOR_DAMAGE' || $conditionStatus === 'UNUSABLE') {
                if ($returnToStore) {
                    $this->store->updateInventory($returnToStore, $issuance['asset_id'], $issuance['quantity'], 'damaged');
                    
                    $this->store->recordMovement([
                        'asset_id' => $issuance['asset_id'],
                        'movement_type' => 'DAMAGED',
                        'to_store_id' => $returnToStore,
                        'quantity' => $issuance['quantity'],
                        'reason' => "Damaged item from issuance - $conditionStatus",
                        'performed_by' => $_SESSION['user_id']
                    ]);
                }
            } elseif ($returnToStore && $conditionStatus === 'MINOR_DAMAGE') {
                // Minor damage - return for repair
                $this->store->updateInventory($returnToStore, $issuance['asset_id'], $issuance['quantity'], 'available');
                
                $this->store->recordMovement([
                    'asset_id' => $issuance['asset_id'],
                    'movement_type' => 'RETURNED',
                    'to_store_id' => $returnToStore,
                    'quantity' => $issuance['quantity'],
                    'reason' => 'Minor damage - returned for repair',
                    'performed_by' => $_SESSION['user_id']
                ]);
            }

            $this->db->commit();

            $_SESSION['success'] = "Asset '{$issuance['asset_name']}' received successfully (Condition: $conditionStatus)";
            header('Location: /assets/receive');
            exit;
        } catch (\Exception $e) {
            $this->db->rollBack();
            $_SESSION['error'] = 'Failed to receive asset: ' . $e->getMessage();
            header('Location: /assets/receive');
            exit;
        }
    }

    /**
     * Print gatepass
     */
    public function printGatepass($id)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT ai.*,
                       a.asset_code, a.name as asset_name, a.category, a.serial_number, a.model,
                       ib.name as issued_by_name,
                       s.store_name
                FROM asset_issuances ai
                JOIN assets a ON ai.asset_id = a.id
                JOIN users ib ON ai.issued_by = ib.id
                LEFT JOIN inventory_stores s ON ai.issued_from_store_id = s.id
                WHERE ai.id = ?
            ");
            $stmt->execute([$id]);
            $issuance = $stmt->fetch();

            if (!$issuance) {
                $_SESSION['error'] = 'Issuance not found';
                header('Location: /assets');
                exit;
            }

            include __DIR__ . '/../../resources/views/assets/gatepass.php';
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Failed to generate gatepass: ' . $e->getMessage();
            header('Location: /assets');
            exit;
        }
    }

    /**
     * Show pending unplanned issuances for approval
     */
    public function pendingApprovals()
    {
        try {
            $stmt = $this->db->query("
                SELECT ai.*,
                       a.asset_code, a.name as asset_name, a.category,
                       ib.name as issued_by_name
                FROM asset_issuances ai
                JOIN assets a ON ai.asset_id = a.id
                JOIN users ib ON ai.issued_by = ib.id
                WHERE ai.issuance_type = 'UNPLANNED' AND ai.approval_status = 'PENDING_APPROVAL'
                ORDER BY ai.created_at DESC
            ");
            $issuances = $stmt->fetchAll();

            $activePage = 'assets';
            include __DIR__ . '/../../resources/views/assets/approve.php';
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Failed to load approvals: ' . $e->getMessage();
            header('Location: /assets');
            exit;
        }
    }

    /**
     * Process approval/rejection
     */
    public function processApproval()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /assets/approvals');
            exit;
        }

        $issuanceId = $_POST['issuance_id'] ?? null;
        $action = $_POST['action'] ?? null; // 'approve' or 'reject'
        $remarks = $_POST['remarks'] ?? '';

        if (!$issuanceId || !in_array($action, ['approve', 'reject'])) {
            $_SESSION['error'] = 'Invalid request';
            header('Location: /assets/approvals');
            exit;
        }

        try {
            $this->db->beginTransaction();

            $newStatus = ($action === 'approve') ? 'APPROVED' : 'REJECTED';

            $stmt = $this->db->prepare("
                UPDATE asset_issuances 
                SET approval_status = ?,
                    approved_by = ?,
                    approved_at = GETDATE(),
                    approval_remarks = ?,
                    updated_at = GETDATE()
                WHERE id = ? AND issuance_type = 'UNPLANNED' AND approval_status = 'PENDING_APPROVAL'
            ");
            $stmt->execute([$newStatus, $_SESSION['user_id'], $remarks, $issuanceId]);

            if ($stmt->rowCount() === 0) {
                throw new \Exception('Issuance not found or already processed');
            }

            // If rejected, revert the asset quantities
            if ($action === 'reject') {
                $stmt = $this->db->prepare("SELECT asset_id, quantity FROM asset_issuances WHERE id = ?");
                $stmt->execute([$issuanceId]);
                $issuance = $stmt->fetch();

                $stmt = $this->db->prepare("
                    UPDATE assets 
                    SET quantity_onhand = quantity_onhand + ?,
                        quantity_issued = quantity_issued - ?,
                        updated_at = GETDATE()
                    WHERE id = ?
                ");
                $stmt->execute([$issuance['quantity'], $issuance['quantity'], $issuance['asset_id']]);

                // Update status to CANCELLED
                $stmt = $this->db->prepare("UPDATE asset_issuances SET status = 'CANCELLED' WHERE id = ?");
                $stmt->execute([$issuanceId]);
            }

            $this->db->commit();

            $_SESSION['success'] = 'Issuance ' . ($action === 'approve' ? 'approved' : 'rejected') . ' successfully';
            header('Location: /assets/approvals');
            exit;
        } catch (\Exception $e) {
            $this->db->rollBack();
            $_SESSION['error'] = 'Failed to process approval: ' . $e->getMessage();
            header('Location: /assets/approvals');
            exit;
        }
    }
}