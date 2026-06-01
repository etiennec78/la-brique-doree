<?php
$title = "La Brique Dorée - Éditeur de menus";
$h1 = "EDITEUR DE MENUS";
$staff_page = true;
$css_files = ['/css/food-cards.css', '/css/menu_editor.css'];
include __DIR__ . '/../includes/header.php';
?>
<main>
    <section class="menu">
    <?php
        $is_editable = true;
        $item_selector_type = 'plus_minus';
        $merge_menu_items = false;
        $menu_editor = true;
        $editing_menu = $_GET['edit'] ?? null;
        include __DIR__ . '/../includes/bento_grid.php';
    ?>
    </section>
</main>
