<?php

namespace App\Views;

// variables: $store (array with store data), $departmentManagers (array of available department managers)

?>

<?php include __DIR__ . '/../layout.php'; ?>

<div class="container mx-auto px-4 py-8 max-w-2xl">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Edit Store: <?= htmlspecialchars($store['store_name']) ?></h1>

    <!-- Error Messages -->
    <?php if (isset($_SESSION['errors'])): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            <p class="font-bold mb-2">Please fix the following errors:</p>
            <ul class="list-disc list-inside">
                <?php foreach ($_SESSION['errors'] as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php unset($_SESSION['errors']); ?>
    <?php endif; ?>

    <form action="/stores/<?= $store['id'] ?>/update" method="POST" class="bg-white shadow rounded-lg p-6">
        <!-- Store Code (Read-only) -->
        <div class="mb-6">
            <label for="store_code" class="block text-sm font-medium text-gray-700 mb-2">
                Store Code
            </label>
            <input type="text" id="store_code" disabled
                class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-700"
                value="<?= htmlspecialchars($store['store_code']) ?>">
            <p class="text-gray-500 text-sm mt-1">Cannot be changed after creation</p>
        </div>

        <!-- Store Name -->
        <div class="mb-6">
            <label for="store_name" class="block text-sm font-medium text-gray-700 mb-2">
                Store Name <span class="text-red-500">*</span>
            </label>
            <input type="text" id="store_name" name="store_name" required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                value="<?= htmlspecialchars($store['store_name']) ?>">
        </div>

        <!-- Location -->
        <div class="mb-6">
            <label for="location" class="block text-sm font-medium text-gray-700 mb-2">
                Location <span class="text-red-500">*</span>
            </label>
            <input type="text" id="location" name="location" required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                value="<?= htmlspecialchars($store['location']) ?>">
        </div>

        <!-- Description -->
        <div class="mb-6">
            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                Description
            </label>
            <textarea id="description" name="description" rows="4"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"><?= htmlspecialchars($store['description'] ?? '') ?></textarea>
        </div>

        <!-- Department Manager -->
        <div class="mb-6">
            <label for="manager_id" class="block text-sm font-medium text-gray-700 mb-2">
                Department Manager <span class="text-red-500">*</span>
            </label>
            <select id="manager_id" name="manager_id" required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">-- Select Department Manager --</option>
                <?php foreach ($departmentManagers as $manager): ?>
                    <option value="<?= $manager['id'] ?>"
                        <?= $store['manager_id'] == $manager['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($manager['first_name'] . ' ' . $manager['last_name']) ?>
                        (<?= $manager['role'] ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Status -->
        <div class="mb-6">
            <label class="flex items-center">
                <input type="checkbox" id="is_active" name="is_active" value="1"
                    <?= $store['is_active'] ? 'checked' : '' ?>
                    class="w-4 h-4 border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                <span class="ml-2 text-sm font-medium text-gray-700">Store is Active</span>
            </label>
            <p class="text-gray-500 text-sm mt-2">Inactive stores won't appear in asset issuance forms</p>
        </div>

        <!-- Metadata -->
        <div class="bg-gray-50 rounded p-4 mb-6">
            <p class="text-sm text-gray-600">
                <strong>Created:</strong> <?= date('M d, Y g:i A', strtotime($store['created_at'])) ?><br>
                <strong>Last Updated:</strong> <?= date('M d, Y g:i A', strtotime($store['updated_at'])) ?>
            </p>
        </div>

        <!-- Buttons -->
        <div class="flex gap-4 justify-end">
            <a href="/stores/<?= $store['id'] ?>" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                Save Changes
            </button>
        </div>
    </form>

    <!-- Danger Zone -->
    <?php if (($_SESSION['user_role'] ?? '') === 'ADMIN'): ?>
        <div class="mt-8 bg-red-50 border border-red-200 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-red-900 mb-4">Danger Zone</h3>
            <p class="text-red-800 mb-4">
                Deactivating a store will remove it from all asset issuance forms. This action cannot be easily undone.
            </p>
            <form action="/stores/<?= $store['id'] ?>/delete" method="POST" onsubmit="return confirm('Are you sure? This will deactivate the store.');">
                <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium">
                    Deactivate Store
                </button>
            </form>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../footer.php'; ?>
