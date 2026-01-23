<?php
$activePage = 'departments';

// Get messages
$error = $_SESSION['error'] ?? '';
$success = $_SESSION['success'] ?? '';
unset($_SESSION['error'], $_SESSION['success']);

// Prepare content
$content = <<<HTML
<div class="mb-6">
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold">Department Management</h1>
        <a href="/departments/create" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            <span class="mr-2">+</span> Add New Department
        </a>
    </div>
</div>

HTML;

// Show messages
if ($error) {
    $content .= <<<HTML
<div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
    $error
</div>
HTML;
}

if ($success) {
    $content .= <<<HTML
<div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
    $success
</div>
HTML;
}

// Departments table
$content .= <<<HTML
<div class="bg-white rounded-lg shadow overflow-hidden" data-page-size="10" data-export-name="departments">
    <div class="p-4 flex flex-col md:flex-row md:items-center md:justify-between gap-3 dt-controls">
        <input type="text" placeholder="Search departments..." class="dt-search w-full md:w-1/3 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
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
    <table class="min-w-full table-auto data-table">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department Name</th>
                <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
HTML;

if (empty($departments)) {
    $content .= <<<HTML
            <tr>
                <td colspan="5" class="px-6 py-4 text-center text-gray-500">No departments found</td>
            </tr>
HTML;
} else {
    foreach ($departments as $dept) {
        $createdDate = date('M d, Y', strtotime($dept['created_at']));
        
        $content .= <<<HTML
            <tr>
                <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900">{$dept['id']}</td>
                <td class="px-6 py-2 whitespace-nowrap"><span class="px-2 py-1 text-xs font-semibold rounded bg-blue-100 text-blue-800">{$dept['department_code']}</span></td>
                <td class="px-6 py-2 whitespace-nowrap text-sm font-medium text-gray-900">{$dept['department_name']}</td>
                <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500">$createdDate</td>
                <td class="px-6 py-2 whitespace-nowrap text-sm font-medium">
                    <div class="flex items-center gap-2">
                        <a href="/departments/edit/{$dept['id']}" class="h-9 w-10 inline-flex items-center justify-center bg-blue-600 text-white rounded hover:bg-blue-700" title="Edit" aria-label="Edit">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M4 20h4.586a1 1 0 00.707-.293l9.414-9.414a1 1 0 000-1.414l-3.586-3.586a1 1 0 00-1.414 0L4.293 14.707A1 1 0 004 15.414V20z" />
                            </svg>
                            <span class="sr-only">Edit</span>
                        </a>
                        <a href="/departments/delete/{$dept['id']}" onclick="return confirm('Are you sure you want to delete this department?')" class="h-9 w-10 inline-flex items-center justify-center bg-red-600 text-white rounded hover:bg-red-700" title="Delete" aria-label="Delete">
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

include __DIR__ . '/../../layout.php';
?>
