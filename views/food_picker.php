<?php
$title = "La Brique Dorée - Choisir un composant";
$h1 = "AJOUTER AU MENU";
$staff_page = true;
$css_files = ['/css/food-cards.css', '/css/menu_editor.css'];
include __DIR__ . '/../includes/header.php';

require_once __DIR__ . '/../src/models/Food.php';
require_once __DIR__ . '/../src/models/Order.php';

$menu_id = isset($_GET['menu_id']) ? (int)$_GET['menu_id'] : 0;

$menus = [];
$all_foods = Food::getAll();
$sorted_foods = Order::sortByType($all_foods);
$food_types = Food::getTypes();

$is_editable = false;
$menu_editor = false;
$is_picker = true;
$picker_menu_id = $menu_id;
?>

<main>
    <div style="margin: 25px; text-align: left;">
        <button onclick="location.href='/menu_editor'" class="basic-btn">Retour à l'éditeur</button>
    </div>
    <section class="menu">
        <?php include __DIR__ . '/../includes/bento_grid.php'; ?>
    </section>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
