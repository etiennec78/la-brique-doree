<?php

class OrderHistoryController extends Controller {
  public function index() {
    /*
        
     INPUT :
             
        None
      
     OUTPUT :

        None

      
     SUMMARY :
        
        Validates user sessions, verifies viewing permissions for historical order logs based on admin privileges, aggregates precise pricing configurations including active coupon updates, and displays the history interface view.

    */
    if (!isset($_SESSION['user'])) {
      header('Location: /login');
      exit();
    }

    require_once __DIR__ . '/../format_data.php';
    require_once __DIR__ . '/../models/Cart.php';
    require_once __DIR__ . '/../models/Coupon.php';
    require_once __DIR__ . '/../models/Menu.php';
    require_once __DIR__ . '/../models/Order.php';
    require_once __DIR__ . '/../models/User.php';

    $uid = $_SESSION['user']['id'];
    $is_admin = User::isAdmin($uid);

    // Get the user id to lookup
    if (!isset($_GET['user_id']) || $_GET['user_id'] == $uid) {
      $target_id = $uid;
    } elseif ($is_admin) {
      $target_id = $_GET['user_id'];
    } else {
      header('Location: /login');
      exit();
    }

    $order_ids = Order::getAllCompletedOrderIdsFromUser($target_id);

    $order_id = null; 

    // Get the order id to display
    if (isset($_GET['order_id']) && in_array($_GET['order_id'], $order_ids)) {
      $order_id = $_GET['order_id'];
    } elseif (!empty($order_ids)) {
      $order_id = $order_ids[0];
    }

    if ($order_id != null) {
      // Get all data about this order
      $order = Order::getOrderById($order_id);
      $order['cook'] = getName(User::getUserInfo($order['cook_id']));
      $order['delivery_person'] = getName(User::getUserInfo($order['delivery_person_id']));
      $order['total_price'] = 0;

      // Get the total price of the order
      $foods = Cart::getCartItems($target_id, 'food', $order['cart_id']);
      $menus = Cart::getCartItems($target_id, 'menu', $order['cart_id']);
      $cart_has_food = count($foods) > 0;

      // Add foods to menus
      foreach ($menus as &$menu) {
          $menu['foods'] = Menu::getMenuFoods($menu['id']);
      }
      unset($menu);

      $cart_items = array_merge($foods, $menus);
      foreach($cart_items as $item) {
        $order['total_price'] += $item['price'] * $item['quantity'];
      }

      $sorted_foods = Order::sortByType($foods);

      // Get the associated coupon
      $order['coupon'] = Coupon::getCouponFromCart($order['cart_id']);
      if ($order['coupon'] != null) {
        $reduction = $order['coupon']['discount_percent'];
        $order['total_price'] *= (1 - $reduction);
      }

      // Get previous and next order ids
      $index = array_search($order_id, $order_ids);
      if ($index !== false) {
        $prev_id = $index > 0 ? $order_ids[$index - 1] : null;
        $next_id = $index < count($order_ids) - 1 ? $order_ids[$index + 1] : null;
      }
    }

    $this->render(
      'order_history',
      [
        'target_id' => $target_id,
        'order_id' => $order_id ?? null,
        'prev_id' => $prev_id ?? null,
        'next_id' => $next_id ?? null,
        'order' => $order ?? [],
        'menus' => $menus ?? [],
        'sorted_foods' => $sorted_foods ?? [],
        'cart_has_food' => $cart_has_food ?? false
      ]
    );
  }
}
