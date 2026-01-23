<?php
$activePage = 'asset-requests';

$error = $_SESSION['error'] ?? '';
$success = $_SESSION['success'] ?? '';
unset($_SESSION['error'], $_SESSION['success']);

$content = <<<HTML
<div class="mb-6">
    <h1 class="text-3xl font-bold mb-2">Create Asset Request</h1>
    <p class="text-gray-600">Submit a new asset request for approval</p>
</div>

HTML;

if ($error) {
    $content .= <<<HTML
<div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">$error</div>
HTML;
}

$content .= <<<HTML
<div class="bg-white rounded-lg shadow p-6 max-w-2xl">
    <div id="form-validation-message" class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded hidden"></div>
    
    <form id="requestForm" action="/asset-requests/create" method="POST">
        <div class="grid grid-cols-2 gap-4">
            <div class="mb-4">
                <label for="asset_name" class="block text-sm font-medium text-gray-700 mb-2">Asset Name <span class="text-red-500">*</span></label>
                <input type="text" id="asset_name" name="asset_name" required 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="e.g., Dell Laptop, iPhone 13, Wireless Mouse">
                <small class="text-gray-500">Minimum 3 characters</small>
            </div>

            <div class="mb-4">
                <label for="asset_category" class="block text-sm font-medium text-gray-700 mb-2">Category <span class="text-red-500">*</span></label>
                <select id="asset_category" name="asset_category" required 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Select Category</option>
                    <option value="Laptop">Laptop</option>
                    <option value="Desktop">Desktop</option>
                    <option value="Monitor">Monitor</option>
                    <option value="Printer">Printer</option>
                    <option value="Phone">Phone</option>
                    <option value="Tablet">Tablet</option>
                    <option value="Accessories">Accessories</option>
                    <option value="Networking">Networking Equipment</option>
                    <option value="Storage">Storage Device</option>
                    <option value="Software">Software</option>
                    <option value="Other">Other</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div class="mb-4">
                <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">Quantity <span class="text-red-500">*</span></label>
                <input type="number" id="quantity" name="quantity" min="1" value="1" required 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <small class="text-gray-500">Must be greater than 0</small>
            </div>

            <div class="mb-4">
                <label for="date_needed" class="block text-sm font-medium text-gray-700 mb-2">Date Needed</label>
                <input type="date" id="date_needed" name="date_needed"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <small class="text-gray-500">Must be today or future date</small>
            </div>
        </div>

        <div class="mb-4">
            <label for="purpose" class="block text-sm font-medium text-gray-700 mb-2">Purpose / Reason <span class="text-red-500">*</span></label>
            <textarea id="purpose" name="purpose" rows="4" required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Describe why you need this asset and how it will be used"></textarea>
            <small class="text-gray-500">Minimum 10 characters</small>
        </div>

        <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded">
            <h3 class="font-semibold text-blue-900 mb-2">Approval Process</h3>
            <ol class="list-decimal list-inside text-sm text-blue-800 space-y-1">
                <li>Your request will be sent to your Department Manager for approval</li>
                <li>Once approved by Department Manager, it will be sent to IT Manager</li>
                <li>After both approvals, IT Staff can issue the asset to you</li>
            </ol>
        </div>

        <div class="flex justify-between">
            <a href="/dashboard" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</a>
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                Submit Request
            </button>
        </div>
    </form>
</div>

<script>
document.getElementById('requestForm').addEventListener('submit', function(event) {
    var isValid = true;
    var message = '';
    
    // Validate asset name
    var assetName = document.getElementById('asset_name').value.trim();
    if (assetName === '') {
        isValid = false;
        message += 'Asset name is required. ';
    } else if (assetName.length < 3) {
        isValid = false;
        message += 'Asset name must be at least 3 characters. ';
    }
    
    // Validate category
    var category = document.getElementById('asset_category').value.trim();
    if (category === '') {
        isValid = false;
        message += 'Category is required. ';
    }
    
    // Validate quantity
    var quantity = parseInt(document.getElementById('quantity').value);
    if (isNaN(quantity) || quantity <= 0) {
        isValid = false;
        message += 'Quantity must be greater than 0. ';
    }
    
    // Validate date needed (must be today or future)
    var dateNeeded = document.getElementById('date_needed').value;
    if (dateNeeded) {
        var selectedDate = new Date(dateNeeded);
        var today = new Date();
        today.setHours(0, 0, 0, 0);
        if (selectedDate < today) {
            isValid = false;
            message += 'Date needed must be today or in the future. ';
        }
    }
    
    // Validate purpose
    var purpose = document.getElementById('purpose').value.trim();
    if (purpose === '') {
        isValid = false;
        message += 'Purpose is required. ';
    } else if (purpose.length < 10) {
        isValid = false;
        message += 'Purpose must be at least 10 characters. ';
    }
    
    if (!isValid) {
        event.preventDefault();
        var msgDiv = document.getElementById('form-validation-message');
        msgDiv.innerText = message;
        msgDiv.classList.remove('hidden');
        msgDiv.scrollIntoView({ behavior: 'smooth' });
    }
});
</script>
HTML;

include __DIR__ . '/../layout.php';
?>
