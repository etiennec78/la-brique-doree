<?php
$title = "Admin - La Brique Dorée";
$h1 = "ADMINISTRATEUR";
$show_cart = false;
$show_video = true;
$css_files = ['/css/admin.css'];
include __DIR__ . '/../includes/header.php';
?>
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
                        <option value="cook" <?= (($_GET['role'] ?? '') === 'cook') ? 'selected' : '' ?>>Restaurateurs</option>
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
                            <td><a href="/order_history?user_id=<?= $user_data['id'] ?>" class="action-link"><?= $user_data['orders'] ?></a></td>
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
<?php include __DIR__ . '/../includes/footer.php'; ?>
