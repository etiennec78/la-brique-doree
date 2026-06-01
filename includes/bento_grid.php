<?php

/* Avoiding the undefined variable php warning */
require_once __DIR__ . '/../src/models/Food.php';
$food_types = Food::getTypes();

/* Show a +/- button to edit the amout of items in cart */
$is_editable = $is_editable ?? false;

/* Selector type for items: 'add_only' (+) or 'plus_minus' (+/-) */
$item_selector_type = $item_selector_type ?? 'add_only';

/* Visually merge food cards from menus into one menu card */
$merge_menu_items = $merge_menu_items ?? false;

/* Act as an item picker for foods */
$is_picker = $is_picker ?? false;

/*Show a card to add foods to menus and edit data*/
$menu_editor = $menu_editor ?? false;
$picker_target = $menu_editor ? 'menu' : 'panier';
$editing_menu = $editing_menu ?? null;
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
            $cat_price_val = floatval($menu['price']);
            $cat_price_str = number_format($cat_price_val, 2, ",");
            $category_name = $menu['name'] . ' (' . $cat_price_str . '€)';
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

    <div class="category <?= $merge_menu_items ? '' : 'gray-container' ?>" <?= ($menu_loop && !$merge_menu_items) ? 'id="menu-' . $menu['id'] . '"' : '' ?>>
        <div class="category-header">
            <?php if ($menu_loop && !$merge_menu_items && $editing_menu == $menu['id']): ?>
                <!-- inputs and floppy disk icon to save menu changes -->
                <form action="/update_menu" method="post">
                    <input type="hidden" name="menu_id" value="<?= $menu['id'] ?>">
                    <input type="text" id="name" name="name" maxlength="30" value="<?= $menu['name'] ?>">
                    <input type="number" id="price" name="price" step="0.01" value="<?= $cat_price_val ?>">
                    <button type="submit" class="basic-btn save-btn">
                        <img src="/assets/images/save.svg" alt="Modifier le menu" class="edit-icon">
                    </button>
                    <div class="subtitle"><input type="text" id="description" name="description" maxlength="255" value="<?= $menu['description'] ?>"></div>
                </form>
            <?php else: ?>
                <!-- category title -->
                <h2><?= $category_name ?></h2>
                <?php if ($menu_editor && $menu_loop): ?>
                    <!-- pencil icon to edit menu -->
                    <a href="?edit=<?= $menu['id'] ?>#menu-<?= $menu['id'] ?>" title="Modifier le menu">
                        <img src="/assets/images/pencil.svg" alt="Modifier le menu" class="edit-icon">
                    </a>
                    <div class="subtitle"><h5><?= $menu['description'] ?></h5></div>
                <?php endif; ?>
            <?php endif; ?>

            <?php if ($menu_loop && !$merge_menu_items): ?>
                <?php if ($is_editable && !$menu_editor): ?>
                    <!-- +/- button to add or remove menus from the cart -->
                    <form method="POST" action="/update_cart" style="display:inline; margin:0; padding:0;">
                        <input type="hidden" name="item_id" value="<?= $menu['id'] ?>">
                        <input type="hidden" name="item_type" value="menu">
                        <div class="nb-selector">
                            <button type="submit" name="action" value="set" style="display: none;"></button>
                            <button class="remove-from-cart" type="submit" name="action" value="remove" aria-label="Retirer du <?= $picker_target ?>">-</button>
                            <input type="number" class="amount" name="amount" min="0" max="9" value="<?= $menu['quantity'] ?>"/>
                            <button class="add-to-cart" type="submit" name="action" value="add" aria-label="Ajouter au <?= $picker_target ?>">+</button>
                        </div>
                    </form>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <div class="items-grid">
            <!-- For each card to display in this category -->
            <?php foreach ($cards as $card): ?>
                <?php
                $price_str = "";
                if (!$menu_loop || $merge_menu_items) {
                    $price_val = floatval($card['price']);
                    $price_str = number_format($price_val, 2, ",") . '€';
                }
                $style = (!$menu_loop || !$merge_menu_items) ? 'flex: 1; background-image: url(/assets/' . htmlspecialchars($card['image_path']) . '); background-size: cover; background-position: center;' : '';

                // Make cards interractive on click for the food picker or menu editor
                $link_url = null;
                if ($is_picker) {
                    $link_url = "/add_menu_food?menu_id=" . $picker_menu_id . "&food_id=" . $card['id'];
                } elseif ($menu_editor && !$menu_loop) {
                    $link_url = "/food_creator?edit_id=" . $card['id'];
                }
                ?>

                <?php if ($link_url): ?>
                <a href="<?= $link_url ?>" style="display: contents;">
                <?php endif; ?>
                <article class="description <?= $card['allergens_classes'] ?? '' ?>" description="<?= $card['description'] ?>" price="<?= $price_str ?>" style="<?= $style ?>">
                    <h3><?= htmlspecialchars($card['name']) ?></h3>
                    <?php if ($is_editable && ( ($menu_editor && $menu_loop) || (!$menu_editor && (!$menu_loop || $merge_menu_items)) )): ?>
                        <?php if ($item_selector_type === 'plus_minus'): ?>
                            <!-- +/- button for cart items -->
                            <form action="/update_<?= $menu_editor ? 'menu' : 'cart' ?>" method="POST">

                                <?php if ($menu_editor): ?>
                                    <input type="hidden" name="menu_id" value="<?= $menu['id'] ?>">
                                <?php endif; ?>

                                <input type="hidden" name="item_id" value="<?= $card['id'] ?>">
                                <input type="hidden" name="item_type" value="<?= $menu_loop ? 'menu' : 'food'?>">

                                <div class="nb-selector">
                                    <button type="submit" name="action" value="set" style="display: none;"></button>
                                    <button class="remove-from-cart" type="submit" name="action" value="remove">-</button>
                                    <input type="number" class="amount" name="amount" min="0" max="9" value="<?= $card['quantity'] ?? 0 ?>"/>
                                    <button class="add-to-cart" type="submit" name="action" value="add">+</button>
                                </div>
                            </form>
                        <?php else: ?>
                            <!-- + button to add items to cart -->
                            <form action="/update_cart" method="POST">
                                <input type="hidden" name="item_id" value="<?= $card['id'] ?>">
                                <input type="hidden" name="item_type" value="<?= $menu_loop ? 'menu' : 'food'?>">
                                <input type="hidden" name="action" value="add">
                                <button class="add-to-cart" type="submit" aria-label="Ajouter au <?= $picker_target ?>">+</button>
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
                <?php if (isset($card_link) && $card_link): ?>
                <?php endif; ?>
            <?php endforeach; ?>
            <!-- Show an add card at the end of the category for the menu/food editor -->
            <?php if ($menu_editor): ?>
                <a href="/<?= $menu_loop ? 'food_picker?menu_id='.$menu['id'] : 'food_creator?food_type='.$food_type_id ?>">
                    <article class="add-card"></article>
                </a>
            <?php endif; ?>
        </div>
    </div>

<?php endfor; ?>
<?php if ($menu_editor): ?>
    <div class="category">
        <div class="items-grid">
            <a href="/menu_creator">
                <article class="add-card"></article>
            </a>
        </div>
    </dib>
<?php endif; ?>
