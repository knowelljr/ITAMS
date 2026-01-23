<?php
// Dashboard page
$activePage = 'dashboard';

// Prepare dashboard content based on user role
if ($userRole === 'REQUESTER') {
    // Live metrics for requester
    $dbConn = \App\Database\Connection::getInstance();
    $db = $dbConn->getConnection();
    $userId = $_SESSION['user_id'] ?? null;

    $totalRequests = 0;
    $approvedCount = 0;
    $pendingCount = 0;
    $pendingReceipts = 0;
    $recentApproved = [];

    if ($userId) {
        // Total requests created by this requester
        $stmt = $db->prepare("SELECT COUNT(*) FROM asset_requests WHERE requester_id = ?");
        $stmt->execute([$userId]);
        $totalRequests = (int) $stmt->fetchColumn();

        // Fully approved requests
        $stmt = $db->prepare("SELECT COUNT(*) FROM asset_requests WHERE requester_id = ? AND status = 'FULLY_APPROVED'");
        $stmt->execute([$userId]);
        $approvedCount = (int) $stmt->fetchColumn();

        // Pending requests (submitted or dept-approved but not fully approved)
        $stmt = $db->prepare("SELECT COUNT(*) FROM asset_requests WHERE requester_id = ? AND status IN ('PENDING','DEPT_APPROVED')");
        $stmt->execute([$userId]);
        $pendingCount = (int) $stmt->fetchColumn();

        // Pending receipts (items issued but not yet received)
        $stmt = $db->prepare("SELECT COUNT(*) FROM asset_issuances ai JOIN asset_requests ar ON ai.asset_request_id = ar.id WHERE ai.status = 'ISSUED' AND ar.requester_id = ?");
        $stmt->execute([$userId]);
        $pendingReceipts = (int) $stmt->fetchColumn();

        // Recently approved requests (last 5)
        $stmt = $db->prepare("
            SELECT TOP 5 
                ar.id,
                ar.request_number,
                COALESCE(NULLIF(ar.asset_name, ''), a.name) AS asset_name,
                ar.quantity_requested,
                ar.updated_at
            FROM asset_requests ar
            LEFT JOIN assets a ON ar.asset_id = a.id
            WHERE ar.requester_id = ? AND ar.status = 'FULLY_APPROVED'
            ORDER BY ar.updated_at DESC
        ");
        $stmt->execute([$userId]);
        $recentApproved = $stmt->fetchAll();
    }

    $content = <<<HTML
    <h1 class="text-3xl font-bold mb-4">Welcome, $userName</h1>
    <p class="text-gray-600 mb-6">Here's your asset request overview</p>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        <div class="bg-blue-50 p-6 rounded-lg border-l-4 border-blue-500">
            <h3 class="text-lg font-semibold text-gray-700">Total Requests</h3>
            <p class="text-3xl font-bold text-blue-600 mt-2">$totalRequests</p>
        </div>
        <div class="bg-green-50 p-6 rounded-lg border-l-4 border-green-500">
            <h3 class="text-lg font-semibold text-gray-700">Approved</h3>
            <p class="text-3xl font-bold text-green-600 mt-2">$approvedCount</p>
        </div>
        <div class="bg-yellow-50 p-6 rounded-lg border-l-4 border-yellow-500">
            <h3 class="text-lg font-semibold text-gray-700">Pending</h3>
            <p class="text-3xl font-bold text-yellow-600 mt-2">$pendingCount</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        <div class="bg-emerald-50 p-6 rounded-lg border-l-4 border-emerald-500">
            <h3 class="text-lg font-semibold text-gray-700">Pending Receipts</h3>
            <p class="text-3xl font-bold text-emerald-600 mt-2">$pendingReceipts</p>
        </div>
    </div>
    
    <div class="bg-gray-50 p-6 rounded">
        <h2 class="text-xl font-bold mb-4">Quick Actions</h2>
        <a href="/asset-requests/create" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 mr-2">Create New Request</a>
        <a href="/assets/receive" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">My Receipts</a>
    </div>

HTML;

    // Build recently approved rows
    $recentRows = '';
    if (!empty($recentApproved)) {
        foreach ($recentApproved as $ra) {
            $reqNo = htmlspecialchars($ra['request_number'] ?? '');
            $assetNm = htmlspecialchars($ra['asset_name'] ?? '');
            $qty = (int)($ra['quantity_requested'] ?? 0);
            $approvedAt = $ra['updated_at'] ? date('M d, Y', strtotime($ra['updated_at'])) : '';
            $approvedAtEsc = htmlspecialchars($approvedAt);
            $id = (int)($ra['id'] ?? 0);
            $link = "/asset-requests/show/{$id}";
            $recentRows .= "<tr>\n"
                . "<td class=\"px-4 py-2 whitespace-nowrap text-sm text-blue-600\"><a class=\"hover:underline\" href=\"$link\">$reqNo</a></td>\n"
                . "<td class=\"px-4 py-2 whitespace-nowrap text-sm text-gray-700\">$assetNm</td>\n"
                . "<td class=\"px-4 py-2 whitespace-nowrap text-sm text-gray-900\">$qty</td>\n"
                . "<td class=\"px-4 py-2 whitespace-nowrap text-sm text-gray-500\">$approvedAtEsc</td>\n"
                . "</tr>\n";
        }
    } else {
        $recentRows = '<tr><td colspan="4" class="px-4 py-3 text-center text-sm text-gray-500">No recently approved requests</td></tr>';
    }

    $content .= <<<HTML
    <div class="bg-white p-6 rounded-lg shadow mt-6">
        <h2 class="text-xl font-bold mb-4 text-gray-800">Recently Approved</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Request #</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Asset</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Qty</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Approved At</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    $recentRows
                </tbody>
            </table>
        </div>
    </div>
HTML;
    
} elseif ($userRole === 'IT_STAFF') {
    $content = <<<HTML
    <h1 class="text-3xl font-bold mb-4">Welcome, $userName</h1>
    <p class="text-gray-600 mb-6">IT Staff Dashboard - Manage asset requests and issuances</p>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        <div class="bg-purple-50 p-6 rounded-lg border-l-4 border-purple-500">
            <h3 class="text-lg font-semibold text-gray-700">Pending Requests</h3>
            <p class="text-3xl font-bold text-purple-600 mt-2">0</p>
        </div>
        <div class="bg-indigo-50 p-6 rounded-lg border-l-4 border-indigo-500">
            <h3 class="text-lg font-semibold text-gray-700">Approved Requests</h3>
            <p class="text-3xl font-bold text-indigo-600 mt-2">0</p>
        </div>
        <div class="bg-pink-50 p-6 rounded-lg border-l-4 border-pink-500">
            <h3 class="text-lg font-semibold text-gray-700">Issued Today</h3>
            <p class="text-3xl font-bold text-pink-600 mt-2">0</p>
        </div>
    </div>
    
    <div class="bg-gray-50 p-6 rounded">
        <h2 class="text-xl font-bold mb-4">Quick Actions</h2>
        <a href="/asset-requests/manage" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 mr-2">Manage Requests</a>
        <a href="/assets" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700 mr-2">View Assets</a>
    </div>
HTML;
    
} elseif ($userRole === 'DEPARTMENT_MANAGER') {
    // Live metrics for department manager approvals
    $dbConn = \App\Database\Connection::getInstance();
    $db = $dbConn->getConnection();
    $userId = $_SESSION['user_id'] ?? null;

    // Get user's department
    $deptStmt = $db->prepare("SELECT department_id FROM users WHERE id = ?");
    $deptStmt->execute([$userId]);
    $deptId = $deptStmt->fetchColumn();

    // Pending approvals (department manager queue scoped to user's department)
    $pendingCount = 0;
    if ($deptId) {
        $pendStmt = $db->prepare("SELECT COUNT(*) FROM asset_requests ar JOIN users u ON ar.requester_id = u.id WHERE ar.department_manager_approval_status = 'PENDING' AND u.department_id = ?");
        $pendStmt->execute([$deptId]);
        $pendingCount = (int) $pendStmt->fetchColumn();
    }

    // Approved count
    $approvedCount = 0;
    if ($deptId) {
        $appStmt = $db->prepare("SELECT COUNT(*) FROM asset_requests ar JOIN users u ON ar.requester_id = u.id WHERE ar.department_manager_approval_status = 'APPROVED' AND u.department_id = ?");
        $appStmt->execute([$deptId]);
        $approvedCount = (int) $appStmt->fetchColumn();
    }

    // Rejected count
    $rejectedCount = 0;
    if ($deptId) {
        $rejStmt = $db->prepare("SELECT COUNT(*) FROM asset_requests ar JOIN users u ON ar.requester_id = u.id WHERE ar.department_manager_approval_status = 'REJECTED' AND u.department_id = ?");
        $rejStmt->execute([$deptId]);
        $rejectedCount = (int) $rejStmt->fetchColumn();
    }

    // Department asset amount and count
    $deptAssetAmount = 0;
    $deptAssetCount = 0;
    if ($deptId) {
        $deptAggStmt = $db->prepare("SELECT SUM(ISNULL(a.cost, 0) * ISNULL(ar.quantity_requested, 0)) AS total_amount, COUNT(DISTINCT ar.asset_id) AS asset_count FROM asset_requests ar JOIN users u ON ar.requester_id = u.id LEFT JOIN assets a ON ar.asset_id = a.id WHERE u.department_id = ?");
        $deptAggStmt->execute([$deptId]);
        $agg = $deptAggStmt->fetch();
        $deptAssetAmount = $agg['total_amount'] ?? 0;
        $deptAssetCount = $agg['asset_count'] ?? 0;
    }
    $deptAssetAmountFmt = number_format($deptAssetAmount, 2);

    $content = <<<HTML
    <h1 class="text-3xl font-bold mb-2">Welcome, $userName</h1>
    <p class="text-gray-600 mb-6">Department Manager Dashboard - Approval Requests</p>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-4 rounded-lg shadow border-l-4 border-yellow-500">
            <p class="text-sm text-gray-500">Pending Approvals</p>
            <p class="text-3xl font-bold text-yellow-600">$pendingCount</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow border-l-4 border-green-500">
            <p class="text-sm text-gray-500">Approved</p>
            <p class="text-3xl font-bold text-green-600">$approvedCount</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow border-l-4 border-red-500">
            <p class="text-sm text-gray-500">Rejected</p>
            <p class="text-3xl font-bold text-red-600">$rejectedCount</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow border-l-4 border-emerald-500">
            <p class="text-sm text-gray-500">Department Assets</p>
            <p class="text-lg font-semibold text-emerald-700">$$deptAssetAmountFmt</p>
            <p class="text-sm text-gray-600">Count: $deptAssetCount</p>
        </div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow">
        <h2 class="text-xl font-bold mb-4 text-gray-800">Quick Actions</h2>
        <a href="/asset-requests/manager-approvals" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 mr-2">Review Pending Approvals</a>
    </div>
HTML;

} elseif ($userRole === 'IT_MANAGER') {
    // Live metrics for approvals and department assets (Manager role)
    $dbConn = \App\Database\Connection::getInstance();
    $db = $dbConn->getConnection();
    $userId = $_SESSION['user_id'] ?? null;

    // Get user's department
    $deptStmt = $db->prepare("SELECT department_id FROM users WHERE id = ?");
    $deptStmt->execute([$userId]);
    $deptId = $deptStmt->fetchColumn();

    // My pending approvals (department manager queue scoped to user's department)
    $myPendingApproval = 0;
    if ($deptId) {
        $myStmt = $db->prepare("SELECT COUNT(*) FROM asset_requests ar JOIN users u ON ar.requester_id = u.id WHERE ar.department_manager_approval_status = 'PENDING' AND u.department_id = ?");
        $myStmt->execute([$deptId]);
        $myPendingApproval = (int) $myStmt->fetchColumn();
    }

    // IT Manager pending approvals (global)
    $itMgrStmt = $db->query("SELECT COUNT(*) FROM asset_requests WHERE it_manager_approval_status = 'PENDING'");
    $itMgrPending = (int) $itMgrStmt->fetchColumn();

    // Department asset amount and count (based on requests from the department)
    $deptAssetAmount = 0;
    $deptAssetCount = 0;
    if ($deptId) {
        $deptAggStmt = $db->prepare("SELECT SUM(ISNULL(a.cost, 0) * ISNULL(ar.quantity_requested, 0)) AS total_amount, COUNT(DISTINCT ar.asset_id) AS asset_count FROM asset_requests ar JOIN users u ON ar.requester_id = u.id LEFT JOIN assets a ON ar.asset_id = a.id WHERE u.department_id = ?");
        $deptAggStmt->execute([$deptId]);
        $agg = $deptAggStmt->fetch();
        $deptAssetAmount = $agg['total_amount'] ?? 0;
        $deptAssetCount = $agg['asset_count'] ?? 0;
    }
    $deptAssetAmountFmt = number_format($deptAssetAmount, 2);

    // Existing sample data (keep charts static for now)
    $pendingRequests = 18;
    $newRequests = 7;
    $cancelledRequests = 3;
    $totalRequests = 120;
    $totalIssuances = 96;
    $performancePct = $totalRequests > 0 ? round(($totalIssuances / $totalRequests) * 100, 1) : 0;

    $roleTitle = 'IT Manager Dashboard Overview';

    $inventoryStanding = [
        ['type' => 'Laptops', 'optimum' => 150, 'outstanding' => 90],
        ['type' => 'Desktops', 'optimum' => 120, 'outstanding' => 70],
        ['type' => 'Monitors', 'optimum' => 200, 'outstanding' => 140],
        ['type' => 'Printers', 'optimum' => 40, 'outstanding' => 25],
        ['type' => 'Network', 'optimum' => 80, 'outstanding' => 45],
    ];

    $fastMoving = [
        ['name' => 'Dell Latitude 7440', 'qty' => 42],
        ['name' => 'HP ProDesk 600', 'qty' => 38],
        ['name' => 'Lenovo ThinkVision 24"', 'qty' => 36],
        ['name' => 'Logitech MX Keys', 'qty' => 30],
        ['name' => 'Cisco 2960X Switch', 'qty' => 28],
        ['name' => 'Ubiquiti U6 AP', 'qty' => 26],
        ['name' => 'Anker Docking Station', 'qty' => 24],
        ['name' => 'Epson Workforce Pro', 'qty' => 22],
        ['name' => 'Kingston NVMe 1TB', 'qty' => 20],
        ['name' => 'Seagate 2TB External', 'qty' => 18],
    ];

    $monthlyTrend = [
        ['month' => 'Jan', 'issuances' => 22, 'receipts' => 18, 'returns' => 6],
        ['month' => 'Feb', 'issuances' => 24, 'receipts' => 20, 'returns' => 5],
        ['month' => 'Mar', 'issuances' => 26, 'receipts' => 21, 'returns' => 7],
        ['month' => 'Apr', 'issuances' => 28, 'receipts' => 19, 'returns' => 6],
        ['month' => 'May', 'issuances' => 25, 'receipts' => 22, 'returns' => 8],
        ['month' => 'Jun', 'issuances' => 27, 'receipts' => 23, 'returns' => 6],
        ['month' => 'Jul', 'issuances' => 29, 'receipts' => 24, 'returns' => 7],
        ['month' => 'Aug', 'issuances' => 31, 'receipts' => 26, 'returns' => 8],
        ['month' => 'Sep', 'issuances' => 30, 'receipts' => 25, 'returns' => 7],
        ['month' => 'Oct', 'issuances' => 32, 'receipts' => 27, 'returns' => 9],
        ['month' => 'Nov', 'issuances' => 33, 'receipts' => 28, 'returns' => 8],
        ['month' => 'Dec', 'issuances' => 35, 'receipts' => 30, 'returns' => 9],
    ];

    $deptAmounts = [
        ['dept' => 'IT', 'amount' => 145000],
        ['dept' => 'HR', 'amount' => 78000],
        ['dept' => 'Finance', 'amount' => 112000],
        ['dept' => 'Operations', 'amount' => 132000],
        ['dept' => 'Sales', 'amount' => 168000],
        ['dept' => 'Marketing', 'amount' => 92000],
    ];

    $content = <<<HTML
    <h1 class="text-3xl font-bold mb-2">Welcome, $userName</h1>
    <p class="text-gray-600 mb-6">$roleTitle</p>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-4 rounded-lg shadow border-l-4 border-yellow-500">
            <p class="text-sm text-gray-500">Pending Requests</p>
            <p class="text-3xl font-bold text-yellow-600">$pendingRequests</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow border-l-4 border-blue-500">
            <p class="text-sm text-gray-500">New Requests</p>
            <p class="text-3xl font-bold text-blue-600">$newRequests</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow border-l-4 border-red-500">
            <p class="text-sm text-gray-500">Cancelled Requests</p>
            <p class="text-3xl font-bold text-red-600">$cancelledRequests</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow border-l-4 border-green-500">
            <p class="text-sm text-gray-500">Performance Delivery</p>
            <p class="text-3xl font-bold text-green-600">$performancePct%</p>
            <p class="text-xs text-gray-500">Total requests vs issuances</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white p-4 rounded-lg shadow border-l-4 border-indigo-500">
            <p class="text-sm text-gray-500">My Pending Approvals</p>
            <p class="text-3xl font-bold text-indigo-600">$myPendingApproval</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow border-l-4 border-blue-600">
            <p class="text-sm text-gray-500">IT Manager Pending Approvals</p>
            <p class="text-3xl font-bold text-blue-700">$itMgrPending</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow border-l-4 border-emerald-500">
            <p class="text-sm text-gray-500">Department Assets</p>
            <p class="text-lg font-semibold text-emerald-700">Amount: $$deptAssetAmountFmt</p>
            <p class="text-sm text-gray-600">Count: $deptAssetCount</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <div class="bg-white p-4 rounded-lg shadow">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-800">Inventory Standing</h2>
                <span class="text-sm text-gray-500">Optimum vs Outstanding</span>
            </div>
            <canvas id="inventoryChart" height="260"></canvas>
        </div>
        <div class="bg-white p-4 rounded-lg shadow">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-800">Top 10 Fast Moving Assets</h2>
                <span class="text-sm text-gray-500">By issuances</span>
            </div>
            <canvas id="fastMovingChart" height="260"></canvas>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <div class="bg-white p-4 rounded-lg shadow">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-800">Monthly Trend</h2>
                <span class="text-sm text-gray-500">Issuances, Receipts, Returns</span>
            </div>
            <canvas id="monthlyTrendChart" height="260"></canvas>
        </div>
        <div class="bg-white p-4 rounded-lg shadow">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-800">IT Asset Amount by Department</h2>
                <span class="text-sm text-gray-500">Value distribution</span>
            </div>
            <canvas id="deptAmountChart" height="260"></canvas>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
HTML;

    $content .= '<script>';
    $content .= 'const inventoryData = ' . json_encode($inventoryStanding) . ';';
    $content .= 'const fastMovingData = ' . json_encode($fastMoving) . ';';
    $content .= 'const monthlyData = ' . json_encode($monthlyTrend) . ';';
    $content .= 'const deptAmountData = ' . json_encode($deptAmounts) . ';';

    $content .= <<<'SCRIPT'

        // Inventory stacked bar
        const invCtx = document.getElementById('inventoryChart');
        new Chart(invCtx, {
            type: 'bar',
            data: {
                labels: inventoryData.map(i => i.type),
                datasets: [
                    { label: 'Optimum Stock', data: inventoryData.map(i => i.optimum), backgroundColor: 'rgba(37, 99, 235, 0.7)' },
                    { label: 'Outstanding Stock', data: inventoryData.map(i => i.outstanding), backgroundColor: 'rgba(249, 115, 22, 0.8)' }
                ]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'bottom' } },
                scales: { x: { stacked: true }, y: { stacked: true, beginAtZero: true } }
            }
        });

        // Top 10 fast moving assets (horizontal bar)
        const fmCtx = document.getElementById('fastMovingChart');
        new Chart(fmCtx, {
            type: 'bar',
            data: {
                labels: fastMovingData.map(i => i.name),
                datasets: [{
                    label: 'Issuances',
                    data: fastMovingData.map(i => i.qty),
                    backgroundColor: 'rgba(16, 185, 129, 0.8)'
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { x: { beginAtZero: true } }
            }
        });

        // Monthly trend line chart
        const mtCtx = document.getElementById('monthlyTrendChart');
        new Chart(mtCtx, {
            type: 'line',
            data: {
                labels: monthlyData.map(i => i.month),
                datasets: [
                    { label: 'Issuances', data: monthlyData.map(i => i.issuances), borderColor: '#2563eb', backgroundColor: 'rgba(37,99,235,0.15)', tension: 0.3, fill: true },
                    { label: 'Receipts', data: monthlyData.map(i => i.receipts), borderColor: '#16a34a', backgroundColor: 'rgba(22,163,74,0.15)', tension: 0.3, fill: true },
                    { label: 'Returns', data: monthlyData.map(i => i.returns), borderColor: '#f97316', backgroundColor: 'rgba(249,115,22,0.15)', tension: 0.3, fill: true }
                ]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'bottom' } },
                scales: { y: { beginAtZero: true } }
            }
        });

        // Department amount bar chart
        const daCtx = document.getElementById('deptAmountChart');
        new Chart(daCtx, {
            type: 'bar',
            data: {
                labels: deptAmountData.map(i => i.dept),
                datasets: [{
                    label: 'Asset Amount',
                    data: deptAmountData.map(i => i.amount),
                    backgroundColor: 'rgba(99, 102, 241, 0.8)'
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });
SCRIPT;
    
    $content .= '</script>';
    
} elseif ($userRole === 'ADMIN') {
    // System statistics for admin dashboard
    $dbConn = \App\Database\Connection::getInstance();
    $db = $dbConn->getConnection();

    // Total users count
    $userStmt = $db->query("SELECT COUNT(*) FROM users");
    $totalUsers = (int) $userStmt->fetchColumn();

    // Total departments count
    $deptStmt = $db->query("SELECT COUNT(*) FROM departments");
    $totalDepartments = (int) $deptStmt->fetchColumn();

    // Total assets count
    $assetStmt = $db->query("SELECT COUNT(*) FROM assets");
    $totalAssets = (int) $assetStmt->fetchColumn();

    // Total asset value
    $assetValStmt = $db->query("SELECT SUM(ISNULL(cost, 0) * ISNULL(quantity_onhand, 0)) FROM assets");
    $totalAssetValue = (float) ($assetValStmt->fetchColumn() ?? 0);
    $totalAssetValueFmt = number_format($totalAssetValue, 2);

    // Pending requests count
    $pendReqStmt = $db->query("SELECT COUNT(*) FROM asset_requests WHERE status = 'PENDING'");
    $totalPendingReqs = (int) $pendReqStmt->fetchColumn();

    // Total approved requests
    $appReqStmt = $db->query("SELECT COUNT(*) FROM asset_requests WHERE status = 'FULLY_APPROVED'");
    $totalApprovedReqs = (int) $appReqStmt->fetchColumn();

    $content = <<<HTML
    <h1 class="text-3xl font-bold mb-2">Welcome, $userName</h1>
    <p class="text-gray-600 mb-6">System Administration Dashboard</p>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white p-4 rounded-lg shadow border-l-4 border-blue-500">
            <p class="text-sm text-gray-500">Total Users</p>
            <p class="text-3xl font-bold text-blue-600">$totalUsers</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow border-l-4 border-purple-500">
            <p class="text-sm text-gray-500">Total Departments</p>
            <p class="text-3xl font-bold text-purple-600">$totalDepartments</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow border-l-4 border-green-500">
            <p class="text-sm text-gray-500">Total Assets</p>
            <p class="text-3xl font-bold text-green-600">$totalAssets</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white p-4 rounded-lg shadow border-l-4 border-emerald-500">
            <p class="text-sm text-gray-500">Total Asset Value</p>
            <p class="text-2xl font-semibold text-emerald-700">\$$totalAssetValueFmt</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow border-l-4 border-yellow-500">
            <p class="text-sm text-gray-500">Pending Requests</p>
            <p class="text-3xl font-bold text-yellow-600">$totalPendingReqs</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow border-l-4 border-indigo-500">
            <p class="text-sm text-gray-500">Approved Requests</p>
            <p class="text-3xl font-bold text-indigo-600">$totalApprovedReqs</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-lg font-bold mb-4 text-gray-800">System Management</h2>
            <div class="space-y-2">
                <a href="/users" class="block bg-blue-50 p-3 rounded hover:bg-blue-100 text-blue-700 font-semibold">👥 Manage Users</a>
                <a href="/departments" class="block bg-purple-50 p-3 rounded hover:bg-purple-100 text-purple-700 font-semibold">🏢 Manage Departments</a>
                <a href="/assets" class="block bg-green-50 p-3 rounded hover:bg-green-100 text-green-700 font-semibold">📦 Manage Assets</a>
            </div>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-lg font-bold mb-4 text-gray-800">Admin Tools</h2>
            <div class="space-y-2">
                <a href="/admin/run-migrations" class="block bg-indigo-50 p-3 rounded hover:bg-indigo-100 text-indigo-700 font-semibold">⚙️ Run Migrations</a>
                <a href="/dashboard" class="block bg-gray-50 p-3 rounded hover:bg-gray-100 text-gray-700 font-semibold">📊 View System Stats</a>
            </div>
        </div>
    </div>
HTML;

} else {
    $content = <<<HTML
    <h1 class="text-3xl font-bold mb-4">Welcome, $userName</h1>
    <p class="text-gray-600">Dashboard loading...</p>
HTML;
}

// Include the layout
include __DIR__ . '/layout.php';
?>
