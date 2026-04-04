<?php 
session_start(); 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include_once '../src/db_connect.php';

    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Obtenir l'id du rôle "client" dans la base de données
    $stmtRole = $pdo->prepare("SELECT id FROM role WHERE name = 'client'");
    $stmtRole->execute();
    $role = $stmtRole->fetch();
    $role_id = $role ? $role['id'] : 1;

    // Ajouter l'utilisateur à la base de données
    try {
        $stmt = $pdo->prepare("INSERT INTO users (email, password_hash, role_id, inscription_date) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$email, $password, $role_id]);
        
        $newUser = [
            "id" => $pdo->lastInsertId(),
            "email" => $email,
            "role" => "client"
        ];

        $_SESSION['user'] = $newUser;
        header("Location: ./home.php");
        exit();
    } catch (\PDOException $e) {
        $erreur = "Erreur lors de l'inscription : " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>La Brique Dorée - Inscription</title>
    <link rel="icon" type="image/x-icon" href="../public/assets/images/favicon.png">
    <link rel="stylesheet" href="../public/css/style.css">
    <link rel="stylesheet" href="../public/css/form.css">
  </head>
  <body>
    <header>
        <div id="main-header">
            <img id="logo" src="../public/assets/images/LOGO.png" alt="Logo d'une brique LEGO dorée">
            <h1>INSCRIPTION</h1>
            <video class="video-background" autoplay muted loop>
                <source src="../public/assets/images/header_background.mp4" type="video/mp4">
            </video>
        </div>
        
        <section id="navbar-header">
            <a href="./home.php" class="navbarbutton">Accueil</a>
            <a href="./presentation.php" class="navbarbutton">Nos produits</a>
            <a href="./reviews.php" class="navbarbutton">Avis</a>

            <?php if (isset($_SESSION['user'])): ?>
                <a href="./profile.php" class="navbarbutton">Mon Profil</a>

                <?php if ($_SESSION['user']['role'] === 'administrator'): ?>
                    <a href="./admin.php" class="navbarbutton">Panel Admin</a>
                <?php elseif ($_SESSION['user']['role'] === 'restaurateur'): ?>
                    <a href="./restaurateur.php" class="navbarbutton">Gestion Commandes</a>
                <?php elseif ($_SESSION['user']['role'] === 'delivery_person'): ?>
                    <a href="./delivery.php" class="navbarbutton">Mes Livraisons</a>
                <?php endif; ?>

                <a href="../src/logout.php" class="navbarbutton alert">Déconnexion</a>

            <?php else: ?>
                <a href="./login.php" class="navbarbutton">Connexion</a>
            <?php endif; ?>
        </section>

    </header>
    <main>
        <div class="form-page">
            <h2>Inscription</h2>
            <form action="./register.php" method="post">
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
            <p>Déjà un compte ? <a href="./login.php">Connexion</a></p>
        </div>
    </main>
  </body>
</html>
