<?php
$activePage = 'assets';

// Get filter values
$fromDate = $_GET['from_date'] ?? date('Y-m-01');
$toDate = $_GET['to_date'] ?? date('Y-m-d');
$search = $_GET['search'] ?? '';
$movementType = $_GET['movement_type'] ?? '';

// Start output buffering for content
ob_start();
?>

<script>
function exportToCSV() {
    const table = document.getElementById('movementTable');
    if (!table) {
        alert('No data to export');
        return;
    }
    
    let csv = [];
    
    // Headers
    const headers = [];
    table.querySelectorAll('thead th').forEach(th => {
        headers.push(th.textContent.trim());
    });
    csv.push(headers.join(','));
    
    // Data rows
    table.querySelectorAll('tbody tr').forEach(tr => {
        const row = [];
        tr.querySelectorAll('td').forEach(td => {
            // Get text content and clean it
            let text = td.textContent.trim().replace(/\s+/g, ' ');
            // Escape quotes and wrap in quotes if contains comma
            if (text.includes(',') || text.includes('"')) {
                text = '"' + text.replace(/"/g, '""') + '"';
            }
            row.push(text);
        });
        csv.push(row.join(','));
    });
    
    // Create download
    const csvContent = csv.join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', 'asset_movements_' + new Date().toISOString().split('T')[0] + '.csv');
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
</script>

<?php

$content = <<<HTML
<div class="mb-6">
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold">Asset Movement Report</h1>
        <a href="/assets" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">Back to Assets</a>
    </div>
</div>

<!-- Summary Statistics -->
<div class="grid grid-cols-5 gap-2 mb-2">
HTML;

// Calculate statistics from the statistics array
$totalReceived = $statistics['by_type']['RECEIVED'] ?? 0;
$totalStored = $statistics['by_type']['STORED'] ?? 0;
$totalIssued = $statistics['by_type']['ISSUED'] ?? 0;
$totalReturned = $statistics['by_type']['RETURNED'] ?? 0;
$totalTransferred = $statistics['by_type']['TRANSFERRED'] ?? 0;

$qtyReceived = $statistics['total_quantities']['RECEIVED'] ?? 0;
$qtyIssued = $statistics['total_quantities']['ISSUED'] ?? 0;

$content .= <<<HTML
    <div class="bg-white p-4 rounded-lg text-center shadow">
        <p class="text-gray-600 text-sm font-medium">Received</p>
        <p class="text-2xl font-bold text-green-600">$totalReceived</p>
        <p class="text-xs text-gray-500">Qty: $qtyReceived</p>
    </div>
    <div class="bg-white p-4 rounded-lg text-center shadow">
        <p class="text-gray-600 text-sm font-medium">Stored</p>
        <p class="text-2xl font-bold text-blue-600">$totalStored</p>
    </div>
    <div class="bg-white p-4 rounded-lg text-center shadow">
        <p class="text-gray-600 text-sm font-medium">Issued</p>
        <p class="text-2xl font-bold text-purple-600">$totalIssued</p>
        <p class="text-xs text-gray-500">Qty: $qtyIssued</p>
    </div>
    <div class="bg-white p-4 rounded-lg text-center shadow">
        <p class="text-gray-600 text-sm font-medium">Returned</p>
        <p class="text-2xl font-bold text-orange-600">$totalReturned</p>
    </div>
    <div class="bg-white p-4 rounded-lg text-center shadow">
        <p class="text-gray-600 text-sm font-medium">Transferred</p>
        <p class="text-2xl font-bold text-indigo-600">$totalTransferred</p>
    </div>
</div>

<!-- Search and Filter Section -->
<div class="bg-white rounded-lg shadow p-4 mb-2">
    <form method="GET" class="grid grid-cols-12 gap-4">
        <div class="col-span-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
            <input type="text" name="search" value="$search" placeholder="Asset code, name, reason..." 
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div class="col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
            <input type="date" name="from_date" value="$fromDate" 
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div class="col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
            <input type="date" name="to_date" value="$toDate" 
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div class="col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">Movement Type</label>
            <select name="movement_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All Types</option>
                <option value="RECEIVED" <?php echo $movementType === 'RECEIVED' ? 'selected' : ''; ?>>Received</option>
                <option value="STORED" <?php echo $movementType === 'STORED' ? 'selected' : ''; ?>>Stored</option>
                <option value="ISSUED" <?php echo $movementType === 'ISSUED' ? 'selected' : ''; ?>>Issued</option>
                <option value="RETURNED" <?php echo $movementType === 'RETURNED' ? 'selected' : ''; ?>>Returned</option>
                <option value="TRANSFERRED" <?php echo $movementType === 'TRANSFERRED' ? 'selected' : ''; ?>>Transferred</option>
                <option value="DAMAGED" <?php echo $movementType === 'DAMAGED' ? 'selected' : ''; ?>>Damaged</option>
                <option value="COUNTED" <?php echo $movementType === 'COUNTED' ? 'selected' : ''; ?>>Counted</option>
            </select>
        </div>
        <div class="col-span-2 flex items-end gap-2 py-1">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                Filter
            </button>
            <button type="button" onclick="exportToCSV()" 
                    class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 h-10 flex items-center" 
                    title="Export CSV">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </button>
        </div>
    </form>
</div>

<!-- Movements Table -->
HTML;

if (empty($movements)) {
    $content .= <<<HTML
    <div class="bg-white rounded-lg shadow p-6">
        <p class="text-center text-gray-600">No asset movements found for the selected filters.</p>
    </div>
HTML;
} else {
    $start = ($page - 1) * 20 + 1;
    $end = min($page * 20, $totalMovements);
    
    $content .= <<<HTML
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table id="movementTable" class="w-full">
                <thead class="bg-gray-100 border-b-2 border-gray-300">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Date & Time</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Asset Code</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Asset Name</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 uppercase">Type</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 uppercase">Quantity</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">From  To</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Performed By</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">User/Recipient</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Reason</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Reference</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
HTML;
    
    foreach ($movements as $movement) {
        $date = date('M d, Y', strtotime($movement['created_at']));
        $time = date('h:i A', strtotime($movement['created_at']));
        $assetCode = htmlspecialchars($movement['asset_code'] ?? 'N/A');
        $assetName = htmlspecialchars($movement['asset_name'] ?? 'N/A');
        $type = htmlspecialchars($movement['movement_type']);
        $quantity = htmlspecialchars($movement['quantity']);
        $performedBy = htmlspecialchars($movement['performed_by_name'] ?? 'N/A');
        $userName = htmlspecialchars($movement['user_name'] ?? '-');
        $reason = htmlspecialchars($movement['reason'] ?? '-');
        $reference = htmlspecialchars($movement['reference_number'] ?? '-');
        
        // Color code based on movement type
        $typeColors = [
            'RECEIVED' => 'bg-green-100 text-green-800',
            'STORED' => 'bg-blue-100 text-blue-800',
            'ISSUED' => 'bg-purple-100 text-purple-800',
            'RETURNED' => 'bg-orange-100 text-orange-800',
            'TRANSFERRED' => 'bg-indigo-100 text-indigo-800',
            'DAMAGED' => 'bg-red-100 text-red-800',
            'COUNTED' => 'bg-gray-100 text-gray-800'
        ];
        $typeColor = $typeColors[$type] ?? 'bg-gray-100 text-gray-800';
        
        // Format from/to locations
        $fromLocation = $movement['from_store_name'] ?? $movement['from_location'] ?? '-';
        $toLocation = $movement['to_store_name'] ?? $movement['to_location'] ?? '-';
        $location = htmlspecialchars("$fromLocation  $toLocation");
        
        $content .= <<<HTML
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm">
                            <div class="font-medium text-gray-900">$date</div>
                            <div class="text-xs text-gray-500">$time</div>
                        </td>
                        <td class="px-4 py-3 text-sm font-medium text-blue-600">$assetCode</td>
                        <td class="px-4 py-3 text-sm text-gray-900">$assetName</td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full $typeColor">$type</span>
                        </td>
                        <td class="px-4 py-3 text-sm text-center font-semibold">$quantity</td>
                        <td class="px-4 py-3 text-sm text-gray-700">$location</td>
                        <td class="px-4 py-3 text-sm text-gray-900">$performedBy</td>
                        <td class="px-4 py-3 text-sm text-gray-700">$userName</td>
                        <td class="px-4 py-3 text-sm text-gray-600">$reason</td>
                        <td class="px-4 py-3 text-sm text-gray-600">$reference</td>
                    </tr>
HTML;
    }
    
    $content .= <<<HTML
                </tbody>
            </table>
        </div>
        <div class="px-6 py-3 border-t border-gray-200 bg-gray-50">
            <p class="text-sm text-gray-700">
                Showing <span class="font-semibold">$start</span> to <span class="font-semibold">$end</span> 
                of <span class="font-semibold">$totalMovements</span> movements
            </p>
        </div>
HTML;
    
    // Pagination
    if ($totalPages > 1) {
        $content .= <<<HTML
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-700">
                    Page <span class="font-semibold">$page</span> of <span class="font-semibold">$totalPages</span>
                </div>
                <div class="flex gap-2">
HTML;
        
        if ($page > 1) {
            $prevPage = $page - 1;
            $queryString = http_build_query(array_merge($_GET, ['page' => $prevPage]));
            $content .= <<<HTML
                    <a href="?$queryString" class="px-3 py-2 bg-white border border-gray-300 rounded hover:bg-gray-50">
                        Previous
                    </a>
HTML;
        }
        
        // Show page numbers
        $startPage = max(1, $page - 2);
        $endPage = min($totalPages, $page + 2);
        
        for ($i = $startPage; $i <= $endPage; $i++) {
            $queryString = http_build_query(array_merge($_GET, ['page' => $i]));
            $activeClass = $i === $page ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50';
            $content .= <<<HTML
                    <a href="?$queryString" class="px-3 py-2 border border-gray-300 rounded $activeClass">$i</a>
HTML;
        }
        
        if ($page < $totalPages) {
            $nextPage = $page + 1;
            $queryString = http_build_query(array_merge($_GET, ['page' => $nextPage]));
            $content .= <<<HTML
                    <a href="?$queryString" class="px-3 py-2 bg-white border border-gray-300 rounded hover:bg-gray-50">
                        Next
                    </a>
HTML;
        }
        
        $content .= <<<HTML
                </div>
            </div>
        </div>
HTML;
    }
    
    $content .= <<<HTML
    </div>
HTML;
}

$content .= ob_get_clean();
include __DIR__ . '/../layout.php';
?>