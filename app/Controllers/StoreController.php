<?php
namespace App\Controllers;

use App\Models\Store;
use App\Models\User;
use App\Database\Connection;

class StoreController
{
    /**
     * Deactivate store (set is_active = 0)
     */
    public function deactivate($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /stores');
            exit;
        }

        // Authorization: Admin only
        if (($_SESSION['user_role'] ?? '') !== 'ADMIN') {
            $_SESSION['error'] = 'Unauthorized access';
            header('Location: /dashboard');
            exit;
        }

        $store = $this->store->getStoreById($id);

        if (!$store) {
            $_SESSION['error'] = 'Store not found';
            header('Location: /stores');
            exit;
        }

        if (!$store['is_active']) {
            $_SESSION['info'] = 'Store is already deactivated';
            header('Location: /stores');
            exit;
        }

        try {
            $this->db->beginTransaction();
            $stmt = $this->db->prepare("
                UPDATE inventory_stores
                SET is_active = 0, updated_at = GETDATE()
                WHERE id = ?
            ");
            $stmt->execute([$id]);
            $this->db->commit();
            $_SESSION['success'] = "Store deactivated successfully";
            header('Location: /stores');
            exit;
        } catch (\Exception $e) {
            $this->db->rollBack();
            $_SESSION['error'] = 'Error deactivating store: ' . $e->getMessage();
            header('Location: /stores/' . $id);
            exit;
        }
    }
    private $db;
    private $store;
    private $user;

    public function __construct($db = null)
    {
        $this->db = $db ?? Connection::getInstance()->getConnection();
        $this->store = new Store();
        $this->user = new User();
    }

    /**
     * List all stores
     */
    public function index()
    {
        // Authorization: Admin only
        if (($_SESSION['user_role'] ?? '') !== 'ADMIN') {
            $_SESSION['error'] = 'Unauthorized access';
            header('Location: /dashboard');
            exit;
        }

        $stores = $this->store->getAllStores();
        
        require_once __DIR__ . '/../../resources/views/stores/index.php';
    }

    /**
     * Show store details
     */
    public function show($id)
    {
        // Authorization: Admin or Store Manager
        $userRole = $_SESSION['user_role'] ?? '';
        $userId = $_SESSION['user_id'] ?? 0;
        
        $store = $this->store->getStoreById($id);
        
        if (!$store) {
            $_SESSION['error'] = 'Store not found';
            header('Location: /stores');
            exit;
        }
        
        // Managers can only view their own stores
        if ($userRole === 'MANAGER' && $store['manager_id'] != $userId) {
            $_SESSION['error'] = 'Unauthorized access';
            header('Location: /stores');
            exit;
        }
        
        // Get inventory for this store
        $inventory = $this->store->getStoreInventory($id);
        
        // Get store statistics
        $stats = $this->store->getStoreStats($id);
        
        require_once 'resources/views/stores/show.php';
    }

    /**
     * Show create store form
     */
    public function create()
    {
        // Authorization: Admin only
        if (($_SESSION['user_role'] ?? '') !== 'ADMIN') {
            $_SESSION['error'] = 'Unauthorized access';
            header('Location: /dashboard');
            exit;
        }

        // Get users who can be managers (IT Manager or Admin role)
        $stmt = $this->db->prepare("
            SELECT id, first_name, last_name, role
            FROM users
            WHERE role IN ('IT_MANAGER', 'ADMIN') AND is_active = 1
            ORDER BY first_name, last_name
        ");
        $stmt->execute();
        $managers = $stmt->fetchAll();

        require_once 'resources/views/stores/create.php';
    }

    /**
     * Store create store
     */
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /stores');
            exit;
        }

        // Authorization: Admin only
        if (($_SESSION['user_role'] ?? '') !== 'ADMIN') {
            $_SESSION['error'] = 'Unauthorized access';
            header('Location: /dashboard');
            exit;
        }

        $storeCode = trim($_POST['store_code'] ?? '');
        $storeName = trim($_POST['store_name'] ?? '');
        $location = trim($_POST['location'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $managerId = (int)($_POST['manager_id'] ?? 0);

        // Validation
        $errors = [];
        
        if (empty($storeCode)) {
            $errors[] = 'Store code is required';
        }
        if (empty($storeName)) {
            $errors[] = 'Store name is required';
        }
        if (empty($location)) {
            $errors[] = 'Location is required';
        }
        if ($managerId <= 0) {
            $errors[] = 'Manager is required';
        }

        // Check for duplicate code
        $dupStmt = $this->db->prepare("SELECT id FROM inventory_stores WHERE store_code = ?");
        $dupStmt->execute([$storeCode]);
        if ($dupStmt->fetch()) {
            $errors[] = 'Store code already exists';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            header('Location: /stores/create');
            exit;
        }

        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("
                INSERT INTO inventory_stores 
                (store_code, store_name, location, description, manager_id, is_active, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, 1, GETDATE(), GETDATE())
            ");

            $stmt->execute([
                $storeCode,
                $storeName,
                $location,
                $description ?: null,
                $managerId
            ]);

            $storeId = $this->db->lastInsertId();

            $this->db->commit();

            $_SESSION['success'] = "Store '$storeName' created successfully";
            header('Location: /stores/' . $storeId);
            exit;

        } catch (\Exception $e) {
            $this->db->rollBack();
            $_SESSION['error'] = 'Error creating store: ' . $e->getMessage();
            header('Location: /stores/create');
            exit;
        }
    }

    /**
     * Show edit store form
     */
    public function edit($id)
    {
        // Authorization: Admin only
        if (($_SESSION['user_role'] ?? '') !== 'ADMIN') {
            $_SESSION['error'] = 'Unauthorized access';
            header('Location: /dashboard');
            exit;
        }

        $store = $this->store->getStoreById($id);
        
        if (!$store) {
            $_SESSION['error'] = 'Store not found';
            header('Location: /stores');
            exit;
        }

        // Get available managers
        $stmt = $this->db->prepare("
            SELECT id, first_name, last_name, role
            FROM users
            WHERE role IN ('IT_MANAGER', 'ADMIN') AND is_active = 1
            ORDER BY first_name, last_name
        ");
        $stmt->execute();
        $managers = $stmt->fetchAll();

        require_once 'resources/views/stores/edit.php';
    }

    /**
     * Update store
     */
    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /stores');
            exit;
        }

        // Authorization: Admin only
        if (($_SESSION['user_role'] ?? '') !== 'ADMIN') {
            $_SESSION['error'] = 'Unauthorized access';
            header('Location: /dashboard');
            exit;
        }

        $store = $this->store->getStoreById($id);
        
        if (!$store) {
            $_SESSION['error'] = 'Store not found';
            header('Location: /stores');
            exit;
        }

        $storeName = trim($_POST['store_name'] ?? '');
        $location = trim($_POST['location'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $managerId = (int)($_POST['manager_id'] ?? 0);
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        // Validation
        $errors = [];
        
        if (empty($storeName)) {
            $errors[] = 'Store name is required';
        }
        if (empty($location)) {
            $errors[] = 'Location is required';
        }
        if ($managerId <= 0) {
            $errors[] = 'Manager is required';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            header('Location: /stores/' . $id . '/edit');
            exit;
        }

        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("
                UPDATE inventory_stores
                SET store_name = ?,
                    location = ?,
                    description = ?,
                    manager_id = ?,
                    is_active = ?,
                    updated_at = GETDATE()
                WHERE id = ?
            ");

            $stmt->execute([
                $storeName,
                $location,
                $description ?: null,
                $managerId,
                $isActive,
                $id
            ]);

            $this->db->commit();

            $_SESSION['success'] = "Store updated successfully";
            header('Location: /stores/' . $id);
            exit;

        } catch (\Exception $e) {
            $this->db->rollBack();
            $_SESSION['error'] = 'Error updating store: ' . $e->getMessage();
            header('Location: /stores/' . $id . '/edit');
            exit;
        }
    }

    /**
     * Delete store (soft delete by deactivating)
     */
    public function delete($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /stores');
            exit;
        }

        // Authorization: Admin only
        if (($_SESSION['user_role'] ?? '') !== 'ADMIN') {
            $_SESSION['error'] = 'Unauthorized access';
            header('Location: /dashboard');
            exit;
        }

        $store = $this->store->getStoreById($id);
        
        if (!$store) {
            $_SESSION['error'] = 'Store not found';
            header('Location: /stores');
            exit;
        }

        try {
            $this->db->beginTransaction();

            // Deactivate store instead of deleting
            $stmt = $this->db->prepare("
                UPDATE inventory_stores
                SET is_active = 0, updated_at = GETDATE()
                WHERE id = ?
            ");

            $stmt->execute([$id]);

            $this->db->commit();

            $_SESSION['success'] = "Store deactivated successfully";
            header('Location: /stores');
            exit;

        } catch (\Exception $e) {
            $this->db->rollBack();
            $_SESSION['error'] = 'Error deactivating store: ' . $e->getMessage();
            header('Location: /stores/' . $id);
            exit;
        }
    }

    /**
     * View store inventory report
     */
    public function inventory($id)
    {
        // Authorization: Admin or Store Manager
        $userRole = $_SESSION['user_role'] ?? '';
        $userId = $_SESSION['user_id'] ?? 0;
        
        $store = $this->store->getStoreById($id);
        
        if (!$store) {
            $_SESSION['error'] = 'Store not found';
            header('Location: /stores');
            exit;
        }
        
        // Managers can only view their own stores
        if ($userRole === 'MANAGER' && $store['manager_id'] != $userId) {
            $_SESSION['error'] = 'Unauthorized access';
            header('Location: /stores');
            exit;
        }

        // Get detailed inventory with asset info
        $stmt = $this->db->prepare("
            SELECT 
                si.*,
                a.asset_code,
                a.name as asset_name,
                a.category,
                a.cost,
                (si.quantity_available * a.cost) as available_value,
                (si.quantity_damaged * a.cost) as damaged_value
            FROM store_inventory si
            JOIN assets a ON si.asset_id = a.id
            WHERE si.store_id = ?
            ORDER BY a.category, a.name
        ");
        $stmt->execute([$id]);
        $inventory = $stmt->fetchAll();

        // Calculate totals
        $totals = [
            'available_qty' => 0,
            'reserved_qty' => 0,
            'damaged_qty' => 0,
            'available_value' => 0,
            'damaged_value' => 0
        ];

        foreach ($inventory as $item) {
            $totals['available_qty'] += $item['quantity_available'];
            $totals['reserved_qty'] += $item['quantity_reserved'];
            $totals['damaged_qty'] += $item['quantity_damaged'];
            $totals['available_value'] += $item['available_value'] ?? 0;
            $totals['damaged_value'] += $item['damaged_value'] ?? 0;
        }

        require_once 'resources/views/stores/inventory.php';
    }

    /**
     * View asset movement history for a store
     */
    public function movements($id)
    {
        // Authorization: Admin or Store Manager
        $userRole = $_SESSION['user_role'] ?? '';
        $userId = $_SESSION['user_id'] ?? 0;
        
        $store = $this->store->getStoreById($id);
        
        if (!$store) {
            $_SESSION['error'] = 'Store not found';
            header('Location: /stores');
            exit;
        }
        
        // Managers can only view their own stores
        if ($userRole === 'MANAGER' && $store['manager_id'] != $userId) {
            $_SESSION['error'] = 'Unauthorized access';
            header('Location: /stores');
            exit;
        }

        // Get movements for this store
        $stmt = $this->db->prepare("
            SELECT 
                am.*,
                a.asset_code,
                a.name as asset_name,
                u.first_name,
                u.last_name
            FROM asset_movements am
            JOIN assets a ON am.asset_id = a.id
            JOIN users u ON am.performed_by = u.id
            WHERE am.from_store_id = ? OR am.to_store_id = ?
            ORDER BY am.created_at DESC
        ");
        $stmt->execute([$id, $id]);
        $movements = $stmt->fetchAll();

        require_once 'resources/views/stores/movements.php';
    }
}







