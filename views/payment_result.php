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
                <button onclick="location.href='/'" type="button" class="basic-btn gray-btn" id="cancel"><?php echo $isSuccess ? "Retourner à l'accueil" : 'Annuler'; ?></button>
                <button onclick="location.href='/order<?php echo $isSuccess ? '_tracking' : 's'; ?>'" type="button" class="basic-btn" id="retry"><?php echo $isSuccess ? 'Suivre ma commande' : 'Réessayer'; ?></button>
            </div>
        </div>
    </main>
</body>
</html>
