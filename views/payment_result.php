<?php
global $pdo;
include_once __DIR__ . '/../src/db_connect.php';
include_once __DIR__ . '/../src/getapikey.php';

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
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Résultat du paiement - La Brique Dorée</title>
    <link rel="icon" type="image/x-icon" href="/assets/images/favicon.png">
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/payment_result.css">
</head>
<body>
    <header>
        <div id="main-header">
            <img id="logo" src="/assets/images/LOGO.png" alt="Logo">
            <h1>LA BRIQUE DORÉE</h1>
            <video class="video-background" autoplay muted loop>
                <source src="/assets/images/header_background.mp4" type="video/mp4">
            </video>
        </div>
    </header>

    <main>
        <div class="result-container <?php echo $statusClass; ?>">
            <img src="<?php echo $icon; ?>" alt="Icône" class="result-icon">
            <h2><?php echo $title; ?></h2>
            <p><?php echo $message; ?></p>

            <div class="btn-group">
                <?php if ($isSuccess): ?>
                    <button onclick="location.href='/'" type="button" class="basic-btn">Retourner à l'accueil</button>
                <?php else: ?>
                    <button onclick="location.href='/'" type="button" class="basic-btn" id="cancel">Annuler</button>
                    <button onclick="location.href='/orders'" type="button" class="basic-btn" id="retry">Réessayer</button>
                <?php endif; ?>
            </div>
        </div>
    </main>
</body>
</html>
