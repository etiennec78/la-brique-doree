<?php
include_once __DIR__ . '/../src/getapikey.php';

$vendeur = 'MI-4_J'; 
$api_key = getAPIKey($vendeur); 
$transaction = uniqid();

$retour_url = "http://localhost/payment_result?cart_id=" . $cart_id;

$total_price = 0;
$reduction = 0;
$cart_details = [];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>La Brique Dorée</title>
    <link rel="icon" type="image/x-icon" href="/assets/images/favicon.png">
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/food-cards.css">
    <link rel="stylesheet" href="/css/orders.css">
</head>
<body>
    <header>
        <div id="main-header">
            <img id="logo" src="/assets/images/LOGO.png" alt="Logo d'une brique LEGO dorée">
            <h1>COMMANDE</h1>
            <a href="/orders">
                <img id="cart" class="icon" src="/assets/images/cart.svg" alt="Icône de panier de courses">
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
      <section class="cart-page">
        <div id="cart-content">
          <h2>~ Éléments du panier ~</h2>
          <section class="bento">
            <?php
            require_once __DIR__ . '/../src/models/User.php';
            require_once __DIR__ . '/../src/models/Menu.php';

            if (isset($_SESSION['user'])) {
              $uid = $_SESSION['user']['id'];

              $cart_menus = Cart::getCartMenus($uid);
              $cart_foods = Cart::getCartFoods($uid);

              $cart_has_food = count($cart_foods) > 0;
              if (count($cart_menus) <= 0 and !$cart_has_food) {
                echo '<p>Votre panier est vide.</p>';
              } else {

                // Boucler pour chaque menu + 1 (plats individuels)
                for($i = 0; $i < count($cart_menus) + $cart_has_food; $i++) {
                  $individual = $i == count($cart_menus);
                  echo '<div>';

                  if ($individual) {
                    $foods = $cart_foods;
                    $menu_name = "Plats individuels";
                    $name_suffix = "";
                  } else {

                    // Ajouter le menu dans la liste de paiements
                    $menu = $cart_menus[$i];
                    $name = $menu['name'];
                    $price_val = floatval($menu['price']);
                    $price_str = number_format($price_val, 2, ",");
                    $quantity = $menu['quantity'];

                    $total_price += $price_val * $quantity;
                    $cart_details[] = "$name (".$price_str."€) x$quantity";

                    $foods = Menu::getMenuFoods($menu['id']);

                    $menu_name = $menu['name'];
                    $name_suffix = $quantity > 1 ? " (x$quantity)" : "";
                  }

                  echo '<h2>'. htmlspecialchars($menu_name) . $name_suffix .'</h2>';
                  echo '<div class="items-grid">';

                  foreach($foods as $food) {
                    $name = $food['name'];
                    $description = $food['description'];
                    $price_val = floatval($food['price']);
                    $price_str = number_format($price_val, 2, ",");
                    $image_path = 'assets/' . $food['image_path'];
                    $food_id = $food['item_id'];

                    if ($individual) {
                      $quantity = $food['quantity'];
                      $total_price += $price_val * $quantity;
                      $cart_details[] = "$name (".$price_str."€) x$quantity";
                    }

                    echo '<article class="description" description="'. htmlspecialchars($description). '" price="'. $price_str .'€" style="background-image: url('. htmlspecialchars($image_path) .');">
                    <h3>'. htmlspecialchars($name) .'</h3>';

                    if ($individual) {
                      echo '<form method="POST" action="/update_cart" style="display:inline; margin:0; padding:0;">
                      <input type="hidden" name="item_id" value="'. $food_id .'">
                      <input type="hidden" name="item_type" value="food">
                      <div class="nb-selector">
                      <button class="remove-from-cart" type="submit" name="action" value="remove" aria-label="Retirer du panier">-</button>
                      <input type="number" class="amount" name="amount" min="0" max="9" value="'. $quantity .'"/>
                      <button class="add-to-cart" type="submit" name="action" value="add" aria-label="Ajouter au panier">+</button>
                      </div>
                      </form>';
                    }
                    echo '</article>';
                  }
                  echo '</div>';
                  echo '</div>';
                }
                if ($coupon !== false) {
                  $reduction = $coupon['discount_percent'];
                  $coupon_code = $coupon['code'];
                  $cart_details[] = "Code: $coupon_code (-". number_format($total_price * $reduction, 2, '.', '') ."€)";
                  $total_price *= (1 - $reduction);
                }
              }
            }
            $montant_cybank = number_format($total_price, 2, '.', '');
            $control = md5($api_key . "#" . $transaction . "#" . $montant_cybank . "#" . $vendeur . "#" . $retour_url . "#");
            ?>
          </section>
        </div>
        <div id="cart-bar">
          <h2>Votre panier</h2>
          <?php
            if (empty($cart_details)) {
                echo "Panier vide.";
            } else {
              echo "<ul><li/>";
              echo implode("<li/>", $cart_details);
              echo "</ul>";
            }
          ?>
          <p class="total-price">Total: <?= number_format($total_price, 2, ",") ?>€</p>
          
          <form action="https://www.plateforme-smc.fr/cybank/index.php" method="POST">
            <input type="hidden" name="transaction" value="<?= $transaction ?>">
            <input type="hidden" name="montant" value="<?= $montant_cybank ?>">
            <input type="hidden" name="vendeur" value="<?= $vendeur ?>">
            <input type="hidden" name="retour" value="<?= $retour_url ?>">
            <input type="hidden" name="control" value="<?= $control ?>">
            <button id="checkout" type="submit" class="basic-btn checkout-btn" <?php if($total_price <= 0) echo 'disabled'; ?>>Payer</button>
          </form>

          <details class="coupon-details">
            <summary>Code promo ?</summary>
            <form class="coupon-form" action="/apply_coupon" method="POST">
              <input type="text" name="coupon" placeholder="Code promo">
              <button id="submit_coupon" class="basic-btn" type="submit">Appliquer</button>
            </form>
          </details>
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
