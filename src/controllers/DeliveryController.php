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
        require_once __DIR__ . '/../models/User.php';

        $uid = $_SESSION['user']['id'];
        $is_admin = ($_SESSION['user']['role_id'] == 3);

        $deliveries = [];

        if ($is_admin) {
            for ($driver_id = 1; $driver_id <= 30; $driver_id++) {
                $driver_deliveries = Delivery::getDeliveries($driver_id);
                if (!empty($driver_deliveries) && is_array($driver_deliveries)) {
                    
                    $driver_info = User::getUserInfo($driver_id);
                    $driver_name = !empty($driver_info['first_name']) ? $driver_info['first_name'] : "Livreur";

                    foreach ($driver_deliveries as &$deliv) {
                        $deliv['driver_first_name'] = $driver_name;
                    }
                    $deliveries = array_merge($deliveries, $driver_deliveries);
                }
            }
        } else {
            $deliveries = Delivery::getDeliveries($uid);
        }
        
        $deliveries_coordinates = $is_admin ? [] : Delivery::getDeliveriesCoordinates($uid);
        $map_url = Delivery::buildMapURL($deliveries_coordinates);

        $this->render(
            'delivery',
            [
                'deliveries' => $deliveries,
                'map_url' => $map_url,
                'getName' => 'getName',
                'getAddress' => 'getAddress',
                'is_admin' => $is_admin
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
        $this->requireRole([3, 4], true);

        require_once __DIR__ . '/../models/Delivery.php';
        require_once __DIR__ . '/../models/User.php';

        $uid = $_SESSION['user']['id'];
        $is_admin = ($_SESSION['user']['role_id'] == 3);

        $deliveries = [];

        if ($is_admin) {
            for ($driver_id = 1; $driver_id <= 30; $driver_id++) {
                $driver_deliveries = Delivery::getDeliveries($driver_id);
                if (!empty($driver_deliveries) && is_array($driver_deliveries)) {
                    
                    $driver_info = User::getUserInfo($driver_id);
                    $driver_name = !empty($driver_info['first_name']) ? $driver_info['first_name'] : "Livreur";

                    foreach ($driver_deliveries as &$deliv) {
                        $deliv['driver_first_name'] = $driver_name;
                    }
                    $deliveries = array_merge($deliveries, $driver_deliveries);
                }
            }
        } else {
            $deliveries = Delivery::getDeliveries($uid);
        }

        header('Content-Type: application/json');
        echo json_encode(['deliveries' => $deliveries]);
        exit();
    }
}
