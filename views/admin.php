<?php
$title = "Admin - La Brique Dorée";
$h1 = "ADMINISTRATEUR";
$staff_page = true;
$css_files = ['/css/admin.css'];
$js_files = ['/js/admin.js'];
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
                    <noscript>
                        <button type="submit" class="action-link" onchange="this.form.submit()" style="margin-left: 10px; cursor: pointer;">Filtrer</button>
                    </noscript>
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

                    $is_banned = !empty($user_data['banned']);
                ?>
                    <tr id="user-row-<?= $user_data['id'] ?>" class="<?= $is_banned ? 'banned' : '' ?>">
                        <td><?= $user_data['id'] ?></td>
                        <td><strong><?= getName($user_data) ?></strong></td>
                        <td><a href="mailto:<?= $user_data['email'] ?>" class="action-link"><?= $user_data['email'] ?></a></td>
                        <td><a href="/order_history?user_id=<?= $user_data['id'] ?>" class="action-link"><?= $user_data['orders'] ?></a></td>
                        <td>
                            <form action="/global_reduction" method="POST">
                                <input type="hidden" name="user_id" value="<?= $user_data['id'] ?>">
                                <input type="number" name="reduction" min=0 max=100 value="<?= $user_data['global_reduction'] * 100 ?>" onchange="this.form.submit()">
                                <noscript>
                                    <button id="manage" type="submit" class="action-link">Appliquer</button>
                                </noscript>
                            </form>
                        </td>
                        <td><?= $user_data['role'] ?></td>
                        <td>
                            <div>
                                <a href="/profile?user_id=<?= $user_data['id'] ?>" class="action-link">Gérer</a>

                                <button class="ban-btn action-link" 
                                        data-user-id="<?= $user_data['id'] ?>" 
                                        data-banned="<?= $is_banned ? '1' : '0' ?>">
                                    <?= $is_banned ? 'Débannir' : 'Bannir' ?>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>

<?php
  if (isset($_SESSION['error'])){
    unset($_SESSION['error']);
  }
  include __DIR__ . '/../includes/footer.php'; 
?>