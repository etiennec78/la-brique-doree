<?php session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Livreur - La Brique Dorée</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="delivery.css">
</head>
<body>
    <header>
        <div id="main-header">
            <img id="logo" src="./images/LOGO.png" alt="Logo">
            <h1>LIVRAISON</h1>
        </div>
        <nav id="navbar-header">
            <a href="index.php" id="navbarbutton">Accueil</a>
            <a href="avis.php" id="navbarbutton">Avis</a>
            <a href="delivery.php" id="navbarbutton" style="color:var(--solid-gold); border-bottom:2px solid var(--solid-gold)">Livraison</a>
        </nav>
    </header>

    <main>
        <div class="map-box">
            <iframe src="https://www.openstreetmap.org/export/embed.html?bbox=2.067717435883694%2C49.03406993317959%2C2.0714322953693203%2C49.03570705430734&amp;layer=mapnik"></iframe>
        </div>

        <h2 class="section-title">Commandes à livrer</h2>

        <div class="delivery-card">
            <div class="card-header">
                <span class="order-id">#402</span>
                <span class="client-name">MARC ANTOINE</span>
            </div>
            <div class="card-body">
                <p class="address">📍 15 AVE DES CHAMPS, CERGY</p>
                <p class="access">🔑 ÉTAGE 3 - CODE: 123</p>
            </div>
            <button class="basic-btn action-btn"> CONFIRMER LIVRAISON</button>
        </div>

        <div class="delivery-card">
            <div class="card-header">
                <span class="order-id">#405</span>
                <span class="client-name">LUCIE BERNARD</span>
            </div>
            <div class="card-body">
                <p class="address">📍 3 RUE DU PORT, PONTOISE</p>
                <p class="access">🔑 RDC - SONNER "BERNARD"</p>
            </div>
            <button class="basic-btn action-btn">CONFIRMER LIVRAISON</button>
        </div>
    </main>
</body>
</html>
