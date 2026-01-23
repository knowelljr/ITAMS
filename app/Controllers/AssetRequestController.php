<?php
namespace App\Controllers;

class AssetRequestController
{
    protected $db;

    public function __construct()
    {
        $this->db = \App\Database\Connection::getInstance()->getConnection();
    }
    /**
     * Show IT Manager approvals page (requests approved by department manager, pending IT approval)
     */
    public function itManagerApprovals()
    {
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['IT_MANAGER', 'ADMIN'])) {
            header('Location: /dashboard');
            exit;
        }
        $user = [
            'id' => $_SESSION['user_id'],
            'role' => $_SESSION['user_role'],
        ];
        try {
            $stmt = $this->db->prepare("SELECT ar.*, u.name as requester_name, u.employee_number, d.department_name, ar.asset_name, ar.asset_category, ar.quantity_requested, dm.name as dept_mgr_name FROM asset_requests ar JOIN users u ON ar.requester_id = u.id LEFT JOIN departments d ON u.department_id = d.id LEFT JOIN users dm ON ar.department_manager_approved_by = dm.id WHERE ar.department_manager_approval_status = 'APPROVED' AND ar.it_manager_approval_status = 'PENDING' AND ar.status NOT IN ('REJECTED', 'CANCELLED', 'FULLY_APPROVED') ORDER BY ar.created_at DESC");
            $stmt->execute();
            $requests = $stmt->fetchAll();
            $activePage = 'approvals';
            $approvalType = 'it_manager';
            include __DIR__ . '/../../resources/views/asset-requests/approve-it.php';
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Failed to load IT approvals: ' . $e->getMessage();
            header('Location: /dashboard');
            exit;
        }
    }

    /**
     * Process IT Manager approval action
     */
    public function processItManagerApproval()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /asset-requests/it-approvals');
            exit;
        }
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['IT_MANAGER', 'ADMIN'])) {
            header('Location: /dashboard');
            exit;
        }
        $requestId = $_POST['request_id'] ?? null;
        $action = $_POST['action'] ?? null;
        $remarks = $_POST['remarks'] ?? '';
        if (!$requestId || !in_array($action, ['approve', 'reject'])) {
            $_SESSION['error'] = 'Invalid request';
            header('Location: /asset-requests/it-approvals');
            exit;
        }
        try {
            $stmt = $this->db->prepare("SELECT * FROM asset_requests WHERE id = ?");
            $stmt->execute([$requestId]);
            $request = $stmt->fetch();
            if (!$request || $request['it_manager_approval_status'] !== 'PENDING' || $request['department_manager_approval_status'] !== 'APPROVED') {
                $_SESSION['error'] = 'Request not eligible for IT approval.';
                header('Location: /asset-requests/it-approvals');
                exit;
            }
            $approvalStatus = ($action === 'approve') ? 'APPROVED' : 'REJECTED';
            $newStatus = ($action === 'approve') ? 'FULLY_APPROVED' : 'REJECTED';
            $stmt = $this->db->prepare("UPDATE asset_requests SET it_manager_approval_status = ?, it_manager_approved_by = ?, it_manager_approved_at = GETDATE(), it_manager_remarks = ?, status = ?, updated_at = GETDATE() WHERE id = ?");
            $stmt->execute([
                $approvalStatus,
                $_SESSION['user_id'],
                $remarks,
                $newStatus,
                $requestId
            ]);
            $_SESSION['success'] = 'Request ' . ($action === 'approve' ? 'approved' : 'rejected') . ' successfully';
            header('Location: /asset-requests/it-approvals');
            exit;
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Failed to process approval: ' . $e->getMessage();
            header('Location: /asset-requests/it-approvals');
            exit;
        }
    }


    /**
     * Cancel asset request (by requester, only if not yet approved)
     */
    public function cancel($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /asset-requests/my-requests');
            exit;
        }

        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            $_SESSION['error'] = 'Unauthorized.';
            header('Location: /login');
            exit;
        }

        // Only allow cancel if not approved by any manager
        $stmt = $this->db->prepare("SELECT * FROM asset_requests WHERE id = ? AND requester_id = ?");
        $stmt->execute([$id, $userId]);
        $request = $stmt->fetch();
        if (!$request) {
            $_SESSION['error'] = 'Request not found or not yours.';
            header('Location: /asset-requests/my-requests');
            exit;
        }
        if ($request['department_manager_approval_status'] === 'APPROVED' || $request['it_manager_approval_status'] === 'APPROVED') {
            $_SESSION['error'] = 'Cannot cancel: already approved by a manager.';
            header('Location: /asset-requests/my-requests');
            exit;
        }
        if ($request['status'] === 'CANCELLED') {
            $_SESSION['info'] = 'Request already cancelled.';
            header('Location: /asset-requests/my-requests');
            exit;
        }
        $stmt = $this->db->prepare("UPDATE asset_requests SET status = 'CANCELLED', updated_at = GETDATE() WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['success'] = 'Request cancelled successfully.';
        header('Location: /asset-requests/my-requests');
        exit;
    }

    /**
     * Show create request form (for REQUESTER)
     */
    public function create()
    {
        $activePage = 'asset-requests';
        include __DIR__ . '/../../resources/views/asset-requests/create.php';
    }

    /**
     * Store new asset request
     */
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /asset-requests/create');
            exit;
        }

        $assetName = $_POST['asset_name'] ?? '';
        $assetCategory = $_POST['asset_category'] ?? '';
        $quantity = $_POST['quantity'] ?? 1;
        $dateNeeded = $_POST['date_needed'] ?? null;
        $purpose = $_POST['purpose'] ?? '';

        // Server-side validation
        // Server-side validation
        $user = [
            'id' => $_SESSION['user_id'],
            'role' => $_SESSION['user_role'],
        ];
        try {
                    $this->db->beginTransaction();

                    // Generate request number
                    $requestNumber = 'REQ' . date('Ymd') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);

                    // Check if request number already exists
                    $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM asset_requests WHERE request_number = ?");
                    $stmt->execute([$requestNumber]);
                    $exists = $stmt->fetch();
            
                    if ($exists['count'] > 0) {
                        // Generate a new one with milliseconds
                        $requestNumber = 'REQ' . date('Ymd') . str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT);
                    }

                    // Check if requester is a Department Manager
                    $stmt = $this->db->prepare("SELECT role FROM users WHERE id = ?");
                    $stmt->execute([$_SESSION['user_id']]);
                    $userRole = $stmt->fetch();
                    $isDeptManager = ($userRole['role'] === 'DEPARTMENT_MANAGER');

                    // If department manager, auto-approve and set status to DEPT_APPROVED to skip to IT Manager
                    $status = $isDeptManager ? 'DEPT_APPROVED' : 'PENDING';
                    $deptApprovalStatus = $isDeptManager ? 'APPROVED' : 'PENDING';
                    $deptApprovedBy = $isDeptManager ? $_SESSION['user_id'] : null;
                    $deptApprovedAt = $isDeptManager ? 'GETDATE()' : null;

                    $sql = "INSERT INTO asset_requests (
                        requester_id, asset_name, asset_category, quantity_requested, 
                        date_needed, reason, request_number, status, department_manager_approval_status, 
                        department_manager_approved_by, department_manager_approved_at
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, " . ($deptApprovedAt ? $deptApprovedAt : "NULL") . ")";
            
                    $stmt = $this->db->prepare($sql);
                    $stmt->execute([
                        $_SESSION['user_id'],
                        $assetName,
                        $assetCategory,
                        $quantity,
                        $dateNeeded,
                        $purpose,
                        $requestNumber,
                        $status,
                        $deptApprovalStatus,
                        $deptApprovedBy
                    ]);
                    $this->db->commit();

                    if ($isDeptManager) {
                        $_SESSION['success'] = 'Asset request created and auto-approved. Request Number: ' . $requestNumber . ' (forwarded to IT Manager)';
                    } else {
                        $_SESSION['success'] = 'Asset request created successfully. Request Number: ' . $requestNumber;
                    }
                    header('Location: /asset-requests/my-requests');
                    exit;
                } catch (\Exception $e) {
                    $this->db->rollBack();
                    $_SESSION['error'] = 'Failed to create request: ' . $e->getMessage();
                    header('Location: /asset-requests/create');
                    exit;
            }
    }

    /**
     * Show a single asset request details
     */
    public function show($id)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT ar.*,
                       u.name as requester_name,
                       u.email as requester_email,
                       u.employee_number,
                       d.department_name,
                       dm.name as dept_mgr_name,
                       im.name as it_mgr_name,
                       a.name as linked_asset_name,
                       a.asset_code as linked_asset_code
                FROM asset_requests ar
                JOIN users u ON ar.requester_id = u.id
                LEFT JOIN departments d ON u.department_id = d.id
                LEFT JOIN users dm ON ar.department_manager_approved_by = dm.id
                LEFT JOIN users im ON ar.it_manager_approved_by = im.id
                LEFT JOIN assets a ON ar.asset_id = a.id
                WHERE ar.id = ?
            ");
            $stmt->execute([$id]);
            $request = $stmt->fetch();

            if (!$request) {
                $_SESSION['error'] = 'Request not found';
                header('Location: /asset-requests/my-requests');
                exit;
            }

            // Access control: requester can view own request; staff/managers/admin can view any
            $role = $_SESSION['user_role'] ?? 'REQUESTER';
            $userId = $_SESSION['user_id'] ?? null;
            $allowedRoles = ['IT_STAFF', 'IT_MANAGER', 'DEPARTMENT_MANAGER', 'ADMIN'];
            if ($role === 'REQUESTER' && (int)$request['requester_id'] !== (int)$userId) {
                $_SESSION['error'] = 'Access denied to this request';
                header('Location: /asset-requests/my-requests');
                exit;
            }

            $activePage = 'asset-requests';
            include __DIR__ . '/../../resources/views/asset-requests/show.php';
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Failed to load request: ' . $e->getMessage();
            header('Location: /asset-requests/my-requests');
            exit;
        }
    }

    /**
            $stmt->execute([$user['department_id']]);
            $requests = $stmt->fetchAll();

            $activePage = 'approvals';
            $approvalType = 'department';
            include __DIR__ . '/../../resources/views/asset-requests/approve-dept.php';
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Failed to load approvals: ' . $e->getMessage();
            header('Location: /dashboard');
            exit;
        }
    }

    /**
     * Process department manager approval
     */
    public function processDepartmentApproval()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /asset-requests/dept-approvals');
            exit;
        }

        $requestId = $_POST['request_id'] ?? null;
        $action = $_POST['action'] ?? null;
        $remarks = $_POST['remarks'] ?? '';

        if (!$requestId || !in_array($action, ['approve', 'reject'])) {
            $_SESSION['error'] = 'Invalid request';
            header('Location: /asset-requests/dept-approvals');
            exit;
        }

        try {
            $this->db->beginTransaction();

            $approvalStatus = ($action === 'approve') ? 'APPROVED' : 'REJECTED';
            $newStatus = ($action === 'approve') ? 'DEPT_APPROVED' : 'REJECTED';

            $stmt = $this->db->prepare("
                UPDATE asset_requests 
                SET department_manager_approval_status = ?,
                    department_manager_approved_by = ?,
                    department_manager_approved_at = GETDATE(),
                    department_manager_remarks = ?,
                    status = ?,
                    updated_at = GETDATE()
                WHERE id = ? AND department_manager_approval_status = 'PENDING'
            ");
            $stmt->execute([
                $approvalStatus,
                $_SESSION['user_id'],
                $remarks,
                $newStatus,
                $requestId
            ]);

            if ($stmt->rowCount() === 0) {
                throw new \Exception('Request not found or already processed');
            }

            $this->db->commit();

            $_SESSION['success'] = 'Request ' . ($action === 'approve' ? 'approved' : 'rejected') . ' successfully';
            header('Location: /asset-requests/dept-approvals');
            exit;
        } catch (\Exception $e) {
            $this->db->rollBack();
            $_SESSION['error'] = 'Failed to process approval: ' . $e->getMessage();
            header('Location: /asset-requests/dept-approvals');
            exit;
        }
    }

    /**
    * Show requests pending department manager approval
     */
    public function departmentManagerApprovals()
    {
        try {
            // Get user's department
            $stmt = $this->db->prepare("SELECT department_id FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $user = $stmt->fetch();

            if (!$user['department_id']) {
                $_SESSION['error'] = 'Department not assigned to your user';
                header('Location: /dashboard');
                exit;
            }

            $stmt = $this->db->prepare("
                SELECT ar.*,
                       u.name as requester_name,
                       u.employee_number,
                       u.email,
                       d.department_name
                FROM asset_requests ar
                JOIN users u ON ar.requester_id = u.id
                LEFT JOIN departments d ON u.department_id = d.id
                WHERE u.department_id = ?
                  AND ar.department_manager_approval_status = 'PENDING'
                  AND ar.status NOT IN ('REJECTED', 'CANCELLED')
                ORDER BY ar.created_at DESC
            ");
            $stmt->execute([$user['department_id']]);
            $requests = $stmt->fetchAll();

            $activePage = 'approvals';
            $approvalType = 'department_manager';
            include __DIR__ . '/../../resources/views/asset-requests/approve-manager.php';
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Failed to load approvals: ' . $e->getMessage();
            header('Location: /dashboard');
            exit;
        }
    }

    /**
    * Process department manager approval
     */

    /**
     * Show requests pending IT manager approval
     */

    /**
     * Process IT manager approval
     */

    /**
     * IT Staff manage requests list
     */
    public function itStaffManage()
    {
        try {
            // Pagination
            $page = max(1, (int)($_GET['page'] ?? 1));
            $perPage = 20;
            $offset = ($page - 1) * $perPage;

            // Search
            $search = $_GET['search'] ?? '';
            $searchCondition = '';
            $searchParams = [];
            if ($search) {
                $searchCondition = " AND (ar.request_number LIKE ? OR u.name LIKE ? OR u.employee_number LIKE ? OR d.department_name LIKE ? OR ar.asset_name LIKE ?)";
                $searchTerm = '%' . $search . '%';
                $searchParams = [$searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm];
            }

            // Sorting
            $sort = $_GET['sort'] ?? 'date';
            $orderBy = match($sort) {
                'department' => 'd.department_name ASC, ar.created_at DESC',
                'priority' => "CASE WHEN ar.priority='high' THEN 1 WHEN ar.priority='fair' THEN 2 ELSE 3 END, ar.created_at DESC",
                default => 'ar.created_at DESC'
            };

            // Count total
            $countSql = "SELECT COUNT(*) as total FROM asset_requests ar
                         JOIN users u ON ar.requester_id = u.id
                         LEFT JOIN departments d ON u.department_id = d.id
                         WHERE ar.status NOT IN ('CANCELLED')" . $searchCondition;
            $countStmt = $this->db->prepare($countSql);
            $countStmt->execute($searchParams);
            $total = $countStmt->fetch()['total'];
            $totalPages = ceil($total / $perPage);

            // Fetch requests
            $sql = "SELECT ar.*,
                       u.name as requester_name,
                       u.employee_number,
                       d.department_name
                FROM asset_requests ar
                JOIN users u ON ar.requester_id = u.id
                LEFT JOIN departments d ON u.department_id = d.id
                WHERE ar.status NOT IN ('CANCELLED')" . $searchCondition . "
                ORDER BY " . $orderBy . "
                OFFSET " . $offset . " ROWS FETCH NEXT " . $perPage . " ROWS ONLY";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($searchParams);
            $requests = $stmt->fetchAll();

            $activePage = 'asset-requests';
            include __DIR__ . '/../../resources/views/asset-requests/manage.php';
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Failed to load requests: ' . $e->getMessage();
            header('Location: /dashboard');
            exit;
        }
    }

    /**
     * Process IT Staff manage actions: cancel, update priority/PO, upload quotation
     */
    public function processItStaffManage()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /asset-requests/manage');
            exit;
        }

        $action = $_POST['action'] ?? 'update';
        $requestId = $_POST['request_id'] ?? null;
        $priority = $_POST['priority'] ?? null;
        $poNumber = $_POST['po_number'] ?? null;
        $remarks = $_POST['remarks'] ?? '';

        if (!$requestId) {
            $_SESSION['error'] = 'Invalid request';
            header('Location: /asset-requests/manage');
            exit;
        }

        try {
            $this->db->beginTransaction();

            if ($action === 'cancel') {
                $stmt = $this->db->prepare("\n                    UPDATE asset_requests\n                    SET status = 'CANCELLED', updated_at = GETDATE()\n                    WHERE id = ? AND status IN ('PENDING','DEPT_APPROVED','FULLY_APPROVED')\n                ");
                $stmt->execute([$requestId]);
                if ($stmt->rowCount() === 0) {
                    throw new \Exception('Cannot cancel: already processed or invalid status');
                }
            } else {
                // Handle quotation upload if provided
                $quotationPath = null;
                if (isset($_FILES['quotation']) && $_FILES['quotation']['error'] === UPLOAD_ERR_OK) {
                    $uploadDir = __DIR__ . '/../../public/uploads/quotations';
                    if (!is_dir($uploadDir)) {
                        @mkdir($uploadDir, 0777, true);
                    }
                    $ext = strtolower(pathinfo($_FILES['quotation']['name'], PATHINFO_EXTENSION));

                    // Validate file type and size
                    $allowedExt = ['pdf','doc','docx','jpg','jpeg','png'];
                    if (!in_array($ext, $allowedExt, true)) {
                        throw new \Exception('Invalid file type. Allowed: PDF, DOC, DOCX, JPG, PNG');
                    }
                    $maxSize = 5 * 1024 * 1024; // 5MB
                    if ($_FILES['quotation']['size'] > $maxSize) {
                        throw new \Exception('File too large. Max size is 5MB');
                    }

                    // Optional MIME validation
                    if (function_exists('finfo_open')) {
                        $finfo = finfo_open(FILEINFO_MIME_TYPE);
                        $mime = finfo_file($finfo, $_FILES['quotation']['tmp_name']);
                        finfo_close($finfo);
                        $allowedMime = [
                            'pdf' => ['application/pdf'],
                            'doc' => ['application/msword','application/x-msword'],
                            'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
                            'jpg' => ['image/jpeg'],
                            'jpeg' => ['image/jpeg'],
                            'png' => ['image/png']
                        ];
                        $validMime = false;
                        foreach ($allowedMime[$ext] ?? [] as $m) {
                            if (stripos($mime, $m) === 0) { $validMime = true; break; }
                        }
                        if (!$validMime) {
                            throw new \Exception('Invalid file content type');
                        }
                    }

                    $safeName = 'Q' . date('YmdHis') . '_' . uniqid() . '.' . $ext;
                    $dest = $uploadDir . '/' . $safeName;
                    if (!move_uploaded_file($_FILES['quotation']['tmp_name'], $dest)) {
                        throw new \Exception('Failed to upload quotation file');
                    }
                    $quotationPath = '/uploads/quotations/' . $safeName;
                }

                $sql = "UPDATE asset_requests SET updated_at = GETDATE()";
                $params = [];
                // Sanitize/validate inputs
                $prioritySan = null;
                if ($priority) {
                    $p = strtolower(trim($priority));
                    if (in_array($p, ['low','fair','high'], true)) {
                        $prioritySan = $p;
                    }
                }
                $poSan = null;
                if ($poNumber !== null) {
                    $poSan = trim($poNumber);
                    if ($poSan === '') { $poSan = null; }
                    if ($poSan !== null) { $poSan = substr($poSan, 0, 100); }
                }

                if ($prioritySan !== null) { $sql .= ", priority = ?"; $params[] = $prioritySan; }
                if ($poSan !== null) { $sql .= ", po_number = ?"; $params[] = $poSan; }
                if ($quotationPath) { $sql .= ", quotation_file = ?"; $params[] = $quotationPath; }
                $sql .= " WHERE id = ?";
                $params[] = $requestId;

                $stmt = $this->db->prepare($sql);
                $stmt->execute($params);
            }

            $this->db->commit();

            $_SESSION['success'] = ($action === 'cancel') ? 'Request cancelled successfully' : 'Request updated successfully';
            header('Location: /asset-requests/manage');
            exit;
        } catch (\Exception $e) {
            $this->db->rollBack();
            $_SESSION['error'] = 'Failed to process: ' . $e->getMessage();
            header('Location: /asset-requests/manage');
            exit;
        }
    }

    /**
     * Get request details by reference number (for IT Staff)
     */
    public function getRequestByNumber()
    {
        header('Content-Type: application/json');
        
        $requestNumber = $_GET['request_number'] ?? '';
        
        if (!$requestNumber) {
            echo json_encode(['error' => 'Request number is required']);
            exit;
        }

        try {
            $stmt = $this->db->prepare("
                SELECT ar.*,
                       u.name as requester_name,
                       u.employee_number,
                       u.email,
                       u.mobile_number,
                       d.department_name,
                       dm.name as dept_mgr_name,
                       im.name as it_mgr_name
                FROM asset_requests ar
                JOIN users u ON ar.requester_id = u.id
                LEFT JOIN departments d ON u.department_id = d.id
                LEFT JOIN users dm ON ar.department_manager_approved_by = dm.id
                LEFT JOIN users im ON ar.it_manager_approved_by = im.id
                WHERE ar.request_number = ?
            ");
            $stmt->execute([$requestNumber]);
            $request = $stmt->fetch();

            if (!$request) {
                echo json_encode(['error' => 'Request not found']);
                exit;
            }

            // Check if fully approved
            $isFullyApproved = ($request['status'] === 'FULLY_APPROVED' && 
                               $request['department_manager_approval_status'] === 'APPROVED' && 
                               $request['it_manager_approval_status'] === 'APPROVED');

            echo json_encode([
                'success' => true,
                'request' => $request,
                'isFullyApproved' => $isFullyApproved
            ]);
        } catch (\Exception $e) {
            echo json_encode(['error' => 'Failed to fetch request: ' . $e->getMessage()]);
        }
        exit;
    }

    /**
     * Show requester dashboard with statistics and issued assets
     */
    public function dashboard()
    {
        try {
            $userId = $_SESSION['user_id'];

            // Get request statistics
            $statsStmt = $this->db->prepare("
                SELECT 
                    COUNT(*) as total_requests,
                    SUM(CASE WHEN status = 'PENDING' THEN 1 ELSE 0 END) as pending_requests,
                    SUM(CASE WHEN status = 'FULLY_APPROVED' THEN 1 ELSE 0 END) as approved_requests,
                    SUM(CASE WHEN status = 'REJECTED' THEN 1 ELSE 0 END) as rejected_requests,
                    SUM(CASE WHEN status = 'CANCELLED' THEN 1 ELSE 0 END) as cancelled_requests
                FROM asset_requests
                WHERE requester_id = ?
            ");
            $statsStmt->execute([$userId]);
            $stats = $statsStmt->fetch();

            // Get recent requests
            $recentStmt = $this->db->prepare("
                SELECT TOP 5
                    ar.id,
                    ar.request_number,
                    ar.asset_name,
                    ar.asset_category,
                    ar.quantity,
                    ar.status,
                    ar.department_manager_approval_status,
                    ar.it_manager_approval_status,
                    ar.created_at,
                    ar.date_needed
                FROM asset_requests ar
                WHERE ar.requester_id = ?
                ORDER BY ar.created_at DESC
            ");
            $recentStmt->execute([$userId]);
            $recentRequests = $recentStmt->fetchAll();

            // Get issued assets from approved requests
            $issuedStmt = $this->db->prepare("
                SELECT 
                    ai.id as issuance_id,
                    ai.asset_id,
                    a.asset_code,
                    a.name as asset_name,
                    ai.quantity,
                    ai.issued_at,
                    ai.accepted_at,
                    ai.status as issuance_status,
                    ar.request_number
                FROM asset_issuances ai
                JOIN assets a ON ai.asset_id = a.id
                LEFT JOIN asset_requests ar ON ai.asset_request_id = ar.id
                WHERE ar.requester_id = ? AND ar.status = 'FULLY_APPROVED'
                ORDER BY ai.issued_at DESC
            ");
            $issuedStmt->execute([$userId]);
            $issuedAssets = $issuedStmt->fetchAll();

            $activePage = 'dashboard';
            include __DIR__ . '/../../resources/views/asset-requests/dashboard.php';
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Failed to load dashboard: ' . $e->getMessage();
            header('Location: /dashboard');
            exit;
        }
    }
}
