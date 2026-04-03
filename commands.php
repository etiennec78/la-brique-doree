<?php
session_start();
include_once 'db_connect.php';
include_once 'get_cart_count.php';
include_once 'getapikey.php'; 

$vendeur = 'MI-4_J'; 
$api_key = getAPIKey($vendeur); 
$transaction = uniqid();

$retour_url = "http://localhost/payment_result.php";

$total_price = 0;
$cart_details = [];
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
    <link rel="stylesheet" href="commands.css">
</head>
<body>
    <header>
        <div id="main-header">
            <img id="logo" src="./images/LOGO.png" alt="Logo d'une brique LEGO dorée">
            <h1>COMMANDE</h1>
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
      <section class="cart-page">
        <div id="cart-content">
          <div class="food-section" name="Éléments du panier">
            <h2>~ Éléments du panier ~</h2>
            <section class="bento">
              <?php
          
              if (isset($_SESSION['user'])) {
                  try {
                    $uid = $_SESSION['user']['id'];
                    
                    // Récupérer les plats du panier
                    $stmt_f = $pdo->prepare("
                      SELECT f.id as item_id, 'food' as item_type, f.name, f.price, f.description, f.image_path, cf.quantity
                      FROM cart c
                      JOIN cart_food cf ON c.id = cf.cart_id
                      JOIN food f ON cf.food_id = f.id
                      WHERE c.user_id = ? AND c.payment_status_id = 1
                  ");
                    $stmt_f->execute([$uid]);
                    $cart_foods = $stmt_f->fetchAll();

                    // Récupérer les menus du panier
                  $stmt_m = $pdo->prepare("
                      SELECT m.id as item_id, 'menu' as item_type, m.name, m.price, 'Menu complet' as description, 'images/LOGO.png' as image_path, cm.quantity
                      FROM cart c
                      JOIN cart_menu cm ON c.id = cm.cart_id
                      JOIN menu m ON cm.menu_id = m.id
                      WHERE c.user_id = ? AND c.payment_status_id = 1
                  ");
                    $stmt_m->execute([$uid]);
                    $cart_menus = $stmt_m->fetchAll();

                    $cart_items = array_merge($cart_foods, $cart_menus);

                    if (empty($cart_items)) {
                        echo '<p>Votre panier est vide.</p>';
                    }

                    foreach($cart_items as $item) {
                    $name = $item['name'];
                    $description = $item['description'];
                    $quantity = $item['quantity'];
                    $price_val = floatval($item['price']);
                    $price_str = number_format($price_val, 2, ",");
                    $image_path = $item['image_path'];
                    $item_id = $item['item_id'];
                    $item_type = $item['item_type'];

                    $total_price += $price_val * $quantity;
                    $cart_details[] = "$name ($price_str €) x$quantity";

                    echo '<article class="description" description="'. htmlspecialchars($description). '" price="'. $price_str .'€" style="background-image: url('. htmlspecialchars($image_path) .');">
                    <h3>'. htmlspecialchars($name) .'</h3>
                    <div class="nb-selector">
                      <button class="remove-from-cart" type="button" aria-label="Retirer du panier" onclick="updateCart('. $item_id .', \''. $item_type .'\', \'remove\')">-</button>
                      <input type="number" class="amount" min="0" max="9" value="'. $quantity .'"/>
                      <button class="add-to-cart" type="button" aria-label="Ajouter au panier" onclick="updateCart('. $item_id .', \''. $item_type .'\', \'add\')">+</button>
                    </div>
                    </article>';
                    }
                  } catch (\PDOException $e) {
                    echo "Erreur de base de données : " . $e->getMessage();
                }
                }
              $montant_cybank = number_format($total_price, 2, '.', ''); 
              $control = md5($api_key . "#" . $transaction . "#" . $montant_cybank . "#" . $vendeur . "#" . $retour_url . "#");
              ?>

            </section>
          </div>
        </div>
        <div id="cart-bar">
          <h2>Votre panier</h2>
          <p>
            <?php 
              if (empty($cart_details)) {
                  echo "Panier vide.";
              } else {
                echo "<ul><li/>";
                echo implode("<li/>", $cart_details);
                echo "</ul>";
              }
            ?>
          </p>
          <p>Total: <?php echo number_format($total_price, 2, ","); ?>€</p>
          
          <form action="https://www.plateforme-smc.fr/cybank/index.php" method="POST">
            <input type="hidden" name="transaction" value="<?php echo $transaction; ?>">
            <input type="hidden" name="montant" value="<?php echo $montant_cybank; ?>">
            <input type="hidden" name="vendeur" value="<?php echo $vendeur; ?>">
            <input type="hidden" name="retour" value="<?php echo $retour_url; ?>">
            <input type="hidden" name="control" value="<?php echo $control; ?>">
            <button id="checkout" type="submit" class="basic-btn" <?php if($total_price <= 0) echo 'disabled'; ?>>Payer</button>
          </form>
        </div>
      </section>
    </main>

    <script>
    function updateCart(itemId, itemType, action) {

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'update_cart.php';
        
        // Add item_id
        const idInput = document.createElement('input');
        idInput.type = 'hidden';
        idInput.name = 'item_id';
        idInput.value = itemId;
        form.appendChild(idInput);
        
        // Add item_type
        const typeInput = document.createElement('input');
        typeInput.type = 'hidden';
        typeInput.name = 'item_type';
        typeInput.value = itemType;
        form.appendChild(typeInput);

        // Add action
        const actionInput = document.createElement('input');
        actionInput.type = 'hidden';
        actionInput.name = 'action';
        actionInput.value = action;
        form.appendChild(actionInput);
        
        document.body.appendChild(form);
        form.submit();
    }
    </script>

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
