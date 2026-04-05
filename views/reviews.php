<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Avis - La Brique Dorée</title>
    <link rel="icon" type="image/x-icon" href="/assets/images/favicon.png">
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/reviews.css">
</head>
<body>
    <header>
        <div id="main-header">
            <img id="logo" src="/assets/images/LOGO.png" alt="Logo">
            <h1>NOS AVIS</h1>
            <a href="/orders">
                <img id="cart" class="icon" src="/assets/images/cart.svg">
                <p id="cart_items" class="bubble"><?= $cart_count ?></p>
            </a>
            <video class="video-background" autoplay muted loop>
                <source src="/assets/images/header_background.mp4" type="video/mp4">
            </video>
        </div>
       
        <section id="navbar-header">
            <a href="/" class="navbarbutton">Accueil</a>
            <a href="/products" class="navbarbutton">Nos produits</a>
            <a href="/reviews" class="navbarbutton">Avis</a>

            <?php if (isset($_SESSION['user'])): ?>
                <a href="/profile" class="navbarbutton">Mon Profil</a>

            <?php if ($_SESSION['user']['role'] === 'administrator'): ?>
                <a href="/admin" class="navbarbutton">Panel Admin</a>
                
            <?php elseif ($_SESSION['user']['role'] === 'restaurateur'): ?>
                <a href="/restaurateur" class="navbarbutton">Gestion Commandes</a>
                
            <?php elseif ($_SESSION['user']['role'] === 'delivery_person'): ?>
                <a href="/delivery" class="navbarbutton">Mes Livraisons</a>
            <?php endif; ?>

            <a href="/logout" class="navbarbutton alert">Déconnexion</a>

            <?php else: ?>
                <a href="/login" class="navbarbutton">Connexion</a>
            <?php endif; ?>
        </section>

    </header>

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
                    <textarea name="comment" placeholder="Partagez votre expérience ici..." required></textarea>
                    <button type="submit" name="submit_avis" class="basic-btn btn-send">Envoyer l'avis</button>
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

    <footer>
        <div id="contact">
            <h4>Venez vivre l'expérience LEGO</h4>
            <p>
                La Brique Dorée
                <br/>
                <a href="https://www.google.fr/maps/dir//Lego+House,+Ole+Kirks+Plads+1,+7190+Billund,+Danemark">1 Place Ole Kirks, 7190 Billund, Danemark</a>
                <br/><br/>
                <a href="mailto:contact@labriquedoree.fr">contact@labriquedoree.fr</a>
                <br/>
                <a href="tel:+33134251010">01 34 25 10 10</a>
                <br/><br/>
                <a href="https://www.google.com/search?tbm=lcl&kgmid=/g/11bwcc8tz6&rldimm=16393462302552454915#lkt=LocalPoiReviews">Google</a>,
                <a href="https://www.tripadvisor.fr/Attraction_Review-g189531-d12928696-Reviews-LEGO_House-Billund_South_Jutland_Jutland.html">Tripadvisor</a>,
                <a href="https://www.instagram.com/legohouse">Insta</a>,
                <a href="https://www.facebook.com/OfficialLEGOHOUSE">Facebook</a>
            </p>
        </div>
        <div id="hours">
            <h4>Horaires</h4>
            <p>
                <b>Mardi – Jeudi</b>
                <br/>12h–14h / 19h–21h30
                <br/><br/>
                <b>Mercredi</b>
                <br/>12h–14h
                <br/><br/>
                <b>Vendredi – Samedi</b>
                <br/>12h–14h / 19h–22h
                <br/><br/>
                <b>Dimanche – Lundi : Fermé</b>
            </p>
        </div>
        <div id="map">
            <h4>Nous trouver</h4>
            <iframe src="https://www.openstreetmap.org/export/embed.html?bbox=9.113277196884157%2C55.72994659971866%2C9.11605328321457%2C55.73135269752343&marker=55.730662,9.114866&amp;layer=mapnik"></iframe>
        </div>
    </footer>
</body>
</html>
