<?php

class ProfileController extends Controller {
    public function index() {
        require_once __DIR__ . '/../models/Cart.php';
        require_once __DIR__ . '/../models/User.php';

        $uid = $_SESSION['user']['id'];
        $cart_count = Cart::getCartCount();
        $user_data = User::getUserData($uid);

        $this->render(
            'profile',
            [
                'cart_count' => $cart_count,
                'user_data' => $user_data
            ]
        );
    }
}
