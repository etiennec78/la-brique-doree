<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Suivi - La Brique Dorée</title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/order_tracking.css">
</head>
<body>
    <header>
        <h1>SUIVI DE COMMANDE</h1>
    </header>

    <div class="tracking-container">
        <h2>Commande #<?php echo $order['id'] ?></h2>
        
        <div class="stepper">
            <div class="step <?php if ($order['status'] >= 1) { echo 'active'; } ?>">
                <div class="circle">📩</div>
                <p>Reçue</p>
                <div class="line"></div>
            </div>

            <div class="step <?php if ($order['status'] >= 2) { echo 'active'; } ?>">
                <div class="circle">🍳</div>
                <p>En préparation</p>
                <div class="line"></div>
            </div>

            <div class="step <?php if ($order['status'] >= 3) { echo 'active'; } ?>">
                <div class="circle">🚴</div>
                <p>En route</p>
                <div class="line"></div>
            </div>

            <div class="step <?php if ($order['status'] >= 4) { echo 'active'; } ?>">
                <div class="circle">😋</div>
                <p>Livrée</p>
            </div>
        </div>

        <div class="delivery-info">
            <p>Livreur : <?php echo $order['first_name'] . ' ' . $order['last_name']; ?></p>
            <p>Arrivée : XX min</p>
        </div>


        <a href="/" class="navbarbutton basic-btn">Retour à l'accueil</a>

    </main>

    </body>
</html>
