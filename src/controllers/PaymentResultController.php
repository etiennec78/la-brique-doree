<?php

class PaymentResultController extends Controller {
  public function index() {
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

            $updateCart = $pdo->prepare("UPDATE cart SET payment_status_id = 2 WHERE id = ? AND user_id = ?");
            $updateCart->execute([$cart_id, $user_id]);

            $checkOrder = $pdo->prepare("SELECT id FROM orders WHERE cart_id = ?");
            $checkOrder->execute([$cart_id]);
            
            if (!$checkOrder->fetch()) {
                $insertOrder = $pdo->prepare("INSERT INTO orders (cart_id, customer_id, order_status_id) VALUES (?, ?, 1)");
                $insertOrder->execute([$cart_id, $user_id]);
            }
        } catch (PDOException $e) {
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
