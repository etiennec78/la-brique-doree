<?php
$title = "La Brique Dorée";
$h1 = "LA BRIQUE DOREE";
$staff_page = false;
$css_files = ['/css/slideshow.css'];
$js_files = ['/js/slider.js'];
include __DIR__ . '/../includes/header.php';
?>
<main>
    <div id="slideshow-frame">
        <button id="prev" class="slide-btn">❮</button>
        <button id="next" class="slide-btn">❯</button>

        <div id="slideshow">
            <div class="slide">
                <img src="/assets/images/RestaurantPhoto1.jpg" alt="Une photo de l'intérieur du restaurant">
                <div class="rectangle">
                    <h2> RESERVEZ DES MAINTENANT </h2>
                    <p>
                        Vous souhaitez vivre l’expérience sur place ? Réservez dès maintenant votre table en appelant notre restaurant au 01 34 25 10 10. Notre équipe sera ravie de vous accueillir dans une ambiance conviviale et 100% LEGO !
                    </p>
                </div>
            </div>
            <div class="slide">
                <img src="/assets/images/food/dish/ramen.webp" alt="Une photo d'un bol de ramen.">
                <div class="rectangle">
                    <h2> NOTRE PLAT LE PLUS CONNU </h2>
                    <p> Notre plat le plus célèbre est sans aucun doute notre incroyable ramen LEGO ! Préparé avec des briques soigneusement assemblées, il est aussi beau que délicieux. Ses couleurs vives et ses nombreux détails en font le favori de nos clients. Une création unique qui fait la renommée de notre restaurant !</p>
                </div>
            </div>
            <div class="slide">
                <img src="/assets/images/RestaurantPhoto2.jpg" alt="Une photo de l'intérieur du restaurant">
                <div class="rectangle">
                    <h2> COMMANDEZ DES MAINTENANT </h2>
                    <p>Envie d’un délicieux repas 100% briques ? Commandez dès maintenant vos plats préférés directement depuis notre site ! Rendez-vous dans la section Nos produits pour découvrir toutes nos spécialités colorées et gourmandes. En quelques clics, votre commande sera prête à être dégustée !</p>
                </div>
            </div>
            <div class="slide">
                <img src="/assets/images/food/dish/burger.png" alt="Une photo d'un burger avec des frites.">
                <div class="rectangle">
                    <h2> NOTRE PLAT DU JOUR </h2>
                    <p> Aujourd’hui, découvrez notre plat du jour : un savoureux burger LEGO accompagné de ses frites croustillantes. Un classique revisité façon briques, parfait pour les petits comme pour les grands constructeurs affamés !</p>
                </div>
            </div>
        </div>
    </div>
</main>
<?php include __DIR__ . '/../includes/footer.php'; ?>
