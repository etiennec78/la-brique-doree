<?php

class RestaurateurController extends Controller {
    public function index() {
        require_once __DIR__ . '/../models/Cart.php';

        $cart_count = Cart::get_cart_count();

        $this->render('restaurateur', ['cart_count' => $cart_count]);
    }
}
