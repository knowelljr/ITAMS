<?php
$activePage = 'assets';

$error = $_SESSION['error'] ?? '';
$success = $_SESSION['success'] ?? '';
unset($_SESSION['error'], $_SESSION['success']);

$content = <<<HTML
<div class="mb-6">
    <h1 class="text-3xl font-bold mb-2">Receive Asset</h1>
    <a href="/assets" class="text-blue-600 hover:text-blue-800">&larr; Back to Assets</a>
</div>

HTML;

if ($error) {
    $content .= <<<HTML
<div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">$error</div>
HTML;
}

if ($success) {
    $content .= <<<HTML
<div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">$success</div>
HTML;
}

$content .= <<<HTML
<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase">Asset</th>
                <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase">Qty</th>
                <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase">Issued Date</th>
                <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
HTML;

if (empty($issuances)) {
    $content .= <<<HTML
            <tr>
                <td colspan="5" class="px-6 py-4 text-center text-gray-500">No issued assets to receive</td>
            </tr>
HTML;
} else {
    foreach ($issuances as $iss) {
        $issuedDate = date('M d, Y', strtotime($iss['created_at']));
        
        $content .= <<<HTML
            <tr>
                <td class="px-6 py-2 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">{$iss['asset_name']}</div>
                </td>
                <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500">{$iss['asset_code']}</td>
                <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900">{$iss['quantity']}</td>
                <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500">$issuedDate</td>
                <td class="px-6 py-2 whitespace-nowrap text-sm">
                    <button onclick="openReceiveModal({$iss['id']}, '{$iss['asset_name']}', {$iss['quantity']})" 
                        class="h-9 w-10 inline-flex items-center justify-center bg-green-600 text-white rounded hover:bg-green-700" title="Receive" aria-label="Receive">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M12 3v8m-4 4h8m-4 4v-8" />
                        </svg>
                        <span class="sr-only">Receive</span>
                    </button>
                </td>
            </tr>
HTML;
    }
}

$content .= <<<HTML
        </tbody>
    </table>
</div>

<!-- Receive Modal -->
<div id="receiveModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <h3 class="text-lg font-medium mb-4">Receive Asset</h3>
        <form action="/assets/receive/process" method="POST">
            <input type="hidden" id="modal_issuance_id" name="issuance_id">
            
            <div class="mb-4">
                <p class="text-sm text-gray-700 mb-2"><strong>Asset:</strong> <span id="modal_asset_name"></span></p>
                <p class="text-xs text-gray-500">Full asset received. Confirm condition below.</p>
            </div>

            <div class="mb-4">
                <label for="condition_status" class="block text-sm font-medium text-gray-700 mb-2">Condition <span class="text-red-500">*</span></label>
                <select id="condition_status" name="condition_status" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="GOOD">Good - No issues</option>
                    <option value="MINOR_DAMAGE">Minor Damage - Will be repaired</option>
                    <option value="MAJOR_DAMAGE">Major Damage - Will be scrapped</option>
                    <option value="UNUSABLE">Unusable - Dispose immediately</option>
                </select>
                <small class="text-gray-500 mt-1 block">Asset condition upon receipt</small>
            </div>

            <div class="mb-4">
                <label for="receipt_notes" class="block text-sm font-medium text-gray-700 mb-2">Receipt Notes</label>
                <textarea id="receipt_notes" name="receipt_notes" rows="3" placeholder="Any damage details, issues, or additional notes"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                <small class="text-gray-500 mt-1 block">Document any damage, missing parts, or concerns</small>
            </div>

            <hr class="my-4">

            <div class="mb-4">
                <h4 class="text-sm font-semibold text-gray-900 mb-3">Endorsement / Assignment</h4>
                <label class="block text-sm font-medium text-gray-700 mb-2">Endorse To <span class="text-red-500">*</span></label>
                <div class="space-y-2">
                    <label class="flex items-center">
                        <input type="radio" id="endorse_department" name="endorsement_type" value="DEPARTMENT" checked
                            class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500 cursor-pointer"
                            onchange="toggleEndorsementFields()">
                        <span class="ml-2 text-sm text-gray-700">Department</span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" id="endorse_individual" name="endorsement_type" value="INDIVIDUAL"
                            class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500 cursor-pointer"
                            onchange="toggleEndorsementFields()">
                        <span class="ml-2 text-sm text-gray-700">Individual Employee</span>
                    </label>
                </div>
                <small class="text-gray-500 mt-1 block">Choose if this asset belongs to the department or is assigned to a specific employee</small>
            </div>

            <div id="employee_field" class="mb-4 hidden">
                <label for="endorsed_employee_number" class="block text-sm font-medium text-gray-700 mb-2">Employee Number <span class="text-red-500">*</span></label>
                <input type="text" id="endorsed_employee_number" name="endorsed_employee_number" placeholder="Enter employee number"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <small class="text-gray-500 mt-1 block">The employee this asset is being assigned to</small>
            </div>

            <div class="mb-4">
                <label for="endorsement_remarks" class="block text-sm font-medium text-gray-700 mb-2">Endorsement Remarks</label>
                <textarea id="endorsement_remarks" name="endorsement_remarks" rows="2" placeholder="Additional details about the endorsement or assignment"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                <small class="text-gray-500 mt-1 block">e.g., assigned for project, department pool, personal use, etc.</small>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="flex-1 bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Confirm Receipt</button>
                <button type="button" onclick="closeReceiveModal()" class="flex-1 bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
function openReceiveModal(issuanceId, assetName, maxQty) {
    document.getElementById('modal_issuance_id').value = issuanceId;
    document.getElementById('modal_asset_name').textContent = assetName;
    document.getElementById('condition_status').value = 'GOOD';
    document.getElementById('receipt_notes').value = '';
    document.getElementById('endorse_department').checked = true;
    document.getElementById('endorsed_employee_number').value = '';
    document.getElementById('endorsement_remarks').value = '';
    toggleEndorsementFields();
    document.getElementById('receiveModal').classList.remove('hidden');
}

function closeReceiveModal() {
    document.getElementById('receiveModal').classList.add('hidden');
}

function toggleEndorsementFields() {
    const endorsementType = document.querySelector('input[name="endorsement_type"]:checked').value;
    const employeeField = document.getElementById('employee_field');
    const employeeInput = document.getElementById('endorsed_employee_number');
    
    if (endorsementType === 'INDIVIDUAL') {
        employeeField.classList.remove('hidden');
        employeeInput.required = true;
    } else {
        employeeField.classList.add('hidden');
        employeeInput.required = false;
        employeeInput.value = '';
    }
}
</script>
HTML;

include __DIR__ . '/../layout.php';
?>
