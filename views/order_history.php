<?php
$title = "La Brique Dorée - Historique des commandes";
$h1 = "HISTORIQUE DES COMMANDES";
$show_cart = true;
$show_video = true;
$css_files = ['/css/order_tracking.css', '/css/order_history.css'];
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
            <p>Total price : <?= number_format($order['total_price'], 2, ",") ?>€</p>
        <?php endif; ?>
    </div>
</main>
