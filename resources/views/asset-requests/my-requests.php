<?php
// Fetch requester's request list
$requesterId = 1; // Example requester ID (this should ideally come from session or context)

// Sample data - replace this with actual data fetching logic
$requests = [
    ["id" => 1, "date" => "2026-01-20", "asset" => "Laptop", "quantity" => 1, "status" => "Pending"],
    ["id" => 2, "date" => "2026-01-19", "asset" => "Monitor", "quantity" => 2, "status" => "Approved"],
    ["id" => 3, "date" => "2026-01-18", "asset" => "Keyboard", "quantity" => 1, "status" => "Denied"]
];

function getStatusBadge($status) {
    switch ($status) {
        case "Pending":
            return '<span style="color: orange;">Pending</span>';
        case "Approved":
            return '<span style="color: green;">Approved</span>';
        case "Denied":
            return '<span style="color: red;">Denied</span>';
        default:
            return '<span>Status Unknown</span>';
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Requests</title>
    <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 8px 12px; border: 1px solid #ccc; text-align: left; }
        .action-buttons button { margin-right: 5px; }
    </style>
</head>
<body>
    <h1>My Asset Requests</h1>
    <table>
        <thead>
            <tr>
                <th>Request ID</th>
                <th>Date</th>
                <th>Asset Requested</th>
                <th>Quantity</th>
                <th>Current Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($requests as $request): ?>
            <tr>
                <td><?php echo $request['id']; ?></td>
                <td><?php echo $request['date']; ?></td>
                <td><?php echo $request['asset']; ?></td>
                <td><?php echo $request['quantity']; ?></td>
                <td><?php echo getStatusBadge($request['status']); ?></td>
                <td class="action-buttons">
                    <button onclick="viewDetails(<?php echo $request['id']; ?>)">View Details</button>
                    <button onclick="trackRequest(<?php echo $request['id']; ?>)">Track</button>
                    <?php if ($request['status'] === 'Pending'): ?>
                        <button onclick="cancelRequest(<?php echo $request['id']; ?>)">Cancel</button>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <script>
    function viewDetails(id) { alert('Viewing details for request ' + id); }
    function trackRequest(id) { alert('Tracking request ' + id); }
    function cancelRequest(id) { alert('Canceling request ' + id); }
    </script>
</body>
</html>
