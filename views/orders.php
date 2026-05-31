<?php
$title = "La Brique Dorée";
$h1 = "COMMANDE";
$staff_page = false;
$css_files = ['/css/food-cards.css', '/css/orders.css'];
$js_files = ['/js/orders.js'];
include __DIR__ . '/../includes/header.php';
?>
<main>
      <section class="cart-page">
        <div id="cart-content">
          <section class="bento">
          <?php if (count($menus) <= 0 && !$cart_has_food): ?>
            <div class="items-grid">
              <p>Votre panier est vide.</p>
              <button onclick="location.href='/products'" class="basic-btn">Explorer la carte</button>
            </div>
          <?php else: ?>
            <?php 
            $is_editable = true;
            include __DIR__ . '/../includes/bento_grid.php'; 
            ?>
          <?php endif; ?>
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
          <?php if ((count($menus) > 0 || $cart_has_food)): ?>
          <?php if ($expired_coupon): ?>
            <p class="alert">Coupon expiré: <?= $coupon['code'] ?></p>
          <?php endif ?>
          <p class="total-price">Total: <?= number_format($total_price, 2, ",") ?>€</p>
          <?php if (!$user_has_valid_info): ?>
            <h3>Vous ne pouvez pas encore commander.</h3>
            <p>Vous devez remplir votre profil pour passer une commande.</p>
            <button onclick="location.href='/profile'" type="button" class="basic-btn">Remplir votre profil</button>
          <?php else: ?>
            <form id="delivery-type" action="/set_delivery_type" method="POST">
              <button type="submit" name="is_takeaway" value="0" class="<?= !$is_takeaway ? 'selected' : '' ?>">À domicile</button>
              <button type="submit" name="is_takeaway" value="1" class="<?= $is_takeaway ? 'selected' : '' ?>">À emporter</button>
            </form>

            <form id="delivery-time" action="/set_delivery_type" method="POST" class="<?= $is_takeaway ? '' : 'hidden' ?>">
              <label id="takeway" for="takeaway_time">Heure de retrait</label>
              <input type="time" id="takeaway_time" name="takeaway_time" value="<?= htmlspecialchars($takeaway_time) ?>" required>
              <br>
              <noscript>
                <button id="takeaway" type="submit" class="basic-btn">Confirmer l'heure</button>
              </noscript>
            </form>
          
            <?php if ((!$user_has_valid_address) && (!$is_takeaway)): ?>
              <h3>Vous ne pouvez pas encore commander à domicile.</h3>
              <p>Vous devez remplir votre profil avec votre adresse .</p>
              <button onclick="location.href='/profile'" type="button" class="basic-btn">Remplir votre profil</button>
            <?php else: ?>
              <?php 
                $_SESSION['free_order'] = false;
                $_SESSION['free_takeaway'] = $is_takeaway;
                $_SESSION['free_time'] = $takeaway_time; 
              ?>
              <?php if ($total_price > 0): ?>
                <form action="https://www.plateforme-smc.fr/cybank/index.php" method="POST">
                  <input type="hidden" name="transaction" value="<?= $transaction ?>">
                  <input type="hidden" name="montant" value="<?= $montant_cybank ?>">
                  <input type="hidden" name="vendeur" value="<?= $vendeur ?>">
                  <input type="hidden" name="retour" value="<?= $retour_url ?>">
                  <input type="hidden" name="control" value="<?= $control ?>">
                  <button style="font-size:15px;" id="checkout" type="submit" class="basic-btn checkout-btn" <?php if($is_takeaway && empty($takeaway_time)) echo 'disabled'; ?>>
                    <?= ($is_takeaway && empty($takeaway_time)) ? "Veuillez confirmer l'heure" : 'Payer' ?>
                  </button>
                </form>
              
              <?php elseif ($total_price == 0): ?>
                <form action="/payment_result">
                  <?php 
                    $_SESSION['free_order'] = true;
                    $_SESSION['free_takeaway'] = $is_takeaway;
                    $_SESSION['free_time'] = $takeaway_time;
                  ?>
                  <button style="font-size:15px;" id="checkout" type="submit" class="basic-btn checkout-btn" <?php if($is_takeaway && empty($takeaway_time)) echo 'disabled'; ?>>
                    <?= ($is_takeaway && empty($takeaway_time)) ? "Veuillez confirmer l'heure" : 'Payer' ?>
                  </button>
                </form>
              
              <?php else: ?>
                <h3>Le prix de la commande est invalide !</h3>
                <p>Veuillez essayer de commander autre chose.</p>
                <button onclick="location.href='/products'" type="button" class="basic-btn">Produits</button>
              <?php endif; ?>
              
              
              <details class="coupon-details">
              <summary>Code promo ?</summary>
                <form class="coupon-form" action="/apply_coupon" method="POST">
                  <?php $coupon['code'] = isset($_SESSION['previous_coupon']) ? $_SESSION['previous_coupon'] : ''; ?>
                  <input type="text" name="coupon" placeholder="Code promo" value="<?= $coupon['code'] ?>">
                  <noscript>
                    <button id="submit_coupon" class="basic-btn" type="submit">Appliquer</button>
                  </noscript>
                </form>
              </details>
            <?php endif; ?>
          <?php endif; ?>
          <?php endif; ?>
        </div>
      </section>
    </main>
<?php
  if (isset($_SESSION['error'])){
    unset($_SESSION['error']);
  }
  include __DIR__ . '/../includes/footer.php'; 
?>
