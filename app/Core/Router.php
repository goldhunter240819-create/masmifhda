<?php

namespace App\Core;

class Router {
    private $routes = [];

    public function add($method, $path, $handler) {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler
        ];
    }

    public function run() {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // Find the base path by looking at where index.php is
        // Usually something like /public/index.php
        $scriptName = $_SERVER['SCRIPT_NAME']; 
        $basePath = str_replace('\\', '/', dirname($scriptName)); // /public
        
        // Also consider the project root (one level up from public)
        $projectRoot = str_replace('\\', '/', dirname($basePath)); // /mismifhda
        
        // Remove the longest matching base path from the URI (Case Insensitive for Windows/XAMPP)
        if (stripos($path, $scriptName) === 0) {
            $path = substr($path, strlen($scriptName));
        } elseif (stripos($path, $basePath) === 0) {
            $path = substr($path, strlen($basePath));
        } elseif (stripos($path, $projectRoot) === 0) {
            $path = substr($path, strlen($projectRoot));
        }

        if (empty($path) || $path === '') {
            $path = '/';
        }
        
        // Ensure path starts with /
        if ($path[0] !== '/') {
            $path = '/' . $path;
        }

        // Strip trailing slash if path is not exactly '/'
        if ($path !== '/' && substr($path, -1) === '/') {
            $path = rtrim($path, '/');
        }

        if (isset($_SESSION['role_id']) && in_array($_SESSION['role_id'], [1, 99]) && $_SERVER['REQUEST_METHOD'] === 'GET') {
            if ($path === '/siakad' || $path === '/siakad/') {
                $query = isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '';
                redirect('/admin/siakad/dashboard' . $query);
                exit;
            } elseif (strpos($path, '/siakad/') === 0 && strpos($path, '/admin/siakad/') === false) {
                $uri = $_SERVER['REQUEST_URI'];
                $newUri = preg_replace('#/siakad/#', '/admin/siakad/', $uri, 1);
                header('Location: ' . $newUri);
                exit;
            }
        }

        foreach ($this->routes as $route) {
            $pattern = "#^" . preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[a-zA-Z0-9_]+)', $route['path']) . "$#";
            if ($route['method'] == $method && preg_match($pattern, $path, $matches)) {
                $handler = $route['handler'];
                if (is_callable($handler)) {
                    return call_user_func_array($handler, array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY));
                }
                if (is_array($handler)) {
                    $controllerName = $handler[0];
                    $methodName = $handler[1];
                    $controller = new $controllerName();
                    return call_user_func_array([$controller, $methodName], array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY));
                }
            }
        }

        http_response_code(404);
        echo "404 Not Found";
    }
}
