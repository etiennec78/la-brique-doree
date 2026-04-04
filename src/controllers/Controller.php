<?php

class Controller {
    protected function render($view, $data = []) {
        extract($data);
        require_once dirname(__DIR__, 2) . "/views/{$view}.php";
    }
}
