<?php
$activePage = 'users';

// Flash messages
$error = $_SESSION['error'] ?? '';
$success = $_SESSION['success'] ?? '';
unset($_SESSION['error'], $_SESSION['success']);

$content = <<<HTML
<div class="mb-6">
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold">User Management</h1>
        <a href="/users/create" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            <span class="mr-2">+</span> Add New User
        </a>
    </div>
</div>
HTML;

if ($error) {
    $content .= <<<HTML
<div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">$error</div>
HTML;
}

if ($success) {
    $content .= <<<HTML
    <script>document.addEventListener('DOMContentLoaded',function(){showSuccess("$success");});</script>
HTML;
}

$content .= <<<HTML
<div class="bg-white rounded-lg shadow overflow-hidden" data-page-size="10" data-export-name="users">
    <div class="p-4 flex flex-col md:flex-row md:items-center md:justify-between gap-3 dt-controls">
        <input type="text" placeholder="Search users..." class="dt-search w-full md:w-1/3 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
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
                <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee #</th>
                <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mobile</th>
                <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
HTML;

if (empty($users)) {
    $content .= <<<HTML
            <tr>
                <td colspan="9" class="px-6 py-4 text-center text-gray-500">No users found</td>
            </tr>
HTML;
} else {
    foreach ($users as $user) {
        $roleColors = [
            'ADMIN' => 'bg-red-100 text-red-800',
            'IT_MANAGER' => 'bg-purple-100 text-purple-800',
            'IT_STAFF' => 'bg-blue-100 text-blue-800',
            'DEPARTMENT_MANAGER' => 'bg-yellow-100 text-yellow-500',
            'REQUESTER' => 'bg-green-100 text-green-800',
        ];

        $roleShortNames = [
            'ADMIN' => 'Admin',
            'IT_MANAGER' => 'IT Manager',
            'IT_STAFF' => 'IT Staff',
            'DEPARTMENT_MANAGER' => 'Dept. Manager',
            'REQUESTER' => 'Requester',
        ];

        $roleColor = $roleColors[$user['role']] ?? 'bg-gray-100 text-gray-800';
        $roleShortName = $roleShortNames[$user['role']] ?? 'Unknown';
        $departmentName = $user['department_name'] ?? 'N/A';
        $isCurrentUser = ($user['id'] == ($_SESSION['user_id'] ?? null));
        $isArchived = isset($user['archived']) && (int)$user['archived'] === 1;
        $rowClass = $isArchived ? 'bg-gray-100' : '';
        $statusBadge = $isArchived
            ? '<span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-200 text-gray-700">Archived</span>'
            : '<span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>';
        $archiveText = $isArchived ? 'Unarchive' : 'Archive';
        $archiveBtnClasses = $isArchived
            ? 'bg-green-600 hover:bg-green-700 border border-green-700'
            : 'bg-orange-600 hover:bg-orange-700 border border-orange-700';
        $archiveInlineStyle = $isArchived
            ? 'style="background-color:#16a34a;border:1px solid #15803d;"'
            : 'style="background-color:#ea580c;border:1px solid #c2410c;"';
        $deleteBtnClasses = $isCurrentUser ? 'bg-red-600 text-white opacity-50 cursor-not-allowed' : 'bg-red-600 text-white hover:bg-red-700';

        $content .= <<<HTML
            <tr class="$rowClass">
                <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900">{$user['id']}</td>
                <td class="px-6 py-2 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">{$user['name']}</div>
                </td>
                <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900">{$user['employee_number']}</td>
                <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500">{$user['email']}</td>
                <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500">{$user['mobile_number']}</td>
                <td class="px-6 py-2 whitespace-nowrap">
                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full $roleColor">$roleShortName</span>
                </td>
                <td class="px-6 py-2 whitespace-nowrap">$statusBadge</td>
                <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500">
        HTML;

        if (!$isCurrentUser) {
            $content .= <<<HTML
                    <div class="flex items-center gap-2">
                        <a href="/users/edit/{$user['id']}" class="h-9 w-10 inline-flex items-center justify-center bg-blue-600 text-white rounded hover:bg-blue-700" title="Edit" aria-label="Edit">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M4 20h4.586a1 1 0 00.707-.293l9.414-9.414a1 1 0 000-1.414l-3.586-3.586a1 1 0 00-1.414 0L4.293 14.707A1 1 0 004 15.414V20z" />
                            </svg>
                            <span class="sr-only">Edit</span>
                        </a>
                        <a href="/users/reset-password/{$user['id']}" class="h-9 w-10 inline-flex items-center justify-center bg-purple-600 text-white rounded hover:bg-purple-700" onclick="return confirm('This will require the user to reset their password on next login. Continue?')" title="Reset Password" aria-label="Reset Password">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.5 11a6.5 6.5 0 1113 0c0 3.866-3.634 7-6.5 7S5.5 14.866 5.5 11z" />
                            </svg>
                            <span class="sr-only">Reset Password</span>
                        </a>
                        <a href="/users/archive/{$user['id']}" class="h-9 w-10 inline-flex items-center justify-center text-white rounded $archiveBtnClasses" $archiveInlineStyle onclick="return confirm('Are you sure you want to $archiveText this user?')" title="$archiveText" aria-label="$archiveText">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <path d="M5 7a3 3 0 013-3h8a3 3 0 013 3v10a2 2 0 01-2 2H7a2 2 0 01-2-2V7z" opacity="0.85" />
                                <path d="M10 7a2 2 0 114 0v2h1.5a.5.5 0 01.5.5v2.75a3.5 3.5 0 11-7 0V9.5a.5.5 0 01.5-.5H10V7zm2 7.5a1.5 1.5 0 001.5-1.5V11h-3v2a1.5 1.5 0 001.5 1.5z" fill="currentColor" />
                            </svg>
                            <span class="sr-only">$archiveText</span>
                        </a>
                        <a href="/users/delete/{$user['id']}" class="h-9 w-10 inline-flex items-center justify-center rounded $deleteBtnClasses" onclick="return confirm('Are you sure you want to permanently delete this user?')" title="Delete" aria-label="Delete">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            <span class="sr-only">Delete</span>
                        </a>
                    </div>
            HTML;
        } else {
            $content .= <<<HTML
                    <div class="flex items-center gap-2 text-gray-400">
                        <span title="Cannot modify your own account">Edit</span>
                        <span title="Cannot modify your own account">Reset</span>
                        <span title="Cannot modify your own account">Archive</span>
                        <span title="Cannot delete your own account">Delete</span>
                    </div>
            HTML;
        }

        $content .= <<<HTML
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
