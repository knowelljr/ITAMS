<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code - <?php echo $asset['asset_code']; ?></title>
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
    <style>
        @media print {
            .no-print { display: none; }
            body { margin: 0; }
        }
        .sticker {
            width: 10cm;
            height: 6cm;
            border: 2px dashed #ccc;
            padding: 1cm;
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="no-print" style="padding: 20px; background: #f3f4f6;">
        <button onclick="window.print()" style="background: #2563eb; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">Print QR Sticker</button>
        <button onclick="window.close()" style="background: #6b7280; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; margin-left: 10px;">Close</button>
    </div>

    <div class="sticker" style="text-align: center; font-family: Arial, sans-serif;">
        <h2 style="margin: 0 0 10px 0; font-size: 18px;">IT Asset</h2>
        <div id="qrcode" style="display: inline-block; margin: 10px 0;"></div>
        <div style="margin-top: 10px;">
            <p style="margin: 5px 0; font-weight: bold; font-size: 16px; font-family: monospace;"><?php echo $asset['asset_code']; ?></p>
            <p style="margin: 5px 0; font-size: 14px;"><?php echo $asset['name']; ?></p>
            <p style="margin: 5px 0; font-size: 12px; color: #666;"><?php echo $asset['category']; ?></p>
        </div>
    </div>

    <script>
        const assetData = {
            code: '<?php echo $asset['asset_code']; ?>',
            name: '<?php echo $asset['name']; ?>',
            category: '<?php echo $asset['category']; ?>',
            serial: '<?php echo $asset['serial_number']; ?>'
        };
        
        new QRCode(document.getElementById('qrcode'), {
            text: JSON.stringify(assetData),
            width: 150,
            height: 150
        });
    </script>
</body>
</html>
