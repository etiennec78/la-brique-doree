<?php

class Router {
    private $routes = [];

    public function add($method, $path, $controller, $action) {
        /*
            
         INPUT :
                 
            (string) $method : variable representing the HTTP request method
            (string) $path : variable representing the destination endpoint route
            (string) $controller : variable representing the target class handler identifier
            (string) $action : variable representing the specific class method execution target
          
         OUTPUT :

            None

          
         SUMMARY :
            
            Trims surrounding slashes from the requested endpoint URI pattern and appends the unified configuration parameters to the persistent internal routing collection.

        */
        $path = trim($path, '/');

        $this->routes[] = ['method' => $method, 'path' => $path, 'controller' => $controller, 'action' => $action];
    }

    public function dispatch($requestedUrl, $requestMethod) {
        /*
            
         INPUT :
                 
            (string) $requestedUrl : variable representing the incoming execution request URI string
            (string) $requestMethod : variable representing the active HTTP transmission verb
          
         OUTPUT :

            None

          
         SUMMARY :
            
            Parses the requested URL path, enforces real-time suspension and ban checks for active user profiles, attempts to dynamically locate and invoke the matching controller action handler, or defaults to rendering a standard 404 page error status.

        */
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
