<?php

namespace App\Controllers;

use App\Database\Connection;

class AssetController
{
    private $db;

    public function __construct()
    {
        $this->db = Connection::getInstance()->getConnection();
    }

    public function index()
    {
        try {
            $page = (int)($_GET['page'] ?? 1);
            $perPage = 20;
            $offset = ($page - 1) * $perPage;

            $countStmt = $this->db->query("SELECT COUNT(*) as total FROM assets");
            $countResult = $countStmt->fetch();
            $totalRecords = $countResult['total'] ?? 0;
            $totalPages = ceil($totalRecords / $perPage);

            $stmt = $this->db->query("
                SELECT id, asset_code, name, category, quantity_onhand, cost, location, updated_at, status, optimum_stock, serial_number, quantity_issued
                FROM assets
                ORDER BY asset_code ASC
                OFFSET " . (int)$offset . " ROWS FETCH NEXT " . (int)$perPage . " ROWS ONLY
            ");
            $assets = $stmt->fetchAll();

            $activePage = 'assets';
            include __DIR__ . '/../../resources/views/assets/index.php';
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Failed to fetch assets: ' . $e->getMessage();
            header('Location: /dashboard');
            exit;
        }
    }

    public function stockOnHand()
    {
        try {
            // Check authorization - only IT Staff, IT Manager, Department Manager, and Admin can view
            $userRole = $_SESSION['user_role'] ?? '';
            $allowedRoles = ['ADMIN', 'IT_MANAGER', 'IT_STAFF', 'DEPARTMENT_MANAGER'];
            
            if (!in_array($userRole, $allowedRoles)) {
                $_SESSION['error'] = 'You do not have permission to view stock reports';
                header('Location: /dashboard');
                exit;
            }

            // Get filter and pagination parameters
            $filterType = $_GET['filter_type'] ?? 'store'; // 'store' is primary
            $filterId = $_GET['filter_id'] ?? '';
            $page = (int)($_GET['page'] ?? 1);
            $perPage = 20;
            $offset = ($page - 1) * $perPage;

            // Stock by Store (from store_inventory table)
            $countQuery = "
                SELECT COUNT(*) as total
                FROM (
                    SELECT DISTINCT si.id
                    FROM store_inventory si
                    JOIN inventory_stores s ON si.store_id = s.id
                    WHERE si.quantity_available > 0
                    " . (!empty($filterId) ? "AND s.id = $filterId" : "") . "
                ) as subquery
            ";
            
            $dataQuery = "
                SELECT 
                    a.asset_code,
                    a.name as asset_name,
                    a.category,
                    s.store_code,
                    s.store_name,
                    si.quantity_available as quantity_on_hand,
                    si.quantity_reserved,
                    si.quantity_damaged,
                    (si.quantity_available + si.quantity_reserved + si.quantity_damaged) as total_qty,
                    a.cost,
                    (si.quantity_available * a.cost) as total_value,
                    si.last_counted_at as last_counted,
                    s.location as store_location
                FROM store_inventory si
                JOIN inventory_stores s ON si.store_id = s.id
                JOIN assets a ON si.asset_id = a.id
                WHERE si.quantity_available > 0
                " . (!empty($filterId) ? "AND s.id = $filterId" : "") . "
                ORDER BY s.store_name, a.asset_code
                OFFSET $offset ROWS FETCH NEXT $perPage ROWS ONLY
            ";

            // Get all stores for filter dropdown
            $storesStmt = $this->db->query("
                SELECT id, store_name, store_code 
                FROM inventory_stores 
                ORDER BY store_name
            ");
            $filterOptions = $storesStmt->fetchAll();

            // Get total count
            $countStmt = $this->db->query($countQuery);
            $countResult = $countStmt->fetch();
            $totalRecords = $countResult['total'] ?? 0;
            $totalPages = ceil($totalRecords / $perPage);

            // Get paginated data
            $stmt = $this->db->query($dataQuery);
            $stockData = $stmt->fetchAll();

            $activePage = 'reports';
            include __DIR__ . '/../../resources/views/reports/stock_on_hand.php';

        } catch (\Exception $e) {
            $_SESSION['error'] = 'Failed to generate stock report: ' . $e->getMessage();
            header('Location: /dashboard');
            exit;
        }
    }

    public function create()
    {
        try {
            $categoriesStmt = $this->db->query("SELECT DISTINCT category FROM assets WHERE category IS NOT NULL ORDER BY category");
            $categories = $categoriesStmt->fetchAll();

            $storesStmt = $this->db->query("SELECT id, store_name FROM inventory_stores ORDER BY store_name");
            $stores = $storesStmt->fetchAll();

            $activePage = 'assets';
            include __DIR__ . '/../../resources/views/assets/create.php';
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Failed to load create form: ' . $e->getMessage();
            header('Location: /assets');
            exit;
        }
    }

    public function store()
    {
        try {
            $assetCode = trim($_POST['asset_code'] ?? '');
            $name = trim($_POST['name'] ?? '');
            $category = trim($_POST['category'] ?? '');
            $quantityOnhand = (int)($_POST['quantity_onhand'] ?? 0);
            $quantityIssued = (int)($_POST['quantity_issued'] ?? 0);
            $cost = (float)($_POST['cost'] ?? 0);
            $location = trim($_POST['location'] ?? '');
            $status = trim($_POST['status'] ?? 'ACTIVE');
            $optimumStock = (int)($_POST['optimum_stock'] ?? 0);
            $serialNumber = trim($_POST['serial_number'] ?? '');
            $storeId = (int)($_POST['store_id'] ?? null);

            if (!$assetCode || !$name) {
                $_SESSION['error'] = 'Asset code and name are required.';
                header('Location: /assets/create');
                exit;
            }

            $sql = "INSERT INTO assets (asset_code, name, category, quantity_onhand, quantity_issued, cost, location, status, optimum_stock, serial_number, store_id, created_at, updated_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, GETDATE(), GETDATE())";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $assetCode, $name, $category, $quantityOnhand, $quantityIssued, $cost, 
                $location, $status, $optimumStock, $serialNumber, $storeId ?: null
            ]);

            $_SESSION['success'] = 'Asset created successfully.';
            header('Location: /assets');
            exit;
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Failed to create asset: ' . $e->getMessage();
            header('Location: /assets/create');
            exit;
        }
    }

    public function show($id = null)
    {
        try {
            // Accept ID from argument (pretty URL) or fallback to GET param
            if ($id === null) {
                $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            } else {
                $id = (int)$id;
            }

            $stmt = $this->db->prepare("SELECT * FROM assets WHERE id = ?");
            $stmt->execute([$id]);
            $asset = $stmt->fetch();

            if (!$asset) {
                $_SESSION['error'] = 'Asset not found.';
                header('Location: /assets');
                exit;
            }

            $activePage = 'assets';
            include __DIR__ . '/../../resources/views/assets/show.php';
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Failed to load asset: ' . $e->getMessage();
            header('Location: /assets');
            exit;
        }
    }

    public function edit($id = null)
    {
        try {
            // Accept ID from argument (pretty URL) or fallback to GET param
            if ($id === null) {
                $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            } else {
                $id = (int)$id;
            }

            $stmt = $this->db->prepare("SELECT * FROM assets WHERE id = ?");
            $stmt->execute([$id]);
            $asset = $stmt->fetch();

            if (!$asset) {
                $_SESSION['error'] = 'Asset not found.';
                header('Location: /assets');
                exit;
            }

            $asset['category'] = $asset['category'] ?? '';
            $asset['cost'] = $asset['cost'] ?? 0;
            $asset['location'] = $asset['location'] ?? '';
            $asset['status'] = $asset['status'] ?? 'ACTIVE';
            $asset['optimum_stock'] = $asset['optimum_stock'] ?? 0;
            $asset['serial_number'] = $asset['serial_number'] ?? '';
            $asset['quantity_onhand'] = $asset['quantity_onhand'] ?? 0;
            $asset['quantity_issued'] = $asset['quantity_issued'] ?? 0;

            $categoriesStmt = $this->db->query("SELECT DISTINCT category FROM assets WHERE category IS NOT NULL ORDER BY category");
            $categories = $categoriesStmt->fetchAll();

            $storesStmt = $this->db->query("SELECT id, store_name FROM inventory_stores ORDER BY store_name");
            $stores = $storesStmt->fetchAll();

            $activePage = 'assets';
            include __DIR__ . '/../../resources/views/assets/edit.php';
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Failed to load edit form: ' . $e->getMessage();
            header('Location: /assets');
            exit;
        }
    }
    

    public function update()
    {
        try {
            $id = (int)($_POST['id'] ?? 0);
            $assetCode = trim($_POST['asset_code'] ?? '');
            $name = trim($_POST['name'] ?? '');
            $category = trim($_POST['category'] ?? '');
            $quantityOnhand = (int)($_POST['quantity_onhand'] ?? 0);
            $quantityIssued = (int)($_POST['quantity_issued'] ?? 0);
            $cost = (float)($_POST['cost'] ?? 0);
            $location = trim($_POST['location'] ?? '');
            $status = trim($_POST['status'] ?? 'ACTIVE');
            $optimumStock = (int)($_POST['optimum_stock'] ?? 0);
            $serialNumber = trim($_POST['serial_number'] ?? '');
            $storeId = (int)($_POST['store_id'] ?? null);

            if (!$assetCode || !$name) {
                $_SESSION['error'] = 'Asset code and name are required.';
                header('Location: /assets/edit/' . $id);
                exit;
            }

            $sql = "UPDATE assets SET 
                    asset_code = ?, 
                    name = ?, 
                    category = ?, 
                    quantity_onhand = ?, 
                    quantity_issued = ?, 
                    cost = ?, 
                    location = ?, 
                    status = ?, 
                    optimum_stock = ?, 
                    serial_number = ?, 
                    store_id = ?,
                    updated_at = GETDATE()
                    WHERE id = ?";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $assetCode, $name, $category, $quantityOnhand, $quantityIssued, $cost,
                $location, $status, $optimumStock, $serialNumber, $storeId ?: null, $id
            ]);

            $_SESSION['success'] = 'Asset updated successfully.';
            header('Location: /assets');
            exit;
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Failed to update asset: ' . $e->getMessage();
            header('Location: /assets/edit/' . $id);
            exit;
        }
    }

    public function delete()
    {
        try {
            $id = (int)($_POST['id'] ?? 0);

            $checkStmt = $this->db->prepare("SELECT COUNT(*) as count FROM asset_issuances WHERE asset_id = ?");
            $checkStmt->execute([$id]);
            $checkResult = $checkStmt->fetch();

            if ($checkResult['count'] > 0) {
                $_SESSION['error'] = 'Cannot delete asset that has issuances.';
                header('Location: /assets');
                exit;
            }

            $stmt = $this->db->prepare("DELETE FROM assets WHERE id = ?");
            $stmt->execute([$id]);

            $_SESSION['success'] = 'Asset deleted successfully.';
            header('Location: /assets');
            exit;
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Failed to delete asset: ' . $e->getMessage();
            header('Location: /assets');
            exit;
        }
    }

    public function printQrCode($id = null)
    {
        try {
            // Accept ID from argument (pretty URL) or fallback to GET param
            if ($id === null) {
                $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            } else {
                $id = (int)$id;
            }

            $stmt = $this->db->prepare("SELECT asset_code, name FROM assets WHERE id = ?");
            $stmt->execute([$id]);
            $asset = $stmt->fetch();

            if (!$asset) {
                $_SESSION['error'] = 'Asset not found.';
                header('Location: /assets');
                exit;
            }

            $activePage = 'assets';
            include __DIR__ . '/../../resources/views/assets/print_qr.php';
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Failed to load QR code: ' . $e->getMessage();
            header('Location: /assets');
            exit;
        }
    }

    public function assetMovement()
    {
        try {
            $page = (int)($_GET['page'] ?? 1);
            $perPage = 20;
            $offset = ($page - 1) * $perPage;

            $fromDate = $_GET['from_date'] ?? date('Y-m-01');
            $toDate = $_GET['to_date'] ?? date('Y-m-d');
            $search = $_GET['search'] ?? '';

            $whereConditions = [];
            $params = [];
            
            if ($fromDate) {
                $whereConditions[] = "am.created_at >= ?";
                $params[] = $fromDate . ' 00:00:00';
            }
            if ($toDate) {
                $whereConditions[] = "am.created_at <= ?";
                $params[] = $toDate . ' 23:59:59';
            }
            if ($search) {
                $whereConditions[] = "(a.asset_code LIKE ? OR a.name LIKE ? OR am.reason LIKE ?)";
                $searchParam = '%' . $search . '%';
                $params[] = $searchParam;
                $params[] = $searchParam;
                $params[] = $searchParam;
            }

            $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';

            $allSql = "SELECT am.* FROM asset_movements am " . $whereClause;
            $allStmt = $this->db->prepare($allSql);
            $allStmt->execute($params);
            $allMovements = $allStmt->fetchAll();

            $totalMovements = count($allMovements);
            $totalPages = ceil($totalMovements / $perPage);

            $sql = "SELECT am.*, 
                    a.asset_code, 
                    a.name as asset_name,
                    u.name as moved_by_user
                    FROM asset_movements am
                    LEFT JOIN assets a ON am.asset_id = a.id
                    LEFT JOIN users u ON am.performed_by = u.id
                    " . $whereClause . "
                    ORDER BY am.created_at DESC
                    OFFSET " . (int)$offset . " ROWS FETCH NEXT " . (int)$perPage . " ROWS ONLY";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $movements = $stmt->fetchAll();

            $activePage = 'assets';
            include __DIR__ . '/../../resources/views/assets/movement.php';
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Failed to load asset movements: ' . $e->getMessage();
            header('Location: /dashboard');
            exit;
        }
    }
}