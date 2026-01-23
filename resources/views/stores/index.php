<?php

namespace App\Views;

// variables: $stores (array of stores)

?>


<?php ob_start(); ?>
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl text-gray-800">Inventory Stores</h1>
        <?php if (($_SESSION['user_role'] ?? '') === 'ADMIN'): ?>
            <a href="/stores/create" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                + New Store
            </a>
        <?php endif; ?>
    </div>

    <!-- Success/Error Messages -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <?= htmlspecialchars($_SESSION['success']) ?>
            <?php unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?= htmlspecialchars($_SESSION['error']) ?>
            <?php unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <!-- Stores Table -->
    <?php if (!empty($stores)): ?>
        <div class="overflow-x-auto bg-white shadow">
            <table class="w-full">
                <thead>
                    <tr>
                        <th class="px-6 py-3 text-left">Code</th>
                        <th class="px-6 py-3 text-left">Store Name</th>
                        <th class="px-6 py-3 text-left">Location</th>
                        <th class="px-6 py-3 text-left">Department Manager</th>
                        <th class="px-6 py-3 text-left">Status</th>
                        <th class="px-6 py-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($stores as $store): ?>
                        <tr class="border-b">
                            <td class="px-6 py-4"><?= htmlspecialchars($store['store_code']) ?></td>
                            <td class="px-6 py-4"><?= htmlspecialchars($store['store_name']) ?></td>
                            <td class="px-6 py-4"><?= htmlspecialchars($store['location']) ?></td>
                            <td class="px-6 py-4"><?= htmlspecialchars($store['manager_name'] ?? 'Unassigned') ?></td>
                            <td class="px-6 py-4">
                                <?= $store['is_active'] ? 'Active' : 'Inactive' ?>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex justify-center">
                                    <?php if ($store['is_active']): ?>
                                        <form method="POST" action="/stores/<?= $store['id'] ?>/deactivate" onsubmit="return confirm('Are you sure you want to deactivate this store?');">
                                            <button type="submit" class="h-9 w-10 inline-flex items-center justify-center bg-red-600 hover:bg-red-700 text-white rounded" title="Deactivate" aria-label="Deactivate">
                                                <i class="fas fa-ban fa-lg"></i>
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-gray-400">Deactivated</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded">
            No stores found. <a href="/stores/create" class="font-bold underline">Create one now</a>
        </div>
    <?php endif; ?>
</div>
<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/../layout.php'; ?>


