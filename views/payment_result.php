<?php
$title = "Résultat du paiement - La Brique Dorée";
$h1 = "LA BRIQUE DORÉE";
$show_cart = false;
$show_video = true;
$css_files = ['/css/payment_result.css'];
include __DIR__ . '/../includes/header.php';
?>
<main>
        <div class="result-container <?= $statusClass ?>">
            <img src="<?= $icon ?>" alt="Icône" class="result-icon">
            <h2><?= $title ?></h2>
            <p><?= $message ?></p>

            <div class="btn-group">
                <button onclick="location.href='/'" type="button" class="basic-btn gray-btn" id="cancel"><?= $isSuccess ? "Retourner à l'accueil" : 'Annuler' ?></button>
                <button onclick="location.href='/order<?= $isSuccess ? '_tracking' : 's' ?>'" type="button" class="basic-btn" id="retry"><?= $isSuccess ? 'Suivre ma commande' : 'Réessayer' ?></button>
            </div>
        </div>
    </main>
<?php include __DIR__ . '/../includes/footer.php'; ?>
