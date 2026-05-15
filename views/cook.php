<?php
$title = "Restaurateur - La Brique Dorée";
$h1 = "RESTAURATEUR";
$show_cart = true;
$show_video = true;
$css_files = ['/css/cook.css'];
$js_files = ['/js/cook.js'];
include __DIR__ . '/../includes/header.php';
?>
<main>
        <h2>~ Commandes ~</h2>
        
        <?php if(isset($_GET['error'])): ?>
            <p style="text-align: center; font-weight: bold;"><?= $_GET['error'] ?></p>
        <?php endif; ?>

        <div class="tabs">  
            <nav class="tabs-nav">
                <label class="tab-item">
                    <span>À CUISINER</span>
                    <input type="radio" id="pending-toggle" name="tabs-toggle" checked>
                </label>

                <label class="tab-item">
                    <span>À REMETTRE</span>
                    <input type="radio" id="delivery-toggle" name="tabs-toggle">
                </label>
            </nav>

            <div class="tab-content" id="pending-content">
                <table>
                    <tr>
                        <td><h3>COMMANDE</h3></td>
                        <td><h3>ITEMS</h3></td>
                        <td><h3 id="state">ETAT</h3></td>
                    </tr>

                    <?php if (empty($pending_orders)): ?>
                        <tr><td colspan="3" style="text-align:center;">Aucune commande en attente.</td></tr>
                    <?php endif; ?>

                    <?php foreach ($pending_orders as $order): ?>
                    <tr>
                        <td>
                            <span>Commande #<?= $order['id'] ?> (<?= getName($order) ?>)</span>
                            <?php if (isset($order['is_takeaway']) && $order['is_takeaway']): ?>
                                <br><small>Retrait : <?= $order['takeaway_time'] ? date('H\hi', strtotime($order['takeaway_time'])) : 'Au plus vite' ?></small>
                            <?php else: ?>
                                <br><small>Livraison</small>
                            <?php endif; ?>
                                </td>
                                    <td class="order-items">
                                        <ul>
                                            <?php foreach ($order['items']['menus'] as $menu): ?>
                                                <li><strong><?= $menu['name'] ?></strong> x<?= $menu['quantity'] ?></li>
                                            <?php endforeach; ?>
                                            <?php foreach ($order['items']['foods'] as $food): ?>
                                                <li><?= $food['name'] ?> x<?= $food['quantity'] ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </td>
                                <td>
                            <form action="/assign_order" method="POST">
                                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                <button id="manage" type="submit" class="basic-btn">Prêt !</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>

            <div class="tab-content" id="delivery-content">
                <table>
                    <tr>
                        <td><h3>COMMANDE</h3></td>
                        <td><h3 id="state">ETAT</h3></td>
                    </tr>

                    <?php if (empty($delivery_orders)): ?>
                        <tr><td colspan="2" style="text-align:center;">Aucune livraison en cours.</td></tr>
                    <?php endif; ?>

                    <?php foreach ($delivery_orders as $order): ?>
                    <tr>
                        <td>
                            <span>Commande #<?= $order['id'] ?> (<?= getName($order) ?>)</span>
                            <?php if (isset($order['is_takeaway']) && $order['is_takeaway']): ?>
                                <br><small>Retrait : <?= $order['takeaway_time'] ? date('H\hi', strtotime($order['takeaway_time'])) : 'Au plus vite' ?></small>
                            <?php else: ?>
                                <br><small>Livraison</small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (isset($order['is_takeaway']) && $order['is_takeaway']): ?>
                                <form action="/finish_takeaway" method="POST">
                                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                    <button type="submit" class="basic-btn">Remis</button>
                                </form>
                            <?php else: ?>
                                <label class="selection">
                                    <span>En attente du retour d'un livreur</span>
                                    <input type="checkbox" checked disabled/> 
                                </label>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
    </main>
<?php include __DIR__ . '/../includes/footer.php'; ?>
