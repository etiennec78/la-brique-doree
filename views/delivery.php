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
        <?php if (empty($deliveries)): ?>
            <div class="delivery-card">
                <div class="card-header">
                    <span class="client-name">En attente d'un nouveau client...</span>
                </div>
            </div>
        <?php else: ?>
            <?php foreach($deliveries as $delivery): ?>
                <div class="delivery-card">
                    <div class="card-header">
                        <span class="order-id">#<?= $delivery['id'] ?></span>
                        <span class="client-name"><?= getName($delivery) ?></span>
                    </div>
                    <button onclick="location.href='https://www.google.com/maps/search/?api=1&query=<?= urlencode(getAddress($delivery)) ?>'" class="basic-btn action-btn">Ouvrir dans Google Maps</button>
                    <div class="card-body">
                        <p class="address">📍 <?= getAddress($delivery) ?></p>
                        <?php if (isset($delivery['intercom_code'])): ?>
                            <p class="access">🔑 Code <?= $delivery['intercom_code'] ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="delivery-actions">
                        <form action="/confirm_delivery" method="POST">
                            <input type="hidden" name="order_id" value="<?= $delivery['id'] ?>">
                            <button type="submit" class="basic-btn action-btn btn-confirm">Confirmer</button>
                        </form>
                        <form action="/cancel_delivery" method="POST">
                            <input type="hidden" name="order_id" value="<?= $delivery['id'] ?>">
                            <button type="submit" class="basic-btn action-btn btn-cancel">Annuler</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </main>
</body>
</html>
