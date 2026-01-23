<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gatepass - <?php echo $issuance['gatepass_number']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.0.0/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
    <style>
        @media print {
            .no-print { display: none; }
            body { print-color-adjust: exact; -webkit-print-color-adjust: exact; }
        }
        .signature-box {
            border-bottom: 2px solid #000;
            min-height: 60px;
        }
    </style>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto bg-white shadow-lg">
        <!-- Print Button -->
        <div class="no-print p-4 bg-gray-100 flex justify-between">
            <button onclick="window.print()" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Print Gatepass</button>
            <button onclick="window.close()" class="bg-gray-600 text-white px-6 py-2 rounded hover:bg-gray-700">Close</button>
        </div>

        <!-- Gatepass Content -->
        <div class="p-8">
            <!-- Header -->
            <div class="text-center border-b-4 border-blue-600 pb-4 mb-6">
                <h1 class="text-3xl font-bold text-blue-600">IT ASSET MANAGEMENT SYSTEM</h1>
                <h2 class="text-xl font-semibold mt-2">ASSET GATEPASS</h2>
                <p class="text-lg font-mono mt-2"><?php echo $issuance['gatepass_number']; ?></p>
                <?php if ($issuance['issuance_type'] === 'UNPLANNED'): ?>
                    <div class="mt-2">
                        <span class="inline-block px-3 py-1 text-sm font-semibold rounded bg-orange-100 text-orange-800 border border-orange-300">
                            UNPLANNED ISSUANCE
                        </span>
                        <?php if ($issuance['approval_status'] === 'PENDING_APPROVAL'): ?>
                            <span class="inline-block px-3 py-1 text-sm font-semibold rounded bg-yellow-100 text-yellow-800 border border-yellow-300 ml-2">
                                PENDING IT MANAGER APPROVAL
                            </span>
                        <?php elseif ($issuance['approval_status'] === 'APPROVED'): ?>
                            <span class="inline-block px-3 py-1 text-sm font-semibold rounded bg-green-100 text-green-800 border border-green-300 ml-2">
                                ✓ APPROVED
                            </span>
                        <?php elseif ($issuance['approval_status'] === 'REJECTED'): ?>
                            <span class="inline-block px-3 py-1 text-sm font-semibold rounded bg-red-100 text-red-800 border border-red-300 ml-2">
                                ✗ REJECTED
                            </span>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <span class="inline-block px-3 py-1 text-sm font-semibold rounded bg-blue-100 text-blue-800 border border-blue-300 mt-2">
                        REQUEST-BASED ISSUANCE
                    </span>
                <?php endif; ?>
            </div>

            <div class="grid grid-cols-2 gap-6 mb-6">
                <!-- Left Column -->
                <div>
                    <h3 class="font-bold text-lg mb-3 text-blue-600">Asset Information</h3>
                    <table class="w-full text-sm">
                        <tr class="border-b">
                            <td class="py-2 font-semibold">Asset Code:</td>
                            <td class="py-2"><?php echo $issuance['asset_code']; ?></td>
                        </tr>
                        <tr class="border-b">
                            <td class="py-2 font-semibold">Asset Name:</td>
                            <td class="py-2"><?php echo $issuance['asset_name']; ?></td>
                        </tr>
                        <tr class="border-b">
                            <td class="py-2 font-semibold">Category:</td>
                            <td class="py-2"><?php echo $issuance['category']; ?></td>
                        </tr>
                        <tr class="border-b">
                            <td class="py-2 font-semibold">Serial Number:</td>
                            <td class="py-2"><?php echo $issuance['serial_number'] ?: 'N/A'; ?></td>
                        </tr>
                        <tr class="border-b">
                            <td class="py-2 font-semibold">Model:</td>
                            <td class="py-2"><?php echo $issuance['model'] ?: 'N/A'; ?></td>
                        </tr>
                        <tr class="border-b">
                            <td class="py-2 font-semibold">Quantity:</td>
                            <td class="py-2 font-bold"><?php echo $issuance['quantity_issued']; ?></td>
                        </tr>
                    </table>
                </div>

                <!-- Right Column -->
                <div>
                    <h3 class="font-bold text-lg mb-3 text-blue-600">Issuance Details</h3>
                    <table class="w-full text-sm">
                        <tr class="border-b">
                            <td class="py-2 font-semibold">Issued To:</td>
                            <td class="py-2"><?php echo $issuance['issued_to_name']; ?></td>
                        </tr>
                        <tr class="border-b">
                            <td class="py-2 font-semibold">Employee #:</td>
                            <td class="py-2"><?php echo $issuance['employee_number']; ?></td>
                        </tr>
                        <tr class="border-b">
                            <td class="py-2 font-semibold">Department:</td>
                            <td class="py-2"><?php echo $issuance['department_name']; ?></td>
                        </tr>
                        <tr class="border-b">
                            <td class="py-2 font-semibold">Contact:</td>
                            <td class="py-2"><?php echo $issuance['mobile_number'] ?: $issuance['email']; ?></td>
                        </tr>
                        <tr class="border-b">
                            <td class="py-2 font-semibold">Date Issued:</td>
                            <td class="py-2"><?php echo date('F d, Y', strtotime($issuance['created_at'])); ?></td>
                        </tr>
                        <tr class="border-b">
                            <td class="py-2 font-semibold">Expected Return:</td>
                            <td class="py-2"><?php echo $issuance['expected_return_date'] ? date('F d, Y', strtotime($issuance['expected_return_date'])) : 'N/A'; ?></td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Purpose -->
            <?php if ($issuance['purpose']): ?>
            <div class="mb-6">
                <h3 class="font-bold text-lg mb-2 text-blue-600">Purpose</h3>
                <p class="text-sm border p-3 rounded bg-gray-50"><?php echo nl2br(htmlspecialchars($issuance['purpose'])); ?></p>
            </div>
            <?php endif; ?>

            <!-- QR Code -->
            <div class="flex justify-center mb-6">
                <div>
                    <div id="qrcode" class="inline-block p-2 border-2 border-blue-600"></div>
                    <p class="text-center text-xs mt-2">Scan for verification</p>
                </div>
            </div>

            <!-- Signatures -->
            <div class="grid grid-cols-2 gap-8 mb-6">
                <div>
                    <h3 class="font-bold text-sm mb-4">Issued By:</h3>
                    <div class="signature-box mb-2"></div>
                    <p class="text-sm"><?php echo $issuance['issued_by_name']; ?></p>
                    <p class="text-xs text-gray-600">IT Staff Signature</p>
                    <p class="text-xs text-gray-600">Date: <?php echo date('M d, Y', strtotime($issuance['created_at'])); ?></p>
                </div>

                <div>
                    <h3 class="font-bold text-sm mb-4">Received By:</h3>
                    <div class="signature-box mb-2"></div>
                    <p class="text-sm"><?php echo $issuance['issued_to_name']; ?></p>
                    <p class="text-xs text-gray-600">Recipient Signature</p>
                    <p class="text-xs text-gray-600">Date: _________________</p>
                </div>
            </div>

            <!-- Security Check -->
            <div class="border-t-2 border-gray-300 pt-4">
                <h3 class="font-bold text-lg mb-4 text-center">Security Checkpoint</h3>
                <div class="grid grid-cols-2 gap-8">
                    <div>
                        <h4 class="font-bold text-sm mb-3">Gate Out:</h4>
                        <div class="signature-box mb-2"></div>
                        <p class="text-xs">Security Signature</p>
                        <p class="text-xs">Date/Time: _________________</p>
                    </div>

                    <div>
                        <h4 class="font-bold text-sm mb-3">Gate In:</h4>
                        <div class="signature-box mb-2"></div>
                        <p class="text-xs">Security Signature</p>
                        <p class="text-xs">Date/Time: _________________</p>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="mt-6 text-center text-xs text-gray-600 border-t pt-4">
                <p>This gatepass must be presented at security checkpoints</p>
                <p>Generated on <?php echo date('F d, Y h:i A'); ?></p>
            </div>
        </div>
    </div>

    <script>
        // Generate QR code
        const qrData = {
            gatepass: '<?php echo $issuance['gatepass_number']; ?>',
            asset: '<?php echo $issuance['asset_code']; ?>',
            user: '<?php echo $issuance['employee_number']; ?>',
            date: '<?php echo date('Y-m-d', strtotime($issuance['created_at'])); ?>'
        };
        
        new QRCode(document.getElementById('qrcode'), {
            text: JSON.stringify(qrData),
            width: 150,
            height: 150
        });
    </script>
</body>
</html>
