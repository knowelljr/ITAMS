<?php

namespace App\Views;

// variables: $store, $inventory, $totals

?>

<?php include __DIR__ . '/../layout.php'; ?>

<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-start mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Inventory Report</h1>
            <p class="text-gray-600 mt-1"><?= htmlspecialchars($store['store_name']) ?></p>
        </div>
        <div class="text-right">
            <p class="text-sm text-gray-600">Generated: <?= date('M d, Y g:i A') ?></p>
            <button onclick="window.print()" class="mt-2 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                🖨️ Print Report
            </button>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white shadow rounded-lg p-6 border-l-4 border-green-500">
            <p class="text-gray-600 text-sm">Available Items</p>
            <p class="text-3xl font-bold text-green-600"><?= $totals['available_qty'] ?></p>
            <p class="text-gray-600 text-xs mt-1">SR <?= number_format($totals['available_value'], 2) ?></p>
        </div>

        <div class="bg-white shadow rounded-lg p-6 border-l-4 border-yellow-500">
            <p class="text-gray-600 text-sm">Reserved Items</p>
            <p class="text-3xl font-bold text-yellow-600"><?= $totals['reserved_qty'] ?></p>
        </div>

        <div class="bg-white shadow rounded-lg p-6 border-l-4 border-red-500">
            <p class="text-gray-600 text-sm">Damaged Items</p>
            <p class="text-3xl font-bold text-red-600"><?= $totals['damaged_qty'] ?></p>
            <p class="text-gray-600 text-xs mt-1">SR <?= number_format($totals['damaged_value'], 2) ?></p>
        </div>

        <div class="bg-white shadow rounded-lg p-6 border-l-4 border-blue-500">
            <p class="text-gray-600 text-sm">Total Value</p>
            <p class="text-3xl font-bold text-blue-600">SR <?= number_format($totals['available_value'] + $totals['damaged_value'], 2) ?></p>
        </div>
    </div>

    <!-- Inventory Table -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-100 border-b">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Asset Code</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Asset Name</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Category</th>
                        <th class="px-6 py-3 text-right text-sm font-semibold text-gray-700">Available</th>
                        <th class="px-6 py-3 text-right text-sm font-semibold text-gray-700">Reserved</th>
                        <th class="px-6 py-3 text-right text-sm font-semibold text-gray-700">Damaged</th>
                        <th class="px-6 py-3 text-right text-sm font-semibold text-gray-700">Cost/Unit</th>
                        <th class="px-6 py-3 text-right text-sm font-semibold text-gray-700">Total Value</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($inventory)): ?>
                        <?php foreach ($inventory as $item): ?>
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                    <code class="bg-gray-100 px-2 py-1 rounded"><?= htmlspecialchars($item['asset_code']) ?></code>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    <?= htmlspecialchars($item['asset_name']) ?>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700">
                                    <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded text-xs font-semibold">
                                        <?= htmlspecialchars($item['category']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right text-sm font-semibold text-green-600">
                                    <?= $item['quantity_available'] ?>
                                </td>
                                <td class="px-6 py-4 text-right text-sm font-semibold text-yellow-600">
                                    <?= $item['quantity_reserved'] ?>
                                </td>
                                <td class="px-6 py-4 text-right text-sm font-semibold text-red-600">
                                    <?= $item['quantity_damaged'] ?>
                                </td>
                                <td class="px-6 py-4 text-right text-sm text-gray-700">
                                    SR <?= number_format($item['cost'], 2) ?>
                                </td>
                                <td class="px-6 py-4 text-right text-sm font-semibold text-blue-600">
                                    SR <?= number_format(($item['available_value'] ?? 0) + ($item['damaged_value'] ?? 0), 2) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <tr class="bg-gray-50 font-bold border-t-2 border-gray-300">
                            <td colspan="3" class="px-6 py-4 text-right text-sm">TOTALS:</td>
                            <td class="px-6 py-4 text-right text-sm text-green-600"><?= $totals['available_qty'] ?></td>
                            <td class="px-6 py-4 text-right text-sm text-yellow-600"><?= $totals['reserved_qty'] ?></td>
                            <td class="px-6 py-4 text-right text-sm text-red-600"><?= $totals['damaged_qty'] ?></td>
                            <td class="px-6 py-4 text-right text-sm">-</td>
                            <td class="px-6 py-4 text-right text-sm text-blue-600">
                                SR <?= number_format($totals['available_value'] + $totals['damaged_value'], 2) ?>
                            </td>
                        </tr>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                                No inventory items in this store
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Footer -->
    <div class="mt-8 flex justify-between items-center">
        <a href="/stores/<?= $store['id'] ?>" class="text-blue-600 hover:text-blue-900 font-medium">
            ← Back to Store
        </a>
        <a href="/stores/<?= $store['id'] ?>/movements" class="text-purple-600 hover:text-purple-900 font-medium">
            View Movement History →
        </a>
    </div>
</div>

<?php include __DIR__ . '/../footer.php'; ?>
