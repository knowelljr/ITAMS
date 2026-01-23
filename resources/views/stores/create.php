<?php

namespace App\Views;

// variables: $departmentManagers (array of users who can be department managers)

?>

<?php include __DIR__ . '/../layout.php'; ?>

<div class="container mx-auto px-4 py-8 max-w-2xl">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Create New Store</h1>

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

    <form action="/stores/store" method="POST" class="bg-white shadow rounded-lg p-6">
        <!-- Store Code -->
        <div class="mb-6">
            <label for="store_code" class="block text-sm font-medium text-gray-700 mb-2">
                Store Code <span class="text-red-500">*</span>
            </label>
            <input type="text" id="store_code" name="store_code" required
                placeholder="e.g., MAIN_STORE"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                value="<?= htmlspecialchars($_POST['store_code'] ?? '') ?>">
            <p class="text-gray-500 text-sm mt-1">Unique identifier for the store (e.g., MAIN_STORE, BRANCH_01)</p>
        </div>

        <!-- Store Name -->
        <div class="mb-6">
            <label for="store_name" class="block text-sm font-medium text-gray-700 mb-2">
                Store Name <span class="text-red-500">*</span>
            </label>
            <input type="text" id="store_name" name="store_name" required
                placeholder="e.g., Main Warehouse"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                value="<?= htmlspecialchars($_POST['store_name'] ?? '') ?>">
        </div>

        <!-- Location -->
        <div class="mb-6">
            <label for="location" class="block text-sm font-medium text-gray-700 mb-2">
                Location <span class="text-red-500">*</span>
            </label>
            <input type="text" id="location" name="location" required
                placeholder="e.g., Building A, Ground Floor"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                value="<?= htmlspecialchars($_POST['location'] ?? '') ?>">
        </div>

        <!-- Description -->
        <div class="mb-6">
            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                Description
            </label>
            <textarea id="description" name="description" rows="4"
                placeholder="Optional: Additional details about this store"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
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
                        <?= ($_POST['manager_id'] ?? '') == $manager['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($manager['first_name'] . ' ' . $manager['last_name']) ?>
                        (<?= $manager['role'] ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Buttons -->
        <div class="flex gap-4 justify-end">
            <a href="/stores" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                Create Store
            </button>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../footer.php'; ?>
