<?php

session_start();

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Route handling
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$request_method = $_SERVER['REQUEST_METHOD'];

// Simple routing
$routes = include __DIR__ . '/../routes/web.php';

$route_found = false;

foreach ($routes as $route => $action) {
    $pattern = preg_replace('/\{(\w+)\}/', '(?P<$1>\d+)', $route);
    $pattern = '#^' . $pattern . '$#';
    
    if (preg_match($pattern, $request_uri, $matches)) {
        $route_found = true;
        list($controller, $method) = explode('@', $action);
        
        $controller_class = 'App\\Controllers\\' . $controller;
        $controller_instance = new $controller_class();
        
        $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
        call_user_func_array([$controller_instance, $method], $params);
        break;
    }
}

if (!$route_found) {
    http_response_code(404);
    echo "404 - Page Not Found";
}