<?php
global $pdo;
include_once __DIR__ . '/../src/db_connect.php';
include_once __DIR__ . '/../src/get_cart_count.php';

// Les commandes en attente (Statut 1 ou 2)
$stmt_waiting = $pdo->prepare("SELECT o.id, u.first_name FROM orders o JOIN users u ON o.customer_id = u.id WHERE o.order_status_id IN (1, 2)");
$stmt_waiting->execute();
$waiting_orders = $stmt_waiting->fetchAll();

// les commandes déjà parties (Statut 3 ou 4)
$stmt_delivery = $pdo->prepare("SELECT o.id, u.first_name FROM orders o JOIN users u ON o.customer_id = u.id WHERE o.order_status_id IN (3, 4)");
$stmt_delivery->execute();
$delivery_orders = $stmt_delivery->fetchAll();

// La liste des livreurs
$stmt_users = $pdo->prepare("SELECT id, first_name FROM users WHERE role_id = 4");
$stmt_users->execute();
$deliverers = $stmt_users->fetchAll();

?>
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
                <p id="cart_items" class="bubble"><?php echo $cart_count; ?></p>
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
                    <input type="radio" id="waiting-toggle" name="tabs-toggle" checked>
                </label>

                <label class="tab-item">
                    <span>EN LIVRAISON</span>
                    <input type="radio" id="delivery-toggle" name="tabs-toggle">
                </label>
            </nav>

            <div class="tab-content" id="waiting-content">
                <table>
                    <tr>
                        <td><h3>COMMANDE</h3></td>
                        <td><h3 id="state">ETAT / LIVREUR</h3></td>
                    </tr>

                    <?php if (empty($waiting_orders)): ?>
                        <tr><td colspan="2" style="text-align:center;">Aucune commande en attente.</td></tr>
                    <?php endif; ?>

                    <?php foreach ($waiting_orders as $order): ?>
                    <tr>
                        <td><span>Commande #<?php echo $order['id']; ?> (<?php echo $order['first_name']; ?>)</span></td>
                        <td>
                            <form action="../src/assign_order.php" method="POST" style="display: flex; align-items: center; gap: 10px;">
                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                
                                <select name="delivery_person_id" required style="padding: 5px; border-radius: 5px; border: 1px solid #FFD700; background: #000; color: #fff;">
                                    <option value="">-- Choisir Livreur --</option>
                                    <?php foreach ($deliverers as $d): ?>
                                        <option value="<?php echo $d['id']; ?>"><?php echo ($d['first_name']); ?></option>
                                    <?php endforeach; ?>
                                </select>

                                <label class="selection">
                                    <span>Prêt !</span>
                                    <input type="radio" name="confirm" onchange="this.form.submit()"/> 
                                </label>
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
                        <td><span>Commande #<?php echo $order['id']; ?> (<?php echo ($order['first_name']); ?>)</span></td>
                        <td>
                            <label class="selection">
                                <span>En route...</span>
                                <input type="checkbox" checked disabled/> 
                            </label>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
    </main>
</body>
</html>


