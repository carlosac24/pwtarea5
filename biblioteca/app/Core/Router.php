<?php

class Router
{
    protected $routes = [];

    public function add($method, $path, $controller, $action)
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'controller' => $controller,
            'action' => $action
        ];
    }

    public function dispatch($uri, $method)
    {
        // Remove query string
        $uri = parse_url($uri, PHP_URL_PATH);
        
        // Remove base path if exists (e.g. /biblioteca/public)
        $scriptName = dirname($_SERVER['SCRIPT_NAME']);
        if ($scriptName !== '/' && strpos($uri, $scriptName) === 0) {
            $uri = substr($uri, strlen($scriptName));
        }
        
        $uri = trim($uri, '/');
        if ($uri === '') {
            $uri = 'home'; // Default route
        }

        foreach ($this->routes as $route) {
            if ($route['path'] === $uri && $route['method'] === $method) {
                require_once __DIR__ . '/../Controllers/' . $route['controller'] . '.php';
                $controllerName = $route['controller'];
                $controller = new $controllerName();
                $action = $route['action'];
                $controller->$action();
                return;
            }
        }

        echo "404 Not Found";
    }
}
