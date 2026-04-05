<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>La Brique Dorée - Profil</title>
    <link rel="icon" type="image/x-icon" href="/assets/images/favicon.png">
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/form.css">
    <link rel="stylesheet" href="/css/profile.css">
  </head>
  <body>
    <header>
        <div id="main-header">
            <img id="logo" src="/assets/images/LOGO.png" alt="Logo d'une brique LEGO dorée">
            <h1>PROFIL</h1>
            <a href="/orders">
                <img id="cart" class="icon" src="/assets/images/cart.svg" alt="Icône de panier de courses">
                <p id="cart_items" class="bubble"><?php echo $cart_count; ?></p>
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
        <div class="form-page">
            <h2>Profil</h2>
            <form action="/profile" method="post">
                <input type="hidden" name="user_id" value="<?php echo $target; ?>">
                <div class="input-group">
                    <label for="first_name">Prénom</label>
                    <input type="text" id="first_name" name="first_name" value="<?php echo $user_data['first_name']; ?>" required>
                </div>
                <div class="input-group">
                    <label for="last_name">Nom</label>
                    <input type="text" id="last_name" name="last_name" value="<?php echo $user_data['last_name']; ?>" required>
                </div>
                <div class="input-group">
                    <label for="street_nb">Adresse</label>
                    <div id="address-group">
                        <input type="number" id="street_nb" name="street_nb" value="<?php echo $user_data['street_nb']; ?>" required>
                        <select name="street_nb_suf" id="street_nb_suf" value="<?php echo $user_data['street_nb_suf']; ?>">
                            <option value=""></option>
                            <option value="bis">Bis</option>
                            <option value="ter">Ter</option>
                            <option value="quater">Quater</option>
                            <option value="quinquiens">Quinquiens</option>
                        </select>
                        <input type="text" id="street" name="street" value="<?php echo $user_data['street']; ?>" required>
                    </div>
                </div>
                <div class="input-group">
                   
                    <label for="zip_code">Code postal</label>
                    <input type="number" id="zip_code" name="zip_code" value="<?php echo $user_data['zip_code']; ?>" required>
                </div>
                <div class="input-group">
                    <label for="phone">Numéro de téléphone</label>
                    <input type="tel" id="phone" name="phone" value="<?php echo $user_data['phone']; ?>" required>
                </div>
                <div class="input-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo $user_data['email']; ?>" required>
                </div>
                <div class="input-group">
                    <label for="intercom_code">Code interphone (optionnel)</label>
                    <input type="text" id="intercom_code" name="intercom_code" value="<?php echo $user_data['intercom_code']; ?>">
                </div>
                <div class="input-group">
                    <label for="birth_date">Date de naissance (optionnel)</label>
                    <input type="date" id="birth_date" name="birth_date" value="<?php echo $user_data['birth_date']; ?>">
                </div>
                <button type="submit" class="basic-btn">Mettre à jour les informations</button>
            </form>
        </div>
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
