<?php
$activePage = 'departments';

// Get messages
$error = $_SESSION['error'] ?? '';
unset($_SESSION['error']);

// Prepare content
$content = <<<HTML
<div class="mb-6">
    <h1 class="text-3xl font-bold mb-2">Edit Department</h1>
    <a href="/departments" class="text-blue-600 hover:text-blue-800">&larr; Back to Departments</a>
</div>

HTML;

if ($error) {
    $content .= <<<HTML
<div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
    $error
</div>
HTML;
}

// ...existing code...
$content .= <<<HTML
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="p-4 flex flex-col md:flex-row md:items-center md:justify-between gap-3 dt-controls">
        <!-- Add any filter/search controls here if needed -->
    </div>
    <div class="p-6">
    <form action="/departments/update/{$department['id']}" method="POST">
        <div class="mb-4">
            <label for="department_code" class="block text-sm font-medium text-gray-700 mb-2">
                Department Code <span class="text-red-500">*</span>
                <span class="text-gray-500 text-xs">(Max 10 characters)</span>
            </label>
            <input 
                type="text" 
                id="department_code" 
                name="department_code" 
                value="{$department['department_code']}"
                required 
                maxlength="10"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 uppercase"
            >
        </div>

        <div class="mb-6">
            <label for="department_name" class="block text-sm font-medium text-gray-700 mb-2">
                Department Name <span class="text-red-500">*</span>
                <span class="text-gray-500 text-xs">(Max 80 characters)</span>
            </label>
            <input 
                type="text" 
                id="department_name" 
                name="department_name" 
                value="{$department['department_name']}"
                required 
                maxlength="80"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
        </div>

        <div class="flex gap-2">
            <button 
                type="submit" 
                class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700"
            >
                Update Department
            </button>
            <a 
                href="/departments" 
                class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400"
            >
                Cancel
            </a>
        </div>
    </form>
</div>
HTML;

include __DIR__ . '/../../layout.php';
?>
