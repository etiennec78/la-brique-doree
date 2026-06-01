<?php
$title = "La Brique Dorée - Créateur de plats";
$h1 = "CREATEUR DE PLATS";
$staff_page = true;
$css_files = ['/css/form.css'];
include __DIR__ . '/../includes/header.php';
?>

<main>
    <div class="form-page">
        <form action="/manage_food" method="post">
            <?php if ($edit_id): ?>
                <input type="hidden" name="edit_id" value="<?= $edit_id ?>">
            <?php endif; ?>
            <div class="input-group">
                <label for="name">Nom</label>
                <input type="text" id="name" name="name" maxlength="30" value="<?= $defaults['name'] ?? '' ?>" required>
            </div>
            <div class="input-group">
                <label for="food_type">Type de nourriture</label>
                <select name="food_type" id="food_type">
                    <option value=""></option>
                    <?php foreach($food_types as $id => $name): ?>
                        <option <?= ($id == ($defaults['food_type'] ?? -1)) ? 'selected' : '' ?> value=<?= $id ?>><?= $name ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="input-group">
                <label for="description">Description</label>
                <input type="text" id="description" name="description" maxlength="255" value="<?= $defaults['description'] ?? '' ?>" required>
            </div>
            <div class="input-group">
                <label for="price">Prix</label>
                <input type="number" id="price" name="price" step="0.01" value="<?= $defaults['price'] ?? '' ?>" required>
            </div>
            <div class="input-group">
                <label for="image_path">Emplacement de l'image</label>
                <input type="text" id="image_path" name="image_path" maxlength="255" placeholder="images/food/..." value="<?= $defaults['image_path'] ?? '' ?>" required>
            </div>
            <button type="submit" class="basic-btn"><?= $edit_id ? 'Modifier' : 'Créer' ?> le plat</button>
        </form>
    </div>
</main>
