<?php
session_start();

if (isset($_GET['status'])) {
    $status = $_GET['status']; 
} else {
    $status = 'error'; 
}

if ($status === 'success') {
    $title = "COMMANDE VALIDEE !";
    $message = "Merci pour votre confiance. </br> Vos briques sont en cours d'assemblage.";
    $icon = "./images/favicon.png";
    $statusClass = "payment-success";
} else {
    $title = "PAIEMENT ECHOUE";
    $message = "Mince ! Un problème est survenu lors de la transaction. <br> Aucune brique n'a été prélevée de votre compte.";
    $icon = "./images/cart.svg"; 
    $statusClass = "payment-error";
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Résultat du paiement - La Brique Dorée</title>
    <link rel="icon" type="image/x-icon" href="./images/favicon.png">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div id="main-header">
            <img id="logo" src="./images/LOGO.png" alt="Logo">
            <h1>LA BRIQUE DORÉE</h1>
            <video class="video-background" autoplay muted loop>
                <source src="./images/header_background.mp4" type="video/mp4">
            </video>
        </div>
    </header>

    <main>
        <div class="result-container <?php echo $statusClass; ?>">
            <img src="<?php echo $icon; ?>" alt="Icône" class="result-icon">
            <h2><?php echo $title; ?></h2>
            <p><?php echo $message; ?></p>

            <div class="btn-group">
                <?php if ($status === 'success'): ?>
                    <a href="index.php" class="basic-btn">RETOURNER À L'ACCUEIL</a>
                <?php else: ?>
                    <a href="commands.php" class="basic-btn">RÉESSAYER</a>
                    <a href="index.php" class="navbarbutton">Annuler</a>
                <?php endif; ?>
            </div>
        </div>
    </main>
</body>
</html>