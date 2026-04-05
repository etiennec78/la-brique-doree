<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurateur - La Brique Dorée</title>
    <link rel="icon" type="image/x-icon" href="/assets/images/favicon.png">
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/restaurateur.css">
</head>
<body>
    <header>
        <div id="main-header">
            <img id="logo" src="/assets/images/LOGO.png" alt="Logo">
            <h1>RESTAURATEUR</h1>
            <a href="/orders">
                <img id="cart" class="icon" src="/assets/images/cart.svg">
                <p id="cart_items" class="bubble"><?= $cart_count ?></p>
            </a>
            <video class="video-background" autoplay muted loop>
                <source src="/assets/images/header_background.mp4" type="video/mp4">
            </video>
        </div>
        
        <section id="navbar-header">
            <a href="/" class="navbarbutton">Accueil</a>
            <a href="/products" class="navbarbutton">Nos produits</a>
            <a href="/reviews" class="navbarbutton">Avis</a>

            <?php if (isset($_SESSION['user'])): ?>
                <a href="/profile" class="navbarbutton">Mon Profil</a>

                <?php if ($_SESSION['user']['role'] === 'administrator'): ?>
                    <a href="/admin" class="navbarbutton">Panel Admin</a>
                    
                <?php elseif ($_SESSION['user']['role'] === 'restaurateur'): ?>
                    <a href="/restaurateur" class="navbarbutton">Gestion Commandes</a>
                    
                <?php elseif ($_SESSION['user']['role'] === 'delivery_person'): ?>
                    <a href="/delivery" class="navbarbutton">Mes Livraisons</a>
                <?php endif; ?>

                <a href="/logout" class="navbarbutton alert">Déconnexion</a>

            <?php else: ?>
                <a href="/login" class="navbarbutton">Connexion</a>
            <?php endif; ?>
        </section>

    </header>

    <main>
        <h2>~ Commandes ~</h2>
        
        <?php if(isset($_GET['success'])): ?>
            <p style="text-align: center; font-weight: bold;">Commande mise à jour avec succès !</p>
        <?php endif; ?>

        <div class="tabs">  
            <nav class="tabs-nav">
                <label class="tab-item">
                    <span>EN ATTENTE</span>
                    <input type="radio" id="pending-toggle" name="tabs-toggle" checked>
                </label>

                <label class="tab-item">
                    <span>EN LIVRAISON</span>
                    <input type="radio" id="delivery-toggle" name="tabs-toggle">
                </label>
            </nav>

            <div class="tab-content" id="pending-content">
                <table>
                    <tr>
                        <td><h3>COMMANDE</h3></td>
                        <td><h3 id="state">ETAT</h3></td>
                    </tr>

                    <?php if (empty($pending_orders)): ?>
                        <tr><td colspan="2" style="text-align:center;">Aucune commande en attente.</td></tr>
                    <?php endif; ?>

                    <?php foreach ($pending_orders as $order): ?>
                    <tr>
                        <td><span>Commande #<?= $order['id'] ?> (<?= getName($order) ?>)</span></td>
                        <td>
                            <form action="/assign_order" method="POST">
                                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                <button id="manage" type="submit" class="basic-btn">Prêt !</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>

            <div class="tab-content" id="delivery-content">
                <table>
                    <tr>
                        <td><h3>COMMANDE</h3></td>
                        <td><h3 id="state">ETAT</h3></td>
                    </tr>

                    <?php if (empty($delivery_orders)): ?>
                        <tr><td colspan="2" style="text-align:center;">Aucune livraison en cours.</td></tr>
                    <?php endif; ?>

                    <?php foreach ($delivery_orders as $order): ?>
                    <tr>
                        <td><span>Commande #<?= $order['id'] ?> (<?= getName($order) ?>)</span></td>
                        <td>
                            <?php if (isset($order['is_takeaway']) && $order['is_takeaway']): ?>
                                <form action="/finish_takeaway" method="POST">
                                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                    <button type="submit" class="basic-btn" style="padding:5px 10px; cursor:pointer;">Récupéré !</button>
                                </form>
                            <?php else: ?>
                                <label class="selection">
                                    <span>En route</span>
                                    <input type="checkbox" checked disabled/> 
                                </label>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
    </main>
</body>
</html>


