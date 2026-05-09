<?php
$title = "La Brique Dorée";
$h1 = "COMMANDE";
$show_cart = true;
$show_video = true;
$css_files = ['/css/food-cards.css', '/css/orders.css'];
$js_files = ['/js/orders.js'];
include __DIR__ . '/../includes/header.php';
?>
<main>
      <section class="cart-page">
        <div id="cart-content">
          <h2>~ Éléments du panier ~</h2>
          <section class="bento">
            <?php
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
                  $menu = $cart_menus[$i];
                  $quantity = $menu['quantity'];
                  $foods = $menu['foods'];
                  $menu_name = $menu['name'];
                }

                echo '<div class="menu-header">';
                echo '<h2>'. htmlspecialchars($menu_name) .'</h2>';
                if (!$individual) {
                  echo '<form method="POST" action="/update_cart" style="display:inline; margin:0; padding:0;">
                  <input type="hidden" name="item_id" value="'. $menu['id'] .'">
                  <input type="hidden" name="item_type" value="menu">
                  <div class="nb-selector">
                  <button class="remove-from-cart" type="submit" name="action" value="remove" aria-label="Retirer du panier">-</button>
                  <input type="number" class="amount" name="amount" min="0" max="9" value="'. $quantity .'"/>
                  <button class="add-to-cart" type="submit" name="action" value="add" aria-label="Ajouter au panier">+</button>
                  </div>
                  </form>';
                }
                echo '</div>';
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
            }
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
          <?php if ($expired_coupon): ?>
            <p class="alert">Coupon expiré: <?= $coupon['code'] ?></p>
          <?php endif ?>
          <p class="total-price">Total: <?= number_format($total_price, 2, ",") ?>€</p>
          
          <form id="delivery-type" action="/set_delivery_type" method="POST">
            <button type="submit" name="is_takeaway" value="0" class="<?= !$is_takeaway ? 'selected' : '' ?>">À domicile</button>
            <button type="submit" name="is_takeaway" value="1" class="<?= $is_takeaway ? 'selected' : '' ?>">À emporter</button>
          </form>

          <form id="delivery-time" action="/set_delivery_type" method="POST" class="<?= $is_takeaway ? '' : 'hidden' ?>">
            <label id="takeway" for="takeaway_time">Heure de retrait</label>
            <input type="time" id="takeaway_time" name="takeaway_time" value="<?= htmlspecialchars($takeaway_time) ?>" required>
            <br>
            <button id="takeaway" type="submit" class="basic-btn">Confirmer l'heure</button>
          </form>
          
          <form action="https://www.plateforme-smc.fr/cybank/index.php" method="POST">
            <input type="hidden" name="transaction" value="<?= $transaction ?>">
            <input type="hidden" name="montant" value="<?= $montant_cybank ?>">
            <input type="hidden" name="vendeur" value="<?= $vendeur ?>">
            <input type="hidden" name="retour" value="<?= $retour_url ?>">
            <input type="hidden" name="control" value="<?= $control ?>">
            <button id="checkout" type="submit" class="basic-btn checkout-btn" <?php if($total_price <= 0 || ($is_takeaway && empty($takeaway_time))) echo 'disabled'; ?>>
                <?= ($is_takeaway && empty($takeaway_time)) ? "Veuillez confirmer l'heure" : 'Payer' ?>
            </button>
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
<?php include __DIR__ . '/../includes/footer.php'; ?>
