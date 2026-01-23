<?php

namespace App\Views;

// variables: $store, $movements

?>

<?php include __DIR__ . '/../layout.php'; ?>

<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-start mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Movement History</h1>
            <p class="text-gray-600 mt-1"><?= htmlspecialchars($store['store_name']) ?></p>
        </div>
        <button onclick="window.print()" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            🖨️ Print
        </button>
    </div>

    <!-- Movement Types Legend -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
        <p class="font-semibold text-blue-900 mb-2">Movement Types:</p>
        <div class="grid grid-cols-2 md:grid-cols-5 gap-3 text-sm text-blue-800">
            <span><strong>ISSUED</strong> - Asset issued to requester</span>
            <span><strong>RECEIVED</strong> - Asset received back</span>
            <span><strong>RETURNED</strong> - Asset returned for repair</span>
            <span><strong>DAMAGED</strong> - Asset marked as damaged</span>
            <span><strong>TRANSFERRED</strong> - Inter-store transfer</span>
        </div>
    </div>

    <!-- Movements Table -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-100 border-b">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Date</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Type</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Asset</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Qty</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">From</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">To</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Performed By</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Reason</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($movements)): ?>
                        <?php foreach ($movements as $movement): ?>
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    <?= date('M d, Y', strtotime($movement['created_at'])) ?><br>
                                    <span class="text-xs text-gray-500"><?= date('g:i A', strtotime($movement['created_at'])) ?></span>
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <?php
                                    $typeColors = [
                                        'ISSUED' => 'bg-green-100 text-green-800',
                                        'RECEIVED' => 'bg-blue-100 text-blue-800',
                                        'RETURNED' => 'bg-yellow-100 text-yellow-800',
                                        'DAMAGED' => 'bg-red-100 text-red-800',
                                        'TRANSFERRED' => 'bg-purple-100 text-purple-800',
                                        'TEST_ISSUED' => 'bg-gray-100 text-gray-800'
                                    ];
                                    $color = $typeColors[$movement['movement_type']] ?? 'bg-gray-100 text-gray-800';
                                    ?>
                                    <span class="px-3 py-1 rounded text-xs font-semibold <?= $color ?>">
                                        <?= htmlspecialchars($movement['movement_type']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    <code class="bg-gray-100 px-2 py-1 rounded text-xs"><?= htmlspecialchars($movement['asset_code']) ?></code><br>
                                    <span class="text-xs text-gray-600"><?= htmlspecialchars($movement['asset_name']) ?></span>
                                </td>
                                <td class="px-6 py-4 text-sm font-semibold text-gray-700">
                                    <?= $movement['quantity'] ?>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700">
                                    <?php if ($movement['from_store_id']): ?>
                                        <span class="text-xs">Store <?= htmlspecialchars($movement['from_store_id']) ?></span>
                                    <?php else: ?>
                                        <span class="text-gray-400">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700">
                                    <?php if ($movement['to_store_id']): ?>
                                        <span class="text-xs">Store <?= htmlspecialchars($movement['to_store_id']) ?></span>
                                    <?php else: ?>
                                        <span class="text-gray-400">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700">
                                    <?= htmlspecialchars($movement['first_name'] . ' ' . $movement['last_name']) ?>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700">
                                    <?= htmlspecialchars($movement['reason'] ?? '-') ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                                No movements recorded for this store
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
        <a href="/stores/<?= $store['id'] ?>/inventory" class="text-purple-600 hover:text-purple-900 font-medium">
            View Inventory Report →
        </a>
    </div>
</div>

<?php include __DIR__ . '/../footer.php'; ?>
