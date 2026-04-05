<?php

class ProductsController extends Controller {
    public function index() {
        require_once __DIR__ . '/../models/Cart.php';
        require_once __DIR__ . '/../models/Menu.php';
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
        }

        $food_types = Food::getTypes();
        foreach($food_types as &$type) {
            $foods = Food::getByType($type['id']);
            foreach($foods as &$food) {
                $food['allergens'] = Food::getAllergens($food['id']);
            }
            $type['foods'] = $foods;
        }

        $this->render(
            'products',
            [
                'cart_count' => $cart_count,
                'menus' => $menus,
                'food_types' => $food_types
            ]
        );
    }
}
