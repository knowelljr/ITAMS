<?php
$activePage = 'assets';

// Pre-calculate values for display
$costFormatted = number_format($asset['cost'], 2);
$statusColors = [
    'AVAILABLE' => 'bg-green-100 text-green-800',
    'ISSUED' => 'bg-blue-100 text-blue-800',
    'MAINTENANCE' => 'bg-yellow-100 text-yellow-800',
    'DECOMMISSIONED' => 'bg-red-100 text-red-800',
];
$statusColor = $statusColors[$asset['status']] ?? 'bg-gray-100 text-gray-800';

$content = <<<HTML
<div class="mb-6">
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold">Asset Details</h1>
        <div class="flex gap-2">
            <a href="/assets/print-qr/{$asset['id']}" target="_blank" class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700">Print QR Sticker</a>
            <a href="/assets" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">Back to Assets</a>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 bg-white rounded-lg shadow p-6">
        <div class="grid grid-cols-2 gap-4">
            <div class="border-b pb-3">
                <p class="text-sm text-gray-600">Asset Code</p>
                <p class="font-semibold text-lg font-mono">{$asset['asset_code']}</p>
            </div>
            <div class="border-b pb-3">
                <p class="text-sm text-gray-600">Asset Name</p>
                <p class="font-semibold text-lg">{$asset['name']}</p>
            </div>
            <div class="border-b pb-3">
                <p class="text-sm text-gray-600">Category</p>
                <p class="font-semibold">{$asset['category']}</p>
            </div>
            <div class="border-b pb-3">
                <p class="text-sm text-gray-600">Serial Number</p>
                <p class="font-semibold">{$asset['serial_number']}</p>
            </div>
            <div class="border-b pb-3">
                <p class="text-sm text-gray-600">Model</p>
                <p class="font-semibold">{$asset['model']}</p>
            </div>
            <div class="border-b pb-3">
                <p class="text-sm text-gray-600">Location</p>
                <p class="font-semibold">{$asset['location']}</p>
            </div>
            <div class="border-b pb-3">
                <p class="text-sm text-gray-600">Cost</p>
                <p class="font-semibold">SR {$costFormatted}</p>
            </div>
            <div class="border-b pb-3">
                <p class="text-sm text-gray-600">Status</p>
                <span class="px-2 py-1 inline-flex text-xs font-semibold rounded-full $statusColor">{$asset['status']}</span>
            </div>
        </div>

        <div class="mt-4 border-t pt-4">
            <p class="text-sm text-gray-600 mb-2">Details</p>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="font-semibold text-gray-700">Category:</span> {$asset['category']}
                </div>
                <div>
                    <span class="font-semibold text-gray-700">Serial Number:</span> {$asset['serial_number']}
                </div>
                <div>
                    <span class="font-semibold text-gray-700">Model:</span> {$asset['model']}
                </div>
                <div>
                    <span class="font-semibold text-gray-700">Location:</span> {$asset['location']}
                </div>
                <div>
                    <span class="font-semibold text-gray-700">Cost:</span> SR {$costFormatted}
                </div>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-4 gap-4">
            <div class="bg-blue-50 p-4 rounded text-center">
                <p class="text-sm text-gray-600">On Hand</p>
                <p class="text-2xl font-bold text-blue-600">{$asset['quantity_onhand']}</p>
            </div>
            <div class="bg-purple-50 p-4 rounded text-center">
                <p class="text-sm text-gray-600">Issued</p>
                <p class="text-2xl font-bold text-purple-600">{$asset['quantity_issued']}</p>
            </div>
            <div class="bg-green-50 p-4 rounded text-center">
                <p class="text-sm text-gray-600">Optimum Stock</p>
                <p class="text-2xl font-bold text-green-600">{$asset['optimum_stock']}</p>
            </div>
            <div class="bg-orange-50 p-4 rounded text-center">
                <p class="text-sm text-gray-600">Max Stock</p>
                <p class="text-2xl font-bold text-orange-600">{$asset['max_stock']}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6 text-center">
        <h3 class="font-bold mb-4">Asset QR Code</h3>
        <div id="qrcode" class="inline-block p-4 border-2 border-blue-600 rounded"></div>
        <p class="text-xs text-gray-600 mt-4">Scan to view asset details</p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script>
    const assetData = {
        code: '{$asset['asset_code']}',
        name: '{$asset['name']}',
        category: '{$asset['category']}',
        serial: '{$asset['serial_number']}'
    };
    
    new QRCode(document.getElementById('qrcode'), {
        text: JSON.stringify(assetData),
        width: 200,
        height: 200
    });
</script>
HTML;

include __DIR__ . '/../layout.php';
?>
