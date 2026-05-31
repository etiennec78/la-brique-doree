<?php

class CookController extends Controller {
    private function getEnrichedPendingOrders() {
        /*
            
         INPUT :
                 
            None
          
         OUTPUT :

            (array) $pending_orders : variable representing the list of enriched orders

          
         SUMMARY :
            
            Fetches orders with paid or preparing states that do not have an assigned delivery person, and enriches each order record with its corresponding items list.

        */
        require_once __DIR__ . '/../models/Order.php';
        $pending_orders = Order::getOrdersFromState(['paid', 'preparing']);
        
        $pending_orders = array_filter($pending_orders, function($order) {
            return empty($order['delivery_person_id']);
        });
        $pending_orders = array_values($pending_orders);
      
        for ($i = 0; $i < count($pending_orders); $i++) {
            $pending_orders[$i]['items'] = Order::getOrderItems($pending_orders[$i]['id']);
        }
        
        return $pending_orders;
    }

    public function index() {
        /*
            
         INPUT :
                 
            None
          
         OUTPUT :

            None

          
         SUMMARY :
            
            Requires role level 2 or 3, retrieves core dataset values for the kitchen environment including pending and delivery orders, and displays the cook interface view.

        */
        $this->requireRole([2, 3]);

        require_once __DIR__ . '/../models/Cart.php';
        require_once __DIR__ . '/../models/Order.php';
        require_once __DIR__ . '/../models/User.php';
        include_once __DIR__ . '/../format_data.php';

        $uid = $_SESSION['user']['id'];
        $is_admin = User::isAdmin($uid);
        $cart_count = Cart::getCartCount();
        $pending_orders = $this->getEnrichedPendingOrders();
        $delivery_orders = Order::getOrdersFromState(['ready', 'shipping']);
        $deliverers = User::getUsersFromRole('delivery_person');

        $this->render(
            'cook',
            [
                'is_admin' => $is_admin,
                'cart_count' => $cart_count,
                'get_name' => 'getName',
                'pending_orders' => $pending_orders,
                'delivery_orders' => $delivery_orders,
                'deliverers' => $deliverers
            ]
        );
    }

    public function menuEditor() {
        /*
            
         INPUT :
                 
            None
          
         OUTPUT :

            None

          
         SUMMARY :
            
            Requires role level 2 or 3, aggregates existing menus while resolving internal food allergen classifications, collects sorted food variants, and presents the menu configuration dashboard.

        */
        $this->requireRole([2, 3]);
        require_once __DIR__ . '/../models/Menu.php';
        require_once __DIR__ . '/../models/Order.php';
        require_once __DIR__ . '/../models/Food.php';

        $menus = Menu::getMenus();
        foreach($menus as &$menu) {
            $menu['foods'] = Menu::getMenuFoods($menu['id']);
            $menu_allergens = [];
            foreach($menu['foods'] as $food) {
                $food_allergens = Food::getAllergens($food['id']);
                $menu_allergens = array_merge($menu_allergens, $food_allergens);
            }
            $menu['allergens'] = array_unique($menu_allergens);
            $menu['allergens_classes'] = implode(' ', array_map('strtolower', array_map('htmlspecialchars', $menu['allergens'])));
        }

        $foods = Food::getAll();
        $sorted_foods = Order::sortByType($foods);
        $food_types = Food::getTypes();

        $this->render(
            'menu_editor',
            [
                'menus' => $menus,
                'sorted_foods' => $sorted_foods,
                'food_types' => $food_types
            ]
        );
    }

    public function assignOrder() {
        /*
            
         INPUT :
                 
            None
          
         OUTPUT :

            None

          
         SUMMARY :
            
            Requires role level 2 or 3, processes an order selection from global POST variables to modify its execution status to ready, or binds it to a vacant courier.

        */
        $this->requireRole([2, 3]);
        require_once __DIR__ . '/../models/Order.php';

        if (!isset($_POST['order_id'])) {
            header('Location: /cook?error=missing_order_id');
            exit();
        }

        $order_id = (int)$_POST['order_id'];
        $order = Order::getOrderById($order_id);

        if (!$order) {
            header('Location: /cook?error=order_not_found');
            exit();
        }

        if ($order['is_takeaway']) {
            Order::setReadyStatus($order_id);
        } else {
            // Try to find an available delivery person and attach it, or else set the status to "ready"
            $delivery_person = Order::getAvailableStaff("delivery_person");
            if ($delivery_person == null || Order::deliveryCanceled($order_id, $delivery_person)) {
                Order::setReadyStatus($order_id);
            } else {
                Order::setShippingStatus($order_id, $delivery_person);
            }
        }
        header('Location: /cook');
        exit();
    }

    public function finishTakeaway() {
        /*
            
         INPUT :
                 
            None
          
         OUTPUT :

            None

          
         SUMMARY :
            
            Requires role level 2 or 3, updates the condition of a localized takeaway order to a completed delivery status using parameters sent over POST, and performs route forwarding.

        */
        $this->requireRole([2, 3]);
        require_once __DIR__ . '/../models/Order.php';

        if (isset($_POST['order_id'])) {
            $order_id = (int)$_POST['order_id'];
            Order::setDeliveredStatus($order_id);
            header('Location: /cook?success=finished&tab=delivery');
            exit();
        } else {
            header('Location: /cook?tab=delivery');
            exit();
        }
    }

    public function apiCookGetPending() {
        /*
            
         INPUT :
                 
            None
          
         OUTPUT :

            None

          
         SUMMARY :
            
            Requires role level 2 or 3 in API mode, extracts active kitchen operations alongside transit items, and formats the response structure into a JSON stream.

        */
        $this->requireRole([2, 3], true);

        require_once __DIR__ . '/../models/Order.php';
        
        $pending_orders = $this->getEnrichedPendingOrders();
        $delivery_orders = Order::getOrdersFromState(['ready', 'shipping']);

        header('Content-Type: application/json');
        echo json_encode(['pending' => $pending_orders, 'delivery' => $delivery_orders]);
        exit();
    }

    public static function updateMenu() {
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['item_id'], $_POST['action'])) {
            $food_id = (int)$_POST['item_id']; // item_id correspond ici au food_id
            $action = $_POST['action'];

            $menu_id = isset($_POST['menu_id']) ? (int)$_POST['menu_id'] : null;

            if ($menu_id) {
                require_once __DIR__ . '/../models/Menu.php';

                if (isset($_POST['amount']) && $action === 'set') {
                    $amount = (int)$_POST['amount'];
                    Menu::updateItem($menu_id, $food_id, $action, $amount);
                } else {
                    Menu::updateItem($menu_id, $food_id, $action);
                }
            } else {
                $_SESSION['error'] = "Erreur : ID du menu manquant lors de la mise à jour.";
            }
        }

        if (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
            header('Content-Type: application/json');
            $response = [];
            if (isset($_SESSION['error'])) {
                $response['error'] = $_SESSION['error'];
                unset($_SESSION['error']);
            }
            echo json_encode($response);
            exit();
        }

        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        header("Location: $referer");
        exit();
    }
}
