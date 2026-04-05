<?php

class OrderTrackingController extends Controller {
  public function index() {
    require_once __DIR__ . '/../models/Order.php';
    require_once __DIR__ . '/../models/User.php';
    include_once __DIR__ . '/../get_name.php';

    $uid = $_SESSION['user']['id'];
    $order = Order::getUserRunningOrder($uid);
    $cook = User::getUserData($order['cook_id']);
    $delivery_person = User::getUserData($order['delivery_person_id']);

    $this->render(
      'order_tracking',
      [
        'order' => $order,
        'cook' => $cook,
        'delivery_person' => $delivery_person,
        'get_name' => 'getName'
      ]
    );
  }
}
