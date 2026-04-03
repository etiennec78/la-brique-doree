<?php
session_start();
include_once 'db_connect.php';
include_once 'get_cart_count.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurateur - La Brique Dorée</title>
    <link rel="icon" type="image/x-icon" href="./images/favicon.png">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="restaurateur.css">
</head>
<body>
    <header>
        <div id="main-header">
            <img id="logo" src="./images/LOGO.png" alt="Logo">
            <h1>RESTAURATEUR</h1>
            <a href="commands.php">
                <img id="cart" class="icon" src="./images/cart.svg">
                <p id="cart_items" class="bubble"><?php echo $cart_count; ?></p>
            </a>
            <video class="video-background" autoplay muted loop>
                <source src="./images/header_background.mp4" type="video/mp4">
            </video>
        </div>
        
        <section id="navbar-header">
            <a href="index.php" class="navbarbutton">Accueil</a>
            <a href="presentation.php" class="navbarbutton">Nos produits</a>
            <a href="reviews.php" class="navbarbutton">Avis</a>

            <?php if (isset($_SESSION['user'])): ?>
                <a href="profile.php" class="navbarbutton">Mon Profil</a>

            <?php if ($_SESSION['user']['role'] === 'administrator'): ?>
                <a href="admin.php" class="navbarbutton">Panel Admin</a>
                
            <?php elseif ($_SESSION['user']['role'] === 'restaurateur'): ?>
                <a href="restaurateur.php" class="navbarbutton">Gestion Commandes</a>
                
            <?php elseif ($_SESSION['user']['role'] === 'delivery_person'): ?>
                <a href="delivery.php" class="navbarbutton">Mes Livraisons</a>
            <?php endif; ?>

            <a href="logout.php" class="navbarbutton alert">Déconnexion</a>

            <?php else: ?>
                <a href="login.php" class="navbarbutton">Connexion</a>
            <?php endif; ?>
        </section>

    </header>

    <main>
        <h2>~ Commandes ~</h2>
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
                        <td><h3 id="state">ETAT</h3></td>
                    </tr>

                    <tr>
                        <td><span>Commande 1</span></td>
                        <td>
                            <label class="selection">
                                <span>Préparation</span>
                                <input type="radio" name="state-1" value="waiting" checked/> 
                            </label>                            
                            <label class="selection">
                                <span>Livraison</span>
                                <input type="radio" name="state-1" value="delivery"/>                                
                            </label>
                        </td>
                    </tr>

                    <tr>
                        <td><span>Commande 2</span></td>
                        <td>
                            <label class="selection">
                                <span>Préparation</span>
                                <input type="radio" name="state-2" value="waiting" checked/>                               
                            </label>
                            <label class="selection">
                                <span>Livraison</span>
                                <input type="radio" name="state-2" value="delivery"/>                               
                            </label>
                        </td>
                    </tr>

                    <tr>
                        <td><span>Commande 3</span></td>
                        <td>
                            <label class="selection">
                                <span>Préparation</span>
                                <input type="radio" name="state-3" value="waiting" checked/>                                
                            </label>
                            <label class="selection">
                                <span>Livraison</span>
                                <input type="radio" name="state-3" value="delivery"/>                                 
                            </label>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="tab-content" id="delivery-content">
                <table>
                    <tr>
                        <td><h3>COMMANDE</h3></td>
                        <td><h3 id="state">ETAT</h3></td>
                    </tr>

                    <tr>
                        <td><span>Commande 1</span></td>
                        <td>
                            <label class="selection">
                                <span>Livrée</span>
                                <input type="checkbox" name="state-1" value="delivered"/> 
                            </label>
                        </td>
                    </tr>

                    <tr>
                        <td><span>Commande 2</span></td>
                        <td>
                            <label class="selection">
                                <span>Livrée</span>
                                <input type="checkbox" name="state-2" value="delivered"/> 
                            </label>
                        </td>
                    </tr>

                    <tr>
                        <td><span>Commande 3</span></td>
                        <td>
                            <label class="selection">
                                <span>Livrée</span>
                                <input type="checkbox" name="state-3" value="delivered"/> 
                            </label>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </main>
</body>
</html>


