<?php
/**
 * Router class for handling URL routing
 */
class Router
{
    private $routes = [];
    private $requestUri;
    private $requestMethod;
    private $basePath;

    public function __construct()
    {
        $this->basePath = realpath(__DIR__ . '/..');
        $this->requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $this->requestMethod = $_SERVER['REQUEST_METHOD'];
    }

    /**
     * Register a GET route
     */
    public function get($path, $callback)
    {
        $this->registerRoute('GET', $path, $callback);
    }

    /**
     * Register a POST route
     */
    public function post($path, $callback)
    {
        $this->registerRoute('POST', $path, $callback);
    }

    /**
     * Register a route
     */
    private function registerRoute($method, $path, $callback)
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'callback' => $callback
        ];
    }

    /**
     * Dispatch the request to the appropriate route
     */
    public function dispatch()
    {
        foreach ($this->routes as $route) {
            if ($route['method'] === $this->requestMethod) {
                $params = $this->matchPath($route['path']);
                if ($params !== false) {
                    try {
                        call_user_func_array($route['callback'], $params);
                    } catch (Exception $e) {
                        http_response_code(500);
                        echo "Error: " . htmlspecialchars($e->getMessage());
                    }
                    return;
                }
            }
        }

        // No route found
        http_response_code(404);
        echo '404 Not Found - Route:  ' . htmlspecialchars($this->requestUri);
    }

    /**
     * Check if the request path matches the route path
     * Returns array of parameters if matched, false otherwise
     */
    private function matchPath($routePath)
    {
        // Normalize paths
        $routePath = rtrim($routePath, '/') ?: '/';
        $requestUri = rtrim($this->requestUri, '/') ?: '/';

        // Check for exact match
        if ($routePath === $requestUri) {
            return [];
        }

        // Check for dynamic segments
        $routePattern = preg_replace('/:([\w]+)/', '([^/]+)', $routePath);
        $routePattern = '#^' . $routePattern . '$#';

        if (preg_match($routePattern, $requestUri, $matches)) {
            array_shift($matches); // Remove full match
            return $matches;
        }

        return false;
    }
}
?>