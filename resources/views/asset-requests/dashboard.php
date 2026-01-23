<?php
$activePage = 'dashboard';

// Prepare statistics data
$totalRequests = $stats['total_requests'] ?? 0;
$pendingRequests = $stats['pending_requests'] ?? 0;
$approvedRequests = $stats['approved_requests'] ?? 0;
$rejectedRequests = $stats['rejected_requests'] ?? 0;
$cancelledRequests = $stats['cancelled_requests'] ?? 0;
$totalIssued = count($issuedAssets);

$content = <<<HTML
<div class="mb-6">
    <h1 class="text-3xl font-bold">Requester Dashboard</h1>
    <p class="text-gray-600 mt-2">Welcome back! Here's your asset request overview.</p>
</div>

<!-- Key Statistics -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
    <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg shadow p-6 text-center">
        <p class="text-sm text-gray-600 mb-2">Total Requests</p>
        <p class="text-3xl font-bold text-blue-600">$totalRequests</p>
    </div>
    <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-lg shadow p-6 text-center">
        <p class="text-sm text-gray-600 mb-2">Pending</p>
        <p class="text-3xl font-bold text-yellow-600">$pendingRequests</p>
    </div>
    <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg shadow p-6 text-center">
        <p class="text-sm text-gray-600 mb-2">Approved</p>
        <p class="text-3xl font-bold text-green-600">$approvedRequests</p>
    </div>
    <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-lg shadow p-6 text-center">
        <p class="text-sm text-gray-600 mb-2">Rejected</p>
        <p class="text-3xl font-bold text-red-600">$rejectedRequests</p>
    </div>
    <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg shadow p-6 text-center">
        <p class="text-sm text-gray-600 mb-2">Assets Issued</p>
        <p class="text-3xl font-bold text-purple-600">$totalIssued</p>
    </div>
</div>

<!-- Quick Actions -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
    <div class="bg-white rounded-lg shadow p-4">
        <a href="/asset-requests/create" class="flex items-center justify-between hover:bg-blue-50 p-3 rounded transition">
            <div>
                <h3 class="font-semibold text-gray-800">Create New Request</h3>
                <p class="text-sm text-gray-600">Submit a new asset request</p>
            </div>
            <span class="text-2xl">+</span>
        </a>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <a href="/asset-requests/my-requests" class="flex items-center justify-between hover:bg-blue-50 p-3 rounded transition">
            <div>
                <h3 class="font-semibold text-gray-800">View All Requests</h3>
                <p class="text-sm text-gray-600">Track all your asset requests</p>
            </div>
            <span class="text-2xl">→</span>
        </a>
    </div>
</div>

<!-- Recent Requests -->
<div class="bg-white rounded-lg shadow p-6 mb-8">
    <h2 class="text-xl font-bold mb-4">Recent Requests</h2>
HTML;

if (empty($recentRequests)) {
    $content .= <<<HTML
    <p class="text-center text-gray-500 py-8">No requests yet. <a href="/asset-requests/create" class="text-blue-600 hover:underline">Create one now</a></p>
HTML;
} else {
    $content .= <<<HTML
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-100 border-b-2 border-gray-300">
                <tr>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Ref #</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Asset</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Category</th>
                    <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700">Qty</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Date Needed</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                </tr>
            </thead>
            <tbody>
HTML;

    foreach ($recentRequests as $request) {
        $statusClass = match($request['status']) {
            'PENDING' => 'bg-yellow-100 text-yellow-800',
            'FULLY_APPROVED' => 'bg-green-100 text-green-800',
            'REJECTED' => 'bg-red-100 text-red-800',
            'CANCELLED' => 'bg-gray-100 text-gray-800',
            default => 'bg-blue-100 text-blue-800'
        };
        
        $dateNeeded = $request['date_needed'] ? date('M d, Y', strtotime($request['date_needed'])) : 'N/A';
        
        $content .= <<<HTML
                <tr class="border-b border-gray-200 hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm font-mono text-gray-700">{$request['request_number']}</td>
                    <td class="px-4 py-3 text-sm text-gray-700">{$request['asset_name']}</td>
                    <td class="px-4 py-3 text-sm text-gray-600">{$request['asset_category']}</td>
                    <td class="px-4 py-3 text-center text-sm font-semibold">{$request['quantity']}</td>
                    <td class="px-4 py-3 text-sm text-gray-600">$dateNeeded</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full $statusClass">{$request['status']}</span>
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
</div>

<!-- Issued Assets -->
<div class="bg-white rounded-lg shadow p-6">
    <h2 class="text-xl font-bold mb-4">Your Issued Assets</h2>
HTML;

if (empty($issuedAssets)) {
    $content .= <<<HTML
    <p class="text-center text-gray-500 py-8">No assets issued yet. Once your requests are approved and processed, they will appear here.</p>
HTML;
} else {
    $content .= <<<HTML
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-100 border-b-2 border-gray-300">
                <tr>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Asset Code</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Asset Name</th>
                    <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700">Qty</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Issued Date</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Request #</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                </tr>
            </thead>
            <tbody>
HTML;

    foreach ($issuedAssets as $asset) {
        $statusClass = match($asset['issuance_status']) {
            'RECEIVED' => 'bg-green-100 text-green-800',
            'PENDING' => 'bg-yellow-100 text-yellow-800',
            'RETURNED' => 'bg-blue-100 text-blue-800',
            default => 'bg-gray-100 text-gray-800'
        };
        
        $issuedDate = date('M d, Y H:i', strtotime($asset['issued_at']));
        $refNum = $asset['request_number'] ?? 'N/A';
        
        $content .= <<<HTML
                <tr class="border-b border-gray-200 hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm font-mono text-gray-700">{$asset['asset_code']}</td>
                    <td class="px-4 py-3 text-sm text-gray-700">{$asset['asset_name']}</td>
                    <td class="px-4 py-3 text-center text-sm font-semibold">{$asset['quantity']}</td>
                    <td class="px-4 py-3 text-sm text-gray-600">$issuedDate</td>
                    <td class="px-4 py-3 text-sm font-mono text-gray-700">$refNum</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full $statusClass">{$asset['issuance_status']}</span>
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
</div>
HTML;

include __DIR__ . '/../layout.php';
?>
