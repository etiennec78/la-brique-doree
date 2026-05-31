<?php

class HomeController extends Controller {
    public function index() {
        /*
            
         INPUT :
                 
            None
          
         OUTPUT :

            None

          
         SUMMARY :
            
            Fetches total product amounts registered inside the user's shopping basket context and forwards tracking properties directly into the home landing view template.

        */
        require_once __DIR__ . '/../models/Cart.php';

        $cart_count = Cart::getCartCount();

        $this->render('home', ['cart_count' => $cart_count]);
    }
}
