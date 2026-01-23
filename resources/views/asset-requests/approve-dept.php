<?php
$activePage = 'approvals';

$error = $_SESSION['error'] ?? '';
$success = $_SESSION['success'] ?? '';
unset($_SESSION['error'], $_SESSION['success']);

$content = <<<HTML
<div class="mb-6">
    <h1 class="text-3xl font-bold mb-2">Department Manager Approvals</h1>
    <p class="text-gray-600">Review and approve asset requests from your department</p>
</div>

HTML;

if ($error) {
    $content .= <<<HTML
<div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">$error</div>
HTML;
}

$content .= <<<HTML
<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200 data-table">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Request #</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Requester</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Asset</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Needed</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Requested</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
HTML;

if (empty($requests)) {
    $content .= <<<HTML
            <tr>
                <td colspan="7" class="px-6 py-4 text-center text-gray-500">No pending approvals</td>
            </tr>
HTML;
} else {
    foreach ($requests as $req) {
        $dateRequested = date('M d, Y', strtotime($req['created_at']));
        $dateNeeded = $req['date_needed'] ? date('M d, Y', strtotime($req['date_needed'])) : 'N/A';
        $content .= <<<HTML
            <tr>
                <td class="px-6 py-2 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">{$req['request_number']}</div>
                </td>
                <td class="px-6 py-2">
                    <div class="text-sm font-medium text-gray-900">{$req['requester_name']}</div>
                    <div class="text-sm text-gray-500">{$req['employee_number']}</div>
                </td>
                <td class="px-6 py-2">
                    <div class="text-sm font-medium text-gray-900">{$req['asset_name']}</div>
                    <div class="text-sm text-gray-500">{$req['asset_category']}</div>
                </td>
                <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900">{$req['quantity_requested']}</td>
                <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900">$dateNeeded</td>
                <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900">$dateRequested</td>
                <td class="px-6 py-2 whitespace-nowrap text-sm font-medium">
                    <div class="flex items-center gap-2">
                        <button onclick="showApprovalModal({$req['id']}, 'approve', '{$req['request_number']}')"
                            class="px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700" title="Approve" aria-label="Approve">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="sr-only">Approve</span>
                        </button>
                        <button onclick="showApprovalModal({$req['id']}, 'reject', '{$req['request_number']}')"
                            class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700" title="Reject" aria-label="Reject">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            <span class="sr-only">Reject</span>
                        </button>
                    </div>
                </td>
            </tr>
HTML;
    }
}

$content .= <<<HTML
        </tbody>
    </table>
</div>

<!-- Approval Modal -->
<div id="approvalModal" class="hidden fixed z-10 inset-0 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
        <div class="relative bg-white rounded-lg max-w-lg w-full p-6">
            <h3 id="modalTitle" class="text-lg font-medium mb-4">Confirm Action</h3>
            <p id="modalRequestNumber" class="text-sm text-gray-600 mb-4"></p>
            <form id="approvalForm" action="/asset-requests/dept-approvals/process" method="POST">
                <input type="hidden" id="modal_request_id" name="request_id">
                <input type="hidden" id="modal_action" name="action">
                
                <div class="mb-4">
                    <label for="modal_remarks" class="block text-sm font-medium text-gray-700 mb-2">Remarks</label>
                    <textarea id="modal_remarks" name="remarks" rows="3"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Enter approval/rejection remarks (optional)"></textarea>
                </div>
                
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeApprovalModal()"
                        class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" id="modalSubmitBtn"
                        class="px-4 py-2 text-white rounded-lg">
                        Confirm
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showApprovalModal(requestId, action, requestNumber) {
    const modal = document.getElementById('approvalModal');
    const title = document.getElementById('modalTitle');
    const requestNumDisplay = document.getElementById('modalRequestNumber');
    const submitBtn = document.getElementById('modalSubmitBtn');
    
    document.getElementById('modal_request_id').value = requestId;
    document.getElementById('modal_action').value = action;
    document.getElementById('modal_remarks').value = '';
    
    requestNumDisplay.textContent = 'Request: ' + requestNumber;
    
    if (action === 'approve') {
        title.textContent = 'Approve Request';
        submitBtn.textContent = 'Approve';
        submitBtn.className = 'px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700';
    } else {
        title.textContent = 'Reject Request';
        submitBtn.textContent = 'Reject';
        submitBtn.className = 'px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700';
    }
    
    modal.classList.remove('hidden');
}

function closeApprovalModal() {
    document.getElementById('approvalModal').classList.add('hidden');
}
</script>
HTML;

include __DIR__ . '/../layout.php';
?>
