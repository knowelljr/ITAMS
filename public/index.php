<?php
// Start the session management
session_start();

// Load environment variables from .env file
if (file_exists(__DIR__ . '/.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
}

// Composer autoloading
require __DIR__ . '/vendor/autoload.php';

// Route handling
$requestUri = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Example routing logic
switch ($requestUri) {
    case '/':
        // Instantiate the home controller
gotoHomeController();
        break;
    case '/about':
        // Instantiate the about controller
        gotoAboutController();
        break;
    default:
        // 404 Not Found response
        handle404();
        break;
}

function gotoHomeController() {
    // Logic for home controller
}

function gotoAboutController() {
    // Logic for about controller
}

function handle404() {
    http_response_code(404);
    echo '404 Not Found';
}
?>