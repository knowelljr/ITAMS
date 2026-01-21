<?php

// DASHBOARD VIEW FOR REQUESTERS

// Quick Stats
$total_requests = 100; // Example data
$pending_requests = 40; // Example data
$approved_requests = 50; // Example data
$issued_requests = 10; // Example data

// Recent Requests
$recent_requests = [
    ['date' => '2026-01-20', 'status' => 'Approved', 'action' => 'View'],
    ['date' => '2026-01-19', 'status' => 'Pending', 'action' => 'View'],
    ['date' => '2026-01-18', 'status' => 'Issued', 'action' => 'View'],
];

// HTML Output
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Requester Dashboard</title>
    <link rel="stylesheet" href="/css/styles.css"> <!-- Link to your CSS file -->
</head>
<body>
    <div class="dashboard">
        <h1>Requester Dashboard</h1>
        <div class="quick-stats">
            <h2>Quick Stats</h2>
            <ul>
                <li>Total Requests: <?php echo $total_requests; ?></li>
                <li>Pending: <?php echo $pending_requests; ?></li>
                <li>Approved: <?php echo $approved_requests; ?></li>
                <li>Issued: <?php echo $issued_requests; ?></li>
            </ul>
        </div>
        <div class="request-tracker">
            <h2>Request Tracker</h2>
            <table>
                <tr>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                <?php foreach ($recent_requests as $request): ?>
                    <tr>
                        <td><?php echo $request['date']; ?></td>
                        <td class="status-<?php echo strtolower($request['status']); ?>"><?php echo $request['status']; ?></td>
                        <td><a href="#"><?php echo $request['action']; ?></a></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
        <div class="create-request">
            <h2>Create New Request</h2>
            <button><a href="/request/create">Create Request</a></button>
        </div>
    </div>
</body>
</html>
