<?php

class Controller {
    protected function render($view, $data = []) {
        extract($data);
        require_once dirname(__DIR__, 2) . "/views/{$view}.php";
    }

    protected function requireRole($allowedRoles, $isApi = false) {
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
