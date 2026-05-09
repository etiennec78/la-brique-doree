<?php

class OrderTrackingController extends Controller {
  public function index() {
    if (!isset($_SESSION['user'])) {
        header('Location: /login');
        exit();
    }

    require_once __DIR__ . '/../models/Order.php';
    require_once __DIR__ . '/../models/User.php';
    include_once __DIR__ . '/../format_data.php';

    $uid = $_SESSION['user']['id'];
    $order = Order::getUserRunningOrder($uid);
    $cook = User::getUserInfo($order['cook_id']);
    $delivery_person = User::getUserInfo($order['delivery_person_id']);

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

  public function apiOrderStatus() {
    if (!isset($_SESSION['user'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized']);
        return;
    }

    require_once __DIR__ . '/../models/Order.php';
    $uid = $_SESSION['user']['id'];
    $order = Order::getUserRunningOrder($uid);

    header('Content-Type: application/json');
    if ($order) {
        echo json_encode(['status' => $order['status']]);
    } else {
        echo json_encode(['status' => null]);
    }
  }
}
