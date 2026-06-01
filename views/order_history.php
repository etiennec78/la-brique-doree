<?php
$title = "La Brique Dorée - Historique des commandes";
$h1 = "HISTORIQUE DES COMMANDES";
$staff_page = false;
$css_files = ['/css/order_tracking.css', '/css/order_history.css', '/css/food-cards.css', '/css/orders.css'];
include __DIR__ . '/../includes/header.php';
?>
<main>
    <div class="tracking-container">
        <?php if ($order_id == null): ?>
            <h2>Aucune commande pour l'instant</h2>
            <button onclick="location.href='/products'" class="basic-btn">Commander</button>
        <?php else: ?>
            <?php if ($prev_id !== null): ?>
                <button id="prev" class="slide-btn" onclick="location.href='/order_history?order_id=<?= $prev_id ?>&user_id=<?= $target_id ?>'">❮</button>
            <?php endif; ?>
            <?php if ($next_id !== null): ?>
                <button id="next" class="slide-btn" onclick="location.href='/order_history?order_id=<?= $next_id ?>&user_id=<?= $target_id ?>'">❯</button>
            <?php endif; ?>
            <h2>Commande #<?= $order['id'] ?></h2>
            <div class="delivery-info">
                <p>Retrait : <?= $order['is_takeaway'] ? "À emporter" : "À domicile" ?></p>
                <p>Cuisinier : <?= $order['cook'] ?></p>
                <?php if (!$order['is_takeaway']): ?>
                    <p>Livreur : <?= $order['delivery_person'] ?></p>
                <?php else: ?>
                    <p>Retrait programmé : <?= $order['takeaway_time'] ?></p>
                <?php endif; ?>
            </div>
            <?php if ($order['coupon'] != null): ?>
                <p>Code utilisé : <?= $order['coupon']['code'] ?> (-<?= (100 * $order['coupon']['discount_percent']) ?>%)</p>
            <?php endif; ?>
            <p>Prix total : <?= number_format($order['total_price'], 2, ",") ?>€</p>

            <section class="items-container">
                <?php 
                $is_editable = false;
                include __DIR__ . '/../includes/bento_grid.php'; 
                ?>
            </section>
        <?php endif; ?>
    </div>
</main>
<?php
  if (isset($_SESSION['error'])){
    unset($_SESSION['error']);
  }
?>