<?php
$title = "La Brique Dorée - Créateur de menu";
$h1 = "CREATEUR DE MENU";
$staff_page = true;
$css_files = ['/css/form.css'];
include __DIR__ . '/../includes/header.php';
?>
<main>
    <div class="form-page">
        <form action="/create_menu" method="post">
            <div class="input-group">
                <label for="name">Nom</label>
                <input type="text" id="name" name="name" maxlength="30" required>
                <label for="description">Description</label>
                <input type="text" id="description" name="description" maxlength="255" required>
                <label for="price">Prix</label>
                <input type="number" id="price" name="price" step="0.01" required>
            </div>
            <button type="submit" class="basic-btn">Créer le menu</button>
        </form>
    </div>
</main>
