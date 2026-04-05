<?php

class OrderTrackingController extends Controller {
  public function index() {
    require_once __DIR__ . '/../models/Order.php';
    include_once __DIR__ . '/../get_name.php';

    $uid = $_SESSION['user']['id'];
    $order = Order::getUserRunningOrder($uid);

    $this->render('order_tracking', ['order' => $order, 'get_name' => 'getName']);
  }
}
