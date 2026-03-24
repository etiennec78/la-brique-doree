<?php session_start(); ?>
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
            <a href="commands.html">
                <img id="cart" class="icon" src="./images/cart.svg">
                <p id="cart_items" class="bubble">10</p>
            </a>
            <video class="video-background" autoplay muted loop>
                <source src="./images/header_background.mp4" type="video/mp4">
            </video>
        </div>
        
        <section id="navbar-header">
            <a href="index.php" id="navbarbutton">Accueil</a>
            <a href="presentation.php" id="navbarbutton">Nos produits</a>
            <a href="avis.php" id="navbarbutton">Avis</a>

            <?php if (isset($_SESSION['user'])): ?>
                <a href="profile.php" id="navbarbutton">Mon Profil (<?php echo $_SESSION['user']['prenom']; ?>)</a>

            <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                <a href="admin.php" id="navbarbutton">Panel Admin</a>
                
            <?php elseif ($_SESSION['user']['role'] === 'restaurateur'): ?>
                <a href="restaurateur.php" id="navbarbutton">Gestion Commandes</a>
                
            <?php elseif ($_SESSION['user']['role'] === 'livreur'): ?>
                <a href="delivery.php" id="navbarbutton">Mes Livraisons</a>
            <?php endif; ?>

            <a href="logout.php" id="navbarbutton" style="color: #ff4d4d;">Déconnexion</a>

            <?php else: ?>
                <a href="login.php" id="navbarbutton">Connexion</a>
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


