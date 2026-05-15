<?php

class OrderHistoryController extends Controller {
  public function index() {
    if (!isset($_SESSION['user'])) {
      header('Location: /login');
      exit();
    }

    require_once __DIR__ . '/../format_data.php';
    require_once __DIR__ . '/../models/Order.php';
    require_once __DIR__ . '/../models/User.php';

    $uid = $_SESSION['user']['id'];
    $is_admin = User::isAdmin($uid);
    $order_id = null;
    $prev_id = null;
    $next_id = null;
    $order = [];

    // Get the user id to lookup
    if (!isset($_GET['user_id'])) {
      $target_id = $uid;
    } elseif ($is_admin) {
      $target_id = $_GET['user_id'];
    } else {
      header('Location: /login');
      exit();
    }

    $order_ids = Order::getAllCompletedOrderIdsFromUser($target_id);

    // Get the order id to display
    if (isset($_GET['order_id']) && in_array($_GET['order_id'], $order_ids)) {
      $order_id = $_GET['order_id'];
    } elseif (!empty($order_ids)) {
      $order_id = $order_ids[0];
    }

    if ($order_id != null) {
      // Get all data about this order
      $order = Order::getOrderById($order_id);
      $order['items'] = Order::getOrderItems($order['id']);
      $order['cook'] = User::getUserInfo($order['cook_id']);
      $order['delivery_person'] = User::getUserInfo($order['delivery_person_id']);

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
        'order_id' => $order_id,
        'prev_id' => $prev_id,
        'next_id' => $next_id,
        'order' => $order,
        'get_name' => 'getName'
      ]
    );
  }
}

?>
