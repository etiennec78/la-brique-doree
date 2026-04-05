<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - La Brique Dorée</title>
    <link rel="icon" type="image/x-icon" href="/assets/images/favicon.png">
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/admin.css">
</head>
<body>
    <header>
        <div id="main-header">
            <img id="logo" src="/assets/images/LOGO.png" alt="Logo">
            <h1>ADMINISTRATEUR</h1>
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
    <main class="admin-main">
        <div class="panel">
            <div class="panel-header">
                <h2>Gestion des comptes utilisateurs</h2>
                <form method="GET" action="" class="controls">
                    <input type="text" name="search" placeholder="Rechercher un nom ou ID..." class="search-bar" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                    <select name="role" class="filter-select">
                        <option value="">Tous les profils</option>
                        <option value="user" <?= (($_GET['role'] ?? '') === 'user') ? 'selected' : '' ?>>Clients actifs</option>
                        <option value="delivery_person" <?= (($_GET['role'] ?? '') === 'delivery_person') ? 'selected' : '' ?>>Livreurs</option>
                        <option value="restaurateur" <?= (($_GET['role'] ?? '') === 'restaurateur') ? 'selected' : '' ?>>Restaurateurs</option>
                    </select>
                    <button type="submit" class="action-link" style="margin-left: 10px; cursor: pointer;">Filtrer</button>
                </form>
            </div>

            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Utilisateur</th>
                        <th>Adresse Email</th>
                        <th>Commandes</th>
                        <th>Réduction globale (%)</th>
                        <th>Rôle</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $search = strtolower(trim($_GET['search'] ?? ''));
                    $role_filter = $_GET['role'] ?? '';
                    
                    foreach($users_data as $user_data): 
                        $user_name = strtolower(getName($user_data));
                        $user_id = strtolower((string)$user_data['id']);
                        $user_role = $user_data['role'];
                        
                        // Application des filtres
                        if ($search !== '' && !str_contains($user_name, $search) && !str_contains($user_id, $search)) {
                            continue;
                        }
                        if ($role_filter !== '' && $user_role !== $role_filter) {
                            continue;
                        }
                    ?>
                        <tr class="<?= !empty($user_data['banned']) ? 'banned' : '' ?>">
                            <td><?= $user_data['id'] ?></td>
                            <td><strong><?= getName($user_data) ?></strong></td>
                            <td><?= $user_data['email'] ?></td>
                            <td><?= $user_data['total_quantity'] ?></td>
                            <td>
                                <form action="/global_reduction" method="POST">
                                    <input type="hidden" name="user_id" value="<?= $user_data['id'] ?>">
                                    <input type="text" name="reduction" maxlength=3 size=3 value="<?= $user_data['global_reduction'] * 100 ?>">
                                    <button id="manage" type="submit" class="action-link">Appliquer</button>
                                </form>
                            </td>
                            <td><span class="tag gold"><?= $user_data['role'] ?></span></td>
                            <td>
                                <form action="/profile" method="POST">
                                    <input type="hidden" name="user_id" value="<?= $user_data['id'] ?>">
                                    <button id="manage" type="submit" class="action-link">Gérer</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
