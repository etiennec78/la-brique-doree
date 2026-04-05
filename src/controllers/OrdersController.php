<?php

class OrdersController extends Controller {
    public function index() {
        require_once __DIR__ . '/../models/Cart.php';
        require_once __DIR__ . '/../models/Coupon.php';
        require_once __DIR__ . '/../models/User.php';

        $uid = $_SESSION['user']['id'];
        $cart_count = Cart::getCartCount();
        $cart_id = Cart::getUserCartId($uid);
        $coupon = Coupon::getCouponFromUser($uid);
        $global_reduction = User::getGlobalReduction($uid);
        $expired_coupon = (
            !empty($coupon)
            and isset($coupon['expiration_date'])
            and strtotime($coupon['expiration_date']) < time()
        );

        $this->render(
            'orders',
            [
                'cart_count' => $cart_count,
                'cart_id' => $cart_id,
                'coupon' => $coupon,
                'expired_coupon' => $expired_coupon,
                'global_reduction' => $global_reduction
            ]
        );
    }

    public function updateCart() {
        global $pdo;
        require_once __DIR__ . '/../db_connect.php';

        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['item_id'], $_POST['item_type'], $_POST['action'])) {
            $item_id = (int)$_POST['item_id'];
            $item_type = $_POST['item_type'];
            $action = $_POST['action'];
            $user_id = $_SESSION['user']['id'];

            if (!in_array($item_type, ['food', 'menu'])) {
                header('Location: /orders');
                exit;
            }

            $table_name = $item_type === 'food' ? 'cart_food' : 'cart_menu';
            $foreign_key = $item_type === 'food' ? 'food_id' : 'menu_id';

            try {
                $pdo->beginTransaction();

                // Get active cart
                $stmt = $pdo->prepare("SELECT id FROM cart WHERE user_id = ? AND payment_status_id = 1");
                $stmt->execute([$user_id]);
                $cart = $stmt->fetch();

                if ($cart) {
                    $cart_id = $cart['id'];
                } else {

                    $creerPanier = $pdo->prepare("INSERT INTO cart (user_id, payment_status_id, created_at) VALUES (?, 1, NOW())");

                    $creerPanier->execute([$user_id]);

                    $cart_id = $pdo->lastInsertId();
                }

                // Get current cart items
                $stmt = $pdo->prepare("SELECT quantity FROM $table_name WHERE cart_id = ? AND $foreign_key = ?");
                $stmt->execute([$cart_id, $item_id]);
                $cart_item = $stmt->fetch();

                if ($cart_item) {
                    $current_quantity = (int)$cart_item['quantity'];

                    if ($action === 'add' and $current_quantity < 9) {
                        $stmt = $pdo->prepare("UPDATE $table_name SET quantity = quantity + 1 WHERE cart_id = ? AND $foreign_key = ?");
                        $stmt->execute([$cart_id, $item_id]);
                    } elseif ($action === 'remove') {
                        if ($current_quantity > 1) {
                            $stmt = $pdo->prepare("UPDATE $table_name SET quantity = quantity - 1 WHERE cart_id = ? AND $foreign_key = ?");
                            $stmt->execute([$cart_id, $item_id]);
                        } else {
                            // Remove item completely
                            $stmt = $pdo->prepare("DELETE FROM $table_name WHERE cart_id = ? AND $foreign_key = ?");
                            $stmt->execute([$cart_id, $item_id]);
                        }
                    }
                } elseif ($action === 'add') {
                    // Create a new entry
                    $stmt = $pdo->prepare("INSERT INTO $table_name (cart_id, $foreign_key, quantity) VALUES (?, ?, 1)");
                    $stmt->execute([$cart_id, $item_id]);
                }

                $pdo->commit();
            } catch (\PDOException $e) {
                $pdo->rollBack();
                error_log("Cart update error: " . $e->getMessage());
            }
        }

        $referer = $_SERVER['HTTP_REFERER'] ?? '../views/orders.php';
        header("Location: $referer");
        exit;
    }

    public function applyCoupon() {
        global $pdo;
        require_once __DIR__ . '/../db_connect.php';
        require_once __DIR__ . '/../models/Cart.php';
        require_once __DIR__ . '/../models/Coupon.php';

        $coupon_code = $_POST['coupon'];
        $coupon = Coupon::getCoupon($coupon_code);

        if ($coupon !== false) {
            $uid = $_SESSION['user']['id'];
            $cart_id = Cart::getUserCartId($uid);

            Coupon::addCouponToCart($coupon['id'], $cart_id);
        }
        header("Location: /orders");
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
        exit;
    }
}
