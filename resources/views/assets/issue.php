<?php
$activePage = 'assets';

// Set default empty arrays if not provided by controller
$assets = $assets ?? [];

$error = $_SESSION['error'] ?? '';
$success = $_SESSION['success'] ?? '';
unset($_SESSION['error'], $_SESSION['success']);

$content = <<<HTML
<div class="mb-6">
    <h1 class="text-3xl font-bold mb-2">Issue Asset</h1>
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
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <h2 class="text-xl font-semibold mb-4">Select Issuance Type</h2>
    <div class="flex gap-4">
        <label class="flex items-center p-4 border-2 border-blue-600 rounded-lg cursor-pointer bg-blue-50">
            <input type="radio" name="issuance_type_selector" value="REQUEST_BASED" checked onchange="toggleIssuanceType(this.value)"
                class="mr-3 h-4 w-4 text-blue-600">
            <div>
                <span class="font-semibold text-blue-900">Request-Based Issuance</span>
                <p class="text-sm text-gray-600 mt-1">Process approved asset requests</p>
            </div>
        </label>
        <label class="flex items-center p-4 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-orange-500 hover:bg-orange-50">
            <input type="radio" name="issuance_type_selector" value="UNPLANNED" onchange="toggleIssuanceType(this.value)"
                class="mr-3 h-4 w-4 text-orange-600">
            <div>
                <span class="font-semibold text-orange-900">Unplanned Issuance</span>
                <p class="text-sm text-gray-600 mt-1">Emergency issuance (pending IT Manager approval)</p>
            </div>
        </label>
    </div>
</div>

<div id="request_lookup_panel" class="bg-white rounded-lg shadow p-6 mb-6">
    <h2 class="text-xl font-semibold mb-4">Lookup Asset Request</h2>
    <div class="flex gap-4">
        <div class="flex-1">
            <label for="request_number_input" class="block text-sm font-medium text-gray-700 mb-2">Request Reference Number</label>
            <input type="text" id="request_number_input" 
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Enter request number (e.g., REQ20260122001)">
        </div>
        <div class="flex items-end">
            <button type="button" onclick="lookupRequest()" 
                class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                Lookup Request
            </button>
        </div>
    </div>
    
    <div id="request_details" class="mt-4 hidden">
        <div class="border-t pt-4">
            <h3 class="font-semibold mb-2 text-green-700">Request Found</h3>
            <div id="request_info" class="grid grid-cols-2 gap-4 text-sm"></div>
            <div id="approval_warning" class="mt-3 hidden p-3 bg-red-50 border border-red-300 rounded text-red-700">
                <strong>Warning:</strong> This request is not fully approved. Cannot proceed with issuance.
            </div>
        </div>
    </div>
    
    <div id="request_error" class="mt-4 hidden p-3 bg-red-100 border border-red-400 text-red-700 rounded"></div>
</div>

<div class="bg-white rounded-lg shadow p-6 mb-6">
    <h2 class="text-xl font-semibold mb-4">
        <span id="form_title">Issue Asset Form</span>
        <span id="type_badge" class="ml-2 px-2 py-1 text-xs rounded bg-blue-100 text-blue-800">Request-Based</span>
    </h2>
    <form action="/assets/issue/process" method="POST" id="issuance_form">
        <input type="hidden" id="request_id" name="request_id">
        <input type="hidden" id="request_number" name="request_number">
        <input type="hidden" id="issuance_type" name="issuance_type" value="REQUEST_BASED">
            
        <div id="requester_info" class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded hidden">
            <p class="text-sm text-blue-800"><strong>Requester:</strong> <span id="display_requester"></span></p>
        </div>

        <div class="mb-4">
            <label for="store_id" class="block text-sm font-medium text-gray-700 mb-2">Issue From Store <span class="text-red-500">*</span></label>
            <select id="store_id" name="store_id" required onchange="loadStoreInventory()"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Select Store</option>
HTML;

foreach ($stores as $store) {
    $storeId = $store['id'];
    $storeName = htmlspecialchars($store['store_name']);
    $location = htmlspecialchars($store['location'] ?? 'N/A');
    $content .= "<option value='$storeId'>$storeName ($location)</option>";
}

$content .= <<<HTML
            </select>
            <small class="text-gray-500">Choose which store inventory to issue from</small>
        </div>
            
        <div class="mb-4">
            <label for="asset_id" class="block text-sm font-medium text-gray-700 mb-2">Asset <span class="text-red-500">*</span></label>
            <select id="asset_id" name="asset_id" required 
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Select store first to see available assets</option>
            </select>
            <small class="text-gray-500">Showing only assets available in selected store</small>
        </div>

        <div class="mb-4">
            <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">Quantity <span class="text-red-500">*</span></label>
            <input type="number" id="quantity" name="quantity" min="1" value="1" required 
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <button type="submit" class="w-full bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
            Issue Asset
        </button>
    </form>
</div>
HTML;

$content .= <<<SCRIPT
<script>
let currentRequestData = null;

function toggleIssuanceType(type) {
    const lookupPanel = document.getElementById('request_lookup_panel');
    const typeBadge = document.getElementById('type_badge');
    const issuanceTypeField = document.getElementById('issuance_type');
    const requestIdField = document.getElementById('request_id');
    const requestNumberField = document.getElementById('request_number');
    const requesterInfo = document.getElementById('requester_info');
    
    issuanceTypeField.value = type;
    currentRequestData = null;
    
    if (type === 'REQUEST_BASED') {
        lookupPanel.style.display = 'block';
        typeBadge.textContent = 'Request-Based';
        typeBadge.className = 'ml-2 px-2 py-1 text-xs rounded bg-blue-100 text-blue-800';
        
        // Clear fields
        document.getElementById('request_details').classList.add('hidden');
        document.getElementById('request_error').classList.add('hidden');
        requesterInfo.classList.add('hidden');
        document.getElementById('asset_id').value = '';
        document.getElementById('quantity').value = '1';
        document.getElementById('issuance_form').querySelector('button[type="submit"]').disabled = false;
    } else {
        lookupPanel.style.display = 'none';
        typeBadge.textContent = 'Unplanned (Emergency)';
        typeBadge.className = 'ml-2 px-2 py-1 text-xs rounded bg-orange-100 text-orange-800';
        
        // Clear request fields
        requestIdField.value = '';
        requestNumberField.value = '';
        document.getElementById('request_number_input').value = '';
        requesterInfo.classList.add('hidden');
        
        // Clear form fields
        document.getElementById('asset_id').value = '';
        document.getElementById('quantity').value = '1';
        document.getElementById('issuance_form').querySelector('button[type="submit"]').disabled = false;
    }
}

function lookupRequest() {
    const requestNumber = document.getElementById('request_number_input').value.trim();
    const detailsDiv = document.getElementById('request_details');
    const errorDiv = document.getElementById('request_error');
    const infoDiv = document.getElementById('request_info');
    const warningDiv = document.getElementById('approval_warning');
    
    if (!requestNumber) {
        errorDiv.textContent = 'Please enter a request number';
        errorDiv.classList.remove('hidden');
        detailsDiv.classList.add('hidden');
        return;
    }
    
    // Fetch request details
    fetch('/api/asset-requests/get-by-number?request_number=' + encodeURIComponent(requestNumber))
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                errorDiv.textContent = data.error;
                errorDiv.classList.remove('hidden');
                detailsDiv.classList.add('hidden');
                currentRequestData = null;
            } else {
                const req = data.request;
                currentRequestData = req;
                
                // Display request details
                infoDiv.innerHTML = \`
                    <div><strong>Request Number:</strong> \${req.request_number}</div>
                    <div><strong>Requester:</strong> \${req.requester_name} (\${req.employee_number})</div>
                    <div><strong>Department:</strong> \${req.department_name}</div>
                    <div><strong>Asset:</strong> \${req.asset_name}</div>
                    <div><strong>Category:</strong> \${req.asset_category}</div>
                    <div><strong>Quantity:</strong> \${req.quantity_requested}</div>
                    <div><strong>Purpose:</strong> \${req.reason || 'N/A'}</div>
                    <div><strong>Date Needed:</strong> \${req.date_needed || 'N/A'}</div>
                    <div><strong>Dept Mgr Approval:</strong> <span class="font-semibold \${req.department_manager_approval_status === 'APPROVED' ? 'text-green-600' : 'text-yellow-600'}">\${req.department_manager_approval_status}</span></div>
                    <div><strong>IT Mgr Approval:</strong> <span class="font-semibold \${req.it_manager_approval_status === 'APPROVED' ? 'text-green-600' : 'text-yellow-600'}">\${req.it_manager_approval_status}</span></div>
                \`;
                
                errorDiv.classList.add('hidden');
                detailsDiv.classList.remove('hidden');
                
                // Check if fully approved
                if (data.isFullyApproved) {
                    warningDiv.classList.add('hidden');
                    populateFormFromRequest(req);
                } else {
                    warningDiv.classList.remove('hidden');
                    document.getElementById('issuance_form').querySelector('button[type="submit"]').disabled = true;
                }
            }
        })
        .catch(error => {
            errorDiv.textContent = 'Failed to fetch request: ' + error.message;
            errorDiv.classList.remove('hidden');
            detailsDiv.classList.add('hidden');
            currentRequestData = null;
        });
}

function populateFormFromRequest(request) {
    document.getElementById('request_id').value = request.id;
    document.getElementById('request_number').value = request.request_number;
    document.getElementById('quantity').value = request.quantity_requested;
    
    // Show requester info
    const requesterInfo = document.getElementById('requester_info');
    document.getElementById('display_requester').textContent = \`\${request.requester_name} (\${request.employee_number}) - \${request.department_name}\`;
    requesterInfo.classList.remove('hidden');
    
    // Find matching asset by name/category if possible
    const assetSelect = document.getElementById('asset_id');
    for (let i = 0; i < assetSelect.options.length; i++) {
        const option = assetSelect.options[i];
        if (option.text.includes(request.asset_name)) {
            assetSelect.value = option.value;
            break;
        }
    }
    
    // Enable submit button
    document.getElementById('issuance_form').querySelector('button[type="submit"]').disabled = false;
}

// Load inventory from selected store
function loadStoreInventory() {
    const storeId = document.getElementById('store_id').value;
    const assetSelect = document.getElementById('asset_id');
    
    if (!storeId) {
        assetSelect.innerHTML = '<option value="">Select store first to see available assets</option>';
        return;
    }
    
    // Fetch store inventory
    fetch('/api/stores/' + storeId + '/inventory')
        .then(response => response.json())
        .then(data => {
            if (data.inventory && data.inventory.length > 0) {
                let html = '<option value="">Select Asset</option>';
                data.inventory.forEach(item => {
                    const assetId = item.asset_id;
                    const assetCode = item.asset_code;
                    const assetName = item.asset_name;
                    const available = item.quantity_available;
                    const category = item.category;
                    
                    if (available > 0) {
                        html += \`<option value='\${assetId}' data-available='\${available}'>\${assetCode} - \${assetName} (Category: \${category}, Available: \${available})</option>\`;
                    }
                });
                assetSelect.innerHTML = html;
                
                if (data.inventory.filter(i => i.quantity_available > 0).length === 0) {
                    assetSelect.innerHTML = '<option value="">No assets available in this store</option>';
                }
            } else {
                assetSelect.innerHTML = '<option value="">No assets in selected store</option>';
            }
        })
        .catch(error => {
            console.error('Error loading store inventory:', error);
            assetSelect.innerHTML = '<option value="">Error loading inventory</option>';
        });
}

// Validate form before submission
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('issuance_form');
    form.addEventListener('submit', function(e) {
        const storeId = document.getElementById('store_id').value;
        const issuanceType = document.getElementById('issuance_type').value;
        const requestNumber = document.getElementById('request_number').value;
        
        if (!storeId) {
            e.preventDefault();
            alert('Please select a store');
            return false;
        }
        
        if (issuanceType === 'REQUEST_BASED' && !requestNumber) {
            e.preventDefault();
            alert('Please lookup and validate a request before submitting');
            return false;
        }
    });
});
</script>
SCRIPT;

?>
<?php include __DIR__ . '/../layout.php'; ?>
