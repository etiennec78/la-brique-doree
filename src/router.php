<?php

class Router {
    $routes = [];

    function add($method, $path, $controller, $action) {
        $path = trim($path, '/');
        
        $this->routes[] = ['method' => $method, 'path' => $path, 'controller' => $controller, 'action' => $action];
    }

    function dispatch($requestedUrl, $requestMethod) {
        $requestedUrl = trim(parse_url($requestedUrl, PHP_URL_PATH), '/');

        foreach ($this->routes as $route) {
            if ($route['path'] === $requestedUrl && $route['method'] === $requestMethod) {
                
                $controllerName = $route['controller'];
                $actionName = $route['action'];

                require_once "../src/controllers/" . $controllerName . ".php";

                $controller = new $controllerName();
                $controller->$actionName();
                
                return;
            }
        }

        http_response_code(404);
        echo "<h1>404 - Page non trouvée</h1><p>La page n'existe pas.</p>";
    }
}