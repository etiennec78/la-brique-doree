<?php
$status = 2; // 1=Reçue, 2=Prépa, 3=En route, 4=Livrée
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Suivi - La Brique Dorée</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>SUIVI DE COMMANDE</h1>
    </header>

    <div class="tracking-container">
        <h2>Commande #1111</h2>
        
        <div class="stepper">
            <div class="step <?php if ($status >= 1) { echo 'active'; } ?>">
                <div class="circle">📩</div>
                <p>Reçue</p>
                <div class="line"></div>
            </div>

            <div class="step <?php if ($status >= 2) { echo 'active'; } ?>">
                <div class="circle">🍳</div>
                <p>En préparation</p>
                <div class="line"></div>
            </div>

            <div class="step <?php if ($status >= 3) { echo 'active'; } ?>">
                <div class="circle">🚴</div>
                <p>En route</p>
                <div class="line"></div>
            </div>

            <div class="step <?php if ($status >= 4) { echo 'active'; } ?>">
                <div class="circle">😋</div>
                <p>Livrée</p>
            </div>
        </div>

        <div class="delivery-info">
            <p>Livreur : Jean le livreur</p>
            <p>Arrivée : 15 min</p>
        </div>


        <a href="index.php" class="navbarbutton basic-btn">Retour à l'accueil</a>

    </main>

    </body>
</html>