<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
/**
 * ITAMS Application Entry Point
 */

// Start the session
session_start();

// Load environment variables from .env file
if (file_exists(__DIR__ . '/../.env')) {
    if (class_exists('Dotenv\Dotenv')) {
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
        $dotenv->load();
    }
}

// Composer autoloading
require __DIR__ . '/../vendor/autoload.php';

// Load the Router
require __DIR__ . '/../app/Router.php';

// Create a new Router instance
$router = new Router();

// Load all routes from routes/web.php
require __DIR__ . '/../routes/web.php';

// Dispatch the request
$router->dispatch();
?>