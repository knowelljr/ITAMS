<?php
// Asset Inventory List

$assets = [
    [
        'code' => 'A001',
        'name' => 'Asset One',
        'category' => 'Electronics',
        'serial_number' => 'SN001',
        'status' => 'Available',
        'quantity_on_hand' => 10,
        'optimum_stock_level' => 5,
        'location' => 'Warehouse 1',
    ],
    // Additional assets...
];

$categories = array_unique(array_column($assets, 'category'));
$status_options = ['Available', 'Checked Out', 'Decommissioned'];

// Function to filter assets
function filterAssets($assets, $category, $status) {
    return array_filter($assets, function($asset) use ($category, $status) {
        return (!$category || $asset['category'] === $category) && (!$status || $asset['status'] === $status);
    });
}

$filtered_assets = filterAssets($assets, $_POST['category'] ?? null, $_POST['status'] ?? null);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Asset Inventory</title>
    <style>
        .badge {
            padding: 5px;
            border-radius: 3px;
            color: white;
        }
        .Available { background-color: green; }
        .CheckedOut { background-color: blue; }
        .Decommissioned { background-color: red; }
    </style>
</head>
<body>
    <h1>Asset Inventory List</h1>

    <form method="POST" action="">
        <select name="category">
            <option value="">All Categories</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat ?>"><?= $cat ?></option>
            <?php endforeach; ?>
        </select>
        <select name="status">
            <option value="">All Statuses</option>
            <?php foreach ($status_options as $status): ?>
                <option value="<?= $status ?>"><?= $status ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Filter</button>
    </form>

    <table border="1">
        <tr>
            <th>Asset Code</th>
            <th>Name</th>
            <th>Category</th>
            <th>Serial Number</th>
            <th>Status</th>
            <th>Quantity On-Hand</th>
            <th>Optimum Stock Level</th>
            <th>Location</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($filtered_assets as $asset): ?>
            <tr>
                <td><?= $asset['code'] ?></td>
                <td><?= $asset['name'] ?></td>
                <td><?= $asset['category'] ?></td>
                <td><?= $asset['serial_number'] ?></td>
                <td><span class="badge <?= $asset['status'] ?>"><?= $asset['status'] ?></span></td>
                <td><?= $asset['quantity_on_hand'] ?></td>
                <td><?= $asset['optimum_stock_level'] ?></td>
                <td><?= $asset['location'] ?></td>
                <td>
                    <button>View</button>
                    <button>Edit</button>
                    <button>Decommission</button>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
