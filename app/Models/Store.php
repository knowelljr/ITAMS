<?php

namespace App\Models;

use App\Database\Connection;

class Store
{
    private $db;

    public function __construct()
    {
        $this->db = Connection::getInstance()->getConnection();
    }

    /**
     * Get all active stores
     */
    public function getAllStores()
    {
        $stmt = $this->db->query("
            SELECT id, store_code, store_name, location, manager_id, is_active, created_at
            FROM inventory_stores
            WHERE is_active = 1
            ORDER BY store_name
        ");
        return $stmt->fetchAll();
    }

    /**
     * Get store by ID
     */
    public function getStoreById($storeId)
    {
        $stmt = $this->db->prepare("
            SELECT s.*, u.name as manager_name
            FROM inventory_stores s
            LEFT JOIN users u ON s.manager_id = u.id
            WHERE s.id = ?
        ");
        $stmt->execute([$storeId]);
        return $stmt->fetch();
    }

    /**
     * Get store inventory with asset details
     */
    public function getStoreInventory($storeId)
    {
        $stmt = $this->db->prepare("
            SELECT 
                si.id,
                si.store_id,
                si.asset_id,
                si.quantity_available,
                si.quantity_reserved,
                si.quantity_damaged,
                a.asset_code,
                a.name as asset_name,
                a.category,
                a.model,
                a.cost,
                si.last_counted_at,
                (si.quantity_available + si.quantity_reserved + si.quantity_damaged) as total_quantity
            FROM store_inventory si
            JOIN assets a ON si.asset_id = a.id
            WHERE si.store_id = ? AND si.quantity_available > 0
            ORDER BY a.name
        ");
        $stmt->execute([$storeId]);
        return $stmt->fetchAll();
    }

    /**
     * Get available quantity of asset in store
     */
    public function getAvailableQuantity($storeId, $assetId)
    {
        $stmt = $this->db->prepare("
            SELECT quantity_available
            FROM store_inventory
            WHERE store_id = ? AND asset_id = ?
        ");
        $stmt->execute([$storeId, $assetId]);
        $result = $stmt->fetch();
        return $result ? (int)$result['quantity_available'] : 0;
    }

    /**
     * Update store inventory (deduct on issue, add on return)
     */
    public function updateInventory($storeId, $assetId, $quantityChange, $type = 'available')
    {
        try {
            // Ensure record exists
            $stmt = $this->db->prepare("
                SELECT id FROM store_inventory 
                WHERE store_id = ? AND asset_id = ?
            ");
            $stmt->execute([$storeId, $assetId]);
            
            if (!$stmt->fetch()) {
                // Create new record if doesn't exist
                $insertStmt = $this->db->prepare("
                    INSERT INTO store_inventory (store_id, asset_id, quantity_available, created_at, updated_at)
                    VALUES (?, ?, ?, GETDATE(), GETDATE())
                ");
                $insertStmt->execute([$storeId, $assetId, max(0, $quantityChange)]);
            }

            // Update quantity based on type
            if ($type === 'available') {
                $updateStmt = $this->db->prepare("
                    UPDATE store_inventory
                    SET quantity_available = quantity_available + ?,
                        updated_at = GETDATE()
                    WHERE store_id = ? AND asset_id = ?
                ");
            } elseif ($type === 'reserved') {
                $updateStmt = $this->db->prepare("
                    UPDATE store_inventory
                    SET quantity_reserved = quantity_reserved + ?,
                        updated_at = GETDATE()
                    WHERE store_id = ? AND asset_id = ?
                ");
            } elseif ($type === 'damaged') {
                $updateStmt = $this->db->prepare("
                    UPDATE store_inventory
                    SET quantity_damaged = quantity_damaged + ?,
                        updated_at = GETDATE()
                    WHERE store_id = ? AND asset_id = ?
                ");
            }

            return $updateStmt->execute([$quantityChange, $storeId, $assetId]);
        } catch (\Exception $e) {
            error_log('Store inventory update error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Record asset movement for audit trail
     */
    public function recordMovement($data)
    {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO asset_movements (
                    asset_id, movement_type, from_location, to_location,
                    from_store_id, to_store_id, quantity, asset_request_id,
                    user_id, performed_by, reason, notes, reference_number, created_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, GETDATE())
            ");

            return $stmt->execute([
                $data['asset_id'] ?? null,
                $data['movement_type'] ?? 'OTHER',
                $data['from_location'] ?? null,
                $data['to_location'] ?? null,
                $data['from_store_id'] ?? null,
                $data['to_store_id'] ?? null,
                $data['quantity'] ?? 0,
                $data['asset_request_id'] ?? null,
                $data['user_id'] ?? null,
                $data['performed_by'] ?? null,
                $data['reason'] ?? null,
                $data['notes'] ?? null,
                $data['reference_number'] ?? null
            ]);
        } catch (\Exception $e) {
            error_log('Asset movement recording error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get movement history for an asset
     */
    public function getAssetMovementHistory($assetId, $limit = 50)
    {
        $stmt = $this->db->prepare("
            SELECT TOP ?
                am.id,
                am.movement_type,
                am.from_location,
                am.to_location,
                fs.store_name as from_store,
                ts.store_name as to_store,
                am.quantity,
                am.reason,
                am.notes,
                u.name as user_name,
                pb.name as performed_by_name,
                am.created_at
            FROM asset_movements am
            LEFT JOIN inventory_stores fs ON am.from_store_id = fs.id
            LEFT JOIN inventory_stores ts ON am.to_store_id = ts.id
            LEFT JOIN users u ON am.user_id = u.id
            LEFT JOIN users pb ON am.performed_by = pb.id
            WHERE am.asset_id = ?
            ORDER BY am.created_at DESC
        ");
        $stmt->execute([$limit, $assetId]);
        return $stmt->fetchAll();
    }

    /**
     * Get movement history by user (for receiving receipts)
     */
    public function getUserMovementHistory($userId, $limit = 20)
    {
        $stmt = $this->db->prepare("
            SELECT TOP ?
                am.id,
                am.asset_id,
                am.movement_type,
                am.quantity,
                a.name as asset_name,
                a.asset_code,
                am.created_at,
                am.reason,
                fs.store_name as from_store
            FROM asset_movements am
            JOIN assets a ON am.asset_id = a.id
            LEFT JOIN inventory_stores fs ON am.from_store_id = fs.id
            WHERE am.user_id = ? AND am.movement_type = 'ISSUED'
            ORDER BY am.created_at DESC
        ");
        $stmt->execute([$limit, $userId]);
        return $stmt->fetchAll();
    }

    /**
     * Get store statistics
     */
    public function getStoreStats($storeId)
    {
        $stmt = $this->db->prepare("
            SELECT 
                COUNT(DISTINCT asset_id) as total_asset_types,
                SUM(quantity_available) as total_available,
                SUM(quantity_reserved) as total_reserved,
                SUM(quantity_damaged) as total_damaged,
                SUM(quantity_available + quantity_reserved + quantity_damaged) as total_quantity,
                SUM(CAST((si.quantity_available + si.quantity_reserved + si.quantity_damaged) as DECIMAL) * CAST(a.cost as DECIMAL)) as total_value
            FROM store_inventory si
            JOIN assets a ON si.asset_id = a.id
            WHERE si.store_id = ?
        ");
        $stmt->execute([$storeId]);
        return $stmt->fetch();
    }

    /**
     * Create or update a store
     */
    public function saveStore($data)
    {
        try {
            if (isset($data['id']) && $data['id']) {
                $stmt = $this->db->prepare("
                    UPDATE inventory_stores
                    SET store_code = ?, store_name = ?, location = ?,
                        description = ?, manager_id = ?, updated_at = GETDATE()
                    WHERE id = ?
                ");
                return $stmt->execute([
                    $data['store_code'],
                    $data['store_name'],
                    $data['location'] ?? null,
                    $data['description'] ?? null,
                    $data['manager_id'] ?? null,
                    $data['id']
                ]);
            } else {
                $stmt = $this->db->prepare("
                    INSERT INTO inventory_stores (store_code, store_name, location, description, manager_id, created_at, updated_at)
                    VALUES (?, ?, ?, ?, ?, GETDATE(), GETDATE())
                ");
                return $stmt->execute([
                    $data['store_code'],
                    $data['store_name'],
                    $data['location'] ?? null,
                    $data['description'] ?? null,
                    $data['manager_id'] ?? null
                ]);
            }
        } catch (\Exception $e) {
            error_log('Store save error: ' . $e->getMessage());
            return false;
        }
    }
}
?>
