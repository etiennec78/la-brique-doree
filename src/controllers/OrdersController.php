<?php

class OrdersController extends Controller {
    public function index() {
        require_once __DIR__ . '/../models/Cart.php';

        $cart_count = Cart::getCartCount();
        $cart_id = Cart::getUserCartId($_SESSION['user']['id']);

        $this->render('orders', ['cart_count' => $cart_count, 'cart_id' => $cart_id]);
    }
}
