<?php

class Router {
    private $routes = [];

    public function add($method, $path, $controller, $action) {
        $path = trim($path, '/');

        $this->routes[] = ['method' => $method, 'path' => $path, 'controller' => $controller, 'action' => $action];
    }

    public function dispatch($requestedUrl, $requestMethod) {
        $requestedUrl = trim(parse_url($requestedUrl, PHP_URL_PATH), '/');

        if (isset($_SESSION['user']['id'])) {
            require_once __DIR__ . '/models/User.php';
            $check_user = User::getUserInfo($_SESSION['user']['id']);
            if (!empty($check_user['banned'])) {
                unset($_SESSION['user']);
                $_SESSION['error'] = 'Votre compte a été suspendu.';
                header('Location: /login');
                exit();
            }
        }

        require_once __DIR__ . '/controllers/Controller.php';

        foreach ($this->routes as $route) {
            if ($route['path'] === $requestedUrl && $route['method'] === $requestMethod) {

                $controllerName = $route['controller'];
                $actionName = $route['action'];

                require_once __DIR__ . '/controllers/' . $controllerName . '.php';

                $controller = new $controllerName();
                $controller->$actionName();

                return;
            }
        }

        http_response_code(404);
        echo "<h1>404 - Page non trouvée</h1><p>La page n'existe pas.</p>";
    }
}
