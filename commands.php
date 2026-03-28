<?php session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>La Brique Dorée</title>
    <link rel="icon" type="image/x-icon" href="./images/favicon.png">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="food-cards.css">
    <link rel="stylesheet" href="commands.css">
</head>
<body>
    <header>
        <div id="main-header">
            <img id="logo" src="./images/LOGO.png" alt="Logo d'une brique LEGO dorée">
            <h1>COMMANDE</h1>
            <a href="commands.php">
                <img id="cart" class="icon" src="./images/cart.svg" alt="Icône de panier de courses">
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
              <a href="profile.php" id="navbarbutton">Mon Profil</a>

          <?php if ($_SESSION['user']['role'] === 'administrator'): ?>
              <a href="admin.php" id="navbarbutton">Panel Admin</a>
              
          <?php elseif ($_SESSION['user']['role'] === 'restaurateur'): ?>
              <a href="restaurateur.php" id="navbarbutton">Gestion Commandes</a>
              
          <?php elseif ($_SESSION['user']['role'] === 'delivery_person'): ?>
              <a href="delivery.php" id="navbarbutton">Mes Livraisons</a>
          <?php endif; ?>

          <a href="logout.php" id="navbarbutton" style="color: #ff4d4d;">Déconnexion</a>

          <?php else: ?>
              <a href="login.php" id="navbarbutton">Connexion</a>
          <?php endif; ?>
        </section>

    </header>
      <section class="cart-page">
        <div id="cart-content">
          <div class="food-section" name="Éléments du panier">
          <h2>~ Éléments du panier ~</h2>
          <section class="bento">
            <?php
            include_once 'db_connect.php';

            try {

              $stmt = $pdo->prepare("SELECT name, price, description, image_path FROM food f");
              $stmt->execute();
              $food_items = $stmt->fetchAll();
              foreach($food_items as $food) {
                $name = $food['name'];
                $description = $food['description'];
                $price = number_format($food['price'], 2, ",");
                $image_path = $food['image_path'];

                echo '<article class="description" description="'. $description. '" price="'. $price .'€" style="background-image: url('. $image_path .');">
                <h3>'. $name .'</h3>
                <div class="nb-selector">
                  <button class="remove-from-cart" type="button" aria-label="Retirer du panier">-</button>
                  <input type="number" class="amount" min="0" max="9" value="0"/>
                  <button class="add-to-cart" type="button" aria-label="Ajouter au panier">+</button>
                </div>
                </article>';
              }
            } catch (\PDOException $e) {
              $erreur = "Erreur de base de données : " . $e->getMessage();
            }
            ?>

            </section>
          </div>
        </div>
        <div id="cart-bar">
          <h2>Votre panier</h2>
          <p>
            • Crabe (21,70€)<br/>
            • Bento (11,50€)
          <p>
          <p>Total: 33,20€</p>
          <button id="checkout" type="button">Payer</button>
        </div>
      </section>
    </main>

    <footer>
        <div id="contact">
            <h4>Venez vivre l'expérience LEGO</h4>
            <p>
                La Brique Dorée
                <br/>
                <a href="https://www.google.fr/maps/dir//Lego+House,+Ole+Kirks+Plads+1,+7190+Billund,+Danemark">1 Place Ole Kirks, 7190 Billund, Danemark</a>
                <br/><br/>
                <a href="mailto:contact@labriquedoree.fr">contact@labriquedoree.fr</a>
                <br/>
                <a href="tel:+33134251010">01 34 25 10 10</a>
                <br/><br/>
                <a href="https://www.google.com/search?tbm=lcl&kgmid=/g/11bwcc8tz6&rldimm=16393462302552454915#lkt=LocalPoiReviews">Google</a>,
                <a href="https://www.tripadvisor.fr/Attraction_Review-g189531-d12928696-Reviews-LEGO_House-Billund_South_Jutland_Jutland.html">Tripadvisor</a>,
                <a href="https://www.instagram.com/legohouse">Insta</a>,
                <a href="https://www.facebook.com/OfficialLEGOHOUSE">Facebook</a>
            </p>
        </div>
        <div id="hours">
            <h4>Horaires</h4>
            <p>
                <b>Mardi – Jeudi</b>
                <br/>12h–14h / 19h–21h30
                <br/><br/>
                <b>Mercredi</b>
                <br/>12h–14h
                <br/><br/>
                <b>Vendredi – Samedi</b>
                <br/>12h–14h / 19h–22h
                <br/><br/>
                <b>Dimanche – Lundi : Fermé</b>
            </p>
        </div>
        <div id="map">
            <h4>Nous trouver</h4>
            <iframe src="https://www.openstreetmap.org/export/embed.html?bbox=9.113277196884157%2C55.72994659971866%2C9.11605328321457%2C55.73135269752343&marker=55.730662,9.114866&amp;layer=mapnik"></iframe>
        </div>
    </footer>
</body>
</html>
