<?php
$activePage = 'assets';

// Flash messages
$error = $_SESSION['error'] ?? '';
$success = $_SESSION['success'] ?? '';
unset($_SESSION['error'], $_SESSION['success']);

$content = <<<HTML
<div class="mb-6">
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold">Asset Management</h1>
        <a href="/assets/create" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            <span class="mr-2">+</span> Add New Asset
        </a>
    </div>
</div>
HTML;

if ($error) {
    $content .= <<<HTML
<div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">$error</div>
HTML;
}

if ($success) {
    $content .= <<<HTML
<div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">$success</div>
HTML;
}

$content .= <<<HTML
<!-- Search and Controls -->
<div class="bg-white rounded-lg shadow-md p-4 mb-2">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <input type="text" placeholder="Search assets..." class="dt-search w-full md:w-1/3 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        <div class="flex gap-2">
            <button type="button" class="dt-export h-9 w-10 inline-flex items-center justify-center bg-green-600 text-white rounded hover:bg-green-700" title="Export CSV" aria-label="Export CSV">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v12m0 0l-4-4m4 4l4-4" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15h14v4a2 2 0 01-2 2H7a2 2 0 01-2-2v-4z" />
                </svg>
                <span class="sr-only">Export CSV</span>
            </button>
            <button type="button" class="dt-print h-9 w-10 inline-flex items-center justify-center bg-gray-700 text-white rounded hover:bg-gray-800" title="Print" aria-label="Print">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9V4h12v5" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 15h12v5H6v-5z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 13H5a2 2 0 01-2-2V9a2 2 0 012-2h14a2 2 0 012 2v2a2 2 0 01-2 2h-1" />
                </svg>
                <span class="sr-only">Print</span>
            </button>
        </div>
    </div>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden data-table" data-page-size="10" data-export-name="assets">
    <table class="min-w-full table-auto">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Serial #</th>
                <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">On Hand</th>
                <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Issued</th>
                <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
HTML;

if (empty($assets)) {
    $content .= <<<HTML
            <tr>
                <td colspan="8" class="px-6 py-4 text-center text-gray-500">No assets found</td>
            </tr>
HTML;
} else {
    foreach ($assets as $asset) {
        $statusColors = [
            'AVAILABLE' => 'bg-green-100 text-green-800',
            'ISSUED' => 'bg-blue-100 text-blue-800',
            'MAINTENANCE' => 'bg-yellow-100 text-yellow-800',
            'DECOMMISSIONED' => 'bg-red-100 text-red-800',
        ];
        $statusColor = $statusColors[$asset['status']] ?? 'bg-gray-100 text-gray-800';
        
        $lowStock = ($asset['quantity_onhand'] < $asset['optimum_stock']) ? 'bg-red-50' : '';

        $content .= <<<HTML
            <tr class="$lowStock">
                <td class="px-6 py-2 whitespace-nowrap">
                    <span class="text-sm text-gray-900">{$asset['asset_code']}</span>
                </td>
                <td class="px-6 py-2 whitespace-nowrap text-sm font-medium text-gray-900">{$asset['name']}</td>
                <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500">{$asset['category']}</td>
                <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500">{$asset['serial_number']}</td>
                <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900">{$asset['quantity_onhand']}</td>
                <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900">{$asset['quantity_issued']}</td>
                <td class="px-6 py-2 whitespace-nowrap">
                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full $statusColor">{$asset['status']}</span>
                </td>
                <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500">
                    <div class="flex items-center gap-2">
                        <a href="/assets/show/{$asset['id']}" class="px-3 py-1 bg-indigo-600 text-white rounded hover:bg-indigo-700" title="View" aria-label="View">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <span class="sr-only">View</span>
                        </a>
                        <a href="/assets/edit/{$asset['id']}" class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700" title="Edit" aria-label="Edit">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M4 20h4.586a1 1 0 00.707-.293l9.414-9.414a1 1 0 000-1.414l-3.586-3.586a1 1 0 00-1.414 0L4.293 14.707A1 1 0 004 15.414V20z" />
                            </svg>
                            <span class="sr-only">Edit</span>
                        </a>
                        <a href="/assets/print-qr/{$asset['id']}" class="px-3 py-1 bg-purple-600 text-white rounded hover:bg-purple-700" target="_blank" title="QR Code" aria-label="QR Code">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h7v7H3V3zM14 3h7v7h-7V3zM14 14h7v7h-7v-7zM3 14h7v7H3v-7z" />
                            </svg>
                            <span class="sr-only">QR Code</span>
                        </a>
                        <a href="/assets/delete/{$asset['id']}" class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700" onclick="return confirm('Are you sure you want to delete this asset?')" title="Delete" aria-label="Delete">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            <span class="sr-only">Delete</span>
                        </a>
                    </div>
                </td>
            </tr>
HTML;
    }
}

$content .= <<<HTML
        </tbody>
    </table>
    <div class="dt-pagination flex flex-wrap gap-2 px-4 py-3 border-t border-gray-200"></div>
</div>
HTML;

include __DIR__ . '/../layout.php';
?>
