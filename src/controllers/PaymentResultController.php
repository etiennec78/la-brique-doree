<?php

class PaymentResultController extends Controller {
  public function index() {
    if (!isset($_SESSION['user'])) {
        header('Location: /login');
        exit();
    }

    global $pdo;
    include_once __DIR__ . '/../db_connect.php';
    include_once __DIR__ . '/../getapikey.php';

    $trans = $_GET['transaction'] ?? 'error';
    $montant = $_GET['montant'] ?? 'error';
    $vendeur = $_GET['vendeur'] ?? 'error';
    $status_bank = $_GET['status'] ?? 'error'; 
    $control_bank = $_GET['control'] ?? 'error';
    $cart_id = $_GET['cart_id'] ?? 0;

    $api_key = getAPIKey($vendeur);
    $control_check = md5($api_key . "#" . $trans . "#" . $montant . "#" . $vendeur . "#" . $status_bank . "#");

    $isSuccess = ($status_bank === 'accepted' && $control_bank === $control_check);

    if ($isSuccess) {
        try {
            $user_id = $_SESSION['user']['id'];
            require_once __DIR__ . '/../models/Cart.php';
            require_once __DIR__ . '/../models/Order.php';

            Cart::markCartAsPaid($cart_id, $user_id);

            if (!Order::checkOrderExistsByCartId($cart_id)) {
                $is_takeaway = isset($_GET['is_takeaway']) ? (int)$_GET['is_takeaway'] : 0;
                $takeaway_time_str = isset($_GET['takeaway_time']) && !empty($_GET['takeaway_time']) ? $_GET['takeaway_time'] : null;
                $takeaway_time = $takeaway_time_str ? date('Y-m-d ') . $takeaway_time_str . ':00' : null;

                $order_status = 1; // default: paid
                $cook_id = Order::getAvailableStaff("restaurateur");
                if ($cook_id != null) {
                    $order_status = 2; // preparing
                }
                
                Order::createOrder($cart_id, $user_id, $order_status, $cook_id, $is_takeaway, $takeaway_time);
            }
        } catch (Exception $e) {
            $erreur = "Erreur de base de données : " . $e->getMessage();
        }

        $title = "COMMANDE VALIDEE !";
        $message = "Merci pour votre confiance. <br> Vos briques sont en cours d'assemblage.";
        $icon = "/assets/images/favicon.png";
        $statusClass = "payment-success";
    } else {
        $title = "PAIEMENT ECHOUE";
        $message = "Mince ! Un problème est survenu lors de la transaction. <br> Aucune brique n'a été prélevée de votre compte.";
        $icon = "/assets/images/cart.svg"; 
        $statusClass = "payment-error";
    }

    $this->render('payment_result', [
        'isSuccess' => $isSuccess,
        'title' => $title,
        'message' => $message,
        'icon' => $icon,
        'statusClass' => $statusClass
    ]);
  }
}
