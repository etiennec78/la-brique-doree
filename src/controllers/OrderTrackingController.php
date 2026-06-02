<?php

class OrderTrackingController extends Controller {
  public function index() {
    /*
        
     INPUT :
             
        None
      
     OUTPUT :

        None

      
     SUMMARY :
        
        Collects active ongoing delivery tasks linked to the authenticated user ID, maps relational staff profiles (cooks and couriers), and shows the dynamic tracking interface view.

    */
    if (!isset($_SESSION['user'])) {
        header('Location: /login');
        exit();
    }

    require_once __DIR__ . '/../models/Cart.php';
    require_once __DIR__ . '/../models/Order.php';
    require_once __DIR__ . '/../models/User.php';
    include_once __DIR__ . '/../format_data.php';

    $uid = $_SESSION['user']['id'];
    $cart_count = Cart::getCartCount();
    
    $force_ids = isset($_GET['keep_ids']) ? explode(',', $_GET['keep_ids']) : [];
    $force_ids = array_filter($force_ids, 'is_numeric');
    
    $orders = Order::getUserActiveOrders($uid, $force_ids);

    foreach ($orders as &$order) {
        $order['cook'] = User::getUserInfo($order['cook_id']);
        $order['delivery_person'] = User::getUserInfo($order['delivery_person_id']);
    }

    $this->render(
      'order_tracking',
      [
        'cart_count' => $cart_count,
        'orders' => $orders,
        'get_name' => 'getName'
      ]
    );
  }

  public function apiOrderStatus() {
    /*
        
     INPUT :
             
        None
      
     OUTPUT :

        None

      
     SUMMARY :
        
        Evaluates ongoing order status logs linked to safe user references using arrays provided inside GET attributes and terminates execution returning JSON payloads.

    */
    if (!isset($_SESSION['user'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized']);
        return;
    }

    require_once __DIR__ . '/../models/Order.php';
    $uid = $_SESSION['user']['id'];
    
    header('Content-Type: application/json');

    if (isset($_GET['ids'])) {
        $ids = explode(',', $_GET['ids']);
        $ids = array_filter($ids, 'is_numeric');
        if (empty($ids)) {
             echo json_encode(['statuses' => []]);
             return;
        }
        $statuses = Order::getOrderStatuses($uid, $ids);
        echo json_encode(['statuses' => $statuses]);
    } else {
        echo json_encode(['statuses' => []]);
    }
  }
}
