<?php
/* Show a +/- button to edit the amout of items in cart */
$is_editable = $is_editable ?? false;

/* Selector type for items: 'add_only' (+) or 'plus_minus' (+/-) */
$item_selector_type = $item_selector_type ?? 'add_only';

/* Visually merge food cards from menus into one menu card */
$merge_menu_items = $merge_menu_items ?? false;
?>

<!-- Loop for each menu (or 1 if combined) + individual food categories -->
<?php
    $menu_categories = $merge_menu_items ? 1 : count($menus);
    $loop_count = $menu_categories + count($sorted_foods);
?>
<?php for ($i = 0; $i < $loop_count; $i++): ?>

    <!-- Get the list of foods to display, and the title of their category -->
    <?php
    $menu_loop = $i < $menu_categories;
    if ($menu_loop) {
        if ($merge_menu_items) {
            $category_name = "Menus";
            $cards = $menus;
        } else {
            $menu = $menus[$i];
            $category_name = $menu['name'];
            $cards = $menu['foods'];
        }
    } else {
        $food_type_id = array_keys($sorted_foods)[$i - $menu_categories];
        $category_name = $food_types[$food_type_id];
        $cards = $sorted_foods[$food_type_id]; // Foods list by type
    }

    if ($merge_menu_items) {
        $category_name = "~ " . $category_name . " ~";
    }
    ?>

    <div class="category <?= $merge_menu_items ? '' : 'gray-container' ?>">
        <div class="category-header">
            <h2><?= $category_name ?></h2>
            <?php if ($menu_loop && $is_editable && !$merge_menu_items): ?>
                <!-- +/- button to add or remove menus from the cart -->
                <form method="POST" action="/update_cart" style="display:inline; margin:0; padding:0;">
                    <input type="hidden" name="item_id" value="<?= $menu['id'] ?>">
                    <input type="hidden" name="item_type" value="<?= $menu_loop ? 'menu' : 'food'?>">
                    <div class="nb-selector">
                        <button class="remove-from-cart" type="submit" name="action" value="remove" aria-label="Retirer du panier">-</button>
                        <input type="number" class="amount" name="amount" min="0" max="9" value="<?= $menu['quantity'] ?>"/>
                        <button class="add-to-cart" type="submit" name="action" value="add" aria-label="Ajouter au panier">+</button>
                    </div>
                </form>
            <?php endif; ?>
        </div>

        <div class="items-grid">
            <!-- For each card to display in this category -->
            <?php foreach ($cards as $card): ?>
                <?php
                $price_val = floatval($card['price']);
                $price_str = number_format($price_val, 2, ",");
                $style = (!$menu_loop || !$merge_menu_items) ? 'flex: 1; background-image: url(/assets/' . htmlspecialchars($card['image_path']) . '); background-size: cover; background-position: center;' : '';
                ?>
                <article class="description <?= $card['allergens_classes'] ?? '' ?>" description="<?= $card['description'] ?>" price="<?= $price_str ?>€" style="<?= $style ?>">
                    <h3><?= htmlspecialchars($card['name']) ?></h3>

                    <?php if ($is_editable && (!$menu_loop || $merge_menu_items)): ?>
                        <?php if ($item_selector_type === 'plus_minus'): ?>
                            <!-- +/- button for cart items -->
                            <form action="/update_cart" method="POST">
                                <input type="hidden" name="item_id" value="<?= $card['id'] ?>">
                                <input type="hidden" name="item_type" value="<?= $menu_loop ? 'menu' : 'food'?>">
                                <div class="nb-selector">
                                    <button class="remove-from-cart" type="submit" name="action" value="remove" aria-label="Retirer du panier">-</button>
                                    <input type="number" class="amount" name="amount" min="0" max="9" value="<?= $card['quantity'] ?>"/>
                                    <button class="add-to-cart" type="submit" name="action" value="add" aria-label="Ajouter au panier">+</button>
                                </div>
                            </form>
                        <?php else: ?>
                            <!-- + button to add items to cart -->
                            <form action="/update_cart" method="POST">
                                <input type="hidden" name="item_id" value="<?= $card['id'] ?>">
                                <input type="hidden" name="item_type" value="<?= $menu_loop ? 'menu' : 'food'?>">
                                <input type="hidden" name="action" value="add">
                                <button class="add-to-cart" type="submit" aria-label="Ajouter au panier">+</button>
                            </form>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if ($menu_loop && $merge_menu_items): ?>
                        <!-- Display a fragmented view of the menu content -->
                        <div class="menu-grid">
                            <?php foreach ($card['foods'] as $food): ?>
                                <div style="flex: 1; background-image: url(/assets/<?= htmlspecialchars($food['image_path']) ?>); background-size: cover; background-position: center;"></div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </div>
    </div>

<?php endfor; ?>
