<?php

class ProductsController extends Controller {
    public function index() {
        require_once __DIR__ . '/../models/Cart.php';
        require_once __DIR__ . '/../models/Menu.php';
        require_once __DIR__ . '/../models/Order.php';
        require_once __DIR__ . '/../models/Food.php';

        $cart_count = Cart::getCartCount();
        
        $menus = Menu::getMenus();
        foreach($menus as &$menu) {
            $menu['foods'] = Menu::getMenuFoods($menu['id']);
            $menu_allergens = [];
            foreach($menu['foods'] as $food) {
                $food_allergens = Food::getAllergens($food['item_id']);
                $menu_allergens = array_merge($menu_allergens, $food_allergens);
            }
            $menu['allergens'] = array_unique($menu_allergens);
            $menu['allergens_classes'] = implode(' ', array_map('strtolower', array_map('htmlspecialchars', $menu['allergens'])));
        }

        $foods = Food::getAll();
        $sorted_foods = Order::sortByType($foods);
        $food_types = Food::getTypes();

        $this->render(
            'products',
            [
                'cart_count' => $cart_count,
                'menus' => $menus,
                'sorted_foods' => $sorted_foods,
                'food_types' => $food_types
            ]
        );
    }
}
