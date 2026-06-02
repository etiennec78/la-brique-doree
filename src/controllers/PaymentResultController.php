<?php

class PaymentResultController extends Controller {
  public function index() {
    /*
        
     INPUT :
             
        None
      
     OUTPUT :

        None

      
     SUMMARY :
        
        Validates the bank transaction safety parameters using md5 checksum controls, updates the cart payment status, establishes delivery or takeaway timing constraints, assigns an available cook if present, and executes the payment result view presentation.

    */
    if (!isset($_SESSION['user'])) {
        header('Location: /login');
        exit();
    }

    global $pdo;
    include_once __DIR__ . '/../models/Cart.php';
    include_once __DIR__ . '/../db_connect.php';
    include_once __DIR__ . '/../getapikey.php';

    $cart_count = Cart::getCartCount();

    $trans = $_GET['transaction'] ?? 'error';
    $montant = $_GET['montant'] ?? 'error';
    $vendeur = $_GET['vendeur'] ?? 'error';
    $status_bank = $_GET['status'] ?? 'error'; 
    $control_bank = $_GET['control'] ?? 'error';
    $cart_id = $_GET['cart_id'] ?? 0;

    $api_key = getAPIKey($vendeur);
    $control_check = md5($api_key . "#" . $trans . "#" . $montant . "#" . $vendeur . "#" . $status_bank . "#");

    $isSuccess = ($_SESSION['free_order'] || ($status_bank === 'accepted' && $control_bank === $control_check));

    if ($isSuccess) {
        try {
            $user_id = $_SESSION['user']['id'];
            require_once __DIR__ . '/../models/Cart.php';
            require_once __DIR__ . '/../models/Order.php';

            if ($_SESSION['free_order']) {
                $cart_id = Cart::getUserCartId($user_id);
            }

            Cart::markCartAsPaid($cart_id, $user_id);

            if (!Order::checkOrderExistsByCartId($cart_id)) {
                
                if ($_SESSION['free_order']) {
                    $is_takeaway = $_SESSION['free_takeaway'];
                    $takeaway_time_str = $_SESSION['free_time'];
                }

                else {
                    $is_takeaway = isset($_GET['is_takeaway']) ? (int)$_GET['is_takeaway'] : 0;
                    $takeaway_time_str = isset($_GET['takeaway_time']) && !empty($_GET['takeaway_time']) ? $_GET['takeaway_time'] : null;
                }

                $takeaway_time = $takeaway_time_str ? date('Y-m-d ') . $takeaway_time_str . ':00' : null;

                $order_status = 1; // default: paid
                $cook_id = Order::getAvailableStaff("cook");
                if ($cook_id != null) {
                    $order_status = 2; // preparing
                }
                
                Order::createOrder($cart_id, $user_id, $order_status, $cook_id, $is_takeaway, $takeaway_time);
            }
        } catch (Exception $error) {
            $_SESSION['error'] = 'Erreur de base de données : ' . $error->getMessage();
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
        'cart_count' => $cart_count,
        'isSuccess' => $isSuccess,
        'title' => $title,
        'message' => $message,
        'icon' => $icon,
        'statusClass' => $statusClass
    ]);
  }
}
