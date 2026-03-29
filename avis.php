<?php
session_start();
include_once 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (isset($_SESSION['user'])) {
    $user_id = $_SESSION['user']['id'];
    
    // Vérifier si l'utilisateur a un prénom et un nom
    $stmt_check = $pdo->prepare("SELECT first_name, last_name FROM users WHERE id = ?");
    $stmt_check->execute([$user_id]);
    $user_data = $stmt_check->fetch();
    
    if ($user_data && !empty($user_data['first_name']) && !empty($user_data['last_name'])) {
      $comment = $_POST['comment'];
      $product = $_POST['product'];
      $delivery = $_POST['delivery'];

      // Ajouter l'avis à la base de données
      try {
        $stmt = $pdo->prepare("INSERT INTO reviews (user_id, product_stars, delivery_stars, comment) VALUES (?, ?, ?, ?)");
        $stmt->execute([$user_id, $product, $delivery, $comment]);
      } catch (\PDOException $e) {
        $error = "Erreur lors de l'insertion : " . $e->getMessage();
      }
    } else {
      $error = "Vous devez renseigner votre prénom et nom dans votre profil pour laisser un avis.";
    }
  } else {
    $error = "Vous devez être connecté pour laisser un avis.";
  }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Avis - La Brique Dorée</title>
    <link rel="icon" type="image/x-icon" href="./images/favicon.png">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="avis.css">
</head>
<body>
    <header>
        <div id="main-header">
            <img id="logo" src="./images/LOGO.png" alt="Logo">
            <h1>NOS AVIS</h1>
            <a href="commands.php">
                <img id="cart" class="icon" src="./images/cart.svg">
                <p id="cart_items" class="bubble">10</p>
            </a>
            <video class="video-background" autoplay muted loop>
                <source src="./images/header_background.mp4" type="video/mp4">
            </video>
        </div>
       
        <section id="navbar-header">
            <a href="index.php" id="navbarbutton">Accueil</a>
            <a href="presentation.php" id="navbarbutton">Nos produits</a>
            <a href="avis.php" id="navbarbutton">Avis</a>

            <?php if (isset($_SESSION['user'])): ?>
                <a href="profile.php" id="navbarbutton">Mon Profil</a>

            <?php if ($_SESSION['user']['role'] === 'administrator'): ?>
                <a href="admin.php" id="navbarbutton">Panel Admin</a>
                
            <?php elseif ($_SESSION['user']['role'] === 'restaurateur'): ?>
                <a href="restaurateur.php" id="navbarbutton">Gestion Commandes</a>
                
            <?php elseif ($_SESSION['user']['role'] === 'delivery_person'): ?>
                <a href="delivery.php" id="navbarbutton">Mes Livraisons</a>
            <?php endif; ?>

            <a href="logout.php" id="navbarbutton" style="color: #ff4d4d;">Déconnexion</a>

            <?php else: ?>
                <a href="login.php" id="navbarbutton">Connexion</a>
            <?php endif; ?>
        </section>

    </header>

    <main>
        <h2 id="reviews-title">~ Avis Clients ~</h2>
        <?php
        include_once 'db_connect.php';

        try {
          $stmt = $pdo->prepare("SELECT u.first_name, u.last_name, r.product_stars, r.delivery_stars, r.comment FROM reviews r JOIN users u ON r.user_id = u.id");
          $stmt->execute();
          $reviews = $stmt->fetchAll();
          foreach($reviews as $review) {
            $first_name = $review['first_name'];
            $last_name = $review['last_name'];
            $product = $review['product_stars'];
            $delivery = $review['delivery_stars'];
            $comment = $review['comment'];

            echo '<table class="review-block">
            <tr>
              <th class="user-name">'. "$first_name $last_name" .'</th>
              <td class="user-ratings">
                <p>Produits : </p><p class="stars">'. str_repeat('★', $product) .'</p>
                <p>Livraison : </p><p class="stars">'. str_repeat('★', $delivery) .'</p>
              </td>
            </tr>
            <tr>
              <td colspan="2" class="review-text">
                <p>'. $comment .'</p>
              </td>
            </tr>
          </table>';
          }
        } catch (\PDOException $e) {
          $error = "Erreur de base de données : " . $e->getMessage();
        }
        ?>

    <?php
    $can_review = false;
    $missing_info = false;
    $not_connected = true;

    if (isset($_SESSION['user']['id'])) {
        $not_connected = false;
        try {
            $stmt_check = $pdo->prepare("SELECT first_name, last_name FROM users WHERE id = ?");
            $stmt_check->execute([$_SESSION['user']['id']]);
            $current_user = $stmt_check->fetch();
            if ($current_user && !empty($current_user['first_name']) && !empty($current_user['last_name'])) {
                $can_review = true;
            } else {
                $missing_info = true;
            }
        } catch (\PDOException $e) {
            $error = "Erreur : " . $e->getMessage();
        }
    }
    ?>

    <?php if ($can_review): ?>
    <form action="avis.php" method="post">
        <table class="review-block">
            <tr>
                <th class="user-name">LAISSER UN AVIS</th>
                <td class="user-ratings">
                    <?php if(isset($error)): ?>
                        <div style="color: #e74c3c; margin-bottom: 10px; font-weight: bold;">
                            <?php echo htmlspecialchars($error); ?>
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
                    <button type="submit" name="submit_avis" class="btn-send">Envoyer l'avis</button>
                </td>
            </tr>
        </table>
    </form>
    <?php elseif ($missing_info): ?>
        <table class="review-block">
            <tr>
                <td id="review-unavailable">
                    <h3>Vous ne pouvez pas encorer laisser d'avis.</h3>
                    <p>Veuillez renseigner votre prénom et votre nom dans votre profil pour pouvoir écrire un avis.</p>
                    <button onclick="location.href='profile.php'" type="button">Compléter mon profil</button>
                </td>
            </tr>
        </table>
    <?php else: ?>
        <table class="review-block">
            <tr>
                <td id="review-unavailable">
                    <h3>Connectez-vous pour laisser un avis !</h3>
                    <p>Vous devez avoir un compte et le compléter pour laisser un avis sur nos produits et la livraison.</p>
                    <button onclick="location.href='login.php'" type="button">Me connecter</button>
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
