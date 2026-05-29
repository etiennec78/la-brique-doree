<?php
$title = "La Brique Dorée - Inscription";
$h1 = "INSCRIPTION";
$staff_page = false;
$css_files = ['/css/form.css'];
$js_files = ['/js/show_password.js'];
include __DIR__ . '/../includes/header.php';
?>
<main>
        <div class="form-page">
            <h2>Inscription</h2>
            
            <form action="/register" method="post">
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
                <button type="submit" class="basic-btn">S'inscrire</button>
            </form>
            <p>Déjà un compte ? <a href="/login">Connexion</a></p>
        </div>
    </main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
