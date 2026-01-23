<?php
/**
 * Stock On-Hand Report View
 * Shows inventory levels by Store with pagination and filtering
 */

$activePage = 'reports';
$error = $_SESSION['error'] ?? '';
$success = $_SESSION['success'] ?? '';
unset($_SESSION['error'], $_SESSION['success']);

$filterType = 'store'; // Fixed to store
$filterId = $_GET['filter_id'] ?? '';
$page = (int)($_GET['page'] ?? 1);

ob_start();
?>
<div class="mb-6">
    <h1 class="text-3xl font-bold mb-2">Stock On-Hand Report</h1>
    <p class="text-gray-600">View current inventory levels by Department or Store</p>
</div>

<?php if ($error): ?>
    <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
        <?php echo htmlspecialchars($error); ?>
    </div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
        <?php echo htmlspecialchars($success); ?>
    </div>
<?php endif; ?>

<!-- Summary Cards -->
<?php
$totalItems = 0;
$totalValue = 0;
$totalReserved = 0;

foreach ($stockData as $item) {
    $totalItems += $item['total_qty'];
    $totalValue += $item['total_value'];
    $totalReserved += $item['quantity_reserved'];
}
?>

<div class="grid grid-cols-1 md:grid-cols-4 gap-2 mb-2">
    <div class="bg-white rounded-lg shadow p-4">
        <div class="text-gray-600 text-sm font-medium">Total Items</div>
        <div class="text-2xl font-bold text-blue-600"><?php echo number_format($totalItems); ?></div>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <div class="text-gray-600 text-sm font-medium">Total Value</div>
        <div class="text-2xl font-bold text-green-600">SR <?php echo number_format($totalValue, 2); ?></div>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <div class="text-gray-600 text-sm font-medium">Unique Assets</div>
        <div class="text-2xl font-bold text-purple-600"><?php echo count($stockData); ?></div>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <div class="text-gray-600 text-sm font-medium">Reserved Items</div>
        <div class="text-2xl font-bold text-orange-600"><?php echo number_format($totalReserved); ?></div>
    </div>
</div>

<!-- Filter and Controls Section -->
<div class="bg-white rounded-lg shadow-md p-4 mb-2">
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 flex-wrap">
        <form method="GET" class="flex gap-4 items-end flex-wrap">
            <div>
                <label for="filter_id" class="block text-sm font-medium text-gray-700 mb-2">
                    Filter by Store
                </label>
                <select name="filter_id" id="filter_id" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">-- All Stores --</option>
                    <?php foreach ($filterOptions as $option): ?>
                        <option value="<?php echo $option['id']; ?>" <?php echo $filterId == $option['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($option['store_name'] . ' (' . $option['store_code'] . ')'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                Filter
            </button>

            <a href="/reports/stock-on-hand" class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400">
                Clear
            </a>
        </form>
        
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

<!-- Stock Table -->
<div class="bg-white rounded-lg shadow overflow-hidden" data-export-name="stock-on-hand">
    <table class="min-w-full divide-y divide-gray-200 data-table">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Asset Code</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Asset Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Store</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">On-Hand</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Reserved</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Damaged</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Total</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Unit Cost</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total Value</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <?php if (empty($stockData)): ?>
                <tr>
                    <td colspan="10" class="px-6 py-4 text-center text-gray-500">
                        No stock items found for the selected criteria
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($stockData as $item): ?>
                    <?php
                    $assetCode = htmlspecialchars($item['asset_code']);
                    $assetName = htmlspecialchars($item['asset_name']);
                    $category = htmlspecialchars($item['category']);
                    $location = htmlspecialchars($item['store_name'] ?? '');
                    $onHand = $item['quantity_on_hand'] ?? 0;
                    $reserved = $item['quantity_reserved'] ?? 0;
                    $damaged = $item['quantity_damaged'] ?? 0;
                    $total = $item['total_qty'] ?? 0;
                    $unitCost = $item['cost'] ?? 0;
                    $totalValue = $item['total_value'] ?? 0;
                    $onHandClass = $onHand <= 5 ? 'bg-red-50 text-red-900' : '';
                    ?>
                    <tr class="<?php echo $onHandClass; ?>">
                        <td class="px-6 py-3 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo $assetCode; ?></td>
                        <td class="px-6 py-3 text-sm text-gray-900"><?php echo $assetName; ?></td>
                        <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500"><?php echo $category; ?></td>
                        <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500"><?php echo $location; ?></td>
                        <td class="px-6 py-3 text-center text-sm font-semibold text-blue-600"><?php echo number_format($onHand); ?></td>
                        <td class="px-6 py-3 text-center text-sm text-orange-600"><?php echo number_format($reserved); ?></td>
                        <td class="px-6 py-3 text-center text-sm text-red-600"><?php echo number_format($damaged); ?></td>
                        <td class="px-6 py-3 text-center text-sm font-semibold text-gray-900"><?php echo number_format($total); ?></td>
                        <td class="px-6 py-3 text-right text-sm text-gray-700">SR <?php echo number_format($unitCost, 2); ?></td>
                        <td class="px-6 py-3 text-right text-sm font-semibold text-green-600">SR <?php echo number_format($totalValue, 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
        <p class="text-sm text-gray-700">
            Showing <strong><?php echo max(1, ($page - 1) * 20 + 1); ?></strong> to <strong><?php echo min($page * 20, $totalRecords); ?></strong> of <strong><?php echo $totalRecords; ?></strong> items
        </p>
    </div>
</div>

<!-- Pagination -->
<?php if ($totalPages > 1): ?>
    <div class="mt-4 flex justify-center items-center gap-2">
        <?php
        if ($page > 1) {
            $prevPage = $page - 1;
            $prevUrl = "/reports/stock-on-hand?filter_id=$filterId&page=$prevPage";
            echo "<a href=\"$prevUrl\" class=\"px-3 py-2 border border-gray-300 rounded hover:bg-gray-50\">&larr; Previous</a>";
        }

        $startPage = max(1, $page - 2);
        $endPage = min($totalPages, $page + 2);

        if ($startPage > 1) {
            echo "<a href=\"/reports/stock-on-hand?filter_id=$filterId&page=1\" class=\"px-3 py-2 border border-gray-300 rounded hover:bg-gray-50\">1</a>";
            if ($startPage > 2) {
                echo "<span class=\"px-3 py-2\">...</span>";
            }
        }

        for ($i = $startPage; $i <= $endPage; $i++) {
            if ($i == $page) {
                echo "<span class=\"px-3 py-2 bg-blue-600 text-white rounded\">$i</span>";
            } else {
                echo "<a href=\"/reports/stock-on-hand?filter_id=$filterId&page=$i\" class=\"px-3 py-2 border border-gray-300 rounded hover:bg-gray-50\">$i</a>";
            }
        }

        if ($endPage < $totalPages) {
            if ($endPage < $totalPages - 1) {
                echo "<span class=\"px-3 py-2\">...</span>";
            }
            echo "<a href=\"/reports/stock-on-hand?filter_id=$filterId&page=$totalPages\" class=\"px-3 py-2 border border-gray-300 rounded hover:bg-gray-50\">$totalPages</a>";
        }

        if ($page < $totalPages) {
            $nextPage = $page + 1;
            $nextUrl = "/reports/stock-on-hand?filter_id=$filterId&page=$nextPage";
            echo "<a href=\"$nextUrl\" class=\"px-3 py-2 border border-gray-300 rounded hover:bg-gray-50\">Next &rarr;</a>";
        }
        ?>
    </div>
    <div class="mt-2 text-center text-sm text-gray-600">
        Page <?php echo $page; ?> of <?php echo $totalPages; ?> (Total: <?php echo $totalRecords; ?> items)
    </div>
<?php endif; ?>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>
