<?php
$title = "La Brique Dorée";
$h1 = "NOS PRODUITS";
$staff_page = false;
$css_files = ['/css/food-cards.css', '/css/products.css'];
$js_files = ['/js/cart.js', '/js/filters.js', '/js/sort_products.js'];
include __DIR__ . '/../includes/header.php';
?>
<main>
      <input type="checkbox" class="filter" id="crustacean" checked />
      <input type="checkbox" class="filter" id="fish" checked />
      <input type="checkbox" class="filter" id="gluten" checked />
      <input type="checkbox" class="filter" id="milk" checked />
      <input type="checkbox" class="filter" id="sesame" checked />
      <input type="checkbox" class="filter" id="egg" checked />
      <input type="checkbox" class="filter" id="soy" checked />
      <input type="checkbox" class="filter" id="nut" checked />
      <input type="checkbox" class="filter" id="sulfite" checked />

      <div class="products-toolbar">
        
        <details class="filters-panel">
          <summary>
            <img src="/assets/images/filter.svg" alt="Filtres">
          </summary>
          <aside class="filters-menu" aria-label="Filtres allergenes">
            <div class="filters-list">
              <label for="crustacean">Crustacé</label>
              <label for="fish">Poisson</label>
              <label for="gluten">Gluten</label>
              <label for="milk">Lait</label>
              <label for="sesame">Sésame</label>
              <label for="egg">Œuf</label>
              <label for="soy">Soja</label>
              <label for="nut">Fruit à coque</label>
              <label for="sulfite">Sulfite</label>
            </div>
          </aside>
        </details>

        <div class="sort-container">
            <label for="sort-price">Trier par prix :</label>
            <select id="sort-price" class="custom-sort-select">
                <option value="default">Par défaut</option>
                <option value="asc">Ordre croissant (du - cher au + cher)</option>
                <option value="desc">Ordre décroissant (du + cher au - cher)</option>
            </select>
        </div>

      </div>

      <section class="menu-content">
        <div class="food-section" name="Menus">
          <h2>~ Nos Menus ~</h2>
          <section class="bento">
            <?php foreach($menus as $menu): ?>
                <?php
                    $id = $menu['id'];
                    $name = htmlspecialchars($menu['name']);
                    $price = number_format($menu['price'], 2, ",");
                    $description = htmlspecialchars($menu['description']);
                    $menus_data = $menu['foods'];
                    $menu_allergens_classes = implode(' ', array_map('htmlspecialchars', $menu['allergens']));
                ?>
                <article class="description <?= $menu_allergens_classes ?>" description="<?= $description ?>" price="<?= $price ?>€" data-raw-price="<?= $menu['price'] ?>">
                  <h3><?= $name ?></h3>
                  <div class="menu-grid">
                    <?php foreach($menus_data as $menu_item): ?>
                        <?php for($j = 0; $j < $menu_item['quantity']; $j++): ?>
                            <div style="flex: 1; background-image: url(/assets/<?= htmlspecialchars($menu_item['image_path']) ?>); background-size: cover; background-position: center;"></div>
                        <?php endfor; ?>
                    <?php endforeach; ?>
                  </div>
                  <form action="/update_cart" method="POST">
                    <input type="hidden" name="item_id" value="<?= $id ?>">
                    <input type="hidden" name="item_type" value="menu">
                    <input type="hidden" name="action" value="add">
                    <button class="add-to-cart" type="submit" aria-label="Ajouter au panier">+</button>
                  </form>
                </article>
            <?php endforeach; ?>
          </section>
        </div>

        <?php foreach($food_types as $food_type): ?>
            <div class="food-section" name="<?= htmlspecialchars($food_type['name']) ?>">
                <h2>~ <?= htmlspecialchars($food_type['name']) ?> ~</h2>
                <section class="bento">
                    <?php foreach($food_type['foods'] as $food): ?>
                        <?php
                            $id = $food['id'];
                            $name = htmlspecialchars($food['name']);
                            $description = htmlspecialchars($food['description']);
                            $price = number_format($food['price'], 2, ",");
                            $image_path = '/assets/' . htmlspecialchars($food['image_path']);
                            $allergens_classes = implode(' ', array_map('htmlspecialchars', $food['allergens']));
                        ?>
                        <article class="description <?= $allergens_classes ?>" description="<?= $description ?>" price="<?= $price ?>€" style="background-image: url('<?= $image_path ?>');" data-raw-price="<?= $food['price'] ?>">
                            <h3><?= $name ?></h3>
                            <form action="/update_cart" method="POST">
                                <input type="hidden" name="item_id" value="<?= $id ?>">
                                <input type="hidden" name="item_type" value="food">
                                <input type="hidden" name="action" value="add">
                                <button class="add-to-cart" type="submit" aria-label="Ajouter au panier">+</button>
                            </form>
                        </article>
                    <?php endforeach; ?>
                </section>
            </div>
        <?php endforeach; ?>
      </section>
    </main>
<?php
  if (isset($_SESSION['error'])){
    unset($_SESSION['error']);
  }
  include __DIR__ . '/../includes/footer.php'; 
?>
