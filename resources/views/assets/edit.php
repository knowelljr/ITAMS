<?php
$activePage = 'assets';

$error = $_SESSION['error'] ?? '';
unset($_SESSION['error']);

$content = <<<HTML
<div class="mb-6">
    <h1 class="text-3xl font-bold mb-2">Edit Asset</h1>
    <a href="/assets" class="text-blue-600 hover:text-blue-800">&larr; Back to Assets</a>
</div>

HTML;

if ($error) {
    $content .= <<<HTML
<div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">$error</div>
HTML;
}

$content .= <<<HTML
<div class="bg-white rounded-lg shadow p-6">
    <div id="form-validation-message" class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded hidden"></div>
    
    <form id="editAssetForm" action="/assets/update/{$asset['id']}" method="POST">
            <input type="hidden" name="id" value="{$asset['id']}">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="mb-4">
                <label for="asset_code" class="block text-sm font-medium text-gray-700 mb-2">Asset Code <span class="text-red-500">*</span></label>
                <input type="text" id="asset_code" name="asset_code" value="{$asset['asset_code']}" required 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <small class="text-gray-500">Minimum 3 characters, alphanumeric</small>
            </div>

            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Asset Name <span class="text-red-500">*</span></label>
                <input type="text" id="name" name="name" value="{$asset['name']}" required 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <small class="text-gray-500">Minimum 3 characters</small>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="mb-4">
                <label for="category" class="block text-sm font-medium text-gray-700 mb-2">Category <span class="text-red-500">*</span></label>
                <select id="category" name="category" required 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Select Category</option>
HTML;

$categories = ['Laptops', 'Desktops', 'Monitors', 'Printers', 'Network Equipment', 'Peripherals', 'Software', 'Other'];
foreach ($categories as $cat) {
    $selected = ($asset['category'] === $cat) ? 'selected' : '';
    $content .= "<option value='$cat' $selected>$cat</option>";
}

$content .= <<<HTML
                </select>
            </div>

            <div class="mb-4">
                <label for="serial_number" class="block text-sm font-medium text-gray-700 mb-2">Serial Number</label>
                <input type="text" id="serial_number" name="serial_number" value="{$asset['serial_number']}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="mb-4">
                <label for="model" class="block text-sm font-medium text-gray-700 mb-2">Model</label>
                <input type="text" id="model" name="model" value="{$asset['model']}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="mb-4">
                <label for="location" class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                <input type="text" id="location" name="location" value="{$asset['location']}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
        </div>

        <div class="mb-4">
            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
            <textarea id="description" name="description" rows="3"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{$asset['description']}</textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="mb-4">
                <label for="cost" class="block text-sm font-medium text-gray-700 mb-2">Cost</label>
                <input type="number" id="cost" name="cost" step="0.01" min="0" value="{$asset['cost']}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <small class="text-gray-500">Must be >= 0</small>
            </div>

            <div class="mb-4">
                <label for="quantity_onhand" class="block text-sm font-medium text-gray-700 mb-2">Quantity On Hand</label>
                <input type="number" id="quantity_onhand" name="quantity_onhand" min="0" value="{$asset['quantity_onhand']}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <small class="text-gray-500">Must be >= 0</small>
            </div>

            <div class="mb-4">
                <label for="optimum_stock" class="block text-sm font-medium text-gray-700 mb-2">Optimum Stock</label>
                <input type="number" id="optimum_stock" name="optimum_stock" min="0" value="{$asset['optimum_stock']}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <small class="text-gray-500">Must be >= 0</small>
            </div>

            <div class="mb-4">
                <label for="max_stock" class="block text-sm font-medium text-gray-700 mb-2">Max Stock</label>
                <input type="number" id="max_stock" name="max_stock" min="0" value="{$asset['max_stock']}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <small class="text-gray-500">Must be >= Optimum Stock</small>
            </div>
        </div>

        <div class="mb-6">
            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
            <select id="status" name="status"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
HTML;

$statuses = ['AVAILABLE', 'ISSUED', 'MAINTENANCE', 'DECOMMISSIONED'];
foreach ($statuses as $status) {
    $selected = ($asset['status'] === $status) ? 'selected' : '';
    $content .= "<option value='$status' $selected>$status</option>";
}

$content .= <<<HTML
            </select>
        </div>

        <div class="flex gap-2">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                Update Asset
            </button>
            <a href="/assets" class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400">
                Cancel
            </a>
        </div>
    </form>
</div>
HTML;

$content .= <<<'JS'
<script>
document.getElementById('editAssetForm').addEventListener('submit', function(event) {
    var isValid = true;
    var message = '';
    
    // Validate asset code
    var assetCode = document.getElementById('asset_code').value.trim();
    if (assetCode === '') {
        isValid = false;
        message += 'Asset code is required. ';
    } else if (assetCode.length < 3) {
        isValid = false;
        message += 'Asset code must be at least 3 characters. ';
    }
    
    // Validate asset name
    var assetName = document.getElementById('name').value.trim();
    if (assetName === '') {
        isValid = false;
        message += 'Asset name is required. ';
    } else if (assetName.length < 3) {
        isValid = false;
        message += 'Asset name must be at least 3 characters. ';
    }
    
    // Validate category
    var category = document.getElementById('category').value.trim();
    if (category === '') {
        isValid = false;
        message += 'Category is required. ';
    }
    
    // Validate cost
    var cost = parseFloat(document.getElementById('cost').value);
    if (isNaN(cost) || cost < 0) {
        isValid = false;
        message += 'Cost must be a valid number >= 0. ';
    }
    
    // Validate quantities
    var qtyOnHand = parseInt(document.getElementById('quantity_onhand').value);
    if (isNaN(qtyOnHand) || qtyOnHand < 0) {
        isValid = false;
        message += 'Quantity on hand must be >= 0. ';
    }
    
    var optimumStock = parseInt(document.getElementById('optimum_stock').value);
    if (isNaN(optimumStock) || optimumStock < 0) {
        isValid = false;
        message += 'Optimum stock must be >= 0. ';
    }
    
    var maxStock = parseInt(document.getElementById('max_stock').value);
    if (isNaN(maxStock) || maxStock < 0) {
        isValid = false;
        message += 'Max stock must be >= 0. ';
    } else if (maxStock < optimumStock) {
        isValid = false;
        message += 'Max stock must be >= optimum stock. ';
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
JS;

include __DIR__ . '/../layout.php';
?>
