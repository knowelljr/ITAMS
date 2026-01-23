
<?php

$router->post('/asset-requests/:id/cancel', function($id) {
    $controller = new \App\Controllers\AssetRequestController();
    $controller->cancel($id);
});

$router->post('/stores/:id/deactivate', function($id) {
    $controller = new \App\Controllers\StoreController();
    $controller->deactivate($id);
});

// Homepage/Dashboard redirect
$router->get('/', function() {
    if (isset($_SESSION['user_id'])) {
        header('Location: /dashboard');
        exit;
    } else {
        header('Location: /login');
        exit;
    }
});

// Auth Routes - Login
$router->get('/login', function() {
    if (isset($_SESSION['user_id'])) {
        header('Location: /dashboard');
        exit;
    }
    include __DIR__ . '/../resources/views/auth/login.php';
});


$router->post('/login', function() {
    \App\Controllers\AuthController::login();
});

// Auth Routes - Register
$router->get('/register', function() {
    if (isset($_SESSION['user_id'])) {
        header('Location: /dashboard');
        exit;
    }
    include __DIR__ . '/../resources/views/auth/register.php';
});


$router->post('/register', function() {
    \App\Controllers\AuthController::register();
});

// Logout
$router->get('/logout', function() {
    session_destroy();
    header('Location: /login');
    exit;
});

// Change Password Route
$router->get('/change-password', function() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: /login');
        exit;
    }
    include __DIR__ . '/../resources/views/auth/change-password.php';
});


$router->post('/change-password', function() {
    \App\Controllers\AuthController::changePassword();
});

// Dashboard Routes
$router->get('/dashboard', function() {
    if (!isset($_SESSION['user_id'])) {
        header('Location:  /login');
        exit;
    }
    
    $userRole = $_SESSION['user_role'] ?? 'REQUESTER';
    $userName = $_SESSION['user_name'] ?? 'User';
    $activePage = 'dashboard';
    
    include __DIR__ . '/../resources/views/dashboard.php';
});

// Admin Routes - User Management
$router->get('/users', function() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: /login');
        exit;
    }
    
    if ($_SESSION['user_role'] !== 'ADMIN') {
        $_SESSION['error'] = 'Access denied. Admin only.';
        header('Location: /dashboard');
        exit;
    }
    
    $admin = new \App\Controllers\AdminController();
    $admin->index();
});

$router->get('/users/create', function() {
    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'ADMIN') {
        header('Location: /dashboard');
        exit;
    }
    
    $admin = new \App\Controllers\AdminController();
    $admin->create();
});

$router->post('/users/store', function() {
    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'ADMIN') {
        header('Location: /dashboard');
        exit;
    }
    
    $admin = new \App\Controllers\AdminController();
    $admin->store();
});

$router->get('/users/edit/:id', function($id) {
    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'ADMIN') {
        header('Location: /dashboard');
        exit;
    }
    
    $admin = new \App\Controllers\AdminController();
    $admin->edit($id);
});

$router->post('/users/update/:id', function($id) {
    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'ADMIN') {
        header('Location: /dashboard');
        exit;
    }
    
    $admin = new \App\Controllers\AdminController();
    $admin->update($id);
});

$router->get('/users/archive/:id', function($id) {
    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'ADMIN') {
        header('Location: /dashboard');
        exit;
    }
    
    $admin = new \App\Controllers\AdminController();
    $admin->toggleArchive($id);
});

$router->get('/users/reset-password/:id', function($id) {
    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'ADMIN') {
        header('Location: /dashboard');
        exit;
    }
    
    $admin = new \App\Controllers\AdminController();
    $admin->resetPassword($id);
});

$router->get('/users/delete/:id', function($id) {
    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'ADMIN') {
        header('Location: /dashboard');
        exit;
    }
    
    $admin = new \App\Controllers\AdminController();
    $admin->delete($id);
});

// Admin Routes - Department Management
$router->get('/departments', function() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: /login');
        exit;
    }
    
    if ($_SESSION['user_role'] !== 'ADMIN') {
        $_SESSION['error'] = 'Access denied. Admin only.';
        header('Location: /dashboard');
        exit;
    }
    
    $dept = new \App\Controllers\DepartmentController();
    $dept->index();
});

$router->get('/departments/create', function() {
    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'ADMIN') {
        header('Location: /dashboard');
        exit;
    }
    
    $dept = new \App\Controllers\DepartmentController();
    $dept->create();
});

$router->post('/departments/store', function() {
    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'ADMIN') {
        header('Location: /dashboard');
        exit;
    }
    
    $dept = new \App\Controllers\DepartmentController();
    $dept->store();
});

$router->get('/departments/edit/:id', function($id) {
    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'ADMIN') {
        header('Location: /dashboard');
        exit;
    }
    
    $dept = new \App\Controllers\DepartmentController();
    $dept->edit($id);
});

$router->post('/departments/update/:id', function($id) {
    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'ADMIN') {
        header('Location: /dashboard');
        exit;
    }
    
    $dept = new \App\Controllers\DepartmentController();
    $dept->update($id);
});

$router->get('/departments/delete/:id', function($id) {
    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'ADMIN') {
        header('Location: /dashboard');
        exit;
    }
    
    $dept = new \App\Controllers\DepartmentController();
    $dept->delete($id);
});

// Asset Routes - IT Staff and IT Manager access
$router->get('/assets', function() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: /login');
        exit;
    }
    
    if (!in_array($_SESSION['user_role'], ['IT_STAFF', 'IT_MANAGER', 'ADMIN'])) {
        $_SESSION['error'] = 'Access denied.';
        header('Location: /dashboard');
        exit;
    }
    
    $assetCtrl = new \App\Controllers\AssetController();
    $assetCtrl->index();
});

$router->get('/assets/create', function() {
    if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['IT_STAFF', 'IT_MANAGER', 'ADMIN'])) {
        header('Location: /dashboard');
        exit;
    }
    
    $assetCtrl = new \App\Controllers\AssetController();
    $assetCtrl->create();
});

$router->post('/assets/store', function() {
    if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['IT_STAFF', 'IT_MANAGER', 'ADMIN'])) {
        header('Location: /dashboard');
        exit;
    }
    
    $assetCtrl = new \App\Controllers\AssetController();
    $assetCtrl->store();
});

$router->get('/assets/show/:id', function($id) {
    if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['IT_STAFF', 'IT_MANAGER', 'ADMIN'])) {
        header('Location: /dashboard');
        exit;
    }
    
    $assetCtrl = new \App\Controllers\AssetController();
    $assetCtrl->show($id);
});

$router->get('/assets/edit/:id', function($id) {
    if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['IT_STAFF', 'IT_MANAGER', 'ADMIN'])) {
        header('Location: /dashboard');
        exit;
    }
    
    $assetCtrl = new \App\Controllers\AssetController();
    $assetCtrl->edit($id);
});

$router->post('/assets/update/:id', function($id) {
    if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['IT_STAFF', 'IT_MANAGER', 'ADMIN'])) {
        header('Location: /dashboard');
        exit;
    }
    
    $assetCtrl = new \App\Controllers\AssetController();
    $assetCtrl->update($id);
});

$router->get('/assets/delete/:id', function($id) {
    if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['IT_STAFF', 'IT_MANAGER', 'ADMIN'])) {
        header('Location: /dashboard');
        exit;
    }
    
    $assetCtrl = new \App\Controllers\AssetController();
    $assetCtrl->delete($id);
});

$router->get('/assets/print-qr/:id', function($id) {
    if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['IT_STAFF', 'IT_MANAGER', 'ADMIN'])) {
        header('Location: /dashboard');
        exit;
    }
    
    $assetCtrl = new \App\Controllers\AssetController();
    $assetCtrl->printQrCode($id);
});

$router->get('/assets/movement', function() {
    if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['IT_STAFF', 'IT_MANAGER', 'ADMIN'])) {
        header('Location: /dashboard');
        exit;
    }
    
    $assetCtrl = new \App\Controllers\AssetController();
    $assetCtrl->assetMovement();
});

// Asset Issuance Routes
$router->get('/assets/issue', function() {
    if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['IT_STAFF', 'IT_MANAGER', 'ADMIN'])) {
        header('Location: /dashboard');
        exit;
    }
    
    $issueCtrl = new \App\Controllers\AssetIssuanceController();
    $issueCtrl->issueForm();
});

$router->post('/assets/issue/process', function() {
    if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['IT_STAFF', 'IT_MANAGER', 'ADMIN'])) {
        header('Location: /dashboard');
        exit;
    }
    
    $issueCtrl = new \App\Controllers\AssetIssuanceController();
    $issueCtrl->processIssuance();
});

$router->get('/assets/receive', function() {
    if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['REQUESTER', 'IT_STAFF', 'IT_MANAGER', 'ADMIN'])) {
        header('Location: /dashboard');
        exit;
    }
    
    $issueCtrl = new \App\Controllers\AssetIssuanceController();
    $issueCtrl->receiveForm();
});

$router->post('/assets/receive/process', function() {
    if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['REQUESTER', 'IT_STAFF', 'IT_MANAGER', 'ADMIN'])) {
        header('Location: /dashboard');
        exit;
    }
    
    $issueCtrl = new \App\Controllers\AssetIssuanceController();
    $issueCtrl->processReceipt();
});

$router->get('/assets/gatepass/:id', function($id) {
    if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['IT_STAFF', 'IT_MANAGER', 'ADMIN'])) {
        header('Location: /dashboard');
        exit;
    }
    
    $issueCtrl = new \App\Controllers\AssetIssuanceController();
    $issueCtrl->printGatepass($id);
});

// Asset Approval Routes (IT Manager only)
$router->get('/assets/approvals', function() {
    if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['IT_MANAGER', 'ADMIN'])) {
        header('Location: /dashboard');
        exit;
    }
    
    $issueCtrl = new \App\Controllers\AssetIssuanceController();
    $issueCtrl->pendingApprovals();
});

$router->post('/assets/approvals/process', function() {
    if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['IT_MANAGER', 'ADMIN'])) {
        header('Location: /dashboard');
        exit;
    }
    
    $issueCtrl = new \App\Controllers\AssetIssuanceController();
    $issueCtrl->processApproval();
});
// Asset Request Routes
$router->get('/asset-requests', function() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: /login');
        exit;
    }
    header('Location: /asset-requests/my-requests');
    exit;
});

$router->get('/asset-requests/dashboard', function() {
    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'REQUESTER') {
        header('Location: /dashboard');
        exit;
    }
    
    $reqCtrl = new \App\Controllers\AssetRequestController();
    $reqCtrl->dashboard();
});

$router->get('/asset-requests/create', function() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: /login');
        exit;
    }
    
    $reqCtrl = new \App\Controllers\AssetRequestController();
    $reqCtrl->create();
});

$router->post('/asset-requests/create', function() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: /login');
        exit;
    }
    
    $reqCtrl = new \App\Controllers\AssetRequestController();
    $reqCtrl->store();
});

// Asset Request details (view)
$router->get('/asset-requests/show/:id', function($id) {
    if (!isset($_SESSION['user_id'])) {
        header('Location: /login');
        exit;
    }

    $reqCtrl = new \App\Controllers\AssetRequestController();
    $reqCtrl->show((int)$id);
});

$router->get('/asset-requests/my-requests', function() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: /login');
        exit;
    }
    
    $reqCtrl = new \App\Controllers\AssetRequestController();
    $reqCtrl->myRequests();
});

// Manager approval routes
$router->get('/asset-requests/manager-approvals', function() {
    if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['DEPARTMENT_MANAGER', 'ADMIN'])) {
            header('Location: /dashboard');
        exit;
    }
    
    $reqCtrl = new \App\Controllers\AssetRequestController();
        $reqCtrl->departmentManagerApprovals();
});

$router->post('/asset-requests/manager-approvals/process', function() {
    if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['DEPARTMENT_MANAGER', 'ADMIN'])) {
            header('Location: /dashboard');
        exit;
    }
    
    $reqCtrl = new \App\Controllers\AssetRequestController();
        $reqCtrl->processDepartmentManagerApproval();
});

// Department Manager approval routes
$router->get('/asset-requests/dept-approvals', function() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: /login');
        exit;
    }
    
    $reqCtrl = new \App\Controllers\AssetRequestController();
    $reqCtrl->departmentApprovals();
});

$router->post('/asset-requests/dept-approvals/process', function() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: /login');
        exit;
    }
    
    $reqCtrl = new \App\Controllers\AssetRequestController();
    $reqCtrl->processDepartmentApproval();
});

// IT Manager approval routes
$router->get('/asset-requests/it-approvals', function() {
    if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['IT_MANAGER', 'ADMIN'])) {
        header('Location: /dashboard');
        exit;
    }
    
    $reqCtrl = new \App\Controllers\AssetRequestController();
    $reqCtrl->itManagerApprovals();
});

$router->post('/asset-requests/it-approvals/process', function() {
    if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['IT_MANAGER', 'ADMIN'])) {
        header('Location: /dashboard');
        exit;
    }
    
    $reqCtrl = new \App\Controllers\AssetRequestController();
    $reqCtrl->processItManagerApproval();
});

// IT Staff manage requests
$router->get('/asset-requests/manage', function() {
    if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['IT_STAFF', 'IT_MANAGER', 'ADMIN'])) {
        header('Location: /dashboard');
        exit;
    }
    
    $reqCtrl = new \App\Controllers\AssetRequestController();
    $reqCtrl->itStaffManage();
});

$router->post('/asset-requests/manage/process', function() {
    if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['IT_STAFF', 'IT_MANAGER', 'ADMIN'])) {
        header('Location: /dashboard');
        exit;
    }
    
    $reqCtrl = new \App\Controllers\AssetRequestController();
    $reqCtrl->processItStaffManage();
});

// API endpoint for request lookup
$router->get('/api/asset-requests/get-by-number', function() {
    if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['IT_STAFF', 'IT_MANAGER', 'ADMIN'])) {
        header('HTTP/1.1 403 Forbidden');
        echo json_encode(['error' => 'Unauthorized']);
        exit;
    }
    
    $reqCtrl = new \App\Controllers\AssetRequestController();
    $reqCtrl->getRequestByNumber();
});

// API route to get store inventory
$router->get('/api/stores/:id/inventory', function($storeId) {
    if (!isset($_SESSION['user_id'])) {
        header('HTTP/1.1 401 Unauthorized');
        echo json_encode(['error' => 'Unauthorized']);
        exit;
    }
    
    $storeModel = new \App\Models\Store();
    $inventory = $storeModel->getStoreInventory((int)$storeId);
    
    header('Content-Type: application/json');
    echo json_encode(['inventory' => $inventory]);
    exit;
});

// Admin route to run migrations via web context
$router->get('/admin/run-migrations', function() {
    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'ADMIN') {
        header('Location: /dashboard');
        exit;
    }

    // Execute migration script and stream output
    header('Content-Type: text/plain');
    include __DIR__ . '/../run_migrations.php';
});

// ===== REPORTS ROUTES =====

$router->get('/reports/stock-on-hand', function() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: /login');
        exit;
    }
    
    // Check authorization - only IT Staff, IT Manager, and Admin
    $userRole = $_SESSION['user_role'] ?? '';
    $allowedRoles = ['ADMIN', 'IT_MANAGER', 'IT_STAFF'];
    
    if (!in_array($userRole, $allowedRoles)) {
        $_SESSION['error'] = 'You do not have permission to view this report';
        header('Location: /dashboard');
        exit;
    }
    
    $controller = new \App\Controllers\AssetController();
    $controller->stockOnHand();
});

// ===== STORE MANAGEMENT ROUTES =====

$router->get('/stores', function() {
    $controller = new \App\Controllers\StoreController();
    $controller->index();
});

$router->get('/stores/create', function() {
    $controller = new \App\Controllers\StoreController();
    $controller->create();
});

$router->post('/stores/store', function() {
    $controller = new \App\Controllers\StoreController();
    $controller->store();
});

$router->get('/stores/:id/edit', function($id) {
    $controller = new \App\Controllers\StoreController();
    $controller->edit($id);
});

$router->post('/stores/:id/update', function($id) {
    $controller = new \App\Controllers\StoreController();
    $controller->update($id);
});

$router->post('/stores/:id/delete', function($id) {
    $controller = new \App\Controllers\StoreController();
    $controller->delete($id);
});

$router->get('/stores/:id', function($id) {
    $controller = new \App\Controllers\StoreController();
    $controller->show($id);
});

$router->get('/stores/:id/inventory', function($id) {
    $controller = new \App\Controllers\StoreController();
    $controller->inventory($id);
});

$router->get('/stores/:id/movements', function($id) {
    $controller = new \App\Controllers\StoreController();
    $controller->movements($id);
});


$router->get('/asset-requests/it-approvals', function() {
    if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['IT_MANAGER', 'ADMIN'])) {
        header('Location: /dashboard');
        exit;
    }
    $reqCtrl = new \App\Controllers\AssetRequestController();
    $reqCtrl->itManagerApprovals();
});

$router->post('/asset-requests/it-approvals/process', function() {
    if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['IT_MANAGER', 'ADMIN'])) {
        header('Location: /dashboard');
        exit;
    }
    $reqCtrl = new \App\Controllers\AssetRequestController();
    $reqCtrl->processItManagerApproval();
});

?>
