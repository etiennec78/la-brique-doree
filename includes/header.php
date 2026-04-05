<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'La Brique Dorée' ?></title>
    <link rel="icon" type="image/x-icon" href="/assets/images/favicon.png">
    <link rel="stylesheet" href="/css/style.css">
    <?php if (isset($css_files)): foreach ($css_files as $css): ?>
        <link rel="stylesheet" href="<?= htmlspecialchars($css) ?>">
    <?php endforeach; endif; ?>
</head>
<body>
    <header>
        <div id="main-header">
            <img id="logo" src="/assets/images/LOGO.png" alt="Logo">
            <h1><?= $h1 ?? 'LA BRIQUE DOREE' ?></h1>
            
            <?php if (isset($show_cart) && $show_cart): ?>
            <a href="/orders">
                <img id="cart" class="icon" src="/assets/images/cart.svg" alt="Icône de panier de courses">
                <p id="cart_items" class="bubble"><?= $cart_count ?? 0 ?></p>
            </a>
            <?php endif; ?>

            <?php if (isset($show_video) && $show_video): ?>
            <video class="video-background" autoplay muted loop>
                <source src="/assets/images/header_background.mp4" type="video/mp4">
            </video>
            <?php endif; ?>
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