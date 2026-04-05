<?php

class ProfileController extends Controller {
    public function index($uid = NULL) {
        require_once __DIR__ . '/../models/Cart.php';
        require_once __DIR__ . '/../models/User.php';

        if ($uid == NULL)
            $uid = $_SESSION['user']['id'];

        $cart_count = Cart::getCartCount();
        $user_data = User::getUserData($uid);

        $this->render(
            'profile',
            [
                'cart_count' => $cart_count,
                'user_data' => $user_data,
                'target' => $uid
            ]
        );
    }

    public function updateProfile() {
        global $pdo;
        require_once __DIR__ . '/../db_connect.php';

        $target = $_SESSION['user']['id'];
        try {
            if (array_key_exists('user_id', $_POST)) {
                // Vérifier si l'utilisateur actuel est admin
                $user_id = $_SESSION['user']['id'];
                $stmt = $pdo->prepare("
                SELECT r.name
                FROM users u
                JOIN role r ON r.id = u.role_id
                WHERE u.id = ?
                ");
                $stmt->execute([$user_id]);
                $user_role = $stmt->fetch();

                if ($user_role = 'administrator') {
                    $target = $_POST['user_id'];
                }
            }
            if (array_key_exists('first_name', $_POST)) {
                // Modifier les données de l'utilisateur dans la base de données
                $stmt = $pdo->prepare("UPDATE users SET first_name=?, last_name=?, street_nb=?, street_nb_suf=?, street=?, zip_code=?, phone=?, email=?, intercom_code=?, birth_date=? WHERE id = ?");

                $birth_date = !empty($_POST['birth_date']) ? $_POST['birth_date'] : null;

                $stmt->execute([
                    $_POST['first_name'],
                    $_POST['last_name'],
                    $_POST['street_nb'],
                    $_POST['street_nb_suf'],
                    $_POST['street'],
                    $_POST['zip_code'],
                    $_POST['phone'],
                    $_POST['email'],
                    $_POST['intercom_code'],
                    $birth_date,
                    $target
                ]);

                $_SESSION['user'] = array_merge($_SESSION['user'], $_POST);
            }
        } catch (\PDOException $e) {
            $erreur = "Erreur lors de la mise à jour : " . $e->getMessage();
        }

        $this->index($target);
    }
}
