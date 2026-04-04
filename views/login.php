<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    include_once '../src/db_connect.php';

    try {
        $stmt = $pdo->prepare("SELECT u.*, r.name as role_name FROM users u JOIN role r ON u.role_id = r.id WHERE u.email = ?");
        $stmt->execute([$email]);
        $user_found = $stmt->fetch();

        if ($user_found && password_verify($password, $user_found['password_hash'])) {
            $user_found['role'] = $user_found['role_name'];
            
            $_SESSION['user'] = $user_found;

            if ($user_found['role'] === 'administrator') {
                header("Location: ./admin.php");
            } elseif ($user_found['role'] === 'restaurateur') {
                header("Location: ./restaurateur.php");
            } elseif ($user_found['role'] === 'delivery_person') {
                header("Location: ./delivery.php");
            } else {
                header("Location: ../public/index.php");
            }
            exit(); 
        } else {
            $erreur = "Email ou mot de passe incorrect.";
        }
    } catch (\PDOException $e) {
        $erreur = "Erreur de base de données : " . $e->getMessage();
    }
}
?>

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
            <a href="../public/index.php" class="navbarbutton">Accueil</a>
            <a href="./presentation.php" class="navbarbutton">Nos produits</a>
            <a href="./reviews.php" class="navbarbutton">Avis</a>
            <a href="./login.php" class="navbarbutton">Connexion</a>
        </section>
    </header>

    <main>
        <div class="form-page">
            <h2>Connexion</h2>

            <?php if(isset($erreur)): ?>
                <p class="alert"><?php echo $erreur; ?></p>
            <?php endif; ?>

            <form action="./login.php" method="post">
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
