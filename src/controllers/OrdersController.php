<?php

class OrdersController extends Controller {
    public function index() {
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit();
        }

        require_once __DIR__ . '/../models/Cart.php';
        require_once __DIR__ . '/../models/Coupon.php';
        require_once __DIR__ . '/../models/User.php';
        require_once __DIR__ . '/../models/Menu.php';
        require_once __DIR__ . '/../getapikey.php';

        $uid = $_SESSION['user']['id'];
        $cart_count = Cart::getCartCount();
        $cart_id = Cart::getUserCartId($uid);
        $coupon = Coupon::getCouponFromCart($cart_id);
        $global_reduction = User::getGlobalReduction($uid);
        $expired_coupon = (
            !empty($coupon)
            and isset($coupon['expiration_date'])
            and strtotime($coupon['expiration_date']) < time()
        );

        $user_has_valid_info = false;
        $user_has_valid_info = User::hasValidInfo($uid);

        $user_has_valid_address = false;
        $user_has_valid_address = User::hasValidAddress($uid);

        $vendeur = 'MI-4_J'; 
        $api_key = getAPIKey($vendeur); 
        $transaction = uniqid();

        $is_takeaway = isset($_SESSION['is_takeaway']) && $_SESSION['is_takeaway'] ? 1 : 0;
        $takeaway_time = isset($_SESSION['takeaway_time']) ? $_SESSION['takeaway_time'] : '';

        $host = $GLOBALS['config']['host'] ?? 'http://localhost';
        $retour_url = $host . "/payment_result?cart_id=" . $cart_id . "&is_takeaway=" . $is_takeaway . "&takeaway_time=" . urlencode($takeaway_time);

        $total_price = 0;
        $reduction = 0;
        $cart_details = [];

        $menus = Cart::getCartItems($uid, "menu");
        $foods = Cart::getCartItems($uid, "food");
        $cart_has_food = count($foods) > 0;

        foreach ($menus as &$menu) {
            $menu['foods'] = Menu::getMenuFoods($menu['id']);
            $price_val = floatval($menu['price']);
            $quantity = $menu['quantity'];
            $total_price += $price_val * $quantity;
            $price_str = number_format($price_val, 2, ",");
            $cart_details[] = "{$menu['name']} ({$price_str}€) x$quantity";
        }
        unset($menu);

        if ($cart_has_food) {
            foreach ($foods as $food) {
                $price_val = floatval($food['price']);
                $quantity = $food['quantity'];
                $total_price += $price_val * $quantity;
                $price_str = number_format($price_val, 2, ",");
                $cart_details[] = "{$food['name']} ({$price_str}€) x$quantity";
            }
        }

        if ($coupon !== false and !$expired_coupon) {
            $reduction = $coupon['discount_percent'];
            $coupon_code = $coupon['code'];
            $cart_details[] = "Code: $coupon_code ". ($reduction * 100) . "% (-". number_format($total_price * $reduction, 2, '.', '') ."€)";
            $total_price *= (1 - $reduction);
        }

        if ($global_reduction != 0) {
            $cart_details[] = "Réduction globale ". ($global_reduction * 100) . "% (-". number_format($total_price * $global_reduction, 2, '.', '') ."€)";
            $total_price *= (1 - $global_reduction);
        }

        $montant_cybank = number_format($total_price, 2, '.', '');
        $control = md5($api_key . "#" . $transaction . "#" . $montant_cybank . "#" . $vendeur . "#" . $retour_url . "#");

        $this->render(
            'orders',
            [
                'cart_count' => $cart_count,
                'cart_id' => $cart_id,
                'coupon' => $coupon,
                'expired_coupon' => $expired_coupon,
                'global_reduction' => $global_reduction,
                'vendeur' => $vendeur,
                'transaction' => $transaction,
                'is_takeaway' => $is_takeaway,
                'takeaway_time' => $takeaway_time,
                'retour_url' => $retour_url,
                'total_price' => $total_price,
                'cart_details' => $cart_details,
                'menus' => $menus,
                'foods' => $foods,
                'cart_has_food' => $cart_has_food,
                'montant_cybank' => $montant_cybank,
                'control' => $control,
                'user_has_valid_info' => $user_has_valid_info,
                'user_has_valid_address' => $user_has_valid_address
            ]
        );
    }

    public function updateCart() {
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['item_id'], $_POST['item_type'], $_POST['action'])) {
            $item_id = (int)$_POST['item_id'];
            $item_type = $_POST['item_type'];
            $action = $_POST['action'];
            $user_id = $_SESSION['user']['id'];

            if (!in_array($item_type, ['food', 'menu'])) {
                header('Location: /orders');
                exit();
            }

            require_once __DIR__ . '/../models/Cart.php';
            Cart::updateItem($user_id, $item_id, $item_type, $action);
        }

        if (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
            require_once __DIR__ . '/../models/Cart.php';
            header('Content-Type: application/json');
            echo json_encode(['cart_count' => Cart::getCartCount()]);
            exit();
        }

        $referer = $_SERVER['HTTP_REFERER'] ?? '../views/orders.php';
        header("Location: $referer");
        exit();
    }

    public function applyCoupon() {
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit();
        }

        global $pdo;
        require_once __DIR__ . '/../db_connect.php';
        require_once __DIR__ . '/../models/Cart.php';
        require_once __DIR__ . '/../models/Coupon.php';

        $coupon_code = isset($_POST['coupon']) ? trim($_POST['coupon']) : '';
        $_SESSION['previous_coupon'] = $coupon_code;
        $uid = $_SESSION['user']['id'];
        $cart_id = Cart::getUserCartId($uid);

        // Remove coupon if empty
        if ($coupon_code === "") {
            unset($_SESSION['error']);
            Coupon::addCouponToCart(null, $cart_id);
            header("Location: /orders");
            exit();
        }

        // Add coupon if valid
        $coupon = Coupon::getCoupon($coupon_code);

        if ($coupon === false) {
            $_SESSION['error'] = 'Le coupon utilisé est invalide.';
            Coupon::addCouponToCart(null, $cart_id);
            header("Location: /orders");
            exit();
        }

        else {
            unset($_SESSION['error']);
            Coupon::addCouponToCart($coupon['id'], $cart_id);
        }

        header("Location: /orders");
        exit();
    }

    public function setDeliveryType() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['is_takeaway'])) {
                $_SESSION['is_takeaway'] = $_POST['is_takeaway'] === '1';
                // Reset time if user switches back to delivery
                if (!$_SESSION['is_takeaway']) {
                    unset($_SESSION['takeaway_time']);
                }
            }
            if (isset($_POST['takeaway_time']) && !empty($_POST['takeaway_time'])) {
                $_SESSION['takeaway_time'] = $_POST['takeaway_time'];
            }
        }
        header("Location: /orders");
        exit();
    }
}
