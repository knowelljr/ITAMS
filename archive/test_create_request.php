<?php
session_start();

// Simulate a logged-in user
$_SESSION['user_id'] = 1;
$_SESSION['role'] = 'REQUESTER';

// Simulate POST request
$_POST = [
    'asset_name' => 'Test Laptop',
    'asset_category' => 'Electronics',
    'quantity' => 1,
    'date_needed' => '2026-02-15',
    'purpose' => 'Testing asset request creation with fixed schema'
];

require_once __DIR__ . '/app/Controllers/AssetRequestController.php';
require_once __DIR__ . '/app/Database/Connection.php';

$controller = new AssetRequestController();
$controller->store();

// If we get here, there was a redirect
echo "Request created! Check the session messages.";
?>
