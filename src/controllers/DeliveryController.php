<?php

class DeliveryController extends Controller {
    private function assignNextDeliveriesIfEmpty($uid) {
        /*
            
         INPUT :
                 
            (mixed) $uid : variable representing the unique user identifier of the delivery courier
          
         OUTPUT :

            None

          
         SUMMARY :
            
            Evaluates current tasks linked to a courier and automatically queries and binds up to three pending orders if their list is vacant.

        */
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
        /*
            
         INPUT :
                 
            None
          
         OUTPUT :

            None

          
         SUMMARY :
            
            Requires role level 3 or 4, compiles target delivery details filtered by administrative scope or individual profile identity, dynamically shapes geographical mapping strings, and handles template execution.

        */
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
        /*
            
         INPUT :
                 
            None
          
         OUTPUT :

            None

          
         SUMMARY :
            
            Requires role level 3 or 4, marks a designated shipment identifier as delivered based on POST inputs, assigns backup requests if the driver queue clears out, and sends redirection responses.

        */
        $this->requireRole([3, 4]);
        require_once __DIR__ . '/../models/Order.php';
        require_once __DIR__ . '/../models/Delivery.php';

        if (isset($_POST['order_id'])) {
            $order_id = $_POST['order_id'];
            $uid = $_SESSION['user']['id'];
            $is_admin = ($_SESSION['user']['role_id'] == 3);

            if (!$is_admin) {
                Order::setDeliveredStatus($order_id);
                $this->assignNextDeliveriesIfEmpty($uid);
            }

            else {
                Delivery::setOrderDeliverypersonAsAdmin($order_id);
                Order::setDeliveredStatus($order_id);
            }
        }

        header('Location: /delivery');
        exit();
    }

    public function cancelDelivery() {
        /*
            
         INPUT :
                 
            None
          
         OUTPUT :

            None

          
         SUMMARY :
            
            Requires role level 3 or 4, cancels an ongoing transit item derived from POST parameters, shifts next tasks into the active queue when required, and forces location adjustments.

        */
        $this->requireRole([3, 4]);
        require_once __DIR__ . '/../models/Order.php';
        require_once __DIR__ . '/../models/Delivery.php';

        if (isset($_POST['order_id'])) {
            $order_id = $_POST['order_id'];
            $uid = $_SESSION['user']['id'];
            $is_admin = ($_SESSION['user']['role_id'] == 3);

            if (!$is_admin) {
                Order::cancelDelivery($order_id, $uid);
                $this->assignNextDeliveriesIfEmpty($uid);
            }

            else {
                Order::cancelDelivery($order_id, $uid);
            }
        }

        header('Location: /delivery');
        exit();
    }

    public function apiDeliveryGetPending() {
        /*
            
         INPUT :
                 
            None
          
         OUTPUT :

            None

          
         SUMMARY :
            
            Requires role level 3 or 4 in API mode, queries active tasks across couriers based on authorization details, and passes the output dataset structured as JSON.

        */
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
