<?php
session_start();
include_once 'db_connect.php';
include_once 'get_cart_count.php';
include_once 'getapikey.php'; 

$vendeur = 'MI-4_J'; 
$api_key = getAPIKey($vendeur); 
$transaction = uniqid();

$current_cart_id = 0;
if (isset($_SESSION['user'])) {
    $stmt_c = $pdo->prepare("SELECT id FROM cart WHERE user_id = ? AND payment_status_id = 1 LIMIT 1");
    $stmt_c->execute([$_SESSION['user']['id']]);
    $res_c = $stmt_c->fetch();
    $current_cart_id = $res_c ? $res_c['id'] : 0;
}

$retour_url = "http://localhost/payment_result.php?cart_id=" . $current_cart_id;

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
          <h2>~ Éléments du panier ~</h2>
          <section class="bento">
            <?php
            if (isset($_SESSION['user'])) {
              try {
                $uid = $_SESSION['user']['id'];

                // Récupérer les menus du panier
                $stmt = $pdo->prepare("
                SELECT m.id, m.name, m.price, cm.quantity
                FROM cart c
                JOIN cart_menu cm ON c.id = cm.cart_id
                JOIN menu m ON cm.menu_id = m.id
                WHERE c.user_id = ? AND c.payment_status_id = 1
                ");
                $stmt->execute([$uid]);
                $cart_menus = $stmt->fetchAll();

                // Récupérer les plats du panier
                $stmt = $pdo->prepare("
                SELECT f.id as item_id, f.name, f.price, f.description, f.image_path, cf.quantity
                FROM cart c
                JOIN cart_food cf ON c.id = cf.cart_id
                JOIN food f ON cf.food_id = f.id
                WHERE c.user_id = ? AND c.payment_status_id = 1
                ");
                $stmt->execute([$uid]);
                $cart_foods = $stmt->fetchAll();

                $cart_size = count($cart_menus) + count($cart_foods);
                if ($cart_size == 0) {
                  echo '<p>Votre panier est vide.</p>';
                } else {

                  // Boucler pour chaque menu + 1 (plats individuels)
                  for($i = 0; $i < count($cart_menus) + 1; $i++) {
                    $individual = $i == count($cart_menus);
                    echo '<div>';

                    if ($individual) {
                      $foods = $cart_foods;
                      $menu_name = "Plats individuels";
                    } else {

                      // Ajouter le menu dans la liste de paiements
                      $menu = $cart_menus[$i];
                      $name = $menu['name'];
                      $price_val = floatval($menu['price']);
                      $price_str = number_format($price_val, 2, ",");
                      $quantity = $menu['quantity'];

                      $total_price += $price_val * $quantity;
                      $cart_details[] = "$name (".$price_str."€) x$quantity";

                      // Récupérer les plats de chaque menu dans le panier
                      $stmt = $pdo->prepare("
                      SELECT f.id as item_id, f.name, f.price, f.description, f.image_path
                      FROM food f
                      JOIN menu_food mf ON f.id = mf.food_id
                      WHERE mf.menu_id = ?
                      ");
                      $stmt->execute([$menu['id']]);
                      $foods = $stmt->fetchAll();

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
                      $image_path = $food['image_path'];
                      $food_id = $food['item_id'];

                      if ($individual) {
                        $quantity = $food['quantity'];
                        $total_price += $price_val * $quantity;
                        $cart_details[] = "$name (".$price_str."€) x$quantity";
                      }

                      echo '<article class="description" description="'. htmlspecialchars($description). '" price="'. $price_str .'€" style="background-image: url('. htmlspecialchars($image_path) .');">
                      <h3>'. htmlspecialchars($name) .'</h3>';

                      if ($individual) {
                        echo '<form method="POST" action="update_cart.php" style="display:inline; margin:0; padding:0;">
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
