<?php

class RestaurateurController extends Controller {
    public function index() {
        require_once __DIR__ . '/../models/Cart.php';
        include_once __DIR__ . '/../get_name.php';

        $cart_count = Cart::getCartCount();

        $this->render('restaurateur', ['cart_count' => $cart_count, 'get_name' => 'getName']);
    }

    public function assignOrder() {
        global $pdo;
        require_once __DIR__ . '/../db_connect.php';

        if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 2) {
            header('Location: /login');
            exit();
        }

        if (isset($_POST['order_id']) && isset($_POST['delivery_person_id'])) {

            $order_id = $_POST['order_id'];
            $delivery_id = $_POST['delivery_person_id'];

            try {
                $stmt = $pdo->prepare(
                    "UPDATE orders
                    SET delivery_person_id = ?,
                    order_status_id = 3
                    WHERE id = ?
                ");
                $stmt->execute([$delivery_id, $order_id]);

                header('Location: /restaurateur?success=assigned');
                exit();

            } catch (PDOException $e) {
                $erreur = "Erreur de base de données : " . $e->getMessage();
            }
        } else {
            header('Location: /restaurateur');
            exit();
        }
    }
}
