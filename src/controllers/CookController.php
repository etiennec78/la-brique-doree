<?php

class CookController extends Controller {
    private function getEnrichedPendingOrders() {
        require_once __DIR__ . '/../models/Order.php';
        $pending_orders = Order::getOrdersFromState(['paid', 'preparing']);
        
        $pending_orders = array_filter($pending_orders, function($order) {
            return empty($order['delivery_person_id']);
        });
        $pending_orders = array_values($pending_orders);
      
        for ($i = 0; $i < count($pending_orders); $i++) {
            $pending_orders[$i]['items'] = Order::getOrderItems($pending_orders[$i]['id']);
        }
        
        return $pending_orders;
    }

    public function index() {
        $this->requireRole([2, 3]);

        require_once __DIR__ . '/../models/Cart.php';
        require_once __DIR__ . '/../models/Order.php';
        require_once __DIR__ . '/../models/User.php';
        include_once __DIR__ . '/../format_data.php';

        $uid = $_SESSION['user']['id'];
        $is_admin = User::isAdmin($uid);
        $cart_count = Cart::getCartCount();
        $pending_orders = $this->getEnrichedPendingOrders();
        $delivery_orders = Order::getOrdersFromState(['ready', 'shipping']);
        $deliverers = User::getUsersFromRole('delivery_person');

        $this->render(
            'cook',
            [
                'is_admin' => $is_admin,
                'cart_count' => $cart_count,
                'get_name' => 'getName',
                'pending_orders' => $pending_orders,
                'delivery_orders' => $delivery_orders,
                'deliverers' => $deliverers
            ]
        );
    }

    public function assignOrder() {
        $this->requireRole([2, 3]);
        require_once __DIR__ . '/../models/Order.php';

        if (!isset($_POST['order_id'])) {
            header('Location: /cook?error=missing_order_id');
            exit();
        }

        $order_id = (int)$_POST['order_id'];
        $order = Order::getOrderById($order_id);

        if (!$order) {
            header('Location: /cook?error=order_not_found');
            exit();
        }

        if ($order['is_takeaway']) {
            Order::setReadyStatus($order_id);
        } else {
            // Try to find an available delivery person and attach it, or else set the status to "ready"
            $delivery_person = Order::getAvailableStaff("delivery_person");
            if ($delivery_person == null || Order::deliveryCanceled($order_id, $delivery_person)) {
                Order::setReadyStatus($order_id);
            } else {
                Order::setShippingStatus($order_id, $delivery_person);
            }
        }
        header('Location: /cook');
        exit();
    }

    public function finishTakeaway() {
        $this->requireRole([2, 3]);
        require_once __DIR__ . '/../models/Order.php';

        if (isset($_POST['order_id'])) {
            $order_id = (int)$_POST['order_id'];
            Order::setDeliveredStatus($order_id);
            header('Location: /cook?success=finished&tab=delivery');
            exit();
        } else {
            header('Location: /cook?tab=delivery');
            exit();
        }
    }

    public function apiCookGetPending() {
        $this->requireRole([2, 3], true);

        require_once __DIR__ . '/../models/Order.php';
        
        $pending_orders = $this->getEnrichedPendingOrders();
        $delivery_orders = Order::getOrdersFromState(['ready', 'shipping']);

        header('Content-Type: application/json');
        echo json_encode(['pending' => $pending_orders, 'delivery' => $delivery_orders]);
        exit();
    }
}
