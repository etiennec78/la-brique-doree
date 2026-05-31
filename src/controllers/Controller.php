<?php

class Controller {
    protected function render($view, $data = []) {
        /*
            
         INPUT :
                 
            (str) $view : variable representing the target view template filename
            (array) $data : variable representing context array parameters to display inside the template
          
         OUTPUT :

            None

          
         SUMMARY :
            
            Deconstructs arrays into local independent data variables, then includes the needed theme view file path.

        */
        extract($data);
        require_once dirname(__DIR__, 2) . "/views/{$view}.php";
    }

    protected function requireRole($allowedRoles, $isApi = false) {
        /*
            
         INPUT :
                 
            (int|array) $allowedRoles : variable representing standard integer roles or an array list of authorized security level parameters
            (bool) $isApi : variable representing whether the current requested execution route belongs to an API interface structure
          
         OUTPUT :

            None

          
         SUMMARY :
            
            Inspects authorization statuses across individual role arrays, managing unauthenticated attempts via automated interface destruction or custom 403 API headers.

        */
        if (!isset($_SESSION['user'])) {
            if ($isApi) {
                http_response_code(403);
                exit();
            }
            session_destroy();
            unset($_SESSION);
            header('Location: /login');
            exit();
        }

        $roleId = $_SESSION['user']['role_id'];
        $roles = is_array($allowedRoles) ? $allowedRoles : [$allowedRoles];

        if (!in_array($roleId, $roles)) {
            if ($isApi) {
                http_response_code(403);
                exit();
            }
            header('Location: /login');
            exit();
        }
    }
}
