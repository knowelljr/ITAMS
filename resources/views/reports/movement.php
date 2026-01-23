<?php
$activePage = 'assets';

// Get date filter values
$fromDate = $_GET['from_date'] ?? date('Y-m-01');
$toDate = $_GET['to_date'] ?? date('Y-m-d');
$search = $_GET['search'] ?? '';

$content = <<<HTML
<div class="mb-6">
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold">Asset Movement Report</h1>
        <a href="/dashboard" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">Back to Dashboard</a>
    </div>
</div>

<!-- Summary Statistics -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
HTML;

// Calculate statistics
$totalIssuances = count(array_filter($allMovements, fn($m) => $m['transaction_type'] === 'ISSUANCE'));
$totalReceipts = count(array_filter($allMovements, fn($m) => $m['transaction_type'] === 'RECEIPT'));
$totalQtyIssued = array_sum(array_map(fn($m) => $m['transaction_type'] === 'ISSUANCE' ? $m['quantity'] : 0, $allMovements));
$totalQtyReceived = array_sum(array_map(fn($m) => $m['transaction_type'] === 'RECEIPT' ? $m['quantity'] : 0, $allMovements));

$content .= <<<HTML
    <div class="bg-white rounded-lg shadow p-4">
        <div class="text-gray-600 text-sm font-medium">Total Issuances</div>
        <div class="text-2xl font-bold text-blue-600">$totalIssuances</div>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <div class="text-gray-600 text-sm font-medium">Total Receipts</div>
        <div class="text-2xl font-bold text-green-600">$totalReceipts</div>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <div class="text-gray-600 text-sm font-medium">Total Qty Issued</div>
        <div class="text-2xl font-bold text-purple-600">$totalQtyIssued</div>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <div class="text-gray-600 text-sm font-medium">Total Qty Received</div>
        <div class="text-2xl font-bold text-orange-600">$totalQtyReceived</div>
    </div>
</div>

<!-- Search and Date Filter Section -->
<div class="bg-white rounded-lg shadow-md p-4 mb-2">
    <form method="GET" class="flex flex-wrap justify-between items-end gap-4">
        <div class="flex gap-4 items-end">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input type="text" name="search" value="$search" placeholder="Asset code, name, reference..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="w-40">
                <label class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                <input type="date" name="from_date" value="$fromDate" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="w-40">
                <label class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                <input type="date" name="to_date" value="$toDate" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Filter</button>
        </div>
        <button type="button" onclick="exportToCSV()" class="h-9 w-10 inline-flex items-center justify-center bg-green-600 text-white rounded hover:bg-green-700" title="Export CSV" aria-label="Export CSV">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v12m0 0l-4-4m4 4l4-4" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15h14v4a2 2 0 01-2 2H7a2 2 0 01-2-2v-4z" />
            </svg>
            <span class="sr-only">Export CSV</span>
        </button>
    </form>
</div>

<!-- Transactions Table -->
HTML;

if (empty($allMovements)) {
    $content .= <<<HTML
    <div class="bg-white rounded-lg shadow p-6">
        <p class="text-center text-gray-600">No asset movements found for the selected date range.</p>
    </div>
HTML;
} else {
    $start = ($page - 1) * $perPage + 1;
    $end = min($page * $perPage, $totalMovements);
    
    $content .= <<<HTML
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table id="movementTable" class="min-w-full table-auto">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Asset Code</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Asset Name</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">From/To</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Reference #</th>
                    <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Qty</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Status</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
HTML;

    foreach ($paginatedMovements as $movement) {
        $date = date('M d, Y H:i', strtotime($movement['transaction_date']));
        $type = $movement['transaction_type'];
        $typeClass = $type === 'ISSUANCE' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800';
        $fromTo = $type === 'ISSUANCE' ? 
            ($movement['issued_to_name'] ?? 'Stock') : 
            ($movement['issued_by_name'] ?? 'Unknown');
        $refNum = $movement['reference_number'] ?? 'N/A';
        $status = $movement['issuance_status'] ?? 'PENDING';
        $statusClass = match($status) {
            'RECEIVED' => 'bg-green-100 text-green-800',
            'PENDING' => 'bg-yellow-100 text-yellow-800',
            'CANCELLED' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };

        $content .= <<<HTML
                <tr class="hover:bg-blue-50 transition-colors duration-150">
                    <td class="px-6 py-4 text-sm font-mono font-bold text-blue-600">{$movement['asset_code']}</td>
                    <td class="px-6 py-4 text-sm font-semibold text-gray-900">{$movement['asset_name']}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">$date</td>
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex items-center px-3 py-1.5 text-xs font-bold rounded-full border $typeClass shadow-sm">$type</span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-700">$fromTo</td>
                    <td class="px-6 py-4 text-sm font-mono text-gray-700">$refNum</td>
                    <td class="px-6 py-4 text-center text-sm font-semibold text-gray-700">{$movement['quantity']}</td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-3 py-1.5 text-xs font-bold rounded-full border $statusClass shadow-sm">$status</span>
                    </td>
                </tr>
HTML;
    }

    $content .= <<<HTML
            </tbody>
        </table>
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
            <p class="text-sm text-gray-700">Showing <span class="font-semibold">$start</span> to <span class="font-semibold">$end</span> of <span class="font-semibold">$totalMovements</span> transactions</p>
        </div>
HTML;

    // Pagination controls
    if ($totalPages > 1) {
        $searchParam = !empty($search) ? "&search=" . urlencode($search) : "";
        $content .= '<div class="mt-4 flex justify-center items-center gap-2">';
        
        // Previous button
        if ($page > 1) {
            $prevUrl = "?from_date=$fromDate&to_date=$toDate{$searchParam}&page=" . ($page - 1);
            $content .= "<a href='$prevUrl' class='px-3 py-2 border border-gray-300 rounded hover:bg-gray-50'>&larr; Previous</a>";
        }
        
        // Page numbers
        $startPage = max(1, $page - 2);
        $endPage = min($totalPages, $page + 2);
        
        if ($startPage > 1) {
            $content .= "<a href='?from_date=$fromDate&to_date=$toDate{$searchParam}&page=1' class='px-3 py-2 border border-gray-300 rounded hover:bg-gray-50'>1</a>";
            if ($startPage > 2) {
                $content .= "<span class='px-3 py-2'>...</span>";
            }
        }
        
        for ($i = $startPage; $i <= $endPage; $i++) {
            if ($i === $page) {
                $content .= "<span class='px-3 py-2 bg-blue-600 text-white rounded'>$i</span>";
            } else {
                $content .= "<a href='?from_date=$fromDate&to_date=$toDate{$searchParam}&page=$i' class='px-3 py-2 border border-gray-300 rounded hover:bg-gray-50'>$i</a>";
            }
        }
        
        if ($endPage < $totalPages) {
            if ($endPage < $totalPages - 1) {
                $content .= "<span class='px-3 py-2'>...</span>";
            }
            $content .= "<a href='?from_date=$fromDate&to_date=$toDate{$searchParam}&page=$totalPages' class='px-3 py-2 border border-gray-300 rounded hover:bg-gray-50'>$totalPages</a>";
        }
        
        // Next button
        if ($page < $totalPages) {
            $nextUrl = "?from_date=$fromDate&to_date=$toDate{$searchParam}&page=" . ($page + 1);
            $content .= "<a href='$nextUrl' class='px-3 py-2 border border-gray-300 rounded hover:bg-gray-50'>Next &rarr;</a>";
        }
        
        $content .= '</div>';
        $content .= '<div class="mt-2 text-center text-sm text-gray-600">Page ' . $page . ' of ' . $totalPages . '</div>';
        
        $content .= '</div>';
        $content .= '</div>';
    }
    
    $content .= '</div>';
}

$content .= <<<HTML
</div>

<script>
function exportToCSV() {
    const table = document.getElementById('movementTable');
    if (!table) {
        alert('No data to export');
        return;
    }
    
    let csv = [];
    const rows = table.querySelectorAll('tr');
    
    rows.forEach(row => {
        const cols = row.querySelectorAll('td, th');
        const csvRow = [];
        cols.forEach(col => {
            csvRow.push('"' + col.innerText.replace(/"/g, '""') + '"');
        });
        csv.push(csvRow.join(','));
    });
    
    const csvContent = csv.join('\\n');
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    const fileName = 'Asset_Movement_Report_' + new Date().toISOString().slice(0, 10) + '.csv';
    
    link.setAttribute('href', url);
    link.setAttribute('download', fileName);
    link.style.visibility = 'hidden';
    
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
</script>
HTML;

include __DIR__ . '/../layout.php';
?>
