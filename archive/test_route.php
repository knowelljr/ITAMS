<?php
// Test router
require 'vendor/autoload.php';

echo "Checking root path...\n";
echo "POST /login test:\n";

// Simulate the routing
$_SESSION = [];
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/login';

// Call the router
require 'routes/web.php';

echo "Done\n";
