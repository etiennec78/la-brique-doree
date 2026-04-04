<?php
require_once '../src/router.php';

$router = new Router();

$router->add('GET', '', 'HomeController', 'index'); 
$router->add('GET', 'login', 'AuthController', 'showLogin');
$router->add('POST', 'login', 'AuthController', 'processLogin');
$router->add('GET', 'register', 'AuthController', 'showRegister');
$router->add('GET', 'logout', 'AuthController', 'logout');
$router->add('GET', 'orders', 'OrderController', 'index');

$url = $_SERVER['REQUEST_URL'];
$method = $_SERVER['REQUEST_METHOD'];

$router->dispatch($url, $method);