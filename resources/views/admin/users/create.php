<?php
$activePage = 'users';

// Get messages
$error = $_SESSION['error'] ?? '';
unset($_SESSION['error']);

// Get departments
$departments = \App\Controllers\DepartmentController::getAllDepartments();

// Prepare content
$content = <<<HTML
<div class="mb-6">
    <h1 class="text-3xl font-bold mb-2">Create New User</h1>
    <a href="/users" class="text-blue-600 hover:text-blue-800">&larr; Back to Users</a>
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
    <form action="/users/store" method="POST">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name <span class="text-red-500">*</span></label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    required 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Enter full name"
                >
            </div>

            <div class="mb-4">
                <label for="employee_number" class="block text-sm font-medium text-gray-700 mb-2">Employee Number <span class="text-red-500">*</span></label>
                <input 
                    type="text" 
                    id="employee_number" 
                    name="employee_number" 
                    required 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Enter employee number"
                >
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address <span class="text-red-500">*</span></label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    required 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Enter email address"
                >
            </div>

            <div class="mb-4">
                <label for="mobile_number" class="block text-sm font-medium text-gray-700 mb-2">Mobile Number</label>
                <input 
                    type="text" 
                    id="mobile_number" 
                    name="mobile_number" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Enter mobile number"
                >
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="mb-4">
                <label for="department_id" class="block text-sm font-medium text-gray-700 mb-2">Department <span class="text-red-500">*</span></label>
                <select 
                    id="department_id" 
                    name="department_id" 
                    required 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                    <option value="">Select Department</option>
HTML;

foreach ($departments as $dept) {
    $content .= "<option value='{$dept['id']}'>{$dept['department_code']} - {$dept['department_name']}</option>";
}

$content .= <<<HTML
                </select>
            </div>

            <div class="mb-4">
                <label for="role" class="block text-sm font-medium text-gray-700 mb-2">User Role <span class="text-red-500">*</span></label>
                <select 
                    id="role" 
                    name="role" 
                    required 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                    <option value="REQUESTER">Requester</option>
                    <option value="DEPARTMENT_MANAGER">Department Manager</option>
                    <option value="IT_STAFF">IT Staff</option>
                    <option value="IT_MANAGER">IT Manager</option>
                    <option value="ADMIN">Admin</option>
                </select>
            </div>
        </div>

        <div class="mb-6">
            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password <span class="text-red-500">*</span></label>
            <input 
                type="password" 
                id="password" 
                name="password" 
                required 
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Enter password (min. 6 characters)"
            >
        </div>

        <div class="flex gap-2">
            <button 
                type="submit" 
                class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700"
            >
                Create User
            </button>
            <a 
                href="/users" 
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
