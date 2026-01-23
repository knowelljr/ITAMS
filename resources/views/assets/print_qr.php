<?php
// Basic QR Print View Template
// Variables: $asset (array with 'asset_code' and 'name')
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Print Asset QR Code</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; height: 100vh; }
        .qr-center-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
        .qr-container { text-align: center; }
                #qrcode {
                    display: inline-block;
                    border: 3px solid #222;
                    border-radius: 12px;
                    padding: 16px;
                    background: #fff;
                    margin-top: 18px;
                }
        .asset-info { margin-top: 20px; font-size: 1.2em; }
        .print-btn { margin-top: 30px; }
    </style>
</head>
<body>
    <div class="qr-center-wrapper">
        <div class="qr-container">
            <h2>Asset QR Code</h2>
            <div id="qrcode"></div>
            <div class="asset-info">
                <strong>Asset Code:</strong> <?= htmlspecialchars($asset['asset_code']) ?><br>
                <strong>Name:</strong> <?= htmlspecialchars($asset['name']) ?>
            </div>
            <div class="print-btn">
                <button onclick="window.print()">Print</button>
            </div>
        </div>
    </div>
    <!-- QRCode.js library (client-side QR code generator) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script>
        new QRCode(document.getElementById("qrcode"), {
            text: "<?= htmlspecialchars($asset['asset_code']) ?>",
            width: 200,
            height: 200
        });
    </script>
</body>
</html>
