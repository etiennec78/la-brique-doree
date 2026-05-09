<?php

class CookController extends Controller {
    public function index() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 2) {
            header('Location: /login');
            exit();
        }

        require_once __DIR__ . '/../models/Cart.php';
        require_once __DIR__ . '/../models/Order.php';
        require_once __DIR__ . '/../models/User.php';
        include_once __DIR__ . '/../format_data.php';

        $cart_count = Cart::getCartCount();
        $pending_orders = Order::getOrdersFromState(array('paid', 'preparing'));
        $delivery_orders = Order::getOrdersFromState(array('ready', 'shipping'));
        $deliverers = User::getUsersFromRole('delivery_person');

        $this->render(
            'cook',
            [
                'cart_count' => $cart_count,
                'get_name' => 'getName',
                'pending_orders' => $pending_orders,
                'delivery_orders' => $delivery_orders,
                'deliverers' => $deliverers
            ]
        );
    }

    public function assignOrder() {
        require_once __DIR__ . '/../models/Order.php';

        if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 2) {
            header('Location: /login');
            exit();
        }

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
    }

    public function finishTakeaway() {
        require_once __DIR__ . '/../models/Order.php';

        if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 2) {
            header('Location: /login');
            exit();
        }

        if (isset($_POST['order_id'])) {
            $order_id = (int)$_POST['order_id'];
            Order::setDeliveredStatus($order_id);
            header('Location: /cook?success=finished');
        } else {
            header('Location: /cook');
            exit();
        }
    }
}
