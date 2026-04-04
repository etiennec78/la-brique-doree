<?php

class OrdersController extends Controller {
    public function index() {
        require_once __DIR__ . '/../models/Cart.php';

        $cart_count = Cart::getCartCount();

        $this->render('orders', ['cart_count' => $cart_count]);
    }
}
