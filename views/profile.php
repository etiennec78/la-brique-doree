<?php
$title = "La Brique Dorée - Profil";
$h1 = "PROFIL";
$show_cart = true;
$show_video = true;
$css_files = ['/css/form.css', '/css/profile.css'];
include __DIR__ . '/../includes/header.php';
?>
<main>
        <div class="form-page">
            <h2>Profil</h2>
            <form action="/profile" method="post">
                <input type="hidden" name="user_id" value="<?= $target ?>">
                <div class="input-group">
                    <label for="first_name">Prénom</label>
                    <input type="text" id="first_name" name="first_name" value="<?= $user_data['first_name'] ?>" required>
                </div>
                <div class="input-group">
                    <label for="last_name">Nom</label>
                    <input type="text" id="last_name" name="last_name" value="<?= $user_data['last_name'] ?>" required>
                </div>
                <div class="input-group">
                    <label for="street_nb">Adresse</label>
                    <div id="address-group">
                        <input type="number" id="street_nb" name="street_nb" min="1" value="<?= $user_data['street_nb'] ?>" required>
                        <select name="street_nb_suf" id="street_nb_suf" value="<?= $user_data['street_nb_suf'] ?>">
                            <option value=""></option>
                            <option value="bis">Bis</option>
                            <option value="ter">Ter</option>
                            <option value="quater">Quater</option>
                            <option value="quinquiens">Quinquiens</option>
                        </select>
                        <input type="text" id="street" name="street" value="<?= $user_data['street'] ?>" required>
                    </div>
                </div>
                <div class="input-group">
                   
                    <label for="zip_code">Code postal</label>
                    <input type="number" id="zip_code" name="zip_code" value="<?= $user_data['zip_code'] ?>" required>
                </div>
                <div class="input-group">
                    <label for="phone">Numéro de téléphone</label>
                    <input type="tel" id="phone" name="phone" value="<?= $user_data['phone'] ?>" required>
                </div>
                <div class="input-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?= $user_data['email'] ?>" required>
                </div>
                <div class="input-group">
                    <label for="intercom_code">Code interphone (optionnel)</label>
                    <input type="text" id="intercom_code" name="intercom_code" value="<?= $user_data['intercom_code'] ?>">
                </div>
                <div class="input-group">
                    <label for="birth_date">Date de naissance (optionnel)</label>
                    <input type="date" id="birth_date" name="birth_date" value="<?= $user_data['birth_date'] ?>">
                </div>
                <button type="submit" class="basic-btn">Mettre à jour les informations</button>
                <?php if ($is_admin and $user_data['id'] != $uid): ?>
                    <?php if (!empty($user_data['banned'])): ?>
                        <button type="submit" name="action" value="unban" class="basic-btn" style="background-color: green;">Débannir l'utilisateur</button>
                    <?php else: ?>
                        <button type="submit" name="action" value="ban" class="basic-btn alert-btn">Bannir l'utilisateur</button>
                    <?php endif; ?>
                <?php endif; ?>
            </form>
        </div>
        
        <div class="form-page">
            <h2>Historique Des Commandes</h2>
        </div>

        <?php if (empty($all_orders)): ?>
            <span>Aucune commande.</span>
        <?php endif; ?>

        <?php foreach ($all_orders as $order): ?>
        <div class="form-page">
            <div class="command_card">
                <details>
                    <summary class="main_info">
                        <strong>Commande #<?= $order['id']; ?></strong>
                    </summary>

                    <ul>
                        <?php foreach ($order['items']['menus'] as $menu): ?>
                        <li><?= $menu['name'] ?> x<?= $menu['quantity'] ?></li>
                        <?php endforeach; ?>
                        <?php foreach ($order['items']['foods'] as $food): ?>
                        <li><?= $food['name'] ?> x<?= $food['quantity'] ?></li>
                        <?php endforeach; ?>
                    </ul>

                    <strong>Prix Final :</strong> 

                    <br /><br />
                    
                    <details>
                        <summary>
                            <strong>Détails</strong>
                        </summary>
                        <br/><br/>
                        <div>
                            Cuisinier :<?= ' '.$order['cook'] ?><br/><br/>
                            Livreur :<?= ' '.$order['delivery_person'] ?><br/><br/>
                        </div>
                    </details>
                </details>
            </div>
        </div>
        <?php endforeach; ?>
    </main>
<?php include __DIR__ . '/../includes/footer.php'; ?>
