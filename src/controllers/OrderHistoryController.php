<?php

class OrderHistoryController extends Controller {
  public function index() {
    if (!isset($_SESSION['user'])) {
      header('Location: /login');
      exit();
    }

    require_once __DIR__ . '/../models/Order.php';
    require_once __DIR__ . '/../models/User.php';

    $uid = $_SESSION['user']['id'];
    $is_admin = User::isAdmin($uid);

    if (!isset($_GET['user_id'])) {
      $target_id = $uid;
    } elseif ($is_admin) {
      $target_id = $_GET['user_id'];
    } else {
      header('Location: /login');
      exit();
    }

    $all_orders = Order::getAllOrdersFromUser($target_id);

    foreach ($all_orders as &$order) {
      $order['items'] = Order::getOrderItems($order['id']);
    }

    foreach ($all_orders as &$order) {
      $order['cook'] = User::getUserInfo($order['cook_id']);
      $order['cook'] = $order['cook']['first_name'].' '.$order['cook']['last_name'];
      $order['delivery_person'] = User::getUserInfo($order['delivery_person_id']);
      $order['delivery_person'] = $order['delivery_person']['first_name'].' '.$order['delivery_person']['last_name'];
    }

    $this->render('order_history', ['all_orders' => $all_orders]);
  }
}

?>
