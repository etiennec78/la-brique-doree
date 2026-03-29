<?php
session_start();
include_once 'db_connect.php';

// Obtenir les infos des utilisateurs dans la base de données
$stmt = $pdo->prepare("SELECT u.id, u.email, u.first_name, u.last_name, r.name as role, m.quantity FROM users u JOIN role r ON u.role_id = r.id JOIN cart c ON u.id = c.user_id JOIN cart_menu m ON c.id = m.cart_id");
$stmt->execute();
$users_data = $stmt->fetchAll();
if (!$users_data) {
    $users_data = [];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - La Brique Dorée</title>
    <link rel="icon" type="image/x-icon" href="./images/favicon.png">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <header>
        <div id="main-header">
            <img id="logo" src="./images/LOGO.png" alt="Logo">
            <h1>ADMINISTRATEUR</h1>
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
    <main class="admin-main">
        <div class="panel">
            <div class="panel-header">
                <h2>Gestion des comptes utilisateurs</h2>
                <div class="controls">
                    <input type="text" placeholder="Rechercher un nom ou ID..." class="search-bar">
                    <select class="filter-select">
                        <option>Tous les profils</option>
                        <option>Clients actifs</option>
                        <option>Livreurs</option>
                        <option>Restaurateurs</option>
                    </select>
                </div>
            </div>

            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Utilisateur</th>
                        <th>Adresse Email</th>
                        <th>Commandes</th> <th>Rôle</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach($users_data as $user_data) {
                    echo '<tr>
                        <td>'. $user_data['id'] .'</td>
                        <td><strong>'. $user_data['first_name'] .' '. $user_data['last_name'] .'</strong></td>
                        <td>'. $user_data['email'] .'</td>
                        <td>'. $user_data['quantity'] .'</td>
                        <td><span class="tag gold">'. $user_data['role'] .'</span></td>
                        <td><a href="profile.php" class="action-link">Gérer</a></td>
                    </tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
