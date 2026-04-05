<?php

class RestaurateurController extends Controller {
    public function index() {
        require_once __DIR__ . '/../models/Cart.php';
        require_once __DIR__ . '/../models/Order.php';
        require_once __DIR__ . '/../models/User.php';
        include_once __DIR__ . '/../format_data.php';

        $cart_count = Cart::getCartCount();
        $pending_orders = Order::getOrdersFromState(array('paid', 'preparing'));
        $delivery_orders = Order::getOrdersFromState(array('ready', 'shipping'));
        $deliverers = User::getUsersFromRole('delivery_person');

        $this->render(
            'restaurateur',
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

        if (isset($_POST['order_id'])) {
            $order_id = (int)$_POST['order_id'];
            $order = Order::getOrderById($order_id);
            if ($order && $order['is_takeaway']) {
                Order::setReadyStatus($order_id);
            } else {
                $delivery_person = Order::getAvailableDeliveryPerson();
                Order::setDeliveryStatus($order_id, $delivery_person);
            }

            header('Location: /restaurateur?success=assigned');
        } else {
            header('Location: /restaurateur');
            exit();
        }
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
            header('Location: /restaurateur?success=finished');
        } else {
            header('Location: /restaurateur');
            exit();
        }
    }
}
