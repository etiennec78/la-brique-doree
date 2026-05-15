<?php
$title = "La Brique Dorée - Historique des commandes";
$h1 = "HISTORIQUE DES COMMANDES";
$show_cart = true;
$show_video = true;
$css_files = ['/css/order_history.css'];
include __DIR__ . '/../includes/header.php';
?>
<main>
    <?php if (empty($all_orders)): ?>
        <div class="form-page">
            <span>Aucune commande.</span>
        </div>
    <?php endif; ?>

    <?php foreach ($all_orders as $order): ?>
    <div class="form-page">
        <div class="command_card">
            <details>
                <summary class="main_info">
                    <strong>Commande #<?= $order['id']; ?></strong>
                </summary>

                <ul>
                    <?php foreach ($order['items']['menus'] as $menu): ?>
                    <li><?= $menu['name'] ?> x<?= $menu['quantity'] ?></li>
                    <?php endforeach; ?>
                    <?php foreach ($order['items']['foods'] as $food): ?>
                    <li><?= $food['name'] ?> x<?= $food['quantity'] ?></li>
                    <?php endforeach; ?>
                </ul>

                <strong>Prix Final :</strong>

                <br /><br />

                <details>
                    <summary>
                        <strong>Détails</strong>
                    </summary>
                    <br/><br/>
                    <div>
                        Cuisinier :<?= ' '.$order['cook'] ?><br/><br/>
                        Livreur :<?= ' '.$order['delivery_person'] ?><br/><br/>
                    </div>
                </details>
            </details>
        </div>
    </div>
    <?php endforeach; ?>
</main>
