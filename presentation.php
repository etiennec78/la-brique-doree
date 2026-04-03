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
    <title>La Brique Dorée</title>
    <link rel="icon" type="image/x-icon" href="./images/favicon.png">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="food-cards.css">
    <link rel="stylesheet" href="presentation.css">
</head>
<body>
    <header>
        <div id="main-header">
            <img id="logo" src="./images/LOGO.png" alt="Logo d'une brique LEGO dorée">
            <h1>NOS PRODUITS</h1>
            <a href="commands.php">
                <img id="cart" class="icon" src="./images/cart.svg" alt="Icône de panier de courses">
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
      <input type="checkbox" class="filter" id="crustacean" checked />
      <input type="checkbox" class="filter" id="fish" checked />
      <input type="checkbox" class="filter" id="gluten" checked />
      <input type="checkbox" class="filter" id="milk" checked />
      <input type="checkbox" class="filter" id="sesame" checked />
      <input type="checkbox" class="filter" id="egg" checked />
      <input type="checkbox" class="filter" id="soy" checked />
      <input type="checkbox" class="filter" id="nut" checked />
      <input type="checkbox" class="filter" id="sulfite" checked />

      <details class="filters-panel">
        <summary>
          <img src="./images/filter.svg" alt="Filtres">
        </summary>
        <aside class="filters-menu" aria-label="Filtres allergenes">
          <div class="filters-list">
            <label for="crustacean">Crustacé</label>
            <label for="fish">Poisson</label>
            <label for="gluten">Gluten</label>
            <label for="milk">Lait</label>
            <label for="sesame">Sésame</label>
            <label for="egg">Œuf</label>
            <label for="soy">Soja</label>
            <label for="nut">Fruit à coque</label>
            <label for="sulfite">Sulfite</label>
          </div>
        </aside>
      </details>

      <section class="menu-content">
        <?php
          try {
            echo '<div class="food-section" name="Menus">
              <h2>~ Nos Menus ~</h2>
              <section class="bento">';

                $stmtMenu = $pdo->prepare("SELECT id, name, price, description FROM menu ORDER BY id ASC");
                $stmtMenu->execute();
                $menus = $stmtMenu->fetchAll();

                foreach($menus as $menu) {
                    $id = $menu['id'];
                    $name = $menu['name'];
                    $price = number_format($menu['price'], 2, ",");
                    $description = $menu['description'];

                    // On récupère les images spécifiquement pour ce menu
                    $stmtImages = $pdo->prepare("
                      SELECT f.image_path 
                      FROM food f
                      JOIN menu_food mf ON f.id = mf.food_id
                      WHERE mf.menu_id = ?
                    ");
                    
                    $stmtImages->execute([$id]);
                    $images = $stmtImages->fetchAll(PDO::FETCH_COLUMN);

                    echo '<article class="description" description="'. $description . '" price="'. $price .'€">
                            <div class="menu-grid" style="display: flex; height: 100px; gap: 2px;">';
                                
                                foreach($images as $img_path) {
                                    echo '<div style="flex: 1; background-image: url('. $img_path .'); background-size: cover; background-position: center;"></div>';
                                }

                    echo '  </div>
                            <h3>'. $name .'</h3>
                            <form action="update_cart.php" method="POST">
                              <input type="hidden" name="item_id" value="'. $id .'">
                              <input type="hidden" name="item_type" value="menu">
                              <input type="hidden" name="action" value="add">
                              <button class="add-to-cart" type="submit" aria-label="Ajouter au panier">+</button>
                            </form>
                          </article>';
                }
                echo '</section>
                      </div>'; 

            $stmt = $pdo->prepare("SELECT id, name FROM food_type ORDER BY id ASC");
            $stmt->execute();
            $food_types = $stmt->fetchAll();

            foreach($food_types as $food_type) {
              echo '<div class="food-section" name="'. $food_type['name'] .'">
              <h2>~ '. $food_type['name'] .' ~</h2>
              <section class="bento">';

              $stmt = $pdo->prepare("SELECT id, name, price, description, image_path FROM food f WHERE f.food_type = ?");
              $stmt->execute([$food_type['id']]);
              $food_items = $stmt->fetchAll();
              foreach($food_items as $food) {
                $id = $food['id'];
                $name = $food['name'];
                $description = $food['description'];
                $price = number_format($food['price'], 2, ",");
                $image_path = $food['image_path'];

                echo '<article class="description" description="'. $description. '" price="'. $price .'€" style="background-image: url('. $image_path .');">
                <h3>'. $name .'</h3>
                <form action="update_cart.php" method="POST">
                    <input type="hidden" name="item_id" value="'. $id .'">
                    <input type="hidden" name="item_type" value="food">
                    <input type="hidden" name="action" value="add">
                    <button class="add-to-cart" type="submit" aria-label="Ajouter au panier">+</button>
                </form>
                </article>';
              }

              echo "</section>
              </div>";
            }

          } catch (\PDOException $e) {
            $erreur = "Erreur de base de données : " . $e->getMessage();
          }
        ?>
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
