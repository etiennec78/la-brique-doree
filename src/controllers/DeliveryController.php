<?php

class DeliveryController extends Controller {
    private function assignNextDeliveriesIfEmpty($uid) {
        require_once __DIR__ . '/../models/Order.php';
        require_once __DIR__ . '/../models/Delivery.php';
        
        $deliveries = Delivery::getDeliveries($uid);
        if (empty($deliveries)) {
            $next_orders = Order::getNextDeliveries($uid, 3);
            foreach ($next_orders as $next_order) {
                Order::setShippingStatus($next_order['id'], $uid);
            }
        }
    }

    public function index() {
        $this->requireRole([3, 4]);

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
        $this->requireRole(4);
        require_once __DIR__ . '/../models/Order.php';
        require_once __DIR__ . '/../models/Delivery.php';

        if (isset($_POST['order_id'])) {
            $order_id = $_POST['order_id'];
            $uid = $_SESSION['user']['id'];

            Order::setDeliveredStatus($order_id);
            $this->assignNextDeliveriesIfEmpty($uid);
        }

        header('Location: /delivery');
        exit();
    }

    public function cancelDelivery() {
        $this->requireRole(4);
        require_once __DIR__ . '/../models/Order.php';
        require_once __DIR__ . '/../models/Delivery.php';

        if (isset($_POST['order_id'])) {
            $order_id = $_POST['order_id'];
            $uid = $_SESSION['user']['id'];
            
            Order::cancelDelivery($order_id, $uid);
            $this->assignNextDeliveriesIfEmpty($uid);
        }

        header('Location: /delivery');
        exit();
    }

    public function apiDeliveryGetPending() {
        $this->requireRole(4, true);

        require_once __DIR__ . '/../models/Delivery.php';

        $uid = $_SESSION['user']['id'];
        $deliveries = Delivery::getDeliveries($uid);

        header('Content-Type: application/json');
        echo json_encode(['deliveries' => $deliveries]);
        exit();
    }
}
