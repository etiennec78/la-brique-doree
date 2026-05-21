<?php
$is_editable = $is_editable ?? false;
?>
<!-- Boucler pour chaque menu + 1 (plats individuels) -->
<?php for($i = 0; $i < count($menus) + $cart_has_food; $i++): ?>
    <div class="menu">
    <?php
    $individual = $i == count($menus);
    if ($individual) {
        $foods = $foods;
        $menu_name = "Plats individuels";
    } else {
        $menu = $menus[$i];
        $quantity = $menu['quantity'];
        $foods = $menu['foods'];
        $menu_name = $menu['name'] . (!$is_editable ? " (x$quantity)" : "");
    }
    ?>

    <div class="menu-header">
    <h2><?= htmlspecialchars($menu_name) ?></h2>
    <?php if ($is_editable && !$individual): ?>
        <form method="POST" action="/update_cart" style="display:inline; margin:0; padding:0;">
        <input type="hidden" name="item_id" value="<?= $menu['id'] ?>">
        <input type="hidden" name="item_type" value="menu">
        <div class="nb-selector">
        <button class="remove-from-cart" type="submit" name="action" value="remove" aria-label="Retirer du panier">-</button>
        <input type="number" class="amount" name="amount" min="0" max="9" value="<?= $quantity ?>"/>
        <button class="add-to-cart" type="submit" name="action" value="add" aria-label="Ajouter au panier">+</button>
        </div>
        </form>
    <?php endif; ?>
    </div>
    <div class="items-grid">

    <?php foreach($foods as $food): ?>
        <?php 
        $name = $food['name'];
        $description = $food['description'];
        $price_val = floatval($food['price']);
        $price_str = number_format($price_val, 2, ",");
        $image_path = 'assets/' . $food['image_path'];
        $food_id = $food['id'] ?? null;

        if ($individual) {
            $quantity = $food['quantity'];
        } else {
            $quantity = $food['quantity'] ?? 1;
        }
        ?>

        <article class="description" description="<?= htmlspecialchars($description) ?>" price="<?= $price_str ?>€" style="background-image: url(<?= htmlspecialchars($image_path) ?>);">
        
        <h3>
            <?= htmlspecialchars($name) ?>
            <?php if ($quantity > 1 && (!$individual || !$is_editable)): ?>
                (x<?= $quantity ?>)
            <?php endif; ?>
        </h3>

        <?php if ($is_editable && $individual): ?>
            <form method="POST" action="/update_cart" style="display:inline; margin:0; padding:0;">
            <input type="hidden" name="item_id" value="<?= $food_id ?>">
            <input type="hidden" name="item_type" value="food">
            <div class="nb-selector">
            <button class="remove-from-cart" type="submit" name="action" value="remove" aria-label="Retirer du panier">-</button>
            <input type="number" class="amount" name="amount" min="0" max="9" value="<?= $quantity ?>"/>
            <button class="add-to-cart" type="submit" name="action" value="add" aria-label="Ajouter au panier">+</button>
            </div>
            </form>
        <?php endif; ?>
        </article>
    <?php endforeach; ?>
    </div>
    </div>
<?php endfor; ?>
