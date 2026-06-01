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
      <?php
      $is_editable = true;
      $merge_menu_items = true;
      include __DIR__ . '/../includes/bento_grid.php';
      ?>
</main>
<?php include __DIR__ . '/../includes/footer.php'; ?>
