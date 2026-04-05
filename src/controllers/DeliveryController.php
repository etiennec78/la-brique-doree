<?php

class DeliveryController extends Controller {
    public function index() {
        include_once __DIR__ . '/../format_data.php';
        require_once __DIR__ . '/../models/Delivery.php';

        $uid = $_SESSION['user']['id'];
        $deliveries = Delivery::getDeliveries($uid);

        $this->render(
            'delivery',
            [
                'deliveries' => $deliveries,
                'getName' => 'getName',
                'getAddress' => 'getAddress'
            ]
        );
    }

    public function confirmDelivery() {
        require_once __DIR__ . '/../models/Order.php';

        if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 4) {
            header('Location: /login');
            exit();
        }

        if (isset($_POST['order_id'])) {
            $order_id = $_POST['order_id'];
            Order::setDeliveredStatus($order_id);
        }

        header('Location: /delivery');
    }

    public function cancelDelivery() {
        require_once __DIR__ . '/../models/Order.php';

        if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 4) {
            header('Location: /login');
            exit();
        }

        if (isset($_POST['order_id'])) {
            $order_id = $_POST['order_id'];
            Order::cancelDelivery($order_id);
        }

        header('Location: /delivery');
    }
}
