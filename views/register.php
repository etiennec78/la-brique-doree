<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>La Brique Dorée - Inscription</title>
    <link rel="icon" type="image/x-icon" href="/assets/images/favicon.png">
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/form.css">
  </head>
  <body>
    <header>
        <div id="main-header">
            <img id="logo" src="/assets/images/LOGO.png" alt="Logo d'une brique LEGO dorée">
            <h1>INSCRIPTION</h1>
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
            <h2>Inscription</h2>
            <form action="/register" method="post">
                <div class="input-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="input-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" class="basic-btn">S'inscrire</button>
            </form>
            <p>Déjà un compte ? <a href="/login">Connexion</a></p>
        </div>
    </main>
  </body>
</html>
