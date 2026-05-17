<?php

class DeliveryController extends Controller {
    public function index() {
        if (!isset($_SESSION['user']) || (($_SESSION['user']['role_id'] != 4) && ($_SESSION['user']['role_id'] != 3))) {
            session_destroy();
            unset($_SESSION);
            header('Location: /login');
            exit();
        }

        include_once __DIR__ . '/../format_data.php';
        require_once __DIR__ . '/../models/Delivery.php';

        $uid = $_SESSION['user']['id'];
        $deliveries = Delivery::getDeliveries($uid);
        $deliveries_coordinates = Delivery::getDeliveriesCoordinates($uid);
        $map_url = Delivery::buildMapURL($deliveries_coordinates);

        $this->render(
            'delivery',
            [
                'deliveries' => $deliveries,
                'map_url' => $map_url,
                'getName' => 'getName',
                'getAddress' => 'getAddress'
            ]
        );
    }

    public function confirmDelivery() {
        require_once __DIR__ . '/../models/Order.php';
        require_once __DIR__ . '/../models/Delivery.php';

        if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 4) {
            header('Location: /login');
            exit();
        }

        if (isset($_POST['order_id'])) {
            $order_id = $_POST['order_id'];
            $uid = $_SESSION['user']['id'];

            Order::setDeliveredStatus($order_id);
            
            $deliveries = Delivery::getDeliveries($uid);
            if (empty($deliveries)) {
                $next_orders = Order::getNextDeliveries($uid, 3);
                foreach ($next_orders as $next_order) {
                    Order::setShippingStatus($next_order['id'], $uid);
                }
            }
        }

        header('Location: /delivery');
        exit();
    }

    public function cancelDelivery() {
        require_once __DIR__ . '/../models/Order.php';
        require_once __DIR__ . '/../models/Delivery.php';

        if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 4) {
            header('Location: /login');
            exit();
        }

        if (isset($_POST['order_id'])) {
            $order_id = $_POST['order_id'];
            $uid = $_SESSION['user']['id'];
            
            Order::cancelDelivery($order_id, $uid);
            
            $deliveries = Delivery::getDeliveries($uid);
            if (empty($deliveries)) {
                $next_orders = Order::getNextDeliveries($uid, 3);
                foreach ($next_orders as $next_order) {
                    Order::setShippingStatus($next_order['id'], $uid);
                }
            }
        }

        header('Location: /delivery');
        exit();
    }

    public function apiDeliveryGetPending() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 4) {
            http_response_code(403);
            exit();
        }

        require_once __DIR__ . '/../models/Delivery.php';

        $uid = $_SESSION['user']['id'];
        $deliveries = Delivery::getDeliveries($uid);

        header('Content-Type: application/json');
        echo json_encode(['deliveries' => $deliveries]);
        exit();
    }
}
