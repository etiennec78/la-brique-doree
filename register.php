<?php 
session_start(); 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include_once 'db_connect.php';

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
        header("Location: index.php");
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
    <link rel="icon" type="image/x-icon" href="./images/favicon.png">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="form.css">
  </head>
  <body>
    <header>
        <div id="main-header">
            <img id="logo" src="./images/LOGO.png" alt="Logo d'une brique LEGO dorée">
            <h1>INSCRIPTION</h1>
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
        <div class="form-page">
            <h2>Inscription</h2>
            <form action="register.php" method="post">
                <div class="input-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="input-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" class="btn">S'inscrire</button>
            </form>
            <p>Déjà un compte ? <a href="login.php">Connexion</a></p>
        </div>
    </main>
  </body>
</html>
