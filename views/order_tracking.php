<?php
$title = "Suivi - La Brique Dorée";
$h1 = "SUIVI DE COMMANDE";
$staff_page = false;
$css_files = ['/css/order_tracking.css'];
$js_files = ['js/order_tracking.js'];
include __DIR__ . '/../includes/header.php';
?>
    <main>
    <div class="tracking-container">
        <?php if (empty($orders)): ?>
            <h2>Aucune commande en cours</h2>
            <p style="text-align: center; margin-bottom: 2rem;">Vous n'avez pas de commande en préparation ou en cours de livraison actuellement.</p>
        <?php else: ?>
            <?php foreach ($orders as $order): ?>
            <div class="order-tracker" id="order-<?= $order['id'] ?>" data-order-id="<?= $order['id'] ?>" data-status="<?= $order['status'] ?>" style="margin-bottom: 4rem; padding-bottom: 2rem; border-bottom: 1px solid #ccc;">
                <h2>Commande #<?= $order['id'] ?></h2>
                <?php $is_takeaway = isset($order['is_takeaway']) && $order['is_takeaway']; ?>
                <div class="stepper">
                    <div class="step active">
                        <div class="circle">📩</div>
                        <p>Reçue</p>
                        <div class="line"></div>
                    </div>

                    <div class="step <?php if ($order['status'] >= 2) { echo 'active'; } ?>">
                        <div class="circle">🍳</div>
                        <p>En préparation</p>
                        <div class="line"></div>
                    </div>

                    <div class="step <?php if ($order['status'] >= 3) { echo 'active'; } ?>">
                        <div class="circle"><?= $is_takeaway ? '🥡' : '🚴' ?></div>
                        <p><?= $is_takeaway ? 'Prête' : 'En route' ?></p>
                        <div class="line"></div>
                    </div>

                    <div class="step <?php if ($order['status'] >= 5) { echo 'active'; } ?>">
                        <div class="circle">😋</div>
                        <p><?= $is_takeaway ? 'Récupérée' : 'Livrée' ?></p>
                    </div>
                </div>

                <?php if ($order['status'] != 1): ?>
                <div class="delivery-info">
                    <?php if ($order['status'] == 2): ?>
                        <p>Cuisinier : <?= $get_name($order['cook']) ?></p>
                    <?php elseif ($order['status'] == 3 || $order['status'] == 4): ?>
                        <?php if ($is_takeaway): ?>
                            <p>Votre commande est prête à être récupérée au restaurant !</p>
                        <?php else: ?>
                            <p>Livreur : <?= $get_name($order['delivery_person']) ?></p>
                            <p>Arrivée : 10 min</p>
                            <iframe id="map-<?= $order['id'] ?>" src="https://www.openstreetmap.org/export/embed.html?bbox=9.113277196884157%2C55.72994659971866%2C9.11605328321457%2C55.73135269752343&marker=55.730662,9.114866&amp;layer=mapnik" style="width:100%; height:200px; border:0;"></iframe>
                        <?php endif; ?>
                    <?php else: ?>
                        <p>Commande <?= $is_takeaway ? 'récupérée' : 'livrée' ?> !<br/>Merci pour votre confiance. N'hésitez pas à nous laisser un avis !</p>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <div class="tracking-actions">
                    <button onclick="location.href='/reviews'" class="basic-btn <?= ($order['status'] >= 5) ? '' : 'hidden' ?>">Laisser un avis</button>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
        <div class="tracking-actions">
            <?php if (empty($orders)): ?>
                <button onclick="location.href='/'" class="basic-btn gray-btn">Retour à l'accueil</button>
            <?php endif; ?>
            <button onclick="location.href='/order_history'" class="basic-btn">Historique des commandes</button>
        </div>
    </div>
    </main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
