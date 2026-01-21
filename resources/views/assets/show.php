<?php

// Asset details page

// Fetch asset information from database
// Assuming $asset is an associative array containing asset details
// Example Assets Details  
$asset = [
    'code' => 'ASSET-001',
    'name' => 'Laptop',
    'category' => 'Electronics',
    'serial' => 'SN123456',
    'model' => 'Dell XPS 15',
    'location' => 'Office A',
    'cost' => '$1500',
    'stock_levels' => '5',
    'status' => 'Active',
    'issuance_history' => [
        ['date' => '2022-01-01', 'issued_to' => 'John Doe'],
        ['date' => '2023-01-01', 'issued_to' => 'Jane Smith'],
    ]
];

?>
<html>
<head>
    <title>Asset Details</title>
</head>
<body>
    <h1>Asset Details</h1>
    <p><strong>Code:</strong> <?php echo $asset['code']; ?></p>
    <p><strong>Name:</strong> <?php echo $asset['name']; ?></p>
    <p><strong>Category:</strong> <?php echo $asset['category']; ?></p>
    <p><strong>Serial:</strong> <?php echo $asset['serial']; ?></p>
    <p><strong>Model:</strong> <?php echo $asset['model']; ?></p>
    <p><strong>Location:</strong> <?php echo $asset['location']; ?></p>
    <p><strong>Cost:</strong> <?php echo $asset['cost']; ?></p>
    <p><strong>Stock Levels:</strong> <?php echo $asset['stock_levels']; ?></p>
    <p><strong>Status:</strong> <?php echo $asset['status']; ?></p>
    <h2>Issuance History</h2>
    <ul>
        <?php foreach ($asset['issuance_history'] as $issue) { ?>
            <li><?php echo $issue['date'] . ' - ' . $issue['issued_to']; ?></li>
        <?php } ?>
    </ul>
    <button onclick="window.location.href='edit.php?id=<?php echo $asset['code']; ?>'">Edit Asset</button>
    <button onclick="window.location.href='decommission.php?id=<?php echo $asset['code']; ?>'">Decommission Asset</button>
</body>
</html>