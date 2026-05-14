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
        'orders' => $orders,
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
