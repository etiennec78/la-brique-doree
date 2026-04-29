<?php
$title = "Avis - La Brique Dorée";
$h1 = "NOS AVIS";
$show_cart = true;
$show_video = true;
$css_files = ['/css/reviews.css'];
include __DIR__ . '/../includes/header.php';
?>
<main>
        <h2 id="reviews-title">~ Avis Clients ~</h2>
        <?php foreach($reviews as $review): ?>
            <?php if (isset($_GET['edit']) && $_GET['edit'] == $review['id'] && isset($_SESSION['user']) && ($_SESSION['user']['id'] == $review['user_id'] || $is_admin)): ?>
                <form action="/reviews" method="post" class="review-block">
                    <input type="hidden" name="review_id" value="<?= $review['id'] ?>">
                    <table class="review-block edit-review-table">
                        <tr>
                            <th class="user-name">MODIFIER VOTRE AVIS</th>
                            <td class="user-ratings">
                                <div class="rating-group">
                                    <label for="product-<?= $review['id'] ?>">Produit :</label>
                                    <select name="product" id="product-<?= $review['id'] ?>" class="select-note" required>
                                        <option value="5" <?= $review['product_stars'] == 5 ? 'selected' : '' ?>>★★★★★</option>
                                        <option value="4" <?= $review['product_stars'] == 4 ? 'selected' : '' ?>>★★★★</option>
                                        <option value="3" <?= $review['product_stars'] == 3 ? 'selected' : '' ?>>★★★</option>
                                        <option value="2" <?= $review['product_stars'] == 2 ? 'selected' : '' ?>>★★</option>
                                        <option value="1" <?= $review['product_stars'] == 1 ? 'selected' : '' ?>>★</option>
                                    </select>
                                </div>
                                <div class="rating-group">
                                    <label for="delivery-<?= $review['id'] ?>">Livraison :</label>
                                    <select name="delivery" id="delivery-<?= $review['id'] ?>" class="select-note" required>
                                        <option value="5" <?= $review['delivery_stars'] == 5 ? 'selected' : '' ?>>★★★★★</option>
                                        <option value="4" <?= $review['delivery_stars'] == 4 ? 'selected' : '' ?>>★★★★</option>
                                        <option value="3" <?= $review['delivery_stars'] == 3 ? 'selected' : '' ?>>★★★</option>
                                        <option value="2" <?= $review['delivery_stars'] == 2 ? 'selected' : '' ?>>★★</option>
                                        <option value="1" <?= $review['delivery_stars'] == 1 ? 'selected' : '' ?>>★</option>
                                    </select>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="review-text">
                                <textarea name="comment" required><?= htmlspecialchars($review['comment']) ?></textarea>
                                <div class="edit-review-actions">
                                    <button type="submit" name="submit_avis" class="basic-btn btn-send">Mettre à jour</button>
                                    <a href="/reviews" class="basic-btn gray-btn btn-cancel">Annuler</a>
                                </div>
                            </td>
                        </tr>
                    </table>
                </form>
            <?php else: ?>
                <table class="review-block">
                <tr>
                  <th class="user-name">
                      <?= getName($review) ?>
                  </th>
                  <td class="user-ratings">
                    <p>Produits : </p><p class="stars"><?= str_repeat('★', $review['product_stars']) ?></p>
                    <p>Livraison : </p><p class="stars"><?= str_repeat('★', $review['delivery_stars']) ?></p>
                  </td>
                </tr>
                <tr>
                  <td colspan="2" class="review-text">
                    <p><?= htmlspecialchars($review['comment']) ?></p>
                    <?php if (isset($_SESSION['user']) && ($_SESSION['user']['id'] == $review['user_id'] || $is_admin)): ?>
                        <a href="?edit=<?= $review['id'] ?>" title="Modifier" class="edit-icon-link">
                            <img src="/assets/images/pencil.svg" alt="Modifier" class="edit-icon">
                        </a>
                    <?php endif; ?>
                  </td>
                </tr>
              </table>
            <?php endif; ?>
        <?php endforeach; ?>

    <?php if ($logged_in && $user_can_review): ?> 
    <form action="/reviews" method="post">
        <table class="review-block">
            <tr>
                <th class="user-name">LAISSER UN AVIS</th>
                <td class="user-ratings">
                    <?php if(isset($error)): ?>
                        <div class="alert">
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>

                    <div class="rating-group">
                        <label for="product">Produit :</label>
                        <select name="product" id="product" class="select-note" required>
                            <option value="5">★★★★★</option>
                            <option value="4">★★★★</option>
                            <option value="3">★★★</option>
                            <option value="2">★★</option>
                            <option value="1">★</option>
                        </select>
                    </div>

                    <div class="rating-group">
                        <label for="delivery">Livraison :</label>
                        <select name="delivery" id="delivery" class="select-note" required>
                            <option value="5">★★★★★</option>
                            <option value="4">★★★★</option>
                            <option value="3">★★★</option>
                            <option value="2">★★</option>
                            <option value="1">★</option>
                        </select>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="2" class="review-text">
                    <textarea id="review-comm" placeholder="Partagez votre expérience ici (250 caractères min.)..." oninput="compter()" required></textarea>
                    <p class="counter-text"><span id="nb-caracteres">0</span> / 250</p>
                    <button type="submit" id="submit-avis" name="submit_avis" class="basic-btn btn-send" disabled>Envoyer l'avis</button>
                </td>
            </tr>
        </table>
    </form>
    <?php elseif ($logged_in && (! $user_can_review)): ?>
        <table class="review-block">
            <tr>
                <td id="review-unavailable">
                    <h3>Vous ne pouvez pas encorer laisser d'avis.</h3>
                    <p>Vous devez renseigner votre prénom et votre nom dans votre profil, et avoir passé au moins une commande pour pouvoir écrire un avis.</p>
                    <button onclick="location.href='/profile'" type="button" class="basic-btn">Compléter mon profil</button>
                    <button onclick="location.href='/products'" type="button" class="basic-btn">Passer une commande</button>
                </td>
            </tr>
        </table>
    <?php else: ?>
        <table class="review-block">
            <tr>
                <td id="review-unavailable">
                    <h3>Connectez-vous pour laisser un avis !</h3>
                    <p>Vous devez avoir un compte et le compléter pour laisser un avis sur nos produits et la livraison.</p>
                    <button onclick="location.href='/login'" type="button" class="basic-btn">Me connecter</button>
                </td>
            </tr>
        </table>
    <?php endif; ?>
    </main>

<script>
    function compter() {
        let zone = document.getElementById("review-comm");
        let chiffre = document.getElementById("nb-caracteres");
        let bouton = document.getElementById("submit-avis");
        let longueur = zone.value.length;
        
        chiffre.innerText = longueur;
        
        if (longueur < 250) {
            chiffre.style.color = "red";
            bouton.disabled = true;
            bouton.style.opacity = "0.5";
            bouton.style.cursor = "not-allowed";
        } else {
            chiffre.style.color = "var(--solid-gold)";
            bouton.disabled = false;
            bouton.style.opacity = "1";
            bouton.style.cursor = "pointer";
        }
    }
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
