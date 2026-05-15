<?php

class ProfileController extends Controller {
    public function index($target_id = NULL) {
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit();
        }

        require_once __DIR__ . '/../models/Cart.php';
        require_once __DIR__ . '/../models/User.php';
        require_once __DIR__ . '/../models/Order.php';

        $uid = $_SESSION['user']['id'];

        if ($target_id == NULL)
            $target_id = $uid;

        $cart_count = Cart::getCartCount();
        $user_data = User::getUserInfo($target_id);
        $is_admin = User::isAdmin($uid);
        $all_orders = Order::getAllOrdersFromUser($target_id);

        foreach ($all_orders as &$order) {
            $order['items'] = Order::getOrderItems($order['id']);
        }

        foreach ($all_orders as &$order) {
            $order['cook'] = User::getUserInfo($order['cook_id']);
            $order['cook'] = $order['cook']['first_name'].' '.$order['cook']['last_name'];
            $order['delivery_person'] = User::getUserInfo($order['delivery_person_id']);
            $order['delivery_person'] = $order['delivery_person']['first_name'].' '.$order['delivery_person']['last_name'];
        }

        $this->render(
            'profile',
            [
                'cart_count' => $cart_count,
                'user_data' => $user_data,
                'target' => $target_id,
                'is_admin' => $is_admin,
                'uid' => $uid,
                'all_orders' => $all_orders
            ]
        );
    }

    public function updateProfile() {
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit();
        }

        global $pdo;
        require_once __DIR__ . '/../db_connect.php';
        require_once __DIR__ . '/../models/Location.php';
        require_once __DIR__ . '/../models/User.php';

        $uid = $_SESSION['user']['id'];

        $target = $uid;
        $user_banned = User::getUserData($uid, 'banned');
        if ($user_banned)
            return;

        $user_role = User::getUserData($uid, 'r.name');
        $is_admin = $user_role == 'administrator';

        try {
            if ($is_admin and array_key_exists('user_id', $_POST)) {
                $target = $_POST['user_id'];
            }
            if (isset($_POST['action']) && $_POST['action'] === 'ban' && $is_admin) {
                $stmt = $pdo->prepare("UPDATE users SET banned = 1 WHERE id = ?");
                $stmt->execute([$target]);
            }
            else if (isset($_POST['action']) && $_POST['action'] === 'unban' && $is_admin) {
                $stmt = $pdo->prepare("UPDATE users SET banned = 0 WHERE id = ?");
                $stmt->execute([$target]);
            }
            else if (array_key_exists('first_name', $_POST)) {
                $address_has_changed = Location::formAddressHasChanged($target, $_POST);
                if ($address_has_changed) {
                    // Get the coordinates of the delivery address
                    $coordinates = Location::getLocationCoord($_POST, $uid);
                    if (isset($coordinates['error'])) {
                        $error = $coordinates['error'];
                        error_log("Coordinates could not be found for user $uid: $error");
                    } else {
                        $users_data = User::setUserData($target, 'latitude', $coordinates['lat']);
                        $users_data = User::setUserData($target, 'longitude', $coordinates['lng']);
                    }
                }

                $birth_date = !empty($_POST['birth_date']) ? $_POST['birth_date'] : null;

                User::setAllUserData($_POST['first_name'], $_POST['last_name'], $_POST['street_nb'], $_POST['street_nb_suf'], $_POST['street'], $_POST['zip_code'], $_POST['phone'], $_POST['email'], $_POST['intercom_code'], $birth_date, $target);

                $_SESSION['user'] = array_merge($_SESSION['user'], $_POST);
            }
        } catch (\PDOException $e) {
            $erreur = "Erreur lors de la mise à jour : " . $e->getMessage();
        }

        $this->index($target);
    }
}
