<?php
$title = "La Brique Dorée - Connexion";
$h1 = "CONNEXION";
$staff_page = false;
$css_files = ['/css/form.css'];
$js_files = ['/js/show_password.js'];
include __DIR__ . '/../includes/header.php';
?>
<main>
        <div class="form-page">
            <h2>Connexion</h2>

            <form action="/login" method="post">
                <div class="input-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?= isset($_SESSION['failed_email']) ? htmlspecialchars($_SESSION['failed_email']) : '' ?>" required>
                    <?php unset($_SESSION['failed_email']); ?>
                </div>
                <div class="input-group">
                    <label for="password">Mot de passe</label>
                    <div class="password-wrapper">
                        <input type="password" id="password" name="password" required>
                        <span id="toggleEye" onclick="showPwd()">👁️</span>
                    </div>
                </div>
                <button type="submit" class="basic-btn">Se connecter</button>
            </form>
            <p>Pas encore de compte ? <a href="/register">Inscrivez-vous</a></p>
        </div>
    </main>

<?php
  if (isset($_SESSION['error'])){
    unset($_SESSION['error']);
  }
  include __DIR__ . '/../includes/footer.php'; 
?>
