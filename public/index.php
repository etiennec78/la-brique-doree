<?php
session_start();
require_once '../src/db_connect.php';
require_once '../src/router.php';

$router = new Router();

$router->add('GET', '', 'HomeController', 'index');
$router->add('GET', 'login', 'AuthController', 'showLogin');
$router->add('POST', 'login', 'AuthController', 'processLogin');
$router->add('GET', 'register', 'AuthController', 'showRegister');
$router->add('POST', 'register', 'AuthController', 'processRegister');
$router->add('GET', 'logout', 'AuthController', 'logout');
$router->add('GET', 'orders', 'OrdersController', 'index');
$router->add('POST', 'apply_coupon', 'OrdersController', 'applyCoupon');
$router->add('GET', 'products', 'ProductsController', 'index');
$router->add('GET', 'admin', 'AdminController', 'index');
$router->add('POST', 'global_reduction', 'AdminController', 'applyGlobalReduction');
$router->add('GET', 'delivery', 'DeliveryController', 'index');
$router->add('POST', 'confirm_delivery', 'DeliveryController', 'confirmDelivery');
$router->add('GET', 'order_tracking', 'OrderTrackingController', 'index');
$router->add('POST', 'update_cart', 'OrdersController', 'updateCart');
$router->add('GET', 'payment_result', 'PaymentResultController', 'index');
$router->add('GET', 'profile', 'ProfileController', 'index');
$router->add('POST', 'profile', 'ProfileController', 'updateProfile');
$router->add('GET', 'restaurateur', 'RestaurateurController', 'index');
$router->add('POST', 'assign_order', 'RestaurateurController', 'assignOrder');
$router->add('GET', 'reviews', 'ReviewsController', 'index');
$router->add('POST', 'reviews', 'ReviewsController', 'addReview');

$url = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

$router->dispatch($url, $method);
