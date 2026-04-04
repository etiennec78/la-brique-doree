<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>La Brique Dorée - Connexion</title>
    <link rel="stylesheet" href="../public/css/style.css">
    <link rel="stylesheet" href="../public/css/form.css">
</head>
<body>
    <header>
        <div id="main-header">
            <img id="logo" src="../public/assets/images/LOGO.png" alt="Logo">
            <h1>CONNEXION</h1>
        </div>
        
         <section id="navbar-header">
            <a href="./home.php" class="navbarbutton">Accueil</a>
            <a href="./presentation.php" class="navbarbutton">Nos produits</a>
            <a href="./reviews.php" class="navbarbutton">Avis</a>
            <a href="./login.php" class="navbarbutton">Connexion</a>
        </section>
    </header>

    <main>
        <div class="form-page">
            <h2>Connexion</h2>

            <?php if(isset($error)): ?>
                <p class="alert"><?php echo $error; ?></p>
            <?php endif; ?>

            <form action="/login" method="post">
                <div class="input-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="input-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" class="basic-btn">Se connecter</button>
            </form>
            <p>Pas encore de compte ? <a href="./register.php">Inscrivez-vous</a></p>
        </div>
    </main>
</body>
</html>
