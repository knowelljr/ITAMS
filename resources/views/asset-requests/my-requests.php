<?php
$activePage = 'asset-requests';

$error = $_SESSION['error'] ?? '';
$success = $_SESSION['success'] ?? '';
unset($_SESSION['error'], $_SESSION['success']);

$content = <<<HTML
<div class="mb-6">
    <div class="flex justify-between items-center">
        <h1 class="text-3xl">My Asset Requests</h1>
        <a href="/asset-requests/create" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            <span class="mr-2">+</span> New Request
        </a>
    </div>
</div>

HTML;

if ($error) {
    $content .= <<<HTML
<div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">$error</div>
HTML;
}

if (empty($requests)) {
    $content .= <<<HTML
<div class="bg-white rounded-lg shadow p-6">
    <p class="text-center text-gray-600 py-8">
        No requests yet. <a href="/asset-requests/create" class="text-blue-600 hover:underline">Create your first request</a>
    </p>
</div>
HTML;
} else {
    $content .= <<<HTML
<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full table-auto data-table">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ref #</th>
                <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Asset</th>
                <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                <th class="px-6 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Needed</th>
                <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Requested</th>
                <th class="px-6 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Overall Status</th>
                <th class="px-6 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Approvals</th>
                <th class="px-6 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
HTML;

    foreach ($requests as $request) {
        $refNum = $request['request_number'] ?? 'N/A';
        $assetName = $request['asset_name'] ?? 'N/A';
        $category = $request['asset_category'] ?? 'N/A';
        $qty = $request['quantity'] ?? 'N/A';
        $dateNeeded = $request['date_needed'] ? date('M d, Y', strtotime($request['date_needed'])) : 'N/A';
        $createdDate = date('M d, Y', strtotime($request['created_at']));
        $status = $request['status'] ?? 'PENDING';
        
        // Status color coding
        $statusClass = match($status) {
            'PENDING' => 'bg-yellow-100 text-yellow-800',
            'FULLY_APPROVED' => 'bg-green-100 text-green-800',
            'REJECTED' => 'bg-red-100 text-red-800',
            'CANCELLED' => 'bg-gray-100 text-gray-800',
            default => 'bg-blue-100 text-blue-800'
        };
        
        // Approval status
        $deptStatus = $request['department_manager_approval_status'] ?? 'PENDING';
        $itStatus = $request['it_manager_approval_status'] ?? 'PENDING';
        
        $deptBadge = match($deptStatus) {
            'APPROVED' => '<span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded">✓ Dept Mgr</span>',
            'REJECTED' => '<span class="px-2 py-1 bg-red-100 text-red-800 text-xs font-semibold rounded">✗ Dept Mgr</span>',
            default => '<span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-semibold rounded">⏳ Dept Mgr</span>'
        };
        
        $itBadge = match($itStatus) {
            'APPROVED' => '<span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded">✓ IT Mgr</span>',
            'REJECTED' => '<span class="px-2 py-1 bg-red-100 text-red-800 text-xs font-semibold rounded">✗ IT Mgr</span>',
            default => '<span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-semibold rounded">⏳ IT Mgr</span>'
        };
        
        $viewUrl = "/asset-requests/show/" . $request['id'];
        
        $content .= <<<HTML
            <tr>
                <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900">$refNum</td>
                <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900">$assetName</td>
                <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-600">$category</td>
                <td class="px-6 py-2 whitespace-nowrap text-center text-sm">$qty</td>
                <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-600">$dateNeeded</td>
                <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-600">$createdDate</td>
                <td class="px-6 py-2 whitespace-nowrap text-center">
                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">$status</span>
                </td>
                <td class="px-6 py-2 whitespace-nowrap text-center">
                    <div class="flex flex-col gap-2">$deptBadge $itBadge</div>
                </td>
                <td class="px-6 py-2 whitespace-nowrap text-center text-sm">
                    <div class="flex items-center gap-2 justify-center">
                        <a href="$viewUrl" class="h-9 w-10 inline-flex items-center justify-center bg-blue-600 text-white rounded hover:bg-blue-700" title="View details" aria-label="View details">
                            <i class="fas fa-eye"></i>
                        </a>
                    </div>
                </td>
            </tr>
HTML;
    }

    $content .= <<<HTML
        </tbody>
    </table>
</div>
HTML;
}

$content .= <<<HTML

<!-- Request Status Legend -->
<div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-4">
    <div class="bg-white rounded-lg shadow p-4">
        <h3 class="font-bold mb-3 text-gray-800">Overall Status</h3>
        <ul class="space-y-2 text-sm">
            <li><span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded text-xs font-semibold">PENDING</span> - Awaiting approval</li>
            <li><span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs font-semibold">FULLY_APPROVED</span> - Ready to issue</li>
            <li><span class="px-2 py-1 bg-red-100 text-red-800 rounded text-xs font-semibold">REJECTED</span> - Request denied</li>
            <li><span class="px-2 py-1 bg-gray-100 text-gray-800 rounded text-xs font-semibold">CANCELLED</span> - Request cancelled</li>
        </ul>
    </div>
    
    <div class="bg-white rounded-lg shadow p-4">
        <h3 class="font-bold mb-3 text-gray-800">Approval Workflow</h3>
        <ol class="space-y-2 text-sm list-decimal list-inside">
            <li><span class="font-semibold">Department Manager</span> - Reviews and approves</li>
            <li><span class="font-semibold">IT Manager</span> - Reviews and approves</li>
            <li><span class="font-semibold">IT Staff</span> - Issues the asset</li>
        </ol>
    </div>
</div>
HTML;

include __DIR__ . '/../layout.php';
?>
