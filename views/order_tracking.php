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
            <div class="step active">
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

        <?php if ($order['status'] != 1): ?>
        <div class="delivery-info">
            <?php if ($order['status'] == 2): ?>
                <p>Cuisinier : <?php echo $get_name($cook); ?></p>
            <?php elseif ($order['status'] == 3): ?>
                <p>Livreur : <?php echo $get_name($delivery_person); ?></p>
                <p>Arrivée : XX min</p>
                <iframe id="map" src="https://www.openstreetmap.org/export/embed.html?bbox=9.113277196884157%2C55.72994659971866%2C9.11605328321457%2C55.73135269752343&marker=55.730662,9.114866&amp;layer=mapnik"></iframe>
            <?php else: ?>
                <p>Commande livrée !<br/>Merci pour votre confiance. N'hésitez pas à nous laisser un avis !</p>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <div class="tracking-actions">
            <button onclick="location.href='/'" class="basic-btn <?php if ($order['status'] == 4) echo 'gray-btn'; ?>">Retour à l'accueil</button>
            <?php if ($order['status'] == 4): ?>
                <button onclick="location.href='/reviews'" class="basic-btn">Laisser un avis</button>
            <?php endif; ?>
        </div>

    </main>

    </body>
</html>
