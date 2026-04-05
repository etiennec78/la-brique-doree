<?php

class ProfileController extends Controller {
    public function index($uid = NULL) {
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit();
        }

        require_once __DIR__ . '/../models/Cart.php';
        require_once __DIR__ . '/../models/User.php';

        if ($uid == NULL)
            $uid = $_SESSION['user']['id'];

        $cart_count = Cart::getCartCount();
        $user_data = User::getUserData($uid);
        $is_admin = User::isAdmin($_SESSION['user']['id']);

        $this->render(
            'profile',
            [
                'cart_count' => $cart_count,
                'user_data' => $user_data,
                'target' => $uid,
                'is_admin' => $is_admin,
            ]
        );
    }

    public function updateProfile() {
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit();
        }

        global $pdo;
        require_once __DIR__ . '/../db_connect.php';
        require_once __DIR__ . '/../models/User.php';

        $target = $_SESSION['user']['id'];
        $user_role = User::getUserRole($_SESSION['user']['id']);
        try {
            if (array_key_exists('user_id', $_POST)) {
                // Vérifier si l'utilisateur actuel est admin
                if ($user_role == 'administrator')
                    $target = $_POST['user_id'];
            }
            if (isset($_POST['action']) && $_POST['action'] === 'ban' && $user_role == 'administrator') {
                $stmt = $pdo->prepare("UPDATE users SET banned = 1 WHERE id = ?");
                $stmt->execute([$target]);
            }
            else if (isset($_POST['action']) && $_POST['action'] === 'unban' && $user_role == 'administrator') {
                $stmt = $pdo->prepare("UPDATE users SET banned = 0 WHERE id = ?");
                $stmt->execute([$target]);
            }
            else if (array_key_exists('first_name', $_POST)) {
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
