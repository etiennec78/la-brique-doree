<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Livreur - La Brique Dorée</title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/delivery.css">
</head>
<body>
    <header>
        <div id="main-header">
            <img id="logo" src="/assets/images/LOGO.png" alt="Logo">
            <h1>LIVRAISON</h1>
        </div>
        <nav id="navbar-header">
            <a href="/" class="navbarbutton">Accueil</a>
            <a href="/reviews" class="navbarbutton">Avis</a>
            <a href="/delivery" class="navbarbutton">Livraison</a>
        </nav>
    </header>

    <main>
        <h2 class="section-title">Carte</h2>
        <div class="map-box">
            <iframe src="https://www.openstreetmap.org/export/embed.html?bbox=2.067717435883694%2C49.03406993317959%2C2.0714322953693203%2C49.03570705430734&amp;layer=mapnik"></iframe>
        </div>

        <h2 class="section-title">Commandes à livrer</h2>
        <?php foreach($deliveries as $delivery): ?>
            <div class="delivery-card">
                <div class="card-header">
                    <span class="order-id">#<?php echo $delivery['id']; ?></span>
                    <span class="client-name"><?php echo getName($delivery); ?></span>
                </div>
                <div class="card-body">
                    <p class="address">📍 <?php echo getAddress($delivery); ?></p>
                    <?php if (isset($delivery['intercom_code'])): ?>
                        <p class="access">🔑 Code <?php echo $delivery['intercom_code'] ?></p>
                    <?php endif; ?>
                </div>
                <form action="/confirm_delivery" method="POST">
                    <input type="hidden" name="order_id" value="<?php echo $delivery['id']; ?>">
                    <button id="confirm" type="submit" class="basic-btn action-btn">CONFIRMER LIVRAISON</button>
                </form>
            </div>
        <?php endforeach; ?>
    </main>
</body>
</html>
