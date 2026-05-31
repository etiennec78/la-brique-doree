<?php
$title = "La Brique Dorée - Éditeur de menus";
$h1 = "EDITEUR DE MENUS";
$staff_page = true;
$css_files = ['/css/food-cards.css', '/css/products.css', '/css/menu_editor.css'];
include __DIR__ . '/../includes/header.php';
?>
<main>
    <section class="menu">
    <?php
        $is_editable = false;
        $item_selector_type = 'add_only';
        $merge_menu_items = false;
        $menu_editor = true;
        include __DIR__ . '/../includes/bento_grid.php';
    ?>
    </section>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
