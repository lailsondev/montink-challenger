<?php

namespace App;

class Router
{
    private $routes = [];

    public function addRoute($method, $path, $callback)
    {
        $this->routes[] = ['method' => $method, 'path' => $path, 'callback' => $callback];
    }

    public function dispatch()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $route['path'] === $path) {
                if (is_callable($route['callback'])) {
                    call_user_func($route['callback']);
                    return;
                } elseif (is_array($route['callback']) && count($route['callback']) == 2) {
                    $controllerName = "App\\Controllers\\" . $route['callback'][0];
                    $methodName = $route['callback'][1];

                    if (class_exists($controllerName) && method_exists($controllerName, $methodName)) {
                        $controller = new $controllerName();
                        $controller->$methodName();
                        return;
                    }
                }
            }
        }

        http_response_code(404);
        echo MSG_PAGE_NOT_FOUND;
    }
}