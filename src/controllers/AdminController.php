<?php

class AdminController extends Controller {
    public function index() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 3) {
            header('Location: /login');
            exit();
        }

        require_once __DIR__ . '/../models/User.php';
        include_once __DIR__ . '/../format_data.php';

        $users_data = User::getAllUsersInfo();

        $this->render('admin', ['users_data' => $users_data, 'get_name' => 'getName']);
    }

    public function applyGlobalReduction() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 3) {
            header('Location: /login');
            exit();
        }

        require_once __DIR__ . '/../models/User.php';

        if (isset($_POST['user_id']) and isset($_POST['reduction']))
            $users_data = User::setUserData($_POST['user_id'], 'global_reduction', $_POST['reduction']/100);

        header('Location: /admin');
    }

    public function apiBanUser() {
        if (($_SESSION['user']['role_id'] ?? 0) != 3) {
            exit(); 
        }

        require_once __DIR__ . '/../models/User.php';

        $id = $_POST['user_id'];
        $etat = $_POST['banned'];

        if ($id != $_SESSION['user']['id']) {
            User::setUserData($id, 'banned', $etat);
        }

        echo json_encode(['success' => true, 'banned' => $etat]);
        exit();
    }
}