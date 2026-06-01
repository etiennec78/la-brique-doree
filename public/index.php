<?php
session_start();

$GLOBALS['config'] = require_once '../src/config.php';

require_once '../src/db_connect.php';
require_once '../src/router.php';

$router = new Router();

// GET
$router->add('GET', 'admin', 'AdminController', 'index');
$router->add('GET', 'register', 'AuthController', 'showRegister');
$router->add('GET', 'login', 'AuthController', 'showLogin');
$router->add('GET', 'logout', 'AuthController', 'logout');
$router->add('GET', 'delivery', 'DeliveryController', 'index');
$router->add('GET', 'api_delivery', 'DeliveryController', 'apiDeliveryGetPending');
$router->add('GET', '', 'HomeController', 'index');
$router->add('GET', 'add_menu_food', 'MenuEditorController', 'addMenuFood');
$router->add('GET', 'food_creator', 'MenuEditorController', 'foodCreator');
$router->add('GET', 'food_picker', 'MenuEditorController', 'foodPicker');
$router->add('GET', 'menu_editor', 'MenuEditorController', 'index');
$router->add('GET', 'menu_creator', 'MenuEditorController', 'menuCreator');
$router->add('GET', 'orders', 'OrdersController', 'index');
$router->add('GET', 'order_history', 'OrderHistoryController', 'index');
$router->add('GET', 'order_tracking', 'OrderTrackingController', 'index');
$router->add('GET', 'api_order_status', 'OrderTrackingController', 'apiOrderStatus');
$router->add('GET', 'payment_result', 'PaymentResultController', 'index');
$router->add('GET', 'products', 'ProductsController', 'index');
$router->add('GET', 'profile', 'ProfileController', 'index');
$router->add('GET', 'cook', 'CookController', 'index');
$router->add('GET', 'api_cook', 'CookController', 'apiCookGetPending');
$router->add('GET', 'reviews', 'ReviewsController', 'index');

// POST
$router->add('POST', 'global_reduction', 'AdminController', 'applyGlobalReduction');
$router->add('POST', 'api_ban_user', 'AdminController', 'apiBanUser');
$router->add('POST', 'register', 'AuthController', 'processRegister');
$router->add('POST', 'login', 'AuthController', 'processLogin');
$router->add('POST', 'assign_order', 'CookController', 'assignOrder');
$router->add('POST', 'finish_takeaway', 'CookController', 'finishTakeaway');
$router->add('POST', 'confirm_delivery', 'DeliveryController', 'confirmDelivery');
$router->add('POST', 'cancel_delivery', 'DeliveryController', 'cancelDelivery');
$router->add('POST', 'delete_menu', 'MenuEditorController', 'deleteMenu');
$router->add('POST', 'update_menu', 'MenuEditorController', 'updateMenu');
$router->add('POST', 'manage_food', 'MenuEditorController', 'manageFood');
$router->add('POST', 'create_menu', 'MenuEditorController', 'createMenu');
$router->add('POST', 'set_delivery_type', 'OrdersController', 'setDeliveryType');
$router->add('POST', 'update_cart', 'OrdersController', 'updateCart');
$router->add('POST', 'apply_coupon', 'OrdersController', 'applyCoupon');
$router->add('POST', 'profile', 'ProfileController', 'updateProfile');
$router->add('POST', 'reviews', 'ReviewsController', 'addReview');

$url = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

$router->dispatch($url, $method);
