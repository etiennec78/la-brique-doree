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

  public function streamOrder() {
    require_once __DIR__ . '/../models/Order.php';

    ob_clean();
    header('Content-Type: text/event-stream');
    header('Cache-Control: no-cache');
    header('Connection: keep-alive');

    $user_id = $_SESSION['user']['id'];
    session_write_close();

    $prevStatus = null;
    while (true) {
      $order = Order::getUserRunningOrder($user_id);
      $status = $order["status"];
      if ($status != $prevStatus) {
        echo "data: " . json_encode(['status' => $status]) . "\n\n";
      }
      $prevStatus = $status;

      ob_flush();
      flush();

      if (connection_aborted()) break;
      sleep(2);
    }
  }
}
