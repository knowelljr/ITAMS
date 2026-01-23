<?php

namespace App\Views;

// variables: $store (array with store data), $inventory (array of items), $stats (array with stats)

?>

<?php include __DIR__ . '/../layout.php'; ?>

<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex justify-between items-start mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800"><?= htmlspecialchars($store['store_name']) ?></h1>
            <p class="text-gray-600 mt-1"><?= htmlspecialchars($store['location']) ?></p>
        </div>
        <?php if (($_SESSION['user_role'] ?? '') === 'ADMIN'): ?>
            <div class="flex gap-2">
                <a href="/stores/<?= $store['id'] ?>/edit" class="px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600">
                    Edit
                </a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Store Information Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <!-- Status -->
        <div class="bg-white shadow rounded-lg p-6">
            <p class="text-gray-600 text-sm mb-2">Status</p>
            <p class="text-2xl font-bold">
                <span class="px-3 py-1 rounded text-white text-sm <?= $store['is_active'] ? 'bg-green-500' : 'bg-gray-500' ?>">
                    <?= $store['is_active'] ? 'Active' : 'Inactive' ?>
                </span>
            </p>
        </div>

        <!-- Department Manager -->
        <div class="bg-white shadow rounded-lg p-6">
            <p class="text-gray-600 text-sm mb-2">Department Manager</p>
            <p class="text-lg font-semibold text-gray-800">
                <?= htmlspecialchars($store['manager_name'] ?? 'Unassigned') ?>
            </p>
        </div>

        <!-- Total Items -->
        <div class="bg-white shadow rounded-lg p-6">
            <p class="text-gray-600 text-sm mb-2">Total Items</p>
            <p class="text-2xl font-bold text-blue-600"><?= ($stats['available_qty'] ?? 0) + ($stats['reserved_qty'] ?? 0) + ($stats['damaged_qty'] ?? 0) ?></p>
        </div>

        <!-- Total Value -->
        <div class="bg-white shadow rounded-lg p-6">
            <p class="text-gray-600 text-sm mb-2">Total Value</p>
            <p class="text-2xl font-bold text-green-600">
                SR <?= number_format($stats['available_value'] ?? 0, 2) ?>
            </p>
        </div>
    </div>

    <!-- Description -->
    <?php if (!empty($store['description'])): ?>
        <div class="bg-blue-50 border border-blue-200 text-blue-900 px-4 py-3 rounded mb-6">
            <p class="font-semibold mb-2">Description</p>
            <p><?= htmlspecialchars($store['description']) ?></p>
        </div>
    <?php endif; ?>

    <!-- Inventory Overview -->
    <div class="mb-8">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold text-gray-800">Inventory Overview</h2>
            <a href="/stores/<?= $store['id'] ?>/inventory" class="text-blue-600 hover:text-blue-900 font-medium">
                View Full Report →
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Available -->
            <div class="bg-white shadow rounded-lg p-6 border-l-4 border-green-500">
                <p class="text-gray-600 text-sm mb-2">Available for Issue</p>
                <p class="text-3xl font-bold text-green-600"><?= $stats['available_qty'] ?? 0 ?></p>
                <p class="text-gray-600 text-xs mt-2">SR <?= number_format($stats['available_value'] ?? 0, 2) ?></p>
            </div>

            <!-- Reserved -->
            <div class="bg-white shadow rounded-lg p-6 border-l-4 border-yellow-500">
                <p class="text-gray-600 text-sm mb-2">Reserved/Allocated</p>
                <p class="text-3xl font-bold text-yellow-600"><?= $stats['reserved_qty'] ?? 0 ?></p>
            </div>

            <!-- Damaged -->
            <div class="bg-white shadow rounded-lg p-6 border-l-4 border-red-500">
                <p class="text-gray-600 text-sm mb-2">Damaged/In Repair</p>
                <p class="text-3xl font-bold text-red-600"><?= $stats['damaged_qty'] ?? 0 ?></p>
                <p class="text-gray-600 text-xs mt-2">SR <?= number_format($stats['damaged_value'] ?? 0, 2) ?></p>
            </div>

            <!-- Last Updated -->
            <div class="bg-white shadow rounded-lg p-6 border-l-4 border-blue-500">
                <p class="text-gray-600 text-sm mb-2">Last Updated</p>
                <p class="text-lg font-semibold text-gray-800">
                    <?= date('M d, Y', strtotime($store['updated_at'])) ?>
                </p>
                <p class="text-gray-600 text-xs mt-2"><?= date('g:i A', strtotime($store['updated_at'])) ?></p>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h3>
        <div class="flex flex-wrap gap-3">
            <a href="/stores/<?= $store['id'] ?>/inventory" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                📊 View Inventory Report
            </a>
            <a href="/stores/<?= $store['id'] ?>/movements" class="px-4 py-2 bg-purple-500 text-white rounded hover:bg-purple-600">
                📋 View Movement History
            </a>
            <?php if (($_SESSION['user_role'] ?? '') === 'ADMIN'): ?>
                <a href="/assets/issue" class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">
                    ➕ Issue Asset from Store
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../footer.php'; ?>
