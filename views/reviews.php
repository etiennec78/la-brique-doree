<?php
$title = "Avis - La Brique Dorée";
$h1 = "NOS AVIS";
$show_cart = true;
$show_video = true;
$css_files = ['/css/reviews.css'];
$js_files = ['/js/count_characters.js'];
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
    <?php if (!$logged_in): ?>
        <table class="review-block">
            <tr>
                <td id="review-unavailable">
                    <h3>Connectez-vous pour laisser un avis !</h3>
                    <p>Vous devez avoir un compte et le compléter pour laisser un avis sur nos produits et la livraison.</p>
                    <button onclick="location.href='/login'" type="button" class="basic-btn">Me connecter</button>
                </td>
            </tr>
        </table>
    <?php elseif (!$user_has_valid_info): ?>
        <table class="review-block">
            <tr>
                <td id="review-unavailable">
                    <h3>Vous ne pouvez pas laisser d'avis.</h3>
                    <p>Vous devez remplir votre profil pour laisser un avis.</p>
                    <button onclick="location.href='/profile'" type="button" class="basic-btn">Remplir votre profil</button>
                </td>
            </tr>
        </table>
    <?php elseif (!$user_can_review): ?>
        <table class="review-block">
            <tr>
                <td id="review-unavailable">
                    <h3>Vous ne pouvez pas laisser d'avis.</h3>
                    <p>Vous devez passer une nouvelle commande pour laisser un avis.</p>
                    <button onclick="location.href='/products'" type="button" class="basic-btn">Passer une commande</button>
                </td>
            </tr>
        </table>
    <?php else: ?> 
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
                    <textarea id="review-comm" name="comment" maxlength="255" placeholder="Partagez votre expérience ici..." oninput="count_char()" required></textarea>
                    <p class="counter-text"><span id="nb-caracteres">0</span> / 255</p>
                    <button type="submit" id="submit-avis" name="submit_avis" class="basic-btn btn-send" disabled>Envoyer l'avis</button>
                </td>
            </tr>
        </table>
    </form>
    <?php endif; ?>
    </main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
