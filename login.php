<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email_saisi = $_POST['email'];
    $password_saisi = $_POST['password'];

    if (file_exists('users.json')) {
        $json_data = file_get_contents('users.json');
        $utilisateurs = json_decode($json_data, true);

        $user_found = null;


        foreach ($utilisateurs as $user) {
            if ($user['email'] === $email_saisi && $user['password'] === $password_saisi) {
                $user_found = $user;
                break;
            }
        }

        if ($user_found) {
           
            $_SESSION['user'] = $user_found;

            if ($user_found['role'] === 'admin') {
                header("Location: admin.php");
            } elseif ($user_found['role'] === 'restaurateur') {
                header("Location: restaurateur.php");
            } elseif ($user_found['role'] === 'livreur') {
                header("Location: delivery.php");
            } else {
                header("Location: index.php");
            }
            exit(); 
        } else {
            $erreur = "Email ou mot de passe incorrect.";
        }
    } else {
        $erreur = "Erreur : le fichier users.json est introuvable à la racine.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>La Brique Dorée - Connexion</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="form.css">
</head>
<body>
    <header>
        <div id="main-header">
            <img id="logo" src="./images/LOGO.png" alt="Logo">
            <h1>CONNEXION</h1>
        </div>
        
         <section id="navbar-header">
            <a href="index.php" id="navbarbutton">Accueil</a>
            <a href="presentation.php" id="navbarbutton">Nos produits</a>
            <a href="avis.php" id="navbarbutton">Avis</a>
            <a href="login.php" id="navbarbutton">Connexion</a>
        </section>

    <main>
        <div class="form-page">
            <h2>Connexion</h2>

            <?php if(isset($erreur)): ?>
                <p style="color: red; text-align: center; font-weight: bold;"><?php echo $erreur; ?></p>
            <?php endif; ?>

            <form action="login.php" method="post">
                <div class="input-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="input-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" class="btn">Se connecter</button>
            </form>
            <p>Pas encore de compte ? <a href="register.php">Inscrivez-vous</a></p>
        </div>
    </main>
</body>
</html>